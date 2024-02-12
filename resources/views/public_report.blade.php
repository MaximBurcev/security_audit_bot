@extends('layout.app')

@section('content')

    <x-header type="terms" title="Отчет по проекту "/>

    <div class="container">
        <p><strong>Дата обновления</strong>: {{$report->updated_at}}</p>

        <p>{!! nl2br($report->content) !!} </p>
    </div>
@endsection
