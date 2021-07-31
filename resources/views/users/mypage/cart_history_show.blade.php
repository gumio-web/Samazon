@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <span>
                <a href="{{ route('users.mypage.index') }}">マイページ</a> > <a
                    href="{{ route('users.mypage.cart_history') }}">注文履歴</a> > お届け先変更
            </span>

            <h1 class="mt-3">注文履歴詳細</h1>

            <h4 class="mt-3">ご注文情報</h4>

            <hr>

            <div class="row">
                <div class="col-5 mt-2">
                    注文番号
                </div>
                <div class="col-7 mt-2">
                    {{ $cart_purchased->code }}
                </div>

                <div class="col-5 mt-2">
                    注文日時
                </div>
                <div class="col-7 mt-2">
                    {{ $cart_purchased->updated_at }}
                </div>

                <div class="col-5 mt-2">
                    合計金額
                </div>
                <div class="col-7 mt-2">
                    {{ $cart_purchased->price_total }}円
                </div>

                <div class="col-5 mt-2">
                    点数
                </div>
                <div class="col-7 mt-2">
                    {{ $cart_purchased->qty }}点
                </div>

                <div class="col-5 mt-2">
                    注文番号
                </div>
                <div class="col-7 mt-2">
                    {{ $cart_purchased->code }}
                </div>
            </div>

            <hr>

            <div class="row">
                @foreach ($cart_contents as $cartItem)
                <div class="col-md-5 mt-2">
                    <a href="{{route('products.show', $cartItem->id)}}" class="ml-4">
                        <img src="{{ asset('img/dummy.jpg')}}" class="img-fluid w-75">
                    </a>
                </div>
                <div class="col-md-7 mt-2">
                    <div class="flex-cloumn">
                        <p class="mt-4">{{$cartItem->name}}</p>
                        <div class="row">
                            <div class="col-2 mt-2">
                                数量
                            </div>
                            <div class="col-10 mt-2">
                                {{$cartItem->qty}}
                            </div>

                            <div class="col-2 mt-2">
                                小計
                            </div>
                            <div class="col-10 mt-2">
                                ￥{{$cartItem->qty * $cartItem->price}}
                            </div>

                            <div class="col-2 mt-2">
                                送料
                            </div>
                            <div class="col-10 mt-2">
                                @if ($cartItem->options->carriage)
                                ￥{{$cartItem->qty * 800}}
                                @else
                                ￥0
                                @endif
                            </div>

                            <div class="col-2 mt-2">
                                合計
                            </div>
                            <div class="col-10 mt-2">
                                @if ($cartItem->options->carriage)
                                ￥{{$cartItem->qty * ($cartItem->price + 800)}}
                                @else
                                ￥{{$cartItem->qty * $cartItem->price}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection