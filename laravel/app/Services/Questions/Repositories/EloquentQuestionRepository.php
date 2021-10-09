<?php


namespace App\Services\Questions\Repositories;


use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Translation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EloquentQuestionRepository
{
    const DEFAULT_PERPAGE = 10;

    public function search(?int $perPage = null, array $with= []): LengthAwarePaginator
    {
        $query = Question::query();
        $query->with($with);
        return $query->paginate($perPage ?? static::DEFAULT_PERPAGE);
    }

    public function getStatuses()
    {
        return Question::getStatuses();
    }

    public function store(array $data): Question
    {
        $question = Question::create($data);

        $translationModels = $answerTranslationModels = $answerModels = [];

        foreach ( Arr::get($data, 'title', []) as $locale => $text) {
            $translationModels[] = new Translation([
                'locale' => $locale,
                'key' => 'title',
                'value' => $text ?? ''
            ]);
        }

        $question->translations()->saveMany($translationModels);

        foreach ($answerModels as $index => $answerModel) {
            $answerModel->translations()->saveMany($answerTranslationModels[$index]);
        }

        $questionCategories = [];
        foreach ( Arr::get($data, 'question_category_id', []) as $index => $questionCategoryId) {
            // @todo Избавиться от прямой зависимости от QuestionCategory
            $questionCategory = QuestionCategory::find($questionCategoryId);
            if ( $questionCategory !== null) {
                $questionCategories[] = $questionCategory;
            }
        }

        $question->categories()->saveMany($questionCategories);

        return $question;
    }

    public function addEmptyAnswer(Question $question)
    {
        $question->answers()->save( new Answer([
            'right' => Answer::RIGHT_NO
        ]) ) ;
    }

    public function update(Question $question, $data)
    {

        $titleRu = Arr::get($data,'title.ru');
        $titleEn = Arr::get($data,'title.en');

        if ( !in_array($titleRu, [null, $question->title('ru')->value]) ) {
            $translation = $question->title('ru');
            $translation->value = $titleRu;
            $translation->save();
        }

        if ( !in_array($titleEn, [null, $question->title('en')->value]) ) {
            $translation = $question->title('en');
            $translation->value = $titleEn;
            $translation->save();
        }

        $question->answers()->each(function (Answer $answer) use ($data){
            $answerTranslations = Arr::get($data,'answer.'.$answer->id,[]);
            foreach ($answerTranslations as $locale => $text) {
                if ($text === null) {
                    continue;
                }
                $translation = $answer->text($locale);
                $translation->value = $text;
                $translation->save();
            }
            $answer->save();
        });

        $questionCategories = [];
        foreach ( Arr::get($data, 'question_category_id', []) as $index => $questionCategoryId) {
            // @todo Избавиться от прямой зависимости от QuestionCategory
            $questionCategory = QuestionCategory::find($questionCategoryId);
            if ( $questionCategory !== null) {
                $questionCategories[] = $questionCategory;
            }
        }
        $question->categories()->detach();
        $question->categories()->saveMany($questionCategories);

        $question->update($data);
        return $question;
    }

    public function destroy(int $questionId): void
    {
        $question = Question::find($questionId);

        DB::beginTransaction();

        $question->answers()->each(function ($answer){
            $answer->translations()->delete();
        });

        $question->answers()->delete();
        $question->translations()->delete();
        $question->delete();

        DB::commit();
    }


}
