@extends('layout.app')

@section('navigation')
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
        <div class="container px-4">
            <a class="navbar-brand" href="{{ route('main') }}">Аудит безопасности проекта</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('main') }}">Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('personal') }}">Личный кабинет</a></li>
                </ul>
            </div>
        </div>
    </nav>
@endsection

@section('header')
    <!-- Header-->
    <header class="bg-primary bg-gradient text-white">
        <div class="container px-4 text-center">
            <h1 class="fw-bolder">Личный кабинет</h1>
        </div>
    </header>
@endsection

@section('content')
    <div class="container">
        <p>Данные о пользователе</p>
    </div>
@endsection
