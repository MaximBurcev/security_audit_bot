<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function reports(): BelongsToMany
    {
        return $this->BelongsToMany(Report::class);
    }
}
