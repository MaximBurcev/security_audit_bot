# Дорожная карта улучшений

Приоритизированный список работ по проекту. Составлен 2026-08-07 по результатам сплошного разбора
кодовой базы, проверки реестра `docs/technical-debt.md` (D1–D25) и аудита
`docs/SECURITY-AUDIT-2026-08-04.md`.

Отличие от `docs/technical-debt.md`: тот реестр фиксирует **дефекты как есть** и запрещает чинить их
попутно. Этот файл — **порядок работ**: что берём тикетом, в какой очерёдности и зачем. Ссылки вида
`D7` указывают на пункт реестра.

Каждый пункт берётся отдельным тикетом через `/aidd:idea`.

---

## Состояние на момент составления

**Аудит безопасности закрыт почти полностью.** Из 18 находок 2026-08-04 исправлены все критические:
маршрут `/test` с `shell_exec` удалён, `auth` добавлен на все группы `routes/admin.php:13,25,29`,
IDOR в API закрыт (`routes/api.php:31` — `only(['index','store'])`), webhook проверяет
`X-Telegram-Bot-Api-Secret-Token` (`WebhookController.php:28-42`), `escapeshellarg` есть в обоих
путях запуска (`BotReportJob.php:54`, `ReportUpdateCommand.php:56`), XSS в админке экранирован
(`resources/views/admin/reports/show.blade.php:59`), Slack-вебхук вычищен из `.env.example`.
Открытыми остались только «дизайнерские» пункты — они разнесены по разделам ниже (P1.21, P3.25,
P4.32).

**Реестр технического долга актуален целиком.** Из D1–D25 проверены 15 пунктов, исправлен частично
один (D4 — роут закрыт `auth:api`, сам контроллер не тронут).

---

## P0 — ломается в продакшене

Блокеры. Каждый пункт либо роняет боевой путь, либо приводит к молчаливой потере пользовательского
запроса.

### P0.1. `Service::update()` падает с TypeError (D1)

`BaseRepository::update()` — `app/Repositories/BaseRepository.php:32` — возвращает `int`, при этом
объявлены возвращающими модель: `app/Services/ProjectService.php:34`, `UserService.php:36`,
`UtilityService.php:38`, `AuditService.php:55`. В первых трёх включён `strict_types`.

Боевой путь: `app/Services/ProjectSyncService.php:43` → `php artisan app:projects.sync` падает при
обновлении названия проекта. Согласован только `ReportService.php:57` (`: int`).

### P0.2. Невалидный cron кладёт весь планировщик

`app/Http/Requests/Admin/Task/StoreFormRequest.php:27` валидирует `cron_format` как
`required|string`, тогда как `UpdateFormRequest.php:27-31` содержит regex. Значение уходит в
`app/Console/Kernel.php:28` → `->cron($task->cron_format)`.

Одна задача с мусорным cron — `InvalidArgumentException` в `CronExpression` — и `schedule:run`
перестаёт выполнять **все** задачи, включая чужие. Продублировать regex в `StoreFormRequest`.

### P0.3. `findAll()` падает на таблицах без soft deletes (D6)

`app/Repositories/BaseRepository.php:24` фильтрует `whereNull('deleted_at')` вручную. Колонки нет в
`2024_01_25_162328_create_bot_messages_table.php` и `2024_01_30_163943_create_tasks_table.php`.

Живой путь: `app/Services/BotMessageService.php:26` → `findAll()` по `bot_messages` → SQL error.
То же вручную в `ReportRepository::paginate()` (`:25`).

### P0.4. Долгий скан дублируется и теряется (частично D3)

`app/Jobs/BotReportJob.php:24-93` — нет `$tries`, `$timeout`, `$backoff`, `failed()`. При этом:

- `config/queue.php:41` — `retry_after => 90`
- `config/horizon.php:192-193` — `'tries' => 1`, `'timeout' => 60`
- `handle()` вызывает nmap/nikto/sslscan (`:54`), плюс `CveLookupService.php:195` делает
  `usleep(6500мс)` **на каждый найденный сервис внутри той же джобы**

