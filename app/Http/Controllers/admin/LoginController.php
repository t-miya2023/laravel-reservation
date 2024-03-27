<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    //use AuthenticatesUsers;
    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //ログイン後のリダイレクト先URL
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //ログインページにユーザーが管理者として認証されていない場合にのみアクセスを許可するミドルウェア。
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout'); //ログアウト処理は常にアクセスできる
    }
        //管理者の認証が行われる
    protected function guard()
    {
        return Auth::guard('admin');
    }
    //performLogout メソッドを呼び出してログアウト処理を実行し、その後に /admin/logout にリダイレクト
    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect('admin/login');
    }
}
