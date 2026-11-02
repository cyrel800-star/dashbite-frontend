@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-xl">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500 rounded-full blur-3xl opacity-20 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl opacity-10 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TOP --}}
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.orders') }}"
                       class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/60 uppercase tracking-wider font-medium">Customer</p>
                        <h1 class="text-xl font-bold text-white">My Profile</h1>
                    </div>
                </div>

                <a href="{{ route('settings.index') }}"
                   class="bg-white/10 hover:bg-white/20 backdrop-blur rounded-full px-4 py-2 text-white text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>

            {{-- PROFILE INFO --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('profile.avatar') }}" class="relative group flex-shrink-0">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             class="w-20 h-20 rounded-2xl object-cover border-2 border-white/20 group-hover:opacity-75 transition">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-3xl font-bold border-2 border-white/20 group-hover:opacity-75 transition">
                            {{ $user->initials }}
                        </div>
                    @endif

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    @if ($user->isOnline())
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-400 border-4 border-gray-900"></span>
                    @endif
                </a>

                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-lg leading-tight">{{ $user->name }}</p>
                    <p class="text-white/60 text-sm truncate">{{ $user->email }}</p>
                    <span class="inline-block mt-2 text-[10px] bg-orange-500/20 backdrop-blur text-orange-200 px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/10">
                <div>
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Total Orders</p>
                    <p class="text-white font-bold text-xl mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Delivered</p>
                    <p class="text-white font-bold text-xl mt-1">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Total Spent</p>
                    <p class="text-white font-bold text-xl mt-1">₱{{ number_format($stats['total_spent'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNT INFORMATION --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Account Information</h2>
                <p class="text-xs text-gray-500">I-edit sa Settings</p>
            </div>
        </div>

        <div class="p-6 space-y-3 text-sm">
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-gray-500">Name</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->name }}</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-500">Email</span>
                </div>
                <span class="font-semibold text-gray-900 truncate ml-4">{{ $user->email }}</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="text-gray-500">Phone</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->phone ?? 'Not set' }}</span>
            </div>

            <div class="flex justify-between items-center py-3">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-500">Member since</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-3">
            <a href="{{ route('settings.index') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Info
            </a>
            <a href="{{ route('profile.avatar') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Change Picture
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DANGER ZONE --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border-2 border-red-200 overflow-hidden" x-data="{ showDeleteModal: false }">

        <div class="px-6 py-4 border-b border-red-100 bg-red-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-red-900">Danger Zone</h2>
                <p class="text-xs text-red-600">Careful — hindi na mababalik</p>
            </div>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Once you delete your account, there is no going back. All your orders, ratings, and data will be permanently removed.
            </p>

            <button type="button"
                    @click="showDeleteModal = true"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg active:scale-95 transition transform flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete My Account
            </button>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDeleteModal"
             x-cloak
             x-transition.opacity
             @keydown.escape.window="showDeleteModal = false"
             class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
             @click.self="showDeleteModal = false">

            <div x-show="showDeleteModal"
                 x-transition.scale.origin.center
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">

                <div class="flex items-start gap-3 mb-5">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Delete Account?</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            This action cannot be undone. All your data will be permanently deleted.
                        </p>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('settings.destroy') }}"
                      class="space-y-4"
                      x-data="{ showPassword: false }">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Enter your password to confirm
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   placeholder="Your password"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">

                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="confirm_delete"
                               value="1"
                               required
                               class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-xs text-gray-600">
                            I understand that this action is <strong class="text-red-600">permanent</strong> and cannot be undone.
                        </span>
                    </label>

                    <div class="flex gap-2 pt-2">
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="flex-1 border border-gray-300 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 shadow-md hover:shadow-lg active:scale-95 transition transform">
                            Delete Permanently
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-xl">

        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500 rounded-full blur-3xl opacity-20 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl opacity-10 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            {{-- TOP --}}
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.orders') }}"
                       class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/60 uppercase tracking-wider font-medium">Customer</p>
                        <h1 class="text-xl font-bold text-white">My Profile</h1>
                    </div>
                </div>

                <a href="{{ route('settings.index') }}"
                   class="bg-white/10 hover:bg-white/20 backdrop-blur rounded-full px-4 py-2 text-white text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
            </div>

            {{-- PROFILE INFO --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('profile.avatar') }}" class="relative group flex-shrink-0">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                             class="w-20 h-20 rounded-2xl object-cover border-2 border-white/20 group-hover:opacity-75 transition">
                    @else
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-3xl font-bold border-2 border-white/20 group-hover:opacity-75 transition">
                            {{ $user->initials }}
                        </div>
                    @endif

                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    @if ($user->isOnline())
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-400 border-4 border-gray-900"></span>
                    @endif
                </a>

                <div class="flex-1 min-w-0">
                    <p class="text-white font-bold text-lg leading-tight">{{ $user->name }}</p>
                    <p class="text-white/60 text-sm truncate">{{ $user->email }}</p>
                    <span class="inline-block mt-2 text-[10px] bg-orange-500/20 backdrop-blur text-orange-200 px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/10">
                <div>
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Total Orders</p>
                    <p class="text-white font-bold text-xl mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Delivered</p>
                    <p class="text-white font-bold text-xl mt-1">{{ $stats['delivered_orders'] }}</p>
                </div>
                <div class="border-l border-white/10 pl-3">
                    <p class="text-[10px] text-white/50 uppercase tracking-wider font-medium">Total Spent</p>
                    <p class="text-white font-bold text-xl mt-1">₱{{ number_format($stats['total_spent'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNT INFORMATION --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900">Account Information</h2>
                <p class="text-xs text-gray-500">I-edit sa Settings</p>
            </div>
        </div>

        <div class="p-6 space-y-3 text-sm">
            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-gray-500">Name</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->name }}</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-500">Email</span>
                </div>
                <span class="font-semibold text-gray-900 truncate ml-4">{{ $user->email }}</span>
            </div>

            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="text-gray-500">Phone</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->phone ?? 'Not set' }}</span>
            </div>

            <div class="flex justify-between items-center py-3">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-500">Member since</span>
                </div>
                <span class="font-semibold text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-3">
            <a href="{{ route('settings.index') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Info
            </a>
            <a href="{{ route('profile.avatar') }}"
               class="flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-3 rounded-xl text-sm font-semibold hover:bg-gray-100 hover:border-gray-400 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Change Picture
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DANGER ZONE --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-2xl border-2 border-red-200 overflow-hidden" x-data="{ showDeleteModal: false }">

        <div class="px-6 py-4 border-b border-red-100 bg-red-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-red-900">Danger Zone</h2>
                <p class="text-xs text-red-600">Careful — hindi na mababalik</p>
            </div>
        </div>

        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Once you delete your account, there is no going back. All your orders, ratings, and data will be permanently removed.
            </p>

            <button type="button"
                    @click="showDeleteModal = true"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg active:scale-95 transition transform flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete My Account
            </button>
        </div>

        {{-- DELETE MODAL --}}
        <div x-show="showDeleteModal"
             x-cloak
             x-transition.opacity
             @keydown.escape.window="showDeleteModal = false"
             class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
             @click.self="showDeleteModal = false">

            <div x-show="showDeleteModal"
                 x-transition.scale.origin.center
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">

                <div class="flex items-start gap-3 mb-5">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Delete Account?</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            This action cannot be undone. All your data will be permanently deleted.
                        </p>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('settings.destroy') }}"
                      class="space-y-4"
                      x-data="{ showPassword: false }">
                    @csrf
                    @method('DELETE')

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Enter your password to confirm
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   placeholder="Your password"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent">

                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="confirm_delete"
                               value="1"
                               required
                               class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-xs text-gray-600">
                            I understand that this action is <strong class="text-red-600">permanent</strong> and cannot be undone.
                        </span>
                    </label>

                    <div class="flex gap-2 pt-2">
                        <button type="button"
                                @click="showDeleteModal = false"
                                class="flex-1 border border-gray-300 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 bg-red-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 shadow-md hover:shadow-lg active:scale-95 transition transform">
                            Delete Permanently
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection