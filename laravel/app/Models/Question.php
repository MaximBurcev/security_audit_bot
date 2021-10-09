<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTextEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTextRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Question extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 20;

    protected $fillable = [
        'status',
    ];

    public function categories()
    {
        return $this->belongsToMany(QuestionCategory::class)
            ->using(QuestionQuestioncategory::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class,'entity');
    }

    public function title(): Translation
    {
        return $this->translations()
            ->where('locale', '=', App::getLocale())
            ->where('key','=','title')
            ->firstOrNew();
    }

    public function comment(): Translation
    {
        return $this->translations()
            ->where('locale', '=', App::getLocale())
            ->where('key','=','comment')
            ->firstOrNew();
    }

}
