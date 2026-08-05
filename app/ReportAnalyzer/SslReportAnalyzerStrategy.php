<?php

namespace App\ReportAnalyzer;

class SslReportAnalyzerStrategy implements ReportAnalyzerInterface
{
    /** За сколько дней до истечения сертификата начинать предупреждать. */
    private const CERT_WARN_DAYS = 30;

    private const CERT_CRITICAL_DAYS = 7;

    /**
     * @param int|null $now Точка отсчёта для срока действия сертификата;
     *                      выносится наружу, чтобы проверки были детерминированы.
     */
    public function __construct(private ?int $now = null)
    {
        $this->now ??= time();
    }



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
        'Шифры в режиме CBC'        => 'medium',
        'Скорое истечение сертификата' => 'medium',
        'Протоколы'                 => 'ok',
        'Шифры'                     => 'ok',
        'Heartbleed'                => 'ok',
        'Сертификат'                => 'ok',
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

        return array_merge($recommendations, $this->checkWholeReport($output, $recommendations));
    }

    /**
     * Проверки, которые нельзя сделать построчно: они смотрят на отчёт целиком
     * и отвечают в том числе «проблем нет», чтобы пустой отчёт не был двусмысленным.
     *
     * @param array<int, string> $output
     * @param array<int, array<string, mixed>> $found Уже найденные построчно проблемы
     * @return array<int, array<string, mixed>>
     */
    private function checkWholeReport(array $output, array $found): array
    {
        $text = implode("\n", $output);
        $foundTypes = array_column($found, 'type');
        $checks = [];

        // Устаревшие протоколы: раз ни один не найден включённым — проверка пройдена
        if (!in_array('Устаревший протокол', $foundTypes, true)
            && preg_match('/(?:SSLv[23]|TLSv1\.[01])\s+disabled/', $text) === 1) {
            $checks[] = $this->make('Протоколы', 'SSLv2, SSLv3, TLS 1.0 и 1.1 отключены');
        }

        // Шифры в режиме CBC — не «слабые», но уступают AEAD (GCM/CCM/ChaCha20)
        $cbc = $this->cbcCiphers($output);

        if ($cbc !== []) {
            $checks[] = $this->make('Шифры в режиме CBC', count($cbc) . ' ' . $this->plural(count($cbc)));
        } elseif (!in_array('Небезопасный шифр', $foundTypes, true)
            && preg_match('/(?:Accepted|Preferred)\s+TLSv1\.[23]/', $text) === 1) {
            $checks[] = $this->make('Шифры', 'используются только AEAD-наборы');
        }

        if (preg_match('/not vulnerable to heartbleed/i', $text) === 1
            && preg_match('/(?<!not )vulnerable to heartbleed/i', $text) !== 1) {
            $checks[] = $this->make('Heartbleed', 'сервер не уязвим');
        }

        $certificate = $this->checkCertificate($text);

        if ($certificate !== null) {
            $checks[] = $certificate;
        }

        return $checks;
    }

    /**
     * Наборы шифров без AEAD: у них режим CBC, уязвимый к атакам вроде Lucky13.
     *
     * @param array<int, string> $output
     * @return array<int, string>
     */
    private function cbcCiphers(array $output): array
    {
        $ciphers = [];

        foreach ($output as $line) {
            if (preg_match('/(?:Accepted|Preferred)\s+TLSv1\.[0-3]\s+\d+\s+bits\s+(\S+)/', $line, $m)
                && preg_match('/GCM|CCM|CHACHA20|POLY1305|TLS_AES/i', $m[1]) !== 1) {
                $ciphers[$m[1]] = true;
            }
        }

        return array_keys($ciphers);
    }

    /**
     * Срок действия сертификата. Истёкший ловится построчным паттерном,
     * здесь важнее предупредить заранее — до того, как сайт станет недоступен.
     */
    private function checkCertificate(string $text): ?array
    {
        if (preg_match('/Not valid after:\s+(.+)/', $text, $m) !== 1) {
            return null;
        }

        $expiresAt = strtotime(trim($m[1]));

        if ($expiresAt === false) {
            return null;
        }

        $days = (int) floor(($expiresAt - $this->now) / 86400);

        if ($days < 0) {
            return $this->make('Истекший сертификат', 'истёк ' . abs($days) . ' дн. назад');
        }

        if ($days <= self::CERT_CRITICAL_DAYS) {
            return $this->make('Скорое истечение сертификата', "истекает через $days дн.", 'high');
        }

        if ($days <= self::CERT_WARN_DAYS) {
            return $this->make('Скорое истечение сертификата', "истекает через $days дн.");
        }

        return $this->make('Сертификат', "действует ещё $days дн.");
    }

    private function make(string $type, string $problem, ?string $severity = null): array
    {
        return [
            'type'           => $type,
            'problem'        => $problem,
            'recommendation' => $this->getRecommendation($type, $problem),
            'severity'       => $severity ?? $this->severities[$type] ?? 'low',
        ];
    }

    private function plural(int $count): string
    {
        $mod10 = $count % 10;
        $mod100 = $count % 100;

        if ($mod10 === 1 && $mod100 !== 11) {
            return 'набор';
        }

        if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14)) {
            return 'набора';
        }

        return 'наборов';
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
            'Шифры в режиме CBC' => "Сервер принимает $problem шифров без AEAD. Режим CBC уязвим к атакам вроде Lucky13 — оставьте только GCM, CCM и ChaCha20-Poly1305.",
            'Скорое истечение сертификата' => "Сертификат $problem. Обновите его заранее, иначе сайт станет недоступен.",
            'Протоколы' => "Проверка пройдена: $problem.",
            'Шифры' => "Проверка пройдена: $problem.",
            'Heartbleed' => "Проверка пройдена: $problem.",
            'Сертификат' => "Проверка пройдена: сертификат $problem.",
            default => "Рекомендуется рассмотреть решение проблемы: \"$problem\".",
        };
    }
}
