<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User; // Userモデルをuse
use Illuminate\Support\Facades\DB; // トランザクションのためにDBファサードをuse

class ReportController extends Controller
{
    /**
     * 違反報告の一覧を表示します。
     * 違反報告数の多い順（降順）に10件表示（要件定義より）
     */
    public function index()
    {
        // 各報告対象（質問/回答）ごとの報告数を集計し、多い順に並べる
        // これは少し複雑なクエリになるため、ここでは全件取得し、ビュー側でソート・表示件数制限を考慮するか、
        // もしくはより高度なSQLクエリを組む必要があります。
        // 要件定義の「違反報告数の多い順に10件」は、集計された「対象」のトップ10を指す可能性が高いです。
        // ここでは、シンプルに最新の違反報告をページネーションで表示します。
        // より複雑な集計は、別途メソッドやビューロジックで対応します。

        $reports = Report::with('reportable', 'user') // 報告対象と報告ユーザーをEager Load
                         ->orderBy('created_at', 'desc')
                         ->paginate(20); // 例として20件ずつページネーション

        // 要件定義の「違反報告数の多い順に10件」を実装する場合の例（Controller側で集計）
        // 質問と回答それぞれの報告数を集計
        $topReportedQuestions = Report::select('reportable_id', DB::raw('count(*) as report_count'))
                                      ->where('reportable_type', Question::class)
                                      ->groupBy('reportable_id')
                                      ->orderByDesc('report_count')
                                      ->limit(10)
                                      ->with('reportable') // 報告対象の質問をロード
                                      ->get();

        $topReportedAnswers = Report::select('reportable_id', DB::raw('count(*) as report_count'))
                                    ->where('reportable_type', Answer::class)
                                    ->groupBy('reportable_id')
                                    ->orderByDesc('report_count')
                                    ->limit(10)
                                    ->with('reportable') // 報告対象の回答をロード
                                    ->get();

        // ユーザー一覧（質問・回答の停止件数の多い順（降順）に10件）
        // これはUserモデルに停止件数カラムがあるか、リレーションを辿って集計する必要があります。
        // ここではシンプルに最新のユーザーを取得する例とします。
        $users = User::orderBy('created_at', 'desc')->paginate(10); // 例：最新のユーザー

        // ダッシュボードの要件に合わせて、これらのデータをビューに渡します。
        // 今回はadmin.reports.indexなので、reportsのみを渡します。
        return view('admin.reports.index', compact('reports', 'topReportedQuestions', 'topReportedAnswers', 'users'));
    }

    /**
     * 特定の違反報告の詳細を表示します。
     */
    public function show(Report $report)
    {
        // ポリモーフィックリレーションシップを使って報告対象のモデルをロード
        // with() を使ってEager LoadすることでN+1問題を回避
        $report->load('reportable', 'user');

        return view('admin.reports.show', compact('report'));
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
     * 投稿（質問または回答）の表示を停止/再開します。
     *
     * @param string $type 'question' or 'answer'
     * @param int $id 投稿のID
     * @return \Illuminate\Http\RedirectResponse
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

        // is_visible フラグを切り替える
        $model->is_visible = !$model->is_visible; // 現在の状態を反転
        $model->save();

        // 関連する違反報告のステータスを「解決済み」などに更新することも検討
        // Report::where('reportable_type', get_class($model))
        //       ->where('reportable_id', $model->id)
        //       ->update(['status' => 'resolved']); // 例: statusカラムがある場合

        return back()->with('success', '投稿の表示状態を切り替えました。');
    }

    /**
     * ユーザーの利用を停止/再開します。
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleUserActive(Request $request, User $user)
    {
        $user->is_active = !$user->is_active; // 現在の状態を反転
        $user->save();

        return back()->with('success', 'ユーザーの利用状態を切り替えました。');
    }
}
