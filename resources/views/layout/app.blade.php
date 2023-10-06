<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Аудит безопасности проекта</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon.ico') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="page-top">


@yield('navigation')
@yield('header')
@yield('content')

<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container px-4"><p class="m-0 text-center text-white">Copyright &copy; Аудит безопасности проекта {{ date('Y') }}</p></div>
</footer>

</body>
</html>
