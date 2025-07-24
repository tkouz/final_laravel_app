<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer; // Answerモデルをuse
use App\Models\User;   // Userモデルをuse
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Storageファサードをuse
use Illuminate\Http\RedirectResponse; // RedirectResponseをuse
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ★追加: AuthorizesRequestsトレイトをuse

class QuestionController extends Controller
{
    use AuthorizesRequests; // ★追加: AuthorizesRequestsトレイトを使用

    /**
     * 質問一覧を表示します。
     */
    public function index(Request $request)
    {
        $query = Question::with('user', 'answers', 'likes')
                         ->where('is_visible', true); // is_visibleがtrueの質問のみ表示

        $searchQuery = $request->input('keyword');
        $statusFilter = $request->input('status');
        $sortBy = $request->input('sort', 'latest'); // ソート順の値を取得 (デフォルトは'latest')

        // キーワード検索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('body', 'like', "%{$keyword}%");
            });
        }

        // 投稿日時フィルター
        if ($request->filled('posted_at')) {
            $postedAt = $request->input('posted_at');
            switch ($postedAt) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->subWeek(), now()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->subMonth(), now()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [now()->subYear(), now()]);
                    break;
            }
        }

        // ステータスフィルター (解決済み/未解決)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'resolved') {
                $query->whereNotNull('best_answer_id');
            } elseif ($status === 'unresolved') {
                $query->whereNull('best_answer_id');
            }
        }

        // ソート順
        if ($sortBy === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sortBy === 'answers_desc') {
            // 回答数でソートするために、answers_countをロード
            $query->withCount('answers')->orderBy('answers_count', 'desc');
        } elseif ($sortBy === 'likes_desc') {
            // いいね数でソートするために、likes_countをロード
            $query->withCount('likes')->orderBy('likes_count', 'desc');
        }

        $questions = $query->paginate(10); // 1ページ10件でページネーション

        return view('questions.index', compact('questions', 'searchQuery', 'statusFilter', 'sortBy'));
    }

    /**
     * 質問投稿フォームを表示します。
     */
    public function create()
    {
        return view('questions.create');
    }

    /**
     * 新しい質問をデータベースに保存します。
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048', // 画像は任意、最大2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('question_images', 'public');
        }

        $question = new Question([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image_path' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        $question->save();

        return redirect()->route('questions.index')->with('success', '質問が投稿されました！');
    }

    /**
     * 指定された質問の詳細を表示します。
     */
    public function show(Question $question)
    {
        // 質問が非表示の場合、404エラーまたはリダイレクト
        if (!$question->is_visible && (!Auth::check() || !Auth::user()->isAdmin())) { // 管理者以外は見れないようにする
            abort(404); // または redirect()->route('questions.index')->with('error', 'この質問は現在表示されていません。');
        }

        // 質問に対する回答と、各回答に紐づくコメントをロード
        $question->load(['answers.user', 'answers.comments.user', 'user', 'likes']);

        // ログインユーザーがいいねしているか、ブックマークしているかを確認
        $isLiked = Auth::check() ? $question->isLikedByUser(Auth::user()) : false;
        $isBookmarked = Auth::check() ? Auth::user()->bookmarks()->where('question_id', $question->id)->exists() : false;

        return view('questions.show', compact('question', 'isLiked', 'isBookmarked'));
    }

    /**
     * 質問編集フォームを表示します。
     */
    public function edit(Question $question)
    {
        // 質問の所有者のみが編集できるようにポリシーを適用
        $this->authorize('update', $question);

        return view('questions.edit', compact('question'));
    }

    /**
     * 質問をデータベースで更新します。
     */
    public function update(Request $request, Question $question): RedirectResponse
    {
        // 質問の所有者のみが更新できるようにポリシーを適用
        $this->authorize('update', $question);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048', // 画像は任意、最大2MB
            'current_image_path' => 'nullable|string', // 現在の画像パス (削除判断用)
        ]);

        // 画像の処理
        if ($request->hasFile('image')) {
            // 新しい画像がアップロードされた場合、古い画像を削除して新しい画像を保存
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $question->image_path = $request->file('image')->store('question_images', 'public');
        } elseif ($request->boolean('remove_image')) { // 画像削除チェックボックスがオンの場合
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
                $question->image_path = null;
            }
        } elseif (!$request->filled('current_image_path') && $question->image_path) {
            // current_image_pathが送信されず、かつ既存の画像パスがある場合（画像がフォームから削除されたと判断）
            Storage::disk('public')->delete($question->image_path);
            $question->image_path = null;
        }


        $question->title = $validated['title'];
        $question->body = $validated['body'];
        $question->save();

        return redirect()->route('questions.show', $question)->with('success', '質問が更新されました！');
    }

    /**
     * 質問をデータベースから削除します。
     */
    public function destroy(Question $question): RedirectResponse
    {
        // 質問の所有者のみが削除できるようにポリシーを適用
        $this->authorize('delete', $question);

        // 関連する画像があれば削除
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('questions.index')->with('success', '質問が削除されました。');
    }

    /**
     * ベストアンサーを選定します。
     */
    public function markAsBestAnswer(Request $request, Question $question, Answer $answer): RedirectResponse
    {
        // 質問の所有者のみがベストアンサーを選べるようにポリシーを適用
        $this->authorize('markAsBestAnswer', $question);

        // 質問がまだ解決済みでないことを確認
        if ($question->best_answer_id !== null) {
            return back()->with('error', 'この質問は既に解決済みです。');
        }

        // 選ばれた回答がこの質問に属していることを確認
        if ($answer->question_id !== $question->id) {
            return back()->with('error', '選ばれた回答はこの質問に属していません。');
        }

        // ベストアンサーを設定
        $question->best_answer_id = $answer->id;
        $question->save();

        return back()->with('success', 'ベストアンサーが選ばれました！');
    }
}
