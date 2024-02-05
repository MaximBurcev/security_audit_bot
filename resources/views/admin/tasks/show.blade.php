@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Задача: {{ $task->title }}</h1>

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
                                <td>{{ $task->id }}</td>
                            </tr>
                            <tr>
                                <td>Название</td>
                                <td>{{ $task->title }}</td>
                            </tr>
                            <tr>
                                <td>ID Отчета</td>
                                <td>
                                    {{ $task->report_id }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                </div>

                <div class="mt-4">
                    <a class="btn btn-primary" href="{{ route('tasks.edit', $task->id) }}">Редактировать</a>
                </div>

                <div class="mt-4">
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">
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
