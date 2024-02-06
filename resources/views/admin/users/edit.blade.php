@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Редактирование пользователя {{ $user->name }}</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('users.update', [app()->getLocale(), $user->id]) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Имя</label>
                        <input
                                type="text"
                                class="form-control form-control-user @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                placeholder="Имя пользователя"
                            value="{{ $user->name }}"
                        >
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                               id="email" name="email"
                               placeholder="Введите Email пользователя"
                                value="{{ $user->email }}"
                        >
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telegram_user_id">Telegram user ID</label>
                        <input type="number" class="form-control form-control-user @error('telegram_user_id') is-invalid @enderror"
                               id="telegram_user_id" name="telegram_user_id"
                               placeholder="Telegram user ID"
                               value="{{ $user->telegram_user_id }}"
                        >
                        @error('telegram_user_id')
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