Итог: воркер получает дубль задачи через 90 с → двойное сканирование и гонка на записи отчёта; либо
джоба убита по таймауту → отчёт навсегда остаётся в статусе «В процессе» (`:50-52`), `then()` не
выполняется, пользователь остаётся с «Пожалуйста, ожидайте».

Задать `$timeout` под реальное время скана, поднять `retry_after` выше таймаута, вынести задержку
NVD из джобы.

### P0.5. У батча нет обработчика ошибок

`app/Telegram/Commands/StartAuditCommand.php:121-139` — только `progress()` и `then()`, нет
`catch()`/`finally`. В связке с P0.4 любой сбой скана = бот молчит вечно.

### P0.6. Состояние диалога — позиционные пары

`app/Telegram/Commands/StartAuditCommand.php:61-71` читает `bot_messages` плоским списком и склеивает
пары «чётный = проект, нечётный = утилита».

- Нечётное число записей → обращение к `null` на `$arBotMessage[$i+1]->data`. Сценарий достижим:
  Telegram не гасит старые инлайн-клавиатуры, пользователь жмёт «Начать аудит» на прошлом сообщении.
- Два `project` подряд → `$utilityId` становится id проекта → `UtilityService::get()` вернёт `null`
  (см. D5).

`bot_messages` чистятся ровно в одном месте — `:106`, внутри `createAudit()`, который вызывается
только при непустом наборе (`:51`). Выбрал проект и ушёл — запись живёт вечно и отравляет следующий
аудит.

Хранить выбор одной строкой `project_id + utility_id` либо составным `callback_data`
`run:{projectId}:{utilityId}`.

### P0.7. Horizon смонтирован мимо очереди

`.env:21` — `QUEUE_CONNECTION=database`, `config/horizon.php:184` — `'connection' => 'redis'`, при
этом в `docker-compose.yml` сервиса redis нет вовсе. Дашборд всегда пустой, автоскейлинг не работает,
`queue:work` нужен отдельно.

Решение — одно из двух: перевести очередь на redis и добавить сервис в compose, либо убрать Horizon.

### P0.8. `CACHE_DRIVER=memcached` без memcached

`.env:19` — `memcached` (в `.env.example:19` — `file`), контейнера в `docker-compose.yml` нет,
`MEMCACHED_HOST=127.0.0.1` изнутри Sail недоступен. Кеш молча деградирует, следом ломается
cache-мьютекс `withoutOverlapping()` в планировщике.

### P0.9. Весь debug-поток уходит в Slack синхронно

`config/logging.php:57` — `'channels' => ['slack', 'single']`, `:81` — `'level' => env('LOG_LEVEL',
'critical')`, при `LOG_LEVEL=debug` в `.env:9` и `.env.example:9`. Один `LOG_LEVEL` применяется и к
`single`, и к `slack`, поэтому дефолт `critical` перебивается: каждый `Log::info()` внутри воркера —
синхронный HTTP-запрос на вебхук.

Ввести отдельный `LOG_SLACK_LEVEL=critical`, убрать `slack` из `stack` по умолчанию. Заодно `stack`
использует `single` вместо `daily` (`:61-66`) — при shared `storage/logs` между релизами (Envoy) файл
растёт без ротации.

---

## P1 — целостность данных и корректность

### P1.10. Нет ни одной транзакции (D7)

`grep -rn "DB::transaction" app` — пусто. Многошаговые записи без атомарности:
`app/Services/AuditService.php:25-28`, `app/Telegram/Commands/StartAuditCommand.php:81-103`.

### P1.11. Номер аудита через `count() + 1` (D8)

`app/Services/AuditService.php:67` — гонка и дубликаты при параллельных аудитах, плюс статический
вызов `AuditRepository::count()` в обход внедрённого экземпляра.

### P1.12. `Report::status` не кастуется (D9)

`$casts` у модели нет вовсе; пишется как `ReportStatusEnum` (`ReportService.php:35`,
`BotReportJob.php:51,71`, `ReportUpdateCommand.php:42,50,59`), читается строкой.

