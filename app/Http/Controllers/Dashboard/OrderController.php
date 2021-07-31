<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class OrderController extends Controller
{
    // $requestでクエリ情報を受け取る。
    public function index(Request $request)
    {
        // $request->pageがnullじゃなければ$pageに$request->pageを代入。 $request->pageがnullなら$pageに1を代入。
        $page = $request->page != null ? $request->page : 1;
        // ここ重要。codeをnullのままgetOrdersに渡すと何も取得できないので、必ず空文字列に変換する。
        $code = $request->code != null ? $request->code : "";
        $orders = ShoppingCart::getOrders($code);
        $total = count($orders);
        // array_slice(配列, $offset(スルーする数), $length(取得する数。数が足りない場合は取得できる分全部を取得してくれる。))  独習PHP P172参照
        // new LengthAwarePaginator(①そのページで表示する分の配列, ②配列の中身の総数, ③1ページに表示する数, ④現在のページ番号, ⑤URLを指定する配列[パス => ルート名]) 
        $paginator = new LengthAwarePaginator(array_slice($orders, ($page - 1) * 15, 15), $total, 15, $page, ['path' => $request->url()]);

        return view('dashboard.orders.index', compact('total', 'paginator', 'code'));
    }
}