@extends('layouts.app')

@section('content')
<div x-data="orderForm({{ $restaurant->id }}, {{ $restaurant->menuItems->toJson() }})" x-init="init()">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative mb-6 bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">

        <div class="h-40 relative overflow-hidden">
            @if ($restaurant->cover_image_url)
<img src="{{ $restaurant->cover_image_url }}"
     alt="{{ $restaurant->name }}"
     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                 @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-400 to-amber-400"></div>
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
            @endif
        </div>

        <a href="{{ route('customer.restaurants') }}"
           class="absolute top-4 left-4 w-10 h-10 rounded-full bg-white/90 dark:bg-dark-800/90 backdrop-blur flex items-center justify-center shadow-md hover:bg-white dark:hover:bg-gray-900 transition z-10">
            <svg class="w-5 h-5 text-gray-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="relative px-6 -mt-12">
            <div class="flex items-end gap-4">
                <div class="w-24 h-24 rounded-2xl bg-white dark:bg-dark-800 border-4 border-white dark:border-dark-800 shadow-lg flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if ($restaurant->profile_image_url)
                        <img src="{{ $restaurant->profile_image_url }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-12 h-12 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    @endif
                </div>

                <div class="flex-1 pb-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100 leading-tight">{{ $restaurant->name }}</h1>
                    @if ($restaurant->display_badge)
                        <span class="inline-block mt-1 text-xs px-2.5 py-1 rounded-full bg-amber-800 dark:bg-amber-900 text-white font-bold uppercase tracking-wider">
                            {{ $restaurant->display_badge }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="line-clamp-1">{{ $restaurant->address }}</span>
            </div>

            <div class="mt-4 pb-6 flex items-center gap-5 text-sm border-t border-gray-100 dark:border-dark-700 pt-4">
                @if ($restaurant->rating_count > 0)
                    <button type="button" @click="tab = 'reviews'" class="flex items-center gap-1.5 hover:opacity-80 transition">
                        <svg class="w-5 h-5 text-yellow-500 fill-yellow-500" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="font-bold text-gray-900 dark:text-neutral-100">{{ number_format($restaurant->rating_avg, 1) }}</span>
                        <span class="text-gray-400 dark:text-neutral-500 text-xs">({{ $restaurant->rating_count }})</span>
                    </button>
                @else
                    <span class="flex items-center gap-1.5">
                        <svg class="w-5 h-5 text-gray-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span class="text-xs text-gray-400 italic">No ratings</span>
                    </span>
                @endif

                <div class="w-px h-4 bg-gray-200 dark:bg-dark-700"></div>
                <div class="flex items-center gap-1.5 text-gray-600 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">{{ $restaurant->prep_time_minutes ?? 20 }} min</span>
                </div>
                <div class="w-px h-4 bg-gray-200 dark:bg-dark-700"></div>
                <div class="flex items-center gap-1.5 text-gray-600 dark:text-neutral-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="font-medium">{{ $restaurant->menuItems->count() }} items</span>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- TAB NAVIGATION --}}
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden mb-6">
        <div class="flex">
            <button type="button" @click="tab = 'menu'"
                    :class="tab === 'menu' ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20 font-bold' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 font-semibold'"
                    class="flex-1 py-4 px-6 text-sm transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Menu
            </button>

            <button type="button" @click="tab = 'reviews'"
                    :class="tab === 'reviews' ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20 font-bold' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 font-semibold'"
                    class="flex-1 py-4 px-6 text-sm transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Reviews
                @if ($restaurant->rating_count > 0)
                    <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $restaurant->rating_count }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    {{-- MENU TAB --}}
    <form method="POST" action="{{ route('customer.orders.store') }}"
          x-show="tab === 'menu'"
          @submit="clearCartOnSubmit()"
          class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

        {{-- MENU AREA --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Menu Header + View Toggle --}}
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900 dark:text-neutral-100">Menu</h2>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $restaurant->menuItems->count() }} items</p>
                        </div>
                    </div>

                    {{-- VIEW TOGGLE --}}
                    <div class="flex items-center gap-1 bg-gray-200 dark:bg-dark-700 rounded-lg p-1">
                        <button type="button" @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white dark:bg-dark-800 shadow-sm text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700'"
                                class="p-2 rounded-md transition" title="Card view">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-white dark:bg-dark-800 shadow-sm text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700'"
                                class="p-2 rounded-md transition" title="List view">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- CARD GRID VIEW --}}
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                @forelse ($restaurant->menuItems as $item)
                    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-700 transition group flex flex-col">

                        {{-- IMAGE --}}
                        <div class="relative aspect-square overflow-hidden">
                            @if ($item->image_path)
                                <img src="{{ $item->image_url }}"
                                alt="{{ $item->name }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-50 dark:from-dark-850 dark:to-dark-800 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            @endif

                            {{-- FAVORITE --}}
                            @auth
                                @php $isFav = auth()->check() && auth()->user()->hasFavoritedFood($item); @endphp
                                <div x-data="favoriteFoodToggle({{ $item->id }}, {{ $isFav ? 'true' : 'false' }})"
                                     class="absolute top-2 right-2 z-10">
                                    <button @click="toggleFavorite()" type="button" :disabled="loading"
                                            class="w-8 h-8 rounded-full bg-white/95 dark:bg-dark-800/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition">
                                        <template x-if="!isFavorite">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </template>
                                        <template x-if="isFavorite">
                                            <svg class="w-4 h-4 text-red-500 fill-red-500" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        </template>
                                    </button>
                                </div>
                            @endauth

                            {{-- CART BADGE --}}
                            <template x-if="qty({{ $item->id }}) > 0">
                                <div class="absolute top-2 left-2 z-10">
                                    <span class="bg-orange-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md"
                                          x-text="qty({{ $item->id }}) + ' in cart'"></span>
                                </div>
                            </template>
                        </div>

                        {{-- BODY --}}
                        <div class="p-3 flex flex-col flex-1">
                            <p class="font-bold text-sm text-gray-900 dark:text-neutral-100 line-clamp-1 mb-1">
                                {{ $item->name }}
                            </p>

                            @if ($item->description)
                                <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2 mb-2 flex-1">
                                    {{ $item->description }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            <div class="flex items-center justify-between gap-2 mt-auto pt-2 border-t border-gray-100 dark:border-dark-700">
                                <p class="font-bold text-orange-600 dark:text-orange-400 text-sm">
                                    ₱{{ number_format($item->price, 0) }}
                                </p>

                                @auth
                                    <template x-if="qty({{ $item->id }}) === 0">
                                        <button type="button" @click="inc({{ $item->id }})"
                                                class="bg-orange-600 hover:bg-orange-700 text-white p-2 rounded-lg shadow-sm active:scale-90 transition">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template x-if="qty({{ $item->id }}) > 0">
                                        <div class="flex items-center gap-1 bg-orange-50 dark:bg-orange-950/30 rounded-lg p-0.5">
                                            <button type="button" @click="dec({{ $item->id }})"
                                                    class="w-7 h-7 rounded-md bg-white dark:bg-dark-800 text-orange-600 dark:text-orange-400 flex items-center justify-center shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <span class="w-6 text-center font-bold text-sm text-orange-600 dark:text-orange-400"
                                                  x-text="qty({{ $item->id }})"></span>
                                            <button type="button" @click="inc({{ $item->id }})"
                                                    class="w-7 h-7 rounded-md bg-orange-600 text-white flex items-center justify-center shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                @else
                                    <button type="button" @click="showGuestModal = true"
                                            class="bg-orange-600 hover:bg-orange-700 text-white p-2 rounded-lg shadow-sm active:scale-90">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-12 text-center bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700">
                        <svg class="w-16 h-16 text-gray-300 dark:text-neutral-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="text-gray-500 dark:text-neutral-400">No items available right now.</p>
                    </div>
                @endforelse
            </div>

            {{-- LIST VIEW --}}
            <div x-show="viewMode === 'list'" x-cloak
                 class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden">
                @forelse ($restaurant->menuItems as $item)
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-dark-850 transition flex items-center gap-3 border-b border-gray-100 dark:border-dark-700 last:border-0">
                        @if ($item->image_path)
                            <img src="{{ Storage::disk('public')->url($item->image_path) }}" alt="{{ $item->name }}"
                                 class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-dark-850 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-neutral-100 truncate">{{ $item->name }}</p>
                            <p class="text-sm font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($item->price, 2) }}</p>
                        </div>

                        @auth
                            @php $isFav = auth()->check() && auth()->user()->hasFavoritedFood($item); @endphp
                            <div x-data="favoriteFoodToggle({{ $item->id }}, {{ $isFav ? 'true' : 'false' }})">
                                <button @click="toggleFavorite()" type="button" :disabled="loading"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center">
                                    <template x-if="!isFavorite">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </template>
                                    <template x-if="isFavorite">
                                        <svg class="w-4 h-4 text-red-500 fill-red-500" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </template>
                                </button>
                            </div>
                        @endauth

                        <div class="flex items-center gap-2 flex-shrink-0">
                            @auth
                                <template x-if="qty({{ $item->id }}) === 0">
                                    <button type="button" @click="inc({{ $item->id }})"
                                            class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        Add
                                    </button>
                                </template>
                                <template x-if="qty({{ $item->id }}) > 0">
                                    <div class="flex items-center gap-2 bg-gray-100 dark:bg-dark-850 rounded-lg p-0.5">
                                        <button type="button" @click="dec({{ $item->id }})"
                                                class="w-7 h-7 rounded-md bg-white dark:bg-dark-800 text-gray-700 dark:text-neutral-300 flex items-center justify-center shadow-sm">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-6 text-center font-bold text-sm" x-text="qty({{ $item->id }})"></span>
                                        <button type="button" @click="inc({{ $item->id }})"
                                                class="w-7 h-7 rounded-md bg-orange-600 text-white flex items-center justify-center shadow-sm">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            @else
                                <button type="button" @click="showGuestModal = true"
                                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    Add
                                </button>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 dark:text-neutral-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="text-gray-500 dark:text-neutral-400">No items available right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- CART --}}
        <div class="lg:sticky lg:top-24 h-fit">
            @auth
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-900">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-bold text-gray-900 dark:text-neutral-100">Your Order</h2>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="cartSize === 0 ? 'Empty cart' : cartSize + ' item' + (cartSize > 1 ? 's' : '')"></p>
                                </div>
                            </div>

                            <template x-if="cartSize > 0">
                                <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold px-2.5 py-1 rounded-full" x-text="cartSize"></span>
                            </template>
                        </div>
                    </div>

                    <div class="p-5 max-h-64 overflow-y-auto">
                        <template x-if="cartSize === 0">
                            <div class="py-8 text-center">
                                <svg class="w-12 h-12 text-gray-300 dark:text-neutral-600 mx-auto mb-2 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-sm text-gray-400 dark:text-neutral-500">Your cart is empty</p>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="line in cartLines" :key="line.id">
                                <div class="flex justify-between items-start gap-3 text-sm">
                                    <div class="flex items-start gap-2 flex-1 min-w-0">
                                        <span class="w-6 h-6 rounded-md bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold flex items-center justify-center flex-shrink-0"
                                              x-text="line.quantity"></span>
                                        <span class="text-gray-700 dark:text-neutral-300 line-clamp-2" x-text="line.name"></span>
                                    </div>
                                    <span class="text-gray-900 dark:text-neutral-100 font-semibold flex-shrink-0">
                                        ₱<span x-text="line.subtotal.toFixed(2)"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-if="cartSize > 0">
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-dark-700 space-y-2 text-sm bg-gray-50 dark:bg-dark-850">
                            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                                <span>Food cost</span>
                                <span class="font-medium text-gray-900 dark:text-neutral-100">₱<span x-text="foodCost.toFixed(2)"></span></span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                                <span>Delivery fee</span>
                                <span class="font-medium text-gray-900 dark:text-neutral-100">₱{{ number_format(\App\Models\SystemConfig::current()->default_delivery_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-gray-900 dark:text-neutral-100 pt-3 border-t border-gray-200 dark:border-dark-600 text-base">
                                <span>Total</span>
                                <span class="text-orange-600 dark:text-orange-400">₱<span x-text="total.toFixed(2)"></span></span>
                            </div>
                        </div>
                    </template>

                    <div class="px-5 pb-5 pt-4 space-y-4 border-t border-gray-100 dark:border-dark-700">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <label class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Delivery Address</label>
                        </div>

                        <div class="relative">
                            <input type="text"
                                   x-model="address"
                                   @input.debounce.500ms="searchAddress()"
                                   @focus="showSuggestions = address.length >= 3 && suggestions.length > 0"
                                   @keydown.escape="showSuggestions = false"
                                   @keydown.arrow-down.prevent="highlightNext()"
                                   @keydown.arrow-up.prevent="highlightPrev()"
                                   @keydown.enter.prevent="selectHighlighted()"
                                   placeholder="Start typing your address..."
                                   autocomplete="off"
                                   required
                                   class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 pr-10">

                            <div x-show="searching" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>

                            <div x-show="!searching && lat && lng" x-cloak class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <div x-show="showSuggestions && suggestions.length > 0" x-cloak @click.outside="showSuggestions = false"
                                 class="absolute z-30 left-0 right-0 mt-2 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-xl shadow-xl max-h-72 overflow-y-auto">
                                <template x-for="(s, index) in suggestions" :key="index">
                                    <button type="button" @click="selectSuggestion(index)" @mouseenter="highlightedIndex = index"
                                            :class="highlightedIndex === index ? 'bg-orange-50 dark:bg-orange-950/30' : ''"
                                            class="w-full text-left px-4 py-3 text-sm hover:bg-orange-50 dark:hover:bg-orange-950/30 border-b border-gray-100 dark:border-dark-700 last:border-0">
                                        <p class="text-gray-900 dark:text-neutral-100 font-semibold line-clamp-1" x-text="s.display_name.split(',')[0]"></p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-2 mt-0.5" x-text="s.display_name"></p>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <input type="hidden" name="delivery_address" :value="address">
                        <input type="hidden" name="delivery_lat" :value="lat">
                        <input type="hidden" name="delivery_lng" :value="lng">

                        <button type="button" @click="useCurrentLocation()" :disabled="locating"
                                class="w-full flex items-center justify-center gap-2 text-sm text-orange-600 dark:text-orange-400 border-2 border-dashed border-orange-300 dark:border-orange-800 rounded-xl py-2.5 transition disabled:opacity-50 bg-orange-50 dark:bg-orange-950/30">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span x-show="!locating" class="font-medium">Use my current location</span>
                            <span x-show="locating" class="font-medium">Getting location...</span>
                        </button>

                        <template x-if="!searching && lat && lng">
                            <div class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400 font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Location confirmed
                            </div>
                        </template>

                        {{-- ============================================ --}}
                        {{-- PAYMENT METHOD SELECTOR (Dropdown Style) --}}
                        {{-- ============================================ --}}
                        <div class="pt-4 border-t border-gray-100 dark:border-dark-700">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <label class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Payment Method</label>
                            </div>

                            {{-- COD — Always visible --}}
                            <label class="block cursor-pointer mb-2">
                                <input type="radio"
                                       name="payment_method"
                                       value="cod"
                                       x-model="paymentMethod"
                                       @change="showOtherPaymentMethods = false; paymentReference = ''"
                                       class="peer sr-only">

                                <div class="relative border-2 rounded-xl p-3 flex items-center gap-3 transition-all duration-200
                                            border-gray-200 dark:border-dark-600
                                            peer-checked:border-orange-500 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-950/30
                                            peer-checked:shadow-md peer-checked:shadow-orange-500/20
                                            hover:border-gray-300 dark:hover:border-dark-500">

                                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-700 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 dark:text-neutral-100 peer-checked:text-orange-700 dark:peer-checked:text-orange-400">
                                            Cash on Delivery
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                                            Pay via Cash on Delivery
                                        </p>
                                    </div>

                                    <span class="w-5 h-5 rounded-full bg-orange-500 items-center justify-center hidden peer-checked:flex flex-shrink-0">
                                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </div>
                            </label>

                            {{-- TOGGLE BUTTON --}}
                            <button type="button"
                                    @click="showOtherPaymentMethods = !showOtherPaymentMethods"
                                    class="w-full flex items-center justify-between gap-2 py-2 px-1 text-xs font-semibold
                                           text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition group">

                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span x-text="showOtherPaymentMethods ? 'Hide other methods' : 'Pay another method'"></span>
                                </span>

                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                     :class="showOtherPaymentMethods ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- COLLAPSIBLE — GCash + Maya --}}
                            <div x-show="showOtherPaymentMethods"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 class="space-y-2 mt-2">

                                {{-- GCASH --}}
                                <label class="block cursor-pointer">
                                    <input type="radio"
                                           name="payment_method"
                                           value="gcash"
                                           x-model="paymentMethod"
                                           class="peer sr-only">

                                    <div class="relative border-2 rounded-xl p-3 flex items-center gap-3 transition-all duration-200
                                                border-gray-200 dark:border-dark-600
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30
                                                peer-checked:shadow-md peer-checked:shadow-blue-500/20
                                                hover:border-gray-300 dark:hover:border-dark-500">

                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-950/50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 15c-2.76 0-5-2.24-5-5s2.24-5 5-5c1.38 0 2.63.56 3.54 1.46l-1.41 1.41C13.41 9.34 12.75 9 12 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c1.39 0 2.56-.94 2.88-2.23h-2.88v-1.5h4.5c.04.24.06.49.06.75 0 2.76-2.24 5-5 5z"/>
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 dark:text-neutral-100 peer-checked:text-blue-700 dark:peer-checked:text-blue-400">
                                                GCash
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                                Send payment via GCash
                                            </p>
                                        </div>

                                        <span class="w-5 h-5 rounded-full bg-blue-500 items-center justify-center hidden peer-checked:flex flex-shrink-0">
                                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </label>

                                {{-- MAYA --}}
                                <label class="block cursor-pointer">
                                    <input type="radio"
                                           name="payment_method"
                                           value="maya"
                                           x-model="paymentMethod"
                                           class="peer sr-only">

                                    <div class="relative border-2 rounded-xl p-3 flex items-center gap-3 transition-all duration-200
                                                border-gray-200 dark:border-dark-600
                                                peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-950/30
                                                peer-checked:shadow-md peer-checked:shadow-green-500/20
                                                hover:border-gray-300 dark:hover:border-dark-500">

                                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-950/50 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1.5 15v-4.5H9v-1.5h1.5V9.5c0-1.38 1.12-2.5 2.5-2.5H15v1.5h-2c-.55 0-1 .45-1 1V11H15v1.5h-3V17h-1.5z"/>
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 dark:text-neutral-100 peer-checked:text-green-700 dark:peer-checked:text-green-400">
                                                Maya
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                                Send payment via Maya (PayMaya)
                                            </p>
                                        </div>

                                        <span class="w-5 h-5 rounded-full bg-green-500 items-center justify-center hidden peer-checked:flex flex-shrink-0">
                                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </label>
                            </div>

                            {{-- GCASH / MAYA INFO + REFERENCE INPUT --}}
                            <template x-if="paymentMethod === 'gcash' || paymentMethod === 'maya'">
                                <div class="space-y-3 mt-3">
                                    <div class="p-3 rounded-xl border flex items-start gap-2"
                                         :class="paymentMethod === 'gcash'
                                            ? 'bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800'
                                            : 'bg-green-50 dark:bg-green-950/30 border-green-200 dark:border-green-800'">

                                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5"
                                             :class="paymentMethod === 'gcash' ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400'"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>

                                        <div class="text-xs space-y-1.5 flex-1"
                                             :class="paymentMethod === 'gcash' ? 'text-blue-800 dark:text-blue-300' : 'text-green-800 dark:text-green-300'">

                                            <p class="font-bold">
                                                <span x-text="paymentMethod === 'gcash' ? 'Send to GCash' : 'Send to Maya'"></span>
                                            </p>

                                            <p>1. I-send ang bayad sa number na ito:</p>

                                            <div class="flex items-center gap-2 bg-white/70 dark:bg-dark-800/70 px-2.5 py-1.5 rounded-lg">
                                                <p class="font-mono font-bold text-sm"
                                                   x-text="paymentMethod === 'gcash'
                                                       ? '{{ \App\Models\SystemConfig::current()->gcash_number ?? '0917-XXX-XXXX' }}'
                                                       : '{{ \App\Models\SystemConfig::current()->maya_number ?? '0917-XXX-XXXX' }}'">
                                                </p>
                                            </div>

                                            <p>2. Kopyahin ang <strong>reference number</strong> pagkatapos mag-send.</p>
                                            <p>3. I-paste sa ibaba at i-submit.</p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-1.5">
                                            Reference Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               name="payment_reference"
                                               x-model="paymentReference"
                                               :required="paymentMethod !== 'cod'"
                                               maxlength="50"
                                               placeholder="e.g. 1234567890123"
                                               class="w-full border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-xl px-3 py-2.5 text-sm font-mono tracking-wide
                                                      focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                                        <p class="text-[10px] text-gray-500 dark:text-neutral-400 mt-1">
                                            I-verify ng rider ang reference number bago i-mark as paid.
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <template x-for="line in cartLines" :key="'input-' + line.id">
                            <div>
                                <input type="hidden" :name="'items[' + line.index + '][menu_item_id]'" :value="line.id">
                                <input type="hidden" :name="'items[' + line.index + '][quantity]'" :value="line.quantity">
                            </div>
                        </template>

                        <button type="submit"
                                :disabled="cartSize === 0 || !lat || !lng"
                                :class="(cartSize === 0 || !lat || !lng) ? 'opacity-40 cursor-not-allowed' : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-xl active:scale-98'"
                                class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3.5 rounded-xl text-sm font-bold shadow-lg transition transform flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Place Order
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 shadow-sm overflow-hidden">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">Create an account to order</h3>
                        <p class="text-sm text-gray-500 dark:text-neutral-400 mb-5">Sign in or sign up to place your order.</p>

                        <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold shadow-md mb-2">Sign In</a>
                        <a href="{{ route('register') }}" class="block w-full border-2 border-orange-500 text-orange-600 dark:text-orange-400 py-3 rounded-xl text-sm font-bold">Create Account</a>
                    </div>
                </div>
            @endauth
        </div>
    </form>

    {{-- REVIEWS TAB --}}
    <div x-show="tab === 'reviews'" x-cloak x-transition.opacity>
        @include('customer.partials.restaurant-reviews', ['restaurant' => $restaurant, 'reviews' => $reviews])
    </div>

    {{-- EXIT CONFIRMATION MODAL --}}
    @auth
        <div x-show="showExitModal" x-cloak x-transition.opacity @keydown.escape.window="cancelExit()" @click.self="cancelExit()"
             class="fixed inset-0 bg-black/60 z-[110] flex items-center justify-center p-4">
            <div x-show="showExitModal" x-transition.scale.origin.center class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-1">Your cart still has items!</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                    You still have <strong x-text="cartSize"></strong> item<span x-show="cartSize > 1">s</span>.
                </p>
                <div class="space-y-2">
                    <button type="button" @click="confirmExit()" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold">Continue & Save Cart</button>
                    <button type="button" @click="cancelExit()" class="w-full border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-3 rounded-xl text-sm font-semibold">Back to Cart</button>
                </div>
            </div>
        </div>
    @endauth

    {{-- GUEST MODAL --}}
    @guest
        <div x-show="showGuestModal" x-cloak x-transition.opacity @keydown.escape.window="showGuestModal = false" @click.self="showGuestModal = false"
             class="fixed inset-0 bg-black/60 z-[100] flex items-center justify-center p-4">
            <div x-show="showGuestModal" x-transition.scale.origin.center class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center relative">
                <button type="button" @click="showGuestModal = false" class="absolute top-4 right-4 text-gray-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-2">Sign in to order</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-6">You need an account to place an order.</p>
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl text-sm font-bold">Sign In</a>
                    <a href="{{ route('register') }}" class="block w-full border-2 border-orange-500 text-orange-600 dark:text-orange-400 py-3 rounded-xl text-sm font-bold">Create Account</a>
                </div>
            </div>
        </div>
    @endguest
</div>
@endsection

@push('scripts')
<style>
    .active\:scale-90:active { transform: scale(0.90); }
    .active\:scale-98:active { transform: scale(0.98); }
</style>
<script>
function favoriteFoodToggle(menuItemId, initialIsFavorite) {
    return {
        menuItemId,
        isFavorite: initialIsFavorite,
        loading: false,

        async toggleFavorite() {
            if (this.loading) return;
            this.loading = true;
            try {
                const res = await fetch(`/favorites/food/${this.menuItemId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await res.json();
                if (data.ok) this.isFavorite = data.is_favorite;
            } catch (err) { console.error(err); }
            finally { this.loading = false; }
        }
    }
}

function orderForm(restaurantId, menuItems) {
    return {
        tab: 'menu',
        viewMode: 'grid',
        menu: menuItems,
        cart: {},
        deliveryFee: {{ \App\Models\SystemConfig::current()->default_delivery_fee }},

        // ⭐ PAYMENT STATE
        paymentMethod: 'cod',
        paymentReference: '',
        showOtherPaymentMethods: false,

        showGuestModal: false,
        showExitModal: false,
        pendingNavigation: null,
        listenersSetup: false,
        locating: false,
        address: '', lat: '', lng: '',
        suggestions: [], showSuggestions: false, highlightedIndex: -1, searching: false,

        storageKey: 'fooddash_cart_' + restaurantId,
        addressKey: 'fooddash_address_' + restaurantId,
        viewKey: 'fooddash_view_' + restaurantId,
        cartExpiryHours: 24,

        init() {
            this.menu.forEach(m => { this.cart[m.id] = 0; });
            this.restoreCart();
            this.restoreAddress();

            const savedView = localStorage.getItem(this.viewKey);
            if (savedView && ['grid', 'list'].includes(savedView)) {
                this.viewMode = savedView;
            }

            this.$watch('viewMode', (v) => localStorage.setItem(this.viewKey, v));
            this.$watch('cart', () => this.saveCart());
            this.$watch('address', () => this.saveAddress());
            this.$nextTick(() => this.setupNavigationInterceptor());
        },

        setupNavigationInterceptor() {
            if (this.listenersSetup) return;
            this.listenersSetup = true;
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (!link) return;
                if (link.hasAttribute('data-no-cart-warning')) return;
                if (!this.hasItemsInCart()) return;
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                if (href.startsWith('http') && !href.includes(window.location.host)) return;
                const restaurantUrl = `/restaurants/${restaurantId}`;
                if (href.endsWith(restaurantUrl) || href.includes(restaurantUrl + '/')) return;
                e.preventDefault();
                e.stopPropagation();
                this.pendingNavigation = href;
                this.showExitModal = true;
            }, true);
        },

        hasItemsInCart() { return this.cartSize > 0; },
        confirmExit() { const url = this.pendingNavigation; this.pendingNavigation = null; this.showExitModal = false; if (url) window.location.href = url; },
        cancelExit() { this.pendingNavigation = null; this.showExitModal = false; },

        saveCart() {
            try { localStorage.setItem(this.storageKey, JSON.stringify({ cart: this.cart, savedAt: Date.now(), restaurantId })); } catch (err) {}
        },

        restoreCart() {
            try {
                const stored = localStorage.getItem(this.storageKey);
                if (!stored) return;
                const data = JSON.parse(stored);
                const hours = (Date.now() - (data.savedAt || 0)) / (1000 * 60 * 60);
                if (hours > this.cartExpiryHours || data.restaurantId !== restaurantId) { this.clearCartStorage(); return; }
                const validIds = this.menu.map(m => m.id);
                Object.keys(data.cart).forEach(id => {
                    const n = parseInt(id);
                    if (validIds.includes(n) && data.cart[id] > 0) this.cart[n] = data.cart[id];
                });
            } catch (err) { this.clearCartStorage(); }
        },

        clearCartStorage() {
            try { localStorage.removeItem(this.storageKey); localStorage.removeItem(this.addressKey); } catch (err) {}
        },

        clearCartOnSubmit() {
            this.clearCartStorage();
            this.paymentMethod = 'cod';
            this.paymentReference = '';
            this.showOtherPaymentMethods = false;
        },

        saveAddress() {
            try {
                if (this.address && this.lat && this.lng) {
                    localStorage.setItem(this.addressKey, JSON.stringify({ address: this.address, lat: this.lat, lng: this.lng, savedAt: Date.now() }));
                }
            } catch (err) {}
        },

        restoreAddress() {
            try {
                const stored = localStorage.getItem(this.addressKey);
                if (!stored) return;
                const data = JSON.parse(stored);
                const hours = (Date.now() - (data.savedAt || 0)) / (1000 * 60 * 60);
                if (hours > this.cartExpiryHours) { localStorage.removeItem(this.addressKey); return; }
                this.address = data.address || '';
                this.lat = data.lat || '';
                this.lng = data.lng || '';
            } catch (err) {}
        },

        qty(id) { return this.cart[id] || 0; },
        inc(id) { this.cart[id] = (this.cart[id] || 0) + 1; },
        dec(id) { this.cart[id] = Math.max(0, (this.cart[id] || 0) - 1); },

        get cartSize() { return Object.values(this.cart).reduce((a, b) => a + b, 0); },

        get cartLines() {
            let index = 0;
            const lines = [];
            for (const m of this.menu) {
                const qty = this.cart[m.id] || 0;
                if (qty > 0) lines.push({ index: index++, id: m.id, name: m.name, quantity: qty, subtotal: qty * parseFloat(m.price) });
            }
            return lines;
        },

        get foodCost() { return this.cartLines.reduce((sum, l) => sum + l.subtotal, 0); },
        get total() { return this.foodCost + (this.cartSize > 0 ? this.deliveryFee : 0); },

        async searchAddress() {
            if (!this.address || this.address.length < 3) { this.suggestions = []; this.showSuggestions = false; this.lat = ''; this.lng = ''; return; }
            if (this.locating) return;

            const town = '{{ \App\Models\SystemConfig::current()->town_address ?? "Cagayan de Oro" }}';
            const query = `${this.address}, ${town}, Philippines`;
            this.searching = true;

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1&countrycodes=ph`, { headers: { 'Accept-Language': 'en', 'User-Agent': 'FoodDash/1.0' } });
                const data = await res.json();
                this.suggestions = data || [];
                this.highlightedIndex = -1;
                this.showSuggestions = this.suggestions.length > 0;
                if (this.suggestions.length === 0) { this.lat = ''; this.lng = ''; }
            } catch (err) { this.suggestions = []; this.showSuggestions = false; }
            this.searching = false;
        },

        selectSuggestion(index) {
            const s = this.suggestions[index];
            if (!s) return;
            this.address = s.display_name;
            this.lat = s.lat;
            this.lng = s.lon;
            this.showSuggestions = false;
            this.suggestions = [];
        },

        highlightNext() { if (this.suggestions.length) this.highlightedIndex = (this.highlightedIndex + 1) % this.suggestions.length; },
        highlightPrev() { if (this.suggestions.length) this.highlightedIndex = this.highlightedIndex <= 0 ? this.suggestions.length - 1 : this.highlightedIndex - 1; },
        selectHighlighted() { if (this.highlightedIndex >= 0 && this.suggestions[this.highlightedIndex]) this.selectSuggestion(this.highlightedIndex); },

        async useCurrentLocation() {
            if (!navigator.geolocation) { alert('Geolocation not supported.'); return; }
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                async (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lng = pos.coords.longitude;
                    try {
                        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.lat}&lon=${this.lng}&zoom=18&addressdetails=1`, { headers: { 'Accept-Language': 'en', 'User-Agent': 'FoodDash/1.0' } });
                        const data = await res.json();
                        if (data && data.display_name) this.address = data.display_name;
                    } catch (err) {}
                    this.locating = false;
                    this.suggestions = [];
                    this.showSuggestions = false;
                },
                () => { alert('Could not get your location.'); this.locating = false; }
            );
        }
    }
}
</script>
@endpush