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
                    {{-- 検索フォーム --}}
                    <form action="{{ route('questions.index') }}" method="GET" class="mb-6 space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
                        {{-- 変更: name="search" -> name="keyword" --}}
                        <input type="text" name="keyword" placeholder="キーワードで検索..." value="{{ $searchQuery }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-auto flex-grow">

                        {{-- 投稿日時フィルター --}}
                        <div class="flex items-center space-x-2 w-full md:w-auto">
                            <label for="date_filter" class="text-sm text-gray-700 whitespace-nowrap">投稿日時（以降）:</label>
                            {{-- 変更: name="date_filter" -> name="posted_at" --}}
                            <input type="date" name="posted_at" id="date_filter" value="{{ request('posted_at') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        {{-- 変更: name="status_filter" -> name="status" --}}
                        <select name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-auto">
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>全て</option>
                            {{-- 変更: 'open' -> 'unresolved' に統一 --}}
                            <option value="unresolved" {{ $statusFilter == 'unresolved' ? 'selected' : '' }}>未解決</option>
                            <option value="resolved" {{ $statusFilter == 'resolved' ? 'selected' : '' }}>解決済み</option>
                        </select>
                        {{-- 変更: name="sort_by" -> name="sort" --}}
                        <select name="sort" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full md:w-auto">
                            <option value="latest" {{ $sortBy == 'latest' ? 'selected' : '' }}>新しい順</option>
                            <option value="oldest" {{ $sortBy == 'oldest' ? 'selected' : '' }}>古い順</option>
                            {{-- 変更: 'most_answers' -> 'answers_desc' に統一 --}}
                            <option value="answers_desc" {{ $sortBy == 'answers_desc' ? 'selected' : '' }}>回答数が多い順</option>
                            {{-- 変更: 'popular' -> 'likes_desc' に統一 --}}
                            <option value="likes_desc" {{ $sortBy == 'likes_desc' ? 'selected' : '' }}>いいねが多い順</option>
                        </select>
                        <x-primary-button type="submit" class="w-full md:w-auto">{{ __('検索・絞り込み') }}</x-primary-button>
                        <a href="{{ route('questions.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full md:w-auto">
                            {{ __('リセット') }}
                        </a>
                    </form>

                    {{-- 質問一覧の表示部分は変更なし --}}
                    @forelse ($questions as $question)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <a href="{{ route('questions.show', $question) }}" class="hover:underline">
                                    {{ $question->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm">
                                投稿者: {{ $question->user->name }} - {{ $question->created_at->diffForHumans() }}
                                <span class="ml-2">回答数: {{ $question->answers_count }}</span>
                                <span class="ml-2">いいね数: {{ $question->likes_count ?? $question->likes->count() }}</span>
                                @if ($question->is_resolved)
                                    <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">解決済み</span>
                                @endif
                            </p>
                            <p class="mt-2 text-gray-700 text-sm">{{ Str::limit($question->body, 150) }}</p>

                            @if ($question->image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($question->image_path) }}" alt="質問画像" class="max-w-full h-auto rounded-lg shadow-md">
                                </div>
                            @endif

                            <div class="mt-4 flex items-center justify-end space-x-2">
                                {{-- いいね！ボタン --}}
                                @auth
                                <button
                                    id="like-button-{{ $question->id }}"
                                    data-question-id="{{ $question->id }}"
                                    data-liked="{{ $question->isLikedByUser(Auth::user()) ? 'true' : 'false' }}"
                                    class="like-button inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                            {{ $question->isLikedByUser(Auth::user()) ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                            focus:outline-none transition ease-in-out duration-150"
                                >
                                    <span id="like-icon-{{ $question->id }}" class="mr-1">
                                        {{ $question->isLikedByUser(Auth::user()) ? '❤️' : '🤍' }}
                                    </span>
                                    いいね！
                                </button>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                        🤍 いいね！
                                    </span>
                                @endauth
                                <span id="like-count-{{ $question->id }}" class="text-gray-600 text-sm">
                                    {{ $question->likes_count ?? $question->likes->count() }}
                                </span>

                                {{-- ブックマークボタン --}}
                                @auth
                                <button
                                    id="bookmark-button-{{ $question->id }}"
                                    data-question-id="{{ $question->id }}"
                                    data-bookmarked="{{ $question->isBookmarkedByUser(Auth::user()) ? 'true' : 'false' }}"
                                    class="bookmark-button inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                            {{ $question->isBookmarkedByUser(Auth::user()) ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                            focus:outline-none transition ease-in-out duration-150"
                                >
                                    <span id="bookmark-icon-{{ $question->id }}" class="mr-1">
                                        {{ $question->isBookmarkedByUser(Auth::user()) ? '🔖' : '📑' }}
                                    </span>
                                    ブックマーク
                                </button>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                        📑 ブックマーク
                                    </span>
                                @endauth

                            </div>
                        </div>
                    @empty
                        <p class="text-gray-700">まだ質問がありません。</p>
                    @endforelse

                    <div class="mt-4">
                        {{ $questions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Like and Bookmark buttons --}}
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // いいねボタンの処理
            document.querySelectorAll('.like-button').forEach(button => {
                button.addEventListener('click', async function () {
                    const questionId = this.dataset.questionId;
                    let isLiked = this.dataset.liked === 'true';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const likeIcon = document.getElementById(`like-icon-${questionId}`);
                    const likeCountSpan = document.getElementById(`like-count-${questionId}`);

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
                            likeCountSpan.textContent = data.likes_count;

                            if (isLiked) {
                                likeIcon.textContent = '❤️';
                                this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                this.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600');
                            } else {
                                likeIcon.textContent = '🤍';
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

            // ブックマークボタンの処理
            document.querySelectorAll('.bookmark-button').forEach(button => {
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
                                bookmarkIcon.textContent = '🔖';
                                this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                this.classList.add('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                            } else {
                                bookmarkIcon.textContent = '📑';
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
    @endauth
</x-app-layout>