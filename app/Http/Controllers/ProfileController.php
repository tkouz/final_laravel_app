<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect; // 修正点: Redirectファサードをインポート
use App\Models\Question; // Questionモデルをインポート
use App\Models\Answer; // Answerモデルをインポート
use App\Models\Comment; // Commentモデルをインポート
use App\Models\User; // Userモデルをインポート
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * ユーザーのプロフィール編集画面を表示します。
     * Breezeが生成するルートに合わせてeditメソッド名を使用します。
     */
    public function edit()
    {
        $user = Auth::user();

        $userQuestions = Question::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        $userAnswers = Answer::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $userComments = Comment::where('user_id', $user->id)
                              ->orderBy('created_at', 'desc')
                              ->get();

        $bookmarkedQuestions = $user->bookmarks()->orderBy('created_at', 'desc')->get();

        return view('profile.edit', compact('user', 'userQuestions', 'userAnswers', 'userComments', 'bookmarkedQuestions'));
    }

    /**
     * ユーザーのプロフィール情報を更新します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // プロフィール情報（アカウント名、メールアドレス）のバリデーション
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $user = Auth::user();

        // データベースを更新
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました。');
    }

    /**
     * プロフィール画像を更新します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_image')) {
            // 古い画像があれば削除
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }
            // 新しい画像を保存
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image_path = $imagePath;
        } elseif ($request->boolean('remove_image')) {
            // 画像削除がリクエストされた場合
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
                $user->profile_image_path = null;
            }
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'プロフィール画像が更新されました。');
    }

    /**
     * プロフィール画像を削除します。
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->profile_image_path);
            $user->profile_image_path = null;
            $user->save();
        }
        return redirect()->route('profile.edit')->with('success', 'プロフィール画像が削除されました。');
    }

    /**
     * ユーザーのアカウントを削除します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
