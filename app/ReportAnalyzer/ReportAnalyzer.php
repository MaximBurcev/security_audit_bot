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


    private const SEVERITY_ORDER = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];

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
}
