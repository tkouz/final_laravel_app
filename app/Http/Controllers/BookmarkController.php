<?php

namespace App\Http\Controllers;

use App\Models\Question;
// use App\Models\Bookmark; // ★削除: Bookmarkモデルは不要になるため削除
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BookmarkController extends Controller
{
    /**
     * 質問をブックマークします。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Question $question): JsonResponse
    {
        // ユーザーがログインしていることを確認
        if (!Auth::check()) {
            return response()->json(['message' => 'ログインが必要です。'], 401);
        }

        $user = Auth::user();

        // 既にブックマークしているか確認し、していなければ追加
        if ($user->bookmarks()->where('question_id', $question->id)->exists()) {
            return response()->json(['message' => '既にブックマークしています。'], 409); // Conflict
        }

        // ★修正: attach() を使用してブックマークを追加
        $user->bookmarks()->attach($question->id);

        return response()->json([
            'message' => 'ブックマークしました！',
            'bookmarked' => true
        ]);
    }

    /**
     * 質問のブックマークを解除します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Question $question): JsonResponse
    {
        // ユーザーがログインしていることを確認
        if (!Auth::check()) {
            return response()->json(['message' => 'ログインが必要です。'], 401);
        }

        $user = Auth::user();

        // ブックマークしているか確認し、していれば削除
        if (!$user->bookmarks()->where('question_id', $question->id)->exists()) {
            return response()->json(['message' => 'ブックマークしていません。'], 409); // Conflict
        }

        // ★修正: detach() を使用してブックマークを削除
        $user->bookmarks()->detach($question->id);

        return response()->json([
            'message' => 'ブックマークを解除しました。',
            'bookmarked' => false
        ]);
    }
}