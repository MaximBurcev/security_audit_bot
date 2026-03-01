<?php

namespace App\ReportAnalyzer;

class NmapReportAnalyzerStrategy implements ReportAnalyzerInterface
{
    private array $patterns = [
        // Финальная таблица портов: "22/tcp  open  ssh  OpenSSH 7.4"
        '/^(\d+)\/(tcp|udp)\s+open\s+(\S+)(?:\s+(.+))?/'       => 'Открытый порт',
        // Verbose-режим (-v): "Discovered open port 443/tcp on 1.2.3.4"
        '/^Discovered open port (\d+)\/(tcp|udp)/'              => 'Открытый порт',
        '/\bCVE-(\d{4}-\d+)\b/'                                 => 'CVE-уязвимость',
        '/State:\s+VULNERABLE/'                                  => 'Уязвимость',
        '/message_signing:\s+disabled/'                          => 'Небезопасная конфигурация SMB',
        '/Potentially risky methods:\s*(.+)/'                    => 'Небезопасные HTTP-методы',
        '/Anonymous FTP login allowed/'                          => 'Анонимный доступ FTP',
        '/Valid credentials/'                                    => 'Стандартные учетные данные',
    ];

    private array $severities = [
        'Стандартные учетные данные'    => 'critical',
        'CVE-уязвимость'               => 'high',
        'Уязвимость'                   => 'high',
        'Небезопасная конфигурация SMB' => 'high',
        'Анонимный доступ FTP'         => 'high',
        'Небезопасные HTTP-методы'     => 'medium',
        'Открытый порт'                => 'low',
    ];

    public function analyzeOutput($output): array
    {
        $recommendations = [];
        $portIndex = []; // port/proto => index in $recommendations
        $currentScript = null; // последний NSE-скрипт: "| script-name:"

        foreach ($output as $line) {
            // Отслеживаем имя NSE-скрипта по заголовку "| script-name:"
            if (preg_match('/^\| ([\w-]+):$/', trim($line), $m)) {
                $currentScript = $m[1];
            }

            foreach ($this->patterns as $pattern => $type) {
                if (preg_match($pattern, $line, $matches)) {
                    $problem = $type === 'Уязвимость' && $currentScript
                        ? $currentScript
                        : $this->extractProblem($type, $line, $matches);
                    $item = [
                        'type'           => $type,
                        'problem'        => $problem,
                        'recommendation' => $this->getRecommendation($type, $problem),
                        'severity'       => $this->severities[$type] ?? 'low',
                        'link'           => $type === 'CVE-уязвимость'
                            ? "https://nvd.nist.gov/vuln/detail/{$problem}"
                            : null,
                    ];

                    if ($type === 'Открытый порт') {
                        $portKey = "{$matches[1]}/{$matches[2]}";
                        if (isset($portIndex[$portKey])) {
                            // Заменяем краткую verbose-запись на детальную из финальной таблицы
                            $recommendations[$portIndex[$portKey]] = $item;
                        } else {
                            $portIndex[$portKey] = count($recommendations);
                            $recommendations[] = $item;
                        }
                    } else {
                        $recommendations[] = $item;
                    }
                    break;
                }
            }
        }

        return array_values($recommendations);
    }

    private function extractProblem(string $type, string $line, array $matches): string
    {
        return match ($type) {
            'Открытый порт'            => "{$matches[1]}/{$matches[2]}" . (!empty($matches[3]) ? " ({$matches[3]}" . (!empty($matches[4]) ? ' — ' . trim($matches[4]) : '') . ')' : ''),
            'CVE-уязвимость'           => "CVE-{$matches[1]}",
            'Небезопасные HTTP-методы' => trim($matches[1]),
            default                    => trim($line),
        };
    }

    public function getRecommendation($type, $problem): string
    {
        return match ($type) {
            'Открытый порт'                   => "Проверьте необходимость порта $problem. Закройте его, если сервис не используется.",
            'CVE-уязвимость'                  => "Обнаружена уязвимость $problem. Примените патч или обновите уязвимый компонент.",
            'Уязвимость'                      => "NSE-скрипт «{$problem}» обнаружил уязвимость. Изучите полный отчёт и устраните проблему.",
            'Небезопасная конфигурация SMB'   => "Подпись SMB-пакетов отключена. Включите message signing для защиты от relay-атак.",
            'Небезопасные HTTP-методы'        => "Отключите небезопасные HTTP-методы ($problem) в конфигурации веб-сервера.",
            'Анонимный доступ FTP'            => "Анонимный вход на FTP-сервер разрешён. Отключите анонимный доступ.",
            'Стандартные учетные данные'      => "Обнаружены стандартные учетные данные. Смените их на уникальные.",
            default                           => "Требует внимания: \"$problem\".",
        };
    }
}
