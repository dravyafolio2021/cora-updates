<?php
/**
 * Cora Platform Public Documentation Content View
 *
 * Conforms to the Shopify/Notion monochromatic aesthetic.
 *
 * @package CoraWorkspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Fetch active page context passed from the routing engine
global $active_page;

$title       = $active_page['title'] ?? 'Platform Overview';
$slug        = $active_page['slug'] ?? 'platform-overview';
$category    = $active_page['category'] ?? 'overview';
$updated_at  = ! empty( $active_page['updated_at'] ) ? $active_page['updated_at'] : 'yesterday';

// Format updated label
$updated_str = 'Updated ';
if ( $updated_at === 'yesterday' || empty( $updated_at ) ) {
    $updated_str .= 'yesterday';
} else {
    $updated_time = strtotime( $updated_at );
    $diff         = time() - $updated_time;
    if ( $diff < 86400 ) {
        $updated_str .= 'today';
    } elseif ( $diff < 172800 ) {
        $updated_str .= 'yesterday';
    } else {
        $updated_str .= date( 'M j, Y', $updated_time );
    }
}

// Prepend emoji to title if it's the platform overview and doesn't already have it
$display_title = $title;
if ( 'platform-overview' === $slug && false === strpos( $display_title, '👋' ) ) {
    $display_title = '👋 ' . $display_title;
}

// Estimate reading time (average 200 words per minute)
$word_count   = ! empty( $active_page['content'] ) ? str_word_count( strip_tags( $active_page['content'] ) ) : 350;
$reading_time = max( 1, ceil( $word_count / 200 ) ) . ' min read';
?>

<!-- Standard markdown page output box -->
<div class="flex-1 min-w-0 bg-white border border-zinc-200/80 rounded-xl p-6 md:p-8 shadow-xs transition-all duration-200" id="cora-public-main-content">
    
    <!-- Sub-container 1: Standard Page Layout -->
    <div id="cora-public-page-layout" class="space-y-6">
    
    <!-- Top Bar: Breadcrumbs & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-100 pb-5 mb-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-1.5 text-[11px] font-medium text-zinc-400 font-sans select-none">
            <a href="<?php echo esc_url( home_url( '/docs' ) ); ?>" class="hover:text-zinc-900 transition-colors">Docs</a>
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300 ">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            <span class="capitalize" id="cora-public-breadcrumb-category"><?php echo esc_html( str_replace( '-', ' ', $category ) ); ?></span>
            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300 ">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            <span class="text-zinc-650 truncate" id="cora-public-breadcrumb-title"><?php echo esc_html( str_replace( '👋 ', '', $title ) ); ?></span>
        </nav>

        <!-- Action buttons bar -->
        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0 select-none">
            <!-- Copy link -->
            <button onclick="coraPublicCopyLink()" class="px-2.5 py-1.5 bg-white border border-zinc-200 rounded-lg text-[11px] font-semibold text-zinc-650 hover:bg-zinc-50 hover:text-zinc-900 transition-all shadow-3xs cursor-pointer flex items-center gap-1.5 select-none focus:outline-none active:scale-[0.98]">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                <span>Copy link</span>
            </button>

            <!-- Ask Cora AI -->
            <button onclick="window.coraToggleAiSidebar(true)" class="px-2.5 py-1.5 bg-zinc-950 border border-zinc-900 rounded-lg text-[11px] font-bold text-white hover:bg-zinc-900 transition-all shadow-3xs cursor-pointer flex items-center gap-1.5 select-none focus:outline-none active:scale-[0.98]">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.81 2.863a.5.5 0 0 0-.955 0L7.335 6.757 3.441 8.277a.5.5 0 0 0 0 .955l3.894 1.52 1.52 3.894a.5.5 0 0 0 .955 0l1.52-3.894 3.894-1.52a.5.5 0 0 0 0-.955l-3.894-1.52-1.52-3.894zM19 14.5a.5.5 0 0 0-.955 0l-.608 1.558-1.558.608a.5.5 0 0 0 0 .955l1.558.608.608 1.558a.5.5 0 0 0 .955 0l.608-1.558 1.558-.608a.5.5 0 0 0 0-.955l-1.558-.608-.608-1.558z"/>
                </svg>
                <span>Ask Cora AI</span>
            </button>
        </div>
    </div>

    <!-- Page Title -->
    <h1 class="text-2xl md:text-3xl font-extrabold text-zinc-950 font-display tracking-tight leading-none mb-3" id="cora-public-page-title">
        <?php echo esc_html( $display_title ); ?>
    </h1>

    <!-- Metadata Row -->
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-zinc-400 font-medium font-sans mb-8 select-none">
        <span id="cora-public-page-updated"><?php echo esc_html( $updated_str ); ?></span>
        <span class="w-1 h-1 rounded-full bg-zinc-200 "></span>
        
        <!-- Version badge -->
        <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-650 ">v<?php echo defined('CORA_WORKSPACE_VERSION') ? esc_html(CORA_WORKSPACE_VERSION) : '3.2.46'; ?></span>
        <span class="w-1 h-1 rounded-full bg-zinc-200 "></span>
        
        <!-- Category tags -->
        <span class="capitalize px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-500 text-[10px] tracking-wide font-bold uppercase border border-zinc-200/50 " id="cora-public-page-category">
            <?php echo esc_html( str_replace( '-', ' ', $category ) ); ?>
        </span>
        <span class="w-1 h-1 rounded-full bg-zinc-200 "></span>
        
        <!-- Reading time estimate -->
        <span id="cora-public-page-reading-time"><?php echo esc_html( $reading_time ); ?></span>
    </div>

    <!-- Standard Markdown Page Body -->
    <div class="prose max-w-none text-zinc-700 text-xs font-sans leading-relaxed" id="cora-public-markdown-body">
        <?php if ( 'platform-overview' !== $slug ) {
            echo cora_markdown_to_html( $active_page['content'] ?? '' );
        } ?>
    </div>

    <!-- Special Grid Panels (Only visible when current page slug is 'platform-overview') -->
    <div id="cora-platform-overview-special" class="<?php echo 'platform-overview' === $slug ? '' : 'hidden'; ?> mt-6 space-y-8">
        
        <!-- Optional intro content from DB if any -->
        <div id="cora-platform-overview-db-content" class="prose max-w-none text-zinc-700 text-xs font-sans leading-relaxed mb-6">
            <?php if ( 'platform-overview' === $slug && ! empty( $active_page['content'] ) ) {
                echo cora_markdown_to_html( $active_page['content'] );
            } ?>
        </div>

        <!-- Core Features 2x2 Grid -->
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 select-none">Core Features</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Decoupled Architecture -->
                <div class="p-5 border border-zinc-200/80 rounded-xl hover:border-zinc-300 hover:bg-zinc-50/20 transition-all duration-150 flex gap-4 items-start bg-white shadow-3xs">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-zinc-900 font-sans">Decoupled Architecture</h4>
                        <p class="text-[11px] text-zinc-500 leading-relaxed">Isolation of workspaces and databases for maximum security and scalability.</p>
                    </div>
                </div>

                <!-- Universal Auto-Save -->
                <div class="p-5 border border-zinc-200/80 rounded-xl hover:border-zinc-300 hover:bg-zinc-50/20 transition-all duration-150 flex gap-4 items-start bg-white shadow-3xs">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.81 2.863a.5.5 0 0 0-.955 0L7.335 6.757 3.441 8.277a.5.5 0 0 0 0 .955l3.894 1.52 1.52 3.894a.5.5 0 0 0 .955 0l1.52-3.894 3.894-1.52a.5.5 0 0 0 0-.955l-3.894-1.52-1.52-3.894zM19 14.5a.5.5 0 0 0-.955 0l-.608 1.558-1.558.608a.5.5 0 0 0 0 .955l1.558.608.608 1.558a.5.5 0 0 0 .955 0l.608-1.558 1.558-.608a.5.5 0 0 0 0-.955l-1.558-.608-.608-1.558z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-zinc-900 font-sans">Universal Auto-Save</h4>
                        <p class="text-[11px] text-zinc-500 leading-relaxed">Background debounce auto-save utilizing WordPress transient filters.</p>
                    </div>
                </div>

                <!-- MCP Ready -->
                <div class="p-5 border border-zinc-200/80 rounded-xl hover:border-zinc-300 hover:bg-zinc-50/20 transition-all duration-150 flex gap-4 items-start bg-white shadow-3xs">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 12h-3v8h-4v-8H7a3 3 0 0 1-3-3V4h16v5a3 3 0 0 1-3 3z"></path>
                            <line x1="12" y1="20" x2="12" y2="24"></line>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-zinc-900 font-sans">MCP Ready</h4>
                        <p class="text-[11px] text-zinc-500 leading-relaxed">Seamless connection of external LLM tools with inherited role-based permissions.</p>
                    </div>
                </div>

                <!-- Native AI RAG Layer -->
                <div class="p-5 border border-zinc-200/80 rounded-xl hover:border-zinc-300 hover:bg-zinc-50/20 transition-all duration-150 flex gap-4 items-start bg-white shadow-3xs">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.526 5.77 4 4 0 0 0 .556 6.588A4 4 0 1 0 12 18Z"></path>
                            <path d="M12 5a3 3 0 1 1 5.997.125 4 4 0 0 1 2.526 5.77 4 4 0 0 1-.556 6.588A4 4 0 1 1 12 18Z"></path>
                            <path d="M12 5v13"></path>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-zinc-900 font-sans">Native AI RAG Layer</h4>
                        <p class="text-[11px] text-zinc-500 leading-relaxed">Localized knowledge base synchronization per tenant workspace.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Tech Stack 2x2 Grid -->
        <div class="space-y-4 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 select-none">Tech Stack</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                
                <!-- Backend -->
                <div class="flex items-center gap-3.5 py-1.5 border border-transparent border-b-zinc-100/50 ">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M16.5 7.5L12 18L7.5 7.5"></path>
                            <path d="M14.5 7.5L12 14.5L9.5 7.5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] uppercase tracking-wider font-bold text-zinc-400 ">Backend</span>
                        <span class="block text-xs font-semibold text-zinc-750 truncate">WordPress 6.x foundation</span>
                    </div>
                </div>

                <!-- Client UI -->
                <div class="flex items-center gap-3.5 py-1.5 border border-transparent border-b-zinc-100/50 ">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] uppercase tracking-wider font-bold text-zinc-400 ">Client UI</span>
                        <span class="block text-xs font-semibold text-zinc-750 truncate text-ellipsis overflow-hidden">Vanilla CSS & HTML, Tailwind CSS overlays, React integrations</span>
                    </div>
                </div>

                <!-- AI Infrastructure -->
                <div class="flex items-center gap-3.5 py-1.5 border border-transparent border-b-zinc-100/50 ">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] uppercase tracking-wider font-bold text-zinc-400 ">AI Infrastructure</span>
                        <span class="block text-xs font-semibold text-zinc-750 truncate text-ellipsis overflow-hidden">Gemini 3.5 Flash and Claude 3.5 Sonnet RAG context agents</span>
                    </div>
                </div>

                <!-- MCP Gateway -->
                <div class="flex items-center gap-3.5 py-1.5 border border-transparent border-b-zinc-100/50 ">
                    <div class="p-2 border border-zinc-200/80 rounded-lg text-zinc-950 bg-zinc-50/50 shrink-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] uppercase tracking-wider font-bold text-zinc-400 ">MCP Gateway</span>
                        <span class="block text-xs font-semibold text-zinc-750 truncate text-ellipsis overflow-hidden">Secure JSON-RPC over WebSockets connection portal</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
    
    </div> <!-- End of #cora-public-page-layout -->

    <!-- Sub-container 2: API Registry Layout -->
    <div id="cora-public-api-layout" class="hidden space-y-6"></div>
    
    <!-- Sub-container 3: Changelog Layout -->
    <div id="cora-public-changelog-layout" class="hidden space-y-6"></div>
    
    <!-- Sub-container 4: AI Copilot Layout -->
    <div id="cora-public-ai-layout" class="hidden space-y-6"></div>

</div>

<!-- Monochromatic Toast Notification System Fallback / Global Definition -->
<script>
if (typeof window.coraShowToast !== 'function') {
    window.coraShowToast = function(message, type = 'success') {
        let container = document.getElementById('cora-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'cora-toast-container';
            container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-2 pointer-events-none font-sans';
            document.body.appendChild(container);
        }
        
        const toast = document.createElement('div');
        toast.className = 'transform translate-y-4 opacity-0 transition-all duration-300 pointer-events-auto flex items-center gap-2 px-4 py-3 bg-zinc-950 text-white text-xs font-semibold rounded-xl shadow-lg border border-zinc-850/80 select-none';
        
        let iconHtml = '';
        if (type === 'success') {
            iconHtml = `<svg class="w-4 h-4 text-zinc-100 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else if (type === 'error') {
            iconHtml = `<svg class="w-4 h-4 text-zinc-100 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        } else {
            iconHtml = `<svg class="w-4 h-4 text-zinc-100 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        }
        
        toast.innerHTML = `
            ${iconHtml}
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        // Force reflow
        toast.offsetHeight;
        
        // Transition in
        toast.classList.remove('translate-y-4', 'opacity-0');
        
        // Transition out
        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    };
}

// Copy link click handler
function coraPublicCopyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        if (window.coraShowToast) {
            window.coraShowToast('Documentation page link copied to clipboard.', 'success');
        }
    }).catch(err => {
        console.error('Failed to copy link: ', err);
    });
}

</script>
