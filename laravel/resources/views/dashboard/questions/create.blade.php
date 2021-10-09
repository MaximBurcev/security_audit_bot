<?php
/** @var \App\Models\Question $questions */

?>
@extends('dashboard.layouts.app')

@section('h1'){{ trans('messages.questions_create') }} @endsection

@section('content')

    {{ Form::open(['url'=> route('dashboard.question.store') ]) }}

    <div class="row">
        <div class="col-12">
            {{ Form::label('question_category_id', trans('messages.question_category')) }}
            {{ Form::select('question_category_id', $categoriesData, null, ['class'=>'custom-select d-block w-100','multiple'=>'multiple'] ) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.question_body_ru')) }}
            {{ Form::textarea('title[ru]','',['class'=>'form-control w-100']) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.question_body_en')) }}
            {{ Form::textarea('title[en]','',['class'=>'form-control w-100']) }}
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.answer1_ru')) }}
            {{ Form::textarea('answer[0][ru]','',['class'=>'form-control w-100']) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.answer1_en')) }}
            {{ Form::textarea('answer[0][en]','',['class'=>'form-control w-100']) }}
        </div>
    </div>




    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.status')) }}
            {{ Form::select('status', $statuses, null, ['class'=>'custom-select d-block w-100'] ) }}
        </div>
    </div>

    {{ Form::submit(trans('messages.save'), ['class' => 'btn btn-primary mb-2']) }}
    {{ Form::close() }}

@endsection



@section('buttons_toolbar')
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-secondary">Все</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">ru</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">en</button>
        </div>
    </div>
@endsection
