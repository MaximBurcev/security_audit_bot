@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Аудиты</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="mb-4">
                    <a class="btn btn-primary" href="{{ route('audits.create') }}">Добавить</a>
                </div>


                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Пользователь</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($audits as $audit)
                                <tr>
                                    <td><a href="{{ route('audits.show', $audit->id) }}">{{ $audit->id }}</a></td>
                                    <td>
                                        <a href="{{ route('audits.show', $audit->id) }}">{{ $audit->title }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', [app()->getLocale(), $audit->user->id]) }}">  {{ $audit->user->name }}</a>
                                    </td>
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
