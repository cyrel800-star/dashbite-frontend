@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-[#2D2D2D] shadow-xl dark:bg-[#C2410C]">

        {{-- Decorative --}}
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white opacity-10 blur-3xl dark:hidden"></div>
        <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-yellow-300 opacity-20 blur-3xl dark:hidden"></div>

        <div class="relative p-6 text-white dark:text-white">
            {{-- TOP --}}
            <div class="mb-6 flex justify-end">

                {{-- ADMIN BADGE --}}
                <div class="flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3.5 py-1.5 backdrop-blur dark:border-white/20 dark:bg-white/15">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wide text-white">Admin</span>
                </div>
            </div>

            {{-- PROFILE INFO --}}
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('admin.profile.avatar') }}" class="relative group flex-shrink-0">
                    @if ($user->avatar_url)
                        <img src="{{ $user->avatar_url }}"
                             alt="{{ $user->name }}"
                                     class="h-20 w-20 rounded-2xl border-2 border-white/20 object-cover transition group-hover:opacity-75">
                    @else
                                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-2 border-white/30 bg-white/20 text-3xl font-bold text-white backdrop-blur transition group-hover:opacity-75">
                            {{ $user->initials }}
                        </div>
                    @endif

                    <div class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    @if ($user->isOnline())
                        <span class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full border-4 border-[#2D2D2D] bg-green-400 dark:border-white"></span>
                    @endif
                </a>

                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold leading-tight text-white">{{ $user->name }}</p>
                    <p class="truncate text-sm text-white/70">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="rounded-full border border-white/30 bg-white/20 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">
                            Administrator
                        </span>
                        <span class="text-[10px] text-white/70">
                            Since {{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-3 gap-3 border-t border-white/20 pt-5">
                <div>
                    <p class="text-[10px] font-medium uppercase tracking-wider text-white/75">Total Users</p>
                    <p class="mt-1 text-xl font-bold text-white">{{ number_format($stats['total_users']) }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] font-medium uppercase tracking-wider text-white/75">Total Orders</p>
                    <p class="mt-1 text-xl font-bold text-white">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] font-medium uppercase tracking-wider text-white/75">Platform Revenue</p>
                    <p class="mt-1 text-xl font-bold text-white">₱{{ number_format($stats['total_commission'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if ($errors->any())
        <div class="p-4 bg-black dark:bg-neutral-900 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-medium">{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SYSTEM STATS (ADMIN-SPECIFIC) — GREY/WHITE --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Customers</p>
            </div>
            <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ number_format($stats['total_customers']) }}</p>
        </div>

        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Restaurants</p>
            </div>
            <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ number_format($stats['total_restaurants']) }}</p>
        </div>

        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Riders</p>
            </div>
            <p class="text-xl font-bold text-neutral-900 dark:text-white">{{ number_format($stats['total_riders']) }}</p>
        </div>

        <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Revenue</p>
            </div>
            <p class="text-xl font-bold text-neutral-900 dark:text-white">₱{{ number_format($stats['total_revenue'], 0) }}</p>
        </div>
    </div>

    {{-- APPEARANCE --}}
    <section x-data="themeToggle()" x-init="init()" class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-[#262626] dark:bg-[#141414] sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-950/40">
                    <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-neutral-900 dark:text-white">Appearance</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Choose light or dark mode</p>
                </div>
            </div>

            <div role="group" aria-label="Appearance mode" class="grid w-full grid-cols-2 rounded-xl border border-neutral-200 bg-neutral-100 p-1 dark:border-[#404040] dark:bg-[#0a0a0a] sm:w-auto">
                <button type="button" @click="setTheme('light')" :aria-pressed="theme === 'light'"
                        :class="theme === 'light' ? 'bg-white text-neutral-900 shadow-sm dark:bg-[#2D2D2D] dark:text-white' : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white'"
                        class="flex min-h-10 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4" />
                        <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
                    </svg>
                    Light
                </button>
                <button type="button" @click="setTheme('dark')" :aria-pressed="theme === 'dark'"
                        :class="theme === 'dark' ? 'bg-white text-neutral-900 shadow-sm dark:bg-[#2D2D2D] dark:text-white' : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white'"
                        class="flex min-h-10 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    Dark
                </button>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- PENDING ACTIONS --}}
    {{-- ============================================ --}}
    @if ($recentActivity['pending_accounts'] > 0 || $recentActivity['flagged_reviews'] > 0)
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-neutral-900 dark:text-white">Actions Required</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Items na kailangan mong i-review</p>
                </div>
            </div>

            <div class="divide-y divide-neutral-100 dark:divide-[#262626]">
                @if ($recentActivity['pending_accounts'] > 0)
                    <a href="{{ route('admin.accounts', ['filter' => 'pending']) }}"
                       class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-neutral-900 dark:text-white">
                                    Pending Accounts
                                </p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $recentActivity['pending_accounts'] }} account{{ $recentActivity['pending_accounts'] > 1 ? 's' : '' }} waiting for approval
                                </p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-neutral-400 group-hover:text-orange-500 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif

                @if ($recentActivity['flagged_reviews'] > 0)
                    <a href="{{ route('admin.reviews.index', ['flagged' => 1]) }}"
                       class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-neutral-900 dark:text-white">
                                    Flagged Reviews
                                </p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $recentActivity['flagged_reviews'] }} review{{ $recentActivity['flagged_reviews'] > 1 ? 's' : '' }} need moderation
                                </p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-neutral-400 group-hover:text-orange-500 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SYSTEM STATUS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
        <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-neutral-900 dark:text-white">System Status</h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">Real-time overview ng system</p>
            </div>
        </div>

        <div class="grid grid-cols-2 divide-x divide-neutral-100 dark:divide-[#262626]">
            <div class="p-5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Active Restaurants</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $recentActivity['active_restaurants'] }}</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Currently open for orders</p>
            </div>

            <div class="p-5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-bold">Online Riders</p>
                </div>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $recentActivity['online_riders'] }}</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Available for delivery</p>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNT INFORMATION --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-neutral-900 dark:text-white">Account Information</h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">I-edit ang iyong admin profile</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Name <span class="text-orange-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Email
                </label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full border border-neutral-300 dark:border-[#262626] rounded-xl px-3 py-2.5 text-sm bg-neutral-100 dark:bg-[#0a0a0a] text-neutral-500 dark:text-neutral-500 cursor-not-allowed">
                <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Email cannot be changed.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Phone
                </label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                       placeholder="09171234567"
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                    Gender
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="gender" value="male"
                               @checked(old('gender', $user->gender) === 'male')
                               class="peer sr-only">
                        <div class="flex items-center justify-center gap-2 border-2 border-neutral-200 dark:border-[#262626] rounded-xl py-3 transition peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-950/30 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a]">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Male</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="gender" value="female"
                               @checked(old('gender', $user->gender) === 'female')
                               class="peer sr-only">
                        <div class="flex items-center justify-center gap-2 border-2 border-neutral-200 dark:border-[#262626] rounded-xl py-3 transition peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-950/30 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a]">
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Female</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- CHANGE PASSWORD --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden"
         x-data="{ showPassword: false }">

        <div class="px-6 py-4 border-b border-neutral-100 dark:border-[#262626] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-neutral-900 dark:text-white">Security</h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Change your password</p>
                </div>
            </div>

            <button @click="showPassword = !showPassword"
                    type="button"
                    class="text-sm text-orange-600 dark:text-orange-400 hover:underline font-medium">
                <span x-show="!showPassword">Change</span>
                <span x-show="showPassword" x-cloak>Cancel</span>
            </button>
        </div>

        <div x-show="showPassword" x-cloak x-transition class="p-6">
            <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Current Password
                    </label>
                    <input type="password" name="current_password" required
                           class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        New Password
                    </label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Minimum 8 characters.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" required
                           class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                </div>

                <button type="submit"
                        class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                    Update Password
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- QUICK LINKS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
        <a href="{{ route('admin.settings') }}"
           class="flex items-center justify-between p-4 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition border-b border-neutral-100 dark:border-[#262626] group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-neutral-900 dark:text-white">System Settings</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Configure platform settings</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-neutral-400 group-hover:text-orange-500 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        {{-- LOGOUT --}}
        <div x-data="{ showLogout: false }">
            <button @click="showLogout = true"
                    type="button"
                    class="w-full flex items-center justify-between p-4 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">Logout</span>
                </div>
                <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="showLogout"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="showLogout = false"
                 class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
                 @click.self="showLogout = false">

                <div x-show="showLogout"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-[#141414] rounded-2xl shadow-xl max-w-sm w-full p-6">

                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Logout?</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Are you sure you want to logout?</p>
                    </div>

                    <div class="flex gap-2">
                        <button @click="showLogout = false"
                                type="button"
                                class="flex-1 border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                            Cancel
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit"
                                    onclick="clearThemeBeforeLogout()"
                                    class="w-full bg-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 shadow-md transition">
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
function clearThemeBeforeLogout() {
    localStorage.removeItem('theme');
    document.documentElement.classList.remove('dark');
}
</script>
@endpush