@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center mt-3">
    <div class="w-75">
        <h1>ショッピングカートだよ</h1>
        @if (session('message'))
        <div class="flash_message">
            {{session('message')}}
        </div>
        @endif
        <div class="row">
            <div class="offset-8 col-4">
                <div class="row">
                    <div class="col-6">
                        <h2>数量</h2>
                    </div>
                    <div class="col-6">
                        <h2>合計</h2>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row align-items-center">
            @foreach ($cart as $cartItem)
            <div class="col-md-2 mt-2 mb-2">
                <!-- ここの$cartItemはCartItemインスタンス。CartItemインスタンスのIDはProductのIDと一致しているのでそれをパラメータに渡す。 -->
                <a href="{{route('products.show', $cartItem->id)}}">
                    <img src="{{ asset('img/dummy.jpg')}}" class="img-fluid w-100">
                </a>
            </div>
            <div class="col-md-4">
                <h3>{{$cartItem->name}}</h3>
            </div>
            <div class="col-md-2">
                <a href="/users/carts"
                    onclick="event.preventDefault(); document.getElementById('delete-form{{$cartItem->id}}').submit();"
                    class="carts-delete-link">
                    カートから削除
                </a>
                <form id="delete-form{{$cartItem->id}}" action="{{route('users.carts.update')}}" method="POST"
                    style="display: none;">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="delete" value="yes">
                    <input type="hidden" name="update" value="no">
                    <input type="hidden" name="id" value="{{$cartItem->rowId}}">
                    <input type="hidden" name="qty" value="{{$cartItem->qty}}">
                </form>
            </div>
            <div class="col-md-2">
                <form action="{{route('users.carts.update', $cartItem->rowId)}}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="delete" value="no">
                    <input type="hidden" name="update" value=yes>
                    <input type="hidden" name="id" value="{{$cartItem->rowId}}">
                    <select name="qty" onChange="this.form.submit();" class="w-50" style="text-align-last: center;">
                        <option value="{{$cartItem->qty}}" selected>{{$cartItem->qty}}</option>
                        @for($i = 1; $i < 100; $i++) <option value="{{$i}}">{{$i}}</option>
                            @endfor
                    </select>

                </form>
            </div>
            <div class="col-md-2">
                @if ($cartItem->options->carriage)
                <h3 class="w-100">￥{{$cartItem->qty * ($cartItem->price + env('CARRIAGE'))}}</h3>
                @else
                <h3 class="w-100">￥{{$cartItem->qty * $cartItem->price}}</h3>
                @endif
            </div>
            @endforeach
        </div>

        <hr>

        <div class="offset-8 col-4">
            <div class="row">
                <div class="col-6">
                    <h2>合計</h2>
                </div>
                <div class="col-6">
                    <h2>￥{{$total}}</h2>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    表示価格は税込みです
                </div>
            </div>
        </div>

        <form method="post" action="{{route('users.carts.destroy')}}" class="d-flex justify-content-end mt-3">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <a href="/" class="btn samazon-favorite-button border-dark text-dark mr-3">
                買い物を続ける
            </a>
            <!--
                data-toggle="modal" data-target="#buy-confirm-modal"でモーダルを使用することに加え、画面に表示する
                モーダルのidを指定する。そのidを持つHTML要素をモーダルとして呼び出すことができる。
            -->
            <div class="btn samazon-submit-button" data-toggle="modal" data-target="#buy-confirm-modal">購入する</div>

            <div class="modal fade" id="buy-confirm-modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">購入を確定しますか？</h5> <button type="button"
                                class="close" data-dismiss="modal" aria-label="閉じる">
                                <span aria-hidden="true">&times;</span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn samazon-favorite-button border-dark text-dark"
                                data-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn samazon-submit-button">購入</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection