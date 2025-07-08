<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    {{-- ★変更: トップページへのリンクを質問一覧に --}}
                    <a href="{{ route('questions.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- ★追加: 質問一覧リンク --}}
                    <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                        {{ __('質問一覧') }}
                    </x-nav-link>

                    @auth
                        {{-- ★追加・修正: 質問を投稿するリンク --}}
                        <x-nav-link :href="route('questions.create')" :active="request()->routeIs('questions.create')">
                            {{ __('質問を投稿する') }}
                        </x-nav-link>

                        {{-- ★追加: マイページリンク --}}
                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                            {{ __('マイページ') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            {{-- ★ここが変更点です！ sm:flex sm:items-center sm:ms-6 のdiv全体を@auth/@elseで囲みます --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    {{-- ログイン済みユーザー向けのドロップダウンメニュー --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                {{-- ★変更: 認証ユーザー名または「ゲスト」を表示 --}}
                                <div>{{ Auth::user()->name ?? 'ゲスト' }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- 未ログインユーザー向けのリンクをここに追加 --}}
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">ログイン</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">新規登録</a>
                    @endif
                @endauth
            </div>

            {{-- ハンバーガーメニューのトグルボタン --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- レスポンシブナビゲーション（モバイル用） --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            {{-- ★追加: レスポンシブ用質問一覧リンク --}}
            <x-responsive-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                {{ __('質問一覧') }}
            </x-responsive-nav-link>

            @auth
                {{-- ★追加・修正: レスポンシブ用質問を投稿するリンク --}}
                <x-responsive-nav-link :href="route('questions.create')" :active="request()->routeIs('questions.create')">
                    {{ __('質問を投稿する') }}
                </x-responsive-nav-link>

                {{-- ★追加: レスポンシブ用マイページリンク --}}
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ __('マイページ') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                {{-- ★変更: 認証ユーザー名または「ゲスト」を表示 --}}
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name ?? 'ゲスト' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email ?? '' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>