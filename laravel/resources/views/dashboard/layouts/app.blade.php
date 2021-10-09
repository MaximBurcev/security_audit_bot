<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles

        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/styles/atelier-heath-light.min.css">
        @stack('styles')

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/highlight.min.js"></script>
        @stack('scripts')
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')



            <!-- Page Content -->
            <main>
                <div class="flex space-x-4">
                    <div class="w-1/5 bg-white shadow h-full p-10">
                        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                            <div class="sidebar-sticky pt-3">
                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                    <span>{{ trans('messages.questions') }}</span>
                                    <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                                        <span data-feather="plus-circle"></span>
                                    </a>
                                </h6>
                                <ul class="nav flex-column mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.question.index',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.questions_list') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.question.create',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.questions_create') }}
                                        </a>
                                    </li>
                                </ul>

                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                    <span>{{ trans('messages.questions_categories') }}</span>
                                    <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                                        <span data-feather="plus-circle"></span>
                                    </a>
                                </h6>
                                <ul class="nav flex-column mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.category.index',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.questions_categories_list') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.category.create',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.questions_categories_create') }}
                                        </a>
                                    </li>
                                </ul>


                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                    <span>{{ trans('messages.users') }}</span>
                                    <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                                        <span data-feather="plus-circle"></span>
                                    </a>
                                </h6>
                                <ul class="nav flex-column mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.user.index',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.users_list') }}
                                        </a>
                                    </li>
                                </ul>

                                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                                    <span>{{ trans('messages.roles') }}</span>
                                    <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                                        <span data-feather="plus-circle"></span>
                                    </a>
                                </h6>
                                <ul class="nav flex-column mb-2">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.role.index',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.roles_list') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('dashboard.role.create',['locale' => $locale]) }}">
                                            <span data-feather="file-text"></span>
                                            {{ trans('messages.roles_create') }}
                                        </a>
                                    </li>
                                </ul>

                            </div>
                        </nav>
                    </div>
                    <div class="w-4/5">

                        <!-- Page Heading -->
                        <header class="bg-white shadow">
                            <div class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                    {{ $header }}
                                </h2>
                            </div>
                        </header>

                        <div class="">
                            <div class="bg-white shadow p-10">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
