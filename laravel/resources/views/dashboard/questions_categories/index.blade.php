<?php
    /** @var \App\Models\QuestionCategory[] $categories */
?>
<x-app-layout>
    <x-slot name="header">
        {{ trans('messages.questions_categories_list') }}
    </x-slot>

    <table class="border-collapse border border-green-800">
        <tbody>
        @each('dashboard.questions_categories.blocks.list.item', $categories, 'category')
        </tbody>
    </table>
</x-app-layout>
