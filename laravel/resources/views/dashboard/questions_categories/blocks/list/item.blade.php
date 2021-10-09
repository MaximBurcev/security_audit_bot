<tr>
    <td>
        @csrf
    </td>
    <td>
        <a href="{{ route('dashboard.category.show',['category'=> $category]) }}" >{{ $category->title()->value }}</a>
    </td>
</tr>

