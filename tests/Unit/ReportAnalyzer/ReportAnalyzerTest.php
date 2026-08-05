<?php

namespace Tests\Unit\ReportAnalyzer;

use App\ReportAnalyzer\ReportAnalyzer;
use App\ReportAnalyzer\ReportAnalyzerInterface;
use PHPUnit\Framework\TestCase;

class ReportAnalyzerTest extends TestCase
{
    private function analyzerReturning(array $findings): ReportAnalyzer
    {
        $strategy = new class($findings) implements ReportAnalyzerInterface {
            public function __construct(private array $findings)
            {
            }

            public function analyzeOutput($output): array
            {
                return $this->findings;
            }

            public function getRecommendation($type, $problem): string
            {
                return 'test';
            }
        };

        return new ReportAnalyzer($strategy);
    }

    private function finding(string $type, string $problem, string $severity): array
    {
        return [
            'type'           => $type,
            'problem'        => $problem,
            'recommendation' => 'test',
            'severity'       => $severity,
        ];
    }

    public function test_it_sorts_findings_by_severity(): void
    {
        $analyzer = $this->analyzerReturning([
            $this->finding('Открытый порт', '80/tcp', 'low'),
            $this->finding('Уязвимость', 'smb-vuln', 'critical'),
            $this->finding('HTTP методы', 'TRACE', 'medium'),
            $this->finding('CVE-уязвимость', 'CVE-1', 'high'),
        ]);

        $this->assertSame(
            ['critical', 'high', 'medium', 'low'],
            array_column($analyzer->get([]), 'severity')
        );
    }

    public function test_it_removes_duplicates_by_type_and_problem(): void
    {
        $analyzer = $this->analyzerReturning([
            $this->finding('Устаревший протокол', 'TLSv1.0', 'high'),
            $this->finding('Устаревший протокол', 'TLSv1.0', 'high'),
            $this->finding('Устаревший протокол', 'TLSv1.1', 'high'),
        ]);

        $this->assertCount(2, $analyzer->get([]));
    }

    /**
     * Одинаковая проблема разных типов — это две разные находки.
     */
    public function test_same_problem_of_different_types_is_kept(): void
    {
        $analyzer = $this->analyzerReturning([
            $this->finding('Уязвимость', 'ssl-poodle', 'high'),
            $this->finding('Небезопасный шифр', 'ssl-poodle', 'high'),
        ]);

        $this->assertCount(2, $analyzer->get([]));
    }

    public function test_unknown_severity_sinks_to_the_bottom(): void
    {
        $analyzer = $this->analyzerReturning([
            $this->finding('Что-то', 'a', 'неизвестно'),
            $this->finding('Уязвимость', 'b', 'critical'),
        ]);

        $this->assertSame('b', $analyzer->get([])[0]['problem']);
    }

    public function test_empty_output_yields_no_findings(): void
    {
        $this->assertSame([], $this->analyzerReturning([])->get([]));
    }
}
