<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('質問詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $question->title }}</h1>
                    <p class="text-gray-700 mt-2">{{ $question->body }}</p>

                    @if ($question->image_path)
                        <div class="mt-4">
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="質問画像" class="max-w-full h-auto rounded-lg shadow-md">
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-gray-600">
                            投稿者: {{ $question->user->name }} - {{ $question->created_at->diffForHumans() }}
                        </p>
                        <div class="flex items-center space-x-2">
                            {{-- いいねボタン --}}
                            @auth
                                @if (!Auth::user()->isAdmin())
                                    @php
                                        $hasLiked = Auth::user()->likes->contains('id', $question->id);
                                    @endphp
                                    <button id="like-button" data-question-id="{{ $question->id }}" data-liked="{{ $hasLiked ? 'true' : 'false' }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150
                                        {{ $hasLiked ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                        <span id="like-icon" class="text-lg">
                                            {{ $hasLiked ? '❤️' : '🤍' }}
                                        </span>
                                        <span id="like-count" class="ml-1">{{ $question->likes()->count() }}</span>
                                    </button>
                                @endif
                            @endauth

                            {{-- ブックマークボタン (Ajaxによる非同期通信) --}}
                            @auth
                                @if (!Auth::user()->isAdmin())
                                    @php
                                        $isBookmarked = Auth::user()->bookmarks->contains('id', $question->id);
                                    @endphp
                                    <button
                                        id="bookmark-button"
                                        data-question-id="{{ $question->id }}"
                                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150
                                        {{ $isBookmarked ? 'bg-blue-500 text-white hover:bg-blue-600' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                                    >
                                        <span id="bookmark-icon" class="text-lg">
                                            {{ $isBookmarked ? '🔖' : '📖' }}
                                        </span>
                                        <span id="bookmark-text" class="ml-1">{{ $isBookmarked ? 'ブックマーク済み' : 'ブックマーク' }}</span>
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end">
                        {{-- 違反報告数表示 --}}
                        @if (isset($question->reports_count) && $question->reports_count > 0)
                            <span class="text-sm font-semibold text-red-600">
                                報告数: {{ $question->reports_count }} 件
                            </span>
                        @endif

                        {{-- 質問の表示切替ボタン（管理者のみ） --}}
                        @auth
                            @if (Auth::user()->isAdmin())
                                <form action="{{ route('admin.reports.toggle-visibility', ['reportableType' => 'question', 'reportableId' => $question->id]) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('本当にこの質問の表示状態を切り替えますか？');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow-md
                                        {{ $question->is_hidden ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                                        {{ $question->is_hidden ? '表示に戻す' : '非表示にする' }}
                                    </button>
                                </form>
                            @endif
                        @endauth

                        {{-- 質問の違反報告機能 --}}
                        @auth
                            {{-- 自分の投稿ではなく、かつ管理ユーザーでない場合にのみ表示 --}}
                            @if (Auth::id() !== $question->user_id && !Auth::user()->isAdmin())
                                <button x-on:click="$dispatch('open-report-modal', { reportableType: 'question', reportableId: {{ $question->id }} })"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150 ml-2">
                                    {{ __('違反報告') }}
                                </button>
                            @endif
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
            <div id="answers-list">
                @forelse ($question->answers as $answer)
                    <div class="answer-card bg-gray-50 p-4 rounded-lg shadow-sm mb-4 {{ $answer->id === $question->best_answer_id ? 'border-2 border-green-500' : '' }}@if($answer->is_reported) border-2 border-red-500 bg-red-100 @endif">
                        <p class="text-gray-700">{{ $answer->content }}</p>

                        {{-- 回答画像表示 --}}
                        @if ($answer->image_path)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $answer->image_path) }}" alt="回答画像" class="max-w-full h-auto rounded-lg shadow-md">
                            </div>
                        @endif

                        <p class="text-right text-sm text-gray-600">
                            回答者: {{ $answer->user->name }} - {{ $answer->created_at->diffForHumans() }}
                            {{-- 各回答にもベストアンサータグを追加 --}}
                            @if ($answer->id === $question->best_answer_id)
                                <span class="ml-2 px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-full">ベストアンサー</span>
                            @endif
                        </p>

                        {{-- 回答の違反報告数表示 --}}
                        <div class="flex items-center justify-end">
                            @if (isset($answer->reports_count) && $answer->reports_count > 0)
                                <span class="text-sm font-semibold text-red-600">
                                    報告数: {{ $answer->reports_count }} 件
                                </span>
                            @endif

                            <div class="mt-2 flex space-x-2 justify-end">
                                {{-- 回答の表示切替ボタン（管理者のみ） --}}
                                @auth
                                    @if (Auth::user()->isAdmin())
                                        <form action="{{ route('admin.reports.toggle-visibility', ['reportableType' => 'answer', 'reportableId' => $answer->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('本当にこの回答の表示状態を切り替えますか？');">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 shadow-md
                                                {{ $answer->is_hidden ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                                                {{ $answer->is_hidden ? '表示に戻す' : '非表示にする' }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth

                                {{-- 回答の編集・削除ボタン --}}
                                @auth
                                    {{-- 自分の投稿者で、かつ管理ユーザーでない場合にのみ表示 --}}
                                    @if (Auth::id() === $answer->user_id && !Auth::user()->isAdmin())
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
                                    @endif
                                @endauth

                                {{-- 回答の違反報告機能 --}}
                                @auth
                                    {{-- 自分の投稿ではなく、かつ管理ユーザーでない場合にのみ表示 --}}
                                    @if (Auth::id() !== $answer->user_id && !Auth::user()->isAdmin())
                                        <button x-on:click="$dispatch('open-report-modal', { reportableType: 'answer', reportableId: {{ $answer->id }} })"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                                            {{ __('違反報告') }}
                                        </button>
                                    @endif
                                @endauth

                                {{-- ベストアンサー選定ボタン --}}
                                {{-- 管理ユーザーでない場合にのみ表示 --}}
                                @if (Auth::check() && Auth::id() === $question->user_id && !$question->best_answer_id && !Auth::user()->isAdmin())
                                    <form action="{{ route('answers.markAsBestAnswer', ['question' => $question->id, 'answer' => $answer->id]) }}" method="POST" class="ml-2">
                                        @csrf
                                        <x-primary-button type="submit" class="text-green-600 hover:text-green-800 text-sm">ベストアンサーに選ぶ</x-primary-button>
                                    </form>
                                @endif
                            </div>
                        </div>

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
            </div>

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
                // いいねボタンの処理
                const likeButton = document.getElementById('like-button');
                if (likeButton) {
                    likeButton.addEventListener('click', async function () {
                        const questionId = this.dataset.questionId;
                        let isLiked = this.dataset.liked === 'true';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const likeIcon = document.getElementById('like-icon');
                        const likeCountSpan = document.getElementById('like-count');

                        const url = isLiked ? `/questions/${questionId}/unlike` : `/questions/${questionId}/like`;
                        const method = isLiked ? 'DELETE' : 'POST';

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
                            }
                        } catch (error) {
                            console.error('ネットワークエラー:', error);
                        }
                    });
                }

                // ブックマークボタンの処理
                const bookmarkButton = document.getElementById('bookmark-button');
                if (bookmarkButton) {
                    bookmarkButton.addEventListener('click', async function () {
                        const questionId = this.dataset.questionId;
                        let isBookmarked = this.dataset.bookmarked === 'true';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const bookmarkIcon = document.getElementById('bookmark-icon');
                        const bookmarkText = document.getElementById('bookmark-text');

                        const url = `/questions/${questionId}/bookmark`;
                        const method = isBookmarked ? 'DELETE' : 'POST';

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
                                    bookmarkText.textContent = 'ブックマーク済み';
                                    this.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                    this.classList.add('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                                } else {
                                    bookmarkIcon.textContent = '📖';
                                    bookmarkText.textContent = 'ブックマーク';
                                    this.classList.remove('bg-blue-500', 'text-white', 'hover:bg-blue-600');
                                    this.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                                }
                                console.log(data.message);
                            } else {
                                console.error('APIエラー:', data.message || '不明なエラー');
                            }
                        } catch (error) {
                            console.error('ネットワークエラー:', error);
                        }
                    });
                }
            });
        </script>
    @endif
    @endauth
</x-app-layout>
