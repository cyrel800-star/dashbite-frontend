@php
    $isOpen = $restaurant->isOpenNow();
@endphp
<div class="relative group bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200"
     x-data="favoriteToggle({{ $restaurant->id }}, 'restaurant')">
    <a href="{{ route('customer.restaurants.show', $restaurant) }}" class="block">
        <div class="relative h-32 overflow-hidden">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}" alt="{{ $restaurant->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-orange-400 via-orange-500 to-amber-500"></div>
                <div class="absolute inset-0 flex items-center justify-center"><span class="text-5xl opacity-40">🍽️</span></div>
            @endif
            <div class="absolute top-3 left-3">
                @if ($isOpen)
                    <span class="inline-flex items-center gap-1.5 bg-white/95 dark:bg-dark-800/95 backdrop-blur text-green-700 dark:text-green-400 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm"><span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Open</span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-white/95 dark:bg-dark-800/95 backdrop-blur text-red-700 dark:text-red-400 text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm px-2.5 py-1"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Closed</span>
                @endif
            </div>
        </div>
        <div class="p-5">
            <div class="flex items-start justify-between gap-3 mb-2">
                <h2 class="font-bold text-gray-900 dark:text-neutral-100 text-base leading-tight group-hover:text-orange-600 dark:group-hover:text-orange-400 transition line-clamp-1">{{ $restaurant->name }}</h2>
                <span class="text-[10px] bg-gray-100 dark:bg-dark-850 text-gray-600 dark:text-neutral-400 px-2 py-1 rounded-full font-semibold whitespace-nowrap">{{ $restaurant->menu_items_count }} items</span>
            </div>
            @if ($restaurant->cuisine)
                <span class="inline-block text-[10px] bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide mb-2">{{ $restaurant->cuisine }}</span>
            @endif
            <div class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-neutral-400 mb-3">
                <svg class="w-3.5 h-3.5 text-gray-400 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="line-clamp-1">{{ $restaurant->address }}</span>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-dark-700">
                <div class="flex items-center gap-1 text-xs">
                    @if ($isOpen)
                        <span class="font-medium text-green-600 dark:text-green-400">Open now</span>
                    @else
                        <span class="font-medium text-red-600 dark:text-red-400 line-clamp-1">{{ $restaurant->status_label }}</span>
                    @endif
                </div>
                <span class="text-xs font-semibold text-orange-600 dark:text-orange-400 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">View Menu <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></span>
            </div>
        </div>
    </a>
    <button @click.stop.prevent="removeFavorite()"
            type="button"
            :disabled="loading"
            :class="loading ? 'opacity-50' : ''"
            title="Remove from favorites"
            class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/95 dark:bg-dark-800/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition transform">
        <svg class="w-5 h-5 text-red-500 fill-red-500" viewBox="0 0 24 24" fill="currentColor"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
    </button>
</div>