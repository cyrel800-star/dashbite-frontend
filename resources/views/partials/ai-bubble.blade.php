{{-- ============================================ --}}
{{-- AI ASSISTANT — FLOATING BUBBLE + PANEL --}}
{{-- ============================================ --}}

@php $config = \App\Models\SystemConfig::current(); @endphp

{{-- ============================================ --}}
{{-- AI NOTIFICATION POPUP --}}
{{-- ============================================ --}}
<div id="aiPopup" class="ai-popup" role="alert">
    <div class="ai-popup-content">
        <div class="ai-popup-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
        </div>

        <div class="ai-popup-text">
            <p class="ai-popup-title">Hi! I'm Dash </p>
            <p class="ai-popup-body">I can help you find restaurants, discover food, and track your orders. Try me!</p>
        </div>

        <button type="button" class="ai-popup-close" aria-label="Dismiss" onclick="dismissAiPopup(event)">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    {{-- Arrow will be positioned dynamically by JS --}}
</div>

{{-- ============================================ --}}
{{-- FLOATING BUBBLE BUTTON --}}
{{-- ============================================ --}}
<button type="button"
        id="aiBubbleBtn"
        aria-label="Open AI Assistant (drag to move)"
        class="ai-bubble-btn"
        style="width: {{ $config->ai_icon_size_px }}px; height: {{ $config->ai_icon_size_px }}px;"
        title="Click to open · Drag to move">

    {!! $config->ai_icon_html !!}
</button>

{{-- ============================================ --}}
{{-- CHAT PANEL --}}
{{-- ============================================ --}}
<div id="aiPanel" class="ai-panel">

    {{-- HEADER --}}
    <div class="ai-panel-header" id="aiPanelHeader">
    {{-- ⭐ DYNAMIC ICON FROM ADMIN SETTINGS --}}
    <div class="ai-panel-avatar">
        {!! $config->ai_icon_html !!}
    </div>

        <div class="ai-panel-title">
            <p class="ai-panel-name">Ai Assistant</p>
            <p class="ai-panel-subtitle">
                <span class="ai-panel-dot"></span>
                Online
            </p>
        </div>

        <button type="button"
                onclick="toggleAIPanel()"
                aria-label="Close"
                class="ai-panel-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- MESSAGES --}}
<div id="aiMessages" class="ai-messages">

    <div class="ai-welcome">
    <div class="ai-welcome-avatar">
        {!! $config->ai_icon_html !!}
    </div>

    <h3 class="ai-welcome-title">Hi! I'm Dash </h3>

    <p class="ai-welcome-text">
        Your <strong>Smart Food Analytics assistant</strong>. I can help you find restaurants, discover menu items, recommend food, and track your orders.
    </p>

    {{-- ⭐ Badge --}}
    <div class="ai-welcome-badge">
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <span>Powered by DashBite AI</span>
    </div>
</div>

    {{-- Suggestions --}}
    <div class="ai-suggestions">
        <p class="ai-suggestions-label">Try asking</p>

        <button type="button" class="ai-suggestion" onclick="sendAIMessage('What restaurants are open right now?')">
            What restaurants are open right now?
        </button>

        <button type="button" class="ai-suggestion" onclick="sendAIMessage('What menu items do you have besides pizza?')">
            What menu items do you have besides pizza?
        </button>        
    </div>
</div>
    {{-- INPUT --}}
    <form id="aiForm" onsubmit="event.preventDefault(); sendAIMessage();" class="ai-input-form">
        <input type="text"
               id="aiInput"
               placeholder="Type your message..."
               maxlength="500"
               autocomplete="off"
               class="ai-input">
        <button type="submit" id="aiSendBtn" aria-label="Send" class="ai-send-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </form>
</div>

{{-- ============================================ --}}
{{-- STYLES --}}
{{-- ============================================ --}}
<style>
    .ai-welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    padding: 4px 10px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    color: #c2410c;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.dark .ai-welcome-badge {
    background: rgba(249, 115, 22, 0.15);
    border-color: rgba(249, 115, 22, 0.3);
    color: #fb923c;
}
    /* ============================================
   ⭐ CENTERED WELCOME SCREEN
   ============================================ */
.ai-welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 24px 16px 20px;
    gap: 12px;
    animation: aiSlideIn 0.35s ease-out;
}

/* Avatar wrapper */
.ai-welcome-avatar {
    width: 170px;
    height: 170px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 4px;
}

.ai-welcome-avatar svg,
.ai-welcome-avatar img {
    width: 80px;
    height: 80px;
    color: white;
    object-fit: contain;
}

