<?php
$formClass = 'px-4 py-5 bg-white space-y-6 sm:p-6';
$texareaClass = 'shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md';
$inputDescriptionClass = 'mt-2 text-sm text-gray-500';
$labelClass = 'block text-sm font-medium text-gray-700';
$selectInputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$inputClass = 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300';
$btnClass = '"cursor-pointer ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"';
?>

<x-guest-layout>
    <x-slot name="header">
        {{ $pageH1 }}
    </x-slot>

    <div class="<?php echo $formClass; ?>">
        {{ Form::open($formOptions) }}

        <x-form-errors :errors="$errors" />

        <div class="grid grid-cols-3 gap-6">
            <div class="col-span-3 sm:col-span-2">
                {{ Form::label('question_category_id', trans('messages.question_category'),['class'=>$labelClass] ) }}
                <div class="mt-1 flex rounded-md shadow-sm">
                    {{ Form::select('question_category_id', $categoriesData, [], [ 'class'=>$selectInputClass.' h-60'] ) }}
                </div>
            </div>
        </div>

        {{ Form::submit(trans('messages.choose'), ['class' => $btnClass]) }}
        {{ Form::close() }}
    </div>

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    @endpush

</x-guest-layout>
