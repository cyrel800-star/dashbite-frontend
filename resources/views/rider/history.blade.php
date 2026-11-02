@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- ============================================ --}}
    {{-- HERO HEADER --}}
    {{-- ============================================ --}}
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 mb-6 text-white shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-white/80">Rider</p>
                    <h1 class="text-xl font-bold">Delivery History</h1>
                </div>
            </div>

            <a href="{{ route('rider.dashboard') }}"
               class="bg-white/20 backdrop-blur hover:bg-white/30 text-white px-4 py-2 rounded-full text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        {{-- Hero Stats --}}
        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-white/20">
            <div>
                <p class="text-xs text-white/80">Total Deliveries</p>
                <p class="text-2xl font-bold">{{ $stats['total_deliveries'] }}</p>
            </div>
            <div>
                <p class="text-xs text-white/80">Total Earnings</p>
                <p class="text-2xl font-bold">₱{{ number_format($stats['total_earnings'], 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- STAT CARDS --}}
    {{-- ============================================ --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        {{-- Total Earnings --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Earnings</p>
            <p class="text-lg font-bold text-green-600 dark:text-green-400">₱{{ number_format($stats['total_earnings'], 0) }}</p>
        </div>

        {{-- Total Deliveries --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Deliveries</p>
            <p class="text-lg font-bold text-gray-900 dark:text-neutral-100">{{ $stats['total_deliveries'] }}</p>
        </div>

        {{-- Rating --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl border border-gray-200 dark:border-dark-700 p-4 hover:shadow-md transition">
            <div class="w-9 h-9 rounded-lg bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <p class="text-xs text-gray-500 dark:text-neutral-400">Rating</p>
            <div class="flex items-center gap-1 mt-1">
                @if ($stats['avg_rating'] !== 'N/A')
                    <span class="text-lg font-bold text-gray-900 dark:text-neutral-100">{{ $stats['avg_rating'] }}</span>
                    <svg class="w-4 h-4 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @else
                    <span class="text-sm text-gray-400 dark:text-neutral-500">N/A</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- HISTORY LIST --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden transition-colors">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-dark-700 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 dark:text-neutral-100">Recent Deliveries</h2>
            @if ($orders->total() > 0)
                <span class="text-xs text-gray-500 dark:text-neutral-400">{{ $orders->total() }} total</span>
            @endif
        </div>

        @forelse ($orders as $order)
            <div class="p-4 border-b border-gray-100 dark:border-dark-700 last:border-0 hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                <div class="flex items-start gap-3">

                    {{-- ICON --}}
                    <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-950/40 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    {{-- INFO --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-neutral-100 text-sm">
                                    Order #{{ $order->id }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-neutral-400 mt-0.5">
                                    {{ $order->restaurant->name }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-base font-bold text-green-600 dark:text-green-400">
                                    ₱{{ number_format($order->delivery_fee, 2) }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-neutral-500 uppercase tracking-wide">earned</p>
                            </div>
                        </div>

                        {{-- ADDRESS --}}
                        <div class="flex items-center gap-1.5 mt-2">
                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-neutral-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-1">{{ $order->delivery_address }}</p>
                        </div>

                        {{-- DATE + STATUS BADGE --}}
                        <div class="flex items-center justify-between gap-2 mt-2">
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-neutral-500">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $order->updated_at->format('M d, Y · H:i') }}</span>
                            </div>

                            @if ($order->rider_rating)
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $order->rider_rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300 dark:text-gray-600' }}"
                                             viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-850 mb-4">
                    <svg class="w-8 h-8 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-neutral-100 mb-1">No deliveries yet</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400">Your completed deliveries will appear here</p>
                <a href="{{ route('rider.dashboard') }}"
                   class="inline-block mt-4 bg-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-orange-700 transition">
                    Go to Dashboard
                </a>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection