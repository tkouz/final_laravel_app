<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User; // Userモデルをuse
use Illuminate\Support\Facades\DB; // DBファサードをuse

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
                           ->addSelect(DB::raw('COUNT(t2.id) as total_report_count')) // t2はサブクエリのエイリアス
                           ->leftJoin('reports as t2', function ($join) {
                               $join->on('reports.reportable_id', '=', 't2.reportable_id')
                                    ->on('reports.reportable_type', '=', 't2.reportable_type');
                           })
                           ->groupBy('reports.id') // 各報告はユニークなのでreports.idでグループ化
                           ->with(['user', 'reportable']) // 報告者と報告対象をロード
                           ->orderByDesc('total_report_count') // 合計報告数の降順でソート
                           ->orderByDesc('reports.created_at') // 合計報告数が同じ場合は作成日時の新しい順
                           ->paginate(10); // ページネーション

        return view('admin.reports.index', compact('reports'));
    }


    /**
     * 指定された違反報告の詳細を表示します。
     */
    public function show(Report $report)
    {
        $report->load(['user', 'reportable']); // 報告者と報告対象をロード

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
     * 質問または回答の表示状態を切り替えます。
     */
    public function toggleVisibility(Request $request, string $type, int $id)
    {
        $model = null;
        if ($type === 'question') {
            $model = Question::find($id);
        } elseif ($type === 'answer') {
            $model = Answer::find($id);
        }

        if (!$model) {
            return back()->with('error', '対象の投稿が見つかりませんでした。');
        }

        $model->is_visible = !$model->is_visible;
        $model->save();

        return back()->with('success', '投稿の表示状態を切り替えました。');
    }

    /**
     * ユーザーの利用状態を切り替えます。
     * Admin/UserController に移動したので、ここでは不要ですが、
     * 念のため残しておきます（実際には使われません）。
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
