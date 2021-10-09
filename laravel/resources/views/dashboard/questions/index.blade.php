<?php
    /** @var \App\Models\Question[] $questions */
?>
@extends('dashboard.layouts.app')

@section('h1'){{ trans('messages.questions_list') }} @endsection

@section('content')
    <table class="table table-striped">
        <tbody>
            @each('dashboard.questions.blocks.list.item', $questions, 'question')
        </tbody>
    </table>
@endsection
