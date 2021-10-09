<?php
/** @var \App\Models\Question $question */

$formClass = 'px-4 py-5 bg-white space-y-6 sm:p-6';
$texareaClass = 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md';
$inputDescriptionClass = 'mt-2 text-sm text-gray-500';
$labelClass = 'block text-sm font-medium text-gray-700';
$selectInputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$inputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$btnClass = '"cursor-pointer ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"';

// @todo: temporarily. remove.
$arrayExistCategories = $question->categories->pluck('id')->toArray();
$arrayExistCategories = count($arrayExistCategories) > 0 ? $arrayExistCategories : $question->getDefaultCategories();
?>

<x-app-layout>
    <x-slot name="header">
        {{ $pageH1 }}
    </x-slot>

    <div class="<?php echo $formClass; ?>">
        {{ Form::open($formOptions) }}

        <x-form-errors :errors="$errors" />


        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-3 sm:col-span-2">
                {{ Form::label('question_category_id[]', trans('messages.question_category'),['class'=>$labelClass] ) }}
                <div class="mt-1 flex rounded-md shadow-sm">
                    {{ Form::select('question_category_id[]', $categoriesData, $arrayExistCategories, [ 'class'=>$selectInputClass.' h-60', 'multiple'=>'multiple'] ) }}
                </div>
            </div>
        </div>

        <div>
            {{ Form::label('status', trans('messages.question_body_ru'),['class'=>$labelClass]) }}
            <div class="mt-1">
                <x-wysiwyg-textarea
                    field="title[ru]"
                    :value="$question->title('ru')->value"
                    id="questionTitleRu"
                    :textareaClass="$texareaClass"
                    editorName="questionTitleRuEditor"
                />
            </div>
            <p class="<?php echo $inputDescriptionClass;?>"></p>
        </div>

        {{--<div>
            {{ Form::label('status', trans('messages.question_body_en'),['class'=>$labelClass]) }}
            <div class="mt-1">
                <x-wysiwyg-textarea
                    field="title[en]"
                    :value="$question->title('en')->value"
                    id="questionTitleEn"
                    :textareaClass="$texareaClass"
                    editorName="questionTitleEnEditor"
                />
            </div>
            <p class="<?php echo $inputDescriptionClass;?>"></p>
        </div>--}}

        <hr/>

        @foreach ($question->answers as $key => $answer)
            <div>
                {{ Form::label('answer'.$answer->id.'ru', trans('messages.answer'.($key+1).'_ru'),['class'=>$labelClass]) }}
                <div class="mt-1">

                    @php($field = 'answer['.$answer->id.'][ru]')
                    @php($id = 'answer'.$answer->id.'ru')
                    @php($editorName = 'answerEditor'.$answer->id.'ru')

                    <x-wysiwyg-textarea
                        :field="$field"
                        :value="$answer->text('ru')->value"
                        :id="$id"
                        :textareaClass="$texareaClass"
                        :editorName="$editorName"
                    />
                </div>
                <p class="<?php echo $inputDescriptionClass;?>"></p>
            </div>


            {{--<div>
                {{ Form::label('status', trans('messages.answer'.($key+1).'_en'),['class'=>$labelClass]) }}
                <div class="mt-1">

                    @php($field = 'answer['.$answer->id.'][en]')
                    @php($id = 'answer'.$answer->id.'en')
                    @php($editorName = 'answerEditor'.$answer->id.'en')

                    <x-wysiwyg-textarea
                        :field="$field"
                        :value="$answer->text('en')->value"
                        :id="$id"
                        :textareaClass="$texareaClass"
                        :editorName="$editorName"
                    />
                </div>
                <p class="<?php echo $inputDescriptionClass;?>"></p>
            </div>--}}
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

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @endpush

</x-app-layout>
