<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 管理ユーザー向けのルートを追加
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::patch('questions/{question}/toggle-display', [AdminController::class, 'toggleQuestionDisplay'])->name('admin.questions.toggle_display');
        Route::patch('answers/{answer}/toggle-display', [AdminController::class, 'toggleAnswerDisplay'])->name('admin.answers.toggle_display');
    });
});
