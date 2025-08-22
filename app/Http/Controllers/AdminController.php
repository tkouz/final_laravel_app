<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * 質問の表示状態を切り替える
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleQuestionDisplay(Question $question): JsonResponse
    {
        $question->is_hidden = !$question->is_hidden;
        $question->save();

        return response()->json([
            'status' => 'success',
            'is_hidden' => $question->is_hidden,
            'message' => $question->is_hidden ? '質問を非表示にしました' : '質問を表示しました'
        ]);
    }

    /**
     * 回答の表示状態を切り替える
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAnswerDisplay(Answer $answer): JsonResponse
    {
        $answer->is_hidden = !$answer->is_hidden;
        $answer->save();

        return response()->json([
            'status' => 'success',
            'is_hidden' => $answer->is_hidden,
            'message' => $answer->is_hidden ? '回答を非表示にしました' : '回答を表示しました'
        ]);
    }
}
