<?php

namespace App\Http\Controllers;

use App\User;
use App\product;
use App\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    // ユーザのマイページのビューを表示
    public function index()
    {
        $user = Auth::user();

        return view('users.mypage.index', compact('user'));
    }

    // ユーザ情報編集ページのビューを表示
    public function edit(User $user)
    {
        $user = Auth::user();

        return view('users.mypage.edit', compact('user'));
    }

    // ユーザのお届け先情報の編集ページのビューを表示
    public function edit_address()
    {
        $user = Auth::user();

        return view('users.mypage.edit_address', compact('user'));
    }

    // 上2つの編集ページからのPUTアクション
    public function update(Request $request, User $user)
    {
        $user = Auth::user();

        //通常の編集ページからのPUTだとpostal_codeとaddressを渡せないので3項演算子を使う。
        $user->name = $request->name ? $request->name : $user->name;
        $user->email = $request->email ? $request->email : $user->email;
        $user->postal_code = $request->postal_code ? $request->postal_code : $user->postal_code;
        $user->address = $request->address ? $request->address : $user->address;
        $user->phone = $request->phone ? $request->phone : $user->phone;
        $user->update();

        return redirect()->route('users.mypage.index');
    }

    // ユーザのパスワード編集ページのビューを表示
    public function edit_password()
    {
        return view('users.mypage.edit_password');
    }

    // パスワード編集ページからのPUTアクション
    public function update_password(Request $request)
    {
        $user = Auth::user();

        if ($request->password == $request->password_confirmation) {
            $user->password = bcrypt($request->password);   // パスワードをハッシュ化
            $user->update();
        } else {
            return redirect()->route('users.mypage.edit_password');
        }

        return redirect()->route('users.mypage.index');
    }

    // ユーザのお気に入り商品の一覧ページを表示
    public function favorite()
    {
        $user = Auth::user();
        $favorites = $user->favorites(Product::class)->get();

        return view('users.mypage.favorite', compact('favorites'));
    }

    // ユーザ側から論理削除で退会するDELETEアクション
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->deleted_flag) {
            $user->deleted_flag = false;
        } else {
            $user->deleted_flag = true;
        }

        $user->update();
        Auth::logout();

        return redirect('/');
    }
    // 購入履歴一覧を表示
    public function cart_history_index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1;
        $orders = ShoppingCart::getCurrentUserOrders(Auth::id());
        $total = count($orders);
        $paginator = new LengthAwarePaginator(array_slice($orders, ($page - 1) * 15, 15), $total, 15, $page, ['path' => $request->url()]);

        return view('users.mypage.cart_history_index', compact('orders', 'paginator'));
    }
    // 特定の購入履歴を表示
    public function cart_history_show(Request $request)
    {
        $number = $request->number;
        // ログインユーザの購入番号が$numberのレコードを取得
        $cart_purchased = DB::table('shoppingcart')->where('instance', Auth::id())->where('number', $number)->first();
        // そのレコードのidentifierを取得
        $identifier = $cart_purchased->identifier;
        // そのレコードを復活させる = テーブルから削除する。
        Cart::instance(Auth::id())->restore($identifier);
        /**
         * 今この瞬間復活した = テーブルから削除されたコンテンツ(カートアイテム)を取得
         * レコードでは合計金額や合計個数でまとまっているので個別の購入内容を見ることができない。
         * これが欲しいがためにカートをリストアした。
         */
        $cart_contents = Cart::content();
        /**
         * store($identifier)で器になるレコードを先に作成する。CartController参照。
         * identifier(主キー), instance(主キー、ログインユーザのID), content(カートに入っていたもの) , number(null)で作成される。
         */
        Cart::instance(Auth::id())->store($identifier);
        // 復活してカートに入っていたコンテンツを削除
        Cart::destroy();
        // 元に戻す。
        DB::table('shoppingcart')->where('instance', Auth::id())
            ->where('number', null)
            ->update([
                'code' => $cart_purchased->code,
                'number' => $number,
                'price_total' => $cart_purchased->price_total,
                'qty' => $cart_purchased->qty,
                'buy_flag' => $cart_purchased->buy_flag,
                'updated_at' => $cart_purchased->updated_at
            ]);

        return view('users.mypage.cart_history_show', compact('cart_contents', 'cart_purchased'));
    }

    // クレジットカード登録画面を表示
    public function register_card(Request $request)
    {
        $user = Auth::user();

        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);

        $card = [];
        $count = 0;

        if ($user->token != "") {
            // 顧客IDからカードオブジェクトを取得
            $result = \Payjp\Customer::retrieve($user->token)->cards->all(['limit' => 1])->data[0];
            //dd($result);
            // 顧客のカード枚数を取得
            $count = \Payjp\Customer::retrieve($user->token)->cards->all()->count;
            // カードオブジェクトのプロパティを配列にまとめる。
            $card = [
                'brand' => $result['brand'],
                'exp_month' => $result['exp_month'],
                'exp_year' => $result['exp_year'],
                'last4' => $result['last4'],
            ];
        }

        return view('users.mypage.register_card', compact('card', 'count'));
    }

    // クレジットカード登録のPOST
    public function token(Request $request)
    {
        //dd(request('payjp-token'));   // tok_747d95cb1cbf4e6d832e63ec02e9
        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);

        $user = Auth::user();
        $cu_id = $user->token;

        if ($cu_id != "") {
            // 顧客IDから顧客情報(customerオブジェクト)を取得
            $cu = \Payjp\Customer::retrieve($cu_id);
            /**
             * 顧客のカードリストから1枚のカードオブジェクトを取得(指定したidのカードオブジェクト)
             * $card = $cu->cards->retrieve($cu->cards->data[0]);だとエラーになって取得できない。idを入れないとダメ。
             * https://pay.jp/docs/api/?php#%E9%A1%A7%E5%AE%A2%E3%81%AE%E3%82%AB%E3%83%BC%E3%83%89%E6%83%85%E5%A0%B1%E3%82%92%E5%8F%96%E5%BE%97
             */
            $card = $cu->cards->retrieve($cu->cards->data[0]['id']);
            // カードオブジェクトを削除
            $card->delete();
            // 顧客のカードを作成。このトークンIDを使って決済を行う。
            $cu->cards->create(['card' => request('payjp-token')]);
        } else {
            /**
             * customerオブジェクトを生成 引数にcard => トークンIDなどを設定して顧客情報を登録。このトークンIDを使って決済を行う。
             * https://pay.jp/docs/api/#%E9%A1%A7%E5%AE%A2%E3%82%92%E4%BD%9C%E6%88%90
             */
            $cu = \Payjp\Customer::create(['card' => request('payjp-token')]);
            // ユーザのデータベースに顧客idを登録
            $user->token = $cu->id;
            $user->update();
        }

        return redirect()->route('users.mypage.index');
    }
}
