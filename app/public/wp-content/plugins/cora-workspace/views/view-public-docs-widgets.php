<?php
/**
 * Cora Platform Documentation Right Widgets Component (v3.2.100)
 * Monochromatic Notion / Shopify Visual System Standard
 * Dedicated Full-Height Inline AI Playground
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Generate public nonce for public search & RAG chatbot
$public_nonce = wp_create_nonce( 'cora_public_docs_nonce' );
$ajax_url     = admin_url( 'admin-ajax.php' );
?>

<aside class="w-80 shrink-0 sticky top-20 h-[calc(100vh-5.5rem)] hidden lg:flex flex-col bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-2xs font-sans z-30" id="cora-docs-widgets-column">
    <!-- Header -->
    <div class="px-4 py-3 border-b border-zinc-100 flex items-center justify-between select-none shrink-0 bg-white">
        <div class="flex items-center gap-2">
            <span class="p-1 bg-zinc-100 rounded text-zinc-950 flex items-center justify-center">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
            </span>
            <span class="font-bold text-xs text-zinc-950 font-display">Ask Cora AI</span>
            <span class="flex items-center gap-1 px-1.5 py-0.5 rounded bg-green-50 text-green-700 text-[8px] font-bold border border-green-200/30">
                <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                RAG Online
            </span>
        </div>
        <!-- Close button X (Hides the inline sidebar column) -->
        <button onclick="window.coraToggleAiSidebar(false)" class="text-zinc-400 hover:text-zinc-905 transition-colors cursor-pointer p-1.5 bg-transparent border-none flex items-center justify-center rounded-lg hover:bg-zinc-50" title="Close Playground">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Scrollable Chat Area -->
    <div id="cora-inline-messages-container" class="flex-1 overflow-y-auto p-4 space-y-5 bg-zinc-50/10 scrollbar-thin">
        
        <!-- Welcome Greeting & Suggested Questions Container -->
        <div id="cora-inline-welcome-view" class="space-y-5">
            <!-- Welcome Greeting -->
            <div class="space-y-1 select-none pt-1">
                <h3 class="text-lg font-bold text-zinc-900 tracking-tight">Hi Dravya! 👋</h3>
                <p class="text-[11px] text-zinc-500 leading-relaxed font-semibold">
                    I'm Cora AI, your documentation assistant.<br>
                    How can I help you today?
                </p>
            </div>

            <!-- Quick Questions list -->
            <div class="space-y-1.5">
                <button onclick="window.coraSubmitInlineQuickQuery('Explain the authentication flow')" class="w-full text-left p-3 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-[11px] text-zinc-700 hover:text-zinc-955 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        <span>Explain the authentication flow</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitInlineQuickQuery('How do I create a new invoice?')" class="w-full text-left p-3 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-[11px] text-zinc-700 hover:text-zinc-955 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>How do I create a new invoice?</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitInlineQuickQuery('Show me API rate limits')" class="w-full text-left p-3 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-[11px] text-zinc-700 hover:text-zinc-955 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Show me API rate limits</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
                <button onclick="window.coraSubmitInlineQuickQuery('What permissions does a crew member have?')" class="w-full text-left p-3 bg-white border border-zinc-150 hover:border-zinc-300 rounded-xl text-[11px] text-zinc-700 hover:text-zinc-955 hover:bg-zinc-50 transition-all cursor-pointer shadow-3xs font-semibold flex items-center justify-between group">
                    <span class="flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 group-hover:text-zinc-800 transition-colors"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>What permissions does a crew member have?</span>
                    </span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-800 transition-colors"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <!-- Reference Hub Grid inside welcome container -->
            <div class="grid grid-cols-2 gap-3 pt-1">
                <!-- Popular Topics -->
                <div class="space-y-1.5">
                    <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider block">Popular Topics</span>
                    <div class="space-y-1.5">
                        <a href="#" onclick="window.coraInlineTopicLink(event, 'platform-overview')" class="flex items-center gap-2 p-1.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-lg transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-650 rounded group-hover:bg-zinc-950 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-800 text-[10px] truncate group-hover:text-zinc-955 transition-colors">Getting Started</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraInlineTopicLink(event, 'workspace-roles')" class="flex items-center gap-2 p-1.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-lg transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-650 rounded group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-800 text-[10px] truncate group-hover:text-zinc-955 transition-colors">Authentication</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraInlineTopicLink(event, 'api')" class="flex items-center gap-2 p-1.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-lg transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-650 rounded group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-800 text-[10px] truncate group-hover:text-zinc-955 transition-colors">API Reference</span>
                            </div>
                        </a>
                        <a href="#" onclick="window.coraInlineTopicLink(event, 'pwa-push-notifications')" class="flex items-center gap-2 p-1.5 bg-white border border-zinc-150 hover:border-zinc-300 rounded-lg transition-all group">
                            <span class="p-1 bg-zinc-100 text-zinc-650 rounded group-hover:bg-zinc-955 group-hover:text-white transition-colors shrink-0">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-zinc-800 text-[10px] truncate group-hover:text-zinc-955 transition-colors">Webhook Events</span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Recently Viewed -->
                <div class="space-y-1.5">
                    <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-wider block">Recently Viewed</span>
                    <div class="border border-zinc-150 bg-white rounded-lg divide-y divide-zinc-100 overflow-hidden shadow-3xs">
                        <div class="flex items-center justify-between p-2 text-[10px] hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 truncate pr-1">/api/v1/auth/login</span>
                        </div>
                        <div class="flex items-center justify-between p-2 text-[10px] hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 truncate pr-1">Invoices Engine</span>
                        </div>
                        <div class="flex items-center justify-between p-2 text-[10px] hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 truncate pr-1">Crew Scheduler</span>
                        </div>
                        <div class="flex items-center justify-between p-2 text-[10px] hover:bg-zinc-50/50 transition-colors">
                            <span class="font-bold text-zinc-800 truncate pr-1">Webhook Events</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Input Area -->
    <div class="p-3 border-t border-zinc-100 bg-white shrink-0">
        <form id="cora-inline-chat-form" onsubmit="window.coraSubmitInlineChat(event)" class="relative flex items-center">
            <input type="text" id="cora-inline-chat-input" class="w-full border border-zinc-200 rounded-lg pl-3 pr-8 py-2 text-xs bg-zinc-50 focus:bg-white focus:border-zinc-950 outline-none text-zinc-900 transition-all placeholder-zinc-400 font-sans" placeholder="Ask anything about Cora..." required autocomplete="off">
            <button type="submit" id="cora-inline-chat-send-btn" class="absolute right-2 text-zinc-450 hover:text-zinc-900 transition-colors p-1 cursor-pointer bg-transparent border-none flex items-center justify-center" title="Send Doubt">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </form>
        <div class="mt-2 text-center text-[9px] text-zinc-400 select-none">
            Powered by Cora AI &bull; Using docs v2.2.1
        </div>
    </div>
</aside>

<style>
/* Clean, thin monochromatic scrollbar for the RAG chatbot inline panel */
#cora-inline-messages-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(161, 161, 170, 0.25) transparent;
}
#cora-inline-messages-container::-webkit-scrollbar {
    width: 4px;
}
#cora-inline-messages-container::-webkit-scrollbar-track {
    background: transparent;
}
#cora-inline-messages-container::-webkit-scrollbar-thumb {
    background-color: rgba(161, 161, 170, 0.3);
    border-radius: 9999px;
}
#cora-inline-messages-container::-webkit-scrollbar-thumb:hover {
    background-color: rgba(161, 161, 170, 0.5);
}
</style>

<script>
(function() {
    const ajaxUrl = '<?php echo esc_url( $ajax_url ); ?>';
    const publicNonce = '<?php echo esc_attr( $public_nonce ); ?>';

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatMarkdown(text) {
        if (typeof formatMessageMarkdown === 'function') {
            return formatMessageMarkdown(text);
        }
        // Fallback simple parser
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`([^`]+)`/g, '<code class="bg-zinc-200/50 px-1 py-0.5 rounded font-mono text-[10px] text-zinc-900 ">$1</code>')
            .replace(/\n- (.*?)\n/g, '<br>• $1')
            .replace(/\n- (.*?)$/g, '<br>• $1')
            .replace(/\n\* (.*?)\n/g, '<br>• $1')
            .replace(/\n\* (.*?)$/g, '<br>• $1')
            .replace(/\n/g, '<br>');
    }

    window.coraSubmitInlineQuickQuery = function(query) {
        const input = document.getElementById('cora-inline-chat-input');
        if (input) {
            input.value = query;
            window.coraSubmitInlineChat();
        }
    };

    window.coraInlineTopicLink = function(e, slug) {
        if (e) e.preventDefault();
        if (slug === 'api' || slug === 'changelog') {
            coraPublicShowSection(slug);
        } else {
            coraPublicLoadPage(null, slug, null);
        }
    };

    window.coraSubmitInlineChat = function(event) {
        if (event) event.preventDefault();
        
        const input = document.getElementById('cora-inline-chat-input');
        const sendBtn = document.getElementById('cora-inline-chat-send-btn');
        const doubt = input.value.trim();
        if (!doubt) return;
        
        input.value = '';
        input.disabled = true;
        if (sendBtn) sendBtn.disabled = true;
        
        // Hide welcome view on first submit
        const welcomeView = document.getElementById('cora-inline-welcome-view');
        if (welcomeView) {
            welcomeView.classList.add('hidden');
        }
        
        // Append user query to chat history
        const msgList = document.getElementById('cora-inline-messages-container');
        msgList.insertAdjacentHTML('beforeend', `
            <div class="flex gap-2 max-w-[90%] ml-auto justify-end">
                <div class="bg-zinc-950 text-white p-2.5 rounded-lg text-xs leading-relaxed border border-zinc-950 shadow-xs">
                    ${escapeHtml(doubt)}
                </div>
                <div class="w-6 h-6 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center text-[10px] font-bold text-zinc-650 select-none flex-shrink-0">Me</div>
            </div>
        `);
        
        msgList.scrollTop = msgList.scrollHeight;
        
        // Add dynamic dot loading animation
        const typingId = 'cora-inline-typing-' + Date.now();
        msgList.insertAdjacentHTML('beforeend', `
            <div id="${typingId}" class="flex gap-2 max-w-[90%] select-none">
                <div class="w-6 h-6 rounded-full bg-zinc-950 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">AI</div>
                <div class="bg-zinc-100 text-zinc-400 p-2.5 rounded-lg text-xs border border-zinc-200/55 flex items-center gap-1 shadow-3xs">
                    <span class="w-1 h-1 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1 h-1 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1 h-1 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            </div>
        `);
        msgList.scrollTop = msgList.scrollHeight;
        
        // Build query body
        const formData = new FormData();
        formData.append('action', 'cora_public_query_rag');
        formData.append('question', doubt);
        formData.append('nonce', publicNonce);
        
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            const typingEl = document.getElementById(typingId);
            if (typingEl) typingEl.remove();
            
            input.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
            input.focus();
            
            if (res.success && res.data && res.data.reply) {
                addInlineAiMessage(msgList, res.data.reply);
            } else {
                const reply = coraSimulateRagReplyFallback(doubt);
                addInlineAiMessage(msgList, reply);
            }
        })
        .catch(err => {
            const typingEl = document.getElementById(typingId);
            if (typingEl) typingEl.remove();
            
            input.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
            input.focus();
            
            const reply = coraSimulateRagReplyFallback(doubt);
            addInlineAiMessage(msgList, reply);
        });
    };

    function addInlineAiMessage(msgList, text) {
        const formatted = formatMarkdown(text);
        
        msgList.insertAdjacentHTML('beforeend', `
            <div class="flex gap-2 max-w-[90%]">
                <div class="w-6 h-6 rounded-full bg-zinc-950 flex items-center justify-center text-[10px] font-bold text-white select-none flex-shrink-0">AI</div>
                <div class="bg-zinc-100 text-zinc-800 p-2.5 rounded-lg text-xs leading-relaxed border border-zinc-200/55 shadow-xs">
                    ${formatted}
                </div>
            </div>
        `);
        msgList.scrollTop = msgList.scrollHeight;
    }

    function coraSimulateRagReplyFallback(question) {
        const q = question.toLowerCase();
        if (q.includes('auth') || q.includes('token') || q.includes('key')) {
            return "To authenticate requests:\n- Generate an API Key under CRM settings.\n- Set header `Authorization: Bearer <key>`.\n- Rates are limited to 60 requests/min.";
        }
        if (q.includes('invoice') || q.includes('gst') || q.includes('ledger')) {
            return "The Financials engine handles CGST/SGST splitting:\n- Default GST calculation is set at 18%.\n- Splits split transaction inputs equally (9% CGST / 9% SGST).\n- Triggers real-time e-invoice stamps.";
        }
        if (q.includes('role') || q.includes('permission') || q.includes('access')) {
            return "Cora roles include Super Admin (`cora_super_admin`), Owner (`owner`), Administrator (`administrator`), and Scoped Member (`cora_member`). Custom capability maps are configurable inside the User permissions matrix drawer.";
        }
        return "I received your question: \"" + question + "\". As the Cora Documentation Assistant, I can help you with CRM Leads, GST Ledger math, e-signing workflows, or PWA setup. What specific module can I explain?";
    }
})();
</script>
