<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ユーザの一覧を表示
    public function index(Request $request)
    {
        if ($request->keyword !== null) {
            $keyword = rtrim($request->keyword);

            if (is_int($request->keyword)) {
                $keyword = (string)$keyword;
            }

            $users = User::where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%")
                ->orWhere('postal_code', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%")
                ->orWhere('id', "{$keyword}")
                ->paginate(15);
        } else {
            $users = User::paginate(15);
            $keyword = "";
        }

        return view('dashboard.users.index', compact('users', 'keyword'));
    }

    // ユーザの退会処理(今回は論理削除)
    public function destroy(User $user)
    {
        if ($user->deleted_flag) {
            $user->deleted_flag = false;
        } else {
            $user->deleted_flag = true;
        }

        $user->update();

        return redirect()->route('dashboard.users.index');
    }
}