### P1.13. Нет `findOrFail` (D5)

`BaseRepository::find(): ?Model` (`:17`) под non-nullable сигнатурами `ProjectService.php:21`,
`UserService.php:23`, `UtilityService.php:22`, `ReportService.php:43`, `AuditService.php:42`.
Единственный `findOrFail` в `app/` — `Api/V1/ReportController.php:184`, а этот контроллер не
подключён к роутам.

### P1.14. Невалидный email у telegram-пользователя

`app/Services/UserService.php:55`:

```php
'email' => $telegramUser->username ?? $telegramUser->first_name . '@' . parse_url(config('app.url'), PHP_URL_HOST),
```

`.` связывает сильнее `??`, поэтому выражение эквивалентно `$username ?? ($first_name . '@host')`.
При наличии username в `email` пишется голый ник без домена — ломает уникальность адресов и любую
почтовую доставку (`app/Notifications/ReportUpdate.php`).

### P1.15. Плановый аудит слабее ручного

`app/Console/Commands/ReportUpdateCommand.php:56-60` пишет в `content` сырой вывод `shell_exec` — без
`stripAnsi`, без анализа, без CVE. Боевой путь бота (`BotReportJob.php:58-72`) делает всё это и
кладёт `{"raw":…,"analysis":…}`.

Последствия: после первого же планового перезапуска отчёт деградирует в legacy-формат (анализ
считается на лету при просмотре — `PublicReportController.php:26-38`), а **CVE-находки из NVD
теряются полностью** — они добавляются только в `BotReportJob.php:63-67`. ANSI-escape попадают в
`raw` и рендерятся на странице.

Корень — дублирование `stripAnsi` (D11) и `match утилита → стратегия` (D12): фабрики стратегий нет,
поэтому третий вызов её просто не получил. Чинить фабрикой, а не третьей копией.

### P1.16. Расписание в текущем виде вредит

Файл `app/Console/Kernel.php`.

- `:26` — `Task::query()->get()` при **каждом** запуске artisan. Недоступна БД → падает весь
  `schedule:run`, включая задачи, которым БД не нужна.
- `:18` — `app:clear-all-cache` ежедневно вызывает `route:clear` + `view:clear` + `cache:clear`
  (`app/Console/Commands/ClearAllCache.php:29-32`) и **не пересоздаёт** кеши, созданные при деплое.
  Заодно вычищает мьютексы `withoutOverlapping`.
- `:21` — `app:cache:warmup` интерактивна: `WarmupCacheCommand.php:52` (`$this->anticipate`) и `:56`
  (`$this->ask`). Под планировщиком без TTY поведение непредсказуемо; при пустом `CACHE_TTL` кладёт
  значения бессрочно. Передавать аргументы явно.
- `:18,21,28` — нет `withoutOverlapping()` и `onOneServer()` (есть только у `projects.sync` на `:24`);
  динамические `app:report.update` без `runInBackground()` будут наслаиваться и блокировать
  `schedule:run`.
- `:18-29` — `sendOutputTo(..., true)` для всех задач: четыре лог-файла растут без ротации.
- Не запланировано обязательное: `horizon:snapshot` (метрики не собираются вовсе),
  `queue:prune-batches`, `queue:prune-failed`, `telescope:prune`, бэкап БД. Таблицы `job_batches` и
  `failed_jobs` растут без ограничения.

---

## P2 — продукт

### P2.17. Показать результат в Telegram

`app/Telegram/Commands/StartAuditCommand.php:135` шлёт «Ссылки на отчеты №12 №13» — ни слова о том,
нашли ли что-нибудь. При этом `ReportAnalyzer::summarize()` (`app/ReportAnalyzer/ReportAnalyzer.php:76`)
уже считает `problems/passed` и используется только в blade
(`resources/views/public_report.blade.php:31`).

Подставить сводку в сообщение: «nmap: 3 проблемы (1 critical), sslscan: чисто». Самая дешёвая
доработка с самым заметным эффектом. Заодно разбить конкатенацию ссылок — сейчас упрётся в лимит
4096 символов на большом аудите.

