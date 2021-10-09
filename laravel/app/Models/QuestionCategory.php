<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

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
    /*protected $fillable = [

    ];*/

    protected $guarded = [
        '_method',
        '_token'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class);
    }

    public function children(): hasMany
    {
        return $this->hasMany(QuestionCategory::class);
    }

    public function questions(): belongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->using(QuestionQuestioncategory::class);
    }

    public function getCategoriesTree(&$data, $item, $level = 0): void
    {
        if (!isset($data[$item->id])) {
            $data[$item->id] = $item->title()->value;
        }
        $item->children()->each(function ($child) use (&$data, $level){
            $data[$child->id] = str_repeat('&nbsp;', ++$level). ' - '.$child->title()->value;
            $child->getCategoriesTree($data, $child, $level);
        });
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
