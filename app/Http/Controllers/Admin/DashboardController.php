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
        // まず、表示可能な質問に対する違反報告の数を集計
        $topReportedQuestionsData = Report::select('reports.reportable_id', DB::raw('count(reports.id) as report_count'))
                                      ->join('questions', function ($join) {
                                          $join->on('reports.reportable_id', '=', 'questions.id')
                                               ->where('reports.reportable_type', Question::class)
                                               ->where('questions.is_visible', true); // is_visible が true の質問のみ
                                      })
                                      ->groupBy('reports.reportable_id')
                                      ->orderByDesc('report_count')
                                      ->limit(10)
                                      ->get();

        // 取得したreportable_idを使って、実際のQuestionモデルをロード
        $questionIds = $topReportedQuestionsData->pluck('reportable_id');
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id'); // IDをキーにして取得

        // 報告数と結合して、ビューに渡す最終的なコレクションを作成
        $topReportedQuestions = $topReportedQuestionsData->map(function ($item) use ($questions) {
            $question = $questions->get($item->reportable_id);
            if ($question) {
                // reportable プロパティに実際のモデルを設定
                $item->reportable = $question;
            } else {
                // 万が一見つからなかった場合（通常は発生しないはず）
                $item->reportable = null;
            }
            return $item;
        });


        // 2. 回答一覧（違反報告数の多い順（降順）に10件）
        // まず、表示可能な回答に対する違反報告の数を集計
        $topReportedAnswersData = Report::select('reports.reportable_id', DB::raw('count(reports.id) as report_count'))
                                    ->join('answers', function ($join) {
                                        $join->on('reports.reportable_id', '=', 'answers.id')
                                             ->where('reports.reportable_type', Answer::class)
                                             ->where('answers.is_visible', true); // is_visible が true の回答のみ
                                    })
                                    ->groupBy('reports.reportable_id')
                                    ->orderByDesc('report_count')
                                    ->limit(10)
                                    ->get();

        // 取得したreportable_idを使って、実際のAnswerモデルをロード
        $answerIds = $topReportedAnswersData->pluck('reportable_id');
        $answers = Answer::whereIn('id', $answerIds)->with('question')->get()->keyBy('id'); // 質問へのリンクのためにquestionリレーションもロード

        // 報告数と結合して、ビューに渡す最終的なコレクションを作成
        $topReportedAnswers = $topReportedAnswersData->map(function ($item) use ($answers) {
            $answer = $answers->get($item->reportable_id);
            if ($answer) {
                // reportable プロパティに実際のモデルを設定
                $item->reportable = $answer;
            } else {
                // 万が一見つからなかった場合
                $item->reportable = null;
            }
            return $item;
        });


        // 3. ユーザー一覧（質問・回答の停止件数の多い順（降順）に10件）
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