/* Welcome title */
.ai-welcome-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin: 0;
    line-height: 1.3;
}

/* Welcome text */
.ai-welcome-text {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    line-height: 1.55;
    max-width: 280px;
}

.ai-welcome-text strong {
    font-weight: 600;
    color: #ea580c;
}

/* Pulse animation for avatar */
@keyframes aiWelcomePulse {
    0%, 100% {
        box-shadow:
            0 8px 24px rgba(249, 115, 22, 0.35),
            0 4px 12px rgba(249, 115, 22, 0.2),
            0 0 0 4px rgba(249, 115, 22, 0.1);
    }
    50% {
        box-shadow:
            0 8px 32px rgba(249, 115, 22, 0.5),
            0 4px 16px rgba(249, 115, 22, 0.3),
            0 0 0 8px rgba(249, 115, 22, 0.05);
    }
}

/* ⭐ Dark mode */
.dark .ai-welcome-title {
    color: #f4f4f5;
}

.dark .ai-welcome-text {
    color: #a1a1aa;
}

.dark .ai-welcome-text strong {
    color: #fb923c;
}

/* ⭐ Suggestions centered below welcome */
.ai-suggestions {
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    width: 100%;
}
/* ============================================
   BUBBLE BUTTON — transparent, clean
   ============================================ */
.ai-bubble-btn {
    position: fixed;
    bottom: 24px;
    right: 24px;
    border-radius: 50%;
    background: transparent;
    box-shadow: none;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    cursor: grab;
    border: none;
    padding: 0;
    transition: transform 0.15s ease;
    color: #f97316;
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
    overflow: visible;
}

.ai-bubble-btn:hover {
    transform: scale(1.05);
}

.ai-bubble-btn.dragging {
    cursor: grabbing;
    transform: scale(1.1);
    transition: none;
}

.ai-bubble-btn:not(.dragging):active {
    transform: scale(0.95);
}

/* ⭐ Icon classes — full size, no inline restrictions */
.ai-bubble-btn .ai-bubble-icon-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    pointer-events: none;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(249, 115, 22, 0.3));
}

.ai-bubble-btn .ai-bubble-icon-svg {
    width: 100% !important;
    height: 100% !important;
    pointer-events: none;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(249, 115, 22, 0.3));
    color: #f97316;
}

.ai-bubble-btn .ai-bubble-icon-wrap {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 100% !important;
    pointer-events: none;
    filter: drop-shadow(0 4px 8px rgba(249, 115, 22, 0.3));
    color: #f97316;
}

.ai-bubble-btn .ai-bubble-icon-wrap svg {
    width: 100% !important;
    height: 100% !important;
    display: block;
}

/* Legacy selectors (backward compat) */
.ai-bubble-btn img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(249, 115, 22, 0.3));
}

.ai-bubble-btn svg {
    width: 100%;
    height: 100%;
    pointer-events: none;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(249, 115, 22, 0.3));
    color: #f97316;
}

.ai-bubble-btn > span {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.ai-bubble-btn > span svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* ============================================
   AI NOTIFICATION POPUP
   ============================================ */
.ai-popup {
    position: fixed;
    bottom: 32px;
    right: 96px;
    z-index: 9998;
    opacity: 0;
    transform: translateX(20px) scale(0.9);
    transform-origin: bottom right;
    pointer-events: none;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.ai-popup.visible {
    opacity: 1;
    transform: translateX(0) scale(1);
    pointer-events: auto;
}

.ai-popup-content {
    background: #ffffff;
    border-radius: 16px;
    box-shadow:
        0 12px 32px rgba(0, 0, 0, 0.12),
        0 4px 12px rgba(0, 0, 0, 0.08),
        0 0 0 1px rgba(0, 0, 0, 0.04);
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    max-width: 300px;
    min-width: 260px;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    position: relative;
}

.ai-popup-content:hover {
    transform: translateY(-2px);
    box-shadow:
        0 16px 40px rgba(0, 0, 0, 0.15),
        0 6px 16px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(0, 0, 0, 0.04);
}

.ai-popup-arrow {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #ffffff;
    transform: rotate(45deg);
    box-shadow: 2px -2px 4px rgba(0, 0, 0, 0.06);
    z-index: -1;
}

.ai-popup-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
}

.ai-popup-icon svg {
    width: 18px;
    height: 18px;
    color: white;
}

.ai-popup-text {
    flex: 1;
    min-width: 0;
    padding-right: 4px;
}

.ai-popup-title {
    font-size: 13px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 3px 0;
    line-height: 1.3;
}

.ai-popup-body {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
    line-height: 1.45;
}

.ai-popup-close {
    background: transparent;
    border: none;
    width: 20px;
    height: 20px;
    padding: 0;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    flex-shrink: 0;
    transition: background 0.15s ease, color 0.15s ease;
    margin-top: -2px;
}

.ai-popup-close:hover {
    background: #f3f4f6;
    color: #4b5563;
}

.ai-popup-close svg {
    width: 12px;
    height: 12px;
}

/* Dark mode — popup */
.dark .ai-popup-content {
    background: #18181b;
    box-shadow:
        0 12px 32px rgba(0, 0, 0, 0.5),
        0 4px 12px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.06);
}

.dark .ai-popup-arrow {
    background: #18181b;
    box-shadow: 2px -2px 4px rgba(0, 0, 0, 0.3);
}

.dark .ai-popup-title {
    color: #f4f4f5;
}

.dark .ai-popup-body {
    color: #a1a1aa;
}

.dark .ai-popup-close {
    color: #71717a;
}

.dark .ai-popup-close:hover {
    background: #27272a;
    color: #d4d4d8;
}

/* ============================================
   PANEL
   ============================================ */
.ai-panel {
    position: fixed;
    bottom: 96px;
    right: 24px;
    width: 380px;
    height: 520px;
    max-width: calc(100vw - 2rem);
    max-height: calc(100vh - 8rem);
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
    z-index: 9998;
    overflow: hidden;
    display: none;
    flex-direction: column;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* ============================================
   HEADER
   ============================================ */
.ai-panel-header {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    cursor: grab;
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
}

.ai-panel-header.dragging {
    cursor: grabbing;
}

.ai-panel-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-panel-avatar svg {
    width: 20px;
    height: 20px;
    color: white;
}

.ai-panel-title {
    flex: 1;
    min-width: 0;
}

.ai-panel-name {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
}

.ai-panel-subtitle {
    font-size: 11px;
    opacity: 0.9;
    margin: 2px 0 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.ai-panel-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
}

.ai-panel-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
    flex-shrink: 0;
    padding: 0;
}

.ai-panel-close:hover {
    background: rgba(255, 255, 255, 0.25);
}

.ai-panel-close svg {
    width: 16px;
    height: 16px;
}

/* ============================================
   MESSAGES AREA
   ============================================ */
.ai-messages {
    flex: 1;
    padding: 16px;
    background: #fafafa;
    overflow-y: auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.ai-message {
    display: flex;
    animation: aiSlideIn 0.25s ease-out;
}

.ai-message-bot {
    justify-content: flex-start;
}

.ai-message-user {
    justify-content: flex-end;
}

.ai-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}

.ai-bubble-bot {
    background: #ffffff;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}

.ai-bubble-user {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
    border-bottom-right-radius: 4px;
}

.ai-bubble-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
    border-bottom-left-radius: 4px;
}

/* ============================================
   MARKDOWN CONTENT
   ============================================ */
.ai-bubble p {
    margin: 0 0 8px 0;
}

.ai-bubble p:last-child {
    margin-bottom: 0;
}

.ai-bubble strong {
    font-weight: 600;
    color: #111827;
}

.dark .ai-bubble strong {
    color: #fafafa;
}

.ai-bubble em {
    font-style: italic;
    opacity: 0.9;
}

.ai-bubble code {
    font-family: 'SF Mono', Monaco, Consolas, monospace;
    font-size: 12px;
    background: #f3f4f6;
    color: #be185d;
    padding: 1px 5px;
    border-radius: 4px;
}

.dark .ai-bubble code {
    background: #3f3f46;
    color: #fbcfe8;
}

/* ============================================
   BULLET LIST
   ============================================ */
.ai-list {
    list-style: none;
    padding: 0;
    margin: 6px 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.ai-list li {
    position: relative;
    padding-left: 16px;
    line-height: 1.5;
}

.ai-list li::before {
    content: '';
    position: absolute;
    left: 4px;
    top: 9px;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #f97316;
    opacity: 0.7;
}

.ai-spacer {
    height: 4px;
}

.ai-price {
    font-weight: 600;
    color: #ea580c;
    background: #fff7ed;
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 13px;
    white-space: nowrap;
}

.dark .ai-price {
    color: #fb923c;
    background: rgba(249, 115, 22, 0.15);
}

/* ============================================
   SUGGESTIONS
   ============================================ */
.ai-suggestions {
    margin-top: 6px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.ai-suggestions-label {
    font-size: 10px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 2px 4px;
}

.ai-suggestion {
    text-align: left;
    font-size: 13px;
    background: #ffffff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 9px 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: inherit;
}

.ai-suggestion:hover {
    background: #fff7ed;
    border-color: #fed7aa;
    color: #c2410c;
}

/* ============================================
   TYPING INDICATOR
   ============================================ */
.ai-typing {
    display: flex;
    gap: 4px;
    padding: 12px 14px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    border-bottom-left-radius: 4px;
    width: fit-content;
}

.ai-typing-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #9ca3af;
    animation: aiTypingBounce 1.4s infinite ease-in-out both;
}

/* ============================================
   INPUT FORM
   ============================================ */
.ai-input-form {
    padding: 12px;
    border-top: 1px solid #e5e7eb;
    background: #ffffff;
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
}

.ai-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 20px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s ease;
    font-family: inherit;
    color: #1f2937;
    background: #ffffff;
}

