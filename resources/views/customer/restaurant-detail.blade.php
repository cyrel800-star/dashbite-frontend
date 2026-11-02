@extends('layouts.customer')

@section('content')
@php
    $menuForOrdering = $restaurant->menuItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => $item->name,
        'description' => $item->description,
        'price' => (float) $item->price,
    ])->values();
    $deliveryFee = (float) config('orders.delivery_fee');
    $isCustomer = auth()->check() && auth()->user()->role === \App\Enums\UserRole::Customer;
@endphp

<div class="space-y-7" x-data="restaurantOrder(@js($menuForOrdering), {{ $deliveryFee }})">
    @if (session('order_success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/30 dark:text-green-300">
            Order #{{ session('order_success') }} was placed. The restaurant will update its status as it prepares your order.
        </div>
    @endif
    @if (session('review_success'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/30 dark:text-green-300">
            {{ session('review_success') }}
        </div>
    @endif
    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm text-orange-900 dark:border-orange-900 dark:bg-orange-950/30 dark:text-orange-200">
            {{ $errors->first() }}
        </div>
    @endif

    <article class="overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-[#262626] dark:bg-[#141414]">
        <div class="relative h-56 bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 sm:h-72">
            @if ($restaurant->cover_image_url)
                <img src="{{ $restaurant->cover_image_url }}" alt="{{ $restaurant->name }}" class="absolute inset-0 h-full w-full object-cover">
            @endif
            <a href="{{ route('customer.restaurants') }}" class="absolute left-4 top-4 rounded-lg bg-white/95 px-3 py-2 text-sm font-semibold text-neutral-900 shadow">
                Back to restaurants
            </a>
        </div>

        <div class="p-5 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex min-w-0 items-center gap-4">
                    @if ($restaurant->profile_image_url)
                        <img src="{{ $restaurant->profile_image_url }}" alt="" class="h-16 w-16 rounded-2xl object-cover">
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white sm:text-3xl">{{ $restaurant->name }}</h1>
                        @if ($restaurant->cuisine)
                            <p class="mt-1 text-sm font-medium text-orange-700 dark:text-orange-400">{{ $restaurant->cuisine }}</p>
                        @endif
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ $restaurant->address }}</p>
                        @if ($restaurant->phone)
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $restaurant->phone }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($restaurant->display_badge)
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-800 dark:bg-orange-950/40 dark:text-orange-300">{{ $restaurant->display_badge }}</span>
                    @endif
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $restaurant->isOpenNow() ? 'bg-green-100 text-green-800 dark:bg-green-950/50 dark:text-green-300' : 'bg-neutral-100 text-neutral-700 dark:bg-[#262626] dark:text-neutral-300' }}">
                        {{ $restaurant->isOpenNow() ? 'Open' : 'Closed' }}
                    </span>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-neutral-200 pt-4 text-sm dark:border-[#262626]">
                <span class="font-semibold text-neutral-800 dark:text-neutral-200">
                    @if ($restaurant->reviews_count)
                        {{ number_format((float) $restaurant->reviews_avg_rating, 1) }} / 5 · {{ $restaurant->reviews_count }} {{ \Illuminate\Support\Str::plural('review', $restaurant->reviews_count) }}
                    @else
                        No reviews yet
                    @endif
                </span>
                <span class="text-neutral-500 dark:text-neutral-400">About {{ $restaurant->prep_time_minutes }} min</span>
                @if (is_array($restaurant->opening_hours) && count($restaurant->opening_hours))
                    <details class="text-neutral-500 dark:text-neutral-400">
                        <summary class="cursor-pointer font-medium">Opening hours</summary>
                        <ul class="mt-2 space-y-1">
                            @foreach ($restaurant->opening_hours as $day => $hours)
                                <li>{{ ucfirst($day) }}: {{ $hours ?: 'Closed' }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </div>
        </div>
    </article>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-[#262626] dark:bg-[#141414] sm:p-7">
            <div class="mb-5 flex items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400">From the kitchen</p>
                    <h2 class="mt-1 text-xl font-bold text-neutral-900 dark:text-white">Available menu</h2>
                </div>
                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $restaurant->menuItems->count() }} items</span>
            </div>

            @if ($restaurant->menuItems->isEmpty())
                <p class="rounded-xl bg-neutral-50 p-5 text-sm text-neutral-500 dark:bg-[#202020] dark:text-neutral-400">No menu items are available right now.</p>
            @else
                <ul class="divide-y divide-neutral-100 dark:divide-[#262626]">
                    @foreach ($restaurant->menuItems as $item)
                        <li class="flex items-center justify-between gap-4 py-4">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $item->name }}</h3>
                                @if ($item->description)
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $item->description }}</p>
                                @endif
                                <p class="mt-1 text-sm font-bold text-orange-700 dark:text-orange-400">₱{{ number_format((float) $item->price, 2) }}</p>
                            </div>
                            @if ($isCustomer && $restaurant->isOrderable())
                                <div class="flex shrink-0 items-center gap-2">
                                    <template x-if="quantity({{ $item->id }}) === 0">
                                        <button type="button" @click="increment({{ $item->id }})" class="rounded-lg bg-orange-600 px-3 py-2 text-sm font-semibold text-white hover:bg-orange-700">Add</button>
                                    </template>
                                    <template x-if="quantity({{ $item->id }}) > 0">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="decrement({{ $item->id }})" class="grid h-8 w-8 place-items-center rounded-lg border border-neutral-200 text-neutral-800 dark:border-[#404040] dark:text-white" aria-label="Remove one {{ $item->name }}">−</button>
                                            <span class="min-w-4 text-center text-sm font-bold" x-text="quantity({{ $item->id }})"></span>
                                            <button type="button" @click="increment({{ $item->id }})" class="grid h-8 w-8 place-items-center rounded-lg bg-orange-600 text-white" aria-label="Add one {{ $item->name }}">+</button>
                                        </div>
                                    </template>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        @if ($isCustomer)
            <aside class="h-fit rounded-2xl border border-neutral-200 bg-white p-5 dark:border-[#262626] dark:bg-[#141414] lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-neutral-900 dark:text-white">Your order</h2>
                @if ($restaurant->isOrderable())
                    <form method="POST" action="{{ route('customer.orders.store', $restaurant) }}" class="mt-4 space-y-4">
                        @csrf
                        <div class="max-h-48 space-y-2 overflow-y-auto">
                            <template x-for="line in cartLines" :key="line.id">
                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-neutral-700 dark:text-neutral-300"><span x-text="line.quantity"></span> × <span x-text="line.name"></span></span>
                                    <span class="shrink-0 font-semibold" x-text="money(line.price * line.quantity)"></span>
                                </div>
                            </template>
                            <p x-show="cartLines.length === 0" class="py-4 text-sm text-neutral-500 dark:text-neutral-400">Add an item to start your order.</p>
                        </div>

                        <template x-for="(line, index) in cartLines" :key="`order-${line.id}`">
                            <div>
                                <input type="hidden" :name="`items[${index}][menu_item_id]`" :value="line.id">
                                <input type="hidden" :name="`items[${index}][quantity]`" :value="line.quantity">
                            </div>
                        </template>

                        <div class="space-y-1 border-t border-neutral-200 pt-3 text-sm dark:border-[#262626]">
                            <div class="flex justify-between"><span>Subtotal</span><span x-text="money(subtotal)"></span></div>
                            <div class="flex justify-between"><span>Delivery</span><span x-text="money(deliveryFee)"></span></div>
                            <div class="flex justify-between pt-2 text-base font-bold"><span>Total</span><span class="text-orange-700 dark:text-orange-400" x-text="money(total)"></span></div>
                        </div>

                        <label class="block text-sm font-semibold text-neutral-800 dark:text-neutral-200" for="delivery-address">Delivery address</label>
                        <textarea id="delivery-address" name="delivery_address" required maxlength="1000" rows="2" placeholder="Enter your delivery address" class="w-full rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-[#404040] dark:bg-[#0a0a0a] dark:text-white"></textarea>
                        <input type="hidden" name="delivery_lat" x-model="latitude">
                        <input type="hidden" name="delivery_lng" x-model="longitude">
                        <button type="button" @click="useCurrentLocation()" class="w-full rounded-xl border border-orange-300 px-3 py-2 text-sm font-semibold text-orange-700 dark:border-orange-900 dark:text-orange-300">Use my current location</button>
                        <p x-show="locationMessage" x-text="locationMessage" role="status" class="text-xs text-neutral-500 dark:text-neutral-400"></p>

                        <label class="block text-sm font-semibold text-neutral-800 dark:text-neutral-200" for="payment-method">Payment method</label>
                        <select id="payment-method" name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm dark:border-[#404040] dark:bg-[#0a0a0a] dark:text-white">
                            <option value="cod">Cash on delivery</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                        </select>
                        <template x-if="paymentMethod !== 'cod'">
                            <label class="block text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                                Payment reference
                                <input type="text" name="payment_reference" required maxlength="100" placeholder="Enter your payment reference" class="mt-1 w-full rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm font-normal dark:border-[#404040] dark:bg-[#0a0a0a] dark:text-white">
                            </label>
                        </template>
                        <button type="submit" :disabled="cartLines.length === 0" class="w-full rounded-xl bg-orange-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-50">Place order</button>
                    </form>
                @else
                    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">This restaurant is not taking orders right now.</p>
                @endif
            </aside>
        @elseif (auth()->guest())
            <aside class="h-fit rounded-2xl border border-neutral-200 bg-white p-5 text-center dark:border-[#262626] dark:bg-[#141414]">
                <h2 class="font-bold text-neutral-900 dark:text-white">Ready to order?</h2>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">Sign in with a customer account to add items to your cart and check out.</p>
                <a href="{{ route('login') }}" class="mt-4 inline-flex rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-700">Sign in to order</a>
            </aside>
        @else
            <aside class="h-fit rounded-2xl border border-neutral-200 bg-white p-5 text-sm text-neutral-500 dark:border-[#262626] dark:bg-[#141414] dark:text-neutral-400">
                Restaurant staff accounts manage incoming orders from the restaurant workspace.
            </aside>
        @endif
    </div>

    <section class="rounded-2xl border border-neutral-200 bg-white p-5 dark:border-[#262626] dark:bg-[#141414] sm:p-7">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400">Guest feedback</p>
                <h2 class="mt-1 text-xl font-bold text-neutral-900 dark:text-white">Reviews</h2>
            </div>
            <span class="text-sm text-neutral-500 dark:text-neutral-400">
                @if ($restaurant->reviews_count)
                    {{ number_format((float) $restaurant->reviews_avg_rating, 1) }} / 5
                @else
                    No ratings yet
                @endif
            </span>
        </div>

        @if ($reviewableOrders->isNotEmpty())
            <div class="mt-5 space-y-3">
                @foreach ($reviewableOrders as $order)
                    <form method="POST" action="{{ route('customer.reviews.store', $order) }}" class="grid gap-3 rounded-xl bg-neutral-50 p-4 dark:bg-[#202020] sm:grid-cols-[10rem_minmax(0,1fr)_auto] sm:items-end">
                        @csrf
                        <label class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                            Rate order #{{ $order->id }}
                            <select name="rating" required class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 dark:border-[#404040] dark:bg-[#0a0a0a] dark:text-white">
                                <option value="">Choose rating</option>
                                @foreach (range(5, 1) as $rating)
                                    <option value="{{ $rating }}">{{ $rating }} {{ \Illuminate\Support\Str::plural('star', $rating) }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-semibold text-neutral-800 dark:text-neutral-200">
                            Comment (optional)
                            <input name="comment" maxlength="2000" class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 dark:border-[#404040] dark:bg-[#0a0a0a] dark:text-white">
                        </label>
                        <button type="submit" class="rounded-lg bg-[#2D2D2D] px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Submit review</button>
                    </form>
                @endforeach
            </div>
        @endif

        <div class="mt-5 divide-y divide-neutral-100 dark:divide-[#262626]">
            @forelse ($restaurant->reviews as $review)
                <article class="py-4 first:pt-0">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $review->customer?->name ?? 'Customer' }}</h3>
                        <span class="text-sm font-bold text-orange-700 dark:text-orange-400">{{ $review->rating }} / 5</span>
                    </div>
                    @if ($review->comment)
                        <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">{{ $review->comment }}</p>
                    @endif
                    <time class="mt-2 block text-xs text-neutral-400" datetime="{{ $review->created_at->toAtomString() }}">{{ $review->created_at->format('M j, Y') }}</time>
                </article>
            @empty
                <p class="py-6 text-sm text-neutral-500 dark:text-neutral-400">No reviews yet. Complete an order to leave the first one.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
