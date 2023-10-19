<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditReport extends BaseModel
{
    use HasFactory;

    protected $table = 'audit_report';
}
