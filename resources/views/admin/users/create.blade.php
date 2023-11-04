@extends('admin.layout.main')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Добавление пользователя</h1>

        </div>

        <!-- Content Row -->
        <div class="row">


            <div class="col-6">


                <form action="{{ route('users.store') }}" method="post">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <input
                                type="text"
                                class="form-control form-control-user @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                placeholder="Имя пользователя"
                                value="{{ old('name') }}"
                        >
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                               id="email" name="email"
                               placeholder="Введите Email пользователя"
                               value="{{ old('email')  }}"
                        >
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Добавить">
                    </div>

                    <div class="form-group">
                        <p class="text-danger">Пароль будет выслан на почту</p>
                    </div>


                </form>


            </div>


        </div>


    </div>
    <!-- /.container-fluid -->
@endsection
