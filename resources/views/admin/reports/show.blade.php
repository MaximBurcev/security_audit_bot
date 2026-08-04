@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Отчет № #{{ $report->id }}</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <tbody>
                            <tr>
                                <td>ID</td>
                                <td>{{ $report->id }}</td>
                            </tr>
                            <tr>
                                <td>Статус</td>
                                <td>{{ $report->status }}</td>
                            </tr>
                            @if($report->utility)
                                <tr>
                                    <td>Утилита</td>
                                    <td>
                                        {{ $report->utility->title }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>Проект</td>
                                <td>
                                    @if($report->project)
                                        {{ $report->project->title }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Результат сканирования</td>
                                <td>
                                    @php
                                        // Вывод сканера контролируется сканируемым хостом (баннеры, заголовки),
                                        // поэтому экранируем его перед nl2br. Новый формат — JSON с ключом raw,
                                        // старый — сырой текст.
                                        $data = json_decode($report->content ?? '', true);
                                        $scanOutput = is_array($data) && array_key_exists('raw', $data)
                                            ? (string) $data['raw']
                                            : (string) ($report->content ?? '');
                                    @endphp
                                    {!! nl2br(e($scanOutput)) !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>




                </div>


                <div class="mt-4">
                    <form action="{{ route('reports.destroy', $report->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-secondary" value="Удалить" onclick="return confirm('Вы точно уверены?')">Удалить</button>
                    </form>
                </div>

            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
