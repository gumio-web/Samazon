@extends('layouts.dashboard')

@section('content')
<div class="w-75">
    <h1>カテゴリ情報更新</h1>

    <form method="POST" action="{{route('dashboard.categories.update', compact('category'))}}">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label for="category-name">カテゴリ名</label>
            <input type="text" name="name" id="category-name" class="form-control" value="{{ $category->name }}">
        </div>
        <div class="form-group">
            <label for="category-description">カテゴリの説明</label>
            <textarea name="description" id="category-description"
                class="form-control">{{ $category->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="category-major-category">親カテゴリ名</label>
            <select name="major_category_id" class="form-control col-8" id="category-major-category">
                @foreach ($major_categories as $major_category)
                @if ($major_category->id == $category->major_category_id)
                <option value="{{ $major_category->id }}" selected>{{ $major_category->name }}</option>
                @else
                <option value="{{ $major_category->id }}">{{ $major_category->name }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-danger">更新</button>
    </form>

    <a href="{{route('dashboard.categories.index')}}" class="mt-4">カテゴリ一覧に戻る</a>
</div>
@endsection