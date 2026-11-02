@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto" x-data="profileAvatar()">

    @if (session('success'))
        <div class="mb-4 p-3 bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800 text-orange-800 dark:text-orange-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- AVATAR + NAME --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4 text-center transition-colors">

        {{-- CLICKABLE AVATAR --}}
        <button type="button"
                @click="$refs.fileInput.click()"
                class="inline-block relative mb-3 group cursor-pointer focus:outline-none">

            <div class="relative inline-block">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}"
                         alt="{{ $user->name }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-dark-800 shadow-lg group-hover:opacity-75 transition">
                @else
                    <div class="w-24 h-24 rounded-full {{ $user->avatar_color }} flex items-center justify-center text-white text-3xl font-bold border-4 border-white dark:border-dark-800 shadow-lg group-hover:opacity-75 transition">
                        {{ $user->initials }}
                    </div>
                @endif

                {{-- HOVER OVERLAY --}}
                <div class="absolute inset-0 rounded-full bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>

                {{-- ONLINE DOT --}}
                @if ($user->isOnline())
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-orange-500 border-4 border-white dark:border-dark-800 rounded-full"></span>
                @else
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-gray-400 border-4 border-white dark:border-dark-800 rounded-full"></span>
                @endif
            </div>
        </button>

        {{-- HIDDEN FILE INPUT --}}
        <input type="file"
               x-ref="fileInput"
               @change="onFileChange($event)"
               accept="image/jpeg,image/jpg,image/png,image/webp"
               class="hidden">

        <p class="text-xs text-gray-400 dark:text-neutral-500 mb-3">Click picture to change</p>

        <p class="text-lg font-semibold text-gray-900 dark:text-neutral-100">{{ $user->name }}</p>
        <p class="text-sm text-gray-500 dark:text-neutral-400">{{ $user->email }}</p>

        @if ($user->phone)
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">{{ $user->phone }}</p>
        @endif

        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-2">
            <span class="inline-block px-2 py-0.5 rounded-full bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 font-medium">
                {{ ucfirst($user->role) }}
            </span>
        </p>
    </div>

    {{-- ============================================ --}}
    {{-- APPEARANCE (DARK MODE) --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-4 transition-colors"
         x-data="themeToggle()">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-neutral-100">Appearance</h2>
                <p class="text-xs text-gray-500 dark:text-neutral-400">Choose your preferred theme</p>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-3 gap-2">

                {{-- LIGHT --}}
                <button type="button"
                        @click="setTheme('light')"
                        :class="theme === 'light'
                                ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 ring-2 ring-orange-500'
                                : 'border-gray-200 dark:border-dark-600 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'light' ? 'text-orange-500' : 'text-gray-400 dark:text-neutral-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-neutral-300">Light</span>

                    <template x-if="theme === 'light'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-orange-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>

                {{-- DARK --}}
                <button type="button"
                        @click="setTheme('dark')"
                        :class="theme === 'dark'
                                ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 ring-2 ring-orange-500'
                                : 'border-gray-200 dark:border-dark-600 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'dark' ? 'text-orange-500' : 'text-gray-400 dark:text-neutral-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-neutral-300">Dark</span>

                    <template x-if="theme === 'dark'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-orange-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>

                {{-- SYSTEM --}}
                <button type="button"
                        @click="setTheme('system')"
                        :class="theme === 'system'
                                ? 'border-orange-500 bg-orange-50 dark:bg-orange-950/40 ring-2 ring-orange-500'
                                : 'border-gray-200 dark:border-dark-600 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="relative border-2 rounded-xl p-3 flex flex-col items-center gap-2 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         :class="theme === 'system' ? 'text-orange-500' : 'text-gray-400 dark:text-neutral-500'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-medium text-gray-700 dark:text-neutral-300">System</span>

                    <template x-if="theme === 'system'">
                        <span class="absolute top-1.5 right-1.5 w-4 h-4 rounded-full bg-orange-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                    </template>
                </button>
            </div>

            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-3 text-center">
                <template x-if="theme === 'light'">
                    <span>Light mode — bright background</span>
                </template>
                <template x-if="theme === 'dark'">
                    <span>Dark mode — dim background</span>
                </template>
                <template x-if="theme === 'system'">
                    <span>Follows your device settings</span>
                </template>
            </p>
        </div>
    </div>

    {{-- STATS --}}
    @if ($stats)
        <div class="grid grid-cols-3 gap-3 mb-4">
            @if ($user->isCustomer())
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Delivered</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Total Spent</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">₱{{ number_format($stats['total_spent'], 0) }}</p>
                </div>
            @elseif ($user->isRider())
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Deliveries</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['total_deliveries'] }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Earnings</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($stats['total_earnings'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Rating</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @elseif ($user->isRestaurant())
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Sales</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg p-4 text-center transition-colors">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Rating</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['avg_rating'] ? round($stats['avg_rating'], 1) : 'N/A' }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ACCOUNT INFO --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-6 mb-4 transition-colors">
        <h2 class="font-semibold text-gray-900 dark:text-neutral-100 mb-4">Account Information</h2>

        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-dark-700">
                <span class="text-sm text-gray-500 dark:text-neutral-400">Email</span>
                <span class="text-sm text-gray-900 dark:text-neutral-100 font-medium">{{ $user->email }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-dark-700">
                <span class="text-sm text-gray-500 dark:text-neutral-400">Phone</span>
                <span class="text-sm text-gray-900 dark:text-neutral-100 font-medium">{{ $user->phone ?? 'Not set' }}</span>
            </div>

            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-dark-700">
                <span class="text-sm text-gray-500 dark:text-neutral-400">Role</span>
                <span class="text-sm text-gray-900 dark:text-neutral-100 font-medium">{{ ucfirst($user->role) }}</span>
            </div>

            <div class="flex justify-between items-center py-2">
                <span class="text-sm text-gray-500 dark:text-neutral-400">Member since</span>
                <span class="text-sm text-gray-900 dark:text-neutral-100 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- QUICK LINKS --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">
        <a href="{{ route('settings.index') }}"
           class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-dark-850 transition border-b border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-dark-850 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-neutral-100">Edit profile & settings</span>
            </div>
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- LOGOUT --}}
        <div x-data="{ showLogout: false }">
            <button @click="showLogout = true"
                    type="button"
                    class="w-full flex items-center justify-between p-4 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Logout</span>
                </div>
                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            {{-- LOGOUT MODAL --}}
            <div x-show="showLogout"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showLogout = false"
                 class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
                 @click.self="showLogout = false">

                <div x-show="showLogout"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-dark-800 rounded-lg shadow-xl max-w-sm w-full p-6">

                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-neutral-100">Logout?</h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Are you sure you want to logout?</p>
                    </div>

                    <div class="flex gap-2">
                        <button @click="showLogout = false"
                                type="button"
                                class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                            Cancel
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="flex-1" id="logout-form">
                            @csrf
                            <button type="submit"
                                    onclick="clearThemeBeforeLogout()"
                                    class="w-full bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                                Yes, Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')

<script>
function profileAvatar() {
    return {
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Maximum 5MB.');
                e.target.value = '';
                return;
            }

            if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
                alert('Invalid file type. Use JPG, PNG, or WebP.');
                e.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                sessionStorage.setItem('temp_avatar_image', ev.target.result);
                window.location.href = '{{ route('profile.avatar') }}';
            };
            reader.readAsDataURL(file);
        }
    }
}

function themeToggle() {
    return {
        theme: 'system',

        init() {
            const stored = localStorage.getItem('theme');
            this.theme = stored || 'system';
        },

        setTheme(mode) {
            this.theme = mode;
            localStorage.setItem('theme', mode);

            const isDark = mode === 'dark' ||
                (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}
function clearThemeBeforeLogout() {
    localStorage.removeItem('theme');
    document.documentElement.classList.remove('dark');
}
</script>
@endpush