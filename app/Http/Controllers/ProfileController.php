<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage; // ★追加: Storageファサード
use App\Models\Question; // ★追加: Questionモデル
use App\Models\Answer; // ★追加: Answerモデル
use App\Models\Comment; // ★追加: Commentモデル
use App\Models\User; // ★追加: Userモデル (明示的に)

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
         $user = $request->user(); // ★変更：$request->user() を利用

        // ↓↓↓ ここからユーザーが投稿した質問、回答、コメントを取得するロジックを追記 ↓↓↓
        $userQuestions = $user->questions()->latest()->get();
        $userAnswers = $user->answers()->latest()->get();
        $userComments = $user->comments()->latest()->get();
        // ↑↑↑ ここまで追記 ↑↑↑


        return view('profile.edit', [
            'user' => $user,
            'userQuestions' => $userQuestions,// ★追加
            'userAnswers' => $userAnswers,// ★追加
            'userComments' => $userComments,// ★追加
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

      // ↓↓↓ ここからプロフィール画像のアップロード/削除メソッドを追記 ↓↓↓

    /**
     * Update the user's profile image.
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => 'nullable|image|max:2048', // 画像は任意、最大2MB
        ]);

        $user = $request->user();
        $imagePath = $user->profile_image_path; // 現在の画像パス

        if ($request->hasFile('profile_image')) {
            // 古い画像があれば削除
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            // 新しい画像を保存
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->profile_image_path = $imagePath;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-image-updated');
    }

    /**
     * Delete the user's profile image.
     */
    public function deleteImage(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->profile_image_path);
            $user->profile_image_path = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-image-deleted');
    }

    // ↑↑↑ ここまで追記 ↑↑↑

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

         // ↓↓↓ ここからプロフィール画像削除ロジックを追記 ↓↓↓
        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->profile_image_path);
        }
        // ↑↑↑ ここまで追記 ↑↑↑

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
