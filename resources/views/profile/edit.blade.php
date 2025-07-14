{{-- resources/views/profile/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('マイページ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- プロフィール情報更新フォーム --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- プロフィール画像管理セクション --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('プロフィール画像') }}</h3>
                    @include('profile.partials.update-profile-image-form')
                </div>
            </div>

            {{-- パスワード更新フォーム --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 自分が投稿した質問一覧 --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('自分が投稿した質問') }}</h3>
                    @forelse ($userQuestions as $question)
                        <div class="mb-2 p-2 border rounded-md">
                            <h4 class="font-semibold text-gray-800">
                                <a href="{{ route('questions.show', $question) }}" class="text-blue-600 hover:underline">
                                    {{ $question->title }}
                                </a>
                            </h4>
                            <p class="text-sm text-gray-600">{{ $question->created_at->diffForHumans() }}</p>
                            <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($question->body, 150) }}</p>

                {{-- ★ここを追記：編集・削除ボタン --}}
                <div class="mt-2 flex space-x-2">
                    <a href="{{ route('questions.edit', $question) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('編集') }}
                    </a>
                    <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('削除') }}
                        </button>
                    </form>
                </div>
                {{-- ★ここまで追記 --}}
                        </div>
                    @empty
                        <p class="text-gray-700">まだ質問を投稿していません。</p>
                    @endforelse
                </div>
            </div>

           {{-- 自分が投稿した回答一覧 --}}
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mt-8">
    <div class="max-w-full">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('自分が投稿した回答') }}</h3>
        @forelse ($userAnswers as $answer)
            <div class="mb-2 p-2 border rounded-md">
                <p class="text-gray-800">
                    質問:
                    @if ($answer->question)
                        <a href="{{ route('questions.show', $answer->question) }}" class="text-blue-600 hover:underline">
                            {{ Str::limit($answer->question->title, 50) }}
                        </a>
                    @else
                        {{ __('（関連質問は削除されました）') }}
                    @endif
                </p>
                <p class="text-sm text-gray-600">{{ $answer->created_at->diffForHumans() }}</p>
                {{-- ★ここを追記または修正：回答本文の表示 --}}
                <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($answer->content, 150) }}</p> 

                 {{-- ★ここを追記：編集・削除ボタン --}}
                <div class="mt-2 flex space-x-2">
                    {{-- 回答の編集ルート (もしあれば) --}}
                    {{-- 現状の resource('answers') は except(['edit', 'update']) している可能性があるので、
                         もしエラーが出たら routes/web.php で answers.edit が有効か確認してください --}}
                    <a href="{{ route('answers.edit', $answer) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('編集') }}
                    </a>
                    {{-- 回答の削除フォーム --}}
                    <form action="{{ route('answers.destroy', $answer) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('削除') }}
                        </button>
                    </form>
                </div>
                {{-- ★ここまで追記 --}}
            </div>
        @empty
            <p class="text-gray-700">まだ回答を投稿していません。</p>
        @endforelse
    </div>
</div>

            {{-- 自分が投稿したコメント一覧 --}}
<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
    <div class="max-w-full">
        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('自分が投稿したコメント') }}</h3>
        @forelse ($userComments as $comment)
            <div class="mb-2 p-2 border rounded-md">
                <p class="text-gray-800">
                    {{-- コメントは常に回答に紐付くので、直接 $comment->answer にアクセス --}}
                    回答へのコメント:
                    {{-- $comment->answer が存在することを確認（回答が削除された場合など） --}}
                    @if ($comment->answer)
                        <a href="{{ route('questions.show', $comment->answer->question) }}#answer-{{ $comment->answer->id }}" class="text-blue-600 hover:underline">
                            {{ Str::limit($comment->answer->body, 50) }} {{-- 回答の本文を表示 --}}
                        </a>
                    @else
                        {{ __('（関連回答は削除されました）') }}
                    @endif
                </p>
                <p class="text-sm text-gray-600">{{ $comment->created_at->diffForHumans() }}</p>
                {{-- コメント本文の表示 --}}
                <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($comment->content, 150) }}</p> {{-- ★変更: $comment->body を $comment->content に --}}
            </div>
        @empty
            <p class="text-gray-700">まだコメントを投稿していません。</p>
        @endforelse
    </div>
</div>

            <div class="mt-8 bg-white shadow sm:rounded-lg">
              <div class="p-6">
                 <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('ブックマークした質問') }}</h3>

                @forelse ($bookmarkedQuestions as $question)
                 <div class="border-b border-gray-200 py-4 last:border-b-0">
                     <p class="text-lg font-medium text-gray-900">
                         <a href="{{ route('questions.show', $question->id) }}" class="hover:underline">
                             {{ $question->title }}
                          </a>
                       </p>
                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($question->body, 100) }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ __('投稿日:') }} {{ $question->created_at->diffForHumans() }}
                </p>
            </div>
        @empty
            <p class="text-gray-600">{{ __('まだブックマークした質問はありません。') }}</p>
        @endforelse
    </div>
</div>

            {{-- ユーザー退会フォーム --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>