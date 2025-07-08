<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ★削除: AuthorizesRequestsトレイトのインポートを削除

class CommentController extends Controller
{
    // use AuthorizesRequests; // ★削除: トレイトの使用宣言を削除

    /**
     * 指定された回答に対して新しいコメントをデータベースに保存します。
     * コメント投稿後、元の質問詳細ページにリダイレクトします。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer  コメントを投稿する対象の回答モデルのインスタンス
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Answer $answer): RedirectResponse
    {
        // リクエストデータのバリデーション
        $request->validate([
            'content' => 'required|string', // コメント内容は必須、文字列
        ]);

        // 新しいコメントをデータベースに作成
        $comment = new Comment([
            'user_id' => auth()->id(),      // 認証済みユーザーのIDをコメント投稿者とする
            'answer_id' => $answer->id,     // コメントが属する回答のID
            'content' => $request->content, // リクエストからコメント内容を取得
        ]);

        // コメントを保存
        $comment->save();

        // コメントが投稿された回答が属する質問の詳細ページへリダイレクト
        // その際、成功メッセージをセッションにフラッシュ
        return redirect()->route('questions.show', $answer->question)->with('status', 'コメントが投稿されました！');
    }
}