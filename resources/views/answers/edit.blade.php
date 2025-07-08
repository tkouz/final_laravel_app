{{-- resources/views/answers/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('回答を編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-semibold mb-6">{{ __('回答を編集する') }}</h3>

                    <x-validation-errors class="mb-4" />

                    {{-- 回答更新フォーム --}}
                    {{-- ★ここを修正: enctype="multipart/form-data" を追加 --}}
                    <form method="POST" action="{{ route('answers.update', $answer) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Content -->
                        <div>
                            <x-input-label for="content" :value="__('回答内容')" />
                            <x-textarea-input id="content" class="block mt-1 w-full" name="content" rows="10" required>{{ old('content', $answer->content) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Image Attachment -->
                        {{-- ★ここから追加: 画像添付フィールドと既存画像表示 --}}
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('画像添付 (任意)')" />
                            @if ($answer->image_path)
                                <div class="mt-2 mb-2">
                                    <p class="text-sm text-gray-600">現在の画像:</p>
                                    <img src="{{ Storage::url($answer->image_path) }}" alt="回答画像" class="max-w-xs h-auto rounded-lg shadow-md mt-1">
                                    <div class="flex items-center mt-2">
                                        <input type="checkbox" name="remove_image" id="remove_image" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                        <label for="remove_image" class="ml-2 text-sm text-gray-600">{{ __('現在の画像を削除する') }}</label>
                                    </div>
                                </div>
                            @endif
                            <input id="image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="image" /> {{-- 編集では画像は任意 --}}
                            <p class="mt-1 text-sm text-gray-500" id="file_input_help">PNG, JPG, JPEG (最大2MB)</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        {{-- ★ここまで追加 --}}

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('更新') }}
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- 削除フォームと戻るボタンを独立させる --}}
                    <div class="flex items-center justify-between mt-4">
                        {{-- 削除ボタン --}}
                        <form action="{{ route('answers.destroy', $answer) }}" method="POST" onsubmit="return confirm('本当にこの回答を削除しますか？');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('削除') }}
                            </button>
                        </form>

                        {{-- 関連質問詳細に戻るボタン --}}
                        <a href="{{ route('questions.show', $answer->question) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('質問詳細に戻る') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>