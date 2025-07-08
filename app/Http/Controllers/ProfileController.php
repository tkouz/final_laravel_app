<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Comment;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        // ユーザーが投稿した質問 (これは通常リレーション不要)
        $userQuestions = Question::where('user_id', $user->id)->latest()->get();

        // ★★★ 変更: 回答の取得時に質問リレーションをEager Load ★★★
        $userAnswers = Answer::where('user_id', $user->id)
                            ->with('question') // 'question' リレーションをロード
                            ->latest()
                            ->get();

        // ★★★ 変更: コメントの取得時にコメント対象リレーションをEager Load ★★★
        // comments()リレーションがcommentable() (ポリモーフィック) を使用している場合
        // loadMorph() を使用して 'commentable' リレーションをロード
        $userComments = Comment::where('user_id', $user->id)
                                ->with('answer.question') // commentable リレーションをロード
                                ->latest()
                                ->get();

        $bookmarkedQuestions = $user->bookmarks()->latest()->get();

        return view('profile.edit', [
            'user' => $user,
            'userQuestions' => $userQuestions,
            'userAnswers' => $userAnswers,
            'userComments' => $userComments,
            'bookmarkedQuestions' => $bookmarkedQuestions,
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
