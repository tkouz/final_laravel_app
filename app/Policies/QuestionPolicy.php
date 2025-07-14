<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Question; // Questionモデルをuse
use Illuminate\Auth\Access\Response; // Responseクラスをuse

class QuestionPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        // ログインユーザーのIDと質問を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        // ログインユーザーのIDと質問を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $question->user_id;
    }

    // 必要に応じて、view, create, restore, forceDelete などのメソッドも記述
    // 現状はupdateとdeleteのみ
}