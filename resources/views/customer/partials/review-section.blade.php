{{-- ============================================ --}}
{{-- TAB NAVIGATION (Menu / Reviews) --}}
{{-- ============================================ --}}
<div x-data="{ tab: 'menu' }" class="mt-6">
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden">
        
        {{-- Tab buttons --}}
        <div class="flex border-b border-gray-200 dark:border-dark-700">
            <button @click="tab = 'menu'"
                    :class="tab === 'menu' 
                        ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 font-bold' 
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200'"
                    class="flex-1 py-4 px-6 text-sm font-semibold transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Menu
            </button>
            <button @click="tab = 'reviews'"
                    :class="tab === 'reviews' 
                        ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 font-bold' 
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200'"
                    class="flex-1 py-4 px-6 text-sm font-semibold transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Reviews ({{ $restaurant->rating_count }})
            </button>
        </div>
    </div>
</div>