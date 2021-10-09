<?php
/** @var \App\Models\Question $question */

$formClass = 'px-4 py-5 bg-white space-y-6 sm:p-6';
$texareaClass = 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md';
$inputDescriptionClass = 'mt-2 text-sm text-gray-500';
$labelClass = 'block text-sm font-medium text-gray-700';
$selectInputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$inputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$btnClass = '"cursor-pointer ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"';
?>

<x-app-layout>
    <x-slot name="header">
        {{ trans('messages.questions_create') }}
    </x-slot>

    <div class="<?php echo $formClass; ?>">
        {{ Form::open($formOptions) }}

        <x-form-errors :errors="$errors" />

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-3 sm:col-span-2">
                {{ Form::label('question_category_id[]', trans('messages.question_category'),['class'=>$labelClass] ) }}
                <div class="mt-1 flex rounded-md shadow-sm">
                    {{ Form::select('question_category_id[]', $categoriesData, $question->categories->pluck('id')->toArray(), [ 'class'=>$selectInputClass, 'multiple'=>'multiple'] ) }}
                </div>
            </div>
        </div>

        <div>
            {{ Form::label('status', trans('messages.question_body_ru'),['class'=>$labelClass]) }}
            <div class="mt-1">
                {{ Form::textarea('title[ru]', $question->title('ru')->value, ['rows'=>3,'class'=> $texareaClass]) }}
            </div>
            <p class="<?php echo $inputDescriptionClass;?>"></p>
        </div>

        <div>
            {{ Form::label('status', trans('messages.question_body_en'),['class'=>$labelClass]) }}
            <div class="mt-1">
                {{ Form::textarea('title[en]', $question->title('en')->value,['rows'=>3,'class'=> $texareaClass]) }}
            </div>
            <p class="<?php echo $inputDescriptionClass;?>"></p>
        </div>

        <hr/>

        @foreach ($question->answers as $key => $answer)
            <div>
                {{ Form::label('status', trans('messages.answer'.($key+1).'_ru'),['class'=>$labelClass]) }}
                <div class="mt-1">
                    {{ Form::textarea('answer['.$answer->id.'][ru]', $answer->text('ru')->value, ['rows'=>3,'class'=> $texareaClass]) }}
                </div>
                <p class="<?php echo $inputDescriptionClass;?>"></p>
            </div>

            <div>
                {{ Form::label('status', trans('messages.answer'.($key+1).'_en'),['class'=>$labelClass]) }}
                <div class="mt-1">
                    {{ Form::textarea('answer['.$answer->id.'][en]', $answer->text('en')->value, ['rows'=>3,'class'=> $texareaClass]) }}
                </div>
                <p class="<?php echo $inputDescriptionClass;?>"></p>
            </div>
        @endforeach

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-3 sm:col-span-2">
                {{ Form::label('status', trans('messages.status'),['class'=>$labelClass] ) }}
                <div class="mt-1 flex rounded-md shadow-sm">
                    {{ Form::select('status', $statuses, $question->status, ['class'=>$selectInputClass] ) }}
                </div>
            </div>
        </div>


        {{ Form::submit(trans('messages.save'), ['class' => $btnClass]) }}
        {{ Form::close() }}
    </div>

    @if($question->exists)
    <div class="<?php echo $formClass; ?>">
        {{ Form::open(['url' => route('dashboard.question.addEmptyAnswer', ['question' => $question]),'method' => 'POST']) }}
        {{ Form::submit(trans('messages.answers_create'), ['class' => $btnClass]) }}
        {{ Form::close() }}
    </div>
    @endif

</x-app-layout>