### P2.18. Провалы UX бота

Всего шесть хендлеров — `routes/telegram.php:28,34,39,44,48,52`. Отсутствуют:

- `/cancel` и сброс состояния на `/start` (см. P0.6)
- история и повторный запуск: ссылки приходят один раз (`StartAuditCommand.php:135`), потерял
  сообщение — потерял отчёт; команд `/myaudits`, `/last` нет
- `answerCallbackQuery` — нет ни одного вызова, Telegram крутит спиннер на каждой кнопке до
  собственного таймаута
- пагинация в списке проектов: `StartCommand.php:52-54` — по кнопке в отдельной строке на каждый
  проект, при автосинке упрётся в лимит `reply_markup`
- «запустить все утилиты сразу» — сейчас на каждую комбинацию отдельный проход
  `ProjectCallback → UtilityCallback → more`
- сводная страница аудита: публичный роут только на один отчёт (`routes/web.php:30`), пользователь
  получает N ссылок и сшивает картину сам
- отмена запущенного аудита: `$batch->id` уходит только в лог (`StartAuditCommand.php:141`), нигде не
  персистится, `cancel()` вызвать неоткуда

### P2.19. Вынести severity и summary в колонки

Severity живёт строками-магией в пяти файлах (`NmapReportAnalyzerStrategy.php:45-55`,
`SslReportAnalyzerStrategy.php:44-58`, `NiktoReportAnalyzerStrategy.php:28-39`,
`CveLookupService.php:54-59`, `ReportAnalyzer.php:32`) и в blade
(`public_report.blade.php:17-30`). В БД анализ лежит внутри `content` — отдельных колонок нет
(миграции `2023_10_11_165120`, `2024_02_07_115344`).

Следствия: нельзя фильтровать и агрегировать по severity в SQL, нельзя посчитать «сколько critical по
всем проектам», нельзя строить diff, нельзя задать порог «аудит провален при critical». API анализ не
отдаёт вовсе (`V1/ReportResource` — только id/status/utility_id/project_id).

Один рефакторинг разблокирует сразу несколько фич ниже — брать до них.

### P2.20. Нет истории прогонов и diff между аудитами

Путь бота создаёт новый `Report` (`ReportService.php:29-39`) без связи с предыдущим; путь планировщика
**затирает тот же** `Report` на месте (`ReportUpdateCommand.php:57`). История не сохраняется вовсе,
сравнивать нечего.

Нужны неизменяемые прогоны + fingerprint находки. Ключ уже есть: `type|problem` используется как ключ
дедупа в `ReportAnalyzer.php:55`.

Смежное: дедупликация работает только внутри одного отчёта — между отчётами одного аудита (nmap и
nikto оба про порты/заголовки) дублей не убирает, объекта «аудит» в анализе нет. Нет подавления /
accept-risk: ложная находка всплывает в каждом прогоне. Что механизма не хватает, видно по частному
костылю `NiktoReportAnalyzerStrategy::dropContradicted()` (`:99-117`).

### P2.21. Ограничить цели сканирования

`StartCommand.php:52` показывает **все** проекты любому, кто нажал `/start`. Список пополняется кроном
из чужого HTML: `Kernel.php:22` → `ProjectSyncService::sync()`, запись идёт через
`ProjectSyncService.php:39` → `ProjectService::create()` **в обход** `Admin/Project/StoreFormRequest`
(`required|url|unique`) — то есть валидация работает только в админке. Ни whitelist, ни подтверждения
владения доменом, ни запрета приватных диапазонов. Rate-limit `throttle:120,1` (`routes/web.php:28`)
стоит на webhook целиком, а не на запуск аудита.

Для инструмента, стреляющего nmap/nikto по чужим хостам, это правовой риск, а не только продуктовый.
Нужны связь `project → owner`, verification владения (DNS/HTTP) и лимит на запуск. Соответствует
пунктам 13, 14 аудита безопасности.

### P2.22. Решить судьбу API

