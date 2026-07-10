<?php
/**
 * Cora Real Estate CRM - Module 3: Appearance & Navigation Menu Builder
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$active_theme = wp_get_theme();
$menus = wp_get_nav_menus();
$selected_menu_id = isset( $_GET['menu_id'] ) ? intval( $_GET['menu_id'] ) : ( ! empty( $menus ) ? $menus[0]->term_id : 0 );
$menu_items = $selected_menu_id ? wp_get_nav_menu_items( $selected_menu_id ) : array();
$pages = get_pages();
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle>
                <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle>
                <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle>
                <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle>
                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">Appearance & Navigation Builder</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Customize active theme identity, logos, and build drag-and-drop site menus.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-primary px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraSaveAppearanceSettings()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            Save All Settings
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Theme Status & Brand Identity -->
    <div class="space-y-6">
        <!-- Active Theme Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Active Theme Architecture</h3>
                <span class="px-2 py-0.5 bg-zinc-900 text-white text-[10px] font-bold rounded uppercase">Active</span>
            </div>
            <div class="space-y-2">
                <h4 class="text-base font-bold text-zinc-900"><?php echo esc_html( $active_theme->get( 'Name' ) ); ?> <span class="text-xs font-normal text-zinc-500">v<?php echo esc_html( $active_theme->get( 'Version' ) ); ?></span></h4>
                <p class="text-xs text-zinc-600 leading-relaxed"><?php echo esc_html( $active_theme->get( 'Description' ) ? $active_theme->get( 'Description' ) : 'Studio-Grade Agency Minimalist Suite.' ); ?></p>
            </div>
            <div class="pt-2 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-500 font-mono">
                <span>Author: <?php echo esc_html( $active_theme->get( 'Author' ) ? $active_theme->get( 'Author' ) : 'Cora AI' ); ?></span>
                <a href="<?php echo esc_url( admin_url('site-editor.php') ); ?>" target="_blank" class="font-bold text-zinc-900 hover:underline flex items-center gap-1">
                    <span>Full Site Editor</span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                </a>
            </div>
        </div>

        <!-- Brand Identity & Logo Settings -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
            <div class="border-b border-zinc-100 pb-3">
                <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Brand Identity & Assets</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Site Tagline / Subtitle</label>
                    <input type="text" id="cora-brand-tagline" value="<?php echo esc_attr( get_bloginfo('description') ); ?>" placeholder="e.g. Luxury Real Estate & Property Management" class="w-full bg-white border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Agency Logo URL</label>
                    <div class="flex gap-2">
                        <input type="url" id="cora-brand-logo-url" value="<?php echo esc_url( get_option('cora_brand_logo_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                        <button class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-logo-url')">Browse</button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-800 mb-1">Favicon / Icon URL (32x32)</label>
                    <div class="flex gap-2">
                        <input type="url" id="cora-brand-favicon-url" value="<?php echo esc_url( get_option('cora_brand_favicon_url', '') ); ?>" placeholder="https://..." class="flex-1 bg-white border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-900">
                        <button class="px-3 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenMediaSelector('cora-brand-favicon-url')">Browse</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Navigation Menu Builder -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-zinc-100 pb-4">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900">Navigation Menu Structure</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Select a menu to edit or create new custom navigation hierarchies.</p>
                </div>
                <div class="flex items-center gap-2">
                    <select id="cora-nav-menu-select" class="bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-zinc-900 focus:outline-none" onchange="window.location.href='?page=cora-workspace&sub=appearance&menu_id='+this.value">
                        <?php if ( empty( $menus ) ) : ?>
                            <option value="0">Default Primary Menu</option>
                        <?php else : ?>
                            <?php foreach ( $menus as $m ) : ?>
                                <option value="<?php echo esc_attr( $m->term_id ); ?>" <?php selected( $selected_menu_id, $m->term_id ); ?>><?php echo esc_html( $m->name ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button class="px-3 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-semibold text-xs rounded-lg transition-colors cursor-pointer" onclick="coraOpenNewMenuDrawer()">+ Create Menu</button>
                </div>
            </div>

            <!-- Menu Items List -->
            <div id="cora-menu-items-list" class="space-y-2 min-h-[220px]">
                <?php if ( empty( $menu_items ) ) : ?>
                    <div class="p-10 border border-dashed border-zinc-200 rounded-xl text-center flex flex-col items-center justify-center gap-2 bg-zinc-50/50">
                        <span class="text-zinc-400">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        </span>
                        <p class="text-xs font-bold text-zinc-700">No navigation items found in this menu</p>
                        <p class="text-[11px] text-zinc-500">Click "+ Add Menu Item" below to link Pages, Blog Categories, or Custom URLs.</p>
                    </div>
                <?php else : ?>
                    <?php foreach ( $menu_items as $item ) : ?>
                    <div class="flex items-center justify-between p-3.5 bg-zinc-50 hover:bg-zinc-100/80 border border-zinc-200/70 rounded-lg transition-colors group" data-item-id="<?php echo esc_attr( $item->ID ); ?>">
                        <div class="flex items-center gap-3">
                            <span class="cursor-move text-zinc-400 hover:text-zinc-800">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            </span>
                            <div>
                                <span class="text-xs font-bold text-zinc-900"><?php echo esc_html( $item->title ); ?></span>
                                <span class="ml-2 text-[10px] text-zinc-500 font-mono"><?php echo esc_url( $item->url ); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 bg-white px-2 py-0.5 border border-zinc-200 rounded"><?php echo esc_html( $item->type_label ); ?></span>
                            <button class="text-zinc-400 hover:text-red-600 p-1 transition-colors cursor-pointer" onclick="coraRemoveMenuItem(<?php echo esc_js( $item->ID ); ?>)">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Add Item Button -->
            <div class="pt-3 border-t border-zinc-100 flex items-center justify-between">
                <button class="px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer flex items-center gap-1.5" onclick="coraOpenAddMenuItemDrawer()">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Menu Link
                </button>
                <span class="text-[11px] text-zinc-400">Drag items to rearrange menu hierarchy.</span>
            </div>
        </div>
    </div>
</div>

<!-- Right-Sliding Drawer: Add Menu Item -->
<div id="cora-drawer-menu-item" class="fixed inset-y-0 right-0 z-[99999] w-full sm:w-[420px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 translate-x-full">
    <div class="cora-drawer-header p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
        <h3 class="text-base font-bold text-zinc-900 flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Navigation Item
        </h3>
        <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer p-1" onclick="coraCloseAddMenuItemDrawer()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-6 space-y-5">
        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Item Type</label>
            <select id="cora-menu-item-type" class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 font-semibold focus:outline-none" onchange="coraToggleMenuItemTypeFields(this.value)">
                <option value="page">Link to Static Page</option>
                <option value="custom">Custom URL / External Link</option>
            </select>
        </div>

        <div id="cora-field-menu-page" class="space-y-1.5">
            <label class="block text-xs font-bold text-zinc-800">Select Static Page</label>
            <select id="cora-menu-page-id" class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 font-medium focus:outline-none">
                <?php foreach ( $pages as $p ) : ?>
                    <option value="<?php echo esc_attr( $p->ID ); ?>" data-title="<?php echo esc_attr( $p->post_title ); ?>"><?php echo esc_html( $p->post_title ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="cora-field-menu-url" class="space-y-1.5 hidden">
            <label class="block text-xs font-bold text-zinc-800">Destination URL</label>
            <input type="url" id="cora-menu-custom-url" placeholder="https://example.com/property-listings" class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-bold text-zinc-800 mb-1">Navigation Label</label>
            <input type="text" id="cora-menu-item-label" placeholder="e.g. Luxury Listings or Contact Team" class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 focus:outline-none">
            <p class="text-[11px] text-zinc-400 mt-1">Leave blank to use the page's default title.</p>
        </div>
    </div>

    <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-end gap-3">
        <button class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="coraCloseAddMenuItemDrawer()">Cancel</button>
        <button class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer" onclick="coraSubmitMenuItem()">Add to Menu</button>
    </div>
</div>
