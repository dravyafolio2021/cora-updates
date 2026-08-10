<?php
/**
 * Cora Platform Documentation Right Widgets Component (v2.4.0)
 * Monochromatic Notion / Shopify Visual System Standard
 * Core User: Shruti (Studio Administrator)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$agency_id = cora_db_get_agency_id();
$t_pages = $wpdb->prefix . 'cora_docs_pages';

// Dynamically retrieve related pages if they exist in DB
$related_docs = array();
if ( cora_table_exists( $t_pages ) ) {
    $related_docs = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT title, slug FROM {$t_pages} WHERE status = 'published' AND slug != %s ORDER BY id DESC LIMIT 3",
            get_query_var( 'sub_slug', '' )
        ),
        ARRAY_A
    ) ?: array();
}

// Fallback to defaults if DB query is empty or table doesn't exist yet
if ( empty( $related_docs ) ) {
    $related_docs = array(
        array( 'title' => 'Workspace Configuration Guide', 'slug' => 'workspace-configuration' ),
        array( 'title' => 'User Management & Roles', 'slug' => 'user-management' ),
        array( 'title' => 'API Authentication', 'slug' => 'api-authentication' )
    );
}

// Generate public nonce for public search & RAG chatbot
$public_nonce = wp_create_nonce( 'cora_public_docs_nonce' );
$ajax_url     = admin_url( 'admin-ajax.php' );
?>

<aside class="w-72 shrink-0 sticky top-8 space-y-6 hidden lg:block" id="cora-docs-widgets-column">

    <!-- 1. On This Page (Dynamic Scroll-Spy TOC) -->
    <div class="space-y-3">
        <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
            On This Page
        </h4>
        <div class="relative pl-4 border-l border-zinc-200 ">
            <!-- Active indicator vertical guide dot container -->
            <ul id="toc-list" class="space-y-2.5 py-0.5">
                <!-- Dynamically populated via JS -->
                <li class="text-xs text-zinc-400 italic">
                    Loading page headings...
                </li>
            </ul>
        </div>
    </div>

    <!-- 2. Ask Cora AI Chatbot Card -->
    <div class="border border-zinc-200/80 bg-white rounded-xl p-4 shadow-sm flex flex-col space-y-3.5">
        <div class="flex items-center justify-between pb-2 border-b border-zinc-100 ">
            <div class="flex items-center gap-2">
                <span class="p-1.5 bg-zinc-100 rounded-md text-zinc-850 ">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                </span>
                <span class="text-xs font-bold text-zinc-850 ">Ask Cora AI</span>
            </div>
            <!-- Online Indicator -->
            <span class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-medium bg-green-50 text-green-700 border border-green-200/30">
                <span class="w-1 h-1 bg-green-500 rounded-full animate-pulse"></span>
                RAG Online
            </span>
        </div>

        <!-- Chat messages body container -->
        <div id="cora-chat-messages" class="max-h-48 overflow-y-auto space-y-3 pr-1 text-xs flex flex-col scrollbar-thin scrollbar-thumb-zinc-200">
            <div class="p-2.5 rounded-lg text-xs bg-zinc-100 text-zinc-800 self-start max-w-[88%] leading-relaxed">
                Hi! Ask me anything about the Cora Platform features, CRM, CGST/SGST ledger, e-signing, or documentation configuration.
            </div>
        </div>

        <!-- Inline input block -->
        <div class="relative flex items-center shrink-0">
            <input type="text" id="cora-chat-input" onkeydown="handleCoraChatKeyDown(event)" class="w-full border border-zinc-200 rounded-lg pl-3 pr-8 py-2 text-xs bg-zinc-50 focus:border-zinc-400 outline-none text-zinc-900 transition-all font-sans" placeholder="Ask anything about Cora...">
            <button onclick="submitCoraChatQuery()" id="cora-chat-send-btn" class="absolute right-2.5 text-zinc-400 hover:text-zinc-900 transition-colors p-1 cursor-pointer" title="Send Question">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </div>
    </div>

    <!-- 3. Related Docs Panel -->
    <div class="space-y-3">
        <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
            Related Docs
        </h4>
        <div class="space-y-2">
            <?php foreach ( $related_docs as $doc ) : ?>
                <a href="<?php echo esc_url( home_url( '/docs/' . $doc['slug'] ) ); ?>" class="flex items-center gap-2.5 p-2.5 border border-zinc-200/50 hover:border-zinc-400 bg-white hover:bg-zinc-50 rounded-lg transition-all group shadow-xs">
                    <span class="text-zinc-400 group-hover:text-zinc-850 transition-colors shrink-0">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </span>
                    <span class="text-xs text-zinc-600 font-medium truncate group-hover:text-zinc-900 transition-colors">
                        <?php echo esc_html( $doc['title'] ); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. Was This Helpful Voting Module -->
    <div class="border-t border-zinc-200/60 pt-5 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">
                Was this helpful?
            </span>
            <div class="flex items-center gap-1.5">
                <button onclick="handleCoraVote(true, this)" class="p-1.5 border border-zinc-200 text-zinc-550 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-all cursor-pointer shadow-xs" title="Yes, helpful">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                </button>
                <button onclick="handleCoraVote(false, this)" class="p-1.5 border border-zinc-200 text-zinc-550 hover:text-zinc-900 hover:bg-zinc-50 rounded-lg transition-all cursor-pointer shadow-xs" title="No, not helpful">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm12-3h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3"></path></svg>
                </button>
            </div>
        </div>
    </div>

</aside>

<!-- Configuration Bridge -->
<script>
window.coraDocsConfig = window.coraDocsConfig || {
    ajaxUrl: <?php echo json_encode( esc_url( $ajax_url ) ); ?>,
    nonce: <?php echo json_encode( esc_attr( $public_nonce ) ); ?>
};
</script>

<!-- Monochromatic Layout-Safe Styling -->
<style>
/* Custom mini scrollbar for Notion side panel */
#cora-chat-messages {
    scrollbar-width: thin;
    scrollbar-color: rgba(161, 161, 170, 0.2) transparent;
}
#cora-chat-messages::-webkit-scrollbar {
    width: 4px;
}
#cora-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}
#cora-chat-messages::-webkit-scrollbar-thumb {
    background-color: rgba(161, 161, 170, 0.25);
    border-radius: 9999px;
}
.dark #cora-chat-messages::-webkit-scrollbar-thumb {
    background-color: rgba(161, 161, 170, 0.1);
}
</style>

