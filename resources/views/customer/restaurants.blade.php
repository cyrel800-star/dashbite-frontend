@extends('layouts.customer')

@section('content')
<div x-data="restaurantList()" x-init="init()" class="restaurant-page mx-auto flex max-w-7xl flex-col gap-9">

    {{-- ============================================ --}}
    {{-- HERO HEADER WITH SEARCH --}}
    {{-- ============================================ --}}
    <section class="discovery-hero order-0 grid gap-8 rounded-[1.75rem] p-6 sm:p-9 lg:grid-cols-[minmax(0,1.05fr)_minmax(20rem,0.95fr)] lg:items-end">
        <div class="hero-copy relative z-10">
            <p class="hero-eyebrow mb-4 flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em]">
                <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                Your neighborhood, served
            </p>
            <h1 class="max-w-2xl text-4xl font-semibold leading-[1.02] tracking-[-0.045em] text-white sm:text-5xl lg:text-6xl">
                Good things<br><span class="hero-heading-accent">are on the menu.</span>
            </h1>
            <p class="hero-subtitle mt-4 max-w-md text-sm leading-6 sm:text-base">
                Find a new regular, revisit an old favorite, and make tonight taste a little different.
            </p>
            <div class="mt-7 flex flex-wrap gap-2">
                <span class="hero-stat"><strong>{{ $restaurants->count() }}</strong> places to explore</span>
                <span class="hero-stat"><strong>{{ isset($cuisines) ? $cuisines->count() : 0 }}</strong> food styles</span>
            </div>
        </div>

        {{-- SEARCH BAR --}}
        <form method="GET" action="{{ route('customer.restaurants') }}" class="hero-search relative z-10 w-full">
            <label for="restaurant-search" class="mb-3 block text-xs font-bold uppercase tracking-[0.15em] text-white/75">What are you craving?</label>
            <div class="search-control flex items-center gap-1 rounded-2xl border border-white/15 bg-white p-1.5 transition focus-within:ring-4 focus-within:ring-orange-400/25">
                <svg class="ml-3 h-5 w-5 shrink-0 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input id="restaurant-search" type="text" name="search" value="{{ request('search') }}"
                       placeholder="Restaurant or location"
                       class="min-w-0 flex-1 border-0 bg-transparent px-2 py-3.5 text-sm text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-0">

                @if (request('search'))
                    <a href="{{ route('customer.restaurants', ['cuisine' => request('cuisine')]) }}"
                       aria-label="Clear search" title="Clear search"
                       class="rounded-lg p-2 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-900">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif

                @if (request('cuisine'))
                    <input type="hidden" name="cuisine" value="{{ request('cuisine') }}">
                @endif

                <button type="submit" aria-label="Search restaurants"
                        class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#FF6B35] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-orange-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="hidden sm:inline">Find a place</span>
                </button>
            </div>
        </form>
    </section>

    {{-- ============================================ --}}
    {{-- RECOMMENDED MENU CAROUSEL --}}
    {{-- ============================================ --}}
    <section x-data="menuCarousel()" x-init="init()" class="curated-section order-2 space-y-4">

        {{-- HEADER --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="section-stamp flex h-11 w-11 items-center justify-center rounded-2xl bg-[#FF6B35]">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white">A few good picks</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">A rotating taste of what’s cooking nearby</p>
                </div>
            </div>
        </div>

        {{-- LOADING --}}
        <template x-if="loading && items.length === 0">
            <div class="p-6 flex items-center justify-center gap-3">
                <svg class="w-5 h-5 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Loading recommendations...</p>
            </div>
        </template>

        {{-- EMPTY --}}
        <template x-if="!loading && items.length === 0">
            <div class="p-8 text-center">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">No recommendations available right now</p>
            </div>
        </template>

        {{-- CAROUSEL --}}
        <template x-if="items.length > 0">
            <div class="relative">
                <div x-ref="scroller"
                    @mouseenter="pause()"
                    @mouseleave="resume()"
                    @mousedown="onDragStart()"
                    @mouseup="onDragEnd()"
                    @touchstart.passive="pause(); onDragStart()"
                    @touchend.passive="resume(); onDragEnd()"
                    class="flex gap-4 overflow-x-auto scroll-smooth py-2 carousel-scroll">

                    <template x-for="(item, index) in items" :key="item.id + '-' + index">
                        <a :href="item.restaurant_url"
                           class="pick-card group w-48 shrink-0 cursor-pointer overflow-hidden rounded-2xl border border-neutral-200 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-[#262626] dark:bg-[#141414] sm:w-52">

                            {{-- IMAGE --}}
                            <div class="pick-art relative aspect-[1.18/1] overflow-hidden">
                                <template x-if="item.image_url">
                                    <img :src="item.image_url"
                                         :alt="item.name"
                                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </template>
                                <template x-if="!item.image_url">
                                    <div class="pick-art-fallback absolute inset-0 flex items-center justify-center">
                                        <span class="pick-art-orbit"></span>
                                        <span class="pick-art-letter" x-text="item.name?.slice(0, 1) || 'D'"></span>
                                    </div>
                                </template>

                                {{-- RESTAURANT BADGE --}}
                                <div class="absolute bottom-2 left-2 right-2 z-10">
                                    <span class="inline-block max-w-full truncate rounded-full bg-white/90 px-3 py-1.5 text-[10px] font-bold tracking-wide text-neutral-800 shadow-sm backdrop-blur"
                                          x-text="item.restaurant_name"></span>
                                </div>
                            </div>

                            {{-- BODY --}}
                            <div class="p-4">
                                <p class="font-semibold text-sm text-neutral-900 dark:text-white line-clamp-1 group-hover:text-orange-700 dark:group-hover:text-orange-300 transition"
                                   x-text="item.name"></p>

                                <template x-if="item.description">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 line-clamp-2 mt-1 h-8"
                                       x-text="item.description"></p>
                                </template>
                                <template x-if="!item.description">
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 italic mt-1 h-8">No description</p>
                                </template>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </template>
    </section>

    {{-- ============================================ --}}
    {{-- CUISINE CHIPS --}}
    {{-- ============================================ --}}
    @if (isset($cuisines) && $cuisines->count() > 0)
        <section class="order-1 min-w-0 space-y-3">
            <h2 class="text-base font-semibold text-neutral-900 dark:text-white">Browse by food style</h2>
            <div class="cuisine-scroll -mx-4 flex flex-nowrap items-center gap-2 overflow-x-auto px-4 pb-2 sm:mx-0 sm:flex-wrap sm:overflow-visible sm:px-0 sm:pb-0">
                <a href="{{ route('customer.restaurants', ['search' => request('search')]) }}"
                   aria-current="{{ !request('cuisine') ? 'true' : 'false' }}"
                   class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full border px-4 py-2.5 text-xs font-medium transition {{ !request('cuisine')
                        ? 'border-orange-600 bg-orange-600 text-white'
                        : 'border-neutral-200 bg-white text-neutral-700 hover:border-orange-300 hover:text-orange-700 dark:border-[#404040] dark:bg-[#141414] dark:text-neutral-300 dark:hover:border-orange-800 dark:hover:text-orange-300' }}">
                    All Cuisines
                </a>
                @foreach ($cuisines as $cuisine)
                          <a href="{{ route('customer.restaurants', ['search' => request('search'), 'cuisine' => $cuisine]) }}"
                              aria-current="{{ request('cuisine') === $cuisine ? 'true' : 'false' }}"
                              class="inline-flex shrink-0 items-center whitespace-nowrap rounded-full border px-4 py-2.5 text-xs font-medium transition {{ request('cuisine') === $cuisine
                                     ? 'border-orange-600 bg-orange-600 text-white'
                                     : 'border-neutral-200 bg-white text-neutral-700 hover:border-orange-300 hover:text-orange-700 dark:border-[#404040] dark:bg-[#141414] dark:text-neutral-300 dark:hover:border-orange-800 dark:hover:text-orange-300' }}">
                        {{ $cuisine }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- ACTIVE FILTERS --}}
    {{-- ============================================ --}}
    @if (request('search') || request('cuisine'))
        <div class="order-3 flex flex-wrap items-center justify-between gap-3 border-l-2 border-orange-500 py-1 pl-3">
            <div class="flex items-center gap-2 min-w-0">
                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-950/40">
                    <svg class="h-3.5 w-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <p class="text-xs text-neutral-700 dark:text-neutral-300 truncate">
                    Showing
                    @if (request('search'))
                        results for "<strong class="text-neutral-900 dark:text-white">{{ request('search') }}</strong>"
                    @endif
                    @if (request('cuisine'))
                        in <strong class="text-neutral-900 dark:text-white">{{ request('cuisine') }}</strong>
                    @endif
                    · <span class="text-orange-600 dark:text-orange-400 font-bold">{{ $restaurants->count() }} {{ Str::plural('result', $restaurants->count()) }}</span>
                </p>
            </div>

                <a href="{{ route('customer.restaurants') }}"
                    class="shrink-0 text-xs font-semibold text-orange-700 hover:underline dark:text-orange-400">
                Clear
            </a>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- HEADER WITH VIEW TOGGLE --}}
    {{-- ============================================ --}}
    @if ($restaurants->count() > 0)
        <div class="restaurant-results-heading order-4 flex items-center justify-between gap-4 border-b border-neutral-200 pb-4 dark:border-[#262626]">
            <div class="min-w-0">
                <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.18em] text-orange-700 dark:text-orange-300">The local list</p>
                <h2 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Places worth a visit</h2>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $restaurants->count() }} spots for your next meal</p>
            </div>

            {{-- VIEW TOGGLE --}}
            <div class="ml-auto inline-flex shrink-0 items-center rounded-lg border border-neutral-200 bg-white p-1 dark:border-[#404040] dark:bg-[#141414]" role="group" aria-label="Restaurant view">
                <button type="button" @click="viewMode = 'grid'"
                        :aria-pressed="viewMode === 'grid'"
                        :class="viewMode === 'grid' ? 'bg-orange-600 text-white' : 'text-neutral-500 hover:bg-neutral-100 hover:text-neutral-800 dark:text-neutral-400 dark:hover:bg-[#262626] dark:hover:text-white'"
                        class="flex h-9 w-9 items-center justify-center rounded-md transition" aria-label="Card view" title="Card view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button type="button" @click="viewMode = 'list'"
                        :aria-pressed="viewMode === 'list'"
                        :class="viewMode === 'list' ? 'bg-orange-600 text-white' : 'text-neutral-500 hover:bg-neutral-100 hover:text-neutral-800 dark:text-neutral-400 dark:hover:bg-[#262626] dark:hover:text-white'"
                        class="flex h-9 w-9 items-center justify-center rounded-md transition" aria-label="List view" title="List view">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================ --}}
    @if ($restaurants->isEmpty())
        <div class="order-5 py-16 text-center">
            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full border border-orange-200 bg-orange-50 dark:border-orange-900/60 dark:bg-orange-950/30">
                <svg class="h-8 w-8 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="font-semibold text-neutral-900 dark:text-white mb-1">
                @if (request('search') || request('cuisine'))
                    No restaurants found
                @else
                    No restaurants available
                @endif
            </h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
                @if (request('search') || request('cuisine'))
                    Try adjusting your search or filter
                @else
                    Check back later for available restaurants
                @endif
            </p>
            @if (request('search') || request('cuisine'))
                <a href="{{ route('customer.restaurants') }}"
                   class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg transition">
                    Clear Filters
                </a>
            @endif
        </div>
    @else

        {{-- ============================================ --}}
        {{-- GRID VIEW --}}
        {{-- ============================================ --}}
           <div x-show="viewMode === 'grid'"
               x-cloak
               class="restaurant-grid order-5 grid grid-cols-1 gap-5 min-[520px]:grid-cols-2 xl:grid-cols-3">
            @foreach ($restaurants as $restaurant)
                @php
                    $isOpen = $restaurant->isOpenNow();
                    $isFav = auth()->check() && auth()->user()->hasFavorited($restaurant);
                @endphp

                <div x-data="favoriteToggle({{ $restaurant->id }}, {{ $isFav ? 'true' : 'false' }})"
                     class="restaurant-card group relative h-full">

                    {{-- HEART BUTTON --}}
                    @auth
                        <button type="button"
                                @click.prevent.stop="toggleFavorite()"
                                :disabled="loading"
                                :title="isFavorite ? 'Remove from favorites' : 'Add to favorites'"
                                :class="loading ? 'opacity-50' : ''"
                                class="favorite-button absolute right-3 top-3 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-white/70 bg-white/95 shadow-sm transition hover:scale-105 active:scale-95 dark:border-[#404040] dark:bg-[#0a0a0a]/95">

                            <template x-if="isFavorite">
                                <svg class="w-5 h-5 text-red-500 fill-red-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </template>

                            <template x-if="!isFavorite">
                                <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </template>
                        </button>
                    @endauth

                    {{-- GUEST HEART --}}
                    @guest
                        <a href="{{ route('login') }}"
                           title="Sign in to save favorites"
                           class="favorite-button absolute right-3 top-3 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-white/70 bg-white/95 shadow-sm transition hover:scale-105 active:scale-95 dark:border-[#404040] dark:bg-[#0a0a0a]/95">
                            <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </a>
                    @endguest

                    {{-- CARD --}}
                    <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                              class="restaurant-card-link flex h-full flex-col overflow-hidden rounded-[1.35rem] border border-neutral-200 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 dark:border-[#262626] dark:bg-[#141414] dark:focus-visible:ring-offset-[#0a0a0a]">

                        {{-- COVER IMAGE --}}
                        <div class="restaurant-art relative aspect-[1.45/1] overflow-hidden shrink-0">
                            @if ($restaurant->cover_image_url)
                                <img src="{{ $restaurant->cover_image_url }}"
                                     alt="{{ $restaurant->name }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="restaurant-art-fallback absolute inset-0 flex items-center justify-center">
                                    <span class="art-sun"></span>
                                    <svg class="restaurant-art-icon relative z-10 h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- STATUS BADGE --}}
                            <div class="absolute top-2 left-2 z-10">
                                @if ($isOpen)
                                    <span class="status-pill inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-neutral-800 shadow-sm backdrop-blur dark:bg-[#0a0a0a]/95 dark:text-white">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] animate-pulse"></span>
                                        Open
                                    </span>
                                @else
                                    <span class="status-pill inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-neutral-600 shadow-sm backdrop-blur dark:bg-[#0a0a0a]/95 dark:text-neutral-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                        Closed
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- BODY --}}
                        <div class="restaurant-card-body flex flex-1 flex-col p-5">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <h2 class="font-semibold text-lg leading-tight tracking-tight text-neutral-900 transition group-hover:text-orange-700 dark:text-white dark:group-hover:text-orange-300">
                                    {{ $restaurant->name }}
                                </h2>
                            </div>

                            @if ($restaurant->display_badge)
                                <span class="mb-3 inline-block w-fit rounded-full bg-orange-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-orange-800 dark:bg-orange-950/30 dark:text-orange-300">
                                    {{ $restaurant->display_badge }}
                                </span>
                            @endif

                            <div class="mb-4 flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                                <svg class="h-4 w-4 flex-shrink-0 text-orange-700 dark:text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="line-clamp-1">{{ $restaurant->address }}</span>
                            </div>

                            <div class="card-meta mt-auto flex items-center justify-between border-t border-neutral-100 pt-4 dark:border-[#262626]">
                                <div class="flex items-center gap-1 text-xs">
                                    @if ($restaurant->rating_count > 0)
                                        <svg class="w-3.5 h-3.5 text-orange-500 fill-orange-500" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="font-bold text-neutral-900 dark:text-white">{{ number_format($restaurant->rating_avg, 1) }}</span>
                                        <span class="text-neutral-400 dark:text-neutral-500">({{ $restaurant->rating_count }})</span>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-neutral-300 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        <span class="text-neutral-400 dark:text-neutral-500 italic">No ratings</span>
                                    @endif
                                </div>

                                <span class="text-[10px] bg-neutral-100 dark:bg-[#262626] text-neutral-600 dark:text-neutral-400 px-2 py-0.5 rounded-full font-bold whitespace-nowrap">
                                    {{ $restaurant->menu_items_count }} items
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- ============================================ --}}
        {{-- LIST VIEW --}}
        {{-- ============================================ --}}
           <div x-show="viewMode === 'list'" x-cloak
               class="restaurant-list-view order-5 overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-[#262626] dark:bg-[#141414]">
            @foreach ($restaurants as $restaurant)
                @php
                    $isOpen = $restaurant->isOpenNow();
                    $isFav = auth()->check() && auth()->user()->hasFavorited($restaurant);
                @endphp

                <div x-data="favoriteToggle({{ $restaurant->id }}, {{ $isFav ? 'true' : 'false' }})"
                     class="restaurant-list-row relative flex items-center gap-4 px-4 py-4 transition hover:bg-orange-50/60 dark:hover:bg-[#0a0a0a] border-b border-neutral-100 dark:border-[#262626] last:border-0 group">

                    <a href="{{ route('customer.restaurants.show', $restaurant) }}"
                       class="flex items-center gap-3 flex-1 min-w-0">

                        {{-- Cover thumbnail --}}
                        <div class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                            @if ($restaurant->cover_image_url)
                                <img src="{{ $restaurant->cover_image_url }}"
                                     alt="{{ $restaurant->name }}"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            @endif

                            <span class="absolute top-1 right-1 w-3 h-3 rounded-full border-2 border-white dark:border-[#141414] {{ $isOpen ? 'bg-orange-500' : 'bg-neutral-400' }}"></span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="font-bold text-sm text-neutral-900 dark:text-white truncate group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">
                                    {{ $restaurant->name }}
                                </p>
                                @if ($restaurant->cuisine)
                                    <span class="text-[10px] bg-gradient-to-r from-orange-500 to-orange-600 text-white px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                                        {{ $restaurant->cuisine }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $restaurant->address }}</span>
                            </div>
                        </div>

                        {{-- Right side: rating + items --}}
                        <div class="flex-shrink-0 text-right hidden sm:block">
                            <div class="flex items-center justify-end gap-1 text-xs mb-1">
                                @if ($restaurant->rating_count > 0)
                                    <svg class="w-3.5 h-3.5 text-orange-500 fill-orange-500" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="font-bold text-neutral-900 dark:text-white">{{ number_format($restaurant->rating_avg, 1) }}</span>
                                @else
                                    <span class="text-[10px] text-neutral-400 dark:text-neutral-500 italic">No ratings</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-neutral-500 dark:text-neutral-400">
                                {{ $restaurant->menu_items_count }} items
                            </p>
                        </div>
                    </a>

                    {{-- HEART BUTTON --}}
                    @auth
                        <button type="button"
                                @click="toggleFavorite()"
                                :disabled="loading"
                                :title="isFavorite ? 'Remove from favorites' : 'Add to favorites'"
                                :class="loading ? 'opacity-50' : ''"
                                class="flex-shrink-0 w-9 h-9 rounded-lg hover:bg-neutral-100 dark:hover:bg-[#262626] flex items-center justify-center transition">

                            <template x-if="isFavorite">
                                <svg class="w-5 h-5 text-red-500 fill-red-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </template>
                            <template x-if="!isFavorite">
                                <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </template>
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                           title="Sign in to save favorites"
                           class="flex-shrink-0 w-9 h-9 rounded-lg hover:bg-neutral-100 dark:hover:bg-[#262626] flex items-center justify-center transition">
                            <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </a>
                    @endguest

                    {{-- Arrow --}}
                    <svg class="w-4 h-4 text-neutral-400 dark:text-neutral-300 flex-shrink-0 group-hover:translate-x-0.5 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ============================================ --}}
{{-- FLOATING LOGIN MODAL (guests only) --}}
{{-- Colors: Orange #FF6B35 | Dark Gray #2D2D2D | White #FFFFFF --}}
{{-- Light mode = white card | Dark mode = near-black card --}}
{{-- ============================================ --}}
@guest
<div x-data="loginModal({{ ($errors->any() || request('login') === '1') ? 'true' : 'false' }})"
     x-init="init()"
     x-show="open"
     x-cloak
     x-transition.opacity
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-[#2D2D2D]/60 p-4 backdrop-blur-sm dark:bg-black/70"
     @click.self="open = false"
     role="dialog" aria-modal="true" aria-label="Sign in">

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative grid max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-3xl bg-white shadow-2xl shadow-black/40 dark:border dark:border-white/10 dark:bg-[#141414] md:grid-cols-2">

        {{-- CLOSE BUTTON --}}
        <button type="button" @click="open = false" aria-label="Close" title="Close"
                class="absolute right-4 top-4 z-30 flex h-10 w-10 items-center justify-center rounded-full border border-[#2D2D2D]/15 bg-white text-[#2D2D2D] shadow-sm transition hover:border-[#FF6B35] hover:text-[#FF6B35] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#FF6B35]/30 dark:border-white/15 dark:bg-[#2D2D2D] dark:text-white dark:hover:border-[#FF6B35] dark:hover:text-[#FF6B35]">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- LEFT: BRAND PANEL (dark gray in both modes) --}}
        <aside class="relative hidden flex-col justify-between overflow-hidden bg-[#2D2D2D] px-10 py-10 text-white md:flex">
            <div class="absolute -top-20 -left-20 h-56 w-56 rounded-full bg-[#FF6B35]"></div>
            <div class="absolute -bottom-24 -right-16 h-64 w-64 rounded-full border-[24px] border-[#FF6B35]/25"></div>

            <div class="relative">
                <span class="text-2xl font-extrabold">DashBite</span>
            </div>

            <div class="relative mt-16">
                <h2 class="text-4xl font-extrabold leading-tight">
                    Make room<br><span class="text-[#FF6B35]">for a good meal.</span>
                </h2>
                <p class="mt-3 max-w-xs text-sm text-white/70">
                    Find a nearby kitchen, pick something you love, and let dinner take care of itself.
                </p>
            </div>

            <p class="relative mt-8 text-xs text-white/50">
                © {{ date('Y') }} DashBite. All rights reserved.
            </p>
        </aside>

        {{-- RIGHT: FORM --}}
        <div class="flex items-center bg-white px-6 py-10 dark:bg-[#141414] md:px-10">
            <div class="w-full">
                <div class="mb-6">
                    <h2 class="text-3xl font-extrabold text-[#2D2D2D] dark:text-white">Welcome!</h2>
                    <p class="mt-1 text-sm text-[#2D2D2D]/60 dark:text-white/60">Sign in to continue exploring DashBite.</p>
                </div>

                @if (session('success'))
                    <div class="mb-5 flex items-start gap-2 rounded-xl border-l-4 border-[#FF6B35] bg-[#FF6B35]/10 p-3 text-sm text-[#2D2D2D] dark:bg-[#FF6B35]/15 dark:text-white">
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-2 rounded-xl border-l-4 border-[#FF6B35] bg-[#2D2D2D] p-3 text-sm text-white">
                        <span class="font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-4"
                      x-data="{ showPassword: false }" @submit.prevent="signIn($event)">
                    @csrf

                    {{-- EMAIL --}}
                    <div>
                        <label for="modal-email" class="mb-1.5 block text-sm font-semibold text-[#2D2D2D] dark:text-white">Email address</label>
                        <input type="email" id="modal-email" name="email" required
                               placeholder="you@example.com"
                               class="w-full rounded-xl border-2 border-[#2D2D2D]/15 bg-white px-4 py-3 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/40 transition focus:border-[#FF6B35] focus:outline-none focus:ring-4 focus:ring-[#FF6B35]/20 dark:border-white/15 dark:bg-[#0a0a0a] dark:text-white dark:placeholder-white/30 dark:focus:border-[#FF6B35]">
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="modal-password" class="block text-sm font-semibold text-[#2D2D2D] dark:text-white">Password</label>
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="modal-password" name="password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border-2 border-[#2D2D2D]/15 bg-white py-3 pl-4 pr-11 text-sm text-[#2D2D2D] placeholder-[#2D2D2D]/40 transition focus:border-[#FF6B35] focus:outline-none focus:ring-4 focus:ring-[#FF6B35]/20 dark:border-white/15 dark:bg-[#0a0a0a] dark:text-white dark:placeholder-white/30 dark:focus:border-[#FF6B35]">
                            <button type="button" @click="showPassword = !showPassword" aria-label="Show or hide password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#2D2D2D]/40 transition hover:text-[#FF6B35] dark:text-white/40 dark:hover:text-[#FF6B35]">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="modal-remember"
                               class="h-4 w-4 rounded border-[#2D2D2D]/30 text-[#FF6B35] focus:ring-[#FF6B35] dark:border-white/30 dark:bg-[#0a0a0a]">
                        <label for="modal-remember" class="ml-2 select-none text-sm text-[#2D2D2D]/70 dark:text-white/70">Remember me</label>
                    </div>

                    {{-- SIGN IN (orange in both modes) --}}
                    <button type="submit" :disabled="loading"
                            class="flex w-full items-center justify-center rounded-xl bg-[#FF6B35] py-3.5 text-sm font-bold text-white shadow-md shadow-[#FF6B35]/30 transition hover:brightness-95 active:scale-[0.98] disabled:cursor-wait disabled:opacity-70">
                        <span x-text="loading ? 'Signing in…' : 'Sign in'">Sign in</span>
                    </button>
                    <p x-show="error" x-text="error" role="alert"
                       class="rounded-xl border-l-4 border-[#FF6B35] bg-[#2D2D2D] p-3 text-sm text-white"></p>
                </form>

                <p class="mt-4 text-center text-xs text-[#2D2D2D]/55 dark:text-white/55">
                    One sign in for every DashBite account.
                </p>

                {{-- CONTINUE AS GUEST --}}
                <button type="button" @click="open = false"
                        class="mt-4 w-full text-center text-xs text-[#2D2D2D]/50 transition hover:text-[#FF6B35] dark:text-white/50 dark:hover:text-[#FF6B35]">
                    Continue as guest
                </button>
            </div>
        </div>
    </div>
</div>
@endguest
@endsection

@push('scripts')
<style>
    [x-cloak] {
        display: none !important;
    }

    /* Hide scrollbar for Chrome, Safari, Edge */
    .carousel-scroll::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for Firefox */
    .carousel-scroll {
        scrollbar-width: none;
    }

    /* Hide scrollbar for IE/Edge legacy */
    .carousel-scroll {
        -ms-overflow-style: none;
    }

    .cuisine-scroll {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .cuisine-scroll::-webkit-scrollbar {
        display: none;
    }
</style>
<script>
function loginModal(startOpen) {
    return {
        open: startOpen,
        loading: false,
        error: '',

        init() {
            // Any link to /login opens the floating panel instead
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a[href]');
                if (!link || e.ctrlKey || e.metaKey || e.shiftKey) return;
                try {
                    const url = new URL(link.href, window.location.origin);
                    if (url.pathname === '/login') {
                        e.preventDefault();
                        this.open = true;
                    }
                } catch (err) {}
            });

            // Lock page scroll and focus the email box while open
            this.$watch('open', (value) => {
                document.body.classList.toggle('overflow-hidden', value);
                if (value) this.$nextTick(() => document.getElementById('modal-email')?.focus());
            });

            if (this.open) document.body.classList.add('overflow-hidden');
        },

        async signIn(event) {
            if (this.loading) return;

            const form = event.target;
            const formData = new FormData(form);
            this.loading = true;
            this.error = '';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        email: formData.get('email'),
                        password: formData.get('password'),
                        remember: formData.get('remember') === 'on',
                    }),
                });
                const payload = await response.json();

                if (!response.ok) {
                    const validationMessage = payload.errors
                        ? Object.values(payload.errors).flat().find((message) => typeof message === 'string')
                        : null;
                    throw new Error(validationMessage || payload.message || 'Sign in failed. Check your details and try again.');
                }

                if (payload.role === 'customer') {
                    window.location.reload();
                    return;
                }

                if (!payload.token || !payload.user?.role || !payload.user?.account_status) {
                    throw new Error('The server returned an unexpected response. Please try again.');
                }

                sessionStorage.setItem('dashbite.auth.token', payload.token);
                sessionStorage.setItem('dashbite.auth.user', JSON.stringify(payload.user));
                this.open = false;
                window.dispatchEvent(new CustomEvent('dashbite:show-portal', { detail: payload }));
            } catch (error) {
                this.error = error.message || 'Unable to reach the sign-in service. Check your connection and try again.';
            } finally {
                this.loading = false;
            }
        },
    }
}

