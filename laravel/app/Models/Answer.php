<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Answer
 *
 * @property int $id
 * @property int $right
 * @property int $question_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereIsRight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereTextEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereTextRu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Answer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Answer extends Model
{

    const RIGHT_NO = 0;
    const RIGHT_YES = 1;

    protected $fillable = [
        'right',
        'question_id',
    ];


    public function isRight(): bool
    {
        return $this->right === static::RIGHT_YES;
    }


    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }


    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class,'entity');
    }

    public function text(): Translation
    {
        return $this->translations()
            ->where('locale', '=', App::getLocale())
            ->firstOrNew();
    }

}