- **V1 существует, но не смонтирован.** `routes/api.php` регистрирует только `/users` (`:31`) и
  `/statistic` (`:33`); `Api\V1\ReportController` импортирован (`:4`) и не используется. Контроллер
  при этом полностью рабочий (`:151-189`), а OpenAPI-аннотации `:14-147` описывают пять эндпоинтов —
  Swagger UI отдаёт спеку, по которой ни один запрос не сработает. Это же роняет
  `tests/Feature/Api/ReportTest.php` (D2).
- **V2 — заглушка** (D14): `V2/ReportController.php:23-50` — четыре пустых метода, роутов нет,
  `V2/ReportResource` отдаёт сырой `content` строкой без пагинации (`Report::all()`).
- **Версионирования как механизма нет**: ни префикса в роутах, ни `Accept`-заголовка
  (`RouteServiceProvider.php:31-41`). Версии выражены только неймспейсами PHP — снаружи не выбираются.
- **Токен машинный, не пользовательский**: guard `auth:api` работает по `api_clients`, поэтому
  `GET /api/users` отдаёт всех пользователей любому держателю единственного токена, а
  `GenerateAuthToken.php:36` удаляет **все** токены при генерации нового. Rate limit
  `by($request->user()?->id ?: $request->ip())` (`RouteServiceProvider.php:27-29`) фактически работает
  по IP. При этом `laravel/sanctum` установлен и `User` использует `HasApiTokens` (`User.php:20`) —
  готовый механизм персональных токенов не задействован.
- **`POST /api/users` без валидации** (D4): `Api/UserController.php:30-35` — `forceCreate()` в обход
  `$fillable`, `Request` вместо FormRequest, возврат модели вместо `UserResource`.
- **Продуктовых эндпоинтов нет**: запустить аудит, узнать статус, получить находки — нельзя.
  `/statistic` — шесть `COUNT` прямо в замыкании роута (D16).

Решение принимать одно из двух — зарегистрировать и починить, либо удалить вместе с ресурсами и
аннотациями. Текущее состояние хуже обоих.

---

## P3 — процесс и инфраструктура

### P3.23. Нет CI (D25)

Нет `.github/`, нет `.gitlab-ci.yml`, при этом `.gitattributes:9` содержит `/.github export-ignore` —
остаток скелета. Ни один тест и линтер не запускаются автоматически, поэтому падающий
`tests/Feature/Api/ReportTest.php` никто не видит.

Минимум: `composer install` → `pint --test` → `phpunit` → `composer audit`.

### P3.24. Нет статического анализа (D25)

В `require-dev` только `laravel/pint` (`composer.json:24`) — без `pint.json`, из-за чего дефолтный
пресет конфликтует с фактическим стилем. PHPStan/Larastan/Psalm/Rector отсутствуют. В секции
`scripts` (`:52-67`) только автогенерённые хуки Laravel — нет `test`, `lint`, `analyse`.

### P3.25. Зависимости не обновлялись 19 месяцев

- **Laravel 10.48.25** — вне поддержки: bug fixes закончились 06.08.2024, security — 04.02.2025.
  Полтора года без патчей безопасности (пункт 10 аудита). Апгрейд 10 → 11 → 12.
- **`php: ^8.1`** (`composer.json:8`) — PHP 8.1 EOL с декабря 2025 (пункт 18 аудита). Поднять до
  `^8.2`/`^8.3`. Дополнительно рассинхрон: runtime Sail — 8.4, локальный CLI — 8.3.11.
- **`"laravel/horizon": "@stable"`, `"laravel/telescope": "@stable"`** (`:12,15`) при
  `"minimum-stability": "dev"` (`:78`) — любой `composer update` втянет несовместимый мажор.
  Зафиксировать констрейнты.
- **`"maximburcev/security_audit_bot_statistic_page": "@dev"`** (`:16`) — приватный пакет по
  dev-ветке **без секции `repositories`**: в чистом окружении не установится.
- **`"darkaonline/l5-swagger": "8.5.2"`** (`:22`) — жёсткий пин без `^`, не получает патчей.
- `composer.lock` датирован январём 2025; `composer audit` не запускался ни разу.
- Frontend: `vite ^4` (актуален 7.x), `laravel-vite-plugin ^0.8` (актуален 2.x).

