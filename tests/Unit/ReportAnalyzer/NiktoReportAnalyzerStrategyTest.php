<?php

namespace Tests\Unit\ReportAnalyzer;

use App\ReportAnalyzer\NiktoReportAnalyzerStrategy;
use PHPUnit\Framework\TestCase;

class NiktoReportAnalyzerStrategyTest extends TestCase
{
    /**
     * Фрагмент реального отчёта (nikto -ssl -h php-psr.ru).
     */
    private const REPORT = <<<'TXT'
- Nikto v2.1.5
---------------------------------------------------------------------------
+ Target IP:          82.146.58.115
+ Target Hostname:    php-psr.ru
+ Server: nginx/1.14.2
+ Server leaks inodes via ETags, header found with file /, fields: 0x66f18f41 0x6274
+ The anti-clickjacking X-Frame-Options header is not present.
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Hostname 'php-psr.ru' does not match certificate's CN 'some-lects.ru'
+ OSVDB-3092: /sitemap.xml: This gives a nice listing of the site content.
+ OSVDB-3093: /.htaccess: Contains authorization information
TXT;

    private function analyze(string $output): array
    {
        return (new NiktoReportAnalyzerStrategy())->analyzeOutput(explode("\n", $output));
    }

    public function test_it_finds_every_issue_in_a_real_report(): void
    {
        $result = $this->analyze(self::REPORT);

        $this->assertCount(5, $result);
    }

    public function test_osvdb_entries_become_vulnerabilities(): void
    {
        $result = $this->analyze('+ OSVDB-3093: /.htaccess: Contains authorization information');

        $this->assertSame('Уязвимость', $result[0]['type']);
        $this->assertSame('high', $result[0]['severity']);
        $this->assertSame('/.htaccess: Contains authorization information', $result[0]['problem']);
    }

    public function test_it_reports_etag_inode_leak(): void
    {
        $result = $this->analyze('+ Server leaks inodes via ETags, header found with file /, fields: 0x66f18f41');

        $this->assertSame('Утечка информации', $result[0]['type']);
        $this->assertSame('medium', $result[0]['severity']);
    }

    public function test_it_reports_missing_clickjacking_header(): void
    {
        $result = $this->analyze('+ The anti-clickjacking X-Frame-Options header is not present.');

        $this->assertSame('Отсутствует заголовок безопасности', $result[0]['type']);
    }

    public function test_it_reports_certificate_name_mismatch(): void
    {
        $result = $this->analyze("+ Hostname 'php-psr.ru' does not match certificate's CN 'some-lects.ru'");

        $this->assertSame('Несоответствие сертификата', $result[0]['type']);
    }

    public function test_informational_lines_are_not_findings(): void
    {
        $noise = <<<'TXT'
- Nikto v2.1.5
+ Target IP:          82.146.58.115
+ Server: nginx/1.14.2
+ No CGI Directories found (use '-C all' to force check all possible dirs)
+ Start Time:         2025-05-23 15:15:02 (GMT3)
TXT;

        $this->assertSame([], $this->analyze($noise));
    }

    public function test_it_reports_a_cookie_without_the_httponly_flag(): void
    {
        $result = $this->analyze('+ Cookie XSRF-TOKEN created without the httponly flag');

        $this->assertSame('Небезопасная кука', $result[0]['type']);
        $this->assertSame('medium', $result[0]['severity']);
    }

    public function test_it_reports_a_cookie_without_the_secure_flag(): void
    {
        $result = $this->analyze('+ Cookie session_id created without the secure flag');

        $this->assertSame('Небезопасная кука', $result[0]['type']);
    }

    /**
     * Nikto 2.1.5 проверяет кликджекинг на ответе до редиректа, а заголовки
     * снимает с конечной страницы, поэтому в одном отчёте уживаются
     * "header is not present" и "header 'x-frame-options' found".
     * Верить надо второму, иначе отчёт содержит ложную находку.
     */
    public function test_missing_clickjacking_header_is_dropped_when_the_header_is_present(): void
    {
        $report = <<<'TXT'
+ The anti-clickjacking X-Frame-Options header is not present.
+ Uncommon header 'x-frame-options' found, with contents: SAMEORIGIN
TXT;

        $types = array_column($this->analyze($report), 'type');

        $this->assertNotContains('Отсутствует заголовок безопасности', $types);
    }

    public function test_missing_clickjacking_header_is_kept_when_nothing_refutes_it(): void
    {
        $report = <<<'TXT'
+ The anti-clickjacking X-Frame-Options header is not present.
+ Uncommon header 'x-content-type-options' found, with contents: nosniff
TXT;

        $types = array_column($this->analyze($report), 'type');

        $this->assertContains('Отсутствует заголовок безопасности', $types);
    }

    public function test_every_finding_carries_a_recommendation(): void
    {
        foreach ($this->analyze(self::REPORT) as $finding) {
            $this->assertNotEmpty($finding['recommendation'], "Пустая рекомендация для «{$finding['type']}»");
            $this->assertNotEmpty($finding['severity']);
        }
    }
}
