@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Пользователи</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="mb-4">
                    <a class="btn btn-primary" href="{{ route('users.create', [app()->getLocale()]) }}">Добавить</a>
                </div>


                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Логин</th>
                                <th>Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td><a href="{{ route('users.show', [app()->getLocale(), $user->id]) }}"> {{ $user->name }}</a></td>
                                    <td>{{ $user->email }}</td>
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
