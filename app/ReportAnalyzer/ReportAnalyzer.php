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

        return $result;
    }
}
