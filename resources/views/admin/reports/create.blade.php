@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Создание нового отчета</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('reports.store') }}" method="post">

                    @csrf


                    <div class="form-group">
                        <select class="form-control form-control-user @error('status') is-invalid @enderror"
                               id="status"
                                name="status"
                                @readonly(true)
                        >
                            @foreach($statuses as $status)
                                {{ var_dump($status) }}
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select class="form-control form-control-user @error('utility_id') is-invalid @enderror"
                                id="utility_id" name="utility_id"
                        >
                            <option value="" selected>Выберите утилиту</option>
                            @foreach($utilities as $utility)
                                <option value="{{ $utility->id }}" @selected(old('utility_id') == $utility->id)>{{ $utility->title }}</option>
                            @endforeach
                        </select>
                        @error('utility_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select
                                class="form-control form-control-user @error('project_id') is-invalid @enderror"
                                id="project_id"
                                name="project_id"
                        >
                            <option value="" selected>Выберите проект</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->title }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Добавить">
                    </div>

                </form>


            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
