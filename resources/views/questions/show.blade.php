{{-- resources/views/questions/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('質問詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{}">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $question->title }}</h2>
                        <p class="text-gray-600 text-sm">
                            投稿者: {{ $question->user->name }} - {{ $question->created_at->diffForHumans() }}
                            @if ($question->is_resolved)
                                <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">解決済み</span>
                            @endif
                        </p>
                        <p class="mt-4 text-gray-800">{{ $question->body }}</p>

                        {{-- 質問の編集・削除ボタン --}}
                        @auth
                            {{-- 自分の投稿者で、かつ管理ユーザーでない場合にのみ表示 --}}
                            @if (Auth::id() === $question->user_id && !Auth::user()->isAdmin())
                                <div class="mt-4 flex space-x-2">
                                    <a href="{{ route('questions.edit', $question) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('編集') }}
                                    </a>
                                    <form action="{{ route('questions.destroy', $question) }}" method="POST" onsubmit="return confirm('本当にこの質問を削除しますか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('削除') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                            {{-- 管理者用「表示停止/表示する」ボタン --}}
                            @if (Auth::user()->isAdmin())
                                <div class="mt-4 flex space-x-2">
                                    <form action="{{ route('admin.reports.toggleVisibility', ['type' => 'question', 'id' => $question->id]) }}" method="POST" onsubmit="return confirm('本当にこの質問の表示状態を切り替えますか？');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow-md
                                            {{ $question->is_visible ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                                            {{ $question->is_visible ? '表示を停止する' : '表示する' }}
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        {{-- 質問の違反報告機能 --}}
                        @auth
                            {{-- 自分の投稿ではなく、かつ管理ユーザーでない場合にのみ表示 --}}
                            @if (Auth::id() !== $question->user_id && !Auth::user()->isAdmin())
                                <button x-on:click="console.log('回答の違反報告ボタンがクリックされました！'); $dispatch('open-report-modal', { reportableType: 'question', reportableId: {{ $question->id }} })"
                                        class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-500 transition ease-in-out duration-150">
                                        {{ __('違反報告') }}
                                </button>
                            @endif
                        @endauth

                        {{-- 質問画像表示 --}}
                        @if ($question->image_path)
                            <div class="mt-4">
                                <img src="{{ Storage::url($question->image_path) }}" alt="質問画像" class="max-w-full h-auto rounded-lg shadow-md">
                            </div>
                        @endif

                        {{-- いいね！ボタンと表示 --}}
                        <div class="mt-4 flex items-center space-x-2">
                            @auth
                                {{-- 管理ユーザーでない場合にのみ表示 --}}
                                @if (!Auth::user()->isAdmin())
                                    <button
                                        id="like-button"
                                        data-question-id="{{ $question->id }}"
                                        data-liked="{{ $question->isLikedByUser(Auth::user()) ? 'true' : 'false' }}"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                                {{ $question->isLikedByUser(Auth::user()) ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                                focus:outline-none transition ease-in-out duration-150"
                                    >
                                        <span id="like-icon" class="mr-1">
                                            {{ $question->isLikedByUser(Auth::user()) ? '❤️' : '🤍' }}
                                        </span>
                                        いいね！
                                    </button>
                                @else
                                    {{-- 管理ユーザーの場合はボタンを非表示にし、アイコンと数だけ表示 --}}
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                        🤍 いいね！
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                    🤍 いいね！
                                </span>
                            @endauth
                            <span id="like-count" class="text-gray-600 text-sm">
                                {{ $question->likes->count() }}
                            </span>

                            {{-- ブックマークボタン --}}
                            <div class="mt-4 flex items-center space-x-2">
                                @auth
                                    {{-- 管理ユーザーでない場合にのみ表示 --}}
                                    @if (!Auth::user()->isAdmin())
                                        <button
                                            id="bookmark-button"
                                            data-question-id="{{ $question->id }}"
                                            data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md
                                                    {{ $isBookmarked ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}
                                                    focus:outline-none transition ease-in-out duration-150"
                                        >
                                            <span id="bookmark-icon" class="mr-1">
                                                {{-- ★修正: SVGアイコンに変更 --}}
                                                @if ($isBookmarked)
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
                                    @else
                                        {{-- 管理ユーザーの場合はボタンを非表示にし、アイコンだけ表示 --}}
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                            {{-- ★修正: SVGアイコンに変更 --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                            </svg>
                                            ブックマーク
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 text-sm leading-4 font-medium rounded-md">
                                        {{-- ★修正: SVGアイコンに変更 --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                        </svg>
                                        ブックマーク
                                    </span>
                                @endauth
                            </div>
                        </div>
                    </div>

                    {{-- 質問の所有者で、かつまだベストアンサーが選ばれていない場合のみ表示 --}}
                    {{-- 管理ユーザーでない場合にのみ表示 --}}
                    @if (Auth::check() && Auth::id() === $question->user_id && !$question->best_answer_id && !Auth::user()->isAdmin())
                        <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 rounded-lg">
                            <p>ベストアンサーを選ぶことで、この質問を解決済みにできます。</p>
                        </div>
                    @endif

                    {{-- ベストアンサーの表示 --}}
                    @if ($question->bestAnswer)
                        <div class="mt-8 p-4 bg-green-100 border-l-4 border-green-500 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold text-green-800 flex items-center">
                                ベストアンサー
                                {{-- ベストアンサータグを追加 --}}
                                <span class="ml-2 px-2 py-1 bg-green-600 text-white text-xs font-semibold rounded-full">選ばれました！</span>
                            </h3>
                            <p class="text-gray-700 mt-2">{{ $question->bestAnswer->content }}</p>
                            @if ($question->bestAnswer->image_path)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $question->bestAnswer->image_path) }}" alt="ベストアンサー画像" class="max-w-full h-auto rounded-lg shadow-md">
                                </div>
                            @endif
                            <p class="text-right text-sm text-green-600">
                                選ばれた回答者: {{ $question->bestAnswer->user->name }} - {{ $question->bestAnswer->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">回答一覧 ({{ $question->answers->count() }})</h3>

                    @forelse ($question->answers as $answer)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-4 {{ $answer->id === $question->best_answer_id ? 'border-2 border-green-500' : '' }}">
                            <p class="text-gray-700">{{ $answer->content }}</p>

                            {{-- 回答画像表示 --}}
                            @if ($answer->image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($answer->image_path) }}" alt="回答画像" class="max-w-full h-auto rounded-lg shadow-md">
                                </div>
                            @endif

                            <p class="text-right text-sm text-gray-600">
                                回答者: {{ $answer->user->name }} - {{ $answer->created_at->diffForHumans() }}
                                {{-- 各回答にもベストアンサータグを追加 --}}
                                @if ($answer->id === $question->best_answer_id)
                                    <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">ベストアンサー</span>
                                    @endif
                            </p>

                            {{-- 回答の編集・削除ボタン --}}
                            @auth
                                {{-- 自分の投稿者で、かつ管理ユーザーでない場合にのみ表示 --}}
                                @if (Auth::id() === $answer->user_id && !Auth::user()->isAdmin())
                                    <div class="mt-2 flex space-x-2 justify-end"> {{-- 右寄せにするためにjustify-endを追加 --}}
                                        <a href="{{ route('answers.edit', $answer) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('編集') }}
                                        </a>
                                        <form action="{{ route('answers.destroy', $answer) }}" method="POST" onsubmit="return confirm('本当にこの回答を削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('削除') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth

                            {{-- 回答の違反報告機能 --}}
                            @auth
                                {{-- 自分の投稿ではなく、かつ管理ユーザーでない場合にのみ表示 --}}
                                @if (Auth::id() !== $answer->user_id && !Auth::user()->isAdmin())
                                    <button x-on:click="console.log('回答の違反報告ボタンがクリックされました！'); $dispatch('open-report-modal', { reportableType: 'answer', reportableId: {{ $answer->id }} })"
                                            class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                                            {{ __('違反報告') }}
                                    </button>
                                @endif
                            @endauth

                            {{-- ベストアンサー選定ボタン --}}
                            {{-- 管理ユーザーでない場合にのみ表示 --}}
                            @if (Auth::check() && Auth::id() === $question->user_id && !$question->best_answer_id && !Auth::user()->isAdmin())
                                <form action="{{ route('answers.markAsBestAnswer', ['question' => $question->id, 'answer' => $answer->id]) }}" method="POST" class="mt-2 text-right">
                                    @csrf
                                    <x-primary-button type="submit" class="text-green-600 hover:text-green-800 text-sm">ベストアンサーに選ぶ</x-primary-button>
                                </form>
                            @endif

                            {{-- コメント表示 --}}
                            @foreach($answer->comments as $comment)
                                <div class="ml-4 mt-2 p-2 bg-gray-100 rounded-lg text-sm">
                                    <p class="text-gray-700">{{ $comment->content }}</p>
                                    <p class="text-right text-xs text-gray-500">
                                        コメント者: {{ $comment->user->name }} - {{ $comment->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            @endforeach

                            {{-- コメント投稿フォーム --}}
                            @auth
                                {{-- 管理ユーザーでない場合にのみ表示 --}}
                                @if (!Auth::user()->isAdmin())
                                    <form action="{{ route('comments.store', $answer) }}" method="POST" class="mt-2">
                                        @csrf
                                        <x-textarea-input name="content" class="w-full text-sm" rows="2" placeholder="コメントを追加"></x-textarea-input>
                                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                                        <x-primary-button class="mt-2">コメントする</x-primary-button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    @empty
                        <p class="text-gray-700">まだ回答がありません。</p>
                    @endforelse

                    <h3 class="text-xl font-bold text-gray-900 mt-8 mb-4">回答を投稿する</h3>
                    @auth
                        {{-- 管理ユーザーでない場合にのみ表示 --}}
                        @if (!Auth::user()->isAdmin())
                            <form action="{{ route('answers.store', $question) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <x-textarea-input name="content" class="w-full" rows="5" placeholder="あなたの回答を入力してください"></x-textarea-input>
                                <x-input-error :messages="$errors->get('content')" class="mt-2" />

                                {{-- 回答画像添付フィールド --}}
                                <div class="mt-4">
                                    <x-input-label for="answer_image" :value="__('画像添付 (任意)')" />
                                    <input id="answer_image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="image" />
                                    <p class="mt-1 text-sm text-gray-500" id="file_input_help_answer">PNG, JPG, JPEG (最大2MB)</p>
                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                </div>

                                <x-primary-button class="mt-4">回答を投稿</x-primary-button>
                            </form>
                        @else
                            <p class="text-gray-700">回答を投稿するにはログインが必要です。</p>
                        @endif
                    @else
                        <p class="text-gray-700">回答を投稿するにはログインが必要です。</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- モーダルコンポーネントの呼び出し --}}
    @auth
        {{-- 管理ユーザーでない場合にのみ表示 --}}
        @if (!Auth::user()->isAdmin())
            {{-- 質問に対する違反報告モーダル --}}
            <x-report-modal id="reportQuestionModal" reportableType="question" :reportableId="$question->id" />

            {{-- 各回答に対する違反報告モーダル (ループの外で一つずつ生成する必要がある) --}}
            {{-- Alpine.js の dispatch イベントで、クリックされたボタンに対応するモーダルを開く --}}
            @foreach ($question->answers as $answer)
                <x-report-modal id="reportAnswerModal-{{ $answer->id }}" reportableType="answer" :reportableId="$answer->id" />
            @endforeach
        @endif
    @endauth

    {{-- JavaScript for Like and Bookmark buttons --}}
    @auth
    {{-- 管理ユーザーでない場合にのみJavaScriptをロード --}}
    @if (!Auth::user()->isAdmin())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // いいねボタンの処理 (質問詳細ページではIDが単一なのでIDで取得)
            const likeButton = document.getElementById('like-button');
            if (likeButton) {
                likeButton.addEventListener('click', async function () {
                    const questionId = this.dataset.questionId;
                    let isLiked = this.dataset.liked === 'true';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const likeIcon = document.getElementById('like-icon');
                    const likeCountSpan = document.getElementById('like-count');

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
            }

            // ブックマークボタンの処理 (質問詳細ページではIDが単一なのでIDで取得)
            const bookmarkButton = document.getElementById('bookmark-button');
            if (bookmarkButton) {
                bookmarkButton.addEventListener('click', async function () {
                    const questionId = this.dataset.questionId;
                    let isBookmarked = this.dataset.bookmarked === 'true';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const bookmarkIcon = document.getElementById('bookmark-icon');

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
            }
        });
    </script>
    @endif {{-- !Auth::user()->isAdmin() の閉じタグ --}}
    @endauth
</x-app-layout>
