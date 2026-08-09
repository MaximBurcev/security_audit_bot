<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Cron\CronExpression;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Проверяет cron-выражение тем же парсером, которым его потом исполняет планировщик
 * (`dragonmantank/cron-expression`). Регулярка не годится: она пропускает синтаксически похожие,
 * но невалидные значения вроде `99 * * * *`, а невалидное выражение роняет весь `schedule:run` —
 * вместе с задачами, к этой записи отношения не имеющими.
 */
final class ValidCronExpression implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !CronExpression::isValidExpression($value)) {
            $fail('Неверный формат cron-выражения');
        }
    }
}
