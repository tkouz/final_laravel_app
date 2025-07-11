<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Question; // Questionモデルをuse
use App\Models\Answer;   // Answerモデルをuse
use App\Models\Report;   // Reportモデルをuse
use Illuminate\Support\Facades\Auth; // Authファサードをuse

class ReportController extends Controller
{
    /**
     * 新しい違反報告を保存します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // バリデーションルール
        $request->validate([
            'reportable_type' => ['required', 'string', 'in:question,answer'], // 'question'または'answer'のみを許可
            'reportable_id'   => ['required', 'integer'],
            'reason'          => ['required', 'string', 'max:255'],
            'comment'         => ['nullable', 'string', 'max:1000'],
        ]);

        // 報告対象のモデルを特定
        $model = null;
        if ($request->input('reportable_type') === 'question') {
            $model = Question::find($request->input('reportable_id'));
        } elseif ($request->input('reportable_type') === 'answer') {
            $model = Answer::find($request->input('reportable_id'));
        }

        // 対象が見つからない場合はエラー
        if (!$model) {
            return back()->withErrors(['report_error' => '報告対象が見つかりませんでした。']);
        }

        // 既に同じユーザーが同じ対象に報告しているかチェック (二重報告防止)
        $existingReport = Report::where('user_id', Auth::id())
                                ->where('reportable_id', $request->input('reportable_id'))
                                ->where('reportable_type', 'App\\Models\\' . ucfirst($request->input('reportable_type')))
                                ->first();

        if ($existingReport) {
            return back()->withErrors(['report_error' => 'この投稿は既に報告済みです。']);
        }

        // 違反報告をデータベースに保存
        $report = new Report();
        $report->user_id = Auth::id();
        $report->reportable_type = get_class($model); // モデルのフルパスを保存 (例: App\Models\Question)
        $report->reportable_id = $model->id;
        $report->reason = $request->input('reason');
        $report->comment = $request->input('comment');
        $report->status = 'pending'; // デフォルトステータス
        $report->save();

        return back()->with('status', 'report-submitted')->with('success_message', '違反報告が送信されました。');
    }
}