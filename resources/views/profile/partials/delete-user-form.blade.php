<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('アカウント削除') }} {{-- 修正 --}}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('一度アカウントを削除すると、そのリソースとデータはすべて完全に削除されます。アカウントを削除する前に、保持したいデータや情報をダウンロードしてください。') }} {{-- 修正 --}}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('アカウント削除') }}</x-danger-button> {{-- 修正 --}}

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('本当にアカウントを削除しますか？') }} {{-- 修正 --}}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('アカウントを削除すると、そのリソースとデータはすべて完全に削除されます。アカウントを完全に削除することを確認するには、パスワードを入力してください。') }} {{-- 修正 --}}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('パスワード') }}" class="sr-only" /> {{-- 修正 --}}

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('パスワード') }}" {{-- 修正 --}}
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('キャンセル') }} {{-- 修正 --}}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('アカウント削除') }} {{-- 修正 --}}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>