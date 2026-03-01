@extends('layout.app')

@section('content')

    <x-header type="terms" title="Отчет по проекту "/>

    <div class="container">
        <p><strong>Дата обновления</strong>: {{$report->updated_at}}</p>
        <p><strong>Проект</strong>: {{$report->project->title}}</p>
        <p><strong>Утилита</strong>: {{$report->utility->title}}</p>

        <p>{!! nl2br(e($raw)) !!} </p>

        <h2>Рекомендации</h2>
        @php
            $severityBadge = ['critical' => 'danger', 'high' => 'warning', 'medium' => 'secondary', 'low' => 'info'];
            $severityLabel = ['critical' => 'Критическая', 'high' => 'Высокая', 'medium' => 'Средняя', 'low' => 'Низкая'];
        @endphp
        @foreach($recommendations as $recommendation)
            @php $severity = $recommendation['severity'] ?? 'low'; @endphp
            <p>
                <span class="badge bg-{{ $severityBadge[$severity] ?? 'secondary' }} mb-1">
                    {{ $severityLabel[$severity] ?? $severity }}
                </span><br>
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
