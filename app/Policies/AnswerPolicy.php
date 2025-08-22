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

    /**
     * 管理者が回答を非表示にできるかを判定します。
     */
    public function hide(User $user, Answer $answer): bool
    {
        // ユーザーが管理者かどうかをチェック
        return $user->isAdmin();
    }

    /**
     * 管理者が非表示にした回答を再表示できるかを判定します。
     */
    public function unhide(User $user, Answer $answer): bool
    {
        // ユーザーが管理者かどうかをチェック
        return $user->isAdmin();
    }
}
