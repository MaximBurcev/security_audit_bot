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

    /** Момент, относительно которого считается срок действия сертификата. */
    private const NOW = 1785888000; // 2026-08-05

    private function analyze(string $output, ?int $now = null): array
    {
        return (new SslReportAnalyzerStrategy($now ?? self::NOW))->analyzeOutput(explode("\n", $output));
    }

    /**
     * Только находки, требующие действий: пройденные проверки отбрасываем.
     */
    private function problems(string $output, ?int $now = null): array
    {
        return array_values(array_filter(
            $this->analyze($output, $now),
            fn($finding) => $finding['severity'] !== 'ok'
        ));
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

        $this->assertSame([], $this->problems($modern));
    }

    /**
     * "disabled" в таблице поддержки протоколов не должен считаться проблемой.
     */
    public function test_disabled_protocols_are_not_flagged(): void
    {
        $this->assertSame([], $this->problems("  SSLv2     disabled\n  SSLv3     disabled"));
    }

    public function test_disabled_protocols_become_a_passed_check(): void
    {
        $findings = $this->analyze("  SSLv2     disabled\n  TLSv1.0   disabled");

        $this->assertSame('Протоколы', $findings[0]['type']);
        $this->assertSame('ok', $findings[0]['severity']);
    }

    /**
     * Устаревший протокол найден — значит проверка не пройдена и зелёной
     * отметки быть не должно.
     */
    public function test_passed_check_disappears_when_a_protocol_is_enabled(): void
    {
        $report = "  SSLv2     disabled\n  TLSv1.0   enabled\nAccepted  TLSv1.0  128 bits  AES128-SHA";

        $this->assertNotContains('Протоколы', array_column($this->analyze($report), 'type'));
    }

    public function test_only_aead_ciphers_is_a_passed_check(): void
    {
        $report = 'Accepted  TLSv1.2  256 bits  ECDHE-RSA-AES256-GCM-SHA384   Curve 25519 DHE 253';

        $findings = $this->analyze($report);

        $this->assertSame('Шифры', $findings[0]['type']);
        $this->assertSame('ok', $findings[0]['severity']);
    }

    /**
     * CBC-наборы не «слабые» в смысле 3DES, но уступают AEAD и заслуживают
     * жёлтой отметки, а не зелёной.
     */
    public function test_cbc_ciphers_are_reported_as_medium(): void
    {
        $report = <<<'TXT'
Accepted  TLSv1.2  256 bits  ECDHE-ECDSA-AES256-GCM-SHA384 Curve 25519 DHE 253
Accepted  TLSv1.2  128 bits  ECDHE-ECDSA-AES128-SHA        Curve 25519 DHE 253
Accepted  TLSv1.2  256 bits  ECDHE-ECDSA-AES256-SHA        Curve 25519 DHE 253
TXT;

        $cbc = $this->problems($report);

        $this->assertSame('Шифры в режиме CBC', $cbc[0]['type']);
        $this->assertSame('medium', $cbc[0]['severity']);
        $this->assertSame('2 набора', $cbc[0]['problem']);
    }

    public function test_heartbleed_check_passes(): void
    {
        $findings = $this->analyze("TLSv1.2 not vulnerable to heartbleed");

        $this->assertSame('Heartbleed', $findings[0]['type']);
        $this->assertSame('ok', $findings[0]['severity']);
    }

    public function test_heartbleed_vulnerability_is_not_a_passed_check(): void
    {
        $this->assertSame([], $this->analyze('TLSv1.2 vulnerable to heartbleed'));
    }

    public function test_valid_certificate_reports_days_left(): void
    {
        $findings = $this->analyze('Not valid after:  Sep 29 19:31:42 2026 GMT');

        $this->assertSame('Сертификат', $findings[0]['type']);
        $this->assertSame('ok', $findings[0]['severity']);
        $this->assertSame('действует ещё 55 дн.', $findings[0]['problem']);
    }

    /**
     * Главное, чего не хватало: предупреждение до аварии, а не после.
     */
    public function test_certificate_expiring_soon_is_a_warning(): void
    {
        $findings = $this->analyze('Not valid after:  Aug 25 19:31:42 2026 GMT');

        $this->assertSame('Скорое истечение сертификата', $findings[0]['type']);
        $this->assertSame('medium', $findings[0]['severity']);
    }

    public function test_certificate_expiring_within_a_week_is_high(): void
    {
        $findings = $this->analyze('Not valid after:  Aug 10 19:31:42 2026 GMT');

        $this->assertSame('high', $findings[0]['severity']);
    }

    public function test_expired_certificate_is_reported(): void
    {
        $findings = $this->analyze('Not valid after:  Jul 30 19:31:42 2026 GMT');

        $this->assertSame('Истекший сертификат', $findings[0]['type']);
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
