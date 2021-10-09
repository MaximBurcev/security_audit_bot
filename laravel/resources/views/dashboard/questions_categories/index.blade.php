<?php
    /** @var \App\Models\QuestionCategory[] $categories */
?>
@extends('dashboard.layouts.app')

@section('h1'){{ trans('messages.questions_categories_list') }} @endsection

@section('content')
    <table class="table table-striped">
        <tbody>
            @each('dashboard.questions_categories.blocks.list.item', $categories, 'category')
        </tbody>
    </table>
@endsection
