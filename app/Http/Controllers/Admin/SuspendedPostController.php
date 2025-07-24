<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question; // Questionモデルをuse
use App\Models\Answer;   // Answerモデルをuse

class SuspendedPostController extends Controller
{
    /**
     * 非表示の質問一覧を表示します。
     *
     * @return \Illuminate\View\View
     */
    public function indexQuestions()
    {
        // is_visibleがfalseの質問のみを取得し、ページネーション
        $suspendedQuestions = Question::with('user')
                                      ->where('is_visible', false)
                                      ->orderBy('updated_at', 'desc') // 最新の更新順に表示
                                      ->paginate(10);

        return view('admin.suspended-posts.questions', compact('suspendedQuestions'));
    }

    /**
     * 非表示の回答一覧を表示します。
     *
     * @return \Illuminate\View\View
     */
    public function indexAnswers()
    {
        // is_visibleがfalseの回答のみを取得し、ページネーション
        // 回答がどの質問に属しているか分かるように質問もロード
        $suspendedAnswers = Answer::with('user', 'question')
                                  ->where('is_visible', false)
                                  ->orderBy('updated_at', 'desc') // 最新の更新順に表示
                                  ->paginate(10);

        return view('admin.suspended-posts.answers', compact('suspendedAnswers'));
    }
}