### P3.26. Пробелы в деплое (Envoy.blade.php)

- Нет `route:cache` и `event:cache` — `config_project` делает только `config:cache` + `view:cache`.
  С учётом ежедневного `route:clear` (P1.16) кеш маршрутов не существует никогда.
- Нет reload php-fpm / сброса opcache после переключения симлинка: FPM с realpath-кешем продолжает
  отдавать файлы прошлого релиза.
- `npm install` вместо `npm ci` при наличии `package-lock.json` — сборка невоспроизводима.
- Нет rollback-задачи и нет smoke-check после `set_current`: если релиз не поднялся, симлинк уже
  переключён. Плюс `releases_clean` при `$releaseRotate = 5` фактически оставляет 4 релиза
  (`tail -n +5`).
- `queue_restart` вместо `horizon:terminate` — актуально после решения P0.7.

### P3.27. Критичные пробелы в тестах

Покрыто хорошо (699 строк unit): `ReportAnalyzer` и три стратегии, включая регрессии на реальных
отчётах; `PatternCompilationTest.php:34,53` проверяет компиляцию каждого паттерна и наличие severity;
`CveLookupServiceTest.php` — 12 тестов на моках HTTP.

Не покрыто вообще:

1. Весь `app/Telegram/**` и `routes/telegram.php` — то есть именно тот код, где живут P0.5 и P0.6.
2. `BotReportJob` — формат `{raw, analysis}`, вызов CVE только для nmap (`:63`), переходы статусов,
   пустой `shell_exec` (`:54` — отчёт молча уедет в `Finished` с пустым `raw`), неизвестная утилита
   (`:89-91` возвращает `[]`).
3. `PublicReportController` — `abort(401)` без подписи и **legacy-ветка `:26-38`**, единственный путь
   отображения для плановых отчётов.
4. `ProjectSyncService` — парсер чужого HTML (`:155-179`), самая хрупкая часть системы.
   `extractHost()` (`:188-220`: punycode, IP в скобках, путь, пробелы) — чистая функция, тестируется
   тривиально.
5. `ReportUpdateCommand`.

Плюс: `tests/Feature/Api/ReportTest.php` падает с `RouteNotFoundException` (D2, см. P2.22);
`tests/Feature/AppTest.php` недетерминирован — `RefreshDatabase` закомментирован (`:5`),
`User::where(...)->first()` (`:21`), `Audit::all()->random()` (`:88`), проверяются только HTTP-коды
(D22). В `phpunit.xml:15-19` есть `<source>`, но нет `<coverage>` с порогом; `DB_CONNECTION`/`DB_HOST`
не переопределены — задан только `DB_DATABASE=bot_test`.

Приоритет добора: юнит на `getAuditData()` с непарным списком → feature-тест `BotReportJob` с
фейковой утилитой `echo` → юнит на `extractHost()` → починить или удалить `ReportTest`.

### P3.28. Документация и конфигурация окружения

- `README.md` — 1 байт. При этом `CLAUDE.md` и `technical-audit.md` заигнорены (`.gitignore:14-15`) —
  новый разработчик не получает никакой документации.
- `.env.example` рассинхронизирован с `.env`: нет `LOG_SLACK_REPORT_CHANNEL_ID`, `SHOW_PER_PAGE`,
  `TELEGRAM_LOG_CHANNEL`, `TELEGRAM_TOKEN`, `TELESCOPE_ENABLED`.
- **Имя переменной токена расходится**: `config/nutgram.php:5` читает `TELEGRAM_TOKEN`, а
  `.env.example:65` предлагает `TELEGRAM_BOT_TOKEN` — ключ от мёртвого `config/telegram.php` (см.
  P4.30). Разработчик по инструкции получит бота без токена.
- `docker-compose.yml`: нет redis (P0.7), memcached (P0.8), mailpit (при `MAIL_HOST=mailpit` в
  `.env.example:29`); у `laravel.test` нет healthcheck, `depends_on: mysql` без
  `condition: service_healthy`; в образе нет nmap/nikto/sslscan, которые вызывает
  `BotReportJob.php:54` — локально бот нерабочий; phpmyadmin на 8050 в дефолтном compose.
