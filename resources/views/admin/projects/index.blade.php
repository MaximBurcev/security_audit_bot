@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Проекты</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-12">


                <div class="mb-4">
                    <a class="btn btn-primary" href="{{ route('projects.create') }}">Добавить</a>
                </div>


                <div class="card">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>URL</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td><a href="{{ route('projects.show', $project->id) }}"> {{ $project->title }}</a></td>
                                    <td>{{ $project->url }}</td>
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
