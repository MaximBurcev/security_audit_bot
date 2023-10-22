@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Аудит {{ $audit->title }}</h1>

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
                                <td>{{ $audit->id }}</td>
                            </tr>
                            <tr>
                                <td>Название</td>
                                <td>{{ $audit->title }}</td>
                            </tr>
                            <tr>
                                <td>Пользователь</td>
                                <td>
                                    {{ $audit->user->name }}
                                </td>
                            </tr>
                            <tr>
                                <td>Отчеты</td>
                                <td>
                                    @foreach($audit->reports as $report)
                                        <a href="{{ route('reports.show', $report->id) }}">{{ $report->id }}</a>
                                    @endforeach
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>


                </div>

                <div class="mt-4">
                    <a class="btn btn-primary" href="{{ route('audits.edit', $audit->id) }}">Редактировать</a>
                </div>

                <div class="mt-4">
                    <form action="{{ route('audits.destroy', $audit->id) }}" method="POST">
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
