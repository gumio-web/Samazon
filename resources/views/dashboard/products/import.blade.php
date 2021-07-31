@extends('layouts.dashboard')

@section('content')
@if (session('flash_message'))
<div class="flash-success-bg w-25 text-center">
    <span class="flash-success-font">
        ✔ {{ session('flash_message') }}
    </span>
</div>
@endif

<div class="w-75">
    <h1>商品CSV登録</h1>
    <form method="POST" action="{{route('dashboard.products.import_csv')}}" class="form-inline"
        enctype="multipart/form-data">
        @csrf
        <div class="d-flex flex-column">
            <div class="d-flex flex-row">
                <label for="product-import-csv" class="btn samazon-button mr-3">CSVファイルを選択</label>
                <input type="file" name="csv" id="product-import-csv" style="display: none;"
                    onChange="handleCSV(this.files)">
                <!-- buttonのtype属性はデフォルトでsubmitになっている -->
                <button class="btn samazon-submit-button">一括登録</button>
            </div>
            <small id="product-import-csv-filename"></small>
        </div>
    </form>

    <div class="d-flex justify-content-between mt-3">
        <h4 class="d-flex align-self-center mt-1 mb-0">CSVファイルフォーマット</h4>
        @if($csv !== null)
        <a class="btn samazon-button" href="{{ asset('storage/csv/'.$csv) }}">雛形ファイルダウンロード</a>
        @else
        <a class="btn samazon-button" href="#">雛形ファイルダウンロード</a>
        @endif
    </div>

    <hr>

    <div class="row">
        <label class="col-3">商品ID</label>
        <span class="col-9">新規登録の場合は空にしてください。既存の商品を更新する場合は、商品IDを指定してください。</span>

        <label class="col-3">商品名</label>
        <span class="col-9"></span>

        <label class="col-3">商品説明</label>
        <span class="col-9"></span>

        <label class="col-3">価格</label>
        <span class="col-9"></span>

        <label class="col-3">カテゴリID</label>
        <span class="col-9"></span>

        <label class="col-3">オススメ商品フラグ</label>
        <span class="col-9"></span>

        <label class="col-3">送料フラグ</label>
        <span class="col-9"></span>
    </div>

    <hr>
</div>

<script type="text/javascript">
    // FileReader.onload https://lab.syncer.jp/Web/API_Interface/Reference/IDL/FileReader/onload/
    function handleCSV(csv) {
        let reader = new FileReader();
        reader.onload = function(event) {
            let csvName = document.getElementById("product-import-csv-filename");
            console.log(event.target);   // 同じ
            console.log(reader);   // 同じ
            csvName.innerHTML = csv[0].name;
        }
        console.log(csv);
        reader.readAsDataURL(csv[0]);
    }
</script>
@endsection