<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Middleware\PreventAdminActions;
use App\Http\Controllers\Admin\SuspendedPostController;
use App\Http\Controllers\Admin\AnswerController as AdminAnswerController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// トップページへのアクセスを質問一覧にリダイレクト
Route::get('/', function () {
    return redirect()->route('questions.index');
});

// 質問一覧ページのルート (未認証ユーザーもアクセス可能)
Route::get('/questions', [QuestionController::class, 'index'])
    ->name('questions.index');

// 認証済みユーザーのみがアクセスできるルートグループ
Route::middleware(['auth', PreventAdminActions::class])->group(function () {
    // 質問投稿フォーム表示のルートを、動的な質問詳細ルートより前に配置
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');

    // QuestionControllerに対するリソースルートを定義
    Route::resource('questions', QuestionController::class)->except(['index', 'show', 'create']);

    // 回答投稿に関するルート
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->name('answers.store');

    // AnswerControllerに対するリソースルートを定義（編集・削除は無効化済み）
    Route::resource('answers', AnswerController::class)->except(['index', 'show', 'create', 'store']);

    // ブックマークに関するルート
    Route::post('/questions/{question}/bookmark', [BookmarkController::class, 'store'])->name('bookmark.store');
    Route::delete('/questions/{question}/bookmark', [BookmarkController::class, 'destroy'])->name('bookmark.destroy');

    // コメント投稿に関するルート (POSTのみ)
    Route::post('/answers/{answer}/comments', [CommentController::class, 'store'])->name('comments.store');

    // 「いいね！」機能のWebルート
    Route::post('/questions/{question}/like', [LikeController::class, 'like'])->name('questions.like');
    Route::delete('/questions/{question}/unlike', [LikeController::class, 'unlike'])->name('questions.unlike');

    // プロフィール関連 (Laravel Breezeの標準ルートにあなたのカスタム画像ルートを追加)
    // Breezeが生成する標準のprofileルート
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // あなたのカスタム画像ルート (ProfileControllerのメソッドに合わせる)
    Route::patch('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    
    // ベストアンサー選定ルート
    Route::post('/questions/{question}/answers/{answer}/best', [QuestionController::class, 'markAsBestAnswer'])->name('answers.markAsBestAnswer');

    // 違反報告のルート
    Route::post('/report', [ReportController::class, 'store'])->name('reports.store');
});

// 管理者専用のルートグループ
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 例: 管理者ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 違反報告管理に関するルート
    Route::resource('reports', AdminReportController::class)->only(['index', 'show', 'destroy']);
    
    // 質問と回答の表示/非表示を切り替えるルート
    // reportableTypeとreportableIdをURLパラメータとして渡すように変更
    Route::put('/reports/{reportableType}/{reportableId}/toggle-visibility', [AdminReportController::class, 'toggleVisibility'])->name('reports.toggle-visibility');

    // ユーザー管理に関するルート
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index'); // ユーザー一覧
    Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggleActive'); // ユーザーの利用停止/再開

    // 非表示投稿管理ルート
    Route::get('/suspended-questions', [SuspendedPostController::class, 'indexQuestions'])->name('suspended-questions.index');
    Route::get('/suspended-answers', [SuspendedPostController::class, 'indexAnswers'])->name('suspended-answers.index');
});


// 質問詳細ページのルート (未認証ユーザーもアクセス可能)
Route::get('/questions/{question}', [QuestionController::class, 'show'])
    ->name('questions.show');

// 認証関連のルートをインクルード
require __DIR__.'/auth.php';
