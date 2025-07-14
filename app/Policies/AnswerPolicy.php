<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Answer; // Answerモデルをuse
use Illuminate\Auth\Access\Response; // Responseクラスをuse

class AnswerPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Answer $answer): bool
    {
        // ログインユーザーのIDと回答を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $answer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Answer $answer): bool
    {
        // ログインユーザーのIDと回答を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $answer->user_id;
    }

    // 必要に応じて、view, create, restore, forceDelete などのメソッドも記述
    // 現状はupdateとdeleteのみ
}