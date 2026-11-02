<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        (() => {
            const theme = localStorage.getItem('theme');
            document.documentElement.classList.toggle(
                'dark',
                theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)
            );
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff6b35">
    <title>DashBite | Local food, nearby</title>
    @vite(['resources/css/app.css', 'resources/js/customer.js'])
    @stack('styles')
</head>
<body class="customer-shell min-h-screen bg-neutral-50 text-neutral-900 transition-colors dark:bg-[#0a0a0a] dark:text-white">
    <header class="customer-header sticky top-0 z-40 border-b border-neutral-200 bg-white/95 backdrop-blur dark:border-[#262626] dark:bg-[#141414]/95">
        <div class="mx-auto flex h-[4.5rem] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('customer.restaurants') }}" class="brand-lockup flex items-center gap-2.5 text-lg font-extrabold tracking-tight text-neutral-900 dark:text-white">
                <span class="brand-mark grid h-9 w-9 place-items-center rounded-xl bg-orange-600 text-white shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 10h16l-1.6 9H5.6L4 10Zm3-3.5h10M8 4h8m-8 9v2m4-2v2m4-2v2" />
                    </svg>
                </span>
                <span>Dash<span class="text-orange-600 dark:text-orange-400">Bite</span></span>
            </a>
            <nav class="flex items-center gap-2 text-sm sm:gap-4">
                <a href="{{ route('customer.restaurants') }}" class="nav-current rounded-full px-3 py-2 font-semibold text-orange-600 transition dark:text-orange-400">Explore</a>
                <div x-data="themeToggle">
                    <button type="button"
                            @click="setTheme(theme === 'dark' ? 'light' : 'dark')"
                            :aria-label="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                            :aria-pressed="theme === 'dark'"
                            :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                            class="theme-toggle-button flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 bg-white text-neutral-700 transition hover:border-orange-400 dark:border-white/20 dark:bg-[#2D2D2D] dark:text-white">
                        <svg x-show="theme !== 'dark'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="theme === 'dark'" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
                @auth
                    <span class="hidden text-neutral-500 dark:text-neutral-400 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-neutral-200 px-4 py-2 font-semibold transition hover:border-orange-400 hover:text-orange-700 dark:border-[#404040] dark:hover:text-orange-300">
                            Sign out <span aria-hidden="true">↗</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full bg-[#2D2D2D] px-4 py-2.5 font-semibold text-white transition hover:bg-[#FF6B35] dark:bg-white dark:text-neutral-950 dark:hover:bg-[#FF6B35] dark:hover:text-white">Sign in <span aria-hidden="true">↗</span></a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-7 sm:px-6 sm:py-10">
        @yield('content')
    </main>

    @stack('scripts')
    <div id="portal-app" hidden></div>
</body>
</html>
