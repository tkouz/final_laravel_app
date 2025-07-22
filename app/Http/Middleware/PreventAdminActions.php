<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminActions
{
    /**
     * 管理ユーザーが特定の一般ユーザー向けアクションを実行するのを防ぎます。
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 認証済みユーザーが管理者である場合、アクションを許可しない
        if (Auth::check() && Auth::user()->isAdmin()) {
            // 管理ユーザーはこれらのアクションを実行できない旨のメッセージを表示してリダイレクト
            return redirect()->back()->with('error', '管理者アカウントではこの操作は許可されていません。');
        }

        return $next($request);
    }
}
