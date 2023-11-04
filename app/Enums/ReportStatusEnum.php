<?php

namespace App\Enums;

enum ReportStatusEnum: string
{
    case Created = "Создан";
    case InProcess = 'В процессе';
    case Finished = 'Завершен';
}
