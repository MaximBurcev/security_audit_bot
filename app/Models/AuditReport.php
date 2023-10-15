<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditReport extends BaseModel
{
    use HasFactory;

    protected $table = 'audits_reports';
}
