<?php
    /** @var \App\Models\Question $question */
    /** @var \App\Models\Answer $answer */

$btnClass = '"cursor-pointer ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"';
?>
<tr>
    <td>
        <div class="mt-10 mb-2">
            <a href="{{ route('dashboard.question.show',['question'=> $question]) }}" >{{ $question->title()->value }}</a>
        </div>
    </td>
    <td>
        {{ $question->getStatusText() }}
    </td>
    <td>
        @foreach ($question->categories as $category)
            {{ $category->title()->value }}
        @endforeach
    </td>
    <td>
        {{ Form::open(['url'=> route('dashboard.question.edit',['question'=> $question]), 'method' => 'get' ]) }}
        {{ Form::submit(trans('messages.edit'), ['class' => $btnClass]) }}
        {{ Form::close() }}

        {{ Form::open(['url'=> route('dashboard.question.destroy',['question'=> $question]), 'method' => 'delete' ]) }}
        {{ Form::submit(trans('messages.delete'), ['class' => $btnClass]) }}
        {{ Form::close() }}
    </td>
</tr>
<tr>
    <td colspan="4">
        <div class="pl-7">
            @foreach ($question->answers as $answer)
                @if ($answer->isRight() === false)
                    {{--@continue--}}
                @endif
                {!! $answer->text()->value !!}
            @endforeach
        </div>
    </td>
</tr>
