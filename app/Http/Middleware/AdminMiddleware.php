<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 'admin' ガードを使用してチェックを行う
        if (!Auth::guard('admin')->check()) {
            // ユーザーが管理者でない場合、ログインページにリダイレクトする
            return redirect('/admin/login'); // 管理者のログインページのURLに変更してください
        }
        
        return $next($request);
    }
}
