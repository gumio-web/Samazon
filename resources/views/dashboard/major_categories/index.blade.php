@extends('layouts.dashboard')

@section('content')
<div class="w-75">
    <form method="POST" action="{{route('dashboard.major_categories.store')}}">
        @csrf
        <div class="form-group">
            <label for="major-category-name">親カテゴリ名</label>
            <input type="text" name="name" id="major-category-name" class="form-control">
        </div>
        <div class="form-group">
            <label for="major-category-description">親カテゴリの説明</label>
            <textarea name="description" id="major-category-description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn samazon-submit-button">＋新規作成</button>
    </form>
    <div class="table-responsive">
        <table class="table mt-5">
            <thead>
                <tr>
                    <th scope="col" class="w-25">ID</th>
                    <th scope="col">親カテゴリ</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($major_categories as $major_category)
                <tr>
                    <th scope="row">{{ $major_category->id }}</td>
                    <td>{{ $major_category->name }}</td>
                    <td>
                        <a href="{{route('dashboard.major_categories.edit', compact('major_category'))}}"
                            class="dashboard-edit-link">編集</a>
                    </td>
                    <td>
                        <a href="/dashboard/major_categories/{{ $major_category->id }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form{{$major_category->id}}').submit();"
                            class="dashboard-delete-link">
                            削除
                        </a>

                        <form id="logout-form{{$major_category->id}}" action="{{route('dashboard.major_categories.destroy', compact('major_category'))}}"
                            method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $major_categories->links() }}
</div>
@endsection