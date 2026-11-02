@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] shadow-xl">

        {{-- COVER PHOTO --}}
        <div class="relative h-32 overflow-hidden">
            @if ($restaurant->cover_image_url ?? false)
                <img src="{{ $restaurant->cover_image_url }}"
                     alt="{{ $restaurant->name ?? 'Restaurant' }}"
                     class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-black/20"></div>
            @else
                {{-- Orange → Black gradient --}}
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-neutral-900"></div>
                <div class="absolute inset-0 opacity-10"
                     style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 80%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

            <a href="{{ route('restaurant.dashboard') }}"
               class="absolute top-4 left-4 w-10 h-10 rounded-xl bg-white/95 dark:bg-[#0a0a0a]/95 backdrop-blur flex items-center justify-center shadow-lg hover:bg-white dark:hover:bg-[#0a0a0a] hover:scale-105 transition-all z-10 border border-white/20 dark:border-[#262626]">
                <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>

            <div class="absolute top-4 right-4 bg-gradient-to-r from-orange-500 to-orange-600 backdrop-blur rounded-full px-3.5 py-1.5 shadow-lg">
                <span class="text-xs font-bold uppercase tracking-wide text-white">
                    {{ $orders->total() }} Orders
                </span>
            </div>
        </div>

        <div class="relative px-6 -mt-10">
            <div class="flex items-end gap-4 mb-5">
                {{-- PROFILE IMAGE --}}
                <div class="w-20 h-20 rounded-2xl bg-white dark:bg-[#141414] border-4 border-white dark:border-[#141414] shadow-lg flex items-center justify-center flex-shrink-0 overflow-hidden ring-2 ring-orange-500/20">
                    @if ($restaurant->profile_image_url ?? false)
                        <img src="{{ $restaurant->profile_image_url }}"
                             alt="{{ $restaurant->name ?? 'Restaurant' }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="flex-1 pb-1 min-w-0">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Restaurant</p>
                    <h1 class="text-2xl font-bold text-neutral-900 dark:text-white leading-tight truncate">Order History</h1>
                </div>
            </div>

            {{-- SUMMARY STATS --}}
            <div class="grid grid-cols-3 gap-3 pb-6 border-t border-neutral-100 dark:border-[#262626] pt-5">
                <div>
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Total Sales</p>
                    <p class="text-xl font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent mt-1">₱{{ number_format($stats['total_sales'], 0) }}</p>
                </div>
                <div class="border-l border-neutral-100 dark:border-[#262626] pl-3">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Delivered</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-white mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="border-l border-neutral-100 dark:border-[#262626] pl-3">
                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wider font-medium">Rating</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($stats['avg_rating'] !== 'N/A')
                            <span class="text-xl font-bold text-neutral-900 dark:text-white">{{ $stats['avg_rating'] }}</span>
                            <svg class="w-4 h-4 fill-orange-500 text-orange-500" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <span class="text-sm font-medium text-neutral-400 dark:text-neutral-500">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================ --}}
    <form method="GET" class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-sm">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <p class="text-sm font-semibold text-neutral-900 dark:text-white">Filters</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            {{-- STATUS --}}
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">Status</label>
                <select name="status"
                        class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    <option value="">All Status</option>
                    <optgroup label="Completed">
                        <option value="delivered" @selected(request('status') === 'delivered')>Delivered</option>
                    </optgroup>
                    <optgroup label="Failed">
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        <option value="no_rider" @selected(request('status') === 'no_rider')>No Rider</option>
                    </optgroup>
                </select>
            </div>

            {{-- FROM --}}
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>

            {{-- TO --}}
            <div>
                <label class="block text-xs font-medium text-neutral-700 dark:text-neutral-300 mb-1.5">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full border border-neutral-300 dark:border-[#262626] dark:bg-[#0a0a0a] dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            </div>

            {{-- ACTIONS --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg active:scale-98 transition transform">
                    Apply
                </button>
                <a href="{{ route('restaurant.orders') }}"
                   class="px-4 py-2.5 text-sm font-semibold text-neutral-700 dark:text-neutral-300 border border-neutral-300 dark:border-[#262626] rounded-xl hover:bg-neutral-50 dark:hover:bg-[#0a0a0a] transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- ============================================ --}}
    {{-- ORDER LIST --}}
    {{-- ============================================ --}}
    @if ($orders->isEmpty())
        <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] p-12 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-950/40 dark:to-orange-900/20 mb-4">
                <svg class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="font-semibold text-neutral-900 dark:text-white mb-1">No orders found</h3>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Try adjusting your filters</p>
        </div>
    @else
        @php
            $deliveredOrders = $orders->where('status', 'delivered');
            $cancelledOrders = $orders->where('status', 'cancelled');
            $rejectedOrders  = $orders->where('status', 'rejected');
            $noRiderOrders   = $orders->where('status', 'no_rider');

            $sections = [
                [
                    'key'         => 'delivered',
                    'label'       => 'Completed Orders',
                    'subtitle'    => 'Successfully delivered to customers',
                    'icon_bg'     => 'bg-gradient-to-br from-orange-500 to-orange-600',
                    'icon_color'  => 'text-white',
                    'badge_bg'    => 'bg-gradient-to-r from-orange-500 to-orange-600',
                    'badge_text'  => 'text-white',
                    'icon_path'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'orders'      => $deliveredOrders,
                ],
                [
                    'key'         => 'cancelled',
                    'label'       => 'Cancelled Orders',
                    'subtitle'    => 'Cancelled by customer',
                    'icon_bg'     => 'bg-neutral-100 dark:bg-[#262626]',
                    'icon_color'  => 'text-neutral-600 dark:text-neutral-300',
                    'badge_bg'    => 'bg-neutral-100 dark:bg-[#262626]',
                    'badge_text'  => 'text-neutral-700 dark:text-neutral-300',
                    'icon_path'   => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'orders'      => $cancelledOrders,
                ],
                [
                    'key'         => 'rejected',
                    'label'       => 'Rejected Orders',
                    'subtitle'    => 'Rejected by your restaurant',
                    'icon_bg'     => 'bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-700 dark:to-neutral-800',
                    'icon_color'  => 'text-orange-500',
                    'badge_bg'    => 'bg-black dark:bg-neutral-900',
                    'badge_text'  => 'text-orange-500',
                    'icon_path'   => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    'orders'      => $rejectedOrders,
                ],
                [
                    'key'         => 'no_rider',
                    'label'       => 'No Rider Available',
                    'subtitle'    => 'Orders where no rider accepted',
                    'icon_bg'     => 'bg-gradient-to-br from-neutral-800 to-neutral-900 dark:from-neutral-700 dark:to-neutral-800',
                    'icon_color'  => 'text-orange-500',
                    'badge_bg'    => 'bg-black dark:bg-neutral-900',
                    'badge_text'  => 'text-orange-500',
                    'icon_path'   => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                    'orders'      => $noRiderOrders,
                ],
            ];
        @endphp

        <div class="space-y-6">
            @foreach ($sections as $section)
                @if ($section['orders']->count() > 0)
                    {{-- SECTION --}}
                    <div x-data="{ expanded: true }" class="space-y-3">

                        {{-- SECTION HEADER --}}
                        <button type="button"
                                @click="expanded = !expanded"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] hover:shadow-lg hover:border-orange-300 dark:hover:border-orange-800/50 transition-all duration-200 group">

                            <div class="flex items-center gap-3 min-w-0">
                                {{-- SECTION ICON --}}
                                <div class="w-10 h-10 rounded-xl {{ $section['icon_bg'] }} flex items-center justify-center flex-shrink-0 shadow-md">
                                    <svg class="w-5 h-5 {{ $section['icon_color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $section['icon_path'] }}" />
                                    </svg>
                                </div>

                                {{-- SECTION LABEL --}}
                                <div class="text-left min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-neutral-900 dark:text-white text-sm">
                                            {{ $section['label'] }}
                                        </h3>
                                        <span class="{{ $section['badge_bg'] }} {{ $section['badge_text'] }} text-xs font-bold px-2.5 py-0.5 rounded-full shadow-sm">
                                            {{ $section['orders']->count() }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate">
                                        {{ $section['subtitle'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- EXPAND ICON --}}
                            <svg class="w-5 h-5 text-neutral-400 dark:text-neutral-500 transition-transform duration-200 flex-shrink-0 group-hover:text-orange-500"
                                 :class="expanded ? 'rotate-180' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- SECTION CONTENT --}}
                        <div x-show="expanded" x-cloak x-collapse class="space-y-3">
                            @foreach ($section['orders'] as $order)
                                @php
                                    $statusStyles = [
                                        'received'         => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                                        'confirmed'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                                        'preparing'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                                        'finding_rider'    => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                                        'rider_assigned'   => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                                        'picked_up'        => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                                        'out_for_delivery' => ['bg' => 'bg-gradient-to-r from-orange-500 to-orange-600', 'text' => 'text-white'],
                                        'delivered'        => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-white'],
                                        'rejected'         => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                                        'cancelled'        => ['bg' => 'bg-neutral-800 dark:bg-neutral-900', 'text' => 'text-neutral-300'],
                                        'no_rider'         => ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-orange-500'],
                                    ];
                                    $ss = $statusStyles[$order->status] ?? ['bg' => 'bg-black dark:bg-neutral-900', 'text' => 'text-white'];

                                    $statusIconPath = match ($order->status) {
                                        'received'         => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                                        'confirmed'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'preparing'        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                                        'finding_rider'    => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                                        'rider_assigned'   => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1',
                                        'picked_up'        => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                        'out_for_delivery' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                        'delivered'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'rejected'         => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                        'cancelled'        => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'no_rider'         => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                                        default            => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                    };
                                @endphp

                                <div class="bg-white dark:bg-[#141414] rounded-2xl border border-neutral-200 dark:border-[#262626] overflow-hidden hover:shadow-lg transition-all duration-200">

                                    {{-- HEADER --}}
                                    <div class="px-5 py-3 {{ $ss['bg'] }} border-b border-neutral-200 dark:border-[#262626] flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 {{ $ss['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statusIconPath }}" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold {{ $ss['text'] }} text-sm">Order #{{ $order->id }}</p>
                                                <p class="text-xs {{ $ss['text'] }} opacity-80 font-semibold uppercase tracking-wide">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="text-xs {{ $ss['text'] }} opacity-70">{{ $order->created_at->format('M d, Y · H:i') }}</p>
                                    </div>

                                    {{-- BODY --}}
                                    <div class="p-5">
                                        <div class="flex items-start justify-between gap-4">
                                            {{-- LEFT: INFO --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-4 mb-3">
                                                    <div>
                                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Items</p>
                                                        <p class="text-sm font-bold text-neutral-900 dark:text-white">
                                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                                        </p>
                                                    </div>
                                                    <div class="w-px h-8 bg-neutral-200 dark:bg-[#262626]"></div>
                                                    <div>
                                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">Total</p>
                                                        <p class="text-sm font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">
                                                            ₱{{ number_format($order->total_amount, 2) }}
                                                        </p>
                                                    </div>
                                                    @if ($order->is_external_order)
                                                        <div class="w-px h-8 bg-neutral-200 dark:bg-[#262626]"></div>
                                                        <span class="text-[10px] bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 px-2 py-1 rounded-full font-semibold uppercase tracking-wide">
                                                            External
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- CUSTOMER --}}
                                                @if ($order->customer)
                                                    <div class="flex items-center gap-2 text-xs text-neutral-600 dark:text-neutral-400 mb-1.5">
                                                        <svg class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        <span>{{ $order->customer->name }}</span>
                                                    </div>
                                                @endif

                                                {{-- DELIVERY ADDRESS --}}
                                                <div class="flex items-start gap-2 text-xs text-neutral-600 dark:text-neutral-400 mb-1.5">
                                                    <svg class="w-3.5 h-3.5 text-neutral-400 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="line-clamp-2">{{ $order->delivery_address }}</span>
                                                </div>

                                                {{-- RIDER --}}
                                                @if ($order->rider)
                                                    <div class="flex items-center gap-2 text-xs text-orange-600 dark:text-orange-400 font-medium mt-2">
                                                        <div class="w-5 h-5 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                        Rider: {{ $order->rider->user->name }}
                                                    </div>
                                                @endif

                                                {{-- RATINGS --}}
                                                @if ($order->restaurant_rating)
                                                    <div class="flex items-center gap-2 mt-2">
                                                        <p class="text-[10px] text-neutral-400 dark:text-neutral-500 uppercase tracking-wide">Rating:</p>
                                                        <div class="flex gap-0.5">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <svg class="w-3 h-3 {{ $i <= $order->restaurant_rating ? 'text-orange-500 fill-orange-500' : 'text-neutral-300 dark:text-neutral-600' }}"
                                                                     viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- RIGHT: ITEM PREVIEW --}}
                                            <div class="hidden sm:block flex-shrink-0">
                                                <div class="bg-neutral-50 dark:bg-[#0a0a0a] rounded-xl p-3 min-w-[140px] border border-neutral-100 dark:border-[#262626]">
                                                    <p class="text-[10px] text-neutral-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-2">Items</p>
                                                    <div class="space-y-1">
                                                        @foreach ($order->items->take(3) as $item)
                                                            <p class="text-xs text-neutral-700 dark:text-neutral-300 line-clamp-1">
                                                                <span class="font-semibold text-neutral-900 dark:text-white">{{ $item->quantity }}×</span>
                                                                {{ $item->name }}
                                                            </p>
                                                        @endforeach
                                                        @if ($order->items->count() > 3)
                                                            <p class="text-[10px] text-neutral-400 dark:text-neutral-500 italic">
                                                                + {{ $order->items->count() - 3 }} more
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<style>
    .active\:scale-98:active { transform: scale(0.98); }
</style>
@endpush