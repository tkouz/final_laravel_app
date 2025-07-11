@props(['id', 'reportableType', 'reportableId'])

<div x-data="{ open: false }" x-show="open" @open-report-modal.window="
    
    console.log('Event received:', $event.detail.reportableType, $event.detail.reportableId); // ★コメントアウトを解除
    console.log('Modal expected:', '{{ $reportableType }}', {{ $reportableId }}); // ★コメントアウトを解除

    // reportableId を数値に変換して比較する
    if ($event.detail.reportableType === '{{ $reportableType }}' && parseInt($event.detail.reportableId) === {{ $reportableId }}) {
        open = true;
    } else {
        open = false; // 異なるIDのモーダルが開かないようにする
    }
"
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0 scale-95"
x-transition:enter-end="opacity-100 scale-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-95"
class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            違反報告
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                この投稿を不適切であると報告しますか？
                            </p>
                            @if (session('status') === 'report-submitted')
                                <div class="text-green-600 text-sm mt-2">
                                    {{ session('success_message') ?? '違反報告が送信されました。' }}
                                </div>
                            @endif
                            @if ($errors->has('report_error'))
                                <div class="text-red-600 text-sm mt-2">
                                    {{ $errors->first('report_error') }}
                                </div>
                            @endif

                            <form action="{{ route('reports.store') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="reportable_type" value="{{ $reportableType }}">
                                <input type="hidden" name="reportable_id" value="{{ $reportableId }}">

                                <div class="mb-4">
                                    <label for="reason" class="block text-sm font-medium text-gray-700">報告理由 <span class="text-red-500">*</span></label>
                                    <select id="reason" name="reason" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                                        <option value="">選択してください</option>
                                        <option value="hate_speech">ヘイトスピーチ/差別的表現</option>
                                        <option value="spam">スパム/広告</option>
                                        <option value="harassment">嫌がらせ/誹謗中傷</option>
                                        <option value="inappropriate_content">不適切な内容</option>
                                        <option value="other">その他</option>
                                    </select>
                                    @error('reason')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700">詳細コメント (任意)</label>
                                    <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="具体的な状況を記述してください"></textarea>
                                    @error('comment')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        報告を送信
                                    </button>
                                    <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        キャンセル
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>