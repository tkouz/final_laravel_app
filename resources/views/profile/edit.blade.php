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

                            @can('update', $question)
                                <div class="mt-2 text-right">
                                    <a href="{{ route('questions.edit', $question) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                        {{ __('編集') }}
                                    </a>
                                    <form action="{{ route('questions.destroy', $question) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('本当に削除しますか？')">
                                            {{ __('削除') }}
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @empty
                        <p class="text-gray-700">まだ質問を投稿していません。</p>
                    @endforelse
                </div>
            </div>

            {{-- 自分が投稿した回答一覧 --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('自分が投稿した回答') }}</h3>
                    @forelse ($userAnswers as $answer)
                        <div class="mb-2 p-2 border rounded-md">
                            <p class="text-gray-800">
                                質問: <a href="{{ route('questions.show', $answer->question) }}" class="text-blue-600 hover:underline">{{ Str::limit($answer->question->title, 50) }}</a>
                            </p>
                            <p class="text-sm text-gray-600">{{ $answer->created_at->diffForHumans() }}</p>
                            {{-- 回答本文の表示を追加 --}}
                            <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($answer->body, 150) }}</p>

                            @can('update', $answer)
                                <div class="mt-2 text-right">
                                    <a href="{{ route('answers.edit', $answer) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                        {{ __('編集') }}
                                    </a>
                                    <form action="{{ route('answers.destroy', $answer) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('本当に削除しますか？')">
                                            {{ __('削除') }}
                                        </button>
                                    </form>
                                </div>
                            @endcan
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
                                回答へのコメント: <a href="{{ route('questions.show', $comment->answer->question) }}#answer-{{ $comment->answer->id }}" class="text-blue-600 hover:underline">
                                    {{ Str::limit($comment->answer->body, 50) }}
                                </a>
                            </p>
                            <p class="text-sm text-gray-600">{{ $comment->created_at->diffForHumans() }}</p>
                            {{-- コメント本文の表示を追加 --}}
                            <p class="mt-1 text-gray-700 text-sm">{{ Str::limit($comment->body, 150) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-700">まだコメントを投稿していません。</p>
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