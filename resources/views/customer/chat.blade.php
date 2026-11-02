@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto flex flex-col" style="height: calc(95vh - 10rem);">

    {{-- HEADER --}}
    <div class="mb-4 flex-shrink-0">
        <a href="{{ route('customer.chat') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-neutral-300 hover:text-orange-600 dark:hover:text-orange-400 transition mb-2 group">
            <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Back
        </a>
    </div>

    {{-- CHAT CARD --}}
    <div x-data="chatPage({{ $currentOrder->id }})" x-init="init()"
         class="bg-white dark:bg-dark-800 rounded-2xl shadow-lg border border-gray-200 dark:border-dark-700 overflow-hidden flex flex-col flex-1 min-h-0">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-5 py-4 flex items-center gap-3 flex-shrink-0">
            @if ($currentOrder->rider && $currentOrder->rider->user && $currentOrder->rider->user->avatar_url)
                <img src="{{ $currentOrder->rider->user->avatar_url }}"
                     alt="{{ $currentOrder->rider->user->name }}"
                     class="w-11 h-11 rounded-full object-cover border-2 border-white/30">
            @else
                <div class="w-11 h-11 rounded-full {{ $currentOrder->rider?->user?->avatar_color ?? 'bg-white/20' }} flex items-center justify-center text-white text-sm font-bold border-2 border-white/30">
                    {{ $currentOrder->rider?->user?->initials ?? '?' }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <p class="font-bold text-white truncate">
                    {{ $currentOrder->rider?->user?->name ?? 'Rider' }}
                </p>
                <p class="text-xs text-white/80 line-clamp-1">
                    {{ $currentOrder->restaurant->name ?? '' }}
                </p>
            </div>

            @if (in_array($currentOrder->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))
                <span class="text-[10px] bg-white/20 backdrop-blur text-white px-2 py-1 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                    Active
                </span>
            @else
                <span class="text-[10px] bg-white/20 backdrop-blur text-white px-2 py-1 rounded-full font-bold uppercase tracking-wide flex-shrink-0">
                    {{ ucfirst($currentOrder->status) }}
                </span>
            @endif
        </div>

        {{-- MESSAGES --}}
        <div x-ref="messagesContainer"
             @scroll="onScroll()"
             class="chat-scroll flex-1 min-h-0 overflow-y-auto p-4 space-y-2 bg-gray-50 dark:bg-dark-850">

            <template x-if="loading">
                <p class="text-center text-sm text-gray-500 dark:text-neutral-400 py-4">Loading messages...</p>
            </template>

            <template x-if="!loading && messages.length === 0">
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-orange-100 dark:bg-orange-950/40 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-neutral-400">No messages yet</p>
                </div>
            </template>

            {{-- ⭐ MESSAGE LOOP --}}
            <template x-for="msg in messages" :key="msg.id">
                <div class="contents">

                    {{-- DATE DIVIDER --}}
                    <div x-show="isNewDay(msg)" class="flex items-center gap-3 py-3">
                        <div class="flex-1 h-px bg-gray-300 dark:bg-dark-600"></div>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider px-2"
                              x-text="formatDate(msg.created_at)"></span>
                        <div class="flex-1 h-px bg-gray-300 dark:bg-dark-600"></div>
                    </div>

                    {{-- MESSAGE ROW --}}
                    <div :class="msg.sender_id === currentUserId ? 'flex justify-end' : 'flex justify-start'"
                         class="chat-message gap-2 items-end">

                        {{-- LEFT AVATAR --}}
                        <template x-if="msg.sender_id !== currentUserId">
                            <div class="flex-shrink-0">
                                <template x-if="msg.sender_avatar_url">
                                    <img :src="msg.sender_avatar_url"
                                         :alt="msg.sender_name"
                                         class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-dark-800 shadow-sm">
                                </template>
                                <template x-if="!msg.sender_avatar_url">
                                    <div :class="msg.sender_avatar_color || 'bg-gray-500'"
                                         class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-dark-800 shadow-sm"
                                         x-text="msg.sender_initials || '?'"></div>
                                </template>
                            </div>
                        </template>

                        {{-- BUBBLE + TIMESTAMP --}}
                        <div :class="msg.sender_id === currentUserId ? 'items-end' : 'items-start'"
                             class="flex flex-col max-w-[75%]">

                            <div :class="msg.sender_id === currentUserId
                                    ? 'bg-orange-600 text-white rounded-br-none'
                                    : 'bg-white dark:bg-dark-800 text-gray-900 dark:text-neutral-100 border border-gray-200 dark:border-dark-700 rounded-bl-none'"
                                 class="px-3 py-2 rounded-2xl shadow-sm">
                                <p class="text-sm break-words whitespace-pre-wrap" x-text="msg.body"></p>
                            </div>

                            <span class="text-[10px] text-gray-400 dark:text-neutral-500 mt-1 px-1"
                                  :class="msg.sender_id === currentUserId ? 'text-right' : 'text-left'"
                                  x-text="msg.created_at_human"></span>
                        </div>

                        {{-- RIGHT AVATAR --}}
                        <template x-if="msg.sender_id === currentUserId">
                            <div class="flex-shrink-0">
                                <template x-if="msg.sender_avatar_url">
                                    <img :src="msg.sender_avatar_url"
                                         :alt="msg.sender_name"
                                         class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-dark-800 shadow-sm">
                                </template>
                                <template x-if="!msg.sender_avatar_url">
                                    <div :class="msg.sender_avatar_color || 'bg-orange-500'"
                                         class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold border-2 border-white dark:border-dark-800 shadow-sm"
                                         x-text="msg.sender_initials || '?'"></div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- TYPING INDICATOR --}}
            <template x-if="typingName">
                <div class="flex justify-start">
                    <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 px-3 py-2 rounded-2xl rounded-bl-none shadow-sm">
                        <p class="text-xs text-gray-500 dark:text-neutral-400 mb-1" x-text="typingName + ' is typing'"></p>
                        <div class="flex gap-1">
                            <span class="typing-dot"></span>
                            <span class="typing-dot" style="animation-delay: 0.15s"></span>
                            <span class="typing-dot" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- STATUS INDICATOR --}}
            <template x-if="!typingName && lastMessageIsMine && lastOwnMessage">
                <div class="flex justify-end pr-1 mt-1">

                    <template x-if="lastOwnMessage.status === 'sent'">
                        <p class="text-[10px] text-gray-400 dark:text-neutral-500 italic">Sending...</p>
                    </template>

                    <template x-if="lastOwnMessage.status === 'delivered'">
                        <div class="w-4 h-4 rounded-full border-2 border-orange-500 flex items-center justify-center">
                            <svg class="w-2.5 h-2.5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>

                    <template x-if="lastOwnMessage.status === 'seen'">
                        <div class="w-4 h-4 rounded-full bg-orange-500 flex items-center justify-center shadow-sm">
                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- INPUT --}}
        <div class="border-t border-gray-200 dark:border-dark-700 bg-white dark:bg-dark-800 flex-shrink-0"
             x-data="{ showEmojiPicker: false }">

            @if (in_array($currentOrder->status, ['rider_assigned', 'picked_up', 'out_for_delivery']))

                {{-- EMOJI PICKER --}}
                <div x-show="showEmojiPicker"
                     x-cloak
                     x-transition
                     class="border-b border-gray-200 dark:border-dark-700 p-3 bg-gray-50 dark:bg-dark-850">

                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                            Quick Emojis
                        </p>
                        <button type="button"
                                @click="showEmojiPicker = false"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-1">
                        @foreach (['😀','😁','😂','🤣','😊','😍','😘','😎','🤔','😅','😢','😭','😡','🥺','😴','🤤','👍','👎','👏','🙏','💪','👋','🤝','❤️','💕','💯','🔥','⭐','✨','🎉','🎊','🍔','🍕','🍟','🍗','🍜','🍰','☕','🥤','🛵','🚴','🏠','🏪','⏰','📍','✅','❌','⚠️','💬','📞','🙌'] as $emoji)
                            <button type="button"
                                    @click="
                                        const input = $refs.messageInput;
                                        const start = input.selectionStart;
                                        const end = input.selectionEnd;
                                        newMessage = newMessage.substring(0, start) + '{{ $emoji }}' + newMessage.substring(end);
                                        $nextTick(() => {
                                            input.focus();
                                            input.setSelectionRange(start + 2, start + 2);
                                        });
                                    "
                                    class="text-xl w-9 h-9 rounded-lg hover:bg-white dark:hover:bg-dark-800 hover:scale-110 active:scale-95 transition-transform flex items-center justify-center">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- MESSAGE INPUT --}}
                <form @submit.prevent="sendMessage()" class="p-3 md:p-4 flex gap-2 items-center">

                    <button type="button"
                            @click="showEmojiPicker = !showEmojiPicker"
                            :class="showEmojiPicker ? 'bg-orange-100 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-neutral-400 hover:bg-gray-100 dark:hover:bg-dark-850'"
                            class="w-11 h-11 rounded-full flex items-center justify-center transition flex-shrink-0"
                            title="Emojis">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    <input type="text"
                           x-ref="messageInput"
                           x-model="newMessage"
                           @input="onTypingInput()"
                           @keydown.enter.prevent="sendMessage()"
                           placeholder="Type a message..."
                           maxlength="1000"
                           class="flex-1 border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-full px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">

                    <button type="submit"
                            :disabled="!newMessage.trim() || sending"
                            :class="(!newMessage.trim() || sending) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-orange-700 active:scale-95'"
                            class="bg-orange-600 text-white w-11 h-11 rounded-full flex items-center justify-center shadow-md transition transform flex-shrink-0">
                        <template x-if="!sending">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </template>
                        <template x-if="sending">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                    </button>
                </form>

            @else
                <div class="p-3 md:p-4 text-center">
                    <p class="text-xs text-gray-500 dark:text-neutral-400">
                        This order is {{ $currentOrder->status }}. Chat is read-only.
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
    .typing-dot {
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #9ca3af;
        animation: bounceDot 1.4s infinite ease-in-out both;
    }
    @keyframes bounceDot {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }
    .chat-scroll::-webkit-scrollbar { display: none; }
    .chat-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    .chat-message { animation: slideIn 0.25s ease-out; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
<script>
function chatPage(orderId) {
    return {
        loading: true,
        sending: false,
        messages: [],
        newMessage: '',
        currentUserId: {{ auth()->id() }},
        currentUserName: '{{ auth()->user()->name }}',
        currentUserAvatar: '{{ auth()->user()->avatar_url }}',
        currentUserInitials: '{{ auth()->user()->initials }}',
        currentUserAvatarColor: '{{ auth()->user()->avatar_color }}',
        channel: null,
        typingName: '',
        typingTimeout: null,
        isTypingSent: false,
        isAtBottom: true,
        listenerAttached: false,

        init() {
            if (window.Echo) {
                window.Echo.leave(`order.${orderId}.chat`);
            }

            this.loadMessages();

            setTimeout(() => {
                this.subscribeToChat(orderId);
            }, 300);
        },

        subscribeToChat(orderId) {
            if (this.listenerAttached) return;
            if (typeof window.Echo === 'undefined') return;

            const chatChannel = window.Echo.private(`order.${orderId}.chat`);

            chatChannel.listen('.message.sent', (e) => {
                if (e.sender_id === this.currentUserId) return;

                e.status = 'received';
                this.messages.push(e);
                this.markAsRead();

                if (this.isAtBottom) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });

            chatChannel.listen('.user.typing', (e) => {
                if (e.user_id === this.currentUserId) return;
                this.typingName = e.is_typing ? e.user_name : '';
            });

            chatChannel.listen('.messages.read', (e) => {
                if (e.reader_id === this.currentUserId) return;
                this.messages.forEach(m => {
                    if (e.message_ids.includes(m.id) && m.sender_id === this.currentUserId) {
                        m.status = 'seen';
                        m.read_at = e.read_at;
                    }
                });
            });

            this.channel = chatChannel;
            this.listenerAttached = true;
        },

        // ⭐ DATE HELPERS
        isNewDay(msg) {
            const idx = this.messages.findIndex(m => m.id === msg.id);
            if (idx <= 0) return true;

            const prev = this.messages[idx - 1];
            if (!prev) return false;

            return this.getDateString(prev.created_at) !== this.getDateString(msg.created_at);
        },

        getDateString(dateString) {
            if (!dateString) return '';
            const d = new Date(dateString);
            return d.toDateString();
        },

        formatDate(dateString) {
            if (!dateString) return '';

            const d = new Date(dateString);
            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);

            if (d.toDateString() === today.toDateString()) {
                return 'Today';
            }

            if (d.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            }

            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: d.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
            });
        },

        // ⭐ LAST MESSAGE HELPERS
        get lastMessage() {
            if (this.messages.length === 0) return null;
            return this.messages[this.messages.length - 1];
        },

        get lastOwnMessage() {
            if (this.messages.length === 0) return null;

            for (let i = this.messages.length - 1; i >= 0; i--) {
                if (this.messages[i].sender_id === this.currentUserId) {
                    return this.messages[i];
                }
            }

            return null;
        },

        get lastMessageIsMine() {
            const last = this.lastMessage;
            if (!last) return false;
            return last.sender_id === this.currentUserId;
        },

        async loadMessages() {
            try {
                const res = await fetch('{{ route('customer.chat.index', $currentOrder) }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();
                this.messages = (data.messages || []).map(m => ({
                    ...m,
                    status: m.sender_id === this.currentUserId
                        ? (m.read_at ? 'seen' : (m.delivered_at ? 'delivered' : 'sent'))
                        : 'received',
                }));
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            } catch (err) {
                console.error('Failed to load messages', err);
                this.loading = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const body = this.newMessage.trim();
            this.newMessage = '';
            this.stopTyping();

            const optimisticId = 'temp-' + Date.now();
            const optimisticMsg = {
                id: optimisticId,
                sender_id: this.currentUserId,
                sender_name: this.currentUserName,
                sender_avatar_url: this.currentUserAvatar,
                sender_initials: this.currentUserInitials,
                sender_avatar_color: this.currentUserAvatarColor,
                body: body,
                created_at: new Date().toISOString(),
                created_at_human: 'just now',
                status: 'sent',
            };
            this.messages.push(optimisticMsg);
            this.scrollToBottom();

            try {
                const res = await fetch('{{ route('customer.chat.store', $currentOrder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ body })
                });
                const data = await res.json();
                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) {
                    this.messages[idx] = { ...data.message, status: 'delivered' };
                }
            } catch (err) {
                console.error('Failed to send message', err);
                const idx = this.messages.findIndex(m => m.id === optimisticId);
                if (idx !== -1) this.messages.splice(idx, 1);
                this.newMessage = body;
            }
            this.sending = false;
        },

        onTypingInput() {
            if (!this.isTypingSent && this.newMessage.trim()) {
                this.sendTyping(true);
                this.isTypingSent = true;
            }
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.stopTyping();
            }, 2000);
        },

        stopTyping() {
            if (this.isTypingSent) {
                this.sendTyping(false);
                this.isTypingSent = false;
            }
            clearTimeout(this.typingTimeout);
        },

        async sendTyping(isTyping) {
            try {
                await fetch('{{ route('customer.chat.typing', $currentOrder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ is_typing: isTyping })
                });
            } catch (err) { /* silent */ }
        },

        async markAsRead() {
            try {
                await fetch('{{ route('customer.chat.mark-read', $currentOrder) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
            } catch (err) { /* silent */ }
        },

        onScroll() {
            const el = this.$refs.messagesContainer;
            if (!el) return;
            this.isAtBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 50;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                    this.isAtBottom = true;
                }
            });
        }
    }
}
</script>
@endpush