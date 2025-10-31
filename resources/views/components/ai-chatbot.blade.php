<!-- AI Chatbot Floating Button -->
<div id="ai-chatbot-container" class="ai-chatbot-container">
    <!-- Chat Window -->
    <div id="chat-window" class="ai-chat-window ai-chat-hidden">
        <!-- Chat Header -->
        <div class="ai-chat-header">
            <div class="ai-chat-header-content">
                <div class="ai-chat-header-info">
                    <div class="ai-chat-avatar">
                        <svg class="ai-chat-avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="ai-chat-header-text">
                        <h3 class="ai-chat-title">مساعد الذكاء الاصطناعي</h3>
                        <p class="ai-chat-subtitle">متاح الآن للمساعدة</p>
                    </div>
                </div>
                <button id="close-chat" class="ai-chat-close-btn">
                    <svg class="ai-chat-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="ai-chat-messages">
            <!-- Welcome Message -->
            <div class="ai-message ai-message-bot">
                <div class="ai-message-avatar">
                    <svg class="ai-message-avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="ai-message-content">
                    <p class="ai-message-text">
                        مرحباً! أنا مساعدك الذكي 🤖<br>
                        يمكنني مساعدتك في:
                    </p>
                    <ul class="ai-message-list">
                        <li>• البحث عن المنتجات</li>
                        <li>• إضافة منتجات للسلة</li>
                        <li>• الإجابة على استفساراتك</li>
                        <li>• مساعدتك في عملية الشراء</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div id="typing-indicator" class="ai-typing-indicator ai-chat-hidden">
            <div class="ai-message ai-message-bot">
                <div class="ai-message-avatar">
                    <svg class="ai-message-avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="ai-message-content">
                    <div class="ai-typing-dots">
                        <div class="ai-typing-dot"></div>
                        <div class="ai-typing-dot"></div>
                        <div class="ai-typing-dot"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="ai-chat-input-container">
            <div class="ai-chat-input-wrapper">
                <input
                    type="text"
                    id="chat-input"
                    placeholder="اكتب رسالتك هنا..."
                    class="ai-chat-input"
                    autocomplete="off"
                >
                <button id="send-message" class="ai-send-btn">
                    <svg class="ai-send-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="ai-quick-actions">
        <button class="ai-quick-action" data-action="search-products">
            <svg class="ai-quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span>البحث عن المنتجات</span>
        </button>
        <button class="ai-quick-action" data-action="view-cart">
            <svg class="ai-quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            <span>عرض السلة</span>
        </button>
        <button class="ai-quick-action" data-action="help">
            <svg class="ai-quick-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>مساعدة</span>
        </button>
    </div>

    <!-- Floating Button -->
    <button id="chat-toggle" class="ai-floating-btn">
        <svg id="chat-icon" class="ai-floating-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <svg id="close-icon" class="ai-floating-icon ai-chat-hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>

        <!-- Notification Badge -->
        <div id="notification-badge" class="ai-notification-badge ai-chat-hidden">
            <span id="notification-count">1</span>
        </div>

        <!-- Pulse Animation -->
        <div class="ai-pulse-effect"></div>
    </button>
</div>

<style>
/* AI Chatbot Custom Styles - FIXED VERSION */
.ai-chatbot-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10;
    font-family: 'Cairo', sans-serif;
    pointer-events: none;
}

.ai-chatbot-container > * {
    pointer-events: auto;
}

/* Chat Window */
.ai-chat-window {
    width: 400px;
    height: 500px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* Chat Header */
.ai-chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    color: white;
}

.ai-chat-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ai-chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ai-chat-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-chat-avatar-icon {
    width: 24px;
    height: 24px;
    color: white;
}

.ai-chat-title {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
}

.ai-chat-subtitle {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
}

.ai-chat-close-btn {
    background: none;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.ai-chat-close-btn:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.ai-chat-close-icon {
    width: 24px;
    height: 24px;
}

/* Chat Messages */
.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ai-message {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.ai-message-user {
    flex-direction: row-reverse;
}

.ai-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.ai-message-bot .ai-message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.ai-message-user .ai-message-avatar {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.ai-message-avatar-icon {
    width: 16px;
    height: 16px;
    color: white;
}

.ai-message-content {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
}

.ai-message-bot .ai-message-content {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 18px 18px 18px 4px;
}

.ai-message-user .ai-message-content {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border-radius: 18px 18px 4px 18px;
}

.ai-message-text {
    color: white;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
}

.ai-message-list {
    color: rgba(255, 255, 255, 0.9);
    font-size: 12px;
    margin: 8px 0 0 0;
    padding: 0;
    list-style: none;
}

.ai-message-list li {
    margin: 4px 0;
}

/* Typing Indicator */
.ai-typing-indicator {
    padding: 0 20px 20px;
}

.ai-typing-dots {
    display: flex;
    gap: 4px;
    align-items: center;
}

.ai-typing-dot {
    width: 8px;
    height: 8px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    animation: ai-typing 1.5s infinite;
}

.ai-typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.ai-typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes ai-typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Chat Input */
.ai-chat-input-container {
    padding: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.ai-chat-input-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.ai-chat-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px;
    padding: 12px 20px;
    color: white;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
}

.ai-chat-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.ai-chat-input:focus {
    border-color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0.15);
}

.ai-send-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.ai-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.ai-send-icon {
    width: 20px;
    height: 20px;
    color: white;
}

/* Quick Actions */
.ai-quick-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.3s ease;
}

.ai-chatbot-container:hover .ai-quick-actions {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.ai-quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 12px 16px;
    color: white;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: right;
    min-width: 200px;
}

.ai-quick-action:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateX(-5px);
}

.ai-quick-action-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.ai-quick-action span {
    font-weight: 500;
}

/* Floating Button */
.ai-floating-btn {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    position: relative;
    animation: ai-float 3s ease-in-out infinite;
}

.ai-floating-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    animation: none;
}

.ai-floating-icon {
    width: 32px;
    height: 32px;
    transition: transform 0.3s ease;
}

.ai-notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.ai-pulse-effect {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    animation: ai-pulse 2s infinite;
    opacity: 0.2;
}

@keyframes ai-float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes ai-pulse {
    0% {
        transform: scale(1);
        opacity: 0.2;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.1;
    }
    100% {
        transform: scale(1);
        opacity: 0.2;
    }
}

/* Responsive Design - FIXED */
@media (max-width: 768px) {
    .ai-chatbot-container {
        bottom: 16px;
        right: 16px;
        left: auto;
    }

    .ai-chat-window {
        width: calc(100vw - 32px);
        max-width: 400px;
        height: 400px;
    }

    .ai-floating-btn {
        width: 56px;
        height: 56px;
    }

    .ai-floating-icon {
        width: 28px;
        height: 28px;
    }

    .ai-quick-actions {
        margin-bottom: 12px;
    }

    .ai-quick-action {
        min-width: 180px;
        padding: 10px 14px;
        font-size: 13px;
    }

    .ai-quick-action-icon {
        width: 18px;
        height: 18px;
    }
}

/* Utility Classes */
.ai-chat-hidden {
    display: none !important;
}

/* Message Animation */
.ai-message {
    animation: ai-slideInUp 0.3s ease-out;
}

@keyframes ai-slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
