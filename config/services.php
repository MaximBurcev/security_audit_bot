<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
     * Отчёт Selectel Uptime Monitoring, из которого синхронизируется список проектов.
     * Страница отдаёт HTML без API, поэтому парсится разметка (см. ProjectSyncService).
     */
    /*
     * Поиск CVE по версиям сервисов, которые определил nmap -sV.
     * Ключ необязателен: без него NVD разрешает 5 запросов за 30 секунд,
     * с ключом — 50, поэтому пауза между запросами берётся из throttle_ms.
     */
    'nvd' => [
        'url'        => env('NVD_URL', 'https://services.nvd.nist.gov/rest/json/cves/2.0'),
        'api_key'    => env('NVD_API_KEY'),
        'timeout'    => (int) env('NVD_TIMEOUT', 25),
        'throttle_ms' => (int) env('NVD_THROTTLE_MS', 6500),
        'cache_ttl'  => (int) env('NVD_CACHE_TTL', 604800),
        'max_per_service' => (int) env('NVD_MAX_PER_SERVICE', 10),
    ],

    'uptime_report' => [
        'url'     => env('UPTIME_REPORT_URL'),
        'timeout' => (int) env('UPTIME_REPORT_TIMEOUT', 60),
        'scheme'  => env('UPTIME_REPORT_SCHEME', 'https'),
    ],

];
