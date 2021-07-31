<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Product;

class CartController extends Controller
{
    // 現在カートに入っている商品一覧とこれまで購入した商品履歴(カートの履歴)を表示
    public function index()
    {
        // これまでユーザが追加して今カートに入っている情報を取得
        $cart = Cart::instance(Auth::id())->content();
        $total = 0;

        foreach ($cart as $c) {
            // カートの商品に送料が設定されていた場合
            //dd(Product::find($c->id)->carriage_flag);
            if ($c->options->carriage) {
                $total += $c->qty * ($c->price + env('CARRIAGE'));
            } else {
                $total += $c->qty * $c->price;
            }
        }

        return view('users.carts.index', compact('cart', 'total'));
    }

    // カートに商品を追加する
    public function store(Request $request)
    {
        // ユーザIDを元にカートのデータを作成し、リクエスト情報を元にadd関数で商品を追加
        Cart::instance(Auth::id())->add([
            'id' => $request->id,
            'name' => $request->name,
            'qty' => $request->qty,
            'price' => $request->price,
            'weight' => $request->weight,
            'options' => ['carriage' => $request->carriage,],
        ]);

        $product = Product::find($request->id);

        return redirect()->route('products.show', compact('product'));
    }

    /**
     * 応用編で実装予定。過去の商品履歴(カートの履歴)を表示する。
     * ユーザID(Auth::id())とそのユーザの注文履歴(カートの履歴)のID($count)を元に、過去の履歴を表示
     * => UserControllerのcart_history_indexとcart_history_showの方で実装完了なのでこれは必要ない。
     */
    public function show($id)
    {
        $cart = DB::table('shoppingcart')->where('instance', Auth::id())->where('identifier', $count)->get();

        return view('users.carts.show', compact('cart'));
    }

    // 応用編で実装する。現在のカートの中に保存されている商品の個数の変更、商品をカートから削除
    public function update(Request $request)
    {
        $id = $request->id;
        $qty = $request->qty;

        if ($request->delete === "yes") {
            // フォームから送信された商品IDを利用して、Cart::remove()に削除したいカート内の商品ID($cartItem->rowId)を渡すことでカートから削除できる
            Cart::instance(Auth::id())->remove($id);
        } else if($request->update === "yes") {
            // フォームから送信された商品IDの個数を$request->qtyへ変更する。
            Cart::instance(Auth::id())->update($id, $qty);
        }

        return redirect()->route('users.carts.index');
    }

    // カート内の商品を購入する処理
    public function destroy(Request $request)
    {
        // このサイトの全てのユーザが今まで購入したカートインスタンスをCollectionで取得
        $all_user_shoppingcarts = DB::table('shoppingcart')->get();
        // 今ログインしているユーザが今まで購入したカートインスタンスをCollectionで取得
        $user_shoppingcarts = DB::table('shoppingcart')->where('instance', Auth::id())->get();
        // このサイトの全てのユーザの今までの購入回数を取得
        $count = $all_user_shoppingcarts->count();
        // 今ログインしているユーザの今までの購入回数を取得
        $number = $user_shoppingcarts->count();
        // このサイトの全てのユーザの購入回数を+1する。この値がidentifier(主キー)カラムに保存される。
        $count += 1;
        // 今ログインしているユーザの購入回数を+1する。この値がnumberカラムに保存される。
        $number += 1;

        // この瞬間にカートに入っている商品情報をCollectionで取得
        $cart = Cart::instance(Auth::id())->content();
        $price_total = 0;
        $qty_total = 0;
        foreach ($cart as $c) {
            //dd($c->options);
            //dd($c->options->carriage);
            if ($c->options->carriage) {
                $price_total += $c->qty * ($c->price + 800);
            } else {
                $price_total += $c->qty * $c->price;
            }
            $qty_total += $c->qty;
        }
        // store($identifier)で器になるレコードを先に作成
        // identifier(主キー), instance(主キー、ログインユーザのID), content(カートに入っていたもの) , number(null)で作成される。
        Cart::instance(Auth::id())->store($count);
        // このレコードを取得して購入フラグなどを更新
        DB::table('shoppingcart')
            ->where('instance', Auth::id())
            ->where('number', null)   // これで上で作成したレコードを選択
            ->update([
                // substr(文字列, $offset, $length)  offset指定位置からlengthバイト分の文字列を返す。
                'code' => substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10),
                'number' => $number,
                'price_total' => $price_total,
                'qty' => $qty_total,
                'buy_flag' => true,
                'updated_at' => date("Y/m/d H:i:s")
            ]);

        // 支払いを作成
        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);
        $user = Auth::user();
        // https://pay.jp/docs/api/#%E6%94%AF%E6%89%95%E3%81%84%E3%82%92%E4%BD%9C%E6%88%90
        // https://zakkuri.life/%E3%80%90laravel%E3%80%91payjp%E3%81%A7%E6%B1%BA%E6%B8%88%E3%81%99%E3%82%8B-%E6%96%B0%E8%A6%8F%E3%81%AE%E3%81%BF/
        // https://reon777.com/2020/09/16/laravel-payjp/
        //dd(\Payjp\Charge::create(['customer' => $user->token, 'amount' => $price_total, 'currency' => 'jpy',]));
        \Payjp\Charge::create([
            'customer' => $user->token,
            'amount' => $price_total,
            'currency' => 'jpy',
        ]);

        // 今カートに入っていた商品を削除
        Cart::instance(Auth::id())->destroy();

        return redirect()->route('users.carts.index')->with('message', '支払いが完了しました！');
    }
}
