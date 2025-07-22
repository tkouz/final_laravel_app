<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question; // Questionモデルをuse
use App\Models\Answer;   // Answerモデルをuse
use App\Models\User;     // Userモデルをuse
use App\Models\Report;   // Reportモデルをuse
use Illuminate\Support\Facades\DB; // DBファサードをuse (集計クエリ用)

class DashboardController extends Controller
{
    public function index()
    {
        // 1. 質問一覧（違反報告数の多い順（降順）に10件）
        // Reportable_typeがQuestion::classであるReportをカウントし、Question IDでグループ化
        $topReportedQuestions = Report::select('reportable_id', DB::raw('count(*) as report_count'))
                                      ->where('reportable_type', Question::class)
                                      ->groupBy('reportable_id')
                                      ->orderByDesc('report_count')
                                      ->limit(10)
                                      ->with('reportable') // 報告対象の質問をロード
                                      ->get();

        // 2. 回答一覧（違反報告数の多い順（降順）に10件）
        // Reportable_typeがAnswer::classであるReportをカウントし、Answer IDでグループ化
        $topReportedAnswers = Report::select('reportable_id', DB::raw('count(*) as report_count'))
                                    ->where('reportable_type', Answer::class)
                                    ->groupBy('reportable_id')
                                    ->orderByDesc('report_count')
                                    ->limit(10)
                                    ->with('reportable') // 報告対象の回答をロード
                                    ->get();

        // 3. ユーザー一覧（質問・回答の停止件数の多い順（降順）に10件）
        // これは少し複雑な集計になります。
        // 各ユーザーが投稿した質問と回答のうち、is_visible=false の件数を集計します。
        $topSuspendedUsers = User::withCount(['questions' => function ($query) {
                                        $query->where('is_visible', false);
                                    }])
                                    ->withCount(['answers' => function ($query) {
                                        $query->where('is_visible', false);
                                    }])
                                    ->get()
                                    ->sortByDesc(function ($user) {
                                        return $user->questions_count + $user->answers_count;
                                    })
                                    ->take(10);


        return view('admin.dashboard', compact(
            'topReportedQuestions',
            'topReportedAnswers',
            'topSuspendedUsers'
        ));
    }
}
