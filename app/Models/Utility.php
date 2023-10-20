<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utility extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function reports(): HasMany
    {
        return $this->HasMany(Report::class);
    }
}
