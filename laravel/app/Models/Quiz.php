<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quiz extends Model
{
    const DEFAULT_QUESTIONS_COUNT = 10;

    protected $fillable = [
        'questions_max_count',
        'category_id',
        'quiz_type_id',
    ];


    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->using(QuizQuestion::class);
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class);
    }

}
