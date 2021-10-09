<?php
    /** @var \App\Models\Question[] $questions */
?>
<x-app-layout>
    <x-slot name="header">
        {{ trans('messages.questions_list') }}
    </x-slot>

    <table class="table-auto border-collapse border border-green-800">
        <tbody>
        @each('dashboard.questions.blocks.list.item', $questions, 'question')
        </tbody>
    </table>
</x-app-layout>
