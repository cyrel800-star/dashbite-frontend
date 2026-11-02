<div class="relative group bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-700 transition-all duration-200"
     x-data="favoriteFoodToggle({{ $item->id }})">
    <a href="{{ route('customer.restaurants.show', $item->restaurant) }}" class="block">
        <div class="relative h-40 overflow-hidden">
            @if ($item->image_url)
                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-50 dark:from-dark-850 dark:to-dark-900 flex items-center justify-center"><span class="text-5xl opacity-40">🍴</span></div>
            @endif
        </div>
        <div class="p-4">
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 text-base leading-tight group-hover:text-orange-600 dark:group-hover:text-orange-400 transition line-clamp-1">{{ $item->name }}</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5 line-clamp-1">From: {{ $item->restaurant->name }}</p>
            <p class="text-lg font-bold text-orange-600 dark:text-orange-400 mt-2">₱{{ number_format($item->price, 2) }}</p>
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