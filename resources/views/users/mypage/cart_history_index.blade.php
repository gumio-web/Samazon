@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <span>
                <a href="{{ route('users.mypage.index') }}">マイページ</a> > お届け先変更
            </span>

            <div class="table-responsive mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">注文ID</th>
                            <th scope="col">注文番号</th>
                            <th scope="col">購入日時</th>
                            <th scope="col">合計金額</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paginator as $order)
                        <tr>
                            <td>
                                <a href="{{ route('users.mypage.cart_history_show', ['number' => $order['number']]) }}">
                                    {{ $order['number'] }}
                                </a>
                            </td>
                            <td>{{ $order['code'] }}</td>
                            <td>{{ $order['created_at']}}</td>
                            <td>{{ $order['total']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $paginator->links() }}
        </div>
    </div>
</div>

@endsection 