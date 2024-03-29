@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Регулярные задачи</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="mb-4">
                    <a class="btn btn-primary" href="{{ route('tasks.create') }}">Добавить</a>
                </div>


                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>ID Отчета</th>
                                <th>Cron</th>
                                <th>Дата создания</th>
                                <th>Дата изменения</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td>{{ $task->id }}</td>
                                    <td><a href="{{ route('tasks.show', $task->id) }}"> {{ $task->title }}</a></td>
                                    <td><a href="{{ route('reports.show', $task->report_id) }}">{{ $task->report_id }}</a> </td>
                                    <td>{{ $task->cron_format }}</td>
                                    <td>{{ $task->created_at }}</td>
                                    <td>{{ $task->updated_at }}</td>
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
