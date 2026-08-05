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

    /**
     * Порты, доступность которых снаружи сама по себе является проблемой:
     * СУБД, кеши, средства удалённого управления. Открытый 80/443 — норма,
     * открытый 3306 — нет, поэтому у них не может быть одинаковой важности.
     */
    private array $sensitivePorts = [
        21    => ['medium', 'FTP'],
        22    => ['medium', 'SSH'],
        23    => ['high', 'Telnet'],
        135   => ['high', 'MS RPC'],
        139   => ['high', 'NetBIOS'],
        445   => ['high', 'SMB'],
        1433  => ['high', 'MS SQL Server'],
        1521  => ['high', 'Oracle DB'],
        3306  => ['high', 'MySQL'],
        3389  => ['high', 'RDP'],
        5432  => ['high', 'PostgreSQL'],
        5984  => ['high', 'CouchDB'],
        6379  => ['high', 'Redis'],
        9200  => ['high', 'Elasticsearch'],
        11211 => ['high', 'Memcached'],
        27017 => ['high', 'MongoDB'],
        33060 => ['high', 'MySQL X Protocol'],
    ];

    private array $severities = [
        'Стандартные учетные данные'    => 'critical',
        'CVE-уязвимость'               => 'high',
        'Уязвимость'                   => 'high',
        'Небезопасная конфигурация SMB' => 'high',
        'Анонимный доступ FTP'         => 'high',
        'Небезопасные HTTP-методы'     => 'medium',
        'Открытый порт'                => 'low',
        'Чувствительные порты'         => 'ok',
        'Уязвимости'                   => 'ok',
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
                    $port = $type === 'Открытый порт' ? (int) $matches[1] : null;

                    $item = [
                        'type'           => $type,
                        'problem'        => $problem,
                        'recommendation' => $this->getRecommendation($type, $problem, $port),
                        'severity'       => $this->resolveSeverity($type, $port),
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

        return array_merge(array_values($recommendations), $this->passedChecks($output, $recommendations));
    }

    /**
     * Пройденные проверки: без них пустой список неотличим от «сканирование не отработало».
     *
     * @param array<int, string> $output
     * @param array<int, array<string, mixed>> $found
     * @return array<int, array<string, mixed>>
     */
    private function passedChecks(array $output, array $found): array
    {
        // Итоговая таблица портов — признак того, что сканирование дошло до конца
        if (preg_match('/^PORT\s+STATE\s+SERVICE/m', implode("\n", $output)) !== 1) {
            return [];
        }

        $types = array_column($found, 'type');
        $checks = [];

        $sensitiveFound = array_filter(
            $found,
            fn($item) => $item['type'] === 'Открытый порт' && $item['severity'] !== 'low'
        );

        if ($sensitiveFound === []) {
            $checks[] = [
                'type'           => 'Чувствительные порты',
                'problem'        => 'не обнаружены',
                'recommendation' => 'Проверка пройдена: СУБД, кеши и средства удалённого управления снаружи не видны.',
                'severity'       => 'ok',
                'link'           => null,
            ];
        }

        if (!array_intersect(['Уязвимость', 'CVE-уязвимость'], $types)) {
            $checks[] = [
                'type'           => 'Уязвимости',
                'problem'        => 'не обнаружены',
                'recommendation' => 'Проверка пройдена: NSE-скрипты не нашли известных уязвимостей.',
                'severity'       => 'ok',
                'link'           => null,
            ];
        }

        return $checks;
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

    private function resolveSeverity(string $type, ?int $port): string
    {
        if ($type === 'Открытый порт' && $port !== null && isset($this->sensitivePorts[$port])) {
            return $this->sensitivePorts[$port][0];
        }

        return $this->severities[$type] ?? 'low';
    }

    public function getRecommendation($type, $problem, ?int $port = null): string
    {
        if ($type === 'Открытый порт' && $port !== null && isset($this->sensitivePorts[$port])) {
            $service = $this->sensitivePorts[$port][1];

            return "Порт $problem ($service) доступен снаружи. Такие сервисы не должны "
                . "смотреть в интернет: ограничьте доступ файрволом или VPN.";
        }

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
