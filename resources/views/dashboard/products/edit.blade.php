@extends('layouts.dashboard')

@section('content')
<div class="w-50">
    <h1>商品登録</h1>

    <hr>

    <form method="POST" action="{{route('dashboard.products.update', compact('product'))}}" class="mb-5"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-name" class="col-2 d-flex justify-content-start">商品名</label>
            <input type="text" name="name" id="product-name" class="form-control col-8" value="{{ $product->name }}">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-price" class="col-2 d-flex justify-content-start">価格</label>
            <input type="number" name="price" id="product-price" class="form-control col-8"
                value="{{ $product->price }}">
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-category" class="col-2 d-flex justify-content-start">カテゴリ</label>
            <select name="category_id" class="form-control col-8" id="product-category">
                @foreach ($categories as $category)
                @if ($category->id == $product->category_id)
                <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                @else
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label class="col-2 d-flex justify-content-start">画像</label>
            @if ($product->image !== null)
            <img src="{{ asset('storage/products/'.$product->image) }}" id="product-image-preview"
                class="img-fluid w-25">
            @else
            <img src="#" id="product-image-preview">
            @endif
            <div class="d-flex flex-column ml-2">
                <small class="mb-3">600px×600px推奨。<br>商品の魅力が伝わる画像をアップロードして下さい。</small>
                <label for="product-image" class="btn samazon-submit-button">画像を選択</label>
                <!--
                    https://developer.mozilla.org/ja/docs/Web/API/FileList
                    全てのinput要素はFileList型のfiles属性をもち、このリスト内の項目へのアクセスを可能にする。
                    <input id="fileItem" type="file">
                    var file = document.getElementById('fileItem').files[0]
                -->
                <input type="file" name="image" id="product-image" onChange="handleImage(this.files)"
                    style="display: none;">
            </div>
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-price" class="col-2 d-flex justify-content-start">オススメ?</label>
            @if ($product->recommend_flag)
            <input type="checkbox" name="recommend" id="product-recommend" class="samazon-check-box" checked>
            @else
            <input type="checkbox" name="recommend" id="product-recommend" class="samazon-check-box">
            @endif
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-carriage" class="col-2 d-flex justify-content-start">送料</label>
            @if ($product->carriage_flag)
            <input type="checkbox" name="carriage" id="product-carriage" class="samazon-check-box" checked>
            @else
            <input type="checkbox" name="carriage" id="product-carriage" class="samazon-check-box">
            @endif
        </div>
        <div class="form-inline mt-4 mb-4 row">
            <label for="product-description" class="col-2 d-flex justify-content-start align-self-start">商品説明</label>
            <textarea name="description" id="product-description" class="form-control col-8"
                rows="10">{{ $product->description }}</textarea>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" class="w-25 btn samazon-submit-button">更新</button>
        </div>
    </form>

    <div class="d-flex justify-content-end">
        <a href="{{route('dashboard.products.index')}}">商品一覧に戻る</a>
    </div>
</div>
<script type="text/javascript">
    // FileReader.onload https://lab.syncer.jp/Web/API_Interface/Reference/IDL/FileReader/onload/
    function handleImage(image) {
            // インスタンスの生成
            let reader = new FileReader();
            // データの読み込みが正常に完了した時にloadイベントが発生し、ここに設定したコールバック関数が呼び出される。 = 読み込み完了時のイベント
            reader.onload = function(event) {
                console.log(event.target);   // 同じ
                console.log(reader);   // 同じ
                let imagePreview = document.getElementById("product-image-preview");
                // 読み取り成功時にFileReaderオブジェクトのresultプロパティにデータの中身が入るので、それをsrc属性に代入する。
                imagePreview.src = reader.result;
            }
            console.log(image);
            // 読み込みを実行
            reader.readAsDataURL(image[0]);
        }
</script>
@endsection