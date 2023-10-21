@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Создание нового проекта</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('projects.store') }}" method="post">
                    @csrf

                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                placeholder="Введите название проекта"
                            value="{{ old('title') }}"
                        >
                        @error('title')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="url" class="form-control form-control-user @error('url') is-invalid @enderror"
                               id="url" name="url"
                               placeholder="Введите URL проекта"
                            value="{{ old('url') }}"
                        >
                        @error('url')
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
