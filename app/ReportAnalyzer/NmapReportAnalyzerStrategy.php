<?php

namespace App\ReportAnalyzer;

use App\ReportAnalyzer\ReportAnalyzerInterface;

class NmapReportAnalyzerStrategy implements ReportAnalyzerInterface
{

    public function analyzeOutput($output): array
    {
        $recommendations = [];

        // Регулярные выражения для обнаружения различных типов проблем
        $patterns = [
            '/^(\d+)\/tcp\s+open\s+(\S+)/'   => 'Открытый порт',
            '/^Host script results:/'        => 'Уязвимость',
            '/Vulnerability (.+)/'           => 'Уязвимость',
            '/Outdated service (.+)/'        => 'Устаревшее ПО',
            '/Default credentials for (.+)/' => 'Стандартные учетные данные',
        ];

        //dd($output);

        // Проходим по каждой строке вывода Nmap
        foreach ($output as $line) {
            foreach ($patterns as $pattern => $type) {
                if (preg_match($pattern, $line, $matches)) {
                    $problem = trim($matches[1] ?? $line);
                    $recommendation = $this->getRecommendation($type, $problem);
                    $recommendations[] = [
                        'type'           => $type,
                        'problem'        => $problem,
                        'recommendation' => $recommendation,
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
            'Открытый порт' => "Рассмотрите возможность закрытия порта $problem, если он не требуется для работы системы.",
            'Уязвимость' => "Обнаружена уязвимость: \"$problem\". Примените соответствующие патчи или обновления.",
            'Устаревшее ПО' => "Служба $problem устарела. Обновите её до последней версии для устранения возможных уязвимостей.",
            'Стандартные учетные данные' => "Используются стандартные учетные данные для $problem. Измените их на уникальные и безопасные.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
