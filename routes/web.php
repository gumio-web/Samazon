<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// メールでの認証が済んでいない場合はメール送信画面へと遷移
Auth::routes(['verify' => true]);
Route::get('/', 'WebController@index');
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get('/', 'DashboardController@index')->middleware('auth:admins')->name('index');   //  dashboard  ルート名dashboard.index
    Route::get('login', 'Dashboard\Auth\LoginController@showLoginForm')->name('login');   // dashboardj/login ルート名dashboard.login
    Route::post('login', 'Dashboard\Auth\LoginController@login')->name('login');
    Route::get('orders', 'Dashboard\OrderController@index')->middleware('auth:admins')->name('orders.index');
    Route::resource('users', 'Dashboard\UserController')->middleware('auth:admins');
    Route::resource('major_categories', 'Dashboard\MajorCategoryController')->middleware('auth:admins');
    Route::resource('categories', 'Dashboard\CategoryController')->middleware('auth:admins');
    // リソースルートのデフォルトのセットを超えてリソースコントローラにルートを追加する場合はRoute::resourceを呼び出す前にルートを定義しないといけない。
    Route::get('products/import', 'Dashboard\ProductController@import')->middleware('auth:admins')->name('products.import');
    Route::post('products/import', 'Dashboard\ProductController@import_csv')->middleware('auth:admins')->name('products.import_csv');
    Route::resource('products', 'Dashboard\ProductController')->middleware('auth:admins');
});

Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('mypage', 'UserController@index')->name('mypage.index');   // URI = users/mypage   name = users.mypage.index
    Route::get('mypage/edit', 'UserController@edit')->name('mypage.edit');
    Route::get('mypage/address/edit', 'UserController@edit_address')->name('mypage.edit_address');
    Route::put('mypage', 'UserController@update')->name('mypage.update');
    Route::get('mypage/password/edit', 'UserController@edit_password')->name('mypage.edit_password');
    Route::put('mypage/password', 'UserController@update_password')->name('mypage.update_password');
    Route::get('mypage/favorite', 'UserController@favorite')->name('mypage.favorite');
    Route::delete('mypage/delete', 'UserController@destroy')->name('mypage.destroy');
    Route::get('mypage/cart_history', 'UserController@cart_history_index')->name('mypage.cart_history');
    Route::get('mypage/cart_history/{number}', 'UserController@cart_history_show')->name('mypage.cart_history_show');
    Route::get('mypage/register_card', 'UserController@register_card')->name('mypage.register_card');
    Route::post('mypage/token', 'UserController@token')->name('mypage.token');

    Route::get('carts', 'CartController@index')->name('carts.index');   // URI = users/carts   name = users.carts.index
    Route::post('carts', 'CartController@store')->name('carts.store');
    Route::put('carts/put', 'Cartcontroller@update')->name('carts.update');
    Route::delete('carts/delete', 'CartController@destroy')->name('carts.destroy');
});

// create, store, edit, update, destroyは管理者の方で行うのでユーザ側は一覧表示(index)と個別ページの表示(show)のみ
Route::get('products', 'ProductController@index')->name('products.index');
Route::get('products/{product}', 'ProductController@show')->name('products.show');
Route::get('products/{product}/favorite', 'ProductController@favorite')->name('products.favorite');
Route::post('products/{product}/reviews', 'ReviewController@store')->name('products.reviews');   // products.showのレビュー追加フォームから架空のページへPOSTアクセス