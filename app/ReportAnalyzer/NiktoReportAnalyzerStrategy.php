<?php

namespace App\ReportAnalyzer;

use App\ReportAnalyzer\ReportAnalyzerInterface;

class NiktoReportAnalyzerStrategy implements ReportAnalyzerInterface
{

    private array $patterns = [
        '/+ OSVDB-[0-9]+: (.+)/'                => 'Уязвимость',
        '/+ Server leaks inodes via ETags/'     => 'Утечка информации',
        '/+ Apache mod_negotiation is enabled/' => 'Небезопасная конфигурация',
        '/+ Unnecessary service/.*running/'     => 'Ненужный сервис',
        '/+ Allowed HTTP Methods: .+/'          => 'HTTP методы',
        '/+ Web Server is outdated/'            => 'Устаревшее ПО',
    ];

    private array $severities = [
        'Уязвимость'             => 'high',
        'Устаревшее ПО'          => 'high',
        'Утечка информации'      => 'medium',
        'Небезопасная конфигурация' => 'medium',
        'HTTP методы'            => 'medium',
        'Ненужный сервис'        => 'low',
    ];

    public function analyzeOutput($output): array
    {
        $recommendations = [];

        foreach ($output as $line) {
            foreach ($this->patterns as $pattern => $type) {
                if (preg_match($pattern, $line, $matches)) {
                    $problem = trim($matches[1] ?? $line);
                    $recommendations[] = [
                        'type'           => $type,
                        'problem'        => $problem,
                        'recommendation' => $this->getRecommendation($type, $problem),
                        'severity'       => $this->severities[$type] ?? 'low',
                    ];
                    break;
                }
            }
        }

        return $recommendations;
    }

    public function getRecommendation($type, $problem): string
    {
        return match ($type) {
            'Уязвимость' => "Обновите ваше ПО до последней версии или примените соответствующий патч для исправления уязвимости: \"$problem\".",
            'Утечка информации' => "Отключите функцию ETags или настройте их корректно, чтобы предотвратить утечку информации.",
            'Небезопасная конфигурация' => "Отключите модуль mod_negotiation в конфигурации Apache, так как он может привести к перечислению файлов.",
            'Ненужный сервис' => "Отключите ненужные службы, которые не используются в текущей конфигурации сервера.",
            'HTTP методы' => "Ограничьте использование HTTP методов только необходимыми (например, GET, POST).",
            'Устаревшее ПО' => "Обновите ваш веб-сервер до последней версии, чтобы устранить известные уязвимости.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
