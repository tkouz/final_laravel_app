<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

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
Route::middleware('auth')->group(function () {
    // 質問投稿フォーム表示のルートを、動的な質問詳細ルートより前に配置
    // これにより、'/questions/create' が '/questions/{question}' として解釈されるのを防ぎます。
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');

    // QuestionControllerに対するリソースルートを定義
    // index, show, create メソッドは上記で定義済みのため除外
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
    
    // ★★★  'profile.image.update' から 'profile.updateImage' に変更 ★★★
    Route::patch('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.image.update');
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage'])->name('profile.image.delete');
    // ベストアンサー選定ルート
    Route::post('/questions/{question}/answers/{answer}/best', [QuestionController::class, 'markAsBestAnswer'])->name('answers.markAsBestAnswer');
});

// 質問詳細ページのルート (未認証ユーザーもアクセス可能) - ★注意: 認証グループの外で、かつquestions/createより後に配置
Route::get('/questions/{question}', [QuestionController::class, 'show'])
     ->name('questions.show');

// 認証関連のルートをインクルード
require __DIR__.'/auth.php';