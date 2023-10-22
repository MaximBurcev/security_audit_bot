@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Создание нового аудита</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('audits.store') }}" method="post">

                    @csrf

                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                placeholder="Введите название аудита"
                                value="{{ old('title') }}"
                        >
                        @error('title')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select class="form-control form-control-user @error('status') is-invalid @enderror"
                               id="user_id"
                                name="user_id"
                        >
                            <option value="" selected>Выберите пользователя</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>




                    <div class="form-group">
                        <select
                                class="form-control form-control-user @error('report_id') is-invalid @enderror"
                                id="report_id"
                                name="report_id[]"
                                multiple
                                aria-label="multiple select example"
                        >

                            @foreach($reports as $report)
                                <option {{ is_array(old('report_id')) && in_array($report->id, old('report_id'))? ' selected':'' }} value="{{ $report->id }}">{{ $report->id }}</option>
                            @endforeach
                        </select>
                        @error('report_id')
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
