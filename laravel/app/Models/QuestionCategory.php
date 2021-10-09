<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\QuestionCategory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|QuestionCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionCategory extends Model
{
    protected $fillable = [
    ];


    public function questions()
    {
        return $this->belongsToMany(Question::class)
            ->using(QuestionQuestioncategory::class);
    }


    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'entity');
    }

    public function title(): Translation
    {
        return $this->translations()
            ->where('locale', '=', App::getLocale())
            ->firstOrNew();
    }

}
