<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Ищет известные уязвимости по версиям сервисов, определённым через "nmap -sV".
 *
 * Штатный NSE-скрипт vulners для этого не годится: его API закрыт Cloudflare
 * и молча не отдаёт ничего, поэтому запросы идут напрямую в NVD.
 *
 * Вызывается на этапе сканирования, а не при отрисовке отчёта: публичная
 * страница не должна ходить в сеть.
 */
final class CveLookupService
{
    /**
     * Как сервис называется в выводе nmap -> имя продукта в CPE.
     * Совпадают они далеко не всегда: "Apache httpd" в CPE зовётся http_server,
     * и без сопоставления запрос вернёт ноль находок.
     */
    private const PRODUCT_MAP = [
        'apache httpd'        => 'http_server',
        'apache tomcat'       => 'tomcat',
        'microsoft iis httpd' => 'internet_information_services',
        'samba smbd'          => 'samba',
        'postfix smtpd'       => 'postfix',
        'exim smtpd'          => 'exim',
        'openssh'             => 'openssh',
        'nginx'               => 'nginx',
        'mysql'               => 'mysql',
        'mariadb'             => 'mariadb',
        'postgresql'          => 'postgresql',
        'proftpd'             => 'proftpd',
        'vsftpd'              => 'vsftpd',
        'pure-ftpd'           => 'pure-ftpd',
        'dovecot'             => 'dovecot',
        'redis'               => 'redis',
        'mongodb'             => 'mongodb',
        'memcached'           => 'memcached',
        'elasticsearch'       => 'elasticsearch',
        'openresty'           => 'openresty',
        'lighttpd'            => 'lighttpd',
        'haproxy'             => 'haproxy',
        'php'                 => 'php',
    ];

    private const SEVERITY_MAP = [
        'CRITICAL' => 'critical',
        'HIGH'     => 'high',
        'MEDIUM'   => 'medium',
        'LOW'      => 'low',
    ];

    /**
     * Находки в том же виде, что отдают анализаторы отчётов.
     *
     * @param array<int, string> $output Строки вывода nmap
     * @return array<int, array<string, mixed>>
     */
    public function findForNmapOutput(array $output): array
    {
        $findings = [];

        foreach ($this->parseServices($output) as $service) {
            foreach ($this->lookup($service['product'], $service['version']) as $cve) {
                $findings[] = [
                    'type'           => 'CVE-уязвимость',
                    'problem'        => $cve['id'],
                    'recommendation' => sprintf(
                        '%s %s (порт %s): %s. Обновите компонент или примените патч. '
                        . 'Вывод основан на версии из баннера сервиса и может не учитывать '
                        . 'исправления, портированные вашим дистрибутивом.',
                        $service['product'],
                        $service['version'],
                        $service['port'],
                        $cve['score'] !== null ? "CVSS {$cve['score']}" : 'оценка CVSS недоступна'
                    ),
                    'severity'       => $cve['severity'],
                    'link'           => "https://nvd.nist.gov/vuln/detail/{$cve['id']}",
                ];
            }
        }

        return $findings;
    }

    /**
     * Сервисы с распознанными продуктом и версией из итоговой таблицы портов.
     *
     * @param array<int, string> $output
     * @return array<int, array{port: string, product: string, version: string}>
     */
    public function parseServices(array $output): array
    {
        $services = [];

        foreach ($output as $line) {
            if (preg_match('/^(\d+\/(?:tcp|udp))\s+open\s+\S+\s+(.+)$/', trim($line), $m) !== 1) {
                continue;
            }

            $banner = trim($m[2]);

            // "OpenSSH 7.4 (protocol 2.0)" -> продукт "OpenSSH", версия "7.4"
            if (preg_match('/^(.+?)\s+(\d+(?:\.\d+)+)/', $banner, $parts) !== 1) {
                continue;
            }

            $product = $this->normalizeProduct($parts[1]);

            if ($product === null) {
                continue;
            }

            $key = $product . ':' . $parts[2];

            $services[$key] = [
                'port'    => $m[1],
                'product' => $product,
                'version' => $parts[2],
            ];
        }

        return array_values($services);
    }

    /**
     * Только известные продукты: гадать по произвольному баннеру означает
     * засыпать пользователя чужими уязвимостями.
     */
    private function normalizeProduct(string $raw): ?string
    {
        $name = mb_strtolower(trim($raw));

        if (isset(self::PRODUCT_MAP[$name])) {
            return self::PRODUCT_MAP[$name];
        }

        // "nginx" встречается и как "nginx", и как часть более длинного баннера
        foreach (self::PRODUCT_MAP as $needle => $cpe) {
            if (str_starts_with($name, $needle)) {
                return $cpe;
            }
        }

        return null;
    }

    /**
     * @return array<int, array{id: string, score: float|null, severity: string}>
     */
    private function lookup(string $product, string $version): array
    {
        $cacheKey = "nvd:{$product}:{$version}";

        return Cache::remember(
            $cacheKey,
            (int) config('services.nvd.cache_ttl', 604800),
            fn() => $this->request($product, $version)
        );
    }

    /**
     * @return array<int, array{id: string, score: float|null, severity: string}>
     */
    private function request(string $product, string $version): array
    {
        $headers = [];

        if ($apiKey = config('services.nvd.api_key')) {
            $headers['apiKey'] = $apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout((int) config('services.nvd.timeout', 25))
                ->get((string) config('services.nvd.url'), [
                    'virtualMatchString' => "cpe:2.3:a:*:{$product}:{$version}",
                    'resultsPerPage'     => 50,
                ]);
        } catch (Throwable $e) {
            Log::warning('nvd lookup failed', ['product' => $product, 'error' => $e->getMessage()]);

            return [];
        }

        // Пауза между запросами: без ключа NVD разрешает 5 обращений за 30 секунд
        usleep((int) config('services.nvd.throttle_ms', 6500) * 1000);

        if (!$response->successful()) {
            Log::warning('nvd lookup rejected', ['product' => $product, 'status' => $response->status()]);

            return [];
        }

        return $this->extract($response->json('vulnerabilities') ?? []);
    }

    /**
     * @param array<int, mixed> $vulnerabilities
     * @return array<int, array{id: string, score: float|null, severity: string}>
     */
    private function extract(array $vulnerabilities): array
    {
        $result = [];

        foreach ($vulnerabilities as $entry) {
            $cve = $entry['cve'] ?? null;

            if (!is_array($cve) || empty($cve['id'])) {
                continue;
            }

            $metric = $this->firstMetric($cve['metrics'] ?? []);

            $result[] = [
                'id'       => (string) $cve['id'],
                'score'    => isset($metric['baseScore']) ? (float) $metric['baseScore'] : null,
                'severity' => self::SEVERITY_MAP[strtoupper((string) ($metric['baseSeverity'] ?? ''))] ?? 'medium',
            ];
        }

        // Сначала самые опасные, затем обрезаем: список из 74 CVE никто не прочитает
        usort($result, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));

        return array_slice($result, 0, (int) config('services.nvd.max_per_service', 10));
    }

    /**
     * @param array<string, mixed> $metrics
     * @return array<string, mixed>
     */
    private function firstMetric(array $metrics): array
    {
        foreach (['cvssMetricV31', 'cvssMetricV30', 'cvssMetricV2'] as $key) {
            if (!empty($metrics[$key][0]['cvssData'])) {
                $data = $metrics[$key][0]['cvssData'];

                // В CVSS v2 уровень лежит рядом с cvssData, а не внутри
                $data['baseSeverity'] ??= $metrics[$key][0]['baseSeverity'] ?? null;

                return $data;
            }
        }

        return [];
    }
}
