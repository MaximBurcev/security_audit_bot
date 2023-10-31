<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'url'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
