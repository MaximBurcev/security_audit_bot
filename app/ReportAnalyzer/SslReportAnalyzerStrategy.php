<?php

namespace App\ReportAnalyzer;

class SslReportAnalyzerStrategy implements ReportAnalyzerInterface
{


    /**
     * Формат строки sslscan: "Accepted  TLSv1.1  112 bits  TLS_RSA_WITH_3DES_EDE_CBC_SHA".
     * Слова "cipher:" в выводе нет, поэтому слабые шифры опознаются по названию
     * алгоритма и по длине ключа. TLS 1.0 и 1.1 объявлены устаревшими (RFC 8996).
     */
    private array $patterns = [
        // Шифры проверяются раньше протоколов: строка "Accepted TLSv1.1 112 bits 3DES..."
        // несёт сразу две проблемы, а на строку приходится одна находка. Сам факт
        // включённого устаревшего протокола ловится отдельной строкой таблицы
        // поддержки ("TLSv1.0   enabled"), поэтому ничего не теряется.
        '/(?:Accepted|Preferred)\s+\S+\s+\d+\s+bits\s+(\S*(?:3DES|RC4|_DES_|DES-|EXPORT|NULL|MD5|ANON|ADH|AECDH)\S*)/i' => 'Небезопасный шифр',
        '/(?:Accepted|Preferred)\s+\S+\s+(?:0|40|56|112)\s+bits\s+(\S+)/' => 'Слабая длина ключа',
        '/^\s*(SSLv[0-9]|TLSv1\.[01])\s+enabled/'  => 'Устаревший протокол',
        '/(?:Accepted|Preferred)\s+(SSLv[0-9])/'   => 'Устаревший протокол',
        '/(?:Accepted|Preferred)\s+(TLSv1\.[01])/' => 'Устаревший протокол',
        '/No certificate found/'               => 'Отсутствует сертификат',
        '/Certificate\s+not\s+trusted/'        => 'Недостоверный сертификат',
        '/Expired\s+certificate/'              => 'Истекший сертификат',
        '/Self-signed certificate/'            => 'Самоподписанный сертификат',
    ];

    private array $severities = [
        'Отсутствует сертификат'    => 'critical',
        'Устаревший протокол'       => 'high',
        'Небезопасный шифр'        => 'high',
        'Слабая длина ключа'        => 'high',
        'Истекший сертификат'       => 'high',
        'Недостоверный сертификат'  => 'medium',
        'Самоподписанный сертификат' => 'medium',
    ];

    public function analyzeOutput($output): array
    {
        $recommendations = [];

        foreach ($output as $line) {
            foreach ($this->patterns as $pattern => $type) {
                if (preg_match($pattern, $line, $matches)) {
                    // Там, где паттерн выделяет название шифра, показываем его,
                    // иначе — всю совпавшую часть строки (протокол).
                    $problem = trim($matches[1] ?? $matches[0]);
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
            'Устаревший протокол' => "Протокол \"$problem\" считается устаревшим и небезопасным. Отключите его и используйте более современные версии TLS (например, TLS 1.2 или TLS 1.3).",
            'Небезопасный шифр' => "Шифр \"$problem\" является слабым или экспортным. Отключите его и используйте более надежные шифры.",
            'Слабая длина ключа' => "Шифр \"$problem\" использует слишком короткий ключ. Отключите наборы короче 128 бит.",
            'Отсутствует сертификат' => "На сервере отсутствует SSL-сертификат. Установите доверенный сертификат для обеспечения безопасного соединения.",
            'Недостоверный сертификат' => "Сертификат не является доверенным. Используйте сертификат, выпущенный доверенным центром сертификации (CA).",
            'Истекший сертификат' => "Сертификат истек. Обновите его, чтобы обеспечить непрерывную работу защищенного соединения.",
            'Самоподписанный сертификат' => "Используется самоподписанный сертификат. Рекомендуется использовать сертификат, выпущенный доверенным CA, для повышения доверия пользователей.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