- `SESSION_DRIVER=cookie` (`.env:22`, в примере — `file`): сессия целиком в куке, лимит 4 КБ.
- Не решён статус `.claude/`, `docs/`, `reports/`, `conventions.md`, `workflow.md` — постоянный шум в
  `git status`.

### P3.29. Доступ к служебным панелям

`app/Providers/HorizonServiceProvider.php:30-34` — `Gate::define('viewHorizon')` с пустым массивом
email: вне `local` дашборд недоступен никому. `TelescopeServiceProvider.php:61` — email захардкожен,
вынести в конфиг.

---

## P4 — чистка

Быстрые победы, каждая — меньше часа.

### P4.30. Удалить `app/Services/BotService.php` (D10)

200 строк, ноль внешних ссылок (`grep BotService` по `app/ routes/ config/ tests/` пуст). Копия
`StartAuditCommand` + `ProjectCallback` + `UtilityCallback` на SDK `Telegram\Bot\Api` (`:18-19`),
которого **нет в зависимостях** (`grep telegram-bot-sdk composer.lock` = 0). Заодно удалить мёртвый
`config/telegram.php` (271 строка) — единственные ссылки на него из этого же файла.

### P4.31. `getPublished()` по несуществующей колонке (D13)

`where('is_published', true)` в **пяти** репозиториях (реестр упоминает три): `ReportRepository.php:20`,
`UserRepository.php:20`, `AuditRepository.php:25`, `ProjectRepository.php:20`,
`UtilityRepository.php:20`. Строки `is_published` нет ни в одной миграции.

### P4.32. Остальное

- `env()` в рантайме (D20): `Admin/ReportController.php:27` — `env('SHOW_PER_PAGE', 15)`, при
  `config:cache` (его делает Envoy) значение всегда 15. Вынести в `config/`.
- Двойной префикс (D18): `routes/admin.php:14-22` — `/admin/projects/projects`, повторено пять раз.
  Сверить с `sail artisan route:list`.
- Бессрочные подписанные ссылки (пункт 15 аудита): `URL::signedRoute()` без `expiration` —
  `StartAuditCommand.php:131`, `ReportUpdateCommand.php:61`; отозвать нечем. При битой подписи —
  `abort(401)` без текста (`PublicReportController.php:43`).
- Мёртвая проверка `if (isset($this->strategy)) throw` — `ReportAnalyzer.php:19-25` (D15); контракт
  `ReportAnalyzerInterface.php:7-9` расходится с реализацией (D19).
- Тройное логирование одного события — `UtilityService.php:32-34` (D21).
- Заглушки V2 и мелочи D23: `BaseModel extends  Model` с пустым телом, чужие докблоки на моделях,
  `new BotReportJob(...)` с четырьмя аргументами при конструкторе на один
  (`StartAuditCommand.php:90`), три одинаковых `getByTelegramId()` в одном методе (`:60,99,106`),
  неиспользуемые импорты, опечатка `add_sort_deletes_to_projects_table`, несогласованные кейсы enum.
- Артефакты в git: `.playwright-mcp/console-*.log`, `user.http`, `_ide_helper_nutgram.php`.

---

## Рекомендуемый порядок

1. **P0.1–P0.6** — один-два дня, снимает «бот молчит» и падение апдейтов. Самое дорогое по влиянию.
2. **P3.23 + P3.24** — CI и PHPStan, чтобы исправленное не отъезжало обратно.
3. **P2.17 + P1.15** — пользователь наконец видит результат, плановые аудиты перестают быть слабее
   ручных.
4. **P2.19** — колонки severity/summary как фундамент под P2.20, P2.22 и экспорт.
5. Дальше по приоритету разделов.

`P0.7–P0.9` (Horizon, memcached, Slack-логи) вклиниваются в шаг 1, если пользуются dev-окружением или
Horizon-дашбордом; иначе — вместе с шагом 2.
