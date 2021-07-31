<?php

namespace App\Http\Controllers\Dashboard;

use App\Product;
use App\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
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

        if ($request->keyword !== null) {
            // rtrim関数：文字列の最後から空白 (もしくはその他の文字) を取り除く
            $keyword = rtrim($request->keyword);
            $total_count = Product::where('name', 'like', "%{$keyword}%")->orWhere('id', "{$keyword}")->count();
            $products = Product::where('name', 'like', "%{$keyword}%")->orWhere('id', "{$keyword}")->sortable($sort_query)->paginate(15);
        } else {
            $keyword = "";
            $total_count = Product::count();
            $products = Product::sortable($sort_query)->paginate(15);
        }

        return view('dashboard.products.index', compact('products', 'sort', 'sorted', 'total_count', 'keyword'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('dashboard.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;

        if ($request->recommend === 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        if ($request->carriage === 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }

        // name="image" input type="file"がnullじゃなければ
        if ($request->file('image') !== null) {
            /**
             * updateアクションを参照
             */
            $image = $request->file('image')->store('public/products');
            $product->image = basename($image);
        } else {
            $product->image = '';
        }

        $product->save();

        return redirect()->route('dashboard.products.index');
    }

    public function show(Product $product)
    {
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->category_id;

        if ($request->recommend === 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        if ($request->carriage === 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }
        // リクエスト内にname="image" のinput type="file"があればtrue
        if ($request->hasFile('image')) {
            /**
             * https://taidanahibi.com/unix/symbolic-link/
             * $image = $request->file('image')->store('ディレクトリ名');
             * name="image" input type="file" をstorage/app/ディレクトリ名 の中にファイルを保存してファイルパスを返す。今回の例だとstorage/app/public/products
             * 読み込み場所はpublic/storage/products   例：http://localhost/storage/products/~.png
             * imgのsrc属性を src="{{ asset('storage/products/'.$product->image) }} にする。
             */
            $image = $request->file('image')->store('public/products');
            //dd($image);   // "public/products/qaUyqgVzO6jJv0Bn7xA9kTJkYy8Mzrwa26DSkLWG.png"というファイルパスが返ってくる。
            /**
             * basenameはファイルパスを除いてファイル名のみと取り出せる。それをimageプロパティへ代入。
             * 今回の例だとqaUyqgVzO6jJv0Bn7xA9kTJkYy8Mzrwa26DSkLWG.png
             */
            $product->image = basename($image);
        } else {
            $product->image = '';
        }

        $product->update();

        return redirect()->route('dashboard.products.index');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('dashboard.products.index');
    }

    public function import(Request $request)
    {
        if ($request->csv !== null) {
            $csv = $request->csv;
        } else {
            $csv = null;
        }

        return view('dashboard.products.import', compact('csv'));
    }

    public function import_csv(Request $request)
    {
        // リクエスト内にname="csv" のinput type="file"があればtrue
        if ($request->hasFile('csv')) {
            /**
             * https://taidanahibi.com/unix/symbolic-link/
             * storage/app/publicディレクトリが作成されてこの中にExcelで作成したproduct.csvが入る。
             * => ①storage/app/public/csv/product.csv が保存場所になるはず。
             * => ②public/storage/csv/product.csv が読み込み場所になるはず。
             * => ③$file->store('public/csv')の返り値として①からstorage/app/を省略したpublic/csv/product.csvを取得できる。→ これだと拡張子がtxtに勝手に変換されてしまった。
             * => ③$file->storeAs('public/csv', ファイル名) で同様に$pathを取得。
             * => ④basename($path)でファイルパスを除いてファイル名(~.csv)のみを抽出して$csvに代入。
             * なのでimport.blade.phpの雛形ファイルダウンロードの読み込み場所を、②を参照してhref="{{ asset('storage/csv/'.$csv) }}"
             */
            $file = $request->file('csv');
            //$name = $file->getClientOriginalName();   // product.csv
            $hash = \Str::random(40);   // ランダムな文字列  グローバル名前空間に関してはここが分かりやすかった。https://qiita.com/7968/items/1e5c61128fa495358c1f
            $ext = $file->getClientOriginalExtension();   // 拡張子の取得 csv
            $name = $hash . '.' . $ext;   // ランダムな文字列.csv
            $path = $file->storeAs('public/csv', $name);   // public/csv/ランダムな文字列.csv
            $csv = basename($path);

            Excel::import(new ProductsImport, $file);
            return redirect()->route('dashboard.products.import', compact('csv'))->with('flash_message', 'CSVでの一括登録が成功しました!');
        }
        $csv = null;
        return redirect()->route('dashboard.products.import', compact('csv'))->with('flash_message', 'CSVが追加されていません。CSVを追加してください。');
    }
}
