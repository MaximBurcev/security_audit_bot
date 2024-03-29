<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BotMessage
 *
 * @mixin Builder
 */
class Audit extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = ['title', 'user_id'];
}
