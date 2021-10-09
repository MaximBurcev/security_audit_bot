@extends('dashboard.layouts.app')

@section('h1'){{ trans('messages.questions_categories_create') }} @endsection

@section('content')

    {{ Form::open(['url'=> route('dashboard.category.store') ]) }}

    <div class="row">
        <div class="col-12">
            {{ Form::label('question_category_id', trans('messages.parent_category')) }}
            {{ Form::select('question_category_id', $categoriesData, null, ['class'=>'custom-select d-block w-100'] ) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.title_ru')) }}
            {{ Form::text('title[ru]','',['class'=>'form-control w-100']) }}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ Form::label('status', trans('messages.title_en')) }}
            {{ Form::text('title[en]','',['class'=>'form-control w-100']) }}
        </div>
    </div>


    {{ Form::submit( trans('messages.save'), ['class' => 'btn btn-primary mb-2']) }}
    {{ Form::close() }}

@endsection