function restaurantList() {
    return {
        viewMode: 'grid',
        viewKey: 'fooddash_restaurants_view',

        init() {
            const saved = localStorage.getItem(this.viewKey);
            if (saved && ['grid', 'list'].includes(saved)) {
                this.viewMode = saved;
            }

            this.$watch('viewMode', (v) => localStorage.setItem(this.viewKey, v));
        }
    }
}

function favoriteToggle(restaurantId, initialIsFavorite) {
    return {
        restaurantId,
        isFavorite: initialIsFavorite,
        loading: false,

        async toggleFavorite() {
            if (this.loading) return;
            this.loading = true;

            try {
                const res = await fetch(`/favorites/${this.restaurantId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                if (!res.ok) {
                    console.error('Toggle failed:', res.status);
                    this.loading = false;
                    return;
                }

                const data = await res.json();
                if (data.ok) {
                    this.isFavorite = data.is_favorite;
                }
            } catch (err) {
                console.error('Network error:', err);
            }

            this.loading = false;
        }
    }
}

function menuCarousel() {
    return {
        items: [],
        loading: false,
        countdown: 30,
        isPaused: false,
        refreshInterval: null,
        countdownInterval: null,
        rafId: null,
        lastFrameTime: 0,
        scrollSpeed: 0.5,
        isDragging: false,

        async init() {
            await this.fetchItems();

            this.countdownInterval = setInterval(() => {
                this.countdown = Math.max(0, this.countdown - 1);
            }, 1000);

            this.refreshInterval = setInterval(() => {
                this.fetchItems();
                this.countdown = 30;
            }, 30000);

            this.$nextTick(() => {
                this.startAnimationLoop();
            });

            window.addEventListener('beforeunload', () => {
                if (this.rafId) cancelAnimationFrame(this.rafId);
            });
        },

        async fetchItems() {
            this.loading = true;

            try {
                const res = await fetch('{{ route('customer.menu.random') }}', {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    this.loading = false;
                    return;
                }

                const data = await res.json();
                const newItems = data.items || [];

                this.items = [...newItems, ...newItems];

                this.$nextTick(() => {
                    if (this.$refs.scroller) {
                        this.$refs.scroller.scrollLeft = 0;
                    }

                    this.startAnimationLoop();
                });
            } catch (err) {
                console.error('Failed to fetch recommendations:', err);
            }

            this.loading = false;
        },

        startAnimationLoop() {
            if (this.rafId) return;

            const el = this.$refs.scroller;
            if (!el) return;

            const animate = () => {
                const scroller = this.$refs.scroller;
                if (!scroller) {
                    this.rafId = null;
                    return;
                }

                if (!this.isPaused && !this.isDragging && scroller.scrollWidth > scroller.clientWidth) {
                    const halfWidth = scroller.scrollWidth / 2;

                    scroller.scrollLeft += this.scrollSpeed;

                    if (scroller.scrollLeft >= halfWidth) {
                        scroller.scrollLeft -= halfWidth;
                    }
                }

                this.rafId = requestAnimationFrame(animate);
            };

            this.rafId = requestAnimationFrame(animate);
        },

        pause() {
            this.isPaused = true;
        },

        resume() {
            this.isPaused = false;
        },

        onDragStart() {
            this.isDragging = true;
        },

        onDragEnd() {
            this.isDragging = false;
        },

        scrollLeft() {
            const el = this.$refs.scroller;
            if (!el) return;

            const firstCard = el.querySelector('a');
            const step = firstCard ? firstCard.offsetWidth + 12 : 200;
            el.scrollBy({ left: -step, behavior: 'smooth' });
        },

        scrollRight() {
            const el = this.$refs.scroller;
            if (!el) return;

            const firstCard = el.querySelector('a');
            const step = firstCard ? firstCard.offsetWidth + 12 : 200;
            el.scrollBy({ left: step, behavior: 'smooth' });
        }
    }
}
</script>
@endpush