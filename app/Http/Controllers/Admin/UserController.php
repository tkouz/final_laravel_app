<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Userモデルをuse

class UserController extends Controller
{
    /**
     * ユーザーの一覧を表示します。
     */
    public function index()
    {
        // 全てのユーザーを取得（必要に応じてページネーションやソートを追加）
        $users = User::orderBy('created_at', 'desc')->paginate(20); // 例: 20件ずつページネーション

        return view('admin.users.index', compact('users'));
    }

    /**
     * ユーザーの利用を停止/再開します。
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(User $user): \Illuminate\Http\RedirectResponse
    {
        // 管理者自身の利用停止は許可しない
        if ($user->isAdmin()) {
            return back()->with('error', '管理者アカウントの利用状態は変更できません。');
        }

        $user->is_active = !$user->is_active; // 現在の状態を反転
        $user->save();

        return back()->with('success', 'ユーザーの利用状態を切り替えました。');
    }
}
