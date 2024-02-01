@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Редактирование задачи {{ $task->title }}</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('tasks.update', $task->id) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                placeholder="Введите название задачи"
                                value="{{ $task->title }}"
                        >
                        @error('title')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <select
                                class="form-control form-control-user @error('report_id') is-invalid @enderror"
                                id="report_id"
                                name="report_id"
                                aria-label="select example"
                        >
                            <option value="">Выберите отчет</option>
                            @foreach($reports as $report)
                                <option value="{{ $report->id }}" @selected(old('report_id') == $report->id || $task->report_id == $report->id)>#{{$report->id}} {{ $report->project->title }} {{$report->utility->title}}</option>
                            @endforeach
                        </select>
                        @error('report_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('cron_format') is-invalid @enderror"
                                id="cron_format"
                                name="cron_format"
                                placeholder="UNIX cron format"
                                value="{{ $task->cron_format }}"
                        >
                        @error('cron_format')
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
