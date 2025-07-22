{{-- resources/views/admin/reports/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('違反報告詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">違反報告 #{{ $report->id }}</h3>

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

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">報告日時: {{ $report->created_at->format('Y/m/d H:i') }}</p>
                        <p class="text-sm text-gray-600">報告者: {{ $report->user->name ?? '不明なユーザー' }}</p>
                        <p class="text-sm text-gray-600">報告理由: {{ $report->reason }}</p>
                        @if ($report->comment)
                            <p class="text-sm text-gray-600">コメント: {{ $report->comment }}</p>
                        @endif
                        <p class="text-sm text-gray-600">ステータス: {{ $report->status }}</p>
                        <p class="text-sm text-gray-600">この報告対象の合計報告数: {{ $totalReportCount }} 件</p> {{-- ★追加 --}}
                    </div>

                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-2">報告対象の投稿</h4>
                        @if ($report->reportable)
                            <div class="border p-4 rounded-lg bg-gray-50">
                                @if ($report->reportable_type === App\Models\Question::class)
                                    <p class="text-sm text-gray-600">タイプ: 質問</p>
                                    <p class="text-lg font-semibold">{{ $report->reportable->title }}</p>
                                    <p class="text-gray-700">{{ $report->reportable->body }}</p>
                                    @if ($report->reportable->image_path)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $report->reportable->image_path) }}" alt="質問画像" class="max-w-xs h-auto rounded">
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-2">現在の表示状態: {{ $report->reportable->is_visible ? '表示中' : '非表示' }}</p>
                                    <a href="{{ route('questions.show', $report->reportable->id) }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">質問詳細を見る</a>
                                @elseif ($report->reportable_type === App\Models\Answer::class)
                                    <p class="text-sm text-gray-600">タイプ: 回答</p>
                                    <p class="text-gray-700">{{ $report->reportable->content }}</p>
                                    @if ($report->reportable->image_path)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $report->reportable->image_path) }}" alt="回答画像" class="max-w-xs h-auto rounded">
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-2">現在の表示状態: {{ $report->reportable->is_visible ? '表示中' : '非表示' }}</p>
                                    <a href="{{ route('questions.show', $report->reportable->question_id) }}#answer-{{ $report->reportable->id }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">回答詳細を見る</a>
                                @else
                                    <p>報告対象の投稿が見つからないか、不明なタイプです。</p>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-700">この違反報告の対象投稿は既に削除されています。</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- 投稿の表示停止/再開ボタン --}}
                        @if ($report->reportable)
                            <form action="{{ route('admin.reports.toggleVisibility', ['type' => $report->reportable_type === App\Models\Question::class ? 'question' : 'answer', 'id' => $report->reportable->id]) }}" method="POST" onsubmit="return confirm('本当にこの投稿の表示状態を切り替えますか？');">
                                @csrf
                                @method('POST')
                                <button type="submit" class="px-4 py-2 rounded-md {{ $report->reportable->is_visible ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white">
                                    {{ $report->reportable->is_visible ? '投稿を非表示にする' : '投稿を表示する' }}
                                </button>
                            </form>
                        @endif

                        {{-- 違反報告の削除ボタン --}}
                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('本当にこの違反報告を削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-md bg-gray-500 hover:bg-gray-600 text-white">
                                違反報告を削除
                            </button>
                        </form>

                        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white">
                            一覧に戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
