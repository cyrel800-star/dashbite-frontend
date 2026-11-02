<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register — {{ config('app.name', 'DashBite') }}</title>

    {{-- Force light mode — plain white, no dark adaptation --}}
    <script>
    (function() {
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.remove('dark');
    })();
    </script>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff6b35">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">

    <link rel="stylesheet" href="{{ asset('food-dash/app.css') }}">
    <script type="module" src="{{ asset('food-dash/app.js') }}"></script>
</head>
<body class="bg-zinc-50 min-h-screen flex items-center justify-center py-10 px-4">

    @php $config = \App\Models\SystemConfig::current(); @endphp

    <div class="w-full max-w-md">

        {{-- ============================================ --}}
        {{-- LOGO / BRAND --}}
        {{-- ============================================ --}}
        <div class="text-center mb-8">
            <a href="{{ route('customer.restaurants') }}" class="inline-flex items-center justify-center">
                @if ($config->hasLogo())
                    <img src="{{ $config->logo_url }}"
                         alt="{{ config('app.name', 'DashBite') }}"
                         class="w-auto max-h-20 object-contain"
                         style="height: {{ max(56, $config->logo_height_px) }}px;">
                @else
                    <div class="flex items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/30">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="font-bold text-3xl text-zinc-900 tracking-tight">
                            {{ config('app.name', 'DashBite') }}
                        </span>
                    </div>
                @endif
            </a>
            <p class="text-sm text-zinc-500 mt-4">
                Create your account to get started.
            </p>
        </div>

        {{-- ============================================ --}}
        {{-- MAIN CARD --}}
        {{-- ============================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 overflow-hidden"
             x-data="{ role: '{{ old('role', 'customer') }}', showPassword: false, showConfirmPassword: false }">

            <div class="p-6 sm:p-8">

                {{-- ============================================ --}}
                {{-- ALERTS --}}
                {{-- ============================================ --}}
                @if ($errors->any())
                    <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ============================================ --}}
                {{-- REGISTER FORM --}}
                {{-- ============================================ --}}
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- ============================================ --}}
                    {{-- ROLE SELECTOR --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-2 uppercase tracking-wide">
                            Account Type
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            {{-- Customer --}}
                            <label class="cursor-pointer group">
                                <input type="radio"
                                       name="role"
                                       value="customer"
                                       x-model="role"
                                       class="peer sr-only">
                                <div class="border-2 border-zinc-200 rounded-xl p-3 text-center transition
                                            peer-checked:border-orange-500 peer-checked:bg-orange-50
                                            hover:border-zinc-300">
                                    <svg class="w-6 h-6 mx-auto mb-1.5 text-zinc-400 peer-checked:text-orange-500 transition"
                                         :class="role === 'customer' ? 'text-orange-500' : 'text-zinc-400'"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="text-xs font-semibold transition"
                                       :class="role === 'customer' ? 'text-orange-600' : 'text-zinc-600'">
                                        Customer
                                    </p>
                                </div>
                            </label>

                            {{-- Restaurant --}}
                            <label class="cursor-pointer group">
                                <input type="radio"
                                       name="role"
                                       value="restaurant"
                                       x-model="role"
                                       class="peer sr-only">
                                <div class="border-2 border-zinc-200 rounded-xl p-3 text-center transition
                                            peer-checked:border-orange-500 peer-checked:bg-orange-50
                                            hover:border-zinc-300">
                                    <svg class="w-6 h-6 mx-auto mb-1.5 transition"
                                         :class="role === 'restaurant' ? 'text-orange-500' : 'text-zinc-400'"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-xs font-semibold transition"
                                       :class="role === 'restaurant' ? 'text-orange-600' : 'text-zinc-600'">
                                        Restaurant
                                    </p>
                                </div>
                            </label>

                            {{-- Rider --}}
                            <label class="cursor-pointer group">
                                <input type="radio"
                                       name="role"
                                       value="rider"
                                       x-model="role"
                                       class="peer sr-only">
                                <div class="border-2 border-zinc-200 rounded-xl p-3 text-center transition
                                            peer-checked:border-orange-500 peer-checked:bg-orange-50
                                            hover:border-zinc-300">
                                    <svg class="w-6 h-6 mx-auto mb-1.5 transition"
                                         :class="role === 'rider' ? 'text-orange-500' : 'text-zinc-400'"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    <p class="text-xs font-semibold transition"
                                       :class="role === 'rider' ? 'text-orange-600' : 'text-zinc-600'">
                                        Rider
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- NAME --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   placeholder="Juan Dela Cruz"
                                   class="w-full border border-zinc-300 rounded-xl pl-11 pr-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- EMAIL --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   placeholder="you@example.com"
                                   class="w-full border border-zinc-300 rounded-xl pl-11 pr-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- PHONE --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                            Phone Number
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="09171234567"
                                   class="w-full border border-zinc-300 rounded-xl pl-11 pr-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- RESTAURANT FIELDS (conditional) --}}
                    {{-- ============================================ --}}
                    <div x-show="role === 'restaurant'"
                         x-cloak
                         x-transition
                         class="space-y-4 p-4 bg-orange-50 border border-orange-200 rounded-xl">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-xs font-bold text-orange-700 uppercase tracking-wide">
                                Restaurant Information
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                                Restaurant Name
                            </label>
                            <input type="text"
                                   name="restaurant_name"
                                   value="{{ old('restaurant_name') }}"
                                   placeholder="e.g. PizzaKid"
                                   class="w-full border border-zinc-300 rounded-xl px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition bg-white">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                                Restaurant Address
                            </label>
                            <input type="text"
                                   name="restaurant_address"
                                   value="{{ old('restaurant_address') }}"
                                   placeholder="Street, city"
                                   class="w-full border border-zinc-300 rounded-xl px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition bg-white">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                                    Latitude
                                </label>
                                <input type="text"
                                       name="latitude"
                                       value="{{ old('latitude') }}"
                                       placeholder="8.4822"
                                       class="w-full border border-zinc-300 rounded-xl px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                                    Longitude
                                </label>
                                <input type="text"
                                       name="longitude"
                                       value="{{ old('longitude') }}"
                                       placeholder="124.6472"
                                       class="w-full border border-zinc-300 rounded-xl px-4 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition bg-white">
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- PASSWORD --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   minlength="8"
                                   placeholder="Minimum 8 characters"
                                   class="w-full border border-zinc-300 rounded-xl pl-11 pr-12 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">

                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-orange-500 transition"
                                    :title="showPassword ? 'Hide password' : 'Show password'">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- CONFIRM PASSWORD --}}
                    {{-- ============================================ --}}
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-1.5 uppercase tracking-wide">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <input :type="showConfirmPassword ? 'text' : 'password'"
                                   name="password_confirmation"
                                   required
                                   placeholder="Re-enter password"
                                   class="w-full border border-zinc-300 rounded-xl pl-11 pr-12 py-3 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">

                            <button type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-orange-500 transition"
                                    :title="showConfirmPassword ? 'Hide password' : 'Show password'">
                                <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showConfirmPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- SUBMIT --}}
                    {{-- ============================================ --}}
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Create Account
                    </button>
                </form>

                {{-- ============================================ --}}
                {{-- INFO --}}
                {{-- ============================================ --}}
                <div class="mt-5 p-3 bg-zinc-50 border border-zinc-200 rounded-xl flex gap-2">
                    <svg class="w-4 h-4 text-zinc-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-zinc-600 leading-relaxed">
                        Restaurant and rider accounts require admin approval before you can log in.
                        Customers can start ordering right away.
                    </p>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CARD FOOTER --}}
            {{-- ============================================ --}}
            <div class="px-6 sm:px-8 py-5 bg-zinc-50 border-t border-zinc-100 text-center">
                <p class="text-sm text-zinc-600">
                    Already have an account?
                    <a href="{{ route('login') }}"
                       class="font-bold text-orange-600 hover:text-orange-700 transition">
                        Sign In
                    </a>
                </p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- FOOTER --}}
        {{-- ============================================ --}}
        <p class="text-center text-xs text-zinc-400 mt-6">
            © {{ date('Y') }} {{ config('app.name', 'DashBite') }} · All rights reserved
        </p>

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>