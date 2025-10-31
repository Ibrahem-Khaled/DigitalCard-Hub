class AIChatbot {
    constructor() {
        this.isOpen = false;
        this.sessionId = this.generateSessionId();
        this.init();
    }

    init() {
        this.bindEvents();
        this.showWelcomeMessage();
    }

    bindEvents() {
        const toggleBtn = document.getElementById('chat-toggle');
        const closeBtn = document.getElementById('close-chat');
        const sendBtn = document.getElementById('send-message');
        const chatInput = document.getElementById('chat-input');
        const quickActions = document.querySelectorAll('.ai-quick-action');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleChat());
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeChat());
        }

        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }

        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });
        }

        quickActions.forEach(action => {
            action.addEventListener('click', (e) => {
                const actionType = e.target.dataset.action;
                this.handleQuickAction(actionType);
            });
        });
    }

    generateSessionId() {
        return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }

    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }

    openChat() {
        const chatWindow = document.getElementById('chat-window');
        const chatIcon = document.getElementById('chat-icon');
        const closeIcon = document.getElementById('close-icon');

        if (chatWindow) {
            chatWindow.classList.remove('ai-chat-hidden');
            this.isOpen = true;
        }

        if (chatIcon) chatIcon.classList.add('ai-chat-hidden');
        if (closeIcon) closeIcon.classList.remove('ai-chat-hidden');
    }

    closeChat() {
        const chatWindow = document.getElementById('chat-window');
        const chatIcon = document.getElementById('chat-icon');
        const closeIcon = document.getElementById('close-icon');

        if (chatWindow) {
            chatWindow.classList.add('ai-chat-hidden');
            this.isOpen = false;
        }

        if (chatIcon) chatIcon.classList.remove('ai-chat-hidden');
        if (closeIcon) closeIcon.classList.add('ai-chat-hidden');
    }

    showWelcomeMessage() {
        // Welcome message is already in HTML
    }

    async sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (!message) return;

        // Add user message to chat
        this.addMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        try {
            const response = await this.callAI(message);
            this.hideTypingIndicator();
            this.addMessage(response.message, 'bot');

            // Handle actions
            if (response.action) {
                this.handleAction(response.action, response);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('عذراً، حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.', 'bot');
            console.error('AI Chat Error:', error);
        }
    }

    async callAI(message) {
        const response = await fetch('/api/ai-chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                session_id: this.sessionId,
                context: this.getContext()
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return await response.json();
    }

    getContext() {
        return {
            current_page: window.location.pathname,
            user_agent: navigator.userAgent,
            timestamp: new Date().toISOString()
        };
    }

    addMessage(content, sender) {
        const messagesContainer = document.getElementById('chat-messages');
        if (!messagesContainer) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-message ai-message-${sender}`;

        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'ai-message-avatar';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'ai-message-content';

        const textDiv = document.createElement('p');
        textDiv.className = 'ai-message-text';
        textDiv.textContent = content;

        contentDiv.appendChild(textDiv);
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    showTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.classList.remove('ai-chat-hidden');
        }
    }

    hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.classList.add('ai-chat-hidden');
        }
    }

    handleQuickAction(action) {
        let message = '';
        switch (action) {
            case 'search-products':
                message = 'أريد البحث عن منتجات';
                break;
            case 'view-cart':
                message = 'أريد عرض السلة';
                break;
            case 'help':
                message = 'أحتاج مساعدة';
                break;
            default:
                return;
        }

        const input = document.getElementById('chat-input');
        if (input) {
            input.value = message;
            this.sendMessage();
        }
    }

    handleAction(action, response) {
        switch (action) {
            case 'add_to_cart':
                // Handle add to cart action
                break;
            case 'redirect':
                // Handle redirect action
                break;
            default:
                break;
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new AIChatbot();
});
