@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-6">
        <div>
            <a href="{{ route('admin.orders') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white font-medium transition mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100">
                Order #{{ $order->id }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                {{ $order->created_at->format('F d, Y · g:i A') }}
                · {{ $order->created_at->diffForHumans() }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @php
                $statusStyles = [
                    'delivered' => 'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800',
                    'cancelled' => 'bg-gray-100 dark:bg-dark-850 text-gray-700 dark:text-neutral-300 border-gray-200 dark:border-dark-600',
                    'rejected' => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800',
                    'no_rider' => 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800',
                ];
                $statusClass = $statusStyles[$order->status] ?? 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800';

                $statusIconPath = match ($order->status) {
                    'delivered' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'rejected' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    'no_rider' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636',
                    'received' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    'confirmed' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'preparing' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'ready_for_pickup' => 'M5 13l4 4L19 7',
                    'rider_assigned' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1',
                    'picked_up' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'out_for_delivery' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    default => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                };
            @endphp

            <span class="inline-flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-lg border font-semibold uppercase tracking-wide {{ $statusClass }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statusIconPath }}" />
                </svg>
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>

            @if ($order->is_external_order)
                <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 font-bold uppercase tracking-wide">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    External
                </span>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SUMMARY STATS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        {{-- FOOD COST --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wide">Food Cost</p>
                <div class="w-7 h-7 rounded-md bg-gray-100 dark:bg-dark-850 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-gray-600 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">₱{{ number_format($order->food_cost, 2) }}</p>
        </div>

        {{-- DELIVERY FEE --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wide">Delivery Fee</p>
                <div class="w-7 h-7 rounded-md bg-cyan-50 dark:bg-cyan-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">₱{{ number_format($order->delivery_fee, 2) }}</p>
        </div>

        {{-- COMMISSION --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border-2 border-orange-300 dark:border-orange-800 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wide">Commission</p>
                <div class="w-7 h-7 rounded-md bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">₱{{ number_format($order->commission_amount, 2) }}</p>
            <p class="text-[10px] text-orange-600 dark:text-orange-400 mt-0.5 font-medium">
                {{ $order->commission_rate }}% rate
            </p>
        </div>

        {{-- TOTAL --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wide">Total</p>
                <div class="w-7 h-7 rounded-md bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">₱{{ number_format($order->total_amount, 2) }}</p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- CUSTOMER + RESTAURANT + RIDER --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- CUSTOMER --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-5 transition-colors">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Customer</h2>
            </div>

            @if ($order->customer)
                <p class="font-semibold text-gray-900 dark:text-neutral-100">{{ $order->customer->name }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $order->customer->email }}</p>
                @if ($order->customer->phone)
                    <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $order->customer->phone }}</p>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                    <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-1.5">Delivery Address</p>
                    <p class="text-sm text-gray-700 dark:text-neutral-300 leading-relaxed">{{ $order->delivery_address }}</p>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Customer deleted</p>
            @endif
        </div>

        {{-- RESTAURANT --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-5 transition-colors">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Restaurant</h2>
            </div>

            @if ($order->restaurant)
                <p class="font-semibold text-gray-900 dark:text-neutral-100">{{ $order->restaurant->name }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $order->restaurant->address }}</p>

                @if ($order->restaurant->user)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-1">Owner</p>
                        <p class="text-sm text-gray-700 dark:text-neutral-300">{{ $order->restaurant->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $order->restaurant->user->email }}</p>
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-neutral-400">Restaurant earnings</span>
                        <span class="font-bold text-gray-900 dark:text-neutral-100">
                            ₱{{ number_format($order->restaurant_earnings ?? $order->food_cost, 2) }}
                        </span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Restaurant deleted</p>
            @endif
        </div>

        {{-- RIDER --}}
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-5 transition-colors">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-950/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Rider</h2>
            </div>

            @if ($order->rider && $order->rider->user)
                <p class="font-semibold text-gray-900 dark:text-neutral-100">{{ $order->rider->user->name }}</p>
                <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{{ $order->rider->user->email }}</p>

                @if ($order->rider->vehicle_type)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 uppercase tracking-wide font-medium mb-1">Vehicle</p>
                        <p class="text-sm text-gray-700 dark:text-neutral-300">
                            {{ $order->rider->vehicle_type }}
                            @if ($order->rider->vehicle_plate)
                                · <span class="font-mono">{{ $order->rider->vehicle_plate }}</span>
                            @endif
                        </p>
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-dark-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-neutral-400">Delivery fee earned</span>
                        <span class="font-bold text-gray-900 dark:text-neutral-100">
                            ₱{{ number_format($order->delivery_fee, 2) }}
                        </span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Unassigned</p>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ORDER ITEMS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                Order Items ({{ $order->items->count() }})
            </h2>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($order->items as $item)
                <div class="px-5 py-3 flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-neutral-100">
                            <span class="text-orange-600 dark:text-orange-400">{{ $item->quantity }}×</span>
                            {{ $item->name }}
                        </p>
                        @if ($item->menu_item_id)
                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">
                                Menu ID: #{{ $item->menu_item_id }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-500 dark:text-neutral-400">₱{{ number_format($item->price, 2) }} each</p>
                        <p class="font-bold text-gray-900 dark:text-neutral-100">
                            ₱{{ number_format($item->price * $item->quantity, 2) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-5 py-4 bg-gray-50 dark:bg-dark-850 border-t border-gray-200 dark:border-dark-700 space-y-2 text-sm">
            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                <span>Subtotal (food)</span>
                <span class="font-medium text-gray-900 dark:text-neutral-100">₱{{ number_format($order->food_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-gray-600 dark:text-neutral-400">
                <span>Delivery fee</span>
                <span class="font-medium text-gray-900 dark:text-neutral-100">₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900 dark:text-neutral-100 pt-3 border-t border-gray-200 dark:border-dark-600 text-base">
                <span>Total</span>
                <span class="text-orange-600 dark:text-orange-400">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TIMELINE --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Timeline</h2>
        </div>

        <div class="p-5 space-y-4">
            @php
                $timeline = [
                    ['label' => 'Order placed',                     'at' => $order->created_at,                       'icon_path' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'blue'],
                    ['label' => 'Restaurant started preparing',      'at' => $order->restaurant_started_preparing_at,  'icon_path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'amber'],
                    ['label' => 'Marked ready for pickup',           'at' => $order->restaurant_marked_ready_at,       'icon_path' => 'M5 13l4 4L19 7', 'color' => 'green'],
                    ['label' => 'Picked up by rider',                'at' => $order->verified_pickup_at,               'icon_path' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'indigo'],
                    ['label' => 'Cancelled',                          'at' => $order->cancelled_at,                     'icon_path' => 'M6 18L18 6M6 6l12 12', 'color' => 'gray'],
                    ['label' => 'Last updated',                       'at' => $order->updated_at,                       'icon_path' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'gray'],
                ];

                $colorClasses = [
                    'blue' => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400',
                    'amber' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400',
                    'green' => 'bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400',
                    'indigo' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400',
                    'gray' => 'bg-gray-100 dark:bg-dark-850 text-gray-600 dark:text-neutral-400',
                    'orange' => 'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400',
                ];
            @endphp

            @foreach ($timeline as $event)
                @if ($event['at'])
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $colorClasses[$event['color']] }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $event['icon_path'] }}" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-neutral-100">{{ $event['label'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                {{ $event['at']->format('M d, Y · g:i A') }}
                                · {{ $event['at']->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- REJECTION REASON --}}
    {{-- ============================================ --}}
    @if ($order->rejection_reason)
        <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg p-5 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-red-800 dark:text-red-300 uppercase tracking-wide mb-1">Order Rejected</p>
                    <p class="text-sm text-red-700 dark:text-red-400">{{ $order->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CANCELLATION REASON --}}
    {{-- ============================================ --}}
    @if ($order->cancellation_reason)
        <div class="bg-gray-50 dark:bg-dark-850 border border-gray-300 dark:border-dark-600 rounded-lg p-5 mb-6">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-800 dark:bg-gray-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white dark:text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-300 uppercase tracking-wide mb-1">Order Cancelled</p>
                    <p class="text-sm text-gray-700 dark:text-gray-400">{{ $order->cancellation_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- PAYMENT --}}
    {{-- ============================================ --}}
    @if ($order->payment)
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden mb-6">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Payment Record</h2>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-neutral-400">Rider paid restaurant</span>
                    <span class="font-bold text-gray-900 dark:text-neutral-100">
                        ₱{{ number_format($order->payment->rider_paid_restaurant, 2) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-neutral-400">Rider collected from customer</span>
                    <span class="font-bold text-gray-900 dark:text-neutral-100">
                        ₱{{ number_format($order->payment->rider_collected_customer, 2) }}
                    </span>
                </div>
                <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-dark-600">
                    <span class="font-bold text-orange-600 dark:text-orange-400">Rider earnings</span>
                    <span class="font-bold text-orange-600 dark:text-orange-400">
                        ₱{{ number_format($order->delivery_fee, 2) }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 dark:text-neutral-500 pt-2">
                    Recorded at {{ $order->payment->recorded_at->format('M d, Y · g:i A') }}
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- RATINGS --}}
    {{-- ============================================ --}}
    @if ($order->restaurant_rating || $order->rider_rating)
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500 dark:text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-neutral-100">Customer Ratings</h2>
            </div>
            <div class="p-5 space-y-3">
                @if ($order->restaurant_rating)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-neutral-300">Restaurant</span>
                        <div class="flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $order->restaurant_rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300 dark:text-neutral-600' }}"
                                     viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                @endif
                @if ($order->rider_rating)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700 dark:text-neutral-300">Rider</span>
                        <div class="flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $order->rider_rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300 dark:text-neutral-600' }}"
                                     viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection