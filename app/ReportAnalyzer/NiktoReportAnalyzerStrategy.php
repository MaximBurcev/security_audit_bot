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
        '/^\s*\+ (Cookie .* created without the httponly flag.*)$/' => 'Небезопасная кука',
        '/^\s*\+ (Cookie .* created without the secure flag.*)$/'   => 'Небезопасная кука',
    ];

    private array $severities = [
        'Уязвимость'                       => 'high',
        'Устаревшее ПО'                    => 'high',
        'Утечка информации'                => 'medium',
        'Небезопасная конфигурация'        => 'medium',
        'HTTP методы'                      => 'medium',
        'Несоответствие сертификата'       => 'medium',
        'Отсутствует заголовок безопасности' => 'medium',
        'Небезопасная кука'                => 'medium',
        'Ненужный сервис'                  => 'low',
        'Известные уязвимости'             => 'ok',
    ];

    /**
     * Находки, которые сам же отчёт и опровергает.
     *
     * Nikto 2.1.5 проверяет кликджекинг на ответе до редиректа, а заголовки
     * снимает с конечной страницы, поэтому в одном отчёте уживаются
     * "X-Frame-Options header is not present" и "header 'x-frame-options' found".
     * Верить надо второму: заголовок реально отдаётся.
     *
     * @var array<string, string> тип находки => признак опровержения в тексте отчёта
     */
    private array $contradictions = [
        'Отсутствует заголовок безопасности' => "/header\s+'x-frame-options'\s+found/i",
    ];

    public function analyzeOutput($output): array
    {
        $recommendations = [];
        $text = implode("\n", $output);

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

        $recommendations = $this->dropContradicted($recommendations, $text);

        // Явно сообщаем, что проверка отработала и ничего не нашла: пустой отчёт
        // иначе неотличим от несостоявшегося сканирования.
        $scanCompleted = preg_match('/host\(s\) tested|\d+ item\(s\) reported/i', $text) === 1;

        if ($scanCompleted && !in_array('Уязвимость', array_column($recommendations, 'type'), true)) {
            $recommendations[] = [
                'type'           => 'Известные уязвимости',
                'problem'        => 'не обнаружены',
                'recommendation' => 'Проверка пройдена: сканирование не нашло известных уязвимостей.',
                'severity'       => 'ok',
            ];
        }

        return $recommendations;
    }

    /**
     * Убирает находки, опровергнутые другой частью того же отчёта.
     *
     * @param array<int, array<string, mixed>> $recommendations
     * @return array<int, array<string, mixed>>
     */
    private function dropContradicted(array $recommendations, string $text): array
    {
        $refuted = [];

        foreach ($this->contradictions as $type => $pattern) {
            if (preg_match($pattern, $text) === 1) {
                $refuted[$type] = true;
            }
        }

        if ($refuted === []) {
            return $recommendations;
        }

        return array_values(array_filter(
            $recommendations,
            fn($item) => !isset($refuted[$item['type']])
        ));
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
            'Небезопасная кука' => "$problem. Без флага httponly куку читает JavaScript, поэтому её крадёт любая XSS; без secure она уходит по незашифрованному соединению. Проставьте оба флага.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
