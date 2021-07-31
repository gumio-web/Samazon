<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class ShoppingCart extends Model
{
    // 日ごとの売り上げの内容を配列で取得する関数
    public static function getDailyBillings()
    {
        // 日付(インデックス)ごとのtotal, count, avgを入れる配列
        $billings = [];
        // まだ一度もサイト内で購入履歴がない場合は空配列を返す。
        $count = DB::table('shoppingcart')->count();
        if ($count == 0) {
            return $billings;
        }

        // shoppingcartテーブルのcreated_atカラムが最新のレコードをひとつ取得して、そのcreated_atフィールドの値を取得 例：2021-07-26 07:24:44
        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at;
        $recent_date = new Carbon($recent_date);
        //dd($recent_date->toDateTimeString());   // "2021-07-26 07:24:44"
        // 日付を1日進める
        $recent_date->addDays(1);
        //dd($recent_date->toDateTimeString());   // "2021-07-27 07:24:44"

        // shoppingcartテーブルの最初のレコード(一番古いレコード)を取得して、そのcreated_atフィールドの値を取得 例：2021-07-23 12:55:35
        $old_date = DB::table('shoppingcart')->first()->created_at;
        $old_date = new Carbon($old_date);
        //dd($old_date->toDateTimeString());   // "2021-07-23 12:55:35"

        // (最新の日付+1 != 古い日付)を満たす限り繰り返す。
        while ($recent_date->format('Y-m-d') != $old_date->format('Y-m-d')) {
            $date = $old_date->format('Y-m-d');   // "2021-07-23"
            // 古い日付のレコードのクエリビルダを生成。例えばその日に1回しか買われなかったら1つのレコード、5回買われたら5つのレコード
            $query = DB::table('shoppingcart')->whereDate('created_at', '=', $date);
            // []のようにキーを指定しないと自動的にインデックス番号が割り振られる。
            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),   // 取得したレコードのprice_totalカラムの合計値を取得
                'count' => $query->count(),
                'avg' => $query->avg('price_total'),   // 取得したレコードのprice_totalカラムの平均値を取得
            ];
            // 古い日付を一日進める。この処理で最新の日付+1と一致 = 全ての日付のデータを取得できたということ。
            $old_date->addDays(1);
        }

        return $billings;
    }

    // 月ごとの売り上げの内容を配列で取得する関数
    public static function getMonthlyBillings()
    {
        // 日付(インデックス)ごとのtotal, count, avgを入れる配列
        $billings = [];
        // まだ一度もサイト内で購入履歴がない場合は空配列を返す。
        $count = DB::table('shoppingcart')->count();
        if ($count === 0) {
            return $billings;
        }

        // shoppingcartテーブルのcreated_atカラムが最新のレコードをひとつ取得して、そのcreated_atフィールドの値を取得 例：2021-07-26 07:24:44
        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at;
        $recent_date = new Carbon($recent_date);
        $recent_date->addMonths(1);
        //dd($recent_date->toDateTimeString());   // "2021-08-26 07:24:44"

        // shoppingcartテーブルの最初のレコード(一番古いレコード)を取得して、そのcreated_atフィールドの値を取得 例：2021-07-23 12:55:35
        $old_date = DB::table('shoppingcart')->first()->created_at;
        $old_date = new Carbon($old_date);

        // (最新の年月+1 != 古い年月)を満たす限り繰り返す。
        while ($recent_date->format('Y-m') != $old_date->format('Y-m')) {
            $date = $old_date->format('Y-m');
            // 古い年月のレコードのクエリビルダを生成。例えばその年のその月に1回しか買われなかったら1つのレコード、5回買われたら5つのレコード
            $query = DB::table('shoppingcart')->whereYear('created_at', '=', $old_date->year)->whereMonth('created_at', '=', $old_date->month);
            // []のようにキーを指定しないと自動的にインデックス番号が割り振られる。
            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),   // 取得したレコードのprice_totalカラムの合計値を取得
                'count' => $query->count(),
                'avg' => $query->avg('price_total')   // 取得したレコードのprice_totalカラムの平均値を取得
            ];
            // 古い年月の月をひとつ進める。この処理で最新の年月+1と一致 = 全ての月のデータを取得できたということ。
            $old_date->addMonths(1);
        }

        return $billings;
    }

    // サイト内の注文情報を配列として取得するメソッドを定義
    public static function getOrders($code)
    {
        // 検索欄に入力した内容($code)を、shoppingcartテーブルのcodeフィールドに含むレコードを取得。$codeが空文字列(nullはNG)の場合は%%になるので全レコードを取得。
        $shoppingcarts = DB::table('shoppingcart')->where('code', 'like', "%{$code}%")->get();
        $orders = [];
        // 現在までにアプリケーション内で購入された回数をカウント
        $count = DB::table('shoppingcart')->count();

        if ($count === 0) {
            return $orders;
        }

        foreach ($shoppingcarts as $order) {
            // []のようにキーを指定しないと自動的にインデックス番号が割り振られる。
            $orders[] = [
                'created_at' => $order->created_at,
                'total' => $order->price_total,
                'user_name' => User::find($order->instance)->name,
                'code' => $order->code,
            ];
        }

        return $orders;
    }

    // 特定のユーザの購入履歴一覧の内容を配列で取得するメソッド
    public static function getCurrentUserOrders($user_id)
    {
        // 特定の購入者の購入内容を全て取得する。
        $shoppingcarts = DB::table('shoppingcart')->where('instance', $user_id)->get();
        $orders = [];
        $count = DB::table('shoppingcart')->count();
        // 現在までにアプリケーション内で購入された回数をカウント
        if ($count === 0) {
            return $orders;
        }

        foreach ($shoppingcarts as $order) {
            // []のようにキーを指定しないと自動的にインデックス番号が割り振られる。
            $orders[] = [
                'number' => $order->number,
                'created_at' => $order->updated_at,
                'total' => $order->price_total,
                'user_name' => User::find($order->instance)->name,
                'code' => $order->code,
            ];
        }
        // 注文情報を含む情報を返す。
        return $orders;
    }
}
