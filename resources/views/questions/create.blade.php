{{-- resources/views/questions/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('質問を投稿') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- ★ここを修正: enctype="multipart/form-data" を追加 --}}
                    <form method="POST" action="{{ route('questions.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('タイトル')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Body -->
                        <div class="mt-4">
                            <x-input-label for="body" :value="__('質問内容')" />
                            <x-textarea-input id="body" class="block mt-1 w-full" name="body" rows="10" required>{{ old('body') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <!-- Image Attachment -->
                        {{-- ★ここから追加: 画像添付フィールド --}}
                        <div class="mt-4">
                            <x-input-label for="image" :value="__('画像添付')" />
                            <input id="image" class="block mt-1 w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" type="file" name="image" required /> {{-- 質問投稿では画像は必須 --}}
                            <p class="mt-1 text-sm text-gray-500" id="file_input_help">PNG, JPG, JPEG (最大2MB)</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        {{-- ★ここまで追加 --}}

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('投稿する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>