<?php

namespace Tests\Unit\ReportAnalyzer;

use App\ReportAnalyzer\NiktoReportAnalyzerStrategy;
use App\ReportAnalyzer\NmapReportAnalyzerStrategy;
use App\ReportAnalyzer\ReportAnalyzerInterface;
use App\ReportAnalyzer\SslReportAnalyzerStrategy;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Страховка от немой поломки разбора.
 *
 * Все шесть регулярок Nikto долгое время не компилировались из-за неэкранированного
 * плюса: preg_match возвращал false на каждой строке, анализатор всегда отдавал
 * пустой список, и отчёт выглядел как «проблем не найдено». Ошибка не проявлялась
 * ничем, кроме warning'а в логе.
 */
class PatternCompilationTest extends TestCase
{
    public static function strategyProvider(): array
    {
        return [
            'nmap'  => [NmapReportAnalyzerStrategy::class],
            'nikto' => [NiktoReportAnalyzerStrategy::class],
            'ssl'   => [SslReportAnalyzerStrategy::class],
        ];
    }

    /**
     * @dataProvider strategyProvider
     */
    public function test_every_pattern_compiles(string $strategyClass): void
    {
        $patterns = $this->patternsOf(new $strategyClass());

        $this->assertNotEmpty($patterns, "У {$strategyClass} нет ни одного паттерна");

        foreach (array_keys($patterns) as $pattern) {
            $result = @preg_match($pattern, 'проверочная строка');

            $this->assertNotFalse(
                $result,
                "Паттерн не компилируется: {$pattern} ({$strategyClass}): " . preg_last_error_msg()
            );
        }
    }

    /**
     * @dataProvider strategyProvider
     */
    public function test_every_pattern_has_a_severity(string $strategyClass): void
    {
        $strategy = new $strategyClass();

        $severities = new ReflectionProperty($strategy, 'severities');
        $severities->setAccessible(true);
        $known = $severities->getValue($strategy);

        foreach ($this->patternsOf($strategy) as $pattern => $type) {
            $this->assertArrayHasKey(
                $type,
                $known,
                "Для типа «{$type}» не задана важность, находка молча станет low ({$pattern})"
            );
        }
    }

    /**
     * @return array<string, string> pattern => type
     */
    private function patternsOf(ReportAnalyzerInterface $strategy): array
    {
        $property = new ReflectionProperty($strategy, 'patterns');
        $property->setAccessible(true);

        return $property->getValue($strategy);
    }
}
