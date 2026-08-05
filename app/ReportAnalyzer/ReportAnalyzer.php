<?php

namespace App\ReportAnalyzer;

use Exception;

class ReportAnalyzer
{

    private $strategy;


    /**
     *  constructor.
     *
     * @param ReportAnalyzerInterface $strategy
     * @throws Exception
     */
    public function __construct(ReportAnalyzerInterface $strategy)
    {
        if (isset($this->strategy)) {
            throw new Exception("Contract is already present.");
        }
        $this->strategy = $strategy;
    }


    /**
     * "ok" — пройденная проверка, а не проблема, поэтому она всегда в конце списка,
     * после всех находок, требующих действий.
     */
    private const SEVERITY_ORDER = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3, 'ok' => 4];

    public function get($report): array
    {
        $seen = [];
        $result = [];

        foreach ($this->strategy->analyzeOutput($report) as $item) {
            $key = $item['type'] . '|' . $item['problem'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $item;
            }
        }

        usort($result, fn($a, $b) =>
            (self::SEVERITY_ORDER[$a['severity'] ?? 'low'] ?? 3)
            <=> (self::SEVERITY_ORDER[$b['severity'] ?? 'low'] ?? 3)
        );

        return $result;
    }

    /**
     * Сколько находок требует действий, а сколько проверок пройдено.
     *
     * @param array<int, array<string, mixed>> $findings
     * @return array{problems: int, passed: int}
     */
    public static function summarize(array $findings): array
    {
        $passed = count(array_filter($findings, fn($item) => ($item['severity'] ?? null) === 'ok'));

        return [
            'problems' => count($findings) - $passed,
            'passed'   => $passed,
        ];
    }
}
