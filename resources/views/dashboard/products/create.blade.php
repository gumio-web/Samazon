@extends('layouts.dashboard')

@section('content')
<div class="w-50">
    <h1>商品登録</h1>

    <hr>

    <form method="POST" action="{{route('dashboard.products.store')}}" class="mb-5" enctype="multipart/form-data">
        @csrf
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-name" class="col-2 d-flex justify-content-start">商品名</label>
            <input type="text" name="name" id="product-name" class="form-control col-8">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-price" class="col-2 d-flex justify-content-start">価格</label>
            <input type="number" name="price" id="product-price" class="form-control col-8">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-category" class="col-2 d-flex justify-content-start">カテゴリ</label>
            <select name="category_id" class="form-control col-8" id="product-category">
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-image" class="col-2 d-flex justify-content-start">画像</label>
            <img src="#" id="product-image-preview" class="img-fluid w-25">
            <div class="d-flex flex-column ml-2">
                <small class="mb-3">600px×600px推奨。<br>商品の魅力が伝わる画像をアップロードして下さい。</small>
                <label for="product-image" class="btn samazon-submit-button">画像を選択</label>
                <input type="file" name="image" id="product-image" style="display: none;">
            </div>
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-price" class="col-2 d-flex justify-content-start">オススメ?</label>
            <input type="checkbox" name="recommend" id="product-recommend" class="samazon-check-box">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-carriage" class="col-2 d-flex justify-content-start">送料</label>
            <input type="checkbox" name="carriage" id="product-carriage" class="samazon-check-box">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-description" class="col-2 d-flex justify-content-start align-self-start">商品説明</label>
            <textarea name="description" id="product-description" class="form-control col-8" rows="10"></textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="w-25 btn samazon-submit-button">商品を登録</button>
        </div>
    </form>

    <div class="d-flex justify-content-end">
        <a href="{{route('dashboard.products.index')}}">商品一覧に戻る</a>
    </div>
</div>
<script type="text/javascript">
    $("#product-image").change(function() {
            if (this.files && this.files[0]) {
                //console.log(this.files);   FileList
                //console.log(this.files[0]);   File
                let reader = new FileReader();
                reader.onload = function(event) {
                    console.log(event.target);   // 同じ
                    console.log(reader);   // 同じ
                    $("#product-image-preview").attr("src", event.target.result);
                }
                // 読み込みを実行
                reader.readAsDataURL(this.files[0]);
            }
        });
</script>
@endsection