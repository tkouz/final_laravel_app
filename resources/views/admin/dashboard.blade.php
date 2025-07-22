<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('管理者ダッシュボード') }}
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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- 違反報告数の多い質問トップ10 --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">違反報告数の多い質問 (Top 10)</h3>
                            @if ($topReportedQuestions->isEmpty())
                                <p class="text-gray-700">現在、違反報告の多い質問はありません。</p>
                            @else
                                <ul class="list-disc pl-5 space-y-2">
                                    @foreach ($topReportedQuestions as $report)
                                        <li>
                                            @if ($report->reportable)
                                                <a href="{{ route('questions.show', $report->reportable->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ Str::limit($report->reportable->title, 50) }}
                                                </a>
                                                <span class="text-gray-500 text-sm"> (報告数: {{ $report->report_count }})</span>
                                            @else
                                                削除済み質問 (報告数: {{ $report->report_count }})
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- 違反報告数の多い回答トップ10 --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">違反報告数の多い回答 (Top 10)</h3>
                            @if ($topReportedAnswers->isEmpty())
                                <p class="text-gray-700">現在、違反報告の多い回答はありません。</p>
                            @else
                                <ul class="list-disc pl-5 space-y-2">
                                    @foreach ($topReportedAnswers as $report)
                                        <li>
                                            @if ($report->reportable)
                                                <a href="{{ route('questions.show', $report->reportable->question_id) }}#answer-{{ $report->reportable->id }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ Str::limit($report->reportable->content, 50) }}
                                                </a>
                                                <span class="text-gray-500 text-sm"> (報告数: {{ $report->report_count }})</span>
                                            @else
                                                削除済み回答 (報告数: {{ $report->report_count }})
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- 質問・回答の停止件数の多いユーザー上位10名 --}}
                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">投稿停止件数の多いユーザー (Top 10)</h3>
                            @if ($topSuspendedUsers->isEmpty())
                                <p class="text-gray-700">現在、投稿停止件数の多いユーザーはいません。</p>
                            @else
                                <ul class="list-disc pl-5 space-y-2">
                                    @foreach ($topSuspendedUsers as $user)
                                        <li>
                                            {{ $user->name }}
                                            <span class="text-gray-500 text-sm">
                                                (停止質問: {{ $user->questions_count }}, 停止回答: {{ $user->answers_count }})
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
