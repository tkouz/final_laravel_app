{{-- resources/views/questions/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('質問一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- 成功メッセージの表示 --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    {{-- エラーメッセージの表示 --}}
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- 検索・絞り込み・並び替えフォーム --}}
                    <form action="{{ route('questions.index') }}" method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="flex flex-wrap items-end gap-4 mb-4">
                            {{-- キーワード検索 --}}
                            <div class="flex-1 min-w-[200px]">
                                <label for="keyword" class="block text-sm font-medium text-gray-700">キーワード</label>
                                <input type="text" name="keyword" id="keyword"
                                       value="{{ $searchQuery ?? '' }}"
                                       placeholder="タイトルや内容で検索"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            {{-- ステータス絞り込み --}}
                            <div class="flex-1 min-w-[150px]">
                                <label for="status" class="block text-sm font-medium text-gray-700">ステータス</label>
                                <select name="status" id="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">全て</option>
                                    <option value="unresolved" {{ ($statusFilter ?? '') == 'unresolved' ? 'selected' : '' }}>未解決</option>
                                    <option value="resolved" {{ ($statusFilter ?? '') == 'resolved' ? 'selected' : '' }}>解決済み</option>
                                </select>
                            </div>

                            {{-- 並び替え --}}
                            <div class="flex-1 min-w-[150px]">
                                <label for="sort" class="block text-sm font-medium text-gray-700">並び替え</label>
                                <select name="sort" id="sort"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="latest" {{ ($sortBy ?? '') == 'latest' ? 'selected' : '' }}>新しい順</option>
                                    <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>古い順</option>
                                    <option value="answers_desc" {{ ($sortBy ?? '') == 'answers_desc' ? 'selected' : '' }}>回答数が多い順</option>
                                    <option value="likes_desc" {{ ($sortBy ?? '') == 'likes_desc' ? 'selected' : '' }}>いいねが多い順</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-fuchsia-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-fuchsia-800 focus:bg-fuchsia-800 active:bg-fuchsia-900 focus:outline-none focus:ring-2 focus:ring-fuchsia-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg">
                                {{ __('検索・絞り込みを適用') }}
                            </button>
                            <a href="{{ route('questions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('リセット') }}
                            </a>
                        </div>
                    </form>

                    {{-- 質問リスト --}}
                    @if ($questions->isEmpty())
                        <p class="text-gray-700">質問はまだありません。</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($questions as $question)
                                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <a href="{{ route('questions.show', $question) }}" class="hover:underline">
                                            {{ $question->title }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm">
                                        投稿者: {{ $question->user->name }} - {{ $question->created_at->diffForHumans() }}
                                        @if ($question->is_resolved)
                                            <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">解決済み</span>
                                        @endif
                                    </p>
                                    <p class="mt-2 text-gray-800">{{ Str::limit($question->body, 150) }}</p>

                                    {{-- 質問画像表示 (一覧画面) --}}
                                    @if ($question->image_path)
                                        <div class="mt-4">
                                            <img src="{{ Storage::url($question->image_path) }}" alt="質問画像" class="max-w-full h-auto rounded-lg shadow-md w-32 h-32 object-cover">
                                        </div>
                                    @endif

                                    {{-- 回答数・いいね数とボタンを左右に配置 --}}
                                    <div class="mt-2 flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <span>回答: {{ $question->answers->count() }}</span>
                                            <span class="ml-4">いいね: {{ $question->likes->count() }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            {{-- いいね！ボタン (一覧画面) --}}
                                            @auth
                                                @if (!Auth::user()->isAdmin())
                                                    <button
                                                        id="like-button-{{ $question->id }}"
                                                        data-question-id="{{ $question->id }}"
                                                        data-liked="{{ Auth::check() && $question->isLikedByUser(Auth::user()) ? 'true' : 'false' }}"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                                                {{ Auth::check() && $question->isLikedByUser(Auth::user()) ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                                                focus:outline-none transition ease-in-out duration-150"
                                                    >
                                                        <span id="like-icon-{{ $question->id }}" class="mr-1">
                                                            {{-- ★修正: SVGアイコンを使用 --}}
                                                            @if (Auth::check() && $question->isLikedByUser(Auth::user()))
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                                    <path d="M11.645 20.91L3.362 12.592a2.25 2.25 0 01-.532-1.885c.073-.444.266-.815.532-1.885.33-1.053 1.253-2.087 2.666-2.087 1.579 0 2.666.9 3.245 1.725a3.645 3.645 0 003.245 0c.58-.825 1.666-1.725 3.245-1.725 1.413 0 2.336 1.034 2.666 2.087.266 1.07.459 1.441.532 1.885l-8.283 8.318z" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.835 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                                </svg>
                                                            @endif
                                                        </span>
                                                        いいね！
                                                    </button>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.835 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                    </svg>
                                                    いいね！
                                                </span>
                                            @endauth

                                            {{-- ブックマークボタン (一覧画面) --}}
                                            @auth
                                                @if (!Auth::user()->isAdmin())
                                                    <button
                                                        id="bookmark-button-{{ $question->id }}"
                                                        data-question-id="{{ $question->id }}"
                                                        data-bookmarked="{{ Auth::check() && Auth::user()->bookmarks()->where('question_id', $question->id)->exists() ? 'true' : 'false' }}"
                                                        class="ml-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                                                {{ Auth::check() && Auth::user()->bookmarks()->where('question_id', $question->id)->exists() ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                                                focus:outline-none transition ease-in-out duration-150"
                                                    >
                                                        <span id="bookmark-icon-{{ $question->id }}" class="mr-1">
                                                            {{-- SVGアイコンを使用 --}}
                                                            @if (Auth::check() && Auth::user()->bookmarks()->where('question_id', $question->id)->exists())
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                                    <path fill-rule="evenodd" d="M6 3a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V6a3 3 0 00-3-3H6zm.75 1.5a.75.75 0 00-.75.75v10.5a.75.75 0 00.75.75h9a.75.75 0 00.75-.75V5.25a.75.75 0 00-.75-.75h-9zM12 9a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0V9.75A.75.75 0 0112 9z" clip-rule="evenodd" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                                                </svg>
                                                            @endif
                                                        </span>
                                                        ブックマーク
                                                    </button>
                                                @endif
                                            @else
                                                <span class="ml-2 inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                                    </svg>
                                                    ブックマーク
                                                </span>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $questions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Like and Bookmark buttons (一覧画面用) --}}
    @auth
    @if (!Auth::user()->isAdmin())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 各質問のいいねボタンとブックマークボタンにイベントリスナーを設定
            document.querySelectorAll('[id^="like-button-"]').forEach(button => {
                button.addEventListener('click', async function () {
                    const questionId = this.dataset.questionId;
                    let isLiked = this.dataset.liked === 'true';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const likeIcon = document.getElementById(`like-icon-${questionId}`);

                    let url = '';
                    let method = '';

                    if (isLiked) {
                        url = `/questions/${questionId}/unlike`;
                        method = 'DELETE';
                    } else {
                        url = `/questions/${questionId}/like`;
                        method = 'POST';
                    }

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            isLiked = data.liked;
                            this.dataset.liked = isLiked;

                            if (isLiked) {
                                likeIcon.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                        <path d="M11.645 20.91L3.362 12.592a2.25 2.25 0 01-.532-1.885c.073-.444.266-.815.532-1.885.33-1.053 1.253-2.087 2.666-2.087 1.579 0 2.666.9 3.245 1.725a3.645 3.645 0 003.245 0c.58-.825 1.666-1.725 3.245-1.725 1.413 0 2.336 1.034 2.666 2.087.266 1.07.459 1.441.532 1.885l-8.283 8.318z" />
                                    </svg>
                                `;
                                this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                this.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600');
                            } else {
                                likeIcon.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.835 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                `;
                                this.classList.remove('bg-red-500', 'text-white', 'hover:bg-red-600');
                                this.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                            }
                            console.log(data.message);
                        } else {
                            console.error('APIエラー:', data.message || '不明なエラー');
                            alert('エラーが発生しました: ' + (data.message || '不明なエラー'));
                        }
                    } catch (error) {
                        console.error('ネットワークエラー:', error);
                        alert('ネットワークエラーが発生しました。');
                    }
                });
            });

            document.querySelectorAll('[id^="bookmark-button-"]').forEach(button => {
                button.addEventListener('click', async function () {
                    const questionId = this.dataset.questionId;
                    let isBookmarked = this.dataset.bookmarked === 'true';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const bookmarkIcon = document.getElementById(`bookmark-icon-${questionId}`);

                    let url = '';
                    let method = '';

                    if (isBookmarked) {
                        url = `/questions/${questionId}/bookmark`; // ブックマーク削除
                        method = 'DELETE';
                    } else {
                        url = `/questions/${questionId}/bookmark`; // ブックマーク追加
                        method = 'POST';
                    }

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            isBookmarked = data.bookmarked;
                            this.dataset.bookmarked = isBookmarked;

                            if (isBookmarked) {
                                bookmarkIcon.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M6 3a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V6a3 3 0 00-3-3H6zm.75 1.5a.75.75 0 00-.75.75v10.5a.75.75 0 00.75.75h9a.75.75 0 00.75-.75V5.25a.75.75 0 00-.75-.75h-9zM12 9a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0V9.75A.75.75 0 0112 9z" clip-rule="evenodd" />
                                    </svg>
                                `;
                                this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                this.classList.add('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                            } else {
                                bookmarkIcon.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                    </svg>
                                `;
                                this.classList.remove('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                                this.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                            }
                            console.log(data.message);
                        } else {
                            console.error('APIエラー:', data.message || '不明なエラー');
                            alert('エラーが発生しました: ' + (data.message || '不明なエラー'));
                        }
                    } catch (error) {
                        console.error('ネットワークエラー:', error);
                        alert('ネットワークエラーが発生しました。');
                    }
                });
            });
        });
    </script>
    @endif {{-- !Auth::user()->isAdmin() の閉じタグ --}}
    @endauth
</x-app-layout>
