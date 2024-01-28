@extends('layout.app')

@section('content')

    <x-header type="terms" title="Отчет по проекту " />

    <div class="container">
        <p>{!! nl2br($report->content) !!} </p>
    </div>
@endsection
