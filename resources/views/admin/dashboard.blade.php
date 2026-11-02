@extends('layouts.app')

{{--
    DASHBITE COLOR SYSTEM (used as-is)
    Primary   Orange     #FF6B35  -> key numbers, Approve buttons, Delivered, Commission
    Secondary Dark Gray  #2D2D2D  -> titles, table header, main button, cancelled/rejected
    Background White     #FFFFFF  -> cards and tables
    Only tints of these three colors are used. No green, blue, cyan, red or amber.
--}}

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div class="flex items-center gap-3">
            <span class="w-1.5 h-10 rounded-full bg-[#FF6B35]"></span>
            <div>
                <h1 class="text-2xl font-extrabold text-[#2D2D2D] dark:text-white">Admin Dashboard</h1>
                <p class="text-sm text-[#2D2D2D]/60 dark:text-white/60 mt-0.5">System overview and management</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders') }}"
               class="inline-flex items-center gap-2 bg-white dark:bg-[#2D2D2D] border border-[#2D2D2D]/15 dark:border-white/15 text-[#2D2D2D] dark:text-white px-4 py-2 rounded-full text-sm font-semibold hover:border-[#FF6B35] hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                All Orders
            </a>
            <a href="{{ route('admin.reports') }}"
               class="inline-flex items-center gap-2 bg-white dark:bg-[#2D2D2D] border border-[#2D2D2D]/15 dark:border-white/15 text-[#2D2D2D] dark:text-white px-4 py-2 rounded-full text-sm font-semibold hover:border-[#FF6B35] hover:text-[#FF6B35] dark:hover:text-[#FF6B35] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports
            </a>

            {{-- Main button = dark gray --}}
            <a href="{{ route('admin.accounts') }}"
               class="inline-flex items-center gap-2 bg-[#2D2D2D] dark:bg-[#FF6B35] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-black dark:hover:brightness-95 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF6B35]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Manage Accounts
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SUMMARY STATS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">

        {{-- TOTAL ORDERS --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-[#2D2D2D]/60 dark:text-white/60">Total Orders</p>
                <div class="w-8 h-8 rounded-lg bg-[#FF6B35]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-[#2D2D2D] dark:text-white">{{ number_format($stats['total_orders']) }}</p>
        </div>

        {{-- DELIVERED --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-[#2D2D2D]/60 dark:text-white/60">Delivered</p>
                <div class="w-8 h-8 rounded-lg bg-[#FF6B35]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-[#FF6B35]">{{ number_format($stats['delivered_orders']) }}</p>
        </div>

        {{-- TOTAL SALES --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-[#2D2D2D]/60 dark:text-white/60">Total Sales</p>
                <div class="w-8 h-8 rounded-lg bg-[#FF6B35]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-[#2D2D2D] dark:text-white">₱{{ number_format($stats['total_sales'], 0) }}</p>
        </div>

        {{-- PLATFORM COMMISSION (highlight card = solid orange) --}}
        <div class="bg-[#FF6B35] rounded-2xl border border-[#FF6B35] p-4 shadow-md shadow-[#FF6B35]/25 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold text-white">Commission</p>
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-white">₱{{ number_format($stats['total_commission'] ?? 0, 0) }}</p>
            <p class="text-[11px] text-white/85 mt-0.5 font-medium">Platform earnings</p>
        </div>

        {{-- ACTIVE RESTAURANTS --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-[#2D2D2D]/60 dark:text-white/60">Active Restos</p>
                <div class="w-8 h-8 rounded-lg bg-[#FF6B35]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-[#2D2D2D] dark:text-white">{{ number_format($stats['active_restaurants']) }}</p>
        </div>

        {{-- ONLINE RIDERS --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 p-4 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-semibold text-[#2D2D2D]/60 dark:text-white/60">Online Riders</p>
                <div class="w-8 h-8 rounded-lg bg-[#FF6B35]/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-[#2D2D2D] dark:text-white">{{ number_format($stats['online_riders']) }}</p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PENDING APPROVALS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        {{-- PENDING RESTAURANTS --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 overflow-hidden transition-colors">
            <div class="px-5 py-3.5 bg-[#2D2D2D] border-b-2 border-[#FF6B35] flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-sm font-bold text-white">Pending Restaurants</h2>
                </div>
                @if ($pendingRestaurants->count() > 0)
                    <span class="text-xs bg-[#FF6B35] text-white px-2.5 py-0.5 rounded-full font-bold">
                        {{ $pendingRestaurants->count() }}
                    </span>
                @endif
            </div>

            <div class="divide-y divide-[#2D2D2D]/10 dark:divide-white/10">
                @forelse ($pendingRestaurants as $user)
                    <div class="px-5 py-3 flex justify-between items-center hover:bg-[#FF6B35]/5 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#2D2D2D] dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-[#2D2D2D]/60 dark:text-white/60 truncate">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-bold text-white bg-[#FF6B35] hover:brightness-95 px-3.5 py-1.5 rounded-full shadow-sm active:scale-95 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2D2D2D]">
                                Approve
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-[#2D2D2D]/50 dark:text-white/50">No pending restaurants</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- PENDING RIDERS --}}
        <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 overflow-hidden transition-colors">
            <div class="px-5 py-3.5 bg-[#2D2D2D] border-b-2 border-[#FF6B35] flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                    <h2 class="text-sm font-bold text-white">Pending Riders</h2>
                </div>
                @if ($pendingRiders->count() > 0)
                    <span class="text-xs bg-[#FF6B35] text-white px-2.5 py-0.5 rounded-full font-bold">
                        {{ $pendingRiders->count() }}
                    </span>
                @endif
            </div>

            <div class="divide-y divide-[#2D2D2D]/10 dark:divide-white/10">
                @forelse ($pendingRiders as $user)
                    <div class="px-5 py-3 flex justify-between items-center hover:bg-[#FF6B35]/5 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#2D2D2D] dark:text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-[#2D2D2D]/60 dark:text-white/60 truncate">{{ $user->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-bold text-white bg-[#FF6B35] hover:brightness-95 px-3.5 py-1.5 rounded-full shadow-sm active:scale-95 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2D2D2D]">
                                Approve
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <p class="text-sm text-[#2D2D2D]/50 dark:text-white/50">No pending riders</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- RECENT ORDERS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#2D2D2D] rounded-2xl border border-[#2D2D2D]/10 dark:border-white/10 overflow-hidden transition-colors">

        <div class="px-5 py-3.5 bg-[#2D2D2D] border-b-2 border-[#FF6B35] flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-sm font-bold text-white">Recent Orders</h2>
            </div>
            <a href="{{ route('admin.orders') }}"
               class="text-xs text-white hover:text-[#FF6B35] font-semibold inline-flex items-center gap-1 transition">
                View All
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="px-5 py-12 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-[#FF6B35]/10 mb-2">
                    <svg class="w-7 h-7 text-[#FF6B35]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-sm text-[#2D2D2D]/50 dark:text-white/50">No orders yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#2D2D2D]/5 dark:bg-white/5 border-b border-[#2D2D2D]/10 dark:border-white/10">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">Restaurant</th>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">Customer</th>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">Total</th>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">Status</th>
                            <th class="text-left px-5 py-3 font-semibold text-[#2D2D2D]/70 dark:text-white/70 text-xs">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2D2D2D]/10 dark:divide-white/10">
                        @foreach ($recentOrders as $order)
                            @php
                                // delivered = solid orange | cancelled/rejected/no_rider = dark gray | in progress = orange outline
                                $statusStyles = [
                                    'delivered' => 'bg-[#FF6B35] text-white border-[#FF6B35]',
                                    'cancelled' => 'bg-[#2D2D2D]/10 text-[#2D2D2D] border-[#2D2D2D]/20 dark:bg-white/10 dark:text-white dark:border-white/20',
                                    'rejected' => 'bg-[#2D2D2D] text-white border-[#2D2D2D] dark:bg-white dark:text-[#2D2D2D] dark:border-white',
                                    'no_rider' => 'bg-[#2D2D2D] text-white border-[#2D2D2D] dark:bg-white dark:text-[#2D2D2D] dark:border-white',
                                ];
                                $statusClass = $statusStyles[$order->status] ?? 'bg-white text-[#2D2D2D] border-[#FF6B35] dark:bg-transparent dark:text-white';
                            @endphp
                            <tr class="hover:bg-[#FF6B35]/5 transition">
                                <td class="px-5 py-3 font-bold text-[#2D2D2D] dark:text-white">#{{ $order->id }}</td>
                                <td class="px-5 py-3 text-[#2D2D2D]/80 dark:text-white/80">{{ $order->restaurant->name }}</td>
                                <td class="px-5 py-3 text-[#2D2D2D]/80 dark:text-white/80">{{ $order->customer->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3 font-bold text-[#2D2D2D] dark:text-white">₱{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-block text-xs px-2.5 py-1 rounded-full border font-bold {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-xs text-[#2D2D2D]/60 dark:text-white/60">{{ $order->created_at->format('M d, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@endsection