.ai-input:focus {
    border-color: #f97316;
}

.ai-send-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(249, 115, 22, 0.3);
    transition: transform 0.15s ease, opacity 0.15s ease;
    padding: 0;
}

.ai-send-btn:hover {
    transform: scale(1.05);
}

.ai-send-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

.ai-send-btn svg {
    width: 16px;
    height: 16px;
}

/* ============================================
   ANIMATIONS
   ============================================ */
@keyframes aiSlideIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes aiTypingBounce {
    0%, 80%, 100% {
        transform: scale(0.6);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* ============================================
   SCROLLBAR
   ============================================ */
.ai-messages::-webkit-scrollbar {
    width: 6px;
}

.ai-messages::-webkit-scrollbar-track {
    background: transparent;
}

.ai-messages::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.ai-messages::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* ============================================
   DARK MODE
   ============================================ */
.dark .ai-panel {
    background: #18181b;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
}

.dark .ai-messages {
    background: #0a0a0a;
}

.dark .ai-bubble-bot {
    background: #27272a;
    color: #f4f4f5;
    border-color: #3f3f46;
}

.dark .ai-suggestion {
    background: #27272a;
    color: #d4d4d8;
    border-color: #3f3f46;
}

.dark .ai-suggestion:hover {
    background: #3f3f46;
    border-color: #f97316;
    color: #fdba74;
}

.dark .ai-input-form {
    background: #18181b;
    border-color: #27272a;
}

.dark .ai-input {
    background: #27272a;
    color: #f4f4f5;
    border-color: #3f3f46;
}

.dark .ai-input::placeholder {
    color: #71717a;
}

.dark .ai-typing {
    background: #27272a;
    border-color: #3f3f46;
}

.dark .ai-messages::-webkit-scrollbar-thumb {
    background: #3f3f46;
}

/* ============================================
   RESPONSIVE — MOBILE
   ============================================ */
@media (max-width: 767px) {
    .ai-panel {
        top: 64px !important;
        bottom: 80px !important;
        left: 8px !important;
        right: 8px !important;
        width: auto !important;
        height: auto !important;
        max-width: none !important;
        max-height: none !important;
        border-radius: 14px !important;
        z-index: 10000 !important;
    }

    .ai-messages {
        padding: 12px;
    }

    .ai-popup {
        max-width: calc(100vw - 32px);
    }

    .ai-popup-content {
        max-width: 280px;
        min-width: 240px;
    }
}
</style>

{{-- ============================================ --}}
{{-- SCRIPT --}}
{{-- ============================================ --}}
<script>
(function () {
    'use strict';

    const API_URL = '/ai/chat';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const SUGGESTIONS_SELECTOR = '.ai-suggestions';

    let isSending = false;
    let messageHistory = [];

    // ============================================
    // TOGGLE PANEL
    // ============================================
    window.toggleAIPanel = function () {
        const panel = document.getElementById('aiPanel');
        if (!panel) return;

        const isHidden = panel.style.display === 'none' || panel.style.display === '';
        panel.style.display = isHidden ? 'flex' : 'none';

        if (isHidden) {
            positionPanelNearBubble();
            setTimeout(() => {
                document.getElementById('aiInput')?.focus();
            }, 100);
        }
    };

    // ============================================
    // POSITION PANEL NEAR BUBBLE
    // ============================================
    function positionPanelNearBubble() {
        const panel = document.getElementById('aiPanel');
        const btn = document.getElementById('aiBubbleBtn');
        if (!panel || !btn) return;

        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            panel.style.position = 'fixed';
            panel.style.top = '64px';
            panel.style.bottom = '80px';
            panel.style.left = '8px';
            panel.style.right = '8px';
            panel.style.width = 'auto';
            panel.style.height = 'auto';
            panel.style.maxHeight = 'none';
            panel.style.borderRadius = '14px';
            panel.style.zIndex = '10000';
            return;
        }

        panel.style.zIndex = '9998';
        panel.style.bottom = 'auto';
        panel.style.top = 'auto';
        panel.style.left = 'auto';
        panel.style.right = '24px';

        try {
            const saved = localStorage.getItem('fooddash_ai_panel_pos');
            if (saved) {
                const pos = JSON.parse(saved);
                const maxLeft = window.innerWidth - panel.offsetWidth - 8;
                const maxTop = window.innerHeight - panel.offsetHeight - 8;
                panel.style.left = Math.max(8, Math.min(pos.left, maxLeft)) + 'px';
                panel.style.top = Math.max(8, Math.min(pos.top, maxTop)) + 'px';
                panel.style.right = 'auto';
                panel.style.bottom = 'auto';
                return;
            }
        } catch (e) { /* ignore */ }

        const btnRect = btn.getBoundingClientRect();
        const panelWidth = 380;
        const panelHeight = 520;
        const gap = 16;
        const margin = 16;

        let left = btnRect.left + (btnRect.width / 2) - (panelWidth / 2);
        let top = btnRect.top - panelHeight - gap;

        if (top < margin) {
            top = btnRect.bottom + gap;
        }

        left = Math.max(margin, Math.min(left, window.innerWidth - panelWidth - margin));
        top = Math.max(margin, Math.min(top, window.innerHeight - panelHeight - margin));

        panel.style.left = left + 'px';
        panel.style.right = 'auto';
        panel.style.top = top + 'px';
        panel.style.bottom = 'auto';
        panel.style.width = panelWidth + 'px';
        panel.style.height = panelHeight + 'px';
    }

    // ============================================
    // SEND MESSAGE
    // ============================================
    window.sendAIMessage = async function (presetText) {
        if (isSending) return;

        const input = document.getElementById('aiInput');
        const sendBtn = document.getElementById('aiSendBtn');
        const messages = document.getElementById('aiMessages');
        if (!input || !messages) return;

        const text = (presetText ?? input.value).trim();
        if (!text) return;

        isSending = true;
        input.value = '';
        if (sendBtn) sendBtn.disabled = true;

        document.querySelector(SUGGESTIONS_SELECTOR)?.remove();

        appendMessage(text, 'user');
        const typingId = appendTyping();

        const history = messageHistory.slice(-6);

        try {
            const res = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify({
                    message: text,
                    history: history,
                }),
            });

            const data = await res.json();
            removeTyping(typingId);

            if (res.status === 401 || res.status === 419) {
                appendMessage('You need to log in to use the AI Assistant.', 'assistant', true);
            } else if (res.status === 403 || data.customer_only) {
                appendMessage('The AI Assistant is available to customers only.', 'assistant', true);
            } else if (data.ok && data.reply) {
                appendMessage(data.reply, 'assistant');
                messageHistory.push({ role: 'user', content: text });
                messageHistory.push({ role: 'assistant', content: data.reply });
            } else if (data.limit_reached) {
                appendMessage(data.message || 'You have reached your daily limit. Try again tomorrow.', 'assistant', true);
            } else {
                appendMessage(data.message || 'Sorry, there was an error. Please try again.', 'assistant', true);
            }
        } catch (err) {
            console.error('AI chat error:', err);
            removeTyping(typingId);
            appendMessage('Network error. Please try again.', 'assistant', true);
        }

        isSending = false;
        if (sendBtn) sendBtn.disabled = false;
        input.focus();
    };

    // ============================================
    // APPEND MESSAGE
    // ============================================
    function appendMessage(text, role, isError) {
        const container = document.getElementById('aiMessages');
        if (!container) return;

        const isUser = role === 'user';

        const wrapper = document.createElement('div');
        wrapper.className = 'ai-message ' + (isUser ? 'ai-message-user' : 'ai-message-bot');

        const bubble = document.createElement('div');
        bubble.className = 'ai-bubble ' + (
            isUser
                ? 'ai-bubble-user'
                : (isError ? 'ai-bubble-error' : 'ai-bubble-bot')
        );

        if (isUser) {
            const p = document.createElement('p');
            p.textContent = text;
            bubble.appendChild(p);
        } else {
            bubble.innerHTML = renderMarkdown(text);
        }

        wrapper.appendChild(bubble);
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    // ============================================
    // MARKDOWN RENDERER
    // ============================================
    function renderMarkdown(text) {
        if (!text) return '';

        let html = text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

        const lines = html.split('\n');
        const output = [];
        let inList = false;

        for (let line of lines) {
            const trimmed = line.trim();
            const listMatch = trimmed.match(/^[-*•]\s+(.+)$/);

            if (listMatch) {
                if (!inList) {
                    output.push('<ul class="ai-list">');
                    inList = true;
                }
                output.push(`<li>${formatInline(listMatch[1])}</li>`);
                continue;
            }

            if (inList) {
                output.push('</ul>');
                inList = false;
            }

            if (trimmed === '') {
                output.push('<div class="ai-spacer"></div>');
                continue;
            }

            output.push(`<p>${formatInline(trimmed)}</p>`);
        }

        if (inList) output.push('</ul>');

        return output.join('');
    }

    function formatInline(text) {
        let result = text;

        result = result.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        result = result.replace(/(?<!\*)\*([^*]+?)\*(?!\*)/g, '<em>$1</em>');
        result = result.replace(/`([^`]+)`/g, '<code>$1</code>');
        result = result.replace(/(₱[\d,]+(?:\.\d{2})?)/g, '<span class="ai-price">$1</span>');

        return result;
    }

    // ============================================
    // TYPING INDICATOR
    // ============================================
    function appendTyping() {
        const container = document.getElementById('aiMessages');
        if (!container) return null;

        const id = 'ai-typing-' + Date.now();

        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = 'ai-message ai-message-bot';

        const typing = document.createElement('div');
        typing.className = 'ai-typing';
        typing.innerHTML = `
            <span class="ai-typing-dot"></span>
            <span class="ai-typing-dot" style="animation-delay:0.15s;"></span>
            <span class="ai-typing-dot" style="animation-delay:0.3s;"></span>
        `;

        wrapper.appendChild(typing);
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;

        return id;
    }

    function removeTyping(id) {
        if (!id) return;
        document.getElementById(id)?.remove();
    }

    // ============================================
    // AI NOTIFICATION POPUP
    // ============================================
    (function initAiPopup() {
        const popup = document.getElementById('aiPopup');
        const bubble = document.getElementById('aiBubbleBtn');
        if (!popup || !bubble) return;

        const STORAGE_KEY = 'fooddash_ai_popup_dismissed';
        const SHOW_DELAY = 3000;
        const AUTO_HIDE_DELAY = 20000;

        let autoHideTimer = null;
        let arrow = null;

        // Create arrow element
        arrow = document.createElement('div');
        arrow.className = 'ai-popup-arrow';
        popup.appendChild(arrow);

        if (localStorage.getItem(STORAGE_KEY) === 'yes') {
            return;
        }

        const showTimer = setTimeout(() => {
            popup.classList.add('visible');

            autoHideTimer = setTimeout(() => {
                hidePopup(false);
            }, AUTO_HIDE_DELAY);
        }, SHOW_DELAY);

        function positionPopup() {
            if (!popup || !bubble) return;

            const bubbleRect = bubble.getBoundingClientRect();
            const popupRect = popup.getBoundingClientRect();
            const arrowSize = 14;

            const spaceLeft = bubbleRect.left;

            if (spaceLeft > popupRect.width + 30) {
                // Sa kaliwa ng bubble
                popup.style.right = (window.innerWidth - bubbleRect.left + 12) + 'px';
                popup.style.left = 'auto';
                popup.style.bottom = (window.innerHeight - bubbleRect.bottom + 8) + 'px';
                popup.style.top = 'auto';

                const arrowTop = bubbleRect.top + (bubbleRect.height / 2) - popupRect.top - (arrowSize / 2);

                arrow.style.right = '-' + (arrowSize / 2) + 'px';
                arrow.style.left = 'auto';
                arrow.style.top = arrowTop + 'px';
                arrow.style.bottom = 'auto';
                arrow.style.transform = 'rotate(-45deg)';
            } else {
                // Sa itaas ng bubble
                popup.style.right = (window.innerWidth - bubbleRect.right) + 'px';
                popup.style.left = 'auto';
                popup.style.bottom = (window.innerHeight - bubbleRect.top + 12) + 'px';
                popup.style.top = 'auto';

                const arrowLeft = bubbleRect.left + (bubbleRect.width / 2) - popupRect.left - (arrowSize / 2);

                arrow.style.left = arrowLeft + 'px';
                arrow.style.right = 'auto';
                arrow.style.bottom = '-' + (arrowSize / 2) + 'px';
                arrow.style.top = 'auto';
                arrow.style.transform = 'rotate(45deg)';
            }
        }

        const observer = new MutationObserver(() => {
            if (popup.classList.contains('visible')) {
                positionPopup();
            }
        });
        observer.observe(popup, { attributes: true, attributeFilter: ['class'] });

        window.addEventListener('resize', () => {
            if (popup.classList.contains('visible')) {
                positionPopup();
            }
        });

        popup.addEventListener('click', (e) => {
            if (e.target.closest('.ai-popup-close')) return;
            hidePopup(true);
            window.toggleAIPanel();
        });

        window.dismissAiPopup = function (e) {
            if (e) {
                e.stopPropagation();
                e.preventDefault();
            }
            hidePopup(true);
        };

        function hidePopup(userDismissed) {
            clearTimeout(autoHideTimer);
            popup.classList.remove('visible');

            if (userDismissed) {
                try {
                    localStorage.setItem(STORAGE_KEY, 'yes');
                } catch (e) { /* ignore */ }
            }
        }

        positionPopup();
    })();

    // ============================================
    // DRAGGABLE BUBBLE
    // ============================================
    (function initDraggableBubble() {
        const btn = document.getElementById('aiBubbleBtn');
        if (!btn) return;

        const STORAGE_KEY = 'fooddash_ai_bubble_pos';
        const DRAG_THRESHOLD = 5;
        const MARGIN = 8;
        const MOBILE_BOTTOM_OFFSET = 88;

        let isDragging = false;
        let hasMoved = false;
        let startX = 0;
        let startY = 0;
        let startLeft = 0;
        let startTop = 0;

        function restorePosition() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const pos = JSON.parse(saved);
                    const isMobile = window.innerWidth < 768;
                    const maxLeft = window.innerWidth - btn.offsetWidth - MARGIN;
                    const maxTop = window.innerHeight - btn.offsetHeight - MARGIN;

                    let left = Math.max(MARGIN, Math.min(pos.left, maxLeft));
                    let top = Math.max(MARGIN, Math.min(pos.top, maxTop));

                    if (isMobile && top > window.innerHeight - btn.offsetHeight - MOBILE_BOTTOM_OFFSET) {
                        top = window.innerHeight - btn.offsetHeight - MOBILE_BOTTOM_OFFSET;
                    }

                    btn.style.left = left + 'px';
                    btn.style.top = top + 'px';
                    btn.style.right = 'auto';
                    btn.style.bottom = 'auto';
                    return;
                }
            } catch (e) { /* ignore */ }

            const isMobile = window.innerWidth < 768;
            btn.style.left = (window.innerWidth - btn.offsetWidth - 24) + 'px';
            btn.style.top = (window.innerHeight - btn.offsetHeight - (isMobile ? MOBILE_BOTTOM_OFFSET : 24)) + 'px';
            btn.style.right = 'auto';
            btn.style.bottom = 'auto';
        }

        function savePosition(left, top) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({ left, top }));
            } catch (e) { /* ignore */ }
        }

        function getCurrentPos() {
            const rect = btn.getBoundingClientRect();
            return { left: rect.left, top: rect.top };
        }

        function clamp(left, top) {
            const maxLeft = window.innerWidth - btn.offsetWidth - MARGIN;
            const maxTop = window.innerHeight - btn.offsetHeight - MARGIN;
            return {
                left: Math.max(MARGIN, Math.min(left, maxLeft)),
                top: Math.max(MARGIN, Math.min(top, maxTop)),
            };
        }

        function startDrag(clientX, clientY) {
            isDragging = true;
            hasMoved = false;
            const pos = getCurrentPos();
            startX = clientX;
            startY = clientY;
            startLeft = pos.left;
            startTop = pos.top;
            btn.classList.add('dragging');
        }

        function moveDrag(clientX, clientY) {
            if (!isDragging) return;

            const dx = clientX - startX;
            const dy = clientY - startY;

            if (!hasMoved && Math.sqrt(dx * dx + dy * dy) > DRAG_THRESHOLD) {
                hasMoved = true;
            }

            const newPos = clamp(startLeft + dx, startTop + dy);
            btn.style.left = newPos.left + 'px';
            btn.style.top = newPos.top + 'px';
        }

        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            btn.classList.remove('dragging');

            if (hasMoved) {
                const pos = getCurrentPos();
                savePosition(pos.left, pos.top);
            } else {
                window.toggleAIPanel();
            }
        }

        btn.addEventListener('mousedown', function (e) {
            if (e.button !== 0) return;
            e.preventDefault();
            startDrag(e.clientX, e.clientY);

            const onMove = (ev) => moveDrag(ev.clientX, ev.clientY);
            const onUp = () => {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                endDrag();
            };

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });

        btn.addEventListener('touchstart', function (e) {
            const touch = e.touches[0];
            if (!touch) return;
            startDrag(touch.clientX, touch.clientY);

            const onMove = (ev) => {
                const t = ev.touches[0];
                if (!t) return;
                ev.preventDefault();
                moveDrag(t.clientX, t.clientY);
            };
            const onUp = () => {
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onUp);
                document.removeEventListener('touchcancel', onUp);
                endDrag();
            };

            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
            document.addEventListener('touchcancel', onUp);
        }, { passive: false });

        btn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.toggleAIPanel();
            }
        });

        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const pos = getCurrentPos();
                const clamped = clamp(pos.left, pos.top);
                btn.style.left = clamped.left + 'px';
                btn.style.top = clamped.top + 'px';
                savePosition(clamped.left, clamped.top);

                const panel = document.getElementById('aiPanel');
                if (panel && panel.style.display === 'flex') {
                    positionPanelNearBubble();
                }
            }, 150);
        });

        restorePosition();
    })();

    // ============================================
    // DRAGGABLE PANEL
    // ============================================
    (function initDraggablePanel() {
        const header = document.getElementById('aiPanelHeader');
        const panel = document.getElementById('aiPanel');
        if (!header || !panel) return;

        const STORAGE_KEY = 'fooddash_ai_panel_pos';
        const DRAG_THRESHOLD = 5;
        const MARGIN = 8;

        let isDragging = false;
        let hasMoved = false;
        let startX = 0;
        let startY = 0;
        let startLeft = 0;
        let startTop = 0;

        function getCurrentPos() {
            const rect = panel.getBoundingClientRect();
            return { left: rect.left, top: rect.top };
        }

        function clamp(left, top) {
            const maxLeft = window.innerWidth - panel.offsetWidth - MARGIN;
            const maxTop = window.innerHeight - panel.offsetHeight - MARGIN;
            return {
                left: Math.max(MARGIN, Math.min(left, maxLeft)),
                top: Math.max(MARGIN, Math.min(top, maxTop)),
            };
        }

        function savePosition(left, top) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({ left, top }));
            } catch (e) { /* ignore */ }
        }

        function startDrag(clientX, clientY) {
            isDragging = true;
            hasMoved = false;
            const pos = getCurrentPos();
            startX = clientX;
            startY = clientY;
            startLeft = pos.left;
            startTop = pos.top;
            panel.style.left = pos.left + 'px';
            panel.style.top = pos.top + 'px';
            panel.style.right = 'auto';
            panel.style.bottom = 'auto';
            header.classList.add('dragging');
        }

        function moveDrag(clientX, clientY) {
            if (!isDragging) return;

            const dx = clientX - startX;
            const dy = clientY - startY;

            if (!hasMoved && Math.sqrt(dx * dx + dy * dy) > DRAG_THRESHOLD) {
                hasMoved = true;
            }

            const newPos = clamp(startLeft + dx, startTop + dy);
            panel.style.left = newPos.left + 'px';
            panel.style.top = newPos.top + 'px';
        }

        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            header.classList.remove('dragging');

            if (hasMoved) {
                const pos = getCurrentPos();
                savePosition(pos.left, pos.top);
            }
        }

        header.addEventListener('mousedown', function (e) {
            if (e.target.closest('.ai-panel-close')) return;
            if (e.button !== 0) return;
            e.preventDefault();
            startDrag(e.clientX, e.clientY);

            const onMove = (ev) => moveDrag(ev.clientX, ev.clientY);
            const onUp = () => {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                endDrag();
            };

            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });

        header.addEventListener('touchstart', function (e) {
            if (e.target.closest('.ai-panel-close')) return;
            const touch = e.touches[0];
            if (!touch) return;
            startDrag(touch.clientX, touch.clientY);

            const onMove = (ev) => {
                const t = ev.touches[0];
                if (!t) return;
                ev.preventDefault();
                moveDrag(t.clientX, t.clientY);
            };
            const onUp = () => {
                document.removeEventListener('touchmove', onMove);
                document.removeEventListener('touchend', onUp);
                document.removeEventListener('touchcancel', onUp);
                endDrag();
            };

            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
            document.addEventListener('touchcancel', onUp);
        }, { passive: false });

        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (panel.style.display !== 'flex') return;
                const pos = getCurrentPos();
                const clamped = clamp(pos.left, pos.top);
                panel.style.left = clamped.left + 'px';
                panel.style.top = clamped.top + 'px';
            }, 150);
        });
    })();

    // ============================================
    // ESC TO CLOSE
    // ============================================
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const panel = document.getElementById('aiPanel');
            if (panel && panel.style.display === 'flex') {
                window.toggleAIPanel();
            }
        }
    });
})();
</script>