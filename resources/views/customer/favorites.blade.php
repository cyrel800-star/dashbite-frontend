@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Saved</p>
                    <h1 class="text-xl font-bold leading-tight">My Favorites</h1>
                </div>
            </div>

            <p class="text-sm text-white/90 max-w-md mb-5">
                List of your favorite restaurants and dishes
            </p>

            {{-- MINI STATS --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/20">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Restaurants</p>
                    <p class="text-xl font-bold mt-1">{{ $favorites->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Foods</p>
                    <p class="text-xl font-bold mt-1">{{ $favoriteFoods->count() ?? 0 }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Cuisines</p>
                    <p class="text-xl font-bold mt-1">{{ $favorites->pluck('cuisine')->filter()->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABS — Restaurants vs Foods --}}
    {{-- ============================================ --}}
    <div x-data="{ tab: 'restaurants' }" class="space-y-5">

        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="flex">
                <button type="button"
                        @click="tab = 'restaurants'"
                        :class="tab === 'restaurants'
                            ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20 font-bold'
                            : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 font-semibold'"
                        class="flex-1 py-4 px-6 text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Restaurants</span>
                    <span class="bg-gray-100 dark:bg-dark-700 text-gray-700 dark:text-neutral-300 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $favorites->count() }}
                    </span>
                </button>

                <button type="button"
                        @click="tab = 'foods'"
                        :class="tab === 'foods'
                            ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20 font-bold'
                            : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 font-semibold'"
                        class="flex-1 py-4 px-6 text-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Foods</span>
                    <span class="bg-gray-100 dark:bg-dark-700 text-gray-700 dark:text-neutral-300 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $favoriteFoods->count() ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- RESTAURANTS TAB --}}
        {{-- ============================================ --}}
        <div x-show="tab === 'restaurants'" x-cloak>
            @if ($favorites->isEmpty())
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-950/40 mb-4">
                        <svg class="w-10 h-10 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No favorite restaurants yet</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                        Tap the heart button on a restaurant to save it here
                    </p>
                    <a href="{{ route('customer.restaurants') }}"
                       class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md">
                        Browse Restaurants
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                    @foreach ($favorites as $restaurant)
                        @php $isOpen = $restaurant->isOpenNow(); @endphp

                        <div class="relative group bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-700 transition-all duration-200"
                             x-data="favoriteToggle({{ $restaurant->id }})">

                            <a href="{{ route('customer.restaurants.show', $restaurant) }}" class="block">

                                {{-- COVER IMAGE --}}
                                <div class="relative aspect-square overflow-hidden">
                                    <img src="{{ $restaurant->cover_image_url ?: asset('images/noimg.png') }}"
     alt="{{ $restaurant->name }}"
     onerror="this.onerror=null; this.src='{{ asset('images/noimg.png') }}';"
     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                    {{-- OPEN/CLOSED BADGE --}}
                                    <div class="absolute top-2 left-2 z-10">
                                        @if ($isOpen)
                                            <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-dark-800/95 backdrop-blur text-orange-700 dark:text-orange-300 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                                Open
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-dark-800/95 backdrop-blur text-gray-700 dark:text-neutral-300 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                                Closed
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- BODY --}}
                                <div class="p-3">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <h2 class="font-bold text-gray-900 dark:text-neutral-100 text-sm leading-tight line-clamp-1 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition">
                                            {{ $restaurant->name }}
                                        </h2>
                                    </div>

                                    @if ($restaurant->display_badge)
                                        <span class="inline-block text-[10px] bg-orange-800 dark:bg-orange-900 text-white px-2 py-0.5 rounded-full font-bold uppercase tracking-wider mb-2">
                                            {{ $restaurant->display_badge }}
                                        </span>
                                    @endif

                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-neutral-400 mb-2">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="line-clamp-1">{{ $restaurant->address }}</span>
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-dark-700">
                                        <div class="flex items-center gap-1 text-xs">
                                            @if ($restaurant->rating_count > 0)
                                                <svg class="w-3.5 h-3.5 text-orange-500 fill-orange-500" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="font-bold text-gray-900 dark:text-neutral-100">
                                                    {{ number_format($restaurant->rating_avg, 1) }}
                                                </span>
                                                <span class="text-gray-400 dark:text-neutral-500">
                                                    ({{ $restaurant->rating_count }})
                                                </span>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-gray-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                </svg>
                                                <span class="text-gray-400 dark:text-neutral-500 italic">No ratings</span>
                                            @endif
                                        </div>

                                        <span class="text-[10px] bg-gray-100 dark:bg-dark-850 text-gray-600 dark:text-neutral-400 px-2 py-0.5 rounded-full font-semibold whitespace-nowrap">
                                            {{ $restaurant->menu_items_count }} items
                                        </span>
                                    </div>
                                </div>
                            </a>

                            {{-- HEART BUTTON --}}
                            <button @click.stop.prevent="removeFavorite()"
                                    type="button"
                                    :disabled="loading"
                                    title="Remove from favorites"
                                    class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-white/95 dark:bg-dark-800/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition">
                                <svg class="w-4 h-4 text-orange-500 fill-orange-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- FOODS TAB --}}
        {{-- ============================================ --}}
        <div x-show="tab === 'foods'" x-cloak>
            @if (($favoriteFoods ?? collect())->isEmpty())
                <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-950/40 mb-4">
                        <svg class="w-10 h-10 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No favorite foods yet</h3>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                        Tap the heart button on a dish to save it here
                    </p>
                    <a href="{{ route('customer.restaurants') }}"
                       class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md">
                        Browse Restaurants
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
                    @foreach ($favoriteFoods as $item)
                        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-700 transition group flex flex-col"
                             x-data="favoriteFoodToggle({{ $item->id }}, true)">

                            {{-- IMAGE --}}
                                <a href="{{ $item->restaurant ? route('customer.restaurants.show', $item->restaurant) : '#' }}"
                                class="block relative aspect-square overflow-hidden">
                                    <img src="{{ $item->image_path ? Storage::disk('public')->url($item->image_path) : asset('images/noimg.png') }}"
                                    alt="{{ $item->name }}"
                                    onerror="this.onerror=null; this.src='{{ asset('images/noimg.png') }}';"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                {{-- Restaurant badge --}}
                                @if ($item->restaurant)
                                    <div class="absolute bottom-2 left-2 z-10">
                                        <span class="bg-black/60 backdrop-blur text-white text-[10px] font-semibold px-2 py-0.5 rounded-full line-clamp-1 max-w-[120px] truncate">
                                            {{ $item->restaurant->name }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Heart button --}}
                                <button @click.stop.prevent="toggleFavorite()"
                                        type="button"
                                        :disabled="loading"
                                        class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-white/95 dark:bg-dark-800/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition">
                                    <svg class="w-4 h-4 text-orange-500 fill-orange-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </button>
                            </a>

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

                                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-dark-700">
                                    <p class="font-bold text-orange-600 dark:text-orange-400 text-sm">
                                        ₱{{ number_format($item->price, 0) }}
                                    </p>

                                    @if ($item->restaurant)
                                        <a href="{{ route('customer.restaurants.show', $item->restaurant) }}"
                                           class="text-xs font-semibold text-orange-600 dark:text-orange-400 hover:underline inline-flex items-center gap-0.5">
                                            Order
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function favoriteToggle(restaurantId) {
    return {
        restaurantId,
        loading: false,

        async removeFavorite() {
            if (this.loading) return;
            this.loading = true;

            try {
                const res = await fetch(`/favorites/${this.restaurantId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await res.json();
                if (!data.is_favorite) {
                    setTimeout(() => location.reload(), 200);
                }
            } catch (err) {
                console.error(err);
                this.loading = false;
            }
        }
    }
}

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
                if (data.ok) {
                    this.isFavorite = data.is_favorite;
                    if (!data.is_favorite) {
                        this.$el.closest('.grid > div')?.remove();
                    }
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush