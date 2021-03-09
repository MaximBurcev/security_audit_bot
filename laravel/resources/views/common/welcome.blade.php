<?php
    /** @var \App\Models\QuestionCategory[] $questionCategories */
?>
<x-guest-layout>
    <x-slot name="slot">

        <div class="container pt-24 md:pt-48 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center">
            <!--Left Col-->
            <div class="flex flex-col w-full xl:w-2/5 justify-center lg:items-start overflow-y-hidden">
                <h1 class="my-4 text-3xl md:text-5xl text-purple-800 font-bold leading-tight text-center md:text-left slide-in-bottom-h1">Подготовься к собесу по PHP</h1>
                <p class="leading-normal text-base md:text-2xl mb-8 text-center md:text-left slide-in-bottom-subtitle">Полный набор теории, которую спрашивают
                    <br/>+ Немного практики</p>
                {{--<p class="leading-normal text-base md:text-2xl mb-8 text-center md:text-left slide-in-bottom-subtitle">
                    <a href="{{ route('quiz', ['locale' => $locale]) }}">Поехали?</a>
                </p>--}}

                <p class="text-blue-400 font-bold pb-8 lg:pb-6 text-center md:text-left fade-in">Полная версия доступна в приложении:</p>
                <div class="flex w-full justify-center md:justify-start pb-24 lg:pb-0 fade-in">
                    <img src="/images/App Store.svg" class="h-12 pr-4 bounce-top-icons">
                    <img src="/images/Play Store.svg" class="h-12 bounce-top-icons">
                </div>

            </div>

            <!--Right Col-->
            <div class="w-full xl:w-3/5 py-6 overflow-y-hidden">
                <img class="w-5/6 mx-auto lg:mr-0 slide-in-bottom" src="/images/devices.svg">
            </div>
        </div>


    </x-slot>
</x-guest-layout>
