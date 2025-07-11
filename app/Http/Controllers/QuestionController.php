<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // ★DBファサードを追加

class QuestionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ソート順の初期化と取得
        $sortBy = $request->query('sort_by', 'latest'); // デフォルトは 'latest'
        // ステータスフィルターの初期化と取得
        $statusFilter = $request->query('status_filter', 'all'); // デフォルトは 'all'
        // 検索クエリの取得
        $searchQuery = $request->query('search');
        // ★追加: 日付フィルターの取得
        $dateFilter = $request->query('date_filter');

        // with() でロードするリレーションは最初に定義
        // likes リレーションは後で結合するので、ここでは with('likes') は不要になる場合があります
        // ただし、likes_count を表示するために withCount('likes') を使用する方がシンプルです
        // 以下の修正では withCount('likes') を使います
        $questions = Question::with(['user', 'answers.user', 'bookmarkedByUsers']); // likes は withCount で取得するのでリストから除外

        // 検索ロジック
        if ($searchQuery) {
            $questions->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%')
                      ->orWhere('body', 'like', '%' . $searchQuery . '%');
            });
        }

        // ステータスフィルターロジック
        if ($statusFilter === 'open') {
            $questions->where('is_resolved', false);
        } elseif ($statusFilter === 'resolved') {
            $questions->where('is_resolved', true);
        }

        // ★ここから追加: 日付フィルターロジック
        if ($dateFilter) {
            // 指定された日付以降の質問をフィルタリング
            $questions->whereDate('created_at', '>=', $dateFilter);
        }
        // ★ここまで追加

        // ソートロジック
        // ★ここから「いいねが多い順」のソートロジックを追加
        if ($sortBy === 'popular') {
            // likes_count カラムを追加し、そのカウントで並び替える
            $questions->withCount('likes')->orderByDesc('likes_count');
        } elseif ($sortBy === 'oldest') {
            $questions->oldest();
        } elseif ($sortBy === 'most_answers') {
            $questions->withCount('answers')->orderByDesc('answers_count');
        } else { // 'latest' (デフォルト)
            $questions->latest();
        }
        // ★ここまで「いいねが多い順」のソートロジックを追加


        $questions = $questions->paginate(10);

        // 各質問に対して、ログインユーザーがいいねしているか、ブックマークしているかを判定
        // withCount('likes')を使っているので、ここではlikesリレーションは必要ありませんが
        // いいねアイコンの表示のために is_liked_by_user は必要です
        foreach ($questions as $question) {
            $question->is_liked_by_user = Auth::check() ? $question->isLikedByUser(Auth::user()) : false;
            $question->is_bookmarked_by_user = Auth::check() ? $question->isBookmarkedByUser(Auth::user()) : false;
        }

        // $sortBy と $statusFilter, $searchQuery, $dateFilter 変数をビューに渡す
        return view('questions.index', compact('questions', 'sortBy', 'statusFilter', 'searchQuery', 'dateFilter')); // ★修正: $dateFilterを追加
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('questions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'required|image|max:2048', // 画像は必須、最大2MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // publicディスクの'questions'ディレクトリに画像を保存
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        Auth::user()->questions()->create([
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
            'image_path' => $imagePath, // 画像パスを保存
        ]);

        return redirect()->route('questions.index')->with('success', '質問が投稿されました！');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $question->load(['user', 'answers.user', 'answers.comments.user', 'likes', 'bookmarkedByUsers']);
        $isBookmarked = Auth::check() ? $question->bookmarkedByUsers()->where('user_id', Auth::id())->exists() : false;
        return view('questions.show', compact('question', 'isBookmarked'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $this->authorize('update', $question); // ポリシーによる認可
        return view('questions.edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $this->authorize('update', $question); // ポリシーによる認可

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|image|max:2048', // 画像は任意、最大2MB
            'remove_image' => 'boolean', // 画像削除のチェックボックス用
        ]);

        $dataToUpdate = [
            'title' => $validatedData['title'],
            'body' => $validatedData['body'],
        ];

        // 画像の処理
        if ($request->hasFile('image')) {
            // 古い画像があれば削除
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            // 新しい画像を保存
            $dataToUpdate['image_path'] = $request->file('image')->store('questions', 'public');
        } elseif (isset($validatedData['remove_image']) && $validatedData['remove_image']) {
            // remove_imageがチェックされていて、既存の画像パスがある場合
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $dataToUpdate['image_path'] = null; // データベースからパスを削除
        }
        // 画像が送信されず、削除チェックもされていない場合は、既存のパスを維持

        $question->update($dataToUpdate);

        return redirect()->route('questions.show', $question)->with('success', '質問が更新されました！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $this->authorize('delete', $question); // ポリシーによる認可

        // 関連する画像があれば削除
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();
        return redirect()->route('questions.index')->with('success', '質問が削除されました！');
    }

    /**
     * Mark an answer as the best answer.
     */
    public function markAsBestAnswer(Request $request, Question $question, Answer $answer)
    {
        // 質問の所有者のみがベストアンサーを選べるようにする
        if (Auth::id() !== $question->user_id) {
            return back()->with('error', 'この質問のベストアンサーを選ぶ権限がありません。');
        }

        // ベストアンサーIDを更新
        $question->best_answer_id = $answer->id;
        $question->is_resolved = true;
        $question->save();

        return back()->with('success', 'ベストアンサーが選ばれました！');
    }
}