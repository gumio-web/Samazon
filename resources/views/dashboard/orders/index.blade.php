@extends('layouts.dashboard')

@section('content')
<div class="w-75">

    <h1>受注一覧</h1>

    <div class="w-75">
        <!-- codeのクエリ情報を渡す。 -->
        <form method="GET" action="{{route('dashboard.orders.index')}}">
            <div class="d-flex flex-inline form-group">
                <div class="d-flex align-items-center">
                    注文番号
                </div>
                <input id="search-products" name="code" class="form-controll ml-2 w-50" placeholder="123456789" value="{{ $code }}" />
            </div>
            <button type="submit" class="btn samazon-submit-button">検索</button>
        </form>
    </div>
    <div class="mt-5">合計注文件数：{{$total}}件</div>
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col" class="w-25">注文番号</th>
                <th scope="col">注文者名</th>
                <th scope="col">注文日時</th>
                <th scope="col">購入金額</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paginator as $order)
            <tr>
                <th class="align-middle" scope="row">{{ $order['code'] }}</td>
                <td class="align-middle">{{ $order['user_name'] }}</td>
                <td class="align-middle">{{ $order['created_at'] }}</td>
                <td class="align-middle">{{ $order['total'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $paginator->links() }}
</div>
@endsection 