<?php


namespace App\Services\Questions\Repositories;


use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Translation;
use App\Services\Questions\DTO\CreateQuestionDTO;
use App\Services\Questions\DTO\DTOInterface;
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

    public function createFromDTO(DTOInterface $questionDTO): Question
    {
        $data = $questionDTO->toArray();
        return Question::create($data);
    }

    public function saveTranslations(Question $question, DTOInterface $questionDTO): self
    {
        $data = $questionDTO->toArray();
        $translationModels = [];

        foreach ( Arr::get($data, 'title', []) as $locale => $text) {
            $translationModels[] = new Translation([
                'locale' => $locale,
                'key' => 'title',
                'value' => $text ?? ''
            ]);
        }

        $question->translations()->saveMany($translationModels);
        return $this;
    }

    public function saveCategories(Question $question, DTOInterface $questionDTO): self
    {
        $data = $questionDTO->toArray();
        $questionCategories = [];
        foreach ( Arr::get($data, 'question_category_id', []) as $index => $questionCategoryId) {
            // @todo Избавиться от прямой зависимости от QuestionCategory
            $questionCategory = QuestionCategory::find($questionCategoryId);
            if ( $questionCategory !== null) {
                $questionCategories[] = $questionCategory;
            }
        }

        $question->categories()->saveMany($questionCategories);
        return $this;
    }


    public function addEmptyAnswer(Question $question): self
    {
        $question->answers()->save( new Answer([
            'right' => Answer::RIGHT_NO
        ]) ) ;
        return $this;
    }

    public function updateTitle(Question $question, DTOInterface $questionDTO): self
    {
        $data = $questionDTO->toArray();
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
        return $this;
    }

    public function updateAnswersTranslations(Question $question, DTOInterface $questionDTO): self
    {
        $data = $questionDTO->toArray();
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
        $question->update($data);
        return $this;
    }

    public function updateFromDTO(Question $question, DTOInterface $questionDTO): self
    {
        $data = $questionDTO->toArray();
        $question->update($data);
        return $this;
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
