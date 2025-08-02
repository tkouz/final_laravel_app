<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Question;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     * 質問の投稿者のみが質問を更新できる
     */
    public function update(User $user, Question $question): bool
    {
        // ログインユーザーのIDと質問を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * 質問の投稿者のみが質問を削除できる
     */
    public function delete(User $user, Question $question): bool
    {
        // ログインユーザーのIDと質問を投稿したユーザーのIDが一致するかをチェック
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can mark a best answer for the question.
     * 質問の投稿者のみがベストアンサーを選定できる
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function markAsBestAnswer(User $user, Question $question)
    {
        // 質問の投稿者IDと、現在ログインしているユーザーのIDが一致するかをチェック
        return $user->id === $question->user_id
            ? Response::allow()
            : Response::deny('この質問のベストアンサーを選定する権限がありません。');
    }
}
