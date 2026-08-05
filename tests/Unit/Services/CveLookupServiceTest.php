<?php

namespace Tests\Unit\Services;

use App\Services\CveLookupService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Тесты бьют по замоканному HTTP: настоящий NVD в тестах не дёргается.
 */
class CveLookupServiceTest extends TestCase
{
    private CveLookupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.nvd.throttle_ms'     => 0,
            'services.nvd.max_per_service' => 10,
        ]);

        Cache::flush();

        $this->service = new CveLookupService();
    }

    private function nvdResponse(array $items): array
    {
        return ['vulnerabilities' => array_map(fn($i) => [
            'cve' => [
                'id'      => $i[0],
                'metrics' => [
                    'cvssMetricV31' => [[
                        'cvssData' => ['baseScore' => $i[1], 'baseSeverity' => $i[2]],
                    ]],
                ],
            ],
        ], $items)];
    }

    public function test_it_parses_product_and_version_from_the_port_table(): void
    {
        $output = <<<'TXT'
PORT     STATE SERVICE VERSION
22/tcp   open  ssh     OpenSSH 7.4 (protocol 2.0)
80/tcp   open  http    nginx 1.14.2
TXT;

        $services = $this->service->parseServices(explode("\n", $output));

        $this->assertSame(
            [
                ['port' => '22/tcp', 'product' => 'openssh', 'version' => '7.4'],
                ['port' => '80/tcp', 'product' => 'nginx', 'version' => '1.14.2'],
            ],
            $services
        );
    }

    /**
     * В CPE у Apache продукт называется http_server — без сопоставления
     * запрос в NVD возвращает ноль находок.
     */
    public function test_it_maps_apache_to_its_cpe_product_name(): void
    {
        $services = $this->service->parseServices(['80/tcp open http Apache httpd 2.4.29 ((Ubuntu))']);

        $this->assertSame('http_server', $services[0]['product']);
    }

    public function test_unknown_products_are_skipped(): void
    {
        $output = [
            '8080/tcp open  http    Some Homegrown Daemon 1.2.3',
            '9999/tcp open  unknown',
        ];

        $this->assertSame([], $this->service->parseServices($output));
    }

    public function test_services_without_a_version_are_skipped(): void
    {
        $this->assertSame([], $this->service->parseServices(['80/tcp open http nginx']));
    }

    public function test_closed_ports_are_ignored(): void
    {
        $this->assertSame([], $this->service->parseServices(['80/tcp closed http nginx 1.14.2']));
    }

    public function test_the_same_service_is_queried_once(): void
    {
        $output = [
            '80/tcp  open  http  nginx 1.14.2',
            '443/tcp open  http  nginx 1.14.2',
        ];

        $this->assertCount(1, $this->service->parseServices($output));
    }

    public function test_it_builds_findings_with_severity_and_link(): void
    {
        Http::fake(['*' => Http::response($this->nvdResponse([['CVE-2019-9511', 7.5, 'HIGH']]))]);

        $findings = $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']);

        $this->assertCount(1, $findings);
        $this->assertSame('CVE-уязвимость', $findings[0]['type']);
        $this->assertSame('CVE-2019-9511', $findings[0]['problem']);
        $this->assertSame('high', $findings[0]['severity']);
        $this->assertSame('https://nvd.nist.gov/vuln/detail/CVE-2019-9511', $findings[0]['link']);
    }

    /**
     * Версия из баннера не учитывает бэкпорты дистрибутива — об этом должно
     * быть сказано прямо в рекомендации, иначе находка вводит в заблуждение.
     */
    public function test_recommendation_warns_about_distribution_backports(): void
    {
        Http::fake(['*' => Http::response($this->nvdResponse([['CVE-2019-9511', 7.5, 'HIGH']]))]);

        $findings = $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']);

        $this->assertStringContainsString('баннер', $findings[0]['recommendation']);
        $this->assertStringContainsString('CVSS 7.5', $findings[0]['recommendation']);
    }

    public function test_the_most_severe_cves_come_first_and_the_list_is_capped(): void
    {
        config(['services.nvd.max_per_service' => 3]);

        Http::fake(['*' => Http::response($this->nvdResponse([
            ['CVE-1', 5.3, 'MEDIUM'],
            ['CVE-2', 9.8, 'CRITICAL'],
            ['CVE-3', 7.5, 'HIGH'],
            ['CVE-4', 2.1, 'LOW'],
        ]))]);

        $findings = $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']);

        $this->assertCount(3, $findings);
        $this->assertSame(['CVE-2', 'CVE-3', 'CVE-1'], array_column($findings, 'problem'));
    }

    public function test_an_unavailable_nvd_does_not_break_the_scan(): void
    {
        Http::fake(['*' => Http::response('', 503)]);

        $this->assertSame([], $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']));
    }

    public function test_a_network_error_does_not_break_the_scan(): void
    {
        Http::fake(fn() => throw new \RuntimeException('connection refused'));

        $this->assertSame([], $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']));
    }

    public function test_results_are_cached_between_lookups(): void
    {
        Http::fake(['*' => Http::response($this->nvdResponse([['CVE-2019-9511', 7.5, 'HIGH']]))]);

        $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']);
        $this->service->findForNmapOutput(['80/tcp open http nginx 1.14.2']);

        Http::assertSentCount(1);
    }
}
