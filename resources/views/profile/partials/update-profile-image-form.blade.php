{{-- resources/views/profile/partials/update-profile-image-form.blade.php --}}

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール画像') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('あなたのプロフィール画像を設定または変更します。') }}
        </p>
    </header>

    {{-- プロフィール画像の表示 --}}
    <div class="mt-6">
        @if (Auth::user()->profile_image_path)
            <img src="{{ Storage::url(Auth::user()->profile_image_path) }}" alt="プロフィール画像" class="w-24 h-24 rounded-full object-cover">
        @else
            <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
        @endif
    </div>

    {{-- プロフィール画像変更フォーム --}}
    <form method="post" action="{{ route('profile.updateImage') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch') {{-- PUT/PATCHメソッドを使用 --}}

        <div>
            <x-input-label for="profile_image" :value="__('新しいプロフィール画像')" />
            <input id="profile_image" name="profile_image" type="file" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>
        </div>
    </form>

    {{-- プロフィール画像削除フォーム (既存の画像がある場合のみ表示) --}}
    @if (Auth::user()->profile_image_path)
        <form method="post" action="{{ route('profile.deleteImage') }}" class="mt-6">
            @csrf
            @method('delete')

            <x-danger-button>{{ __('プロフィール画像を削除') }}</x-danger-button>
        </form>
    @endif
</section>