<!-- Sidebar JS Mechanics -->
<script>
(function() {
    // 1. Dynamic Table of Contents Generator (Scroll-Spy)
    window.coraDocsRefreshTOC = function() {
        const tocList = document.getElementById('toc-list');
        if (!tocList) return;

        // Target .prose elements inside main panel
        const proseContent = document.querySelector('.prose') || document.querySelector('main article') || document.querySelector('main');
        if (!proseContent) {
            tocList.innerHTML = '<li class="text-xs text-zinc-400 italic">No structure detected.</li>';
            return;
        }

        const headings = proseContent.querySelectorAll('h2, h3');
        if (headings.length === 0) {
            tocList.innerHTML = '<li class="text-xs text-zinc-400 italic">No subheadings on this page.</li>';
            return;
        }

        tocList.innerHTML = '';
        const tocItems = [];

        headings.forEach((heading, idx) => {
            const titleText = heading.textContent.trim();
            if (!titleText) return;

            // Generate clean anchor ID if missing
            if (!heading.id) {
                heading.id = 'heading-' + idx + '-' + titleText.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/(^-|-$)/g, '');
            }

            const isH3 = heading.tagName.toLowerCase() === 'h3';
            const li = document.createElement('li');
            li.className = 'relative flex items-center';
            if (isH3) {
                li.classList.add('pl-4.5');
            }

            // Dot alignment matches typography line-height precisely
            const dot = document.createElement('span');
            dot.className = `toc-dot absolute ${isH3 ? '-left-[4px]' : '-left-[17px]'} w-1.5 h-1.5 rounded-full bg-zinc-900 scale-0 transition-all duration-200 ease-out`;

            const link = document.createElement('a');
            link.href = '#' + heading.id;
            link.className = `toc-link text-xs text-zinc-400 hover:text-zinc-950 transition-colors duration-150 block py-0.5 truncate max-w-[240px] ${isH3 ? 'text-[11px]' : ''}`;
            link.textContent = titleText;
            
            // Safe jump click behavior
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetEl = document.getElementById(heading.id);
                if (targetEl) {
                    window.scrollTo({
                        top: targetEl.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    history.pushState(null, '', '#' + heading.id);
                }
            });

            li.appendChild(dot);
            li.appendChild(link);
            tocList.appendChild(li);

            tocItems.push({
                element: heading,
                linkEl: link,
                dotEl: dot
            });
        });

        // Scroll Spy Handler
        function handleScrollSpy() {
            let activeIdx = -1;
            const scrollPos = window.scrollY + 120;

            for (let i = 0; i < tocItems.length; i++) {
                if (tocItems[i].element.offsetTop <= scrollPos) {
                    activeIdx = i;
                } else {
                    break;
                }
            }

            if (activeIdx === -1 && tocItems.length > 0) {
                activeIdx = 0;
            }

            tocItems.forEach((item, index) => {
                if (index === activeIdx) {
                    item.linkEl.classList.remove('text-zinc-400');
                    item.linkEl.classList.add('text-zinc-950', 'font-semibold');
                    item.dotEl.classList.remove('scale-0');
                    item.dotEl.classList.add('scale-100');
                } else {
                    item.linkEl.classList.remove('text-zinc-950', 'font-semibold');
                    item.linkEl.classList.add('text-zinc-400');
                    item.dotEl.classList.remove('scale-100');
                    item.dotEl.classList.add('scale-0');
                }
            });
        }

        window.removeEventListener('scroll', handleScrollSpy);
        window.addEventListener('scroll', handleScrollSpy);
        handleScrollSpy();
    };

    // 2. Chatbot Input Mechanics
    window.handleCoraChatKeyDown = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitCoraChatQuery();
        }
    };

    window.submitCoraChatQuery = function() {
        const input = document.getElementById('cora-chat-input');
        const sendBtn = document.getElementById('cora-chat-send-btn');
        const query = input.value.trim();
        if (!query) return;

        // Reset inputs and disable
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        // Append user doubt bubble
        appendCoraChatBubble('user', query);

        // Typing indicator card
        const thinkingBubble = appendCoraChatBubble('ai', `
            <div class="flex items-center gap-1.5 py-0.5" id="cora-chat-thinking">
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-500 animate-bounce"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay:0.2s"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-zinc-500 animate-bounce" style="animation-delay:0.4s"></span>
            </div>
        `);

        // Prepare query request payload
        const formData = new FormData();
        formData.append('action', 'cora_public_query_rag');
        formData.append('question', query);
        formData.append('nonce', window.coraDocsConfig.nonce);

        fetch(window.coraDocsConfig.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(res => {
            thinkingBubble.remove();
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();

            if (res.success && res.data && res.data.reply) {
                streamCoraChatReply(res.data.reply);
            } else {
                const errMsg = (res.data && res.data.message) ? res.data.message : 'Doubt lookup error.';
                appendCoraChatBubble('ai', `<span class="text-red-500 font-semibold">${errMsg}</span>`);
            }
        })
        .catch(err => {
            thinkingBubble.remove();
            input.disabled = false;
            sendBtn.disabled = false;
            appendCoraChatBubble('ai', '<span class="text-red-500 font-semibold">Network failure. Please try again.</span>');
        });
    };

    function appendCoraChatBubble(sender, content) {
        const messages = document.getElementById('cora-chat-messages');
        const bubble = document.createElement('div');
        bubble.className = `p-2.5 rounded-lg text-xs max-w-[88%] leading-relaxed ${
            sender === 'user'
                ? 'bg-zinc-950 text-white self-end ml-auto shadow-sm'
                : 'bg-zinc-100 text-zinc-800 self-start shadow-2xs'
        }`;
        bubble.innerHTML = content;
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
        return bubble;
    }

    function parseSimpleMarkdown(text) {
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

    function streamCoraChatReply(fullText) {
        const messages = document.getElementById('cora-chat-messages');
        const bubble = document.createElement('div');
        bubble.className = 'p-2.5 rounded-lg text-xs max-w-[88%] leading-relaxed bg-zinc-100 text-zinc-800 self-start shadow-2xs';
        messages.appendChild(bubble);

        const words = fullText.split(' ');
        let currentIdx = 0;
        let runningText = '';

        const timer = setInterval(() => {
            if (currentIdx >= words.length) {
                clearInterval(timer);
                bubble.innerHTML = parseSimpleMarkdown(fullText);
                messages.scrollTop = messages.scrollHeight;
                return;
            }

            runningText += (currentIdx === 0 ? '' : ' ') + words[currentIdx];
            bubble.innerHTML = parseSimpleMarkdown(runningText);
            messages.scrollTop = messages.scrollHeight;
            currentIdx++;
        }, 30); // Dynamic word progressive rendering
    }

    // 3. Was This Helpful Feedback Widget
    window.handleCoraVote = function(wasUpvote, buttonEl) {
        // Toggle selected styling
        const parent = buttonEl.parentElement;
        const buttons = parent.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.classList.remove('bg-zinc-900', 'text-white');
            btn.classList.add('text-zinc-550', 'bg-transparent');
        });

        buttonEl.classList.remove('text-zinc-550', 'bg-transparent');
        buttonEl.classList.add('bg-zinc-900', 'text-white');

        // Show layout feedback toast
        window.coraShowToast("Thank you for your feedback!");
    };

    // 4. Fallback Toast System (Layout Isolation Policy)
    window.coraShowToast = window.coraShowToast || function(msg, type = 'success') {
        let container = document.getElementById('cora-public-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cora-public-toast-container';
            container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'flex items-center gap-2.5 px-4 py-3 bg-zinc-900 text-white text-xs font-bold rounded-xl shadow-lg pointer-events-auto transition-all duration-300 transform translate-y-3 opacity-0 border border-zinc-800 ';
        
        const successIcon = `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        toast.innerHTML = `${successIcon} <span>${msg}</span>`;
        container.appendChild(toast);

        // Animate entrance
        setTimeout(() => {
            toast.classList.remove('translate-y-3', 'opacity-0');
        }, 20);

        // Dismiss sequence
        setTimeout(() => {
            toast.classList.add('translate-y-3', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3500);
    };

    // Run initialization hooks
    document.addEventListener('DOMContentLoaded', window.coraDocsRefreshTOC);
    window.addEventListener('load', window.coraDocsRefreshTOC);
    
    // Fallback if readyState is already loaded or complete
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        window.coraDocsRefreshTOC();
    }
})();
</script>
