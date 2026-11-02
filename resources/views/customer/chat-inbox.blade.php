@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex justify-between items-start gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100">Chats</h1>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                    {{ $conversations->count() }} {{ Str::plural('conversation', $conversations->count()) }}
                </p>
            </div>

            <div class="flex items-center gap-2">
              

                @if ($conversations->count() > 0)
                    <div x-data="{ showConfirm: false }">
                        <button type="button"
                                @click="showConfirm = true"
                                class="inline-flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-950/30 px-3 py-2 rounded-lg font-medium transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="hidden sm:inline">Clear</span>
                        </button>

                        {{-- CONFIRM MODAL --}}
                        <div x-show="showConfirm"
                             x-cloak
                             x-transition.opacity
                             @keydown.escape.window="showConfirm = false"
                             class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4"
                             @click.self="showConfirm = false">

                            <div x-show="showConfirm"
                                 x-transition.scale.origin.center
                                 class="bg-white dark:bg-dark-800 rounded-2xl shadow-xl max-w-sm w-full p-6">

                                <div class="flex justify-center mb-4">
                                    <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="text-center mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-neutral-100">Clear chat inbox?</h3>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">
                                        This will hide all chats from your inbox. Messages are still kept in the system for records.
                                    </p>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button"
                                            @click="showConfirm = false"
                                            class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                                        Cancel
                                    </button>

                                    <form method="POST" action="{{ route('customer.chat.clear-all') }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full bg-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700 shadow-md transition">
                                            Clear Inbox
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($conversations->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 p-12 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-950/40 mb-4">
                <svg class="w-10 h-10 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 dark:text-neutral-100 mb-1">No chats yet</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                You can chat with riders when you have an active delivery.
            </p>
            <a href="{{ route('customer.orders') }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg transition">
                View Orders
            </a>
        </div>
    @else
        {{-- CONVERSATION LIST --}}
        <div class="bg-white dark:bg-dark-800 rounded-2xl border border-gray-200 dark:border-dark-700 overflow-hidden divide-y divide-gray-100 dark:divide-gray-800">

            @foreach ($conversations as $convo)
                @php
                    $order = $convo['order'];
                    $lastMessage = $convo['last_message'];
                    $rider = $order->rider;
                    $riderUser = $rider?->user;
                @endphp

                <a href="{{ route('customer.chat.show', $order) }}"
                   class="flex items-center gap-3 px-4 py-3.5 hover:bg-gray-50 dark:hover:bg-dark-850 transition group {{ $convo['is_hidden'] ? 'opacity-60' : '' }}">

                    {{-- RIDER PROFILE PICTURE --}}
                    <div class="relative flex-shrink-0">
                        @if ($riderUser && $riderUser->avatar_url)
                            <img src="{{ $riderUser->avatar_url }}"
                                 alt="{{ $riderUser->name }}"
                                 class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-dark-800 shadow-sm">
                        @else
                            <div class="w-12 h-12 rounded-full {{ $riderUser?->avatar_color ?? 'bg-gray-400' }} flex items-center justify-center text-white text-base font-bold border-2 border-white dark:border-dark-800 shadow-sm">
                                {{ $riderUser?->initials ?? '?' }}
                            </div>
                        @endif

                        @if ($convo['unread_count'] > 0)
                            <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 border-2 border-white dark:border-dark-800">
                                {{ $convo['unread_count'] > 99 ? '99+' : $convo['unread_count'] }}
                            </span>
                        @endif

                        @if ($convo['is_active'])
                            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-orange-500 border-2 border-white dark:border-dark-800 rounded-full"></span>
                        @endif
                    </div>

                    {{-- INFO --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <p class="font-bold text-gray-900 dark:text-neutral-100 truncate">
                                {{ $riderUser?->name ?? 'Rider' }}
                            </p>
                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 flex-shrink-0">
                                @if ($lastMessage)
                                    {{ $lastMessage->created_at->diffForHumans(null, true) }}
                                @endif
                            </span>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">
                            <span class="font-medium">Order #{{ $order->id }}</span>
                            · {{ $order->restaurant->name ?? 'Restaurant' }}
                        </p>

                        @if ($lastMessage)
                            <p class="text-sm text-gray-600 dark:text-neutral-400 truncate mt-0.5">
                                @if ($lastMessage->sender_id === auth()->id())
                                    <span class="text-gray-400 dark:text-neutral-500">You: </span>
                                @endif
                                {{ Str::limit($lastMessage->body, 50) }}
                            </p>
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <div class="flex-shrink-0 flex flex-col items-end gap-1">
                        @if ($convo['is_hidden'])
                            <span class="text-[10px] bg-gray-200 dark:bg-dark-700 text-gray-600 dark:text-neutral-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                Cleared
                            </span>
                        @elseif ($convo['is_active'])
                            <span class="text-[10px] bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                Active
                            </span>
                        @else
                            <span class="text-[10px] bg-gray-100 dark:bg-dark-850 text-gray-600 dark:text-neutral-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                {{ ucfirst($order->status) }}
                            </span>
                        @endif

                        <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection