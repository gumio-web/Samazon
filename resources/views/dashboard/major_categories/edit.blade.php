@extends('layouts.dashboard')

@section('content')
<div class="w-75">
    <h1>カテゴリ情報更新</h1>

    <form method="POST" action="{{route('dashboard.major_categories.update', compact('major_category'))}}" class="mb-4">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label for="major-category-name">カテゴリ名</label>
            <input type="text" name="name" id="major-category-name" class="form-control"
                value="{{ $major_category->name }}">
        </div>
        <div class="form-group">
            <label for="major-category-description">カテゴリの説明</label>
            <textarea name="description" id="major-category-description"
                class="form-control">{{ $major_category->description }}</textarea>
        </div>
        <button type="submit" class="btn samazon-submit-button">更新</button>
    </form>

    <a href="{{route('dashboard.major_categories.index')}}">カテゴリ一覧に戻る</a>
</div>
@endsection