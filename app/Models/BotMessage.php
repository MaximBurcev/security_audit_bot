<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * BotMessage
 *
 * @mixin Builder
 */
class BotMessage extends Model
{
    protected $fillable = ['user_id', 'data'];
}
