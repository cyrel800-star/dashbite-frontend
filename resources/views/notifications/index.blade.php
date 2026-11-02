@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-neutral-100">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">All your order updates and messages</p>
        </div>

        @if (auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button class="text-sm text-orange-600 dark:text-orange-400 hover:underline font-medium">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($notifications->isEmpty())
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 p-16 text-center transition-colors">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-dark-850 mb-4">
                <svg class="w-8 h-8 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <p class="text-gray-500 dark:text-neutral-400">No notifications yet.</p>
            <p class="text-sm text-gray-400 dark:text-neutral-500 mt-2">We'll notify you when something happens!</p>
        </div>
    @else
        <div class="bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-700 divide-y divide-gray-100 dark:divide-gray-800 transition-colors">
            @foreach ($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isRead = !is_null($notification->read_at);
                @endphp

                <a href="{{ route('notifications.mark-read', $notification->id) }}"
                   class="flex gap-4 p-4 hover:bg-gray-50 dark:hover:bg-dark-850 transition {{ $isRead ? '' : 'bg-orange-50 dark:bg-orange-950/30' }}">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center text-xl">
                        {{ $data['icon'] ?? '🔔' }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <p class="font-medium text-gray-900 dark:text-neutral-100">
                                {{ $data['title'] ?? 'Notification' }}
                            </p>
                            @if (!$isRead)
                                <span class="flex-shrink-0 w-2 h-2 rounded-full bg-orange-500 mt-1.5"></span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mt-0.5">
                            {{ $data['body'] ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection