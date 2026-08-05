@extends('layout.app')

@section('content')

    <x-header type="terms" title="Отчет по проекту "/>

    <div class="container">
        <p><strong>Дата обновления</strong>: {{$report->updated_at}}</p>
        <p><strong>Проект</strong>: {{$report->project?->title ?? '—'}}</p>
        <p><strong>Утилита</strong>: {{$report->utility?->title ?? '—'}}</p>

        <p>{!! nl2br(e($raw)) !!} </p>

        <h2>Рекомендации</h2>
        @php
            $severityBadge = [
                'critical' => 'danger',
                'high'     => 'danger',
                'medium'   => 'warning',
                'low'      => 'info',
                'ok'       => 'success',
            ];
            $severityLabel = [
                'critical' => 'Критическая',
                'high'     => 'Высокая',
                'medium'   => 'Средняя',
                'low'      => 'Низкая',
                'ok'       => 'ОК',
            ];
            $summary = \App\ReportAnalyzer\ReportAnalyzer::summarize($recommendations);
        @endphp

        @if(empty($recommendations))
            <p>Проверки не выполнялись — не удалось разобрать вывод утилиты.</p>
        @else
            <p>
                @if($summary['problems'] > 0)
                    <span class="badge bg-danger">Проблем: {{ $summary['problems'] }}</span>
                @else
                    <span class="badge bg-success">Проблем не обнаружено</span>
                @endif
                @if($summary['passed'] > 0)
                    <span class="badge bg-success">Проверок пройдено: {{ $summary['passed'] }}</span>
                @endif
            </p>
        @endif

        @foreach($recommendations as $recommendation)
            @php $severity = $recommendation['severity'] ?? 'low'; @endphp
            <p>
                <span class="badge bg-{{ $severityBadge[$severity] ?? 'secondary' }} mb-1">
                    {{ $severityLabel[$severity] ?? $severity }}
                </span><br>
                <strong>Тип:</strong> {{ $recommendation['type'] }}<br>
                <strong>{{ $severity === 'ok' ? 'Результат' : 'Проблема' }}:</strong> {{ $recommendation['problem'] }}<br>
                <strong>Рекомендация:</strong> {{ $recommendation['recommendation'] }}
                @if(!empty($recommendation['link']))
                    <a href="{{ $recommendation['link'] }}" target="_blank" rel="noopener noreferrer">Подробнее →</a>
                @endif
            </p>
            <br>
        @endforeach
    </div>
@endsection
