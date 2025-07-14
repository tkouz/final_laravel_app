<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage; // ★追加: Storageファサードをインポート

class AnswerController extends Controller
{
    use AuthorizesRequests;

    /**
     * 指定された質問に対して新しい回答をデータベースに保存します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question  回答を投稿する対象の質問モデルのインスタンス
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Question $question): RedirectResponse
    {
        // リクエストデータのバリデーション
        $validatedData = $request->validate([
            'content' => 'required|string', // 回答内容は必須、文字列
            'image' => 'nullable|image|max:2048', // ★追加: 画像は任意、最大2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // publicディスクの'answers'ディレクトリに画像を保存
            $imagePath = $request->file('image')->store('answers', 'public');
        }

        // 新しい回答をデータベースに作成
        $answer = new Answer([
            'user_id' => auth()->id(),      // 認証済みユーザーのIDを回答者とする
            'question_id' => $question->id, // 回答が属する質問のID
            'content' => $validatedData['content'], // リクエストから回答内容を取得
            'image_path' => $imagePath, // 画像パスを保存
            'is_best_answer' => false,      // デフォルトではベストアンサーではない
        ]);

        // 回答を保存
        $answer->save();

        // 質問の詳細ページへリダイレクトし、成功メッセージをセッションにフラッシュ
        return redirect()->route('questions.show', $question)->with('status', '回答が投稿されました！');
    }

    /**
     * 回答の編集フォームを表示します。
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\View\View
     */
    public function edit(Answer $answer): \Illuminate\View\View
    {
        $this->authorize('update', $answer); // ポリシーによる認可
        return view('answers.edit', compact('answer'));
    }

    /**
     * 指定された回答をデータベースで更新します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Answer $answer): RedirectResponse
    {
        $this->authorize('update', $answer); // ポリシーによる認可

        $validatedData = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048', // ★修正: 画像は任意、最大2MB
            'remove_image' => 'boolean', // 画像削除のチェックボックス用
        ]);

        $dataToUpdate = [
            'content' => $validatedData['content'],
        ];

        // 画像の処理
        if ($request->hasFile('image')) {
            // 古い画像があれば削除
            if ($answer->image_path) {
                Storage::disk('public')->delete($answer->image_path);
            }
            // 新しい画像を保存
            $dataToUpdate['image_path'] = $request->file('image')->store('answers', 'public');
        } elseif (isset($validatedData['remove_image']) && $validatedData['remove_image']) {
            // remove_imageがチェックされていて、既存の画像パスがある場合
            if ($answer->image_path) {
                Storage::disk('public')->delete($answer->image_path);
            }
            $dataToUpdate['image_path'] = null; // データベースからパスを削除
        }
        // 画像が送信されず、削除チェックもされていない場合は、既存のパスを維持

        $answer->update($dataToUpdate);

        // 回答が属する質問の詳細ページへリダイレクト
        return redirect()->route('questions.show', $answer->question)->with('success', '回答が更新されました！');
    }

    /**
     * 指定された回答をデータベースから削除します。
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Answer $answer): RedirectResponse
    {
        $this->authorize('delete', $answer); // ポリシーによる認可

        // 関連する画像があれば削除
        if ($answer->image_path) {
            Storage::disk('public')->delete($answer->image_path);
        }

        $answer->delete();

        // 質問詳細ページに戻る
        return redirect()->route('questions.show', $answer->question)->with('success', '回答が削除されました！');
    }
}