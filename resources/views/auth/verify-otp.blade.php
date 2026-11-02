<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email — {{ config('app.name', 'DashBite') }}</title>

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
                Verify your email to continue.
            </p>
        </div>

        {{-- ============================================ --}}
        {{-- MAIN CARD --}}
        {{-- ============================================ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 overflow-hidden">

            <div class="p-6 sm:p-8">

                {{-- HEADER --}}
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-100 to-orange-50 mb-3">
                        <svg class="w-7 h-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h1 class="text-lg font-bold text-zinc-900">Check your email</h1>
                    <p class="text-sm text-zinc-500 mt-1">
                        We sent a 6-digit code to
                    </p>
                    <p class="text-sm font-semibold text-zinc-900 truncate">
                        {{ $email ?? 'your email' }}
                    </p>
                </div>

                {{-- ALERTS --}}
                @if (session('success'))
                    <div class="mb-5 p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- OTP FORM --}}
                <form method="POST" action="{{ route('otp.verify') }}" class="space-y-5" id="otpForm">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 mb-3 text-center uppercase tracking-wide">
                            Verification Code
                        </label>

                        <div class="flex justify-center gap-2">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="0"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="1"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="2"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="3"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="4"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                            <input type="text" inputmode="numeric" maxlength="1" data-otp-index="5"
                                   class="otp-box w-12 h-14 text-center text-xl font-bold text-zinc-900 bg-zinc-50 border-2 border-zinc-200 rounded-xl focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:outline-none transition">
                        </div>

                        {{-- HIDDEN INPUT --}}
                        <input type="hidden" name="otp" id="otpHidden">
                    </div>

                    <button type="submit"
                            id="otpSubmit"
                            disabled
                            class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none disabled:transform-none flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Verify Email
                    </button>
                </form>

                {{-- RESEND --}}
                <form method="POST" action="{{ route('otp.resend') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full text-xs font-semibold text-zinc-500 hover:text-orange-600 transition py-2">
                        Didn't receive the code? <span class="text-orange-600 underline">Resend</span>
                    </button>
                </form>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 sm:px-8 py-4 bg-zinc-50 border-t border-zinc-100 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button onclick="localStorage.removeItem('theme'); document.documentElement.classList.remove('dark');"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-500 hover:text-zinc-700 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Cancel and go back
                    </button>
                </form>
            </div>
        </div>

        {{-- PAGE FOOTER --}}
        <p class="text-center text-xs text-zinc-400 mt-6">
            © {{ date('Y') }} {{ config('app.name', 'DashBite') }} · All rights reserved
        </p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const boxes = document.querySelectorAll('.otp-box');
        const hidden = document.getElementById('otpHidden');
        const submit = document.getElementById('otpSubmit');
        const form = document.getElementById('otpForm');

        // ================================
        // Focus first box on load
        // ================================
        if (boxes.length > 0) {
            boxes[0].focus();
        }

        // ================================
        // Update hidden input + button state
        // ================================
        function syncValue() {
            let value = '';
            boxes.forEach(b => value += b.value);
            hidden.value = value;

            submit.disabled = value.length !== 6;
        }

        // ================================
        // AUTO-JUMP + INPUT HANDLING
        // ================================
        boxes.forEach((box, index) => {

            // INPUT — type 1 digit → jump next
            box.addEventListener('input', function (e) {
                let val = e.target.value.replace(/\D/g, '');

                if (val.length === 0) {
                    e.target.value = '';
                    syncValue();
                    return;
                }

                const digit = val[0];
                e.target.value = digit;

                // Highlight filled box
                box.classList.add('border-orange-500', 'bg-white');
                box.classList.remove('border-zinc-200', 'bg-zinc-50');

                // AUTO-JUMP sa next box
                if (index < boxes.length - 1) {
                    boxes[index + 1].focus();
                }

                syncValue();
            });

            // KEYDOWN — Backspace + Arrows
            box.addEventListener('keydown', function (e) {

                // BACKSPACE
                if (e.key === 'Backspace') {
                    if (box.value) {
                        box.value = '';
                        box.classList.remove('border-orange-500', 'bg-white');
                        box.classList.add('border-zinc-200', 'bg-zinc-50');
                        syncValue();
                        e.preventDefault();
                    } else {
                        if (index > 0) {
                            boxes[index - 1].value = '';
                            boxes[index - 1].classList.remove('border-orange-500', 'bg-white');
                            boxes[index - 1].classList.add('border-zinc-200', 'bg-zinc-50');
                            boxes[index - 1].focus();
                            syncValue();
                            e.preventDefault();
                        }
                    }
                    return;
                }

                // ARROW LEFT
                if (e.key === 'ArrowLeft' && index > 0) {
                    boxes[index - 1].focus();
                    e.preventDefault();
                    return;
                }

                // ARROW RIGHT
                if (e.key === 'ArrowRight' && index < boxes.length - 1) {
                    boxes[index + 1].focus();
                    e.preventDefault();
                    return;
                }

                // ENTER — submit kung complete
                if (e.key === 'Enter' && hidden.value.length === 6) {
                    form.submit();
                }
            });

            // PASTE — auto-fill lahat
            box.addEventListener('paste', function (e) {
                e.preventDefault();

                const text = (e.clipboardData || window.clipboardData).getData('text');
                const pasted = text.replace(/\D/g, '').slice(0, 6);

                if (!pasted) return;

                for (let i = 0; i < boxes.length; i++) {
                    boxes[i].value = pasted[i] || '';
                    if (pasted[i]) {
                        boxes[i].classList.add('border-orange-500', 'bg-white');
                        boxes[i].classList.remove('border-zinc-200', 'bg-zinc-50');
                    }
                }

                const lastIndex = Math.min(pasted.length, boxes.length - 1);
                boxes[lastIndex].focus();

                syncValue();
            });

            // CLICK — select content
            box.addEventListener('click', function () {
                box.select();
            });
        });

        // Initial sync
        syncValue();
    });
    </script>
</body>
</html>