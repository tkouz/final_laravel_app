{{-- resources/views/layouts/navigation.blade.php --}}

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    {{-- 管理者なら管理者ダッシュボード、一般ユーザーなら質問一覧へ --}}
                    <a href="{{ Auth::check() && Auth::user()->isAdmin() ? route('admin.dashboard') : route('questions.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if (Auth::user()->isAdmin())
                            {{-- 管理者向けメニュー --}}
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('管理者ダッシュボード') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                                {{ __('違反報告管理') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                                {{ __('ユーザー管理') }}
                            </x-nav-link>
                            {{-- ★ここから追加: 非表示投稿管理リンク (ドロップダウン) --}}
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ __('非表示投稿管理') }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('admin.suspended-questions.index')">
                                        {{ __('非表示の質問') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.suspended-answers.index')">
                                        {{ __('非表示の回答') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                            {{-- ★ここまで追加 --}}
                        @else
                            {{-- 一般ユーザー向けメニュー --}}
                            <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                                {{ __('質問一覧') }}
                            </x-nav-link>
                            <x-nav-link :href="route('questions.create')" :active="request()->routeIs('questions.create')">
                                {{ __('質問を投稿する') }}
                            </x-nav-link>
                            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                                {{ __('マイページ') }}
                            </x-nav-link>
                        @endif
                    @else
                        {{-- 未ログインユーザー向けのリンク --}}
                        <x-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                            {{ __('質問一覧') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    {{-- ログイン済みユーザー向けのドロップダウンメニュー --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (Auth::user()->isAdmin())
                                {{-- 管理者向けドロップダウンメニュー --}}
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('管理者ダッシュボード') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.reports.index')">
                                    {{ __('違反報告管理') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.index')">
                                    {{ __('ユーザー管理') }}
                                </x-dropdown-link>
                                {{-- ★ここから追加: ドロップダウン内の非表示投稿管理リンク --}}
                                <x-dropdown-link :href="route('admin.suspended-questions.index')">
                                    {{ __('非表示の質問') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.suspended-answers.index')">
                                    {{ __('非表示の回答') }}
                                </x-dropdown-link>
                                {{-- ★ここまで追加 --}}
                            @else
                                {{-- 一般ユーザー向けドロップダウンメニュー --}}
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('プロフィール') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('ログアウト') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- 未ログインユーザー向けのリンク --}}
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">ログイン</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ms-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">新規登録</a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if (Auth::user()->isAdmin())
                    {{-- 管理者向けレスポンシブメニュー --}}
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('管理者ダッシュボード') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                        {{ __('違反報告管理') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                        {{ __('ユーザー管理') }}
                    </x-responsive-nav-link>
                    {{-- ★ここから追加: レスポンシブ非表示投稿管理リンク (セクション) --}}
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">{{ __('非表示投稿管理') }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <x-responsive-nav-link :href="route('admin.suspended-questions.index')">
                                {{ __('非表示の質問') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('admin.suspended-answers.index')">
                                {{ __('非表示の回答') }}
                            </x-responsive-nav-link>
                        </div>
                    </div>
                    {{-- ★ここまで追加 --}}
                @else
                    {{-- 一般ユーザー向けレスポンシブメニュー --}}
                    <x-responsive-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                        {{ __('質問一覧') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('questions.create')" :active="request()->routeIs('questions.create')">
                        {{ __('質問を投稿する') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                        {{ __('マイページ') }}
                    </x-responsive-nav-link>
                @endif
            @else
                {{-- 未ログインユーザー向けのレスポンシブリンク --}}
                <x-responsive-nav-link :href="route('questions.index')" :active="request()->routeIs('questions.index')">
                    {{ __('質問一覧') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                @auth
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                @else
                    {{-- 未ログイン時の表示 --}}
                    <div class="font-medium text-base text-gray-800">{{ __('ゲスト') }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ __('ログインしてください') }}</div>
                @endauth
            </div>

            <div class="mt-3 space-y-1">
                @auth
                    @if (Auth::user()->isAdmin())
                        {{-- 管理者向けレスポンシブドロップダウン (設定メニュー内) --}}
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('管理者ダッシュボード') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.index')">
                            {{ __('違反報告管理') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                            {{ __('ユーザー管理') }}
                        </x-responsive-nav-link>
                        {{-- 非表示投稿管理はメインメニューに移動したので、ここでは不要 --}}
                    @else
                        {{-- 一般ユーザー向けレスポンシブドロップダウン (設定メニュー内) --}}
                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('プロフィール') }}
                        </x-responsive-nav-link>
                    @endif
                @endauth

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('ログアウト') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
