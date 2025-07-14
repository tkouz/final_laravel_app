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
use App\Models\User; // Userモデルのuseステートメントを確認

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $userQuestions = Question::where('user_id', $user->id)->latest()->get();
        $userAnswers = Answer::where('user_id', $user->id)
                                ->with('question')
                                ->latest()
                                ->get();
        $userComments = Comment::where('user_id', $user->id)
                                ->with('answer.question')
                                ->latest()
                                ->get();

        // ★追加または修正: ブックマークした質問を取得
        // Userモデルにbookmarks()リレーションが定義されている前提
        $bookmarkedQuestions = $user->bookmarks()->latest()->get();

        return view('profile.edit', [
            'user' => $user,
            'userQuestions' => $userQuestions,
            'userAnswers' => $userAnswers,
            'userComments' => $userComments,
            'bookmarkedQuestions' => $bookmarkedQuestions, // ★ビューに渡す
        ]);
    }

    // ★追加または修正: プロフィール画像更新の処理 (profile.partials.update-profile-image-form.blade.phpと連携)
    public function updateImage(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => 'required|image|max:2048', // 2MBまで
        ]);

        $user = $request->user();
        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        $path = $request->file('profile_image')->store('profile_images', 'public');
        $user->profile_image_path = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-image-updated');
    }

    // ★追加または修正: プロフィール画像削除の処理
    public function deleteImage(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->profile_image_path) {
            Storage::disk('public')->delete($user->profile_image_path);
            $user->profile_image_path = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-image-deleted');
    }

}