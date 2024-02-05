@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Отчеты пользователя</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Проект</th>
                                <th>Утилита</th>
                                <th>Статус</th>
                                <th>Ссылка</th>
                                <th>Дата создания</th>
                                <th>Дата обновления</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        <a href="{{ route('projects.show', [$report->project_id]) }}"> {{ $report->project->title }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('utilities.show', [$report->utility_id]) }}"> {{ $report->utility->title }}</a>
                                    </td>
                                    <td>{{$report->status}}</td>
                                    <td><a href="{{ URL::signedRoute('public-report',
                    ['report' => $report->id]) }}">Посмотреть
                                            отчет</a></td>
                                    <td>{{$report->created_at}}</td>
                                    <td>{{$report->updated_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
