<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class DashboardController extends Controller
{
    // GETアクセスで引数に$requestがある場合はクエリ情報として利用される
    public function index(Request $request)
    {
        // $request->pageがnullじゃないなら$request->pageを$pageに代入/$request->pageがnullなら$pageに1を代入
        $page = $request->page != null ? $request->page : 1;
        $sort = $request->sort;
        $billings = [];
        if ($request->sort == 'month') {
            $billings = ShoppingCart::getMonthlyBillings();
        } else {
            $billings = ShoppingCart::getDailyBillings();
        }
        //dd($billings);
        $total = count($billings);
        // array_slice(配列, $offset(スルーする数), $length(取得する数。数が足りない場合は取得できる分全部を取得してくれる。))  独習PHP P172参照
        // new LengthAwarePaginator(①そのページで表示する分の配列, ②配列の中身の総数, ③1ページに表示する数, ④現在のページ番号, ⑤URLを指定する配列
        $paginator = new LengthAwarePaginator(array_slice($billings, ($page - 1) * 15, 15), $total, 15, $page, ['path' => $request->url()]);
        return view('dashboard.index', compact('billings', 'total', 'paginator', 'sort'));
    }
}
