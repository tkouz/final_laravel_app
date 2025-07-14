<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // ★追加：Authファサードを使うので追加

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ユーザーがログインしているか、かつisAdmin()メソッドで管理者であるかをチェック
        // Auth::check() はユーザーが認証済みかを確認
        // Auth::user()->isAdmin() はUserモデルで定義したisAdmin()メソッドを呼び出す
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
            // または、ログインページやホーム画面にリダイレクトすることもできます
            // return redirect()->route('login'); // ログインページへリダイレクト
            // return redirect()->route('home'); // ホーム画面へリダイレクト
        }

        return $next($request); // 管理者であれば次のリクエスト処理へ進む
    }
}