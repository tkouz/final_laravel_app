<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer; // Answerモデルをuse
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Storageファサードをuse
use Illuminate\Http\RedirectResponse; // RedirectResponseをuse
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // AuthorizesRequestsトレイトをuse
use Carbon\Carbon;

class QuestionController extends Controller
{
    use AuthorizesRequests; // AuthorizesRequestsトレイトを使用

    /**
     * 質問一覧を表示します。
     */
    public function index(Request $request)
    {
        // Questionモデルのクエリを開始
        $query = Question::with('user', 'answers', 'likes')
            ->where('is_visible', true); // is_visibleがtrueの質問のみ表示

        $searchQuery = $request->input('keyword');
        $statusFilter = $request->input('status');
        $dateFilter = $request->input('date_filter'); // 日付フィルターの値を取得
        $sortBy = $request->input('sort', 'latest'); // ソート順の値を取得 (デフォルトは'latest')

        // キーワード検索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('body', 'like', "%{$keyword}%");
            });
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

        // === 修正点: 日付フィルターを追加 ===
        // もし 'date_filter' が存在すれば、その日付以降でcreated_atを絞り込む
        if ($dateFilter) {
            $query->whereDate('created_at', '>=', $dateFilter);
        }

        // ソート順
        if ($sortBy === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sortBy === 'answers_desc') {
            $query->withCount('answers')->orderBy('answers_count', 'desc');
        } elseif ($sortBy === 'likes_desc') {
            $query->withCount('likes')->orderBy('likes_count', 'desc');
        }

        $questions = $query->paginate(10);

        // Bladeに渡す変数名もコントローラーが受け取ったものに合わせる
        return view('questions.index', compact('questions', 'searchQuery', 'statusFilter', 'sortBy', 'dateFilter'));
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
            'image' => 'nullable|image|max:2048',
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
            'is_resolved' => false,
            'is_visible' => true,
        ]);

        $question->save();

        return redirect()->route('questions.index')->with('success', '質問が投稿されました！');
    }

    /**
     * 指定された質問の詳細を表示します。
     */
    public function show(Question $question)
    {
        if (!$question->is_visible && (!Auth::check() || !Auth::user()->isAdmin())) {
            abort(404);
        }

        $question->load(['answers.user', 'answers.comments.user', 'user', 'likes']);

        $isLiked = Auth::check() ? $question->isLikedByUser(Auth::user()) : false;
        $isBookmarked = Auth::check() ? Auth::user()->bookmarks()->where('question_id', $question->id)->exists() : false;

        return view('questions.show', compact('question', 'isLiked', 'isBookmarked'));
    }

    /**
     * 質問編集フォームを表示します。
     */
    public function edit(Question $question)
    {
        $this->authorize('update', $question);

        return view('questions.edit', compact('question'));
    }

    /**
     * 質問をデータベースで更新します。
     */
    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->authorize('update', $question);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'current_image_path' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $question->image_path = $request->file('image')->store('question_images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
                $question->image_path = null;
            }
        } elseif (!$request->filled('current_image_path') && $question->image_path) {
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
        $this->authorize('delete', $question);

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
        $this->authorize('markAsBestAnswer', $question);

        if ($question->best_answer_id !== null) {
            return back()->with('error', 'この質問は既に解決済みです。');
        }

        if ($answer->question_id !== $question->id) {
            return back()->with('error', '選ばれた回答はこの質問に属していません。');
        }

        $question->update([
            'best_answer_id' => $answer->id,
            'is_resolved' => true,
        ]);

        return back()->with('success', 'ベストアンサーが選ばれました！');
    }
}
