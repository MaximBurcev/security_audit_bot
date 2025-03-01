<?php

namespace App\ReportAnalyzer;

interface ReportAnalyzerInterface
{
    public function analyzeOutput($output): array;

    public function getRecommendation($type, $problem);
}
