<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Синхронизирует список проектов с отчётом Selectel Uptime Monitoring.
 *
 * У отчёта нет API — только HTML-страница, поэтому разметка разбирается через DOM.
 * Синхронизация только добавляет и обновляет: проекты, пропавшие из отчёта, остаются
 * нетронутыми, чтобы недоступный сервис или смена вёрстки не вычистили базу.
 */
final class ProjectSyncService
{
    public function __construct(
        protected ProjectService $projectService
    )
    {
    }

    /**
     * Применяет план синхронизации.
     *
     * @return array{created: int, updated: int, skipped: array<int, string>, total: int}
     */
    public function sync(): array
    {
        $plan = $this->buildPlan();

        foreach ($plan['create'] as $row) {
            $this->projectService->create(['title' => $row['title'], 'url' => $row['url']]);
        }

        foreach ($plan['update'] as $row) {
            $this->projectService->update($row['id'], ['title' => $row['title']]);
        }

        $result = [
            'created' => count($plan['create']),
            'updated' => count($plan['update']),
            'skipped' => $plan['skipped'],
            'total'   => $plan['total'],
        ];

        Log::info('projects.sync', [
            'total'   => $result['total'],
            'created' => $result['created'],
            'updated' => $result['updated'],
            'skipped' => $plan['skipped'],
        ]);

        return $result;
    }

    /**
     * Тот же план, но без записи в базу.
     *
     * @return array{create: array<int, array{title: string, url: string}>, update: array<int, array{id: int, title: string, old_title: string}>, skipped: array<int, string>, total: int}
     */
    public function preview(): array
    {
        return $this->buildPlan();
    }

    /**
     * @return array{create: array<int, array{title: string, url: string}>, update: array<int, array{id: int, title: string, old_title: string}>, skipped: array<int, string>, total: int}
     */
    private function buildPlan(): array
    {
        $services = $this->fetchMonitoredServices();
        $existing = $this->existingProjectsByHost();
        $scheme = (string) config('services.uptime_report.scheme', 'https');

        $create = [];
        $update = [];
        $skipped = [];
        $seen = [];

        foreach ($services as $name) {
            $host = $this->extractHost($name);

            if ($host === null) {
                $skipped[] = $name;
                continue;
            }

            // Два разных названия могут свестись к одному хосту (например, с путём и без)
            if (isset($seen[$host])) {
                continue;
            }
            $seen[$host] = true;

            if (isset($existing[$host])) {
                $project = $existing[$host];

                if ($project->title !== $name) {
                    $update[] = [
                        'id'        => (int) $project->id,
                        'title'     => $name,
                        'old_title' => (string) $project->title,
                    ];
                }

                continue;
            }

            $create[] = ['title' => $name, 'url' => $scheme . '://' . $host];
        }

        return [
            'create'  => $create,
            'update'  => $update,
            'skipped' => $skipped,
            'total'   => count($services),
        ];
    }

    /**
     * Названия сервисов из таблицы отчёта.
     *
     * @return array<int, string>
     */
    private function fetchMonitoredServices(): array
    {
        $url = config('services.uptime_report.url');

        if (empty($url)) {
            throw new RuntimeException('UPTIME_REPORT_URL не задан');
        }

        // Отчёт отвечает нестабильно (страница ~200 КБ, случаются таймауты), поэтому ретраим.
        $response = Http::timeout((int) config('services.uptime_report.timeout', 60))
            ->retry(3, 5000, throw: false)
            ->get($url);

        if (!$response->successful()) {
            throw new RuntimeException("Отчёт недоступен: HTTP {$response->status()}");
        }

        return $this->parse($response->body());
    }

    /**
     * @return array<int, string>
     */
    private function parse(string $html): array
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        // Название сервиса — ячейка, за которой сразу идёт ячейка с типом проверки.
        $nodes = (new DOMXPath($document))->query('//tr/td[following-sibling::td[1][@class="type"]]');

        if ($nodes === false || $nodes->length === 0) {
            throw new RuntimeException('Не найдено ни одной строки сервиса — вероятно, изменилась вёрстка отчёта');
        }

        $services = [];
        foreach ($nodes as $node) {
            $name = trim($node->textContent);

            if ($name !== '') {
                $services[] = $name;
            }
        }

        return array_values(array_unique($services));
    }

    /**
     * Приводит название из отчёта к хосту.
     *
     * Отчёт содержит не только домены: встречаются человекочитаемые ярлыки
     * ("demoshop intensa", "iep-cosmo"), хост с IP в скобках, хост с путём
     * и кириллические домены. Всё, из чего нельзя получить хост, отсеивается.
     */
    private function extractHost(string $name): ?string
    {
        $value = trim($name);

        // "arsenal-tula.ru (95.213.135.100)" — оставляем только хост
        $value = trim(preg_replace('/\s*\(.*\)\s*$/u', '', $value) ?? $value);

        // "romibot.mindbox.ru/health" — путь сканеру не нужен, он работает по хосту
        $value = explode('/', $value)[0];

        if ($value === '' || str_contains($value, ' ') || !str_contains($value, '.')) {
            return null;
        }

        $value = mb_strtolower($value);

        // "композитнаядолина.рф" — в URL домен должен быть в punycode
        if (preg_match('/[^\x20-\x7f]/', $value) === 1) {
            $converted = idn_to_ascii($value, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

            if ($converted === false) {
                return null;
            }

            $value = $converted;
        }

        if (filter_var('https://' . $value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $value;
    }

    /**
     * Существующие проекты, разложенные по хосту — так синхронизация не плодит
     * дубли для проектов, заведённых вручную с другой схемой или путём.
     *
     * @return array<string, Project>
     */
    private function existingProjectsByHost(): array
    {
        $projects = [];

        foreach (Project::query()->get() as $project) {
            $host = parse_url((string) $project->url, PHP_URL_HOST);

            if (!empty($host)) {
                $projects[mb_strtolower($host)] = $project;
            }
        }

        return $projects;
    }
}
