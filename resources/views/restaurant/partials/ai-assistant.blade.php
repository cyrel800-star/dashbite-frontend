{{-- ============================================ --}}
{{-- AI ASSISTANT FLOATING CHAT WIDGET --}}
{{-- ============================================ --}}
<div x-data="aiAssistant()" x-init="init()" class="fixed bottom-4 right-4 z-[80]">

    {{-- FLOATING BUTTON --}}
    <button x-show="!isOpen"
            @click="isOpen = true"
            type="button"
            class="relative w-14 h-14 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 shadow-2xl flex items-center justify-center text-white hover:scale-105 active:scale-95 transition transform border-2 border-white dark:border-dark-800 overflow-hidden">

        {{-- ⭐ PROFILE PICTURE OR DEFAULT ICON --}}
        @if (auth()->user()->avatar_url)
            <img src="{{ auth()->user()->avatar_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-full h-full rounded-full object-cover">
        @else
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
        @endif

        <span class="absolute top-0 right-0 w-3 h-3 rounded-full bg-green-400 animate-pulse border-2 border-white dark:border-dark-800"></span>
    </button>

    {{-- CHAT PANEL --}}
    <div x-show="isOpen"
         x-cloak
         x-transition.origin.bottom.right
         @keydown.escape.window="isOpen = false"
         class="bg-white dark:bg-dark-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-dark-700 overflow-hidden flex flex-col"
         style="width: 380px; height: 520px; max-width: calc(100vw - 2rem); max-height: calc(100vh - 2rem);">

        {{-- ⭐ HEADER — DYNAMIC PROFILE --}}
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-4 py-3 flex items-center gap-3 flex-shrink-0">

            {{-- Avatar/Profile Picture --}}
            @if (auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-9 h-9 rounded-full object-cover border-2 border-white/30 flex-shrink-0">
            @else
                <div class="w-9 h-9 rounded-full {{ auth()->user()->avatar_color }} flex items-center justify-center text-white text-xs font-bold border-2 border-white/30 flex-shrink-0">
                    {{ auth()->user()->initials }}
                </div>
            @endif

            <div class="flex-1 min-w-0">
                <p class="font-bold text-white text-sm truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-white/80">
                    <span x-text="remaining"></span> messages left today
                </p>
            </div>

            <button @click="isOpen = false" type="button"
                    class="w-7 h-7 rounded-lg hover:bg-white/20 flex items-center justify-center text-white transition flex-shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                </svg>
            </button>
        </div>

        {{-- MESSAGES --}}
        <div x-ref="scrollContainer"
             class="chat-scroll flex-1 min-h-0 overflow-y-auto p-3 space-y-3 bg-gray-50 dark:bg-dark-850">

            {{-- WELCOME + SUGGESTIONS --}}
            <template x-if="messages.length === 0 && !loading">
                <div>
                    <div class="flex items-end gap-2">
                        {{-- ⭐ AI AVATAR --}}
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>

                        <div class="bg-white dark:bg-dark-800 rounded-2xl rounded-bl-none p-3 border border-gray-200 dark:border-dark-700 shadow-sm max-w-[85%]">
                            <p class="text-sm text-gray-700 dark:text-neutral-300">
                                Kumusta! Ako si <strong>Dash</strong> — tutulungan kita sa analytics ng restaurant mo. Anong gusto mong malaman?
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1.5">
                        <p class="text-[10px] font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider px-1">
                            Try asking
                        </p>
                        <template x-for="(s, i) in suggestions" :key="i">
                            <button @click="send(s)"
                                    type="button"
                                    class="w-full text-left text-xs bg-white dark:bg-dark-800 hover:bg-orange-50 dark:hover:bg-orange-950/30 text-gray-700 dark:text-neutral-300 hover:text-orange-700 dark:hover:text-orange-300 border border-gray-200 dark:border-dark-700 hover:border-orange-300 dark:hover:border-orange-800 rounded-xl px-3 py-2 transition">
                                <span x-text="s"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            {{-- ⭐ MESSAGE LIST — WITH AVATARS --}}
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end items-end gap-2' : 'flex justify-start items-end gap-2'">

                    {{-- ⭐ AI AVATAR (left) --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                    </template>

                    {{-- MESSAGE BUBBLE --}}
                    <div :class="msg.role === 'user'
                            ? 'bg-orange-600 text-white rounded-br-none'
                            : 'bg-white dark:bg-dark-800 text-gray-700 dark:text-neutral-300 border border-gray-200 dark:border-dark-700 rounded-bl-none'"
                         class="max-w-[80%] px-3 py-2 rounded-2xl shadow-sm text-sm">
                        <p class="whitespace-pre-wrap break-words" x-text="msg.content"></p>

                        {{-- Tool badge --}}
                        <template x-if="msg.tool_calls && msg.tool_calls.length > 0">
                            <div class="mt-1.5 pt-1.5 border-t border-white/20 dark:border-dark-700 flex flex-wrap gap-1">
                                <template x-for="(tc, j) in msg.tool_calls" :key="j">
                                    <span class="text-[9px] bg-black/20 dark:bg-white/10 px-1.5 py-0.5 rounded font-mono"
                                          x-text="tc.name"></span>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- ⭐ USER AVATAR (right) --}}
                    <template x-if="msg.role === 'user'">
                        <div class="flex-shrink-0">
                            @if (auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}"
                                     alt="{{ auth()->user()->name }}"
                                     class="w-7 h-7 rounded-full object-cover shadow-sm">
                            @else
                                <div class="w-7 h-7 rounded-full {{ auth()->user()->avatar_color }} flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                                    {{ auth()->user()->initials }}
                                </div>
                            @endif
                        </div>
                    </template>
                </div>
            </template>

            {{-- ⭐ TYPING — WITH AI AVATAR --}}
            <template x-if="loading">
                <div class="flex justify-start items-end gap-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-700 px-3 py-2 rounded-2xl rounded-bl-none shadow-sm">
                        <div class="flex gap-1">
                            <span class="typing-dot"></span>
                            <span class="typing-dot" style="animation-delay: 0.15s"></span>
                            <span class="typing-dot" style="animation-delay: 0.3s"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ERROR --}}
            <template x-if="error">
                <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-xl p-3">
                    <p class="text-xs text-red-800 dark:text-red-300" x-text="error"></p>
                </div>
            </template>
        </div>

        {{-- INPUT --}}
        <form @submit.prevent="send()" class="border-t border-gray-200 dark:border-dark-700 p-3 bg-white dark:bg-dark-800 flex-shrink-0">
            <div class="flex gap-2 items-center">
                <input type="text"
                       x-model="input"
                       :disabled="loading || remaining <= 0"
                       placeholder="Tanungin mo ako..."
                       maxlength="500"
                       class="flex-1 border border-gray-300 dark:border-dark-600 dark:bg-dark-850 dark:text-neutral-100 rounded-full px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent transition disabled:opacity-50">

                <button type="submit"
                        :disabled="!input.trim() || loading || remaining <= 0"
                        :class="(!input.trim() || loading || remaining <= 0) ? 'opacity-40 cursor-not-allowed' : 'hover:bg-orange-700 active:scale-95'"
                        class="bg-orange-600 text-white w-9 h-9 rounded-full flex items-center justify-center shadow-md transition transform flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

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
    </style>

    <script>
    function aiAssistant() {
        return {
            isOpen: false,
            input: '',
            messages: [],
            loading: false,
            error: '',
            remaining: {{ config('groq.daily_message_limit', 20) }},

            suggestions: [
                'How much are my sales this month?',
                'What are my best sellers this week?',
                'How many orders do I have today?',
                'What is my average rating?',
                'Anong peak hours ko?',
            ],

            async init() {
                try {
                    const res = await fetch('{{ route("restaurant.ai.status") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        }
                    });
                    const data = await res.json();
                    if (data.ok) {
                        this.remaining = data.remaining;
                    }
                } catch (err) { /* silent */ }
            },

            async send(presetMessage = null) {
                const text = presetMessage ?? this.input.trim();
                if (!text || this.loading || this.remaining <= 0) return;

                this.messages.push({ role: 'user', content: text });
                this.input = '';
                this.error = '';
                this.loading = true;
                this.scrollToBottom();

                const history = this.messages
                    .slice(0, -1)
                    .slice(-6)
                    .map(m => ({ role: m.role, content: m.content }));

                try {
                    const res = await fetch('{{ route("restaurant.ai.chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ message: text, history }),
                    });

                    const data = await res.json();

                    if (!data.ok) {
                        this.error = data.message || 'May error sa AI. Subukan muli.';
                        if (data.limit_reached) {
                            this.remaining = 0;
                        }
                        this.loading = false;
                        return;
                    }

                    this.messages.push({
                        role: 'assistant',
                        content: data.reply,
                        tool_calls: data.tool_calls || [],
                    });

                    if (typeof data.remaining === 'number') {
                        this.remaining = data.remaining;
                    }
                } catch (err) {
                    console.error(err);
                    this.error = 'Network error. Subukan muli.';
                }

                this.loading = false;
                this.scrollToBottom();
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const el = this.$refs.scrollContainer;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            }
        }
    }
    </script>
</div>