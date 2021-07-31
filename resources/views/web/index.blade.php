@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-2">
        @component('components.sidebar', compact('categories', 'major_category_names'))
        @endcomponent
    </div>
    <div class="col-9">
        <h1>おすすめ商品</h1>
        <div class="row">
            @foreach ($recommended_products as $recommended_product)
            <div class="col-4">
                <a href="/products/{{ $recommended_product->id }}">
                    @if ($recommended_product->image !== "")
                    <img src="{{ asset('storage/products/'.$recommended_product->image) }}" class="img-thumbnail">
                    @else
                    <img src="{{ asset('img/dummy.jpg')}}" class="img-thumbnail">
                    @endif
                </a>
                <div class="row">
                    <div class="col-12">
                        <p class="samazon-product-label mt-2">
                            {{ $recommended_product->name }}<br>
                            <label>￥{{ $recommended_product->price }}</label>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <h1>新着商品</h1>
        <div class="row">
            @foreach ($recent_products as $recent_product)
            <div class="col-3">
                <a href="{{route('products.show', $recent_product)}}">
                    @if ($recent_product->image !== "")
                    <img src="{{ asset('storage/products/'.$recent_product->image) }}" class="img-thumbnail">
                    @else
                    <img src="{{ asset('img/dummy.jpg')}}" class="img-thumbnail">
                    @endif
                </a>
                <div class="row">
                    <div class="col-12">
                        <p class="samazon-product-label mt-2">
                            {{$recent_product->name}}
                            <br>
                            <label>￥{{$recent_product->price}}</label>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection