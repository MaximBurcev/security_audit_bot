<tr>
    <td>
        @csrf
    </td>
    <td>
        <a href="{{ route('dashboard.question.show',['question'=> $question]) }}" >{{ $question->title()->value }}</a>
    </td>
</tr>

