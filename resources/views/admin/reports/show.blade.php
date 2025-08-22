<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- 報告詳細の表示 --}}
                    <h2 class="text-2xl font-bold mb-4">報告詳細</h2>

                    <div class="mb-4">
                        <p><strong>報告ID:</strong> {{ $report->id }}</p>
                        <p><strong>報告者:</strong> <a href="{{ route('admin.users.index', ['user_id' => $report->reporter->id]) }}" class="text-blue-500 hover:text-blue-700">{{ $report->reporter->name }}</a></p>
                        <p><strong>報告された投稿タイプ:</strong>
                            @if ($report->reportable_type === 'App\Models\Answer')
                                回答
                            @elseif ($report->reportable_type === 'App\Models\Question')
                                質問
                            @else
                                不明
                            @endif
                        </p>
                        <p><strong>報告された理由:</strong> {{ $report->reason }}</p>
                        <p><strong>報告日時:</strong> {{ $report->created_at->format('Y/m/d H:i') }}</p>
                    </div>

                    {{-- 報告された投稿内容の表示 --}}
                    @if ($report->reportable)
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-xl font-semibold mb-2">報告された投稿内容</h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-600 mb-2"><strong>投稿者:</strong> <a href="{{ route('admin.users.index', ['user_id' => $report->reportable->user->id]) }}" class="text-blue-500 hover:text-blue-700">{{ $report->reportable->user->name }}</a></p>
                                <p class="text-gray-800">
                                    {{ $report->reportable->content }}
                                </p>
                            </div>
                        </div>

                        {{-- 管理者用「表示停止/表示する」ボタン --}}
                        @if (Auth::user()->isAdmin())
                            <div class="mt-4 flex space-x-2">
                                @if ($report->reportable_type === 'App\Models\Answer')
                                    {{-- 回答の場合の表示/非表示ボタン --}}
                                    @if ($report->reportable->is_hidden)
                                        <form action="{{ route('admin.answers.unhide', $report->reportable) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                回答を表示する
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.answers.hide', $report->reportable) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                回答を非表示にする
                                            </button>
                                        </form>
                                    @endif
                                @elseif ($report->reportable_type === 'App\Models\Question')
                                    {{-- 質問の場合の表示/非表示ボタン --}}
                                    @if ($report->reportable->is_hidden)
                                        <form action="{{ route('admin.questions.unhide', $report->reportable) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                質問を表示する
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.questions.hide', $report->reportable) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                質問を非表示にする
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @endif
                    @else
                        <p class="mt-4 text-red-500">この報告に関連する投稿は見つかりませんでした。</p>
                    @endif

                    <div class="mt-8 flex justify-between">
                        <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:text-blue-700">← 報告一覧に戻る</a>

                        {{-- 報告を削除するボタン --}}
                        <form action="{{ route('admin.reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('本当にこの報告を削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                この報告を削除
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
