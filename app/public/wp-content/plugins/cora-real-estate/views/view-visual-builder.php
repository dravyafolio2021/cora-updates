<?php
/**
 * View: Visual Builder Page
 * Notion/Shopify Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- GrapesJS CDN Assets -->
<link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
<script src="https://unpkg.com/grapesjs/dist/grapes.min.js"></script>

<!-- Monochromatic Styling Overrides for GrapesJS & UI Elements -->
<style>
    /* Monochromatic GrapesJS Theme Overrides */
    .gjs-one-bg {
        background-color: #ffffff !important;
    }
    .gjs-two-color {
        color: #18181b !important;
    }
    .gjs-three-bg {
        background-color: #18181b !important;
    }
    .gjs-four-color, .gjs-four-color-h:hover {
        color: #18181b !important;
    }
    .gjs-pn-views-container {
        background-color: #fafafa !important;
        border-left: 1px solid #e4e4e7 !important;
    }
    .gjs-pn-views {
        border-left: 1px solid #e4e4e7 !important;
    }
    .gjs-pn-panels {
        background-color: #ffffff !important;
        border-bottom: 1px solid #e4e4e7 !important;
    }
    .gjs-block {
        width: 100% !important;
        min-height: auto !important;
        background-color: #ffffff !important;
        border: 1px solid #e4e4e7 !important;
        color: #27272a !important;
        border-radius: 6px !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        padding: 10px !important;
        margin: 0 0 8px 0 !important;
        transition: all 0.15s ease !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-align: center;
    }
    .gjs-block:hover {
        border-color: #18181b !important;
        color: #18181b !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.05) !important;
    }
    .gjs-cv-canvas {
        background-color: #f4f4f5 !important;
    }
    .gjs-highlighter, .gjs-highlighter-sel {
        outline: 1.5px solid #18181b !important;
    }
    .gjs-badge {
        background-color: #18181b !important;
        color: #ffffff !important;
        font-family: inherit !important;
        font-size: 10px !important;
        font-weight: 600 !important;
        border-radius: 2px !important;
    }
    .gjs-toolbar {
        background-color: #18181b !important;
        border-radius: 4px !important;
    }
    .gjs-toolbar-item:hover {
        background-color: #3f3f46 !important;
    }
    
    /* Custom Scrollbar for Blocks Panel */
    #blocks::-webkit-scrollbar {
        width: 4px;
    }
    #blocks::-webkit-scrollbar-track {
        background: transparent;
    }
    #blocks::-webkit-scrollbar-thumb {
        background: #e4e4e7;
        border-radius: 2px;
    }
    #blocks::-webkit-scrollbar-thumb:hover {
        background: #d4d4d8;
    }

    /* General layout adjustments */
    #gjs {
        border: none;
        height: 100% !important;
    }
    .gjs-mdl-dialog {
        border-radius: 6px !important;
        border: 1px solid #e4e4e7 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
</style>

<!-- Top Header Bar -->
<div class="cora-page-header flex flex-col xl:flex-row xl:items-center justify-between gap-4 pb-4 border-b border-zinc-200">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
                <line x1="9" y1="9" x2="21" y2="9"></line>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Visual Page Builder</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Build and customize responsive landing pages utilizing GrapesJS and AI layout generation.</p>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-2.5">
        <!-- Page Select Dropdown -->
        <select id="cora-builder-page-select" class="px-3 py-2 border border-zinc-250 rounded-md text-sm bg-white text-zinc-700 focus:outline-none focus:border-black transition">
            <option value="">-- New Page --</option>
            <?php
            // List builder pages
            $args = array(
                'post_type'      => 'page',
                'posts_per_page' => -1,
                'post_status'    => array( 'publish', 'draft', 'private' ),
                'meta_query'     => array(
                    array(
                        'key'     => '_cora_is_visual_builder',
                        'value'   => '1',
                        'compare' => '='
                    )
                )
            );
            $builder_pages = get_posts( $args );
            foreach ( $builder_pages as $bp ) {
                echo '<option value="' . esc_attr( $bp->ID ) . '">' . esc_html( $bp->post_title ) . '</option>';
            }
            ?>
        </select>
        
        <!-- AI Prompt -->
        <div class="flex items-center border border-zinc-250 rounded-md bg-white overflow-hidden shadow-sm">
            <input type="text" id="cora-builder-ai-prompt" placeholder="AI Prompt: luxury villa..." class="px-3 py-2 text-sm bg-transparent outline-none w-36 sm:w-48 focus:w-64 transition-all duration-300">
            <button id="cora-builder-generate-btn" class="px-3 py-2 bg-zinc-900 text-white hover:bg-zinc-800 text-xs font-semibold uppercase tracking-wider transition-colors shrink-0">
                Generate
            </button>
        </div>
        
        <!-- Settings Trigger -->
        <button id="cora-builder-settings-btn" class="px-4 py-2 border border-zinc-250 text-zinc-700 bg-white hover:bg-zinc-50 font-semibold rounded-md text-sm transition-colors flex items-center gap-2 shadow-sm">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            <span>Settings</span>
        </button>
        
        <!-- Save/Publish Button -->
        <button id="cora-builder-publish-btn" class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-sm transition-colors flex items-center gap-2 shadow-sm">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            <span>Publish</span>
        </button>
    </div>
</div>

<!-- Layout: Sidebar and Canvas -->
<div class="flex h-[calc(100vh-210px)] border border-zinc-200 rounded-lg overflow-hidden bg-white shadow-sm mt-4">
    <!-- Left Sidebar for Blocks -->
    <div class="w-60 border-r border-zinc-200 bg-zinc-50 flex flex-col shrink-0">
        <div class="p-4 border-b border-zinc-200 bg-white select-none">
            <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Canvas Blocks</h3>
            <p class="text-[10px] text-zinc-400 mt-0.5 leading-normal">Drag and drop elements into the editor canvas below.</p>
        </div>
        <div id="blocks" class="p-3 overflow-y-auto flex-1 select-none">
            <!-- GrapesJS will append blocks here -->
        </div>
    </div>
    
    <!-- Main Canvas Area -->
    <div class="flex-1 bg-zinc-150 relative flex flex-col">
        <div id="gjs" class="flex-1">
            <!-- GrapesJS editor canvas container -->
        </div>
    </div>
</div>

<!-- Right-Sliding Settings Drawer Sheet -->
<div id="cora-builder-drawer-overlay" class="fixed inset-0 bg-zinc-900/20 backdrop-blur-[1px] z-[99998] hidden transition-opacity duration-300"></div>
<div id="cora-builder-drawer" class="fixed inset-y-0 right-0 w-[420px] max-w-[95vw] bg-white shadow-2xl border-l border-zinc-200 transform translate-x-full transition-transform duration-300 z-[99999] flex flex-col">
    <div class="flex items-center justify-between p-5 border-b border-zinc-100 bg-zinc-50/50 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="p-1.5 bg-zinc-900 text-white rounded">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </span>
            <h2 class="text-base font-bold text-zinc-900">Page Settings</h2>
        </div>
        <button id="cora-builder-drawer-close" class="text-zinc-400 hover:text-zinc-900 transition-colors p-1 rounded hover:bg-zinc-100 cursor-pointer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div class="p-6 overflow-y-auto flex-1 space-y-5">
        <!-- Hidden input for current page ID -->
        <input type="hidden" id="cora-builder-page-id" value="0">
        
        <!-- Page Title -->
        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Page Title</label>
            <input type="text" id="cora-builder-title" placeholder="e.g. Luxury Villa" class="w-full px-3 py-2 border border-zinc-200 rounded-md text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50 focus:bg-white transition-all">
        </div>
        
        <!-- URL Slug -->
        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">URL Slug</label>
            <input type="text" id="cora-builder-slug" placeholder="e.g. luxury-villa" class="w-full px-3 py-2 border border-zinc-200 rounded-md text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50 focus:bg-white transition-all font-mono">
        </div>
        
        <!-- Page Status -->
        <div class="space-y-1.5">
            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Page Status</label>
            <select id="cora-builder-status" class="w-full px-3 py-2 border border-zinc-200 rounded-md text-sm focus:outline-none focus:border-zinc-900 bg-zinc-50 focus:bg-white transition-all">
                <option value="draft">Draft</option>
                <option value="publish">Published</option>
                <option value="private">Private</option>
            </select>
        </div>
    </div>
</div>

<!-- Monochromatic Loading Indicator Overlay -->
<div id="cora-builder-loading" class="fixed inset-0 bg-white/80 backdrop-blur-[2px] z-[99999] flex flex-col items-center justify-center hidden">
    <div class="w-8 h-8 border-4 border-zinc-300 border-t-zinc-900 rounded-full animate-spin"></div>
    <div class="text-xs font-bold text-zinc-600 tracking-wider uppercase mt-4">Generating Layout...</div>
</div>

<script>
jQuery(document).ready(function($) {
    // 1. Initialize GrapesJS Editor
    const editor = grapesjs.init({
        container: '#gjs',
        fromElement: true,
        height: '100%',
        width: 'auto',
        storageManager: false,
        canvas: {
            styles: [coraREData.pluginsUrl + 'assets/css/tailwind-built.css']
        },
        blockManager: {
            appendTo: '#blocks'
        },
        deviceManager: {
            devices: [
                {
                    name: 'Desktop',
                    width: '', // default
                },
                {
                    name: 'Mobile',
                    width: '375px',
                    widthMedia: '375px',
                }
            ]
        },
        // Monochromatic panel customizations
        panels: {
            defaults: [
                {
                    id: 'commands',
                    buttons: [
                        {
                            id: 'device-desktop',
                            className: 'fa fa-desktop',
                            command: 'set-device-desktop',
                            active: true,
                        },
                        {
                            id: 'device-mobile',
                            className: 'fa fa-mobile',
                            command: 'set-device-mobile',
                        }
                    ]
                }
            ]
        }
    });

    // Device commands
    editor.Commands.add('set-device-desktop', {
        run: editor => editor.setDevice('Desktop')
    });
    editor.Commands.add('set-device-mobile', {
        run: editor => editor.setDevice('Mobile')
    });

    // 2. Define Custom Blocks
    editor.BlockManager.add('section', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg><div>Section</div>`,
        content: '<section class="py-16 px-8 bg-[#FBFaf7] border-b border-zinc-200"><div class="max-w-4xl mx-auto"><h2 class="text-3xl font-light tracking-tight mb-4">Section Heading</h2><p class="text-neutral-600 leading-relaxed text-sm">Add your custom text or details inside this section wrapper...</p></div></section>',
        category: 'Basic'
    });
    
    editor.BlockManager.add('text', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="10" y2="21"></line><line x1="14" y1="3" x2="14" y2="21"></line></svg><div>Text</div>`,
        content: '<p class="text-neutral-700 leading-relaxed text-sm">This is a paragraph of text. Double click inside to customize the copy.</p>',
        category: 'Basic'
    });

    editor.BlockManager.add('image', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg><div>Image</div>`,
        content: '<img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800&q=80" class="w-full h-auto object-cover max-w-full" alt="Villa Exterior Placeholder">',
        category: 'Basic'
    });

    editor.BlockManager.add('button', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="5" y="6" width="14" height="12" rx="2"></rect><line x1="9" y1="12" x2="15" y2="12"></line></svg><div>Button</div>`,
        content: '<a href="#" class="inline-block px-5 py-2.5 bg-black text-white text-xs uppercase tracking-wider font-semibold hover:bg-neutral-800 transition">Action Button</a>',
        category: 'Basic'
    });

    editor.BlockManager.add('columns', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="3" x2="12" y2="21"></line></svg><div>2 Columns</div>`,
        content: '<div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-6"><div class="p-4 border border-zinc-200 rounded">Column 1 content</div><div class="p-4 border border-zinc-200 rounded">Column 2 content</div></div>',
        category: 'Basic'
    });

    // 3. Settings Drawer Handlers
    function openDrawer() {
        $('#cora-builder-drawer-overlay').removeClass('hidden');
        setTimeout(() => {
            $('#cora-builder-drawer').removeClass('translate-x-full');
        }, 10);
    }

    function closeDrawer() {
        $('#cora-builder-drawer').addClass('translate-x-full');
        setTimeout(() => {
            $('#cora-builder-drawer-overlay').addClass('hidden');
        }, 300);
    }

    $('#cora-builder-settings-btn').on('click', function() {
        openDrawer();
    });

    $('#cora-builder-drawer-close, #cora-builder-drawer-overlay').on('click', function() {
        closeDrawer();
    });

    // 4. Page Selection Change Handler
    $('#cora-builder-page-select').on('change', function() {
        const pageId = $(this).val();
        if (!pageId) {
            // New Page: clear editor and settings
            editor.setComponents('');
            editor.setStyle('');
            $('#cora-builder-page-id').val('0');
            $('#cora-builder-title').val('');
            $('#cora-builder-slug').val('');
            $('#cora-builder-status').val('draft');
            return;
        }

        $('#cora-builder-loading').removeClass('hidden');
        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_get_builder_page',
                nonce: coraREData.ajaxNonce,
                page_id: pageId
            },
            success: function(response) {
                $('#cora-builder-loading').addClass('hidden');
                if (response.success) {
                    const data = response.data;
                    $('#cora-builder-page-id').val(data.id);
                    $('#cora-builder-title').val(data.title);
                    $('#cora-builder-slug').val(data.slug);
                    $('#cora-builder-status').val(data.status);
                    
                    editor.setComponents(data.html || '');
                    editor.setStyle(data.css || '');
                    window.coraShowToast("Page loaded successfully.");
                } else {
                    window.coraShowToast("Error loading page: " + (response.data || 'unknown error'));
                }
            },
            error: function() {
                $('#cora-builder-loading').addClass('hidden');
                window.coraShowToast("Failed to make request to load page.");
            }
        });
    });

    // 5. AI Page Generation Handler
    $('#cora-builder-generate-btn').on('click', function() {
        const prompt = $('#cora-builder-ai-prompt').val();
        if (!prompt) {
            window.coraShowToast("Please enter a layout prompt first.");
            return;
        }

        $('#cora-builder-loading').removeClass('hidden');
        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_generate_layout',
                nonce: coraREData.ajaxNonce,
                prompt: prompt
            },
            success: function(response) {
                $('#cora-builder-loading').addClass('hidden');
                if (response.success) {
                    const data = response.data;
                    editor.setComponents(data.html || '');
                    editor.setStyle(data.css || '');
                    window.coraShowToast("AI layout generated and loaded.");
                } else {
                    window.coraShowToast("AI Generation Failed: " + (response.data || 'unknown error'));
                }
            },
            error: function() {
                $('#cora-builder-loading').addClass('hidden');
                window.coraShowToast("Failed to execute AI layout generation.");
            }
        });
    });

    // 6. Publish / Save Handler
    $('#cora-builder-publish-btn').on('click', function() {
        // Ensure Page Title is set (if not, prompt inside settings drawer)
        let title = $('#cora-builder-title').val();
        if (!title) {
            openDrawer();
            $('#cora-builder-title').focus();
            window.coraShowToast("Please define the Page Title first.");
            return;
        }

        const pageId = $('#cora-builder-page-id').val();
        const slug = $('#cora-builder-slug').val();
        const status = $('#cora-builder-status').val();
        const html = editor.getHtml();
        const css = editor.getCss();

        $('#cora-builder-loading').removeClass('hidden');
        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_save_builder_page',
                nonce: coraREData.ajaxNonce,
                page_id: pageId,
                title: title,
                slug: slug,
                status: status,
                html: html,
                css: css
            },
            success: function(response) {
                $('#cora-builder-loading').addClass('hidden');
                if (response.success) {
                    const data = response.data;
                    $('#cora-builder-page-id').val(data.id);
                    $('#cora-builder-slug').val(data.slug);
                    
                    // Update Dropdown option list
                    const exists = $(`#cora-builder-page-select option[value="${data.id}"]`);
                    if (exists.length > 0) {
                        exists.text(data.title);
                    } else {
                        $('#cora-builder-page-select').append(`<option value="${data.id}">${data.title}</option>`);
                    }
                    $('#cora-builder-page-select').val(data.id);

                    window.coraShowToast("Saved successfully! Link: " + data.permalink);
                } else {
                    window.coraShowToast("Error saving page: " + (response.data || 'unknown error'));
                }
            },
            error: function() {
                $('#cora-builder-loading').addClass('hidden');
                window.coraShowToast("AJAX post request failed to save the builder page.");
            }
        });
    });

    // 7. Check if page_id parameter is present in URL query
    const urlParams = new URLSearchParams(window.location.search);
    const pageIdParam = urlParams.get('page_id');
    if (pageIdParam) {
        $('#cora-builder-page-select').val(pageIdParam).trigger('change');
    }
});
</script>
