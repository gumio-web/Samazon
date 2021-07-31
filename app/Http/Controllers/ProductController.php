<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request) // <= この$request内にsidebar.blade.phpで渡されたクエリ情報 $category_idの値が入っている。
    {
        //dd(Auth::user());
        //dd($request->sort); //formのvalueに空文字列を代入したらnullが返ってくる。
        $category_id = $request->category_id;
        $categories = Category::all();
        $major_category_names = Category::pluck('major_category_name')->unique();

        $sort_query = [];
        $sorted = "";
        $sort = [
            '並び替え' => '',
            '価格の安い順' => 'price asc',
            '価格の高い順' => 'price desc',
            '出品の古い順' => 'updated_at asc',
            '出品の新しい順' => 'updated_at desc',
        ];

        /**
         * 例 価格の安い順を選択した場合
         * $request->sort = "price asc";
         * $slices = ["price", "sort"];
         * $sort_query = ["price" => "asc"];
         * $sorted = "price asc";
         */
        if ($request->sort !== null) {
            $slices = explode(' ', $request->sort);
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->sort;
        }

        //sidebarから渡されたクエリ情報の有無を確認
        if ($category_id !== null) {
            //productsテーブルのcategory_idカラムの値が、categoriesテーブルのidカラムと一致するレコード
            $products = Product::where('category_id', $category_id)->sortable($sort_query)->paginate(15);
            $category = Category::find($category_id);
        } else {
            $products = Product::sortable($sort_query)->paginate(15);
            $category = null;
        }

        return view('products.index', compact('products', 'category', 'categories', 'major_category_names', 'sort', 'sorted', 'category_id'));
    }

    public function show(Product $product)
    {
        //dd($product->reviews());  // メソッドで呼び出すとhasManyインスタンスを取得できる
        $reviews = $product->reviews;   // Collectionインスタンスで取得
        //$reviews = $product->reviews()->get();  //　Collectionインスタンスで取得。上と同じ
        //$reviews = $product->reviews->all();  // 配列で取得
        //dd($reviews);
        return view('products.show', compact('product', 'reviews'));
    }

    // 'products/{product}/favorite'へハイパーリンクからのGETでアクセスで呼び出す
    public function favorite(Product $product)
    {
        $user = Auth::user();
        if ($user->hasFavorited($product)) {
            $user->unfavorite($product);   // favoritesテーブルから削除
        } else {
            $user->favorite($product);   // favoritesテーブルに追加
        }

        return redirect()->route('products.show', compact('product'));
    }
}
