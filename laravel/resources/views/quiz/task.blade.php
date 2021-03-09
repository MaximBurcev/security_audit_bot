<x-guest-layout>
    <x-slot name="header">
        Тема "{{ $theme }}"
    </x-slot>

    <style>
        button, .gradient2 {
            background-color: rgb(129, 140, 248);
            background-image: linear-gradient(
                315deg
                , rgb(129, 140, 248) 0%, rgb(91, 33, 182) 74%);
        }
    </style>
    <div class="container mx-auto  pt-24 px-8 items-center">
        <div class="block">
            <p class="leading-normal text-base mb-8 text-center md:text-center slide-in-bottom-subtitle">
                Заколебавшись гуглить, ты, наконец, натыкаешься на это тестирование.
            </p>
            <h1 class="my-4 text-3xl md:text-5xl text-purple-800 font-bold leading-tight text-center slide-in-bottom-h1">Тема "{{ $theme }}"</h1>
        </div>


        <div class="block">
            <div class="row">
                <div class="col">
                    <div id="progressbar"></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    {{-- Task instructions will render here --}}
                    <div id="instruction" class="text-center"></div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    {{-- Task question will render here--}}
                    <div class="question leading-normal text-base md:text-2xl pt-14 pb-24 md:pt-48 px-8 text-center">
                        {!! $quiz->questions[0]->title()->value  !!}
                    </div>
                </div>
            </div>

            {{-- Some comments for task after answer submit --}}
            <div class="comment_area hidden text-center font-extrabold">{!! $quiz->questions[0]->answers[0]->text()->value  !!}</div>

            {{-- Answer variants will render here --}}
            <div class="row">
                <div class="col">
                    <div class="answers_options_area"></div>
                </div>
            </div>

            <div class="row hidden">
                <div class="col">
                    <textarea class="answer_area"></textarea>
                </div>
            </div>

            <div class="hidden grid gap-4 grid-cols-1 next_question_buttons">
                <div class="mx-auto">
                    <button class="iNotRemember mx-auto lg:mx-0 hover:underline text-white-800 font-extrabold rounded my-2 md:my-6 py-4 px-8 shadow-lg w-48">
                        Следующий вопрос
                    </button><br/>
                    <span class="tinyNote">Ctrl + -></span>
                </div>
            </div>

            <div class="grid gap-4 grid-cols-2 answer_buttons">
                <div class="mx-auto">
                    <button class="iNotRemember mx-auto lg:mx-0 hover:underline text-white-800 font-extrabold rounded my-2 md:my-6 py-4 px-8 shadow-lg w-48">
                        @lang('messages.do_not_remember')
                    </button><br/>
                    <span class="tinyNote">Ctrl + <-</span>
                </div>
                {{--<div class="col-2 hidden">
                    <div class="iknowArea">
                        <button class="btn btn-primary iKnowBtn">@lang('messages.check_it')</button><br/>
                        <span class="tinyNote">Ctrl + Enter</span>
                    </div>
                </div>--}}
                <div class="mx-auto">
                    <div class="iRememberArea">
                        <button class="iNotRemember mx-auto lg:mx-0 hover:underline text-white-800 font-extrabold rounded my-2 md:my-6 py-4 px-8 shadow-lg w-48">
                            @lang('messages.i_remember')
                        </button><br/>
                        <span class="tinyNote">Ctrl + -></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="questionsRRR hidden">

        @foreach($quiz->questions as $key => $question)
        <div class="q{{ $key }}">
            {!! $question->title()->value  !!}
        </div>
        @endforeach

        @foreach($quiz->questions as $key => $question)
            <div class="a{{ $key }}">
                {!! $question->answers[0]->text()->value  !!}
            </div>
        @endforeach

    </div>

</x-guest-layout>
