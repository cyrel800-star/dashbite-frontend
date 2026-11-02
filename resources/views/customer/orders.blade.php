@extends('layouts.app')

@section('content')
<div x-data="{ tab: 'ongoing' }" class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 rounded-2xl shadow-xl text-white">

        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full blur-3xl opacity-10 -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-yellow-300 rounded-full blur-3xl opacity-20 -ml-16 -mb-16"></div>

        <div class="relative p-6">
            <div class="flex justify-between items-start mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border-2 border-white/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-white/70 uppercase tracking-wider font-medium">Activity</p>
                        <h1 class="text-xl font-bold leading-tight">My Orders</h1>
                    </div>
                </div>

                <a href="{{ route('customer.restaurants') }}"
                   class="bg-white/20 hover:bg-white/30 backdrop-blur rounded-full px-4 py-2 text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    New
                </a>
            </div>

            <p class="text-sm text-white/90 max-w-md">
                Track all your orders in one place
            </p>

            @if ($orders->count() > 0)
                <div class="grid grid-cols-3 gap-3 pt-5 mt-5 border-t border-white/20">
                    <div>
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Total</p>
                        <p class="text-xl font-bold mt-1">{{ $orders->count() }}</p>
                    </div>
                    <div class="border-l border-white/20 pl-3">
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Delivered</p>
                        <p class="text-xl font-bold mt-1">{{ $orders->where('status', 'delivered')->count() }}</p>
                    </div>
                    <div class="border-l border-white/20 pl-3">
                        <p class="text-[10px] text-white/70 uppercase tracking-wider font-medium">Active</p>
                        <p class="text-xl font-bold mt-1">
                            {{ $orders->whereNotIn('status', ['delivered', 'cancelled', 'rejected', 'no_rider'])->count() }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @php
        $ongoingStatuses = ['received', 'confirmed', 'preparing', 'finding_rider', 'rider_assigned', 'picked_up', 'out_for_delivery'];
        $failedStatuses  = ['rejected', 'cancelled', 'no_rider'];

        $ongoingOrders   = $orders->whereIn('status', $ongoingStatuses);
        $deliveredOrders = $orders->where('status', 'delivered');
        $failedOrders    = $orders->whereIn('status', $failedStatuses);

        // ⭐ SIMPLIFIED — orange lang ang active, black/gray ang neutral
        $statusStyles = [
            'received'        => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'confirmed'       => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'preparing'       => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'finding_rider'   => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'rider_assigned'  => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'picked_up'       => ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'out_for_delivery'=> ['bg' => 'bg-orange-50 dark:bg-orange-950/30', 'border' => 'border-orange-200 dark:border-orange-800', 'text' => 'text-orange-700 dark:text-orange-300'],
            'delivered'       => ['bg' => 'bg-gray-50 dark:bg-dark-850', 'border' => 'border-gray-200 dark:border-dark-600', 'text' => 'text-gray-700 dark:text-neutral-300'],
            'rejected'        => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-700 dark:text-red-300'],
            'cancelled'       => ['bg' => 'bg-gray-50 dark:bg-dark-850', 'border' => 'border-gray-200 dark:border-dark-600', 'text' => 'text-gray-700 dark:text-neutral-300'],
            'no_rider'        => ['bg' => 'bg-red-50 dark:bg-red-950/30', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-700 dark:text-red-300'],
        ];

        $labels = [
            'received' => 'Pending',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'finding_rider' => 'Finding Rider',
            'rider_assigned' => 'Rider Assigned',
            'picked_up' => 'Picked Up',
            'out_for_delivery' => 'On the Way',
            'delivered' => 'Delivered',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            'no_rider' => 'No Rider',
        ];
    @endphp

    {{-- ============================================ --}}
    {{-- TAB NAVIGATION --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden">
        <div class="flex">
            <button type="button" @click="tab = 'ongoing'"
                    :class="tab === 'ongoing'
                        ? 'border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-950/20 font-bold'
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 font-semibold'"
                    class="flex-1 py-4 px-3 text-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="hidden sm:inline">Ongoing</span>
                @if ($ongoingOrders->count() > 0)
                    <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $ongoingOrders->count() }}
                    </span>
                @endif
            </button>

            <button type="button" @click="tab = 'delivered'"
                    :class="tab === 'delivered'
                        ? 'border-b-2 border-gray-900 dark:border-gray-400 text-gray-900 dark:text-neutral-100 bg-gray-100 dark:bg-dark-850 font-bold'
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 font-semibold'"
                    class="flex-1 py-4 px-3 text-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="hidden sm:inline">Delivered</span>
                @if ($deliveredOrders->count() > 0)
                    <span class="bg-gray-200 dark:bg-dark-700 text-gray-700 dark:text-neutral-300 text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $deliveredOrders->count() }}
                    </span>
                @endif
            </button>

            <button type="button" @click="tab = 'failed'"
                    :class="tab === 'failed'
                        ? 'border-b-2 border-red-500 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/20 font-bold'
                        : 'text-gray-500 dark:text-neutral-400 hover:text-gray-700 font-semibold'"
                    class="flex-1 py-4 px-3 text-sm transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="hidden sm:inline">Failed</span>
                @if ($failedOrders->count() > 0)
                    <span class="bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-300 text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $failedOrders->count() }}
                    </span>
                @endif
            </button>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ONGOING TAB --}}
    {{-- ============================================ --}}
    <div x-show="tab === 'ongoing'" x-cloak x-transition.opacity>
        @if ($ongoingOrders->isEmpty())
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-950/40 mb-4">
                    <svg class="w-10 h-10 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No ongoing orders</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                    Place an order and track its progress here
                </p>
                <a href="{{ route('customer.restaurants') }}"
                   class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md">
                    Browse Restaurants
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($ongoingOrders as $order)
                    @php
                        $sc = $statusStyles[$order->status] ?? $statusStyles['received'];
                        $label = $labels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                    @endphp

                    <a href="{{ route('customer.orders.show', $order) }}"
                       class="block bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg transition-all group">

                        <div class="px-5 py-3 {{ $sc['bg'] }} {{ $sc['border'] }} border-b flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white/60 dark:bg-dark-800/60 flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $sc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-neutral-100 text-sm">{{ $order->restaurant->name }}</p>
                                    <p class="text-xs {{ $sc['text'] }} font-semibold uppercase tracking-wide">{{ $label }}</p>
                                </div>
                            </div>
                            <span class="flex items-center gap-1.5 bg-white/70 dark:bg-dark-800/70 backdrop-blur rounded-full px-2.5 py-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider">Active</span>
                            </span>
                        </div>

                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 px-2 py-1 rounded-full font-semibold">
                                        #{{ $order->id }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-neutral-400 mb-2">
                                    <span class="font-bold text-gray-900 dark:text-neutral-100">{{ $order->items->count() }}</span>
                                    {{ Str::plural('item', $order->items->count()) }}
                                    @if ($order->items->count() > 0)
                                        · <span class="text-gray-500 dark:text-neutral-400">{{ $order->items->first()->name }}</span>
                                        @if ($order->items->count() > 1)
                                            <span class="text-gray-400 dark:text-neutral-500">+{{ $order->items->count() - 1 }} more</span>
                                        @endif
                                    @endif
                                </p>

                                <div class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-neutral-400">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.65713.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-1">{{ $order->delivery_address }}</span>
                                </div>

                                @if ($order->rider)
                                    <div class="flex items-center gap-2 mt-2 text-xs">
                                        <div class="w-5 h-5 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3 h-3 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-orange-700 dark:text-orange-400 font-medium">Rider: {{ $order->rider->user->name }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mb-0.5">Total</p>
                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="text-xs text-gray-500 dark:text-neutral-400 group-hover:text-orange-600 inline-flex items-center gap-1 mt-2">
                                    View
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- DELIVERED TAB --}}
    {{-- ============================================ --}}
    <div x-show="tab === 'delivered'" x-cloak x-transition.opacity>
        @if ($deliveredOrders->isEmpty())
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-dark-850 mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No delivered orders yet</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    Your completed orders will appear here
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($deliveredOrders as $order)
                    <a href="{{ route('customer.orders.show', $order) }}"
                       class="block bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg transition-all group">

                        <div class="px-5 py-3 bg-gray-50 dark:bg-dark-850 border-b border-gray-200 dark:border-dark-600 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white dark:bg-dark-800 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-neutral-100 text-sm">{{ $order->restaurant->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 font-semibold uppercase tracking-wide">Delivered</p>
                                </div>
                            </div>
                            @if (!$order->hasBeenReviewed())
                                <span class="bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Rate
                                </span>
                            @endif
                        </div>

                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 px-2 py-1 rounded-full font-semibold">
                                        #{{ $order->id }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-neutral-400 mb-2">
                                    <span class="font-bold text-gray-900 dark:text-neutral-100">{{ $order->items->count() }}</span>
                                    {{ Str::plural('item', $order->items->count()) }}
                                    @if ($order->items->count() > 0)
                                        · <span class="text-gray-500 dark:text-neutral-400">{{ $order->items->first()->name }}</span>
                                        @if ($order->items->count() > 1)
                                            <span class="text-gray-400 dark:text-neutral-500">+{{ $order->items->count() - 1 }} more</span>
                                        @endif
                                    @endif
                                </p>

                                <div class="flex items-start gap-1.5 text-xs text-gray-500 dark:text-neutral-400">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.65713.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="line-clamp-1">{{ $order->delivery_address }}</span>
                                </div>

                                @if ($order->restaurant_rating)
                                    <div class="flex items-center gap-1 mt-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $order->restaurant_rating ? 'text-orange-500 fill-orange-500' : 'text-gray-300 dark:text-neutral-600' }}" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                @endif
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mb-0.5">Total</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-neutral-100">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="text-xs text-gray-500 dark:text-neutral-400 group-hover:text-orange-600 inline-flex items-center gap-1 mt-2">
                                    View
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- FAILED TAB --}}
    {{-- ============================================ --}}
    <div x-show="tab === 'failed'" x-cloak x-transition.opacity>
        @if ($failedOrders->isEmpty())
            <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-dark-850 mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No failed orders</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    All your orders have gone smoothly!
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($failedOrders as $order)
                    @php
                        $sc = $statusStyles[$order->status] ?? $statusStyles['cancelled'];
                        $label = $labels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));

                        $failedIcon = match ($order->status) {
                            'rejected' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                            'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            'no_rider' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                            default => 'M6 18L18 6M6 6l12 12',
                        };
                    @endphp

                    <a href="{{ route('customer.orders.show', $order) }}"
                       class="block bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden hover:shadow-lg transition-all group">

                        <div class="px-5 py-3 {{ $sc['bg'] }} {{ $sc['border'] }} border-b flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-white/60 dark:bg-dark-800/60 flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ $sc['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $failedIcon }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-neutral-100 text-sm">{{ $order->restaurant->name }}</p>
                                    <p class="text-xs {{ $sc['text'] }} font-semibold uppercase tracking-wide">{{ $label }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-gray-200 dark:bg-dark-700 text-gray-600 dark:text-neutral-400">
                                {{ $order->created_at->format('M d') }}
                            </span>
                        </div>

                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-xs bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 px-2 py-1 rounded-full font-semibold">
                                        #{{ $order->id }}
                                    </span>
                                    <span class="text-xs text-gray-400 dark:text-neutral-500">{{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-neutral-400 mb-2">
                                    <span class="font-bold text-gray-900 dark:text-neutral-100">{{ $order->items->count() }}</span>
                                    {{ Str::plural('item', $order->items->count()) }}
                                </p>

                                @if ($order->rejection_reason)
                                    <div class="flex items-start gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span><strong>Reason:</strong> {{ $order->rejection_reason }}</span>
                                    </div>
                                @elseif ($order->cancellation_reason)
                                    <div class="flex items-start gap-1.5 text-xs text-gray-600 dark:text-neutral-400">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span><strong>Reason:</strong> {{ $order->cancellation_reason }}</span>
                                    </div>
                                @elseif ($order->status === 'no_rider')
                                    <div class="flex items-start gap-1.5 text-xs text-red-600 dark:text-red-400">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span><strong>No rider was available at the time</strong></span>
                                    </div>
                                @endif
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500 dark:text-neutral-400 mb-0.5">Total</p>
                                <p class="text-lg font-bold text-gray-400 dark:text-neutral-500 line-through">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="text-xs text-gray-500 dark:text-neutral-400 group-hover:text-orange-600 inline-flex items-center gap-1 mt-2">
                                    View
                                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection