<!DOCTYPE html>
<html lang="en">
<head>
    {{-- DARK MODE - Inline script para hindi mag-flash bago mag-load --}}
    <script>
    (function() {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark' || (stored === 'system' || !stored) && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FoodDash') }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ff6b35">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FoodDash">
    <link rel="apple-touch-icon" href="/icon-192.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png">

    <link rel="stylesheet" href="{{ asset('food-dash/app.css') }}">
    <script type="module" src="{{ asset('food-dash/app.js') }}"></script>
</head>
<body class="bg-gray-50 dark:bg-dark-900 min-h-screen pb-20 md:pb-0 transition-colors">

    {{-- ============================================ --}}
    {{-- TOP NAVIGATION (ALL SCREENS) --}}
    {{-- ============================================ --}}
    @auth
    <nav class="bg-white dark:bg-dark-800/95 dark:backdrop-blur-md border-b border-gray-200 dark:border-dark-700 sticky top-0 z-40 transition-colors">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">

                {{-- LEFT: LOGO + DESKTOP LINKS --}}
                <div class="flex items-center gap-6">
                    @php $config = \App\Models\SystemConfig::current(); @endphp
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        @if ($config->hasLogo() || $config->hasLightLogo() || $config->hasDarkLogo())
                            <img src="{{ $config->hasLightLogo() ? $config->light_logo_url : $config->logo_url }}"
                                alt="{{ config('app.name', 'FoodDash') }}"
                                class="w-auto dark:hidden"
                                style="height: {{ $config->logo_height_px }}px;">
                            <img src="{{ $config->hasDarkLogo() ? $config->dark_logo_url : ($config->hasLightLogo() ? $config->light_logo_url : $config->logo_url) }}"
                                alt="{{ config('app.name', 'FoodDash') }}"
                                class="w-auto hidden dark:block"
                                style="height: {{ $config->logo_height_px }}px;">
                        @else
                            <span class="font-semibold text-lg text-gray-900 dark:text-neutral-100">
                                {{ config('app.name', 'FoodDash') }}
                            </span>
                        @endif
                    </a>

                    {{-- CUSTOMER DESKTOP --}}
                    @if (auth()->user()->isCustomer())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('customer.restaurants') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('customer.restaurants*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Restaurants
                            </a>
                            <a href="{{ route('customer.favorites') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('customer.favorites*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Favorites
                            </a>
                            <a href="{{ route('customer.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('customer.orders*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Orders
                            </a>
                            <a href="{{ route('customer.chat') }}"
                               x-data="customerChatBadge()"
                               x-init="init()"
                               class="relative px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('customer.chat*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }} inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Chat
                                <span x-show="unread > 0"
                                      x-cloak
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                      x-text="unread > 99 ? '99+' : unread"></span>
                            </a>
                        </div>
                    @endif

                    {{-- RIDER DESKTOP --}}
                    @if (auth()->user()->isRider())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('rider.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('rider.dashboard') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('rider.earnings') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('rider.earnings*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Earnings
                            </a>
                            <a href="{{ route('rider.chat') }}"
                               x-data="chatBadge()"
                               x-init="init()"
                               class="relative px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('rider.chat*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }} inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Chat
                                <span x-show="unread > 0"
                                      x-cloak
                                      class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                      x-text="unread > 99 ? '99+' : unread"></span>
                            </a>
                        </div>
                    @endif

                    {{-- RESTAURANT DESKTOP --}}
                    @if (auth()->user()->isRestaurant())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('restaurant.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('restaurant.dashboard') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('restaurant.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('restaurant.orders') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Orders
                            </a>
                            <a href="{{ route('menu-items.index') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('menu-items*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Menu
                            </a>
                            <a href="{{ route('restaurant.hours') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('restaurant.hours*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Hours
                            </a>
                            <a href="{{ route('restaurant.reviews.index') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('restaurant.reviews*') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Reviews
                            </a>
                            <a href="{{ route('restaurant.analytics') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('restaurant.analytics') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Analytics
                            </a>
                        </div>
                    @endif

                    {{-- ADMIN DESKTOP --}}
                    @if (auth()->user()->isAdmin())
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('admin.dashboard') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.accounts') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('admin.accounts') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Accounts
                            </a>
                            <a href="{{ route('admin.orders') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('admin.orders') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Orders
                            </a>
                            <a href="{{ route('admin.reports') }}"
                               class="px-3 py-1.5 rounded-md text-sm transition {{ request()->routeIs('admin.reports') ? 'bg-gray-100 dark:bg-dark-850 text-gray-900 dark:text-neutral-100 font-medium' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Reports
                            </a>
                        </div>
                    @endif
                </div>

                {{-- RIGHT: BELL + SETTINGS + AVATAR --}}
                <div class="flex items-center gap-2">

                    {{-- NOTIFICATION BELL — GAMIT ANG ALPINE STORE --}}
                    <div x-data class="relative" x-init="$store.notifications.init()">
                        <button @click="$store.notifications.toggle()"
                                class="relative p-2 text-gray-600 dark:text-neutral-400 hover:text-orange-600 dark:hover:text-orange-500 transition rounded-full hover:bg-gray-100 dark:hover:bg-dark-850">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            <template x-if="$store.notifications.totalUnread > 0">
                                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"
                                      x-text="$store.notifications.totalUnread > 99 ? '99+' : $store.notifications.totalUnread"></span>
                            </template>
                        </button>

                        {{-- DROPDOWN --}}
                        <div x-show="$store.notifications.isOpen"
                             x-cloak
                             @click.outside="$store.notifications.isOpen = false"
                             x-transition.opacity
                             class="fixed md:absolute left-4 right-4 md:left-auto md:right-0 top-[calc(3.5rem+0.5rem)] md:top-auto md:mt-2 md:w-96 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-xl shadow-2xl z-50 overflow-hidden">

                            {{-- HEADER --}}
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-dark-700 flex justify-between items-center bg-gray-50 dark:bg-dark-850">
                                <span class="text-sm font-semibold text-gray-900 dark:text-neutral-100 flex items-center gap-2">
                                    Notifications
                                    <template x-if="$store.notifications.offers.length > 0">
                                        <span class="text-[10px] bg-orange-500 text-white px-1.5 py-0.5 rounded-full font-bold"
                                              x-text="$store.notifications.offers.length + ' offer' + ($store.notifications.offers.length > 1 ? 's' : '')"></span>
                                    </template>
                                </span>
                                <template x-if="$store.notifications.unreadCount > 0">
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-orange-600 dark:text-orange-400 hover:underline">
                                            Mark all read
                                        </button>
                                    </form>
                                </template>
                            </div>

                            {{-- BODY --}}
                            <div class="max-h-[70vh] overflow-y-auto">

                                {{-- LIVE DELIVERY OFFERS (Rider only) --}}
                                <template x-if="$store.notifications.offers.length > 0">
                                    <div>
                                        <div class="px-4 py-2 bg-orange-50 dark:bg-orange-950/30 border-b border-orange-100 dark:border-orange-900/50">
                                            <p class="text-[10px] font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <circle cx="10" cy="10" r="10" opacity="0.3"/>
                                                    <circle cx="10" cy="10" r="5"/>
                                                </svg>
                                                Live Delivery Offers
                                            </p>
                                        </div>

                                        <template x-for="offer in $store.notifications.offers" :key="offer.order_id">
                                            <div @click="$store.notifications.goToOffer(offer.order_id)"
                                                 class="cursor-pointer px-4 py-3 border-b border-gray-100 dark:border-dark-700 hover:bg-orange-50 dark:hover:bg-orange-950/20 transition group">

                                                <div class="flex gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start justify-between gap-2 mb-1">
                                                            <p class="text-sm font-bold text-gray-900 dark:text-neutral-100 truncate"
                                                               x-text="offer.restaurant"></p>
                                                            <span class="text-[10px] font-bold text-green-600 dark:text-green-400 flex-shrink-0 bg-green-100 dark:bg-green-950/40 px-1.5 py-0.5 rounded">
                                                                NEW
                                                            </span>
                                                        </div>

                                                        <div class="flex items-start gap-1 mb-1.5">
                                                            <svg class="w-3 h-3 text-gray-400 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            <p class="text-xs text-gray-500 dark:text-neutral-400 line-clamp-1"
                                                               x-text="offer.delivery_address"></p>
                                                        </div>

                                                        <div class="flex items-center gap-2 text-[10px]">
                                                            <span class="bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-1.5 py-0.5 rounded font-bold">
                                                                ₱<span x-text="offer.delivery_fee"></span> fee
                                                            </span>
                                                            <span class="text-gray-400 dark:text-neutral-500">
                                                                <span x-text="offer.items_count"></span> items
                                                            </span>
                                                            <span class="ml-auto text-orange-600 dark:text-orange-400 font-bold group-hover:translate-x-0.5 transition-transform">
                                                                Accept →
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                {{-- EMPTY STATE --}}
                                <template x-if="$store.notifications.notifications.length === 0 && $store.notifications.offers.length === 0">
                                    <div class="py-12 text-center">
                                        <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 dark:bg-dark-850 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-neutral-400">No notifications yet</p>
                                    </div>
                                </template>

                                {{-- ORDER UPDATES HEADER --}}
                                <template x-if="$store.notifications.notifications.length > 0 && $store.notifications.offers.length > 0">
                                    <div class="px-4 py-2 bg-gray-50 dark:bg-dark-850/50 border-b border-gray-100 dark:border-dark-700">
                                        <p class="text-[10px] font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                            Order Updates
                                        </p>
                                    </div>
                                </template>

                                {{-- REGULAR NOTIFICATIONS --}}
                                <template x-for="n in $store.notifications.notifications" :key="n.id">
                                    <a :href="'/notifications/' + n.id + '/read'"
                                       :class="n.read ? 'bg-white dark:bg-dark-800' : 'bg-orange-50 dark:bg-orange-950/20'"
                                       class="flex gap-3 px-4 py-3 border-b border-gray-100 dark:border-dark-700 last:border-0 hover:bg-gray-50 dark:hover:bg-dark-850 transition">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center">
                                            <template x-if="n.icon === 'star'">
                                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </template>
                                            <template x-if="n.icon === 'chat'">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                            </template>
                                            <template x-if="!n.icon || (n.icon !== 'star' && n.icon !== 'chat')">
                                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-neutral-100 line-clamp-1" x-text="n.title"></p>
                                            <p class="text-xs text-gray-600 dark:text-neutral-400 line-clamp-2 mt-0.5" x-text="n.body"></p>
                                            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1" x-text="n.created_at"></p>
                                        </div>
                                        <template x-if="!n.read">
                                            <span class="flex-shrink-0 w-2 h-2 rounded-full bg-orange-500 mt-2"></span>
                                        </template>
                                    </a>
                                </template>
                            </div>

                            {{-- FOOTER --}}
                            <div class="px-4 py-2 border-t border-gray-100 dark:border-dark-700 bg-gray-50 dark:bg-dark-850">
                                <a href="{{ route('notifications.index') }}"
                                   class="block text-center text-xs text-orange-600 dark:text-orange-400 hover:underline py-1">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    

                    {{-- AVATAR --}}
                    @php
                        if (auth()->user()->isRestaurant()) {
                            $avatarUrl = route('restaurant.profile');
                            $avatarActive = request()->routeIs('restaurant.profile*');
                        } elseif (auth()->user()->isAdmin()) {
                            $avatarUrl = route('admin.profile.index');
                            $avatarActive = request()->routeIs('admin.profile*');
                        } else {
                            $avatarUrl = route('profile.index');
                            $avatarActive = request()->routeIs('profile*');
                        }
                    @endphp
                    <a href="{{ $avatarUrl }}"
                       class="flex items-center gap-2 px-2 py-1.5 rounded-lg transition {{ $avatarActive ? 'bg-gray-100 dark:bg-dark-850' : 'hover:bg-gray-100 dark:hover:bg-dark-850' }}">
                        <div class="relative">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full {{ auth()->user()->avatar_color }} flex items-center justify-center text-white text-xs font-bold">
                                    {{ auth()->user()->initials }}
                                </div>
                            @endif

                            @if (auth()->user()->isOnline())
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:border-dark-800 rounded-full"></span>
                            @endif
                        </div>

                        <span class="hidden md:inline text-sm font-medium text-gray-700 dark:text-neutral-300">
                            {{ auth()->user()->name }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- ============================================ --}}
    {{-- GUEST NAVIGATION --}}
    {{-- ============================================ --}}
    @guest
    <nav class="bg-white dark:bg-dark-800/95 dark:backdrop-blur-md border-b border-gray-200 dark:border-dark-700 sticky top-0 z-40 transition-colors">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-14">
                @php $config = \App\Models\SystemConfig::current(); @endphp
                <a href="{{ route('customer.restaurants') }}" class="flex items-center gap-2 flex-shrink-0">
                    @if ($config->hasLogo() || $config->hasLightLogo() || $config->hasDarkLogo())
                        <img src="{{ $config->active_logo_url }}"
                            alt="{{ config('app.name', 'FoodDash') }}"
                            class="w-auto dark:hidden"
                            style="height: {{ $config->logo_height_px }}px;"
                            id="light-logo">
                        <img src="{{ $config->hasDarkLogo() ? $config->dark_logo_url : $config->active_logo_url }}"
                            alt="{{ config('app.name', 'FoodDash') }}"
                            class="w-auto hidden dark:block"
                            style="height: {{ $config->logo_height_px }}px;">
                    @else
                        <span class="font-semibold text-lg text-gray-900 dark:text-neutral-100">
                            {{ config('app.name', 'FoodDash') }}
                        </span>
                    @endif
                </a>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('login') }}"
                       class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-dark-850 transition">
                        Sign In
                    </a>

                    <a href="{{ route('register') }}"
                       class="whitespace-nowrap flex-shrink-0 px-4 py-2 rounded-lg text-sm font-semibold bg-gradient-to-r from-orange-500 to-orange-600 text-white hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-lg transition">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </nav>
    @endguest

    {{-- PWA INSTALL PROMPT --}}
    <div x-data="pwaInstall()" x-init="init()" x-cloak>
        <template x-if="showInstall">
            <div class="fixed bottom-24 left-4 right-4 md:bottom-4 md:left-auto md:right-4 md:w-80 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 rounded-lg shadow-lg p-4 z-30">
                <div class="flex items-start gap-3">
                    <img src="/icon-192.png" alt="FoodDash" class="w-10 h-10 rounded">
                    <div class="flex-1">
                        <p class="font-semibold text-sm text-gray-900 dark:text-neutral-100">Install FoodDash</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1">Add to your home screen for quick access.</p>
                    </div>
                    <button @click="dismiss()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 flex gap-2">
                    <button @click="install()" class="flex-1 bg-orange-600 text-white text-sm py-2 rounded hover:bg-orange-700">
                        Install
                    </button>
                    <button @click="dismiss()" class="flex-1 border border-gray-300 dark:border-dark-600 text-gray-700 dark:text-neutral-300 text-sm py-2 rounded hover:bg-gray-50 dark:hover:bg-dark-850">
                        Not now
                    </button>
                </div>
            </div>
        </template>
    </div>

    <main class="max-w-6xl mx-auto px-4 py-6 md:py-8">
        @if (session('success'))
            <div data-floating-message data-floating-type="success" class="hidden">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div data-floating-message data-floating-type="error" class="hidden">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ============================================ --}}
    {{-- BOTTOM NAVIGATION (MOBILE ONLY) --}}
    {{-- ============================================ --}}
    @auth
    @php $globalUnread = auth()->user()->unreadNotifications()->count(); @endphp
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-dark-800/95 dark:backdrop-blur-md border-t border-gray-200 dark:border-dark-700 z-40 transition-colors">
        <div class="flex justify-around items-center h-16">

            {{-- CUSTOMER MOBILE --}}
            @if (auth()->user()->isCustomer())
                <a href="{{ route('customer.restaurants') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.restaurants*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('customer.favorites') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.favorites*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Favorites</span>
                </a>

                <a href="{{ route('customer.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.orders*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('customer.chat') }}"
                   x-data="customerChatBadge()"
                   x-init="init()"
                   class="relative flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('customer.chat*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Chat</span>

                    <span x-show="unread > 0"
                          x-cloak
                          class="absolute top-1 right-1/4 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1"
                          x-text="unread > 99 ? '99+' : unread"></span>
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- RIDER MOBILE --}}
            @if (auth()->user()->isRider())
                <a href="{{ route('rider.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.dashboard') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('rider.chat') }}"
                   x-data="chatBadge()"
                   x-init="init()"
                   class="relative flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.chat') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Chat</span>

                    <span x-show="unread > 0"
                          x-cloak
                          class="absolute top-1 right-1/4 bg-red-500 text-white text-[9px] font-bold rounded-full min-w-[16px] h-[16px] flex items-center justify-center px-1"
                          x-text="unread > 99 ? '99+' : unread"></span>
                </a>

                <a href="{{ route('rider.earnings') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('rider.earnings*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Earnings</span>
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- RESTAURANT MOBILE --}}
            @if (auth()->user()->isRestaurant())
                <a href="{{ route('restaurant.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.dashboard') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('restaurant.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.orders') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('menu-items.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('menu-items*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Menu</span>
                </a>

                <a href="{{ route('restaurant.reviews.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition relative {{ request()->routeIs('restaurant.reviews*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Reviews</span>
                </a>

                <a href="{{ route('restaurant.analytics') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('restaurant.analytics') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Analytics</span>
                </a>

                <a href="{{ route('profile.index') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('profile*') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Profile</span>
                </a>
            @endif

            {{-- ADMIN MOBILE --}}
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.dashboard') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Home</span>
                </a>

                <a href="{{ route('admin.accounts') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.accounts') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Accounts</span>
                </a>

                <a href="{{ route('admin.orders') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.orders') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Orders</span>
                </a>

                <a href="{{ route('admin.reports') }}"
                   class="flex flex-col items-center justify-center flex-1 h-full transition {{ request()->routeIs('admin.reports') ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-[10px] mt-0.5 font-medium">Reports</span>
                </a>
            @endif

        </div>
    </nav>
    @endauth

    {{-- ============================================ --}}
    {{-- SLIDE-DOWN NOTIFICATION BANNER (Rider only) --}}
    {{-- ============================================ --}}
    @auth
        @if (auth()->user()->isRider())
            <div class="fixed top-0 left-0 right-0 z-[100] pointer-events-none" x-data>
                <div class="space-y-2 px-3 pt-3 max-w-md mx-auto">
                    <template x-for="(offer, index) in $store.notifications.slideDownOffers" :key="offer.order_id">
                        <div class="pointer-events-auto bg-white dark:bg-dark-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-dark-700 overflow-hidden cursor-pointer"
                             @click="$store.notifications.goToOffer(offer.order_id)">

                            <div class="h-1 bg-gradient-to-r from-orange-500 to-orange-400"></div>

                            <div class="p-3.5">
                                <div class="flex items-start gap-3">
                                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                            <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wide">
                                                New Delivery Offer
                                            </p>
                                        </div>

                                        <p class="font-bold text-gray-900 dark:text-neutral-100 text-sm leading-tight truncate"
                                           x-text="offer.restaurant"></p>

                                        <p class="text-xs text-gray-600 dark:text-neutral-400 line-clamp-1 mt-0.5"
                                           x-text="offer.delivery_address"></p>

                                        <div class="flex items-center gap-2 mt-1.5 text-[11px]">
                                            <span class="bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-300 px-1.5 py-0.5 rounded font-bold"
                                                  x-text="'₱' + offer.delivery_fee"></span>
                                            <span class="text-gray-500 dark:text-neutral-400"
                                                  x-text="offer.items_count + ' items'"></span>
                                        </div>
                                    </div>

                                    <button @click.stop="$store.notifications.dismissOffer(offer.order_id)"
                                            type="button"
                                            class="w-6 h-6 rounded-full hover:bg-gray-100 dark:hover:bg-dark-850 flex items-center justify-center flex-shrink-0 transition">
                                        <svg class="w-3.5 h-3.5 text-gray-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        @endif
    @endauth

    {{-- ============================================ --}}
    {{-- SERVICE WORKER --}}
    {{-- ============================================ --}}
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
    </script>

    {{-- ============================================ --}}
    {{-- GLOBAL ALPINE FUNCTIONS + STORES --}}
    {{-- ============================================ --}}
    <script>
    // ============================================
    // CHAT BADGE FUNCTIONS
    // ============================================
    function customerChatBadge() {
        return {
            unread: 0,
            interval: null,
            init() {
                this.fetchUnread();
                this.interval = setInterval(() => {
                    this.fetchUnread();
                }, 10000);
                window.addEventListener('beforeunload', () => {
                    if (this.interval) clearInterval(this.interval);
                });
            },
            async fetchUnread() {
                try {
                    const res = await fetch('{{ route('customer.chat.unread-active') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    if (typeof data.unread === 'number') {
                        this.unread = data.unread;
                    }
                } catch (err) { /* silent */ }
            }
        }
    }

    function chatBadge() {
        return {
            unread: 0,
            interval: null,
            init() {
                this.fetchUnread();
                this.interval = setInterval(() => {
                    this.fetchUnread();
                }, 10000);
                window.addEventListener('beforeunload', () => {
                    if (this.interval) clearInterval(this.interval);
                });
            },
            async fetchUnread() {
                try {
                    const res = await fetch('{{ route('rider.chat.unread-active') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    if (typeof data.unread === 'number') {
                        this.unread = data.unread;
                    }
                } catch (err) { /* silent */ }
            }
        }
    }

    // ============================================
    // PWA INSTALL
    // ============================================
    function pwaInstall() {
        return {
            showInstall: false,
            deferredPrompt: null,
            dismissed: false,
            init() {
                if (window.matchMedia('(display-mode: standalone)').matches) return;
                if (localStorage.getItem('pwa-dismissed') === 'yes') return;

                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showInstall = true;
                });
                window.addEventListener('appinstalled', () => {
                    this.showInstall = false;
                    this.deferredPrompt = null;
                });
            },
            async install() {
                if (!this.deferredPrompt) return;
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                if (outcome === 'accepted') this.showInstall = false;
                this.deferredPrompt = null;
            },
            dismiss() {
                this.showInstall = false;
                localStorage.setItem('pwa-dismissed', 'yes');
            }
        }
    }

    // ============================================
    // THEME TOGGLE
    // ============================================
    function themeToggle() {
        return {
            theme: 'system',
            init() {
                const stored = localStorage.getItem('theme');
                this.theme = stored || 'system';
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    if (this.theme === 'system') {
                        this.applyTheme();
                    }
                });
            },
            setTheme(mode) {
                this.theme = mode;
                localStorage.setItem('theme', mode);
                this.applyTheme();
            },
            applyTheme() {
                const isDark = this.theme === 'dark' ||
                    (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        }
    }

    // ============================================
    // NOTIFICATIONS STORE
    // ============================================
    document.addEventListener('alpine:init', () => {
        Alpine.store('notifications', {
            isOpen: false,
            notifications: [],
            unreadCount: 0,
            offers: [],
            slideDownOffers: [],
            slideDownTimer: {},
            newOrders: [],

            restaurantId: {{ auth()->user()?->restaurant?->id ?? 'null' }},
            riderId: {{ auth()->user()?->rider?->id ?? 'null' }},
            currentUserId: {{ auth()->id() ?? 'null' }},

            countdownInterval: null,
            beepInterval: null,
            loadNotificationsInterval: null,
            isLoading: false,
            lastLoadTime: 0,
            _savedNotificationsJSON: '',
            initialized: false,

            get totalUnread() {
                return this.unreadCount + this.offers.length + this.newOrders.length;
            },

            init() {
                if (this.initialized) return;
                this.initialized = true;

                this.loadNotifications();

                if (this.currentUserId && typeof window.Echo !== 'undefined') {
                    window.Echo.private(`user.${this.currentUserId}`)
                        .listen('.notification.new', (e) => {
                            this.notifications.unshift({
                                id: e.id,
                                title: e.title,
                                body: e.body,
                                url: e.url,
                                icon: e.icon,
                                read: false,
                                created_at: 'just now',
                            });
                            this.unreadCount++;
                            this.playBeep();
                            if (this.notifications.length > 10) this.notifications.pop();
                        });
                }

                if (this.restaurantId && typeof window.Echo !== 'undefined') {
                    window.Echo.private(`restaurant.${this.restaurantId}`)
                        .listen('.new.order', (e) => {
                            this.handleNewOrder(e);
                        });
                }

                if (this.riderId && typeof window.Echo !== 'undefined') {
                    window.Echo.private(`rider.${this.riderId}`)
                        .listen('.delivery.offer', (e) => {
                            this.handleNewOffer(e);
                        });
                }

                this.countdownInterval = setInterval(() => {
                    this.tickCountdowns();
                }, 1000);

                this.beepInterval = setInterval(() => {
                    if (this.offers.length > 0 || this.newOrders.length > 0) {
                        this.playBeep();
                    }
                }, 5000);

                this.loadNotificationsInterval = setInterval(() => {
                    if (this.offers.length === 0 && this.newOrders.length === 0 && !this.isOpen) {
                        this.loadNotifications();
                    }
                }, 90000);
            },

            handleNewOrder(e) {
                const orderId = parseInt(e.order_id);
                if (this.newOrders.find(o => o.order_id === orderId)) return;

                const order = {
                    order_id: orderId,
                    customer_name: e.customer_name || 'Customer',
                    items_count: parseInt(e.items_count) || 0,
                    food_cost: parseFloat(e.food_cost || 0).toFixed(2),
                    delivery_fee: parseFloat(e.delivery_fee || 0).toFixed(2),
                    total_amount: parseFloat(e.total_amount || 0).toFixed(2),
                    delivery_address: e.delivery_address || '',
                    is_external: !!e.is_external,
                    created_at_human: e.created_at_human || 'just now',
                };

                this.newOrders.push(order);
                this.playBeep();
                this.showOrderBrowserNotification(order);

                if (window.location.pathname.includes('/restaurant/dashboard')) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }

                setTimeout(() => {
                    this.dismissNewOrder(orderId);
                }, 30000);
            },

            dismissNewOrder(orderId) {
                this.newOrders = this.newOrders.filter(o => o.order_id !== orderId);
            },

            goToOrder(orderId) {
                this.dismissNewOrder(orderId);
                this.isOpen = false;
                window.location.href = '/restaurant/dashboard';
            },

            async loadNotifications() {
                const now = Date.now();
                if (this.isLoading || (now - this.lastLoadTime) < 5000) return;

                this.isLoading = true;
                this.lastLoadTime = now;

                try {
                    const res = await fetch('{{ route('notifications.recent') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();

                    const newNotifications = data.notifications || [];
                    const newUnread = parseInt(data.unread_count) || 0;

                    const newJSON = JSON.stringify(newNotifications);
                    if (newJSON !== this._savedNotificationsJSON) {
                        this.notifications = newNotifications;
                        this._savedNotificationsJSON = newJSON;
                    }
                    if (newUnread !== this.unreadCount) {
                        this.unreadCount = newUnread;
                    }
                } catch (err) {
                    // silent
                } finally {
                    this.isLoading = false;
                }
            },

            async handleNewOffer(e) {
                const orderId = parseInt(e.order_id);
                if (this.offers.find(o => o.order_id === orderId)) return;

                try {
                    const res = await fetch(`/rider/offers/${orderId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (!data.ok) return;

                    const expiresIn = Math.floor(parseInt(data.offer.expires_in) || 180);

                    const offer = {
                        order_id: parseInt(data.offer.order_id),
                        radius_km: Math.floor(parseFloat(data.offer.radius_km) || 0),
                        expires_in: expiresIn,
                        secondsLeft: expiresIn,
                        restaurant: data.offer.restaurant || '',
                        restaurant_address: data.offer.restaurant_address || '',
                        delivery_address: data.offer.delivery_address || '',
                        food_cost: parseFloat(data.offer.food_cost || 0).toFixed(2),
                        delivery_fee: parseFloat(data.offer.delivery_fee || 0).toFixed(2),
                        total_amount: parseFloat(data.offer.total_amount || 0).toFixed(2),
                        items_count: parseInt(data.offer.items_count) || 0,
                        distance_km: data.offer.distance_km ? parseFloat(data.offer.distance_km).toFixed(2) : null,
                    };

                    this.offers.push(offer);
                    this.slideDownOffers.push(offer);

                    this.slideDownTimer[orderId] = setTimeout(() => {
                        this.dismissSlideDown(orderId);
                    }, 60000);

                    this.playBeep();
                    this.showBrowserNotification(offer);
                } catch (err) {
                    console.error('Failed to fetch offer details:', err);
                }
            },

            tickCountdowns() {
                if (this.offers.length === 0 && this.slideDownOffers.length === 0) return;

                this.offers = this.offers.map(offer => {
                    offer.secondsLeft = Math.max(0, Math.floor(offer.secondsLeft) - 1);
                    return offer;
                }).filter(offer => offer.secondsLeft > 0);

                this.slideDownOffers = this.slideDownOffers.map(offer => {
                    offer.secondsLeft = Math.max(0, Math.floor(offer.secondsLeft) - 1);
                    return offer;
                }).filter(offer => offer.secondsLeft > 0);
            },

            dismissSlideDown(orderId) {
                this.slideDownOffers = this.slideDownOffers.filter(o => o.order_id !== orderId);
                if (this.slideDownTimer[orderId]) {
                    clearTimeout(this.slideDownTimer[orderId]);
                    delete this.slideDownTimer[orderId];
                }
            },

            dismissOffer(orderId) {
                this.offers = this.offers.filter(o => o.order_id !== orderId);
                this.slideDownOffers = this.slideDownOffers.filter(o => o.order_id !== orderId);
                if (this.slideDownTimer[orderId]) {
                    clearTimeout(this.slideDownTimer[orderId]);
                    delete this.slideDownTimer[orderId];
                }
            },

            goToOffer(orderId) {
                this.dismissOffer(orderId);
                this.isOpen = false;
                window.location.href = `/rider/dashboard?offer=${orderId}&focus=1`;
            },

            toggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) this.loadNotifications();
            },

            playBeep() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.frequency.value = 800;
                    gain.gain.value = 0.15;
                    osc.start();
                    setTimeout(() => { osc.stop(); ctx.close(); }, 150);
                } catch (e) { /* silent */ }
            },

            showBrowserNotification(offer) {
                if (!('Notification' in window)) return;
                if (Notification.permission !== 'granted') return;

                new Notification('New Delivery Offer!', {
                    body: `${offer.restaurant} — ₱${offer.delivery_fee} fee`,
                    icon: '/icon-192.png',
                    tag: `offer-${offer.order_id}`,
                    requireInteraction: true,
                });
            },

            showOrderBrowserNotification(order) {
                if (!('Notification' in window)) return;
                if (Notification.permission !== 'granted') return;

                new Notification('New Order Received!', {
                    body: `${order.customer_name} — ₱${order.total_amount} (${order.items_count} items)`,
                    icon: '/icon-192.png',
                    tag: `order-${order.order_id}`,
                    requireInteraction: true,
                });
            }
        });
    });
    </script>

    {{-- ============================================ --}}
    {{-- ALPINE.JS --}}
    {{-- ============================================ --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    {{-- ============================================ --}}
    {{-- AUTO-CLEAR THEME ON LOGOUT --}}
    {{-- ============================================ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[action*="logout"]').forEach(form => {
                form.addEventListener('submit', function() {
                    localStorage.removeItem('theme');
                    document.documentElement.classList.remove('dark');
                });
            });
        });
    </script>

    {{-- ============================================ --}}
    {{-- PAGE-SPECIFIC SCRIPTS --}}
    {{-- ============================================ --}}
    @stack('scripts')

    {{-- ============================================ --}}
    {{-- AI ASSISTANT — Customer only --}}
    {{-- ============================================ --}}
    @auth
        @if (auth()->user()->isCustomer())
            @include('partials.ai-bubble')
        @endif
    @endauth

</body>
</html>
