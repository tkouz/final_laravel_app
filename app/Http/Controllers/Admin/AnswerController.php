<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * 回答の表示状態を切り替える
     *
     * @param \App\Models\Answer $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleVisibility(Answer $answer)
    {
        // is_visible フラグを反転させる
        $answer->update(['is_visible' => !$answer->is_visible]);

        // 元のページにリダイレクトし、成功メッセージをセッションに保存
        return back()->with('success', '回答の表示状態を切り替えました。');
    }
}
