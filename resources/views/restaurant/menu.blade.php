@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-5" x-data="menuManager()" x-init="init()">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <a href="{{ route('restaurant.dashboard') }}"
                       class="w-10 h-10 rounded-xl bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Restaurant</p>
                        <h1 class="text-xl font-bold leading-tight">Menu Items</h1>
                    </div>
                </div>

                <div class="bg-white/15 backdrop-blur rounded-full px-3.5 py-1.5 border border-white/20">
                    <span class="text-xs font-bold uppercase tracking-wide">{{ $items->count() }} Items</span>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-3 gap-3 pt-5 border-t border-white/20 mt-5">
                <div>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total</p>
                    <p class="text-xl font-bold mt-1">{{ $items->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Available</p>
                    <p class="text-xl font-bold mt-1">{{ $items->where('is_available', true)->count() }}</p>
                </div>
                <div class="border-l border-white/20 pl-3">
                    <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">With Photos</p>
                    <p class="text-xl font-bold mt-1">{{ $items->whereNotNull('image_path')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="p-4 bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-neutral-800 dark:text-neutral-200 font-medium">{{ session('success') }}</span>
        </div>
    @endif

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
    {{-- ADD NEW ITEM --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <button type="button"
                @click="showAddForm = !showAddForm"
                class="w-full flex items-center justify-between px-6 py-4 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="text-left">
                    <p class="font-bold text-neutral-900 dark:text-white">Add New Item</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Idagdag ang bagong menu item kasama ang larawan</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-500 transition-transform duration-200"
                 :class="showAddForm ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="showAddForm" x-cloak x-transition class="border-t border-neutral-100 dark:border-[#262626]">
            <form method="POST" action="{{ route('menu-items.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Item Name <span class="text-orange-500">*</span>
                        </label>
                        <input type="text" name="name" placeholder="e.g. Pepperoni Pizza" required
                               class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Price (₱) <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-neutral-500 text-sm font-bold">₱</span>
                            <input type="number" name="price" placeholder="0.00" step="0.01" min="0" required
                                   class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl pl-8 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Description <span class="text-neutral-400 dark:text-neutral-500 font-normal">(optional)</span>
                    </label>
                    <textarea name="description" rows="2" placeholder="Ilarawan ang item..."
                              class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"></textarea>
                </div>

                {{-- IMAGE UPLOAD --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Food Photo <span class="text-neutral-400 dark:text-neutral-500 font-normal">(optional)</span>
                    </label>
                    <label class="block cursor-pointer">
                        <input type="file"
                               name="image"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               @change="onFileChange($event)"
                               class="hidden">
                        <div class="border-2 border-dashed border-neutral-300 dark:border-[#262626] rounded-xl p-6 text-center hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition">
                            <template x-if="!imagePreview">
                                <div>
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                        <span class="text-orange-600 dark:text-orange-400 font-bold">Click to upload</span> photo
                                    </p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">JPG, PNG, WebP · Max 5MB</p>
                                </div>
                            </template>
                            <template x-if="imagePreview">
                                <div class="flex flex-col items-center">
                                    <img :src="imagePreview" class="w-32 h-32 rounded-xl object-cover shadow-md">
                                    <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mt-2">Click to change</p>
                                </div>
                            </template>
                        </div>
                    </label>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit"
                            class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Add Item
                    </button>
                    <button type="button"
                            @click="showAddForm = false; resetForm()"
                            class="border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- MENU ITEMS LIST --}}
    {{-- ============================================ --}}
    @if ($items->isEmpty())
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-4">
                <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h3 class="font-semibold text-neutral-900 dark:text-white mb-1">No menu items yet</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">Start by adding your first menu item</p>
            <button type="button"
                    @click="showAddForm = true"
                    class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-95 transition transform">
                + Add First Item
            </button>
        </div>
    @else

        {{-- ============================================ --}}
        {{-- VIEW TOGGLE + FILTER BAR --}}
        {{-- ============================================ --}}
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-900 dark:text-white">All Items</h2>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- FILTER: AVAILABILITY --}}
                    <div class="hidden sm:flex items-center gap-1 bg-neutral-100 dark:bg-[#0a0a0a] rounded-lg p-1 border border-neutral-200 dark:border-[#262626]">
                        <button type="button" @click="filter = 'all'"
                                :class="filter === 'all' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                                class="px-3 py-1.5 rounded-md text-xs font-bold transition">
                            All
                        </button>
                        <button type="button" @click="filter = 'available'"
                                :class="filter === 'available' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                                class="px-3 py-1.5 rounded-md text-xs font-bold transition">
                            Available
                        </button>
                        <button type="button" @click="filter = 'unavailable'"
                                :class="filter === 'unavailable' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200'"
                                class="px-3 py-1.5 rounded-md text-xs font-bold transition">
                            Unavailable
                        </button>
                    </div>

                    {{-- VIEW TOGGLE --}}
                    <div class="flex items-center gap-1 bg-neutral-200 dark:bg-[#0a0a0a] rounded-lg p-1 border border-neutral-200 dark:border-[#262626]">
                        <button type="button" @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                                class="p-2 rounded-md transition" title="Grid view">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-gradient-to-r from-orange-500 to-orange-600 shadow-sm text-white' : 'text-neutral-500 dark:text-neutral-400 hover:text-neutral-700'"
                                class="p-2 rounded-md transition" title="List view">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- GRID VIEW — 4 columns --}}
        {{-- ============================================ --}}
        <div x-show="viewMode === 'grid'" x-cloak x-transition.opacity
             class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($items as $item)
                <div x-show="filter === 'all' || (filter === 'available' && {{ $item->is_available ? 'true' : 'false' }}) || (filter === 'unavailable' && {{ !$item->is_available ? 'true' : 'false' }})"
                     class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800/50 transition-all duration-200 group flex flex-col">

                    {{-- IMAGE --}}
                    <div class="relative aspect-square overflow-hidden">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->name }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-neutral-100 to-neutral-50 dark:from-[#0a0a0a] dark:to-[#141414] flex items-center justify-center">
                                <svg class="w-16 h-16 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif

                        {{-- STATUS BADGE --}}
                        <div class="absolute top-2 left-2 z-10">
                            @if ($item->is_available)
                                <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur text-orange-600 dark:text-orange-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm border border-orange-200 dark:border-orange-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                    Available
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur text-neutral-600 dark:text-neutral-400 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm border border-neutral-200 dark:border-[#262626]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                    Unavailable
                                </span>
                            @endif
                        </div>

                        {{-- ACTIONS OVERLAY --}}
                        <div class="absolute top-2 right-2 z-10 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition">
                            {{-- TOGGLE --}}
                            <form method="POST" action="{{ route('menu-items.toggle', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        title="{{ $item->is_available ? 'Mark unavailable' : 'Mark available' }}"
                                        class="w-8 h-8 rounded-full bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur shadow-md flex items-center justify-center hover:scale-110 active:scale-95 transition transform {{ $item->is_available ? 'text-orange-600' : 'text-neutral-500 dark:text-neutral-400' }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        @if ($item->is_available)
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        @endif
                                    </svg>
                                </button>
                            </form>

                            {{-- EDIT --}}
                            <button type="button"
                                    @click="openEdit({
                                        id: {{ $item->id }},
                                        name: @js($item->name),
                                        description: @js($item->description),
                                        price: {{ $item->price }},
                                        image_url: @js($item->image_url),
                                    })"
                                    title="Edit"
                                    class="w-8 h-8 rounded-full bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur shadow-md flex items-center justify-center text-neutral-700 dark:text-neutral-300 hover:scale-110 active:scale-95 transition transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            {{-- DELETE --}}
                            <button type="button"
                                    @click="openDelete({
                                        id: {{ $item->id }},
                                        name: @js($item->name),
                                        action: '{{ route('menu-items.destroy', $item) }}',
                                    })"
                                    title="Delete"
                                    class="w-8 h-8 rounded-full bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur shadow-md flex items-center justify-center text-neutral-700 dark:text-neutral-300 hover:text-orange-600 dark:hover:text-orange-400 hover:scale-110 active:scale-95 transition transform">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="p-3 flex flex-col flex-1">
                        <p class="font-bold text-sm text-neutral-900 dark:text-white line-clamp-1 mb-1">
                            {{ $item->name }}
                        </p>

                        @if ($item->description)
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 line-clamp-2 mb-2 flex-1">
                                {{ $item->description }}
                            </p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-neutral-100 dark:border-[#262626] mt-auto">
                            <p class="font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent text-sm">
                                ₱{{ number_format($item->price, 0) }}
                            </p>

                            <span class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono">
                                #{{ $item->id }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ============================================ --}}
        {{-- LIST VIEW --}}
        {{-- ============================================ --}}
        <div x-show="viewMode === 'list'" x-cloak x-transition.opacity
             class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden">
            @foreach ($items as $item)
                <div x-show="filter === 'all' || (filter === 'available' && {{ $item->is_available ? 'true' : 'false' }}) || (filter === 'unavailable' && {{ !$item->is_available ? 'true' : 'false' }})"
                     class="flex items-center gap-3 px-4 py-3 hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition border-b border-neutral-100 dark:border-[#262626] last:border-0 group">

                    {{-- IMAGE --}}
                    <div class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}"
                                 alt="{{ $item->name }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-neutral-100 to-neutral-50 dark:from-[#0a0a0a] dark:to-[#141414] flex items-center justify-center">
                                <svg class="w-8 h-8 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif

                        {{-- Status dot --}}
                        <span class="absolute top-1 right-1 w-3 h-3 rounded-full border-2 border-white dark:border-[#141414] {{ $item->is_available ? 'bg-orange-500' : 'bg-neutral-400' }}"></span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="font-bold text-sm text-neutral-900 dark:text-white truncate">
                                {{ $item->name }}
                            </p>
                            @if (!$item->is_available)
                                <span class="text-[10px] bg-neutral-100 dark:bg-[#262626] text-neutral-600 dark:text-neutral-400 px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                                    Unavailable
                                </span>
                            @endif
                        </div>

                        @if ($item->description)
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 line-clamp-1">
                                {{ $item->description }}
                            </p>
                        @endif

                        <p class="text-sm font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent mt-0.5">
                            ₱{{ number_format($item->price, 2) }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        {{-- TOGGLE --}}
                        <form method="POST" action="{{ route('menu-items.toggle', $item) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    title="{{ $item->is_available ? 'Mark unavailable' : 'Mark available' }}"
                                    class="w-9 h-9 rounded-lg {{ $item->is_available ? 'bg-gradient-to-br from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white shadow-sm' : 'bg-neutral-100 dark:bg-[#262626] hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-500 dark:text-neutral-400' }} flex items-center justify-center transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    @if ($item->is_available)
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    @endif
                                </svg>
                            </button>
                        </form>

                        {{-- EDIT --}}
                        <button type="button"
                                @click="openEdit({
                                    id: {{ $item->id }},
                                    name: @js($item->name),
                                    description: @js($item->description),
                                    price: {{ $item->price }},
                                    image_url: @js($item->image_url),
                                })"
                                title="Edit"
                                class="w-9 h-9 rounded-lg bg-neutral-100 dark:bg-[#262626] hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-700 dark:text-neutral-300 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        {{-- DELETE --}}
                        <button type="button"
                                @click="openDelete({
                                    id: {{ $item->id }},
                                    name: @js($item->name),
                                    action: '{{ route('menu-items.destroy', $item) }}',
                                })"
                                title="Delete"
                                class="w-9 h-9 rounded-lg bg-neutral-100 dark:bg-[#262626] hover:bg-orange-100 dark:hover:bg-orange-950/40 text-neutral-700 dark:text-neutral-300 hover:text-orange-600 dark:hover:text-orange-400 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

    {{-- ============================================ --}}
    {{-- EDIT MODAL --}}
    {{-- ============================================ --}}
    <div x-show="editingItem"
         x-cloak
         x-transition.opacity
         @keydown.escape.window="closeEdit()"
         @click.self="closeEdit()"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60">

        <div x-show="editingItem"
             x-transition.scale.origin.center
             class="bg-white dark:bg-[#141414] rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-neutral-200 dark:border-[#262626]">

            {{-- MODAL HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-neutral-200 dark:border-[#262626]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-neutral-900 dark:text-white">Edit Menu Item</h2>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400" x-text="'Editing #' + (editingItem?.id ?? '')"></p>
                    </div>
                </div>

                <button type="button"
                        @click="closeEdit()"
                        class="w-9 h-9 rounded-lg hover:bg-neutral-100 dark:hover:bg-[#262626] flex items-center justify-center text-neutral-500 dark:text-neutral-400 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- MODAL BODY --}}
            <form :action="editingItem ? '/restaurant/menu-items/' + editingItem.id : ''"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Item Name <span class="text-orange-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               x-model="editForm.name"
                               required
                               class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                            Price (₱) <span class="text-orange-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-neutral-500 text-sm font-bold">₱</span>
                            <input type="number"
                                   name="price"
                                   x-model="editForm.price"
                                   step="0.01"
                                   min="0"
                                   required
                                   class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl pl-8 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Description <span class="text-neutral-400 dark:text-neutral-500 font-normal">(optional)</span>
                    </label>
                    <textarea name="description"
                              x-model="editForm.description"
                              rows="3"
                              class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"></textarea>
                </div>

                {{-- IMAGE UPLOAD --}}
                <div>
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-neutral-300 mb-1.5">
                        Food Photo
                        <span class="text-neutral-400 dark:text-neutral-500 font-normal">
                            (optional · iwan lang kung ayaw palitan)
                        </span>
                    </label>

                    <label class="block cursor-pointer">
                        <input type="file"
                               name="image"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               @change="onEditFileChange($event)"
                               class="hidden">
                        <div class="border-2 border-dashed border-neutral-300 dark:border-[#262626] rounded-xl p-6 text-center hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition">
                            <template x-if="!editImagePreview && !editingItem?.image_url">
                                <div>
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                        <span class="text-orange-600 dark:text-orange-400 font-bold">Click to upload</span> photo
                                    </p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">JPG, PNG, WebP · Max 2MB</p>
                                </div>
                            </template>

                            <template x-if="editImagePreview">
                                <div class="flex flex-col items-center">
                                    <img :src="editImagePreview" class="w-32 h-32 rounded-xl object-cover shadow-md">
                                    <p class="text-xs text-orange-600 dark:text-orange-400 font-semibold mt-2">New photo — click to change</p>
                                </div>
                            </template>

                            <template x-if="!editImagePreview && editingItem?.image_url">
                                <div class="flex flex-col items-center">
                                    <img :src="editingItem.image_url" class="w-32 h-32 rounded-xl object-cover shadow-md">
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-semibold mt-2">Current photo — click to replace</p>
                                </div>
                            </template>
                        </div>
                    </label>
                </div>

                {{-- ACTIONS --}}
                <div class="flex gap-2 pt-2">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Changes
                    </button>
                    <button type="button"
                            @click="closeEdit()"
                            class="px-6 py-2.5 border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 rounded-xl font-semibold text-sm hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- DELETE CONFIRMATION MODAL --}}
    {{-- ============================================ --}}
    <div x-show="deletingItem"
         x-cloak
         x-transition.opacity
         @keydown.escape.window="closeDelete()"
         @click.self="closeDelete()"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">

        <div x-show="deletingItem"
             x-transition.scale.origin.center
             class="bg-white dark:bg-[#141414] rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-neutral-200 dark:border-[#262626]">

            {{-- MODAL BODY --}}
            <div class="p-6 text-center">

                {{-- DANGER ICON --}}
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-red-600 shadow-lg shadow-red-500/30 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>

                {{-- TITLE --}}
                <h3 class="text-xl font-bold text-neutral-900 dark:text-white mb-2">
                    Delete Menu Item?
                </h3>

                {{-- ITEM NAME --}}
                <div class="mb-4">
                    <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-2">
                        You are about to delete:
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-neutral-100 dark:bg-[#262626] rounded-xl border border-neutral-200 dark:border-[#262626]">
                        <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span class="font-bold text-neutral-900 dark:text-white text-sm"
                              x-text="deletingItem?.name"></span>
                    </div>
                </div>

                {{-- WARNING --}}
                <div class="p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl mb-5">
                    <p class="text-xs text-red-700 dark:text-red-300 flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>This action cannot be undone. The item will be permanently removed from your menu.</span>
                    </p>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex gap-2 p-4 bg-neutral-50 dark:bg-[#0a0a0a] border-t border-neutral-200 dark:border-[#262626]">
                <button type="button"
                        @click="closeDelete()"
                        class="flex-1 border border-neutral-300 dark:border-[#262626] text-neutral-700 dark:text-neutral-300 px-4 py-2.5 rounded-xl font-semibold text-sm hover:bg-neutral-100 dark:hover:bg-[#262626] transition">
                    Cancel
                </button>

                <form :action="deletingItem?.action"
                      method="POST"
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-red-600 hover:to-red-700 hover:shadow-lg active:scale-98 transition transform flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
    .active\:scale-95:active { transform: scale(0.95); }
</style>
<script>
function menuManager() {
    return {
        showAddForm: {{ $errors->any() ? 'true' : 'false' }},
        imagePreview: '',
        viewMode: 'grid',
        filter: 'all',
        viewKey: 'fooddash_menu_view',

        // EDIT STATE
        editingItem: null,
        editForm: {
            name: '',
            description: '',
            price: '',
        },
        editImagePreview: '',

        // DELETE STATE
        deletingItem: null,

        init() {
            const saved = localStorage.getItem(this.viewKey);
            if (saved && ['grid', 'list'].includes(saved)) {
                this.viewMode = saved;
            }

            this.$watch('viewMode', (v) => localStorage.setItem(this.viewKey, v));
        },

        // ========================================
        // ADD FORM
        // ========================================
        onFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Max 5MB.');
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
                this.imagePreview = ev.target.result;
            };
            reader.readAsDataURL(file);
        },

        resetForm() {
            this.imagePreview = '';
        },

        // ========================================
        // EDIT FORM
        // ========================================
        openEdit(item) {
            this.editingItem = item;
            this.editForm = {
                name: item.name,
                description: item.description || '',
                price: item.price,
            };
            this.editImagePreview = '';

            // Prevent body scroll while modal is open
            document.body.style.overflow = 'hidden';
        },

        closeEdit() {
            this.editingItem = null;
            this.editForm = { name: '', description: '', price: '' };
            this.editImagePreview = '';
            document.body.style.overflow = '';
        },

        onEditFileChange(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Max 5MB.');
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
                this.editImagePreview = ev.target.result;
            };
            reader.readAsDataURL(file);
        },

        // ========================================
        // DELETE CONFIRMATION
        // ========================================
        openDelete(item) {
            this.deletingItem = item;
            document.body.style.overflow = 'hidden';
        },

        closeDelete() {
            this.deletingItem = null;
            document.body.style.overflow = '';
        },
    }
}
</script>
@endpush