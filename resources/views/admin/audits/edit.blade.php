@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Редактирование аудита {{ $audit->title }}</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('audits.update', $audit->id) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                placeholder="Введите название аудита"
                                value="{{ $audit->title }}"
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
                                <option value="{{ $user->id }}" @selected($audit->user->id == $user->id)>{{ $user->name }}</option>
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
                                <option {{ is_array($audit->reports->pluck('id')->toArray()) && in_array($report->id, $audit->reports->pluck('id')->toArray())? ' selected':'' }} value="{{ $report->id }}">{{ $report->id }}</option>
                            @endforeach
                        </select>
                        @error('report_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Обновить">
                    </div>

                </form>


            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
