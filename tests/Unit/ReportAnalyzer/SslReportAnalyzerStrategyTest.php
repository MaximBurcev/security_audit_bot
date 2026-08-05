<?php

namespace Tests\Unit\ReportAnalyzer;

use App\ReportAnalyzer\ReportAnalyzer;
use App\ReportAnalyzer\SslReportAnalyzerStrategy;
use PHPUnit\Framework\TestCase;

class SslReportAnalyzerStrategyTest extends TestCase
{
    /**
     * Фрагмент реального вывода sslscan (ANSI-коды уже сняты, как это делает
     * BotReportJob перед сохранением).
     */
    private const REPORT = <<<'TXT'
  SSLv2     disabled
  SSLv3     disabled
  TLSv1.0   enabled
  TLSv1.1   enabled
  TLSv1.2   enabled
Preferred TLSv1.3  128 bits  TLS_AES_128_GCM_SHA256        Curve 25519 DHE 253
Preferred TLSv1.2  128 bits  ECDHE-ECDSA-AES128-GCM-SHA256 Curve 25519 DHE 253
Preferred TLSv1.1  128 bits  ECDHE-ECDSA-AES128-SHA        Curve 25519 DHE 253
Accepted  TLSv1.1  128 bits  ECDHE-RSA-AES128-SHA          Curve 25519 DHE 253
Accepted  TLSv1.1  112 bits  TLS_RSA_WITH_3DES_EDE_CBC_SHA
Preferred TLSv1.0  128 bits  ECDHE-ECDSA-AES128-SHA        Curve 25519 DHE 253
Accepted  TLSv1.0  112 bits  TLS_RSA_WITH_3DES_EDE_CBC_SHA
TXT;

    private function analyze(string $output): array
    {
        return (new SslReportAnalyzerStrategy())->analyzeOutput(explode("\n", $output));
    }

    private function types(array $findings): array
    {
        return array_values(array_unique(array_column($findings, 'type')));
    }

    public function test_it_flags_tls_10_as_obsolete(): void
    {
        $problems = array_column($this->analyze(self::REPORT), 'problem');

        $this->assertContains('TLSv1.0', $problems);
    }

    /**
     * TLS 1.1 объявлен устаревшим наравне с 1.0 (RFC 8996), но паттерна на него
     * не было — версия проходила мимо анализатора.
     */
    public function test_it_flags_tls_11_as_obsolete(): void
    {
        $problems = array_column($this->analyze(self::REPORT), 'problem');

        $this->assertContains('TLSv1.1', $problems);
    }

    /**
     * Старый паттерн искал "Accepted cipher: WEAK", чего в выводе sslscan нет,
     * поэтому 3DES (Sweet32) не находился никогда.
     */
    public function test_it_flags_3des_cipher(): void
    {
        $findings = $this->analyze('Accepted  TLSv1.1  112 bits  TLS_RSA_WITH_3DES_EDE_CBC_SHA');

        $this->assertSame('Небезопасный шифр', $findings[0]['type']);
        $this->assertSame('high', $findings[0]['severity']);
        $this->assertSame('TLS_RSA_WITH_3DES_EDE_CBC_SHA', $findings[0]['problem']);
    }

    public function test_modern_protocols_are_not_flagged(): void
    {
        $modern = <<<'TXT'
Preferred TLSv1.3  128 bits  TLS_AES_128_GCM_SHA256        Curve 25519 DHE 253
Accepted  TLSv1.2  256 bits  ECDHE-RSA-AES256-GCM-SHA384   Curve 25519 DHE 253
TXT;

        $this->assertSame([], $this->analyze($modern));
    }

    /**
     * "disabled" в таблице поддержки протоколов не должен считаться находкой.
     */
    public function test_disabled_protocols_are_not_flagged(): void
    {
        $this->assertSame([], $this->analyze("  SSLv2     disabled\n  SSLv3     disabled"));
    }

    /**
     * Один протокол принимается несколькими шифрами, но в отчёте должен быть
     * одной строкой, а не пятью одинаковыми.
     */
    public function test_repeated_protocol_collapses_into_single_finding(): void
    {
        $result = (new ReportAnalyzer(new SslReportAnalyzerStrategy()))->get(explode("\n", self::REPORT));

        $protocols = array_filter($result, fn($item) => $item['type'] === 'Устаревший протокол');

        $this->assertCount(2, $protocols, 'Ожидались ровно TLSv1.0 и TLSv1.1');
    }

    public function test_certificate_problems_are_detected(): void
    {
        $this->assertSame(['Отсутствует сертификат'], $this->types($this->analyze('No certificate found')));
        $this->assertSame(['Истекший сертификат'], $this->types($this->analyze('Expired certificate')));
        $this->assertSame(['Самоподписанный сертификат'], $this->types($this->analyze('Self-signed certificate')));
    }
}
