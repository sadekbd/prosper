(function () {
    // 1. Load All Dependencies Dynamically if they don't exist
    const dependencies = [
        { type: 'script', url: 'https://cdn.tailwindcss.com' },
        { type: 'script', url: 'https://cdn.jsdelivr.net/npm/marked/marked.min.js' },
        { type: 'link', url: 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap' },
        { type: 'link', url: 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' }
    ];

    dependencies.forEach(dep => {
        if (dep.type === 'script') {
            if (!document.querySelector(`script[src="${dep.url}"]`)) {
                const script = document.createElement('script');
                script.src = dep.url;
                document.head.appendChild(script);
            }
        } else if (dep.type === 'link') {
            if (!document.querySelector(`link[href="${dep.url}"]`)) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = dep.url;
                document.head.appendChild(link);
            }
        }
    });

    // 2. Inject Custom Scrollbar and Markdown Styles
    const style = document.createElement('style');
    style.textContent = `
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .chat-content-box p { margin-bottom: 0.6rem; line-height: 1.5; }
        .chat-content-box p:last-child { margin-bottom: 0; }
        .chat-content-box strong { font-weight: 700; color: #0f172a; }
        .chat-content-box ul { list-style-type: disc !important; margin-left: 1.25rem !important; margin-top: 0.4rem !important; margin-bottom: 0.4rem !important; padding-left: 0 !important; }
        .chat-content-box ol { list-style-type: decimal !important; margin-left: 1.25rem !important; margin-top: 0.4rem !important; margin-bottom: 0.4rem !important; padding-left: 0 !important; }
        .chat-content-box li { margin-bottom: 0.25rem; display: list-item !important; }
        .chat-content-box h1 { font-size: 1.4rem; font-weight: 700; color: #4338ca; margin-top: 1rem; margin-bottom: 0.5rem; }
        .chat-content-box h2 { font-size: 1.25rem; font-weight: 700; color: #4338ca; margin-top: 0.9rem; margin-bottom: 0.4rem; }
        .chat-content-box h3 { font-size: 1.05rem; font-weight: 700; color: #4f46e5; margin-top: 0.8rem; margin-bottom: 0.3rem; }
        .chat-content-box hr { border-color: #e2e8f0; margin: 0.75rem 0; }
    `;
    document.head.appendChild(style);

    // 3. Inject Widget HTML Elements Into Body
    const widgetContainer = document.createElement('div');
    widgetContainer.className = "fixed bottom-6 right-6 z-50 flex flex-col items-end";
    widgetContainer.innerHTML = `
    <div id="chat-window" class="hidden w-[92vw] sm:w-[420px] h-[580px] max-h-[calc(100dvh-7rem)] bg-white rounded-2xl border border-slate-200 shadow-2xl flex flex-col mb-4 overflow-hidden transition-all duration-200">
        <div class="bg-indigo-600 p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white font-bold"><i class="fa-solid fa-robot"></i></div>
                <div>
                    <h4 class="text-white font-bold text-sm leading-tight">Prosper Media AI</h4>
                    <span class="text-[11px] text-indigo-100 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> Live Support</span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button id="reset-chat-btn" title="Reset Session" class="text-indigo-200 hover:text-white transition p-1 text-sm"><i class="fa-solid fa-rotate-right"></i></button>
                <button id="close-chat-btn" class="text-indigo-200 hover:text-white transition p-1 text-lg"><i class="fa-solid fa-xmark"></i></button>
            </div>
        </div>

            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto custom-scrollbar space-y-4 bg-slate-50/50">
                <div class="flex items-start space-x-2.5 max-w-[85%]">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shrink-0 mt-0.5">AI</div>
                    <div class="bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs leading-relaxed chat-content-box">
                        Hello! Welcome to Prosper Media. How can I help you scale your business today?
                    </div>
                </div>
            </div>

            <div id="typing-indicator" class="hidden items-center space-x-2 px-4 py-2 bg-slate-50/50 text-slate-400 text-xs font-medium">
                <div class="flex space-x-1 py-1 px-2 bg-white rounded-lg border border-slate-100 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
                <span>Thinking...</span>
            </div>

            <form id="chat-form" class="p-3 border-t border-slate-100 bg-white flex items-center space-x-2">
                <input id="user-input" type="text" autocomplete="off" placeholder="Ask about Google Ads, Pixel tracking..." class="flex-1 bg-slate-50 text-slate-800 placeholder-slate-400 border border-slate-200 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition">
                <button id="send-btn" type="submit" class="w-9 h-9 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl flex items-center justify-center text-sm shadow-md transition shrink-0">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <button id="toggle-chat-bubble" class="w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-xl shadow-indigo-200 transition-all duration-300 transform hover:scale-105 active:scale-95 group">
            <i class="fa-solid fa-comments text-xl group-hover:rotate-6 transition"></i>
        </button>
    `;
    document.body.appendChild(widgetContainer);

    // 4. Widget Constants & Logic
    const N8N_WEBHOOK_URL = "https://n8n.srv1106977.hstgr.cloud/webhook/705cc3e9-1972-47cf-832f-951868b0ec99/chat";

    function generateSessionId() {
        let session = localStorage.getItem('chat_session_id');
        if (!session) {
            session = 'session_' + Math.random().toString(36).substring(2, 11) + '_' + Date.now();
            localStorage.setItem('chat_session_id', session);
        }
        return session;
    }

    let currentSessionId = generateSessionId();

    const chatWindow = document.getElementById('chat-window');
    const chatMessagesContainer = document.getElementById('chat-messages');
    const userInputField = document.getElementById('user-input');
    const typingIndicator = document.getElementById('typing-indicator');
    const sendButton = document.getElementById('send-btn');
    const chatForm = document.getElementById('chat-form');

    // Make global for external elements (like header button) to trigger
    window.toggleChat = function() {
        if (chatWindow.classList.contains('hidden')) {
            chatWindow.classList.remove('hidden');
            userInputField.focus();
            scrollToBottom();
        } else {
            chatWindow.classList.add('hidden');
        }
    };

    function scrollToBottom() {
        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
    }

    function showLoadingIndicator() {
        typingIndicator.classList.remove('hidden');
        typingIndicator.classList.add('flex');
        sendButton.disabled = true;
        sendButton.classList.add('opacity-60', 'cursor-not-allowed');
        scrollToBottom();
    }

    function hideLoadingIndicator() {
        typingIndicator.classList.add('hidden');
        typingIndicator.classList.remove('flex');
        sendButton.disabled = false;
        sendButton.classList.remove('opacity-60', 'cursor-not-allowed');
    }

    function formatMessageText(rawInput) {
        if (!rawInput) return "";
        let targetString = rawInput;

        if (typeof rawInput === 'object') {
            targetString = rawInput["aiResponse"] || rawInput["output"] || rawInput["AI Response"] || rawInput["text"] || JSON.stringify(rawInput);
        }

        if (typeof targetString === 'string') {
            let trimmed = targetString.trim();
            if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
                try {
                    if (trimmed.startsWith('[')) {
                        const arr = JSON.parse(trimmed);
                        trimmed = typeof arr[0] === 'object' ? JSON.stringify(arr[0]) : arr[0];
                    }
                    const obj = JSON.parse(trimmed);
                    targetString = obj["aiResponse"] || obj["output"] || obj["AI Response"] || obj["text"] || trimmed;
                } catch (e) {}
            }
        }

        if (typeof targetString === 'string') {
            targetString = targetString.replace(/\\n/g, '\n').replace(/\\"/g, '"');
        }

        if (typeof marked !== "undefined") {
            return marked.parse(targetString);
        }
        return targetString;
    }

    function resetChat() {
        if (confirm("Are you sure you want to clear chat history? This will create a new session.")) {
            localStorage.removeItem('chat_session_id');
            currentSessionId = generateSessionId();
            chatMessagesContainer.innerHTML = `
                <div class="flex items-start space-x-2.5 max-w-[85%]">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shrink-0 mt-0.5">AI</div>
                    <div class="bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs leading-relaxed chat-content-box">
                        Conversation restarted! How can I help you today?
                    </div>
                </div>
            `;
            scrollToBottom();
        }
    }

    async function sendMessage(event) {
        event.preventDefault();
        const userMessage = userInputField.value.trim();
        if (!userMessage) return;

        appendMessage(userMessage, 'user');
        userInputField.value = "";
        userInputField.focus();

        await sendMessageToBot(userMessage);
    }

    async function sendMessageToBot(userMessage) {
        showLoadingIndicator();
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 40000);

        try {
            const response = await fetch(N8N_WEBHOOK_URL, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    chatInput: userMessage,
                    sessionId: currentSessionId
                }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            if (!response.ok) throw new Error("Backend server error");

            const data = await response.json();
            hideLoadingIndicator();

            appendMessage(
                data.aiResponse || data.output || data.response || data.text || data, 
                'bot'
            );

        } catch (error) {
            clearTimeout(timeoutId);
            hideLoadingIndicator();
            let fallbackMessage = (error.name === 'AbortError') 
                ? "The connection seems to be very slow. Please check your internet connection and try again." 
                : "Sorry, our AI assistant is currently a bit busy. If you have any urgent requirements, please submit them directly through our [Contact Form](#contact), and our representative will contact you shortly.";
            
            appendMessage(fallbackMessage, 'bot');
            console.error("AI Error Triggered: ", error);
        }
    }

    function appendMessage(text, sender) {
        const wrapper = document.createElement('div');
        if (sender === 'user') {
            wrapper.className = "flex items-start justify-end space-x-2.5 max-w-[85%] ml-auto";
            wrapper.innerHTML = `
                <div class="bg-indigo-600 text-white rounded-2xl rounded-tr-none p-3 shadow-sm text-xs leading-relaxed whitespace-pre-line">
                    ${text}
                </div>
                <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shrink-0 mt-0.5">ME</div>
            `;
        } else {
            wrapper.className = "flex items-start space-x-2.5 max-w-[85%]";
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs shrink-0 mt-0.5">AI</div>
                <div class="bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-none p-3 shadow-sm text-xs leading-relaxed chat-content-box window-full overflow-hidden">
                    ${formatMessageText(text)}
                </div>
            `;
        }
        chatMessagesContainer.appendChild(wrapper);
        scrollToBottom();
    }

    // 5. Event Listeners Setup
    document.getElementById('toggle-chat-bubble').addEventListener('click', window.toggleChat);
    document.getElementById('close-chat-btn').addEventListener('click', window.toggleChat);
    document.getElementById('reset-chat-btn').addEventListener('click', resetChat);
    chatForm.addEventListener('submit', sendMessage);
})();