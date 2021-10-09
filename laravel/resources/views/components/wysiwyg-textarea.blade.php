{{ Form::hidden($field, $value, ['id'=>$id]) }}
<div class="{{ $textareaClass }} visRedactor" data-name="{{ $editorName }}" data-selector="#{{ $id }}">
    {!! $value  !!}
</div>
