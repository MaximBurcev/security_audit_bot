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

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body>
    <div class="container mx-auto">
        <div class="relative bg-white overflow-hidden">
            <div class="max-w-8xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-3xl lg:w-full lg:pb-28 xl:pb-32">
                    <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2"
                         fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <polygon points="50,0 100,0 50,100 0,100"/>
                    </svg>

                    <div class="relative pt-6 px-4 sm:px-6 lg:px-8">
                        <nav class="relative flex items-center justify-between sm:h-10 lg:justify-start"
                             aria-label="Global">
                            <div class="flex items-center flex-grow flex-shrink-0 lg:flex-grow-0">
                                <div class="flex items-center justify-between w-full md:w-auto">
                                    <a href="#">
                                        <span class="sr-only">Workflow</span>
                                        <img class="h-8 w-auto sm:h-10"
                                             src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg">
                                    </a>
                                    <div class="-mr-2 flex items-center md:hidden">
                                        <button type="button"
                                                class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                                id="main-menu" aria-haspopup="true">
                                            <span class="sr-only">Open main menu</span>
                                            <!-- Heroicon name: menu -->
                                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 6h16M4 12h16M4 18h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:block md:ml-10 md:pr-4 md:space-x-8">
                                <a href="#" class="font-medium text-gray-500 hover:text-gray-900">@lang('menu.homepage')</a>

                                <a href="#" class="font-medium text-gray-500 hover:text-gray-900">@lang('menu.about')</a>

                                <a href="#" class="font-medium text-gray-500 hover:text-gray-900">@lang('menu.pricing')</a>

                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">@lang('menu.logout')</a>

                                <x-locale-select  />
                            </div>
                        </nav>
                    </div>

                    <!--
                      Mobile menu, show/hide based on menu open state.

                      Entering: "duration-150 ease-out"
                        From: "opacity-0 scale-95"
                        To: "opacity-100 scale-100"
                      Leaving: "duration-100 ease-in"
                        From: "opacity-100 scale-100"
                        To: "opacity-0 scale-95"
                    -->
                    <div class="absolute top-0 inset-x-0 p-2 transition transform origin-top-right md:hidden">
                        <div class="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden">
                            <div class="px-5 pt-4 flex items-center justify-between">
                                <div>
                                    <img class="h-8 w-auto"
                                         src="https://tailwindui.com/img/logos/workflow-mark-indigo-600.svg" alt="">
                                </div>
                                <div class="-mr-2">
                                    <button type="button"
                                            class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                        <span class="sr-only">Close main menu</span>
                                        <!-- Heroicon name: x -->
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div role="menu" aria-orientation="vertical" aria-labelledby="main-menu">
                                <div class="px-2 pt-2 pb-3 space-y-1" role="none">
                                    <a href="#"
                                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                       role="menuitem">Product</a>

                                    <a href="#"
                                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                       role="menuitem">Features</a>

                                    <a href="#"
                                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                       role="menuitem">Marketplace</a>

                                    <a href="#"
                                       class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50"
                                       role="menuitem">Company</a>
                                </div>
                                <div role="none">
                                    <a href="#"
                                       class="block w-full px-5 py-3 text-center font-medium text-indigo-600 bg-gray-50 hover:bg-gray-100"
                                       role="menuitem">
                                        Log in
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                <span class="block xl:inline">Code</span>
                                <span class="block text-indigo-600 xl:inline">Boost</span>
                            </h1>
                            <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Check out your coding skills! Improve your style, blablabla. Circumstances. Dog. Cat. Fabric, Smart Developer.
                                Future comes today with us.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                                <div class="rounded-md shadow">
                                    <a href="#"
                                       class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                        Get started
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#"
                                       class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 md:py-4 md:text-lg md:px-10">
                                        Live demo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
                <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full"
                     src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80"
                     alt="">
            </div>
        </div>

        <div class="mt-3  sm:mt-5 sm:text-lg md:text-xl">
            {{ $slot }}
        </div>
    </div>

    <footer class="relative bg-gray-300 mt-48 pt-8 pb-6">
        <div class="bottom-auto top-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden -mt-20"
             style="height:80px;transform:translateZ(0)">
            <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"
                 version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                <polygon class="text-gray-300 fill-current" points="2560 0 2560 100 0 100"></polygon>
            </svg>
        </div>
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap">
                <div class="w-full md:w-6/12 px-4"><h4 class="text-3xl font-semibold">Let's keep in touch!</h4><h5
                        class="text-lg mt-0 mb-2 text-gray-700">Find us on any of these platforms, we respond 1-2 business
                        days.</h5>
                    <div class="mt-6"><a href="https://www.twitter.com/creativetim" target="_blank"><i
                                class="fab fa-twitter  bg-white text-blue-400 shadow-lg font-lg p-3 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2 inline-block text-center"></i></a><a
                            href="https://www.facebook.com/creativetim" target="_blank"><i
                                class="fab fa-facebook-square bg-white text-blue-600 shadow-lg font-lg p-3 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2 inline-block text-center"></i></a><a
                            href="https://www.dribbble.com/creativetim" target="_blank"><i
                                class="fab fa-dribbble bg-white text-pink-400 shadow-lg font-lg p-3 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2 inline-block text-center"></i></a><a
                            href="https://www.github.com/creativetimofficial" target="_blank"><i
                                class="fab fa-github bg-white text-gray-900 shadow-lg font-lg p-3 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2 inline-block text-center"></i></a>
                    </div>
                    <p class="text-sm mt-6 text-gray-600 font-semibold">Currently v1.0.0. Code<a
                            href="https://github.com/creativetimofficial/tailwind-starter-kit" class="text-gray-700"
                            target="_blank"> <!-- -->licensed MIT</a>, docs<a
                            href="https://creativecommons.org/licenses/by/4.0/" targe="_blank" class="text-gray-700">
                            <!-- -->CC BY 4.0</a>.</p></div>
                <div class="w-full md:w-6/12 px-4">
                    <div class="flex flex-wrap items-top mb-6">
                        <div class="w-full md:w-6/12 xl:w-4/12 pt-6 md:pt-0 md:px-4 ml-auto"><span
                                class="block uppercase text-gray-600 text-sm font-semibold mb-2">Useful Links</span>
                            <ul class="list-unstyled">
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="#" target="_blank">@lang('menu.about')</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="#" target="_blank">@lang('menu.submit_question')</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="#"
                                       target="_blank">@lang('menu.training')</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="#" target="_blank">Free
                                        Products</a></li>
                            </ul>
                        </div>
                        <div class="w-full md:w-6/12 xl:w-4/12 pt-6 md:pt-0 md:px-4 ml-auto"><span
                                class="block uppercase text-gray-600 text-sm font-semibold mb-2">Other Resources</span>
                            <ul class="list-unstyled">
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="https://github.com/creativetimofficial/tailwind-starter-kit/blob/master/LICENSE.md"
                                       target="_blank">MIT License</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="https://creative-tim.com/terms" target="_blank">Terms &amp; Conditions</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="https://creative-tim.com/privacy" target="_blank">Privacy Policy</a></li>
                                <li><a class="text-gray-700 hover:text-gray-900 font-semibold block pb-2 text-sm"
                                       href="https://creative-tim.com/contact-us" target="_blank">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-gray-400">
            <div class="flex flex-wrap items-center md:justify-between justify-center">
                <div class="w-full md:w-4/12 px-4 mx-auto text-center">
                    <div class="text-sm text-gray-600 font-semibold py-1">Copyright © <!-- -->2021<!-- --> Tailwind Starter
                        Kit by<!-- --> <a href="https://www.creative-tim.com" class="text-gray-600 hover:text-gray-900"
                                          target="_blank">Creative Tim</a>.
                    </div>
                </div>
            </div>
        </div>
    </footer>
    </body>
</html>
