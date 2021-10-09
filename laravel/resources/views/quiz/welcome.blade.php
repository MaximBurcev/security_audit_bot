@extends('layouts.app')

@section('content')
    Добро пожаловать в нашу викторину по php!
    Выберите интересующий вас раздел вопросов:
    @foreach($tasksGroups as $tasksGroup)
        {{ $tasksGroup }} <br/>
    @endforeach
@endsection
