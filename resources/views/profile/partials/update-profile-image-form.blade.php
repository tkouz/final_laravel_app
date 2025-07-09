{{-- resources/views/profile/partials/update-profile-image-form.blade.php --}}

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('プロフィール画像') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('あなたのプロフィール画像を更新します。') }}
        </p>
    </header>

    {{-- 現在のプロフィール画像の表示 --}}
    @if (Auth::user()->profile_image_path)
        <div class="mt-4">
            <img src="{{ Storage::url(Auth::user()->profile_image_path) }}" alt="プロフィール画像" class="h-20 w-20 rounded-full object-cover">
        </div>
    @else
        <div class="mt-4 text-gray-500">
            {{ __('プロフィール画像は設定されていません。') }}
        </div>
    @endif

    <form method="post" action="{{ route('profile.image.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6"> {{-- ★ action のルート名を変更、enctypeを追加 --}}
        @csrf
        @method('patch') {{-- ★ PATCH メソッドに変更 --}}

        <div>
            <x-input-label for="profile_image" :value="__('新しいプロフィール画像')" />
            <input id="profile_image" name="profile_image" type="file" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-image-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('保存されました。') }}</p>
            @endif
        </div>
    </form>

    {{-- プロフィール画像削除フォーム (既存の画像がある場合のみ表示) --}}
    @if (Auth::user()->profile_image_path)
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900">{{ __('プロフィール画像の削除') }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('現在のプロフィール画像を削除します。') }}</p>
            <form method="post" action="{{ route('profile.image.delete') }}" class="mt-3"> {{-- ★ action のルート名を変更 --}}
                @csrf
                @method('delete') {{-- ★ DELETE メソッドに変更 --}}
                <x-danger-button type="submit">{{ __('プロフィール画像を削除') }}</x-danger-button>
            </form>
        </div>
    @endif
</section>