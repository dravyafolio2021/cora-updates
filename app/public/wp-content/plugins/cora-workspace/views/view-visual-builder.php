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
        overflow: hidden !important;
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
    <div class="flex-1 bg-zinc-150 relative flex flex-col min-w-0 overflow-hidden">
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
    editor.BlockManager.add('cora-hero', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg><div>Cora Hero</div>`,
        content: `<section class="py-20 px-6 bg-[#FBFaf7] text-center flex flex-col items-center gap-6 border-b border-zinc-250 font-sans">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 border border-zinc-200 rounded-full text-[11px] font-semibold text-zinc-500 bg-white shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                Built in India, for Indian real estate agencies
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-zinc-950 tracking-tight leading-tight max-w-3xl mt-2">
                Run your entire agency.<br>On <span class="font-serif italic font-normal text-zinc-500">one</span> platform.
            </h1>
            <p class="text-sm md:text-base text-zinc-500 leading-relaxed max-w-2xl mt-1">
                Cora replaces the six tools your team juggles today — CRM, WhatsApp, listings, calling, payments and reports — with one AI-native system built for how Indian real estate actually works.
            </p>
            <div class="flex items-center gap-3 mt-4">
                <button onclick="window.openSignupDrawer()" class="px-6 py-3 bg-zinc-900 hover:bg-zinc-800 text-white rounded-full font-bold text-xs uppercase tracking-wider transition shadow-md flex items-center gap-2">
                    Start 30-day free trial &rarr;
                </button>
                <button onclick="window.openSignupDrawer('demo')" class="px-6 py-3 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 rounded-full font-bold text-xs uppercase tracking-wider transition">
                    Book a live demo
                </button>
            </div>
            <p class="text-[11px] text-zinc-400 mt-1">Full access. No credit card. Setup in under 10 minutes.</p>
        </section>`,
        category: 'Cora Templates'
    });

    editor.BlockManager.add('cora-dashboard-mockup', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><path d="M12 17v4M8 21h8"></path></svg><div>Dashboard Mock</div>`,
        content: `<div class="w-full bg-[#fbfbfb] border border-zinc-200 rounded-xl overflow-hidden shadow-xl max-w-5xl mx-auto my-12 font-sans text-left">
            <div class="bg-zinc-50 border-b border-zinc-200 px-4 py-3 flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
            </div>
            <div class="flex flex-col md:flex-row min-h-[440px]">
                <div class="w-full md:w-44 bg-white border-r border-zinc-200 p-5 flex flex-col gap-6">
                    <div class="flex items-center gap-2 font-bold text-sm text-zinc-900">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="#ea580c" stroke-width="2.5" fill="none" class="inline-block"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                        <span>cora</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2.5 px-3 py-2 bg-orange-50 text-orange-600 rounded-md font-semibold text-xs cursor-pointer">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                            <span>Leads</span>
                        </div>
                        <div class="flex items-center gap-2.5 px-3 py-2 text-zinc-500 rounded-md font-semibold text-xs cursor-pointer hover:bg-zinc-50">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                            <span>Inventory</span>
                        </div>
                        <div class="flex items-center gap-2.5 px-3 py-2 text-zinc-500 rounded-md font-semibold text-xs cursor-pointer hover:bg-zinc-50">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line></svg>
                            <span>Bookings</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 p-6 flex flex-col gap-5">
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-zinc-900">Leads</h2>
                            <p class="text-[11px] text-zinc-400 mt-0.5">Manage and track your leads in one place.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" class="border border-zinc-200 rounded-md px-2.5 py-1 text-[11px] w-32 bg-white" placeholder="Search leads..." readonly />
                            <button class="border border-zinc-200 bg-white rounded-md px-2.5 py-1 text-[11px] font-semibold text-zinc-700">Filters</button>
                            <button class="bg-orange-500 text-white rounded-md px-2.5 py-1 text-[11px] font-semibold border-none">+ Add Lead</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="bg-zinc-100 rounded-lg p-2.5 flex flex-col gap-2">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider flex justify-between">
                                <span>New</span>
                                <span class="bg-zinc-200 text-zinc-700 px-1.5 py-0.5 rounded-full text-[8px]">12</span>
                            </div>
                            <div class="bg-white border border-zinc-200 rounded p-2 flex flex-col gap-1 shadow-sm">
                                <span class="text-xs font-bold text-zinc-950">Aarav Sharma</span>
                                <span class="text-[10px] text-zinc-500">DLF Phase 3</span>
                            </div>
                            <div class="bg-white border border-zinc-200 rounded p-2 flex flex-col gap-1 shadow-sm">
                                <span class="text-xs font-bold text-zinc-950">Priya Patel</span>
                                <span class="text-[10px] text-zinc-500">Godrej Woods</span>
                            </div>
                        </div>
                        <div class="bg-zinc-100 rounded-lg p-2.5 flex flex-col gap-2">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider flex justify-between">
                                <span>Contacted</span>
                                <span class="bg-zinc-200 text-zinc-700 px-1.5 py-0.5 rounded-full text-[8px]">8</span>
                            </div>
                            <div class="bg-white border border-zinc-200 rounded p-2 flex flex-col gap-1 shadow-sm">
                                <span class="text-xs font-bold text-zinc-950">Rohan Verma</span>
                                <span class="text-[10px] text-zinc-500">M3M Golf Hills</span>
                            </div>
                        </div>
                        <div class="bg-zinc-100 rounded-lg p-2.5 flex flex-col gap-2">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider flex justify-between">
                                <span>Site Visit</span>
                                <span class="bg-zinc-200 text-zinc-700 px-1.5 py-0.5 rounded-full text-[8px]">5</span>
                            </div>
                        </div>
                        <div class="bg-zinc-100 rounded-lg p-2.5 flex flex-col gap-2">
                            <div class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider flex justify-between">
                                <span>Booked</span>
                                <span class="bg-zinc-200 text-zinc-700 px-1.5 py-0.5 rounded-full text-[8px]">3</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white border border-zinc-200 rounded-lg p-3.5 shadow-sm mt-2">
                        <div class="text-xs font-bold text-zinc-950 flex items-center gap-2 border-b border-zinc-100 pb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>WhatsApp AI Assistant</span>
                        </div>
                        <div class="text-[11px] text-zinc-500 mt-2 flex items-center gap-2">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="#10b981" stroke-width="2.5" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <span>This conversation is AI-powered. Cora AI is responding automatically.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>`,
        category: 'Cora Templates'
    });

    // 2.2 Define Level 04 Marketing blocks
    editor.BlockManager.add('cora-header-hero', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="6" rx="1"></rect><rect x="3" y="11" width="18" height="10" rx="1"></rect><line x1="7" y1="15" x2="17" y2="15"></line></svg><div>Hero Header</div>`,
        content: `<div class="w-full bg-white font-sans text-zinc-900 border-b border-zinc-200">
            <!-- Navigation Header -->
            <header class="border-b border-zinc-100 bg-white/90 backdrop-blur-md">
                <div class="max-w-[1200px] mx-auto px-6 py-4 flex items-center justify-between">
                    <a href="#" class="flex items-center gap-2 font-bold text-base text-zinc-950">
                        <div class="w-7 h-7 rounded bg-zinc-950 flex items-center justify-center text-white text-xs font-black">C</div>
                        <span>Cora</span>
                    </a>
                    <div class="flex items-center gap-5 text-xs font-semibold text-zinc-500">
                        <a href="#features" class="hover:text-zinc-950 transition-colors">Features</a>
                        <a href="#pricing" class="hover:text-zinc-950 transition-colors">Pricing</a>
                        <a href="#" class="hover:text-zinc-950 transition-colors">Log in</a>
                        <a href="#" class="px-3.5 py-1.5 bg-zinc-950 hover:bg-zinc-800 text-white rounded-full font-bold transition-all">Start Free</a>
                    </div>
                </div>
            </header>
            
            <!-- Hero Assembly Section -->
            <section class="py-24 px-6 text-center max-w-4xl mx-auto flex flex-col items-center gap-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-zinc-200 bg-zinc-50 text-[11px] font-semibold text-zinc-500 shadow-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Now in Public Beta — v0.1
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-zinc-950 leading-tight">
                    The AI-powered suite for <span class="text-zinc-400">modern real estate</span>
                </h1>
                <p class="text-sm md:text-base text-zinc-500 leading-relaxed max-w-2xl">
                    Listings, client pipelines, marketing automation, document generation, and intelligent closings — all unified in one platform built for agencies that move fast.
                </p>
                <div class="flex items-center gap-3 mt-3">
                    <a href="#" class="px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-semibold rounded-full uppercase tracking-wider transition shadow-sm">Start Your Free Trial</a>
                    <a href="#" class="px-6 py-3 border border-zinc-250 hover:bg-zinc-50 text-zinc-650 text-xs font-semibold rounded-full uppercase tracking-wider transition">Watch Demo</a>
                </div>
            </section>
        </div>`,
        category: 'Cora Level 04'
    });

    editor.BlockManager.add('cora-marquee-ticker', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="8" width="18" height="8" rx="1"></rect><line x1="7" y1="12" x2="17" y2="12"></line><polyline points="14 9 17 12 14 15"></polyline></svg><div>Marquee Ticker</div>`,
        content: `<section class="py-5 overflow-hidden border-y border-zinc-200 bg-zinc-50/50 font-sans">
            <div class="flex whitespace-nowrap gap-10 justify-center text-zinc-400 text-xs font-bold uppercase tracking-wider select-none">
                <span>&bull; AI Listing Generator</span>
                <span>&bull; Client Pipeline CRM</span>
                <span>&bull; Smart Scheduling</span>
                <span>&bull; Document AI</span>
                <span>&bull; Market Intelligence</span>
                <span>&bull; Photo Enhancement</span>
            </div>
        </section>`,
        category: 'Cora Level 04'
    });

    editor.BlockManager.add('cora-feature-cards', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="4" width="5" height="16" rx="1"></rect><rect x="9.5" y="4" width="5" height="16" rx="1"></rect><rect x="17" y="4" width="5" height="16" rx="1"></rect></svg><div>Feature Cards</div>`,
        content: `<section class="py-20 bg-white font-sans">
            <div class="max-w-[1200px] mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Features</div>
                    <h2 class="text-3xl font-extrabold text-zinc-950 tracking-tight">Every tool your agency needs to scale</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Feature Card 1 -->
                    <div class="p-7 border border-zinc-200 rounded-2xl bg-white hover:shadow-lg transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center mb-5 text-zinc-700">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-zinc-950 mb-2">AI Listing Generator</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">Generate compelling property descriptions, SEO-optimized titles, and social media copy from a few bullet points.</p>
                    </div>
                    <!-- Feature Card 2 -->
                    <div class="p-7 border border-zinc-200 rounded-2xl bg-white hover:shadow-lg transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center mb-5 text-zinc-700">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-zinc-950 mb-2">Smart CRM Pipeline</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">AI-scored leads, automated follow-ups, and visual deal pipelines. Know exactly which clients need attention.</p>
                    </div>
                    <!-- Feature Card 3 -->
                    <div class="p-7 border border-zinc-200 rounded-2xl bg-white hover:shadow-lg transition duration-300">
                        <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center mb-5 text-zinc-700">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-zinc-950 mb-2">Document Intelligence</h3>
                        <p class="text-xs text-zinc-500 leading-relaxed">Auto-generate contracts, disclosures, and offer letters. AI reviews clauses for risks and compliance.</p>
                    </div>
                </div>
            </div>
        </section>`,
        category: 'Cora Level 04'
    });

    editor.BlockManager.add('cora-pricing-matrix', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="6" width="5" height="12" rx="1"></rect><rect x="9.5" y="4" width="5" height="16" rx="1"></rect><rect x="17" y="6" width="5" height="12" rx="1"></rect></svg><div>Pricing Matrix</div>`,
        content: `<section class="py-20 bg-zinc-50 border-y border-zinc-200 font-sans">
            <div class="max-w-[1000px] mx-auto px-6">
                <div class="text-center mb-16">
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-400 mb-2">Pricing</div>
                    <h2 class="text-3xl font-extrabold text-zinc-950 tracking-tight">Simple plans, powerful results</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    <!-- Starter -->
                    <div class="p-7 bg-white border border-zinc-200 rounded-2xl flex flex-col">
                        <div class="text-xs font-bold text-zinc-400 mb-2">Starter</div>
                        <div class="text-3xl font-extrabold text-zinc-950 mb-1">$0<span class="text-xs font-normal text-zinc-500">/month</span></div>
                        <p class="text-[11px] text-zinc-400 mb-6">Perfect for solo agents starting out</p>
                        <a href="#" class="py-2.5 text-center text-xs font-semibold border border-zinc-250 rounded-full hover:bg-zinc-50 mb-6 text-zinc-700 transition">Get Started Free</a>
                        <ul class="space-y-3 text-xs text-zinc-600">
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"/></svg>Up to 25 listings</li>
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"/></svg>Basic CRM pipeline</li>
                        </ul>
                    </div>
                    <!-- Pro -->
                    <div class="p-7 bg-zinc-950 border border-zinc-900 rounded-2xl text-white flex flex-col relative shadow-xl scale-105">
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-2.5 py-1 bg-white text-zinc-950 text-[9px] font-extrabold rounded-full border border-zinc-200 uppercase tracking-wider">Popular</div>
                        <div class="text-xs font-bold text-zinc-400 mb-2">Professional</div>
                        <div class="text-3xl font-extrabold mb-1">$79<span class="text-xs font-normal text-zinc-400">/month</span></div>
                        <p class="text-[11px] text-zinc-400 mb-6">For growing teams ready to scale</p>
                        <a href="#" class="py-2.5 text-center text-xs font-semibold bg-white text-zinc-950 rounded-full hover:bg-zinc-100 mb-6 transition">Start 14-Day Trial</a>
                        <ul class="space-y-3 text-xs text-zinc-300">
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-white"><polyline points="20 6 9 17 4 12"/></svg>Unlimited listings</li>
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-white"><polyline points="20 6 9 17 4 12"/></svg>Full AI suite</li>
                        </ul>
                    </div>
                    <!-- Enterprise -->
                    <div class="p-7 bg-white border border-zinc-200 rounded-2xl flex flex-col">
                        <div class="text-xs font-bold text-zinc-400 mb-2">Enterprise</div>
                        <div class="text-3xl font-extrabold text-zinc-950 mb-1">Custom</div>
                        <p class="text-[11px] text-zinc-400 mb-6">For large brokerages and agencies</p>
                        <a href="#" class="py-2.5 text-center text-xs font-semibold border border-zinc-250 rounded-full hover:bg-zinc-50 mb-6 text-zinc-700 transition">Contact Sales</a>
                        <ul class="space-y-3 text-xs text-zinc-600">
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"/></svg>Everything in Pro</li>
                            <li class="flex items-center gap-2"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-500"><polyline points="20 6 9 17 4 12"/></svg>Custom AI training</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>`,
        category: 'Cora Level 04'
    });

    editor.BlockManager.add('cora-cta-banner', {
        label: `<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="2" y="5" width="20" height="14" rx="2"></rect><rect x="9" y="13" width="6" height="3" rx="0.5"></rect></svg><div>CTA Banner</div>`,
        content: `<section class="py-24 bg-white relative font-sans">
            <div class="max-w-3xl mx-auto text-center px-6">
                <h2 class="text-3xl md:text-4xl font-extrabold text-zinc-950 mb-4">Ready to transform your real estate business?</h2>
                <p class="text-zinc-500 text-sm mb-8 leading-relaxed">Join 50+ agencies already using Cora to close more deals, save time, and deliver exceptional client experiences.</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="#" class="px-6 py-3 bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-semibold rounded-full uppercase tracking-wider transition">Start Free Trial</a>
                    <a href="#" class="px-6 py-3 border border-zinc-200 hover:bg-zinc-50 text-zinc-650 text-xs font-semibold rounded-full uppercase tracking-wider transition">Schedule Demo</a>
                </div>
            </div>
        </section>`,
        category: 'Cora Level 04'
    });

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
        content: '<img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=800&q=80" class="w-full h-auto object-cover max-w-full" alt="Villa Exterior Placeholder" loading="lazy">',
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
