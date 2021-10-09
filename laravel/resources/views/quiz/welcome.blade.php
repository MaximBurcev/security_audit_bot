<?php
    /** @var \App\Models\QuestionCategory[] $questionCategories */
?>
@extends('layouts.app')

@section('content')

    Добро пожаловать в нашу викторину по php!
    Выберите интересующий вас раздел вопросов:<br/>
    @foreach( $questionCategories as $questionCategory )
        {{ $questionCategory->title_ru }} <br/>
    @endforeach
@endsection
