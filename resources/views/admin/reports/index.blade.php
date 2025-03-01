@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Отчеты</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="mb-4">
                    <a class="btn btn-primary" href="{{ route('reports.create') }}">Добавить</a>
                </div>

                <div class="mb-4">
                    @if(Session::has('report.store'))
                        <p class="alert alert-info">{{ Session::get('report.store') }}</p>
                    @endif
                </div>

                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Статус</th>
                                <th>Утилита</th>
                                <th>Проект</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td><a href="{{ route('reports.show', $report->id) }}">{{ $report->id }}</a></td>
                                    <td> {{ $report->status }}</td>
                                    <td>
                                        @if($report->utility)
                                            <a href="{{ route('utilities.show', $report->utility->id) }}">{{ $report->utility->title }}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($report->project)
                                            <a href="{{ route('projects.show', $report->project->id) }}">
                                                @endif
                                                {{ $report->project?->title }}
                                                @if($report->project)
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
