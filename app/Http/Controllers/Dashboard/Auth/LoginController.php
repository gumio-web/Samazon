<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// 管理者専用のログイン画面を実装
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;   // この中にloginメソッドがある。

    // ログイン後にリダイレクトするURLを指定
    protected $redirectTo = '/dashboard';

    // ログイン時に使用する認証を指定
    public function __construct()
    {
        $this->middleware('guest:admins')->except('logout');
    }

    // ログイン時に使用する認証を指定
    protected function guard()
    {
        
        return Auth::guard('admins');
    }

    // ログイン画面で使用するビューを指定
    public function showLoginForm()
    {
        return view('dashboard.auth.login');
    }

    // ログアウト後のリダイレクト先を指定
    public function loggedOut(Request $request)
    {
        return redirect()->route('dashboard.login');
    }
}
