@extends('layouts.app')

@section('content')
<div class="container  d-flex justify-content-center mt-3">
    <div class="w-75">
        <h1>お気に入り</h1>

        <hr>

        <div class="row">
            @foreach ($favorites as $fav)
            <div class="col-md-8 mt-2">
                <div class="d-inline-flex">
                    <!-- $favはProductインスタンスの情報が入ったFavoriteクラスのインスタンスだけどshowにアクセスできる -->
                    <!-- しかし実際にはここへアクセスするとfavoritesテーブルの主キーidを持つインスタンスのページへ飛ぶ -->
                    <!-- 正確にはApp\Product::find($fav->favoriteable_id)がProductモデルのインスタンス -->
                    <a href="{{route('products.show', App\Product::find($fav->favoriteable_id))}}" class="w-25">
                        <img src="{{ asset('img/dummy.jpg')}}" class="img-fluid w-100">
                    </a>
                    <div class="container mt-3">
                        <h5 class="w-100 samazon-favorite-item-text">{{App\Product::find($fav->favoriteable_id)->name}}
                        </h5>
                        <h6 class="w-100 samazon-favorite-item-text">￥{{App\Product::find($fav->favoriteable_id)->price}}
                        </h6>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <a href="{{route('products.favorite', App\Product::find($fav->favoriteable_id))}}" class="samazon-favorite-item-delete">
                    削除
                </a>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-end">
                <button type="submit" class="btn samazon-favorite-add-cart text-white w-100">カートに入れる</button>
            </div>
            @endforeach
        </div>

        <hr>
    </div>
</div>
@endsection