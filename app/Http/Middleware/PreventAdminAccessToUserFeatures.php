<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User; // ★追加: Userモデルをuseすることで、Intelephenseが型を認識しやすくなる

class PreventAdminAccessToUserFeatures
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ログインしているユーザーが管理者である場合、
        // 一般ユーザー向けの機能へのアクセスを拒否し、管理者ダッシュボードへリダイレクト
        if (Auth::check() && Auth::user() instanceof User && Auth::user()->isAdmin()) { // ★修正: instanceof User で型ヒントを追加
            return redirect()->route('admin.dashboard')->with('error', '管理者アカウントは一般ユーザー機能にアクセスできません。');
        }

        return $next($request);
    }
}
