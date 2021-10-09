<?php
$btnClass = '"cursor-pointer ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"';
?>
<tr>
    <td>
        <a href="{{ route('dashboard.category.show',['category'=> $category]) }}" >{{ $category->title()->value }}</a>
    </td>
    <td>
        {{ Form::open(['url'=> route('dashboard.category.edit',['category'=> $category]), 'method' => 'get' ]) }}
        {{ Form::submit(trans('messages.edit'), ['class' => $btnClass]) }}
        {{ Form::close() }}

        {{ Form::open(['url'=> route('dashboard.category.destroy',['category'=> $category]), 'method' => 'delete' ]) }}
        {{ Form::submit(trans('messages.delete'), ['class' => $btnClass]) }}
        {{ Form::close() }}
    </td>
</tr>

