@extends('layouts.app')

@section('content')

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
            <div class="question"></div>
        </div>
    </div>



    {{-- Some comments for task after answer submit --}}
    <div class="comment_area"></div>

    <div class="row">
        <div class="col">
            {{-- Answer variants will render here --}}
            <div class="answers_options_area"></div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <textarea class="answer_area"></textarea>
        </div>
    </div>

    <div class="row answer_buttons">
        <div class="col-3"></div>
        <div class="col-2">
            <button class="btn btn-warning iNotRemember">@lang('messages.doNotRemember')</button><br/>
            <span class="tinyNote">Ctrl + <-</span>
        </div>
        <div class="col-2">
            <div class="iknowArea">
                <button class="btn btn-primary iKnowBtn">@lang('messages.checkIt')</button><br/>
                <span class="tinyNote">Ctrl + Enter</span>
            </div>
        </div>
        <div class="col-2">
            <div class="iRememberArea">
                <button class="btn btn-primary iRemember">@lang('messages.iRemember')</button><br/>
                <span class="tinyNote">Ctrl + -></span>
            </div>
        </div>
        <div class="col-3"></div>
    </div>



@endsection
