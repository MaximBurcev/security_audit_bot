<?php

namespace Tests\Unit\ReportAnalyzer;

use App\ReportAnalyzer\NmapReportAnalyzerStrategy;
use PHPUnit\Framework\TestCase;

class NmapReportAnalyzerStrategyTest extends TestCase
{
    /**
     * Вывод в стиле "nmap -v -sV -sC --script vuln": сначала verbose-строки
     * об обнаруженных портах, затем итоговая таблица и результаты NSE-скриптов.
     */
    private const REPORT = <<<'TXT'
Discovered open port 445/tcp on 10.0.0.5
Discovered open port 3306/tcp on 10.0.0.5
PORT      STATE SERVICE      VERSION
22/tcp    open  ssh          OpenSSH 7.4 (protocol 2.0)
80/tcp    open  http         nginx 1.14.2
445/tcp   open  microsoft-ds Samba smbd 4.6.2
3306/tcp  open  mysql        MySQL 5.7.33
| smb-vuln-ms17-010:
|   VULNERABLE:
|     State: VULNERABLE
|     IDs:  CVE:CVE-2017-0143
| http-methods:
|_  Potentially risky methods: TRACE PUT DELETE
| smb-security-mode:
|   message_signing: disabled (dangerous, but default)
|_ftp-anon: Anonymous FTP login allowed (FTP code 230)
TXT;

    private function analyze(string $output): array
    {
        return (new NmapReportAnalyzerStrategy())->analyzeOutput(explode("\n", $output));
    }

    private function findByProblem(array $findings, string $needle): ?array
    {
        foreach ($findings as $finding) {
            if (str_contains($finding['problem'], $needle)) {
                return $finding;
            }
        }

        return null;
    }

    public function test_it_detects_open_ports_from_the_final_table(): void
    {
        $ports = array_filter($this->analyze(self::REPORT), fn($f) => $f['type'] === 'Открытый порт');

        $this->assertCount(4, $ports);
    }

    /**
     * Порт попадает в вывод дважды: строкой "Discovered open port" и строкой
     * итоговой таблицы. В отчёте он должен остаться один — с деталями сервиса.
     */
    public function test_verbose_entry_is_replaced_by_the_detailed_one(): void
    {
        $findings = $this->analyze(self::REPORT);

        $matching = array_filter(
            $findings,
            fn($f) => $f['type'] === 'Открытый порт' && str_starts_with($f['problem'], '445/tcp')
        );

        $this->assertCount(1, $matching);
        $this->assertStringContainsString('microsoft-ds', reset($matching)['problem']);
    }

    /**
     * СУБД, открытая наружу, не может иметь ту же важность, что и 80/tcp.
     */
    public function test_database_port_outranks_a_web_port(): void
    {
        $findings = $this->analyze(self::REPORT);

        $this->assertSame('high', $this->findByProblem($findings, '3306/tcp')['severity']);
        $this->assertSame('medium', $this->findByProblem($findings, '22/tcp')['severity']);
        $this->assertSame('low', $this->findByProblem($findings, '80/tcp')['severity']);
    }

    public function test_sensitive_port_recommendation_mentions_the_service(): void
    {
        $mysql = $this->findByProblem($this->analyze(self::REPORT), '3306/tcp');

        $this->assertStringContainsString('MySQL', $mysql['recommendation']);
    }

    public function test_nse_vulnerability_is_named_after_the_script(): void
    {
        $vuln = $this->findByProblem($this->analyze(self::REPORT), 'smb-vuln-ms17-010');

        $this->assertNotNull($vuln, 'Уязвимость NSE-скрипта не найдена');
        $this->assertSame('Уязвимость', $vuln['type']);
        $this->assertSame('high', $vuln['severity']);
    }

    public function test_cve_gets_a_link_to_the_nvd(): void
    {
        $cve = $this->findByProblem($this->analyze(self::REPORT), 'CVE-2017-0143');

        $this->assertSame('CVE-уязвимость', $cve['type']);
        $this->assertSame('https://nvd.nist.gov/vuln/detail/CVE-2017-0143', $cve['link']);
    }

    public function test_it_detects_risky_http_methods(): void
    {
        $methods = $this->findByProblem($this->analyze(self::REPORT), 'TRACE');

        $this->assertSame('Небезопасные HTTP-методы', $methods['type']);
        $this->assertSame('medium', $methods['severity']);
    }

    public function test_it_detects_smb_signing_and_anonymous_ftp(): void
    {
        $types = array_column($this->analyze(self::REPORT), 'type');

        $this->assertContains('Небезопасная конфигурация SMB', $types);
        $this->assertContains('Анонимный доступ FTP', $types);
    }

    /**
     * Для UDP nmap печатает "open|filtered" — это не подтверждённый открытый порт.
     */
    public function test_filtered_ports_are_not_reported_as_open(): void
    {
        $this->assertSame([], $this->analyze('53/udp open|filtered domain'));
    }

    public function test_closed_ports_are_not_reported(): void
    {
        $this->assertSame([], $this->analyze('81/tcp closed http'));
    }
}
