<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function project(): HasOne
    {
        return $this->HasOne(Project::class);
    }

    public function utility(): HasOne
    {
        return $this->hasOne(Utility::class);
    }

    public function audits(): BelongsToMany
    {
        return $this->belongsToMany(Audit::class);
    }
}
