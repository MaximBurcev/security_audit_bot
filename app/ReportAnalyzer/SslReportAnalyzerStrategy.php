<?php

namespace App\ReportAnalyzer;

class SslReportAnalyzerStrategy implements ReportAnalyzerInterface
{


    public function analyzeOutput($output): array
    {
        $recommendations = [];

        // Регулярные выражения для обнаружения различных типов проблем
        $patterns = [
            '/Accepted\s+SSLv[0-9]/'               => 'Устаревший протокол',
            '/Accepted\s+TLSv1\.0/'                => 'Устаревший протокол',
            '/Accepted\s+cipher:\s+(WEAK|EXPORT)/' => 'Небезопасный шифр',
            '/No certificate found/'               => 'Отсутствует сертификат',
            '/Certificate\s+not\s+trusted/'        => 'Недостоверный сертификат',
            '/Expired\s+certificate/'              => 'Истекший сертификат',
            '/Self-signed certificate/'            => 'Самоподписанный сертификат',
        ];

        // Проходим по каждой строке вывода SSLScan
        foreach ($output as $line) {
            foreach ($patterns as $pattern => $type) {
                if (preg_match($pattern, $line, $matches)) {
                    $problem = trim($matches[0]);
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
            'Устаревший протокол' => "Протокол \"$problem\" считается устаревшим и небезопасным. Отключите его и используйте более современные версии TLS (например, TLS 1.2 или TLS 1.3).",
            'Небезопасный шифр' => "Шифр \"$problem\" является слабым или экспортным. Отключите его и используйте более надежные шифры.",
            'Отсутствует сертификат' => "На сервере отсутствует SSL-сертификат. Установите доверенный сертификат для обеспечения безопасного соединения.",
            'Недостоверный сертификат' => "Сертификат не является доверенным. Используйте сертификат, выпущенный доверенным центром сертификации (CA).",
            'Истекший сертификат' => "Сертификат истек. Обновите его, чтобы обеспечить непрерывную работу защищенного соединения.",
            'Самоподписанный сертификат' => "Используется самоподписанный сертификат. Рекомендуется использовать сертификат, выпущенный доверенным CA, для повышения доверия пользователей.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
