@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-2">
        @component('components.sidebar', compact('categories', 'major_category_names'));
        @endcomponent
    </div>
    <div class="col-9">
        <div class="container">
            @if ($category !== null)
            <a href="/">トップ</a> > <a href="#">{{ $category->major_category_name }}</a> > {{ $category->name }}
            <h1>{{ $category->name }}の商品一覧{{$products->count()}}件</h1>
            <form method="GET" action="{{ route('products.index')}}" class="form-inline">
                <!-- クエリ情報 ?caregory=番号 を維持するためのフォーム -->
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                並び替え
                <select name="sort" onChange="this.form.submit();" class="form-inline ml-2">
                    @foreach ($sort as $key => $value)
                    @if ($sorted == $value)
                    <option value="{{$value}}" selected>{{$key}}</option>
                    @else
                    <option value="{{$value}}">{{$key}}</option>
                    @endif
                    @endforeach
                </select>
            </form>
            @endif
        </div>
        <div class="container mt-4">
            <div class="row w-100">
                @foreach($products as $product)
                <div class="col-3">
                    <a href="{{route('products.show', compact('product'))}}">
                        @if ($product->image !== "")
                        <img src="{{ asset('storage/products/'.$product->image) }}" class="img-thumbnail">
                        @else
                        <img src="{{ asset('img/dummy.jpg')}}" class="img-thumbnail">
                        @endif
                    </a>
                    <div class="row">
                        <div class="col-12">
                            <p class="samazon-product-label mt-2">
                                {{$product->name}}<br>
                                <label>￥{{$product->price}}</label>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @if ($category_id !== null)
        {{$products->appends(['category_id' => $category_id, 'sort' => $sorted])->links()}}
        @else
        {{$products->links()}}
        @endif
    </div>
</div>
@endsection