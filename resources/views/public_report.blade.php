@extends('layout.app')

@section('content')

    <x-header type="terms" title="Отчет по проекту "/>

    <div class="container">
        <p><strong>Дата обновления</strong>: {{$report->updated_at}}</p>
        <p><strong>Проект</strong>: {{$report->project->title}}</p>
        <p><strong>Утилита</strong>: {{$report->utility->title}}</p>

        <p>{!! nl2br(e($raw)) !!} </p>

        <h2>Рекомендации</h2>
        @foreach($recommendations as $recommendation)
            <p>
                <strong>Тип:</strong> {{ $recommendation['type'] }}<br>
            <strong>Проблема:</strong> {{ $recommendation['problem'] }}<br>
            <strong>Рекомендация:</strong> {{ $recommendation['recommendation'] }}
            @if(!empty($recommendation['link']))
                <a href="{{ $recommendation['link'] }}" target="_blank" rel="noopener noreferrer">Подробнее →</a>
            @endif
            </p>
            <br>
        @endforeach
    </div>
@endsection
