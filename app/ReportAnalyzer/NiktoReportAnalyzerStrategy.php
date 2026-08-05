<?php

namespace App\ReportAnalyzer;

use App\ReportAnalyzer\ReportAnalyzerInterface;

class NiktoReportAnalyzerStrategy implements ReportAnalyzerInterface
{

    /**
     * Строки отчёта Nikto начинаются с "+ ", поэтому плюс экранируется: без этого
     * PCRE считает его квантификатором без предшествующего элемента, регулярка
     * не компилируется и preg_match молча возвращает false на каждой строке.
     */
    private array $patterns = [
        '/^\s*\+ OSVDB-\d+: (.+)$/'                              => 'Уязвимость',
        '/^\s*\+ (Server leaks inodes via ETags.*)$/'             => 'Утечка информации',
        '/^\s*\+ (Apache mod_negotiation is enabled.*)$/'         => 'Небезопасная конфигурация',
        '/^\s*\+ (Unnecessary service.*running.*)$/'              => 'Ненужный сервис',
        '/^\s*\+ (Allowed HTTP Methods: .+)$/'                    => 'HTTP методы',
        '/^\s*\+ (Web Server is outdated.*)$/'                    => 'Устаревшее ПО',
        '/^\s*\+ (The anti-clickjacking X-Frame-Options header is not present.*)$/' => 'Отсутствует заголовок безопасности',
        '/^\s*\+ (Hostname .* does not match certificate.*)$/'    => 'Несоответствие сертификата',
    ];

    private array $severities = [
        'Уязвимость'                       => 'high',
        'Устаревшее ПО'                    => 'high',
        'Утечка информации'                => 'medium',
        'Небезопасная конфигурация'        => 'medium',
        'HTTP методы'                      => 'medium',
        'Несоответствие сертификата'       => 'medium',
        'Отсутствует заголовок безопасности' => 'medium',
        'Ненужный сервис'                  => 'low',
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
            'Отсутствует заголовок безопасности' => "Добавьте заголовок X-Frame-Options (или CSP frame-ancestors), чтобы защитить страницы от встраивания в чужой iframe.",
            'Несоответствие сертификата' => "Имя хоста не совпадает с CN сертификата: \"$problem\". Выпустите сертификат на нужное имя или добавьте его в SAN.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
