<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 違反報告の一覧を表示します。
     * 報告数の多い順にソートし、各報告の合計報告数も取得します。
     */
    public function index(Request $request)
    {
        // 違反報告のクエリを開始
        $reports = Report::query();

        // 各reportable_idとreportable_typeの組み合わせに対する報告数をサブクエリで取得
        $reports = $reports->select('reports.*')
                           ->addSelect(DB::raw('COUNT(t2.id) as total_report_count'))
                           ->leftJoin('reports as t2', function ($join) {
                               $join->on('reports.reportable_id', '=', 't2.reportable_id')
                                    ->on('reports.reportable_type', '=', 't2.reportable_type');
                           })
                           ->groupBy('reports.id')
                           ->with(['user', 'reportable'])
                           ->orderByDesc('total_report_count')
                           ->orderByDesc('reports.created_at')
                           ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * 指定された違反報告の詳細を表示します。
     */
    public function show(Report $report)
    {
        $report->load(['user', 'reportable']);

        // 報告対象の合計違反報告数を取得
        $totalReportCount = 0;
        if ($report->reportable) {
            $totalReportCount = Report::where('reportable_id', $report->reportable_id)
                                     ->where('reportable_type', $report->reportable_type)
                                     ->count();
        }

        return view('admin.reports.show', compact('report', 'totalReportCount'));
    }

    /**
     * 違反報告をデータベースから削除します。
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success', '違反報告を削除しました。');
    }
    
    /**
     * 非表示の質問一覧を表示します。
     */
    public function suspendedQuestions()
    {
        $suspendedQuestions = Question::where('is_hidden', 1)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.suspended-posts.questions', compact('suspendedQuestions'));
    }

    /**
     * 指定された質問を非表示にします。
     */
    public function hideQuestion(Request $request, Question $question)
    {
        // updateメソッドを使用して非表示状態を更新
        $question->update(['is_hidden' => true]);
        return back()->with('success', '質問を非表示にしました。');
    }

    /**
     * 指定された質問を表示状態に戻します。
     */
    public function unhideQuestion(Request $request, Question $question)
    {
        // updateメソッドを使用して表示状態を更新
        $question->update(['is_hidden' => false]);
        return back()->with('success', '質問を表示状態に戻しました。');
    }
    
    /**
     * 非表示の回答一覧を表示します。
     */
    public function suspendedAnswers()
    {
        $suspendedAnswers = Answer::where('is_hidden', 1)
            ->with(['user', 'question'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.suspended-posts.answers', compact('suspendedAnswers'));
    }

    /**
     * 指定された回答を非表示にします。
     */
    public function hideAnswer(Request $request, Answer $answer)
    {
        // updateメソッドを使用して非表示状態を更新
        $answer->update(['is_hidden' => true]);
        return back()->with('success', '回答を非表示にしました。');
    }

    /**
     * 指定された回答を表示状態に戻します。
     */
    public function unhideAnswer(Request $request, Answer $answer)
    {
        // updateメソッドを使用して表示状態を更新
        $answer->update(['is_hidden' => false]);
        return back()->with('success', '回答を表示状態に戻しました。');
    }

    /**
     * 質問または回答の表示状態を切り替えます。
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Question|\App\Models\Answer $reportable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleVisibility(Request $request, $reportableType, $reportableId)
    {
        $model = null;
        if ($reportableType === 'question') {
            $model = Question::find($reportableId);
        } elseif ($reportableType === 'answer') {
            $model = Answer::find($reportableId);
        }

        if ($model) {
            $model->update(['is_hidden' => !$model->is_hidden]);
            return back()->with('success', '投稿の表示状態を切り替えました。');
        }

        return back()->with('error', '投稿が見つかりませんでした。');
    }

    /**
     * ユーザーの利用状態を切り替えます。（このメソッドは実際には使われません）
     */
    // public function toggleUserActive(User $user)
    // {
    //     if ($user->isAdmin()) {
    //         return back()->with('error', '管理者アカウントの利用状態は変更できません。');
    //     }
    //     $user->is_active = !$user->is_active;
    //     $user->save();
    //     return back()->with('success', 'ユーザーの利用状態を切り替えました。');
    // }
}
