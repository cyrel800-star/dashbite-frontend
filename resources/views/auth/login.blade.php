<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name', 'FoodDash') }}</title>

    {{-- FORCE LIGHT MODE --}}
    <script>
    (function() {
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.remove('dark');
    })();
    </script>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF6B35">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">

    <link rel="stylesheet" href="{{ asset('food-dash/app.css') }}">
    <script type="module" src="{{ asset('food-dash/app.js') }}"></script>

    <style>[x-cloak] { display: none !important; }</style>
</head>

{{--
    FOODDASH COLORS
    Orange #FF6B35 | Dark Gray #2D2D2D | White #FFFFFF
--}}
<body class="relative min-h-screen overflow-x-hidden bg-neutral-100 text-[#2D2D2D]">

    @php $config = \App\Models\SystemConfig::current(); @endphp

    {{-- ============================================ --}}
    {{-- FLOATING LOGIN PANEL --}}
    {{-- ============================================ --}}
    <div class="relative flex min-h-screen items-center justify-center p-4 sm:p-6">

        <div class="relative grid w-full max-w-4xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-black/40 md:grid-cols-2">

            {{-- CLOSE BUTTON --}}
            <a href="{{ route('customer.restaurants') }}"
               aria-label="Close"
               title="Close"
               class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-[#2D2D2D]/15 bg-white text-[#2D2D2D] shadow-sm transition hover:border-[#FF6B35] hover:text-[#FF6B35] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#FF6B35]/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>

            {{-- ======================================== --}}
            {{-- LEFT: BRAND PANEL (dark gray) --}}
            {{-- ======================================== --}}
            <aside class="relative flex flex-col justify-between overflow-hidden bg-[#2D2D2D] px-6 py-8 text-white md:px-10 md:py-10">

                <div class="absolute -top-20 -left-20 h-56 w-56 rounded-full bg-[#FF6B35]"></div>
                <div class="absolute -bottom-24 -right-16 h-64 w-64 rounded-full border-[24px] border-[#FF6B35]/25"></div>

                {{-- Brand --}}
                <div class="relative">
                    <a href="{{ route('customer.restaurants') }}" class="inline-flex items-center gap-3">
                        @if ($config->hasLogo())
                            <span class="inline-flex rounded-2xl bg-white p-2">
                                <img src="{{ $config->logo_url }}"
                                     alt="{{ config('app.name', 'FoodDash') }}"
                                     class="w-auto"
                                     style="height: {{ max(36, $config->logo_height_px) }}px;">
                            </span>
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#FF6B35]">
                                <span class="text-2xl">🍕</span>
                            </div>
                            <span class="text-2xl font-extrabold text-white">
                                {{ config('app.name', 'FoodDash') }}
                            </span>
                        @endif
                    </a>
                </div>

                {{-- Message --}}
                <div class="relative mt-10 md:mt-16">
                    <h1 class="text-3xl font-extrabold leading-tight md:text-4xl">
                        Good Food.<br>
                        <span class="text-[#FF6B35]">Faster Delivery.</span>
                    </h1>
                    <p class="mt-3 max-w-xs text-sm text-white/70">
                        Order from the best local restaurants and get it delivered to your door.
                    </p>

                    <ul class="mt-8 hidden space-y-4 md:block">
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-[#FF6B35]">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-sm"><b class="block">Real-time tracking</b><span class="text-white/60">Know where your food is.</span></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-[#FF6B35]">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </span>
                            <span class="text-sm"><b class="block">Safe and secure</b><span class="text-white/60">Your information is protected.</span></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-[#FF6B35]">
                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <span class="text-sm"><b class="block">24/7 support</b><span class="text-white/60">We're here to help, anytime.</span></span>
                        </li>
                    </ul>
                </div>

                <p class="relative mt-8 hidden text-xs text-white/50 md:block">
                    © {{ date('Y') }} {{ config('app.name', 'FoodDash') }}. All rights reserved.
                </p>
            </aside>

            {{-- ======================================== --}}
            {{-- RIGHT: LOGIN FORM (white) --}}
            {{-- ======================================== --}}
            <main class="flex items-center bg-white px-6 py-8 md:px-10 md:py-12">
                <div class="w-full">

                    <div class="mb-6">
                        <h2 class="text-3xl font-extrabold text-[#2D2D2D]">Welcome!</h2>
                        <p class="mt-1 text-sm text-[#2D2D2D]/60">Sign in to continue ordering.</p>
                    </div>

                    {{-- ALERTS --}}
                    @if (session('success'))
                        <div class="mb-5 flex items-start gap-2 rounded-xl border-l-4 border-[#FF6B35] bg-[#FF6B35]/10 p-3 text-sm text-[#2D2D2D]">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 flex items-start gap-2 rounded-xl border-l-4 border-[#FF6B35] bg-[#2D2D2D] p-3 text-sm text-white">
                            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="font-medium">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    {{-- LOGIN FORM --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                        @csrf

                        {{-- EMAIL --}}
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-[#2D2D2D]">Email address</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <svg class="h-5 w-5 text-[#2D2D2D]/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       placeholder="you@example.com"
                                       class="w-full rounded-xl border-2 border-[#2D2D2D]/15 bg-white py-3 pl-11 pr-4 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/40 transition focus:border-[#FF6B35] focus:outline-none focus:ring-4 focus:ring-[#FF6B35]/20">
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="password" class="block text-sm font-semibold text-[#2D2D2D]">Password</label>
                                <a href="{{ route('password.request') }}"
                                   class="text-xs font-bold text-[#2D2D2D] underline decoration-[#FF6B35] decoration-2 underline-offset-4 transition hover:text-[#FF6B35]">
                                    Forgot password?
                                </a>
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <svg class="h-5 w-5 text-[#2D2D2D]/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'"
                                       id="password"
                                       name="password"
                                       required
                                       placeholder="••••••••"
                                       class="w-full rounded-xl border-2 border-[#2D2D2D]/15 bg-white py-3 pl-11 pr-11 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/40 transition focus:border-[#FF6B35] focus:outline-none focus:ring-4 focus:ring-[#FF6B35]/20">

                                <button type="button"
                                        @click="showPassword = !showPassword"
                                        aria-label="Show or hide password"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#2D2D2D]/40 transition hover:text-[#FF6B35]">
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- REMEMBER ME --}}
                        <div class="flex items-center">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="h-4 w-4 rounded border-[#2D2D2D]/30 text-[#FF6B35] focus:ring-[#FF6B35]">
                            <label for="remember" class="ml-2 select-none text-sm text-[#2D2D2D]/70">Remember me</label>
                        </div>

                        {{-- SIGN IN (orange) --}}
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#FF6B35] py-3.5 text-sm font-bold text-white shadow-md shadow-[#FF6B35]/30 transition hover:brightness-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-[#2D2D2D]/30 active:scale-[0.98]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Sign in
                        </button>
                    </form>

                    {{-- DIVIDER --}}
                    <div class="relative my-5">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-[#2D2D2D]/10"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="bg-white px-3 font-medium text-[#2D2D2D]/50">or</span>
                        </div>
                    </div>

                    {{-- CONTINUE AS GUEST (dark outline) --}}
                    <a href="{{ route('customer.restaurants') }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-[#2D2D2D] py-3 text-sm font-bold text-[#2D2D2D] transition hover:bg-[#2D2D2D] hover:text-white focus:outline-none focus-visible:ring-4 focus-visible:ring-[#FF6B35]/40 active:scale-[0.98]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Continue as guest
                    </a>

                    <p class="mt-3 text-center text-xs text-[#2D2D2D]/50">
                        Browse restaurants and menus. Sign in only when you order.
                    </p>

                    {{-- REGISTER --}}
                    <p class="mt-6 text-center text-sm text-[#2D2D2D]/70">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                           class="font-bold text-[#2D2D2D] underline decoration-[#FF6B35] decoration-2 underline-offset-4 transition hover:text-[#FF6B35]">
                            Create one now
                        </a>
                    </p>

                    <p class="mt-6 text-center text-xs text-[#2D2D2D]/40 md:hidden">
                        © {{ date('Y') }} {{ config('app.name', 'FoodDash') }}. All rights reserved.
                    </p>
                </div>
            </main>
        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>