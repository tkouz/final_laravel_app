<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; // JsonResponseをインポート

class LikeController extends Controller
{
    /**
     * 質問に「いいね！」を追加します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function like(Request $request, Question $question): JsonResponse
    {
        // ユーザーがログインしていることを確認
        if (!Auth::check()) {
            return response()->json(['message' => 'ログインが必要です。'], 401);
        }

        // 既に「いいね！」しているか確認
        if ($question->isLikedByUser(Auth::user())) {
            return response()->json(['message' => '既にいいねしています。'], 409); // Conflict
        }

        // 「いいね！」を追加
        Auth::user()->likes()->create([
            'question_id' => $question->id,
        ]);

        // 更新された「いいね！」数を取得
        $likeCount = $question->likes()->count();

        return response()->json([
            'message' => 'いいねしました！',
            'liked' => true,
            'likes_count' => $likeCount
        ]);
    }

    /**
     * 質問から「いいね！」を削除します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlike(Request $request, Question $question): JsonResponse
    {
        // ユーザーがログインしていることを確認
        if (!Auth::check()) {
            return response()->json(['message' => 'ログインが必要です。'], 401);
        }

        // 「いいね！」しているか確認し、削除
        $like = Auth::user()->likes()->where('question_id', $question->id)->first();

        if (!$like) {
            return response()->json(['message' => 'いいねしていません。'], 409); // Conflict
        }

        $like->delete();

        // 更新された「いいね！」数を取得
        $likeCount = $question->likes()->count();

        return response()->json([
            'message' => 'いいねを取り消しました。',
            'liked' => false,
            'likes_count' => $likeCount
        ]);
    }
}