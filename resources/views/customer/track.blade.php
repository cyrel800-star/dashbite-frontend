@extends('layouts.app')

@section('content')
<div x-data="orderTracker({{ $order->id }})" x-init="init()" class="max-w-3xl mx-auto space-y-5">

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">{{ $order->restaurant->name }}</p>
        </div>
        
    </div>

    {{-- ============================================ --}}
    {{-- STATUS CARD --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1 font-bold">Current Status</p>
        <p class="text-lg font-bold text-neutral-900 dark:text-white" x-text="statusLabel"></p>

        {{-- PROGRESS --}}
        <div class="mt-6 flex items-center">
            <template x-for="(step, i) in steps" :key="i">
                <div class="flex items-center flex-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 transition-colors"
                         :class="stepIndex >= i
                            ? 'bg-gradient-to-br from-orange-500 to-orange-600 text-white shadow-sm shadow-orange-500/30'
                            : 'bg-neutral-200 dark:bg-neutral-800 text-neutral-500 dark:text-neutral-400'">
                        <span x-text="i + 1"></span>
                    </div>
                    <div class="flex-1 h-0.5 mx-1 transition-colors"
                         :class="stepIndex > i
                            ? 'bg-gradient-to-r from-orange-500 to-orange-400'
                            : 'bg-neutral-200 dark:bg-neutral-800'"
                         x-show="i < steps.length - 1"></div>
                </div>
            </template>
        </div>
        <div class="flex justify-between mt-2">
            <template x-for="(step, i) in steps" :key="'lbl' + i">
                <span class="text-xs transition-colors"
                      :class="stepIndex >= i
                        ? 'text-neutral-900 dark:text-white font-bold'
                        : 'text-neutral-400 dark:text-neutral-500'"
                      x-text="step"></span>
            </template>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- REJECTION REASON --}}
    {{-- ============================================ --}}
    @if ($order->status === 'rejected' && $order->rejection_reason)
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neutral-700 to-neutral-900 dark:from-neutral-600 dark:to-neutral-800 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-neutral-700 dark:text-neutral-300 font-bold mb-1">Order Rejected</p>
                    <p class="text-sm text-neutral-700 dark:text-neutral-300">Reason: {{ $order->rejection_reason }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CANCELLATION REASON --}}
    {{-- ============================================ --}}
    @if ($order->status === 'cancelled' && $order->cancellation_reason)
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neutral-700 to-neutral-900 dark:from-neutral-600 dark:to-neutral-800 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wider text-neutral-700 dark:text-neutral-300 font-bold mb-1">Order Cancelled</p>
                    <p class="text-sm text-neutral-700 dark:text-neutral-300">Reason: {{ $order->cancellation_reason }}</p>
                    @if ($order->cancelled_at)
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                            Cancelled {{ $order->cancelled_at->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CANCEL ORDER (REDESIGNED MODAL) --}}
    {{-- ============================================ --}}
    @if ($order->canBeCancelledByCustomer())
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors"
             x-data="cancelOrderModal()">

            {{-- TRIGGER CARD --}}
            <div class="flex justify-between items-center gap-3 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-neutral-100 to-neutral-200 dark:from-neutral-800 dark:to-neutral-900 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-neutral-700 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-neutral-900 dark:text-white">Need to cancel?</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                            You can cancel this order while it's still being processed.
                        </p>
                    </div>
                </div>

                <button type="button"
                        @click="openModal()"
                        class="flex-shrink-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md hover:from-orange-600 hover:to-orange-700 hover:shadow-lg hover:shadow-orange-500/30 active:scale-98 transition transform flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel Order
                </button>
            </div>

            {{-- ⭐═══════════════════════════════════════════--}}
            {{-- CANCEL MODAL --}}
            {{-- ⭐═══════════════════════════════════════════--}}
            <div x-show="showModal"
                 x-cloak
                 x-transition.opacity
                 @keydown.escape.window="closeModal()"
                 @click.self="closeModal()"
                 class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">

                <div x-show="showModal"
                     x-transition.scale.origin.center
                     class="bg-white dark:bg-neutral-900 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-neutral-200 dark:border-neutral-800">

                    {{-- ⭐ GRADIENT ORANGE HEADER --}}
                    <div class="relative bg-gradient-to-br from-orange-500 via-orange-500 to-orange-600 px-6 py-6 text-white overflow-hidden">
                        {{-- Decorative --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-amber-300 rounded-full blur-3xl opacity-30 -ml-12 -mb-12"></div>

                        <div class="relative flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0 border-2 border-white/30 shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-white/80 uppercase tracking-widest font-bold">Confirmation</p>
                                <h3 class="text-lg font-bold leading-tight">Cancel this order?</h3>
                            </div>
                        </div>
                    </div>

                    {{-- ⭐ BODY --}}
                    <div class="p-6">

                        {{-- ORDER PREVIEW CARD --}}
                        <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-2xl p-4 border border-neutral-200 dark:border-neutral-800 mb-5">
                            <div class="flex items-center gap-3">
                                {{-- Restaurant Image --}}
                                <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-neutral-200 dark:bg-neutral-800">
                                    @if ($order->restaurant->profile_image_url)
                                        <img src="{{ $order->restaurant->profile_image_url }}"
                                             alt="{{ $order->restaurant->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Order Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm text-neutral-900 dark:text-white truncate">
                                        {{ $order->restaurant->name }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                                        Order #{{ $order->id }} · {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                    </p>
                                    <p class="text-sm font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent mt-1">
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- WARNING MESSAGE --}}
                        <div class="flex items-start gap-3 mb-5">
                            <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-neutral-900 dark:text-white mb-1">
                                    This action cannot be undone
                                </p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
                                    Once you cancel this order, <strong class="text-neutral-700 dark:text-neutral-300">it cannot be restored</strong>. You'll need to place a new order if you still want the food.
                                </p>
                            </div>
                        </div>

                        {{-- CANCELLATION REASON SELECTOR --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-2 uppercase tracking-wider">
                                Reason for cancellation <span class="text-orange-500">*</span>
                            </label>
                            <div class="relative">
                                <select x-model="reason"
                                        required
                                        class="w-full appearance-none border-2 border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-800 text-neutral-900 dark:text-white rounded-xl px-4 py-3 pr-10 text-sm font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition cursor-pointer">
                                    <option value="">Select a reason...</option>
                                    <option value="Changed my mind">Changed my mind</option>
                                    <option value="Ordered by mistake">Ordered by mistake</option>
                                    <option value="Found a better option">Found a better option</option>
                                    <option value="Delivery taking too long">Delivery taking too long</option>
                                    <option value="Need to modify my order">Need to modify my order</option>
                                    <option value="Other">Other</option>
                                </select>
                                {{-- Custom dropdown arrow --}}
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex gap-2">
                            <button type="button"
                                    @click="closeModal()"
                                    class="flex-1 bg-neutral-100 dark:bg-neutral-800 border-2 border-neutral-200 dark:border-neutral-700 text-neutral-900 dark:text-white px-4 py-3 rounded-xl font-bold text-sm hover:bg-neutral-200 dark:hover:bg-neutral-700 active:scale-98 transition transform">
                                Keep Order
                            </button>

                            <form method="POST"
                                  action="{{ route('customer.orders.cancel', $order) }}"
                                  class="flex-1"
                                  @submit="submitting = true">
                                @csrf
                                <input type="hidden" name="cancellation_reason" :value="reason">
                                <button type="submit"
                                        :disabled="!reason || submitting"
                                        :class="(!reason || submitting)
                                            ? 'opacity-40 cursor-not-allowed'
                                            : 'hover:from-orange-600 hover:to-orange-700 hover:shadow-lg hover:shadow-orange-500/40 active:scale-98'"
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-xl font-bold text-sm shadow-md shadow-orange-500/30 transition transform flex items-center justify-center gap-2">
                                    <template x-if="!submitting">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancel Order
                                        </span>
                                    </template>
                                    <template x-if="submitting">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            Cancelling...
                                        </span>
                                    </template>
                                </button>
                            </form>
                        </div>

                        {{-- Helper text --}}
                        <p class="text-[10px] text-center text-neutral-400 dark:text-neutral-500 mt-3 leading-relaxed">
                            Once cancelled, the restaurant and rider (if assigned) will be notified.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- DIRECT RATING FORM --}}
    {{-- ============================================ --}}
    @if ($order->status === 'delivered' && $order->canBeReviewed())
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-orange-500/30">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-neutral-900 dark:text-white">
                        Rate your experience
                    </p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">
                        Help other customers by sharing your review for
                        <strong class="text-neutral-700 dark:text-neutral-300">{{ $order->restaurant->name }}</strong>.
                    </p>
                </div>
            </div>

            {{-- INLINE RATING FORM --}}
            <form method="POST" action="{{ route('customer.reviews.store', $order) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- Star Rating --}}
                <div x-data="{ rating: 0, hoverRating: 0 }">
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-2 uppercase tracking-wider">
                        Overall Rating <span class="text-orange-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <template x-for="i in 5" :key="i">
                            <button type="button"
                                    @click="rating = i"
                                    @mouseenter="hoverRating = i"
                                    @mouseleave="hoverRating = 0"
                                    class="transition-transform hover:scale-110">
                                <svg class="w-10 h-10 transition-colors"
                                     :class="i <= (hoverRating || rating)
                                        ? 'text-orange-500 fill-orange-500 drop-shadow-sm'
                                        : 'text-neutral-300 dark:text-neutral-600'"
                                     viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" name="rating" :value="rating">
                    <p x-show="rating > 0"
                       x-text="['', 'Terrible', 'Poor', 'Average', 'Good', 'Excellent'][rating]"
                       class="text-sm font-bold text-orange-600 dark:text-orange-400 mt-2"></p>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-2 uppercase tracking-wider">
                        Title <span class="text-neutral-400 dark:text-neutral-500 font-medium normal-case">(optional)</span>
                    </label>
                    <input type="text" name="title" maxlength="100"
                           placeholder="e.g. Best pizza in town!"
                           class="w-full border border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition placeholder-neutral-400">
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-xs font-bold text-neutral-700 dark:text-neutral-300 mb-2 uppercase tracking-wider">
                        Your Review <span class="text-orange-500">*</span>
                    </label>
                    <textarea name="body" required minlength="10" maxlength="2000" rows="4"
                              placeholder="How was your experience? Was the food and service good?"
                              class="w-full border border-neutral-300 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none transition placeholder-neutral-400"></textarea>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl font-bold text-sm shadow-md shadow-orange-500/30 hover:from-orange-600 hover:to-orange-700 hover:shadow-lg hover:shadow-orange-500/40 active:scale-98 transition transform flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Submit Review
                </button>
            </form>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- EXISTING REVIEW --}}
    {{-- ============================================ --}}
    @if ($order->status === 'delivered' && $order->hasBeenReviewed())
        @php $review = $order->review; @endphp
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-orange-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-neutral-900 dark:text-white">Review Submitted</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        Thank you for your review.
                    </p>
                </div>
            </div>

            <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-4 border border-neutral-200 dark:border-neutral-800">
                <div class="flex items-center gap-1 mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-orange-500 fill-orange-500' : 'text-neutral-300 dark:text-neutral-600' }}"
                             viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                @if ($review->title)
                    <p class="font-bold text-neutral-900 dark:text-white mb-1 text-sm">{{ $review->title }}</p>
                @endif
                <p class="text-sm text-neutral-700 dark:text-neutral-300">{{ $review->body }}</p>
                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">
                    Submitted {{ $review->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- ORDER PICKED UP BADGE --}}
    {{-- ============================================ --}}
    @if ($order->verified_pickup_at)
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border-2 border-orange-500 p-5 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md shadow-orange-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-neutral-900 dark:text-white">Order Picked Up</p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        The rider picked up your order at {{ $order->verified_pickup_at->format('g:i A') }}.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- CHAT WITH RIDER --}}
    {{-- ============================================ --}}
    @if ($order->canChat())
        @php
            $unreadChat = $order->messages()
                ->where('sender_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->count();
        @endphp

        <a href="{{ route('customer.chat.show', $order) }}"
           class="flex items-center justify-between gap-3 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-5 py-4 rounded-2xl shadow-md shadow-orange-500/30 hover:shadow-lg hover:shadow-orange-500/40 transition group">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur flex items-center justify-center flex-shrink-0 border-2 border-white/30">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>

                <div>
                    <p class="font-bold text-sm">Chat with Rider</p>
                    <p class="text-xs text-white/80">
                        {{ $order->rider?->user?->name ?? 'Rider' }}
                    </p>
                </div>

                @if ($unreadChat > 0)
                    <span class="bg-white text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">
                        {{ $unreadChat }} new
                    </span>
                @endif
            </div>

            <svg class="w-5 h-5 text-white/80 group-hover:translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @endif

    {{-- ============================================ --}}
    {{-- LIVE MAP --}}
    {{-- ============================================ --}}
    @if ($order->rider && in_array($order->status, ['rider_assigned', 'preparing', 'ready_for_pickup', 'picked_up', 'out_for_delivery']))
        <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
            <div class="flex justify-between items-center mb-3">
                <p class="text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400 font-bold">Live Rider Location</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400" x-text="lastUpdated"></p>
            </div>
            <div id="map" class="w-full h-80 rounded-2xl border border-neutral-200 dark:border-neutral-800 z-0 overflow-hidden"></div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Distance to you</p>
                    <p class="text-lg font-bold text-neutral-900 dark:text-white mt-0.5" x-text="distanceText || '—'"></p>
                </div>
                <div class="bg-neutral-50 dark:bg-neutral-800/50 rounded-xl p-3 border border-neutral-200 dark:border-neutral-800">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Estimated arrival</p>
                    <p class="text-lg font-bold text-orange-500 mt-0.5" x-text="etaText || '—'"></p>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- DELIVERY --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-2 font-bold">Delivery Address</p>
        <p class="text-sm text-neutral-700 dark:text-neutral-300">{{ $order->delivery_address }}</p>

        @if ($order->rider)
            <div class="mt-4 pt-4 border-t border-neutral-200 dark:border-neutral-800">
                <p class="text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-1 font-bold">Your Rider</p>
                <p class="text-sm font-bold text-neutral-900 dark:text-white">{{ $order->rider->user->name }}</p>
                @if ($order->rider->vehicle_type)
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                        {{ $order->rider->vehicle_type }}
                        @if ($order->rider->vehicle_plate)
                            · {{ $order->rider->vehicle_plate }}
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- ITEMS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-200 dark:border-neutral-800 p-5 transition-colors">
        <p class="text-xs uppercase tracking-wider text-neutral-500 dark:text-neutral-400 mb-4 font-bold">Order Summary</p>

        {{-- Payment Info --}}
        <div class="flex justify-between text-sm pb-4 border-b border-neutral-200 dark:border-neutral-800">
            <span class="text-neutral-500 dark:text-neutral-400 font-medium">Payment</span>
            <div class="text-right">
                <span class="font-bold text-neutral-900 dark:text-white">
                    {{ $order->payment_method_label }}
                </span>
                @if ($order->payment_reference)
                    <p class="text-[10px] text-neutral-400 dark:text-neutral-500 font-mono mt-0.5">
                        Ref: {{ $order->payment_reference }}
                    </p>
                @endif
                @if ($order->isPrepaid())
                    <span class="inline-block mt-1 text-[10px] bg-gradient-to-r from-orange-500 to-orange-600 text-white px-2 py-0.5 rounded-full font-bold uppercase tracking-wide shadow-sm">
                        ✓ Paid
                    </span>
                @endif
            </div>
        </div>

        <div class="space-y-2 mt-4">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-neutral-700 dark:text-neutral-300">
                        <span class="font-bold text-orange-500">{{ $item->quantity }}×</span>
                        {{ $item->name }}
                    </span>
                    <span class="font-semibold text-neutral-900 dark:text-white">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-neutral-200 dark:border-neutral-800 space-y-1.5 text-sm">
            <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                <span>Food cost</span>
                <span>₱{{ number_format($order->food_cost, 2) }}</span>
            </div>
            <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                <span>Delivery fee</span>
                <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-neutral-900 dark:text-white pt-3 border-t border-neutral-200 dark:border-neutral-800 text-base">
                <span>Total</span>
                <span class="bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ============================================
// CANCEL ORDER MODAL
// ============================================
function cancelOrderModal() {
    return {
        showModal: false,
        reason: '',
        submitting: false,

        openModal() {
            this.showModal = true;
            this.reason = '';
            this.submitting = false;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            this.reason = '';
            this.submitting = false;
            document.body.style.overflow = '';
        }
    }
}

// ============================================
// ORDER TRACKER (with LIVE MAP)
// ============================================
function orderTracker(orderId) {
    return {
        status: '{{ $order->status }}',
        steps: ['Placed', 'Confirmed', 'Preparing', 'Rider', 'On the way', 'Delivered'],
        map: null,
        riderMarker: null,
        restaurantMarker: null,
        deliveryMarker: null,
        routeLine: null,
        lastUpdated: '',
        distanceText: '',
        etaText: '',
        mapInitialized: false,
        routeDebounce: null,

        init() {
            window.Echo.private(`order.${orderId}`)
                .listen('.order.status', (e) => { this.status = e.status; location.reload(); })
                .listen('.rider.assigned', (e) => { this.status = e.status; location.reload(); })
                .listen('.no.rider', () => { this.status = 'no_rider'; });

            window.Echo.private(`order.${orderId}`)
                .listen('.rider.location', (e) => {
                    this.updateRiderLocation(e.latitude, e.longitude);
                });

            @if ($order->rider && in_array($order->status, ['rider_assigned', 'preparing', 'ready_for_pickup', 'picked_up', 'out_for_delivery']))
                this.$nextTick(() => { this.initMap(); });
            @endif
        },

        initMap() {
            if (this.mapInitialized) return;

            const mapContainer = document.getElementById('map');
            if (!mapContainer) return;

            if (mapContainer._leaflet_id) {
                mapContainer._leaflet_id = null;
            }

            const restaurantLat = {{ $order->restaurant->latitude }};
            const restaurantLng = {{ $order->restaurant->longitude }};
            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};
            const riderLat = {{ $order->rider->latitude ?? $order->restaurant->latitude }};
            const riderLng = {{ $order->rider->longitude ?? $order->restaurant->longitude }};

            this.map = L.map('map').setView([riderLat, riderLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
            }).addTo(this.map);

            @php
                $trackingRestaurantUrl = $order->restaurant->profile_image_url ?? '';
                $trackingRestaurantInitial = strtoupper(substr($order->restaurant->name, 0, 1));
            @endphp

            const trackingRestaurantUrl = '{{ $trackingRestaurantUrl }}';
            const trackingRestaurantInitial = '{{ $trackingRestaurantInitial }}';

            const restaurantIcon = L.divIcon({
                html: trackingRestaurantUrl ? `
                    <div style="position:relative;width:52px;height:52px;">
                        <div style="width:52px;height:52px;border-radius:50%;overflow:hidden;border:3px solid #f97316;box-shadow:0 3px 8px rgba(249,115,22,0.5);background:white;">
                            <img src="${trackingRestaurantUrl}" style="width:100%;height:100%;object-fit:cover;" alt="Restaurant">
                        </div>
                        <div style="position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:bold;border:2px solid white;white-space:nowrap;">STORE</div>
                    </div>
                ` : `
                    <div style="position:relative;width:52px;height:52px;">
                        <div style="background:linear-gradient(135deg,#f97316,#ea580c);color:white;width:52px;height:52px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:bold;border:3px solid white;box-shadow:0 3px 8px rgba(0,0,0,0.4);">${trackingRestaurantInitial}</div>
                        <div style="position:absolute;bottom:-4px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#f97316,#ea580c);color:white;padding:1px 6px;border-radius:8px;font-size:9px;font-weight:bold;border:2px solid white;white-space:nowrap;">STORE</div>
                    </div>
                `,
                className: '',
                iconSize: [52, 60],
                iconAnchor: [26, 30],
            });

            @php
                $trackingCustomerIcon = auth()->user()->gender === 'female'
                    ? '/images/customergirl.png'
                    : '/images/customerman.png';
            @endphp

            const deliveryIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:48px;height:48px;">
                        <img src="{{ $trackingCustomerIcon }}" style="width:48px;height:48px;object-fit:contain;filter:drop-shadow(0 3px 6px rgba(0,0,0,0.4));" alt="You">
                    </div>
                `,
                className: '',
                iconSize: [48, 48],
                iconAnchor: [24, 24],
            });

            const riderIcon = L.divIcon({
                html: `
                    <div style="position:relative;width:56px;height:56px;">
                        <img src="/images/rider.png" style="width:56px;height:56px;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(0,0,0,0.5));" alt="Rider">
                    </div>
                `,
                className: '',
                iconSize: [56, 56],
                iconAnchor: [28, 28],
            });

            this.restaurantMarker = L.marker([restaurantLat, restaurantLng], { icon: restaurantIcon })
                .addTo(this.map)
                .bindPopup('{{ $order->restaurant->name }}');

            this.deliveryMarker = L.marker([deliveryLat, deliveryLng], { icon: deliveryIcon })
                .addTo(this.map)
                .bindPopup('Your Address');

            @if ($order->rider)
                this.riderMarker = L.marker([riderLat, riderLng], { icon: riderIcon })
                    .addTo(this.map)
                    .bindPopup('{{ $order->rider->user->name }}');
            @endif

            this.drawRoute(restaurantLat, restaurantLng, deliveryLat, deliveryLng);
            this.mapInitialized = true;
        },

        async drawRoute(fromLat, fromLng, toLat, toLng) {
            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) {
                    this.drawStraightLine(fromLat, fromLng, toLat, toLng);
                    return;
                }

                const route = data.routes[0];
                const coordinates = route.geometry.coordinates;
                const latlngs = coordinates.map(coord => [coord[1], coord[0]]);

                this.routeLine = L.polyline(latlngs, {
                    color: '#f97316',
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';

                if (this.routeLine) {
                    const bounds = this.routeLine.getBounds();
                    this.map.fitBounds(bounds, { padding: [50, 50] });
                }
            } catch (err) {
                this.drawStraightLine(fromLat, fromLng, toLat, toLng);
            }
        },

        drawStraightLine(fromLat, fromLng, toLat, toLng) {
            this.routeLine = L.polyline([
                [fromLat, fromLng],
                [toLat, toLng],
            ], {
                color: '#f97316',
                weight: 3,
                opacity: 0.5,
                dashArray: '8, 8',
            }).addTo(this.map);
        },

        updateRiderLocation(lat, lng) {
            if (!this.riderMarker) return;
            this.riderMarker.setLatLng([lat, lng]);
            this.lastUpdated = 'Updated: ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const deliveryLat = {{ $order->delivery_lat }};
            const deliveryLng = {{ $order->delivery_lng }};

            clearTimeout(this.routeDebounce);
            this.routeDebounce = setTimeout(() => {
                this.redrawRouteFromRider(lat, lng, deliveryLat, deliveryLng);
            }, 1000);
        },

        async redrawRouteFromRider(fromLat, fromLng, toLat, toLng) {
            if (this.routeLine) {
                this.map.removeLayer(this.routeLine);
                this.routeLine = null;
            }

            try {
                const url = `https://router.project-osrm.org/route/v1/driving/${fromLng},${fromLat};${toLng},${toLat}?overview=full&geometries=geojson`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.routes || data.routes.length === 0) return;

                const route = data.routes[0];
                const coordinates = route.geometry.coordinates;
                const latlngs = coordinates.map(coord => [coord[1], coord[0]]);

                this.routeLine = L.polyline(latlngs, {
                    color: '#f97316',
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round',
                    lineCap: 'round',
                }).addTo(this.map);

                const distanceKm = route.distance / 1000;
                const durationMin = Math.round(route.duration / 60);

                this.distanceText = distanceKm < 1
                    ? Math.round(distanceKm * 1000) + ' m'
                    : distanceKm.toFixed(1) + ' km';

                this.etaText = durationMin < 1 ? 'Arriving' : durationMin + ' min';
            } catch (err) {
                console.warn('Route redraw failed:', err);
            }
        },

        get statusLabel() {
            const labels = {
                'received': 'Waiting for restaurant confirmation',
                'confirmed': 'Restaurant confirmed your order',
                'preparing': 'Preparing your food',
                'finding_rider': 'Finding a rider',
                'rider_assigned': 'Rider assigned — waiting for restaurant to prepare',
                'picked_up': 'Rider picked up your order',
                'out_for_delivery': 'Order is on the way',
                'delivered': 'Order delivered',
                'no_rider': 'No rider available',
                'cancelled': 'Order cancelled',
                'rejected': 'Order rejected by restaurant',
            };
            return labels[this.status] || this.status.replace(/_/g, ' ');
        },

        get stepIndex() {
            const map = {
                'received': 0, 'confirmed': 1, 'preparing': 2,
                'finding_rider': 3, 'rider_assigned': 3,
                'picked_up': 4, 'out_for_delivery': 4, 'delivered': 5,
                'no_rider': 0, 'cancelled': 0, 'rejected': 0,
            };
            return map[this.status] ?? 0;
        }
    }
}
</script>
@endpush
