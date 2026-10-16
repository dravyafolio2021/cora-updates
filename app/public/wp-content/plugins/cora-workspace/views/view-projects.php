<?php
/**
 * Cora Growth Platform — Projects & Portfolio Case Studies Management Suite
 *
 * Provides workspace-level portfolio showcase management, 4-act narrative editing,
 * 1-click publishing, proof KPI metrics, and direct sync to the Next.js frontend.
 * Conforms 100% to Cora Master Atomic Design System & Notion/Shopify monochromatic standards.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'cora_projects';

// Check if table exists, else initialize
if ( class_exists( 'Cora_Projects_Module' ) ) {
    Cora_Projects_Module::setup_database_table();
}

// Multi-tenancy: Scoped strictly to the active workspace agency_id
$agency_id = function_exists( 'cora_db_get_agency_id' ) ? strval( cora_db_get_agency_id() ) : '1';

// Fetch projects for this workspace
$projects_raw = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE agency_id = %s ORDER BY is_featured DESC, sort_order ASC, created_at DESC",
        $agency_id
    ),
    ARRAY_A
);

if ( empty( $projects_raw ) ) {
    $projects_raw = array();
}

// Calculate real, verifiable workspace portfolio metrics (Zero fabricated data)
$total_projects     = count( $projects_raw );
$published_count    = 0;
$drafts_count       = 0;
$featured_count     = 0;
$testimonials_count = 0;
$unique_services    = array();

foreach ( $projects_raw as $p ) {
    if ( ( $p['status'] ?? 'draft' ) === 'published' ) {
        $published_count++;
    } else {
        $drafts_count++;
    }
    if ( ! empty( $p['is_featured'] ) ) {
        $featured_count++;
    }
    if ( ! empty( $p['testimonial_quote'] ) ) {
        $testimonials_count++;
    }
    $decoded_services = json_decode( $p['services'] ?? '[]', true );
    if ( is_array( $decoded_services ) ) {
        foreach ( $decoded_services as $s ) {
            $s_clean = trim( strval( $s ) );
            if ( $s_clean !== '' && ! in_array( $s_clean, $unique_services, true ) ) {
                $unique_services[] = $s_clean;
            }
        }
    }
}
$services_count = count( $unique_services );
$published_pct  = $total_projects > 0 ? round( ( $published_count / $total_projects ) * 100 ) : 0;

$frontend_base = home_url();

$active_ws_ctx = function_exists( 'cora_get_current_workspace_context' ) ? cora_get_current_workspace_context() : null;
$ws_slug = $active_ws_ctx['slug'] ?? ( $active_ws_ctx['id'] ?? ( isset( $_GET['industry'] ) ? sanitize_title( $_GET['industry'] ) : 'studio' ) );

// Fetch workspace clients for bidirectional Client Portal routing and client assignment
$workspace_clients = $wpdb->get_results( $wpdb->prepare(
    "SELECT id, first_name, last_name, company_name, email, portal_token 
     FROM {$wpdb->prefix}cora_clients 
     WHERE agency_id = %s 
     ORDER BY created_at DESC",
    strval( $agency_id )
), ARRAY_A );
if ( empty( $workspace_clients ) ) {
    $workspace_clients = array();
}

// Fetch initial workspace media files and folders for the unified media picker
$media_args = array(
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'posts_per_page' => 60,
    'post_mime_type' => 'image',
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$current_agency_id = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : $agency_id;
if ( $current_agency_id !== 'super' && ! empty( $current_agency_id ) ) {
    $media_args['meta_query'] = array(
        'relation' => 'OR',
        array(
            'key'     => 'cora_agency_id',
            'value'   => function_exists( 'cora_get_agency_identifiers' ) ? cora_get_agency_identifiers( $current_agency_id ) : $current_agency_id,
            'compare' => 'IN',
        ),
        array(
            'key'     => 'cora_agency_id',
            'compare' => 'NOT EXISTS',
        ),
    );
}
$media_query = new WP_Query( $media_args );
$workspace_media_files = array();
if ( ! empty( $media_query->posts ) ) {
    foreach ( $media_query->posts as $att ) {
        $full_url = wp_get_attachment_url( $att->ID );
        if ( ! $full_url ) {
            continue;
        }
        $thumb_url = wp_get_attachment_image_url( $att->ID, 'medium' ) ?: $full_url;
        $meta      = wp_get_attachment_metadata( $att->ID );
        $dims      = ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) ? ( $meta['width'] . ' × ' . $meta['height'] . ' px' ) : '';
        $filepath  = get_attached_file( $att->ID );
        $filesize  = ( $filepath && file_exists( $filepath ) ) ? size_format( filesize( $filepath ) ) : '';

        // Check if attached to folder
        $terms     = wp_get_post_terms( $att->ID, 'cora_media_folder', array( 'fields' => 'ids' ) );
        $folder_id = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? strval( $terms[0] ) : '';

        $workspace_media_files[] = array(
            'id'         => $att->ID,
            'title'      => $att->post_title ?: basename( $full_url ),
            'url'        => $full_url,
            'thumbnail'  => $thumb_url,
            'caption'    => $att->post_excerpt ?: ( $att->post_title ?: '' ),
            'dimensions' => $dims,
            'size'       => $filesize,
            'folderId'   => $folder_id,
            'date'       => get_the_date( 'd M Y', $att->ID ),
        );
    }
}

// Fetch workspace media folders
$media_folders_raw = get_terms( array(
    'taxonomy'   => 'cora_media_folder',
    'hide_empty' => false,
) );
$workspace_media_folders = array();
if ( ! is_wp_error( $media_folders_raw ) && is_array( $media_folders_raw ) ) {
    foreach ( $media_folders_raw as $t ) {
        $workspace_media_folders[] = array(
            'id'    => strval( $t->term_id ),
            'name'  => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ),
            'count' => intval( $t->count ),
        );
    }
}
?>

<!-- SCOPED STYLING FOR PORTFOLIO MODULE -->
<style>
#cora-projects-module {
    position: relative;
    font-family: var(--cora-font-sans, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif);
}

/* WORKSPACE MEDIA MODULE ASSET PICKER MODAL & SHEET */
#cora-media-library-modal {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(9, 9, 11, 0.65) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 100080 !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 16px !important;
    box-sizing: border-box !important;
}
#cora-media-library-modal.open {
    display: flex !important;
}
@media (max-width: 640px) {
    #cora-media-library-modal {
        align-items: flex-end !important;
        padding: 0 !important;
    }
}
#cora-media-picker-card {
    width: 100% !important;
    max-width: 900px !important;
    max-height: 85vh !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
}
.dark #cora-media-picker-card {
    background: #18181b;
}
@media (max-width: 640px) {
    #cora-media-picker-card {
        max-width: 100vw !important;
        max-height: 90vh !important;
        border-radius: 24px 24px 0 0 !important;
    }
}

/* =========================================================================
   EDGE-TO-EDGE FULL-VIEWPORT DRAWER & BACKDROP ARCHITECTURE (RULE 1 & RULE 12)
   ========================================================================= */

/* =========================================================================
   SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD)
   - Bottom-slide motion when opened: transform: translateY(100%) -> translateY(0)
   - Zero overlay or floating blur: no backdrop element
   - Docks directly below the header: top: var(--cora-main-top, 52px); height: calc(100vh - 52px);
   - Occupies the EXACT available workspace width (based on sidebar status)
   - Zero shadow, square corners (border-radius: 0)
   - Clear formal structural borders: border-left & border-top
   - Sidebar remains 100% visible, clickable, and accessible at all times
   ========================================================================= */

:root {
    --cora-main-left: 16rem;
    --cora-main-top: 52px;
    --cora-main-width: calc(100vw - 16rem);
    --cora-main-height: calc(100vh - 52px);
}

/* MAIN-AREA DOCKED DRAWER PANEL (Shopify Inspired - Canvas Docked) */
#cora-project-drawer {
    position: fixed !important;
    top: var(--cora-main-top, 52px) !important;
    bottom: 0 !important;
    left: var(--cora-main-left, 16rem) !important;
    right: 0 !important;
    width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    max-width: var(--cora-main-width, calc(100vw - 16rem)) !important;
    height: var(--cora-main-height, calc(100vh - 52px)) !important;
    max-height: var(--cora-main-height, calc(100vh - 52px)) !important;
    margin: 0 !important;
    padding: 0 !important;
    border-radius: 0 !important;
    border-top: 1px solid #e4e4e7 !important;
    border-left: 1px solid #e4e4e7 !important;
    border-right: none !important;
    border-bottom: none !important;
    background-color: #ffffff !important;
    z-index: 40 !important; /* Kept strictly below sidebar (z-index: 60) and topbar (z-index: 9999) */
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
    transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1), width 0.2s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.2s cubic-bezier(0.4, 0, 0.2, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

.dark #cora-project-drawer {
    background-color: #18181b !important;
    border-top: 1px solid #27272a !important;
    border-left: 1px solid #27272a !important;
}

/* SIDEBAR COLLAPSED: Dynamically adjust to 4rem (64px / w-16) on desktop */
@media (min-width: 1024px) {
    body:has(.cora-sidebar.collapsed-sidebar) #cora-project-drawer:not(.maximized),
    body.cora-sidebar-collapsed #cora-project-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main #cora-project-drawer:not(.maximized),
    #cora-project-drawer.sidebar-collapsed:not(.maximized) {
        left: 4rem !important;
        width: calc(100vw - 4rem) !important;
        max-width: calc(100vw - 4rem) !important;
    }
}

/* MAXIMIZED FULLSCREEN VIEW (Shopify Expand ⤢ Mode) */
#cora-project-drawer.maximized {
    left: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    border-left: none !important;
    z-index: 70 !important;
}

/* MOBILE / TABLET (< 1024px): Full viewport width */
@media (max-width: 1023px) {
    #cora-project-drawer,
    body:has(.cora-sidebar.collapsed-sidebar) #cora-project-drawer,
    body.cora-sidebar-collapsed #cora-project-drawer,
    #cora-project-drawer.sidebar-collapsed {
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        border-left: none !important;
        z-index: 10000 !important;
    }
    body:has(#cora-project-drawer.open) #cora-mobile-floating-island,
    body:has(#cora-project-drawer:not(.collapsed)) #cora-mobile-floating-island {
        transform: translateY(150%) !important;
        opacity: 0 !important;
        pointer-events: none !important;
        display: none !important;
    }
}

/* OPEN & COLLAPSED STATES (BOTTOM SLIDE MOTION) */
#cora-project-drawer.open:not(.collapsed) {
    transform: translateY(0) !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

#cora-project-drawer.collapsed,
#cora-project-drawer:not(.open) {
    transform: translateY(100%) !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    display: none !important;
}

.cora-proj-tab-panel,
.cora-proj-step-panel {
    display: none;
    max-width: 82rem;
    margin-left: auto;
    margin-right: auto;
    width: 100%;
    animation: coraFadeInStep 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.cora-no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.cora-no-scrollbar::-webkit-scrollbar {
    display: none;
}
.cora-proj-tab-panel.active,
.cora-proj-step-panel.active { display: block; }

/* 2-Column SaaS Workspace Grid Architecture */
.cora-2col-grid-equal {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
    align-items: start !important;
}
.cora-2col-grid-equal.items-stretch {
    align-items: stretch !important;
}
@media (min-width: 1024px) {
    .cora-2col-grid-equal {
        grid-template-columns: 1fr 1fr !important;
        gap: 1.5rem !important;
    }
}

.cora-star-btn {
    background: transparent;
    border: none;
    font-size: 1.05rem;
    line-height: 1;
    padding: 0 1px;
    cursor: pointer;
    transition: transform 0.12s ease;
    user-select: none;
}
.cora-star-btn:hover {
    transform: scale(1.2);
}

.cora-2col-grid-7-5 {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
    align-items: start !important;
}
@media (min-width: 1024px) {
    .cora-2col-grid-7-5 {
        grid-template-columns: 7fr 5fr !important;
        gap: 1.5rem !important;
    }
}

/* Header 3-Part Alignment: Left Title, Center Stepper, Extreme Right Buttons */
@media (max-width: 1023px) {
    .cora-drawer-header-left {
        order: 1 !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .cora-drawer-header-right {
        order: 2 !important;
        margin-left: auto !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .cora-drawer-header-center {
        order: 3 !important;
        width: 100% !important;
        flex: 0 0 100% !important;
    }
}
@media (min-width: 1024px) {
    .cora-drawer-header-left {
        order: 1 !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
    }
    .cora-drawer-header-center {
        order: 2 !important;
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto !important;
    }
    .cora-drawer-header-right {
        order: 3 !important;
        flex: 1 1 0% !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        margin-left: auto !important;
    }
}

/* Edge-to-Edge Sticky Bottom Footer */
#cora-project-drawer-footer {
    position: sticky !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    z-index: 30 !important;
}

/* Spacious Professional SaaS Form Controls (Responsive Mobile Compact + Desktop Spacious) */
.cora-input-spacious {
    height: 36px !important;
    min-height: 36px !important;
    padding: 0 0.75rem !important;
    font-size: 12px !important;
    line-height: 1.4 !important;
    border-radius: 0.625rem !important;
    border: 1px solid #e4e4e7 !important;
    background-color: #ffffff !important;
    color: #09090b !important;
    width: 100% !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    outline: none !important;
}
@media (min-width: 640px) {
    .cora-input-spacious {
        height: 42px !important;
        min-height: 42px !important;
        padding: 0 0.875rem !important;
        font-size: 13px !important;
        line-height: 1.5 !important;
        border-radius: 0.75rem !important;
    }
}
.dark .cora-input-spacious {
    border-color: #27272a !important;
    background-color: #18181b !important;
    color: #fafafa !important;
}
.cora-input-spacious:focus {
    border-color: #a1a1aa !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.08) !important;
}
.dark .cora-input-spacious:focus {
    border-color: #71717a !important;
    background-color: #18181b !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1) !important;
}
.cora-input-spacious::placeholder {
    color: #a1a1aa !important;
    font-size: 11.5px !important;
}
@media (min-width: 640px) {
    .cora-input-spacious::placeholder {
        font-size: 12px !important;
    }
}

.cora-textarea-spacious {
    min-height: 64px !important;
    padding: 0.5rem 0.75rem !important;
    font-size: 12px !important;
    line-height: 1.5 !important;
    border-radius: 0.625rem !important;
    border: 1px solid #e4e4e7 !important;
    background-color: #ffffff !important;
    color: #09090b !important;
    width: 100% !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    outline: none !important;
}
@media (min-width: 640px) {
    .cora-textarea-spacious {
        min-height: 100px !important;
        padding: 0.75rem 0.875rem !important;
        font-size: 13px !important;
        line-height: 1.6 !important;
        border-radius: 0.75rem !important;
    }
}
.dark .cora-textarea-spacious {
    border-color: #27272a !important;
    background-color: #18181b !important;
    color: #fafafa !important;
}
.cora-textarea-spacious:focus {
    border-color: #a1a1aa !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.08) !important;
}
.dark .cora-textarea-spacious:focus {
    border-color: #71717a !important;
    background-color: #18181b !important;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1) !important;
}
.cora-textarea-spacious::placeholder {
    color: #a1a1aa !important;
    font-size: 11.5px !important;
}
@media (min-width: 640px) {
    .cora-textarea-spacious::placeholder {
        font-size: 12px !important;
    }
}

/* Card Form Spacing Architecture (12px mobile, 20-22px desktop) */
.cora-form-section-card {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important; /* 12px clean gap on mobile */
}
@media (min-width: 640px) {
    .cora-form-section-card {
        gap: 1.25rem !important; /* 20px on sm+ screens */
    }
}

.cora-form-field-group {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.25rem !important;
    margin: 0 !important;
}
@media (min-width: 640px) {
    .cora-form-field-group {
        gap: 0.45rem !important;
    }
}

.cora-field-label {
    display: block !important;
    font-size: 10px !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    margin-bottom: 0.25rem !important;
    color: #52525b !important;
}
@media (min-width: 640px) {
    .cora-field-label {
        font-size: 11px !important;
        margin-bottom: 0.45rem !important;
    }
}
.dark .cora-field-label {
    color: #a1a1aa !important;
}

/* Standardized Responsive CTA Button for Inputs (36px mobile, 42px desktop) */
.cora-btn-spacious {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.35rem !important;
    height: 36px !important;
    min-height: 36px !important;
    max-height: 36px !important;
    box-sizing: border-box !important;
    padding: 0 0.875rem !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    border-radius: 0.625rem !important;
    background-color: #09090b !important;
    color: #ffffff !important;
    border: 1px solid transparent !important;
    cursor: pointer !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    transition: all 0.15s ease !important;
    white-space: nowrap !important;
    user-select: none !important;
    flex-shrink: 0 !important;
}
@media (min-width: 640px) {
    .cora-btn-spacious {
        gap: 0.375rem !important;
        height: 42px !important;
        min-height: 42px !important;
        max-height: 42px !important;
        padding: 0 1.25rem !important;
        font-size: 13px !important;
        border-radius: 0.75rem !important;
    }
}
.dark .cora-btn-spacious {
    background-color: #fafafa !important;
    color: #09090b !important;
}
.cora-btn-spacious:hover {
    background-color: #27272a !important;
    color: #ffffff !important;
}
.dark .cora-btn-spacious:hover {
    background-color: #e4e4e7 !important;
    color: #09090b !important;
}
.cora-btn-spacious:active {
    transform: scale(0.98) !important;
}

.cora-2col-grid-5-7 {
    display: grid !important;
    grid-template-columns: 1fr !important;
    gap: 1.25rem !important;
    align-items: start !important;
}
@media (min-width: 1024px) {
    .cora-2col-grid-5-7 {
        grid-template-columns: 5fr 7fr !important;
        gap: 1.5rem !important;
    }
}

@keyframes coraFadeInStep {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* AI Assistant & Field Header */
.cora-ai-field-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.35rem;
}

/* Modern Minimalist Model AI Card: White/Transparent Background with Subtle AI Gradient Outline (No AI Slop) */
.cora-ai-gradient-bar,
.cora-ai-card-modern {
    background: #ffffff !important;
    border: 1px solid rgba(139, 92, 246, 0.32) !important;
    position: relative !important;
    box-shadow: 0 1px 3px rgba(139, 92, 246, 0.05), 0 0 0 1px rgba(139, 92, 246, 0.08) inset !important;
}
.dark .cora-ai-gradient-bar,
.dark .cora-ai-card-modern {
    background: #18181b !important;
    border: 1px solid rgba(168, 85, 247, 0.35) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(168, 85, 247, 0.12) inset !important;
}

/* AI Modern Outlined Button (Clean White/Transparent with Subtle Purple Gradient Accent) */
.cora-ai-btn-purple,
.cora-ai-btn-modern {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    padding: 0.45rem 0.95rem !important;
    border-radius: 0.625rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    background: #ffffff !important;
    color: #6d28d9 !important;
    border: 1px solid rgba(139, 92, 246, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.18s ease !important;
    box-shadow: 0 1px 2px rgba(139, 92, 246, 0.08) !important;
    user-select: none !important;
}
.dark .cora-ai-btn-purple,
.dark .cora-ai-btn-modern {
    background: #18181b !important;
    color: #c084fc !important;
    border-color: rgba(168, 85, 247, 0.4) !important;
}
.cora-ai-btn-purple:hover,
.cora-ai-btn-modern:hover {
    background: rgba(139, 92, 246, 0.06) !important;
    border-color: rgba(139, 92, 246, 0.6) !important;
    box-shadow: 0 2px 6px rgba(139, 92, 246, 0.18) !important;
    transform: translateY(-0.5px) !important;
}
.dark .cora-ai-btn-purple:hover,
.dark .cora-ai-btn-modern:hover {
    background: rgba(168, 85, 247, 0.12) !important;
    border-color: rgba(168, 85, 247, 0.65) !important;
}

/* Story Accordion Architecture */
.cora-story-accordion {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important;
}
.cora-accordion-card {
    border-radius: 1rem !important;
    border: 1px solid #e4e4e7 !important;
    background-color: #ffffff !important;
    overflow: hidden !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
}
.dark .cora-accordion-card {
    border-color: #27272a !important;
    background-color: #18181b !important;
}
.cora-accordion-card.open {
    border-color: #d4d4d8 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04) !important;
}
.dark .cora-accordion-card.open {
    border-color: #3f3f46 !important;
}

.cora-accordion-trigger {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0.625rem 0.85rem !important;
    background: transparent !important;
    border: none !important;
    cursor: pointer !important;
    user-select: none !important;
    text-align: left !important;
    transition: background-color 0.15s ease !important;
}
@media (min-width: 640px) {
    .cora-accordion-trigger {
        padding: 0.875rem 1.125rem !important;
    }
}
.cora-accordion-trigger:hover {
    background-color: #fafafa !important;
}
.dark .cora-accordion-trigger:hover {
    background-color: #202023 !important;
}

.cora-accordion-chevron {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.cora-accordion-card.open .cora-accordion-chevron {
    transform: rotate(180deg) !important;
}

.cora-accordion-content {
    max-height: 0 !important;
    opacity: 0 !important;
    overflow: hidden !important;
    transition: max-height 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease, padding 0.2s ease !important;
    padding: 0 0.85rem !important;
}
@media (min-width: 640px) {
    .cora-accordion-content {
        padding: 0 1.125rem !important;
    }
}
.cora-accordion-card.open .cora-accordion-content {
    max-height: 800px !important;
    opacity: 1 !important;
    overflow: visible !important;
    padding: 0 0.85rem 0.85rem 0.85rem !important;
}
@media (min-width: 640px) {
    .cora-accordion-card.open .cora-accordion-content {
        padding: 0 1.125rem 1.125rem 1.125rem !important;
    }
}

.cora-ai-btn-ghost-purple {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.55rem;
    border-radius: 0.5rem;
    font-size: 0.6875rem;
    font-weight: 600;
    line-height: 1;
    background: rgba(124, 58, 237, 0.08) !important;
    color: #6d28d9 !important;
    border: 1px solid rgba(124, 58, 237, 0.25) !important;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}
.cora-ai-btn-ghost-purple:hover {
    background: rgba(124, 58, 237, 0.16) !important;
    color: #5b21b6 !important;
    border-color: rgba(124, 58, 237, 0.45) !important;
}
.dark .cora-ai-btn-ghost-purple {
    background: rgba(124, 58, 237, 0.18) !important;
    color: #c4b5fd !important;
    border-color: rgba(139, 92, 246, 0.35) !important;
}
.dark .cora-ai-btn-ghost-purple:hover {
    background: rgba(124, 58, 237, 0.28) !important;
    color: #e0e7ff !important;
    border-color: rgba(139, 92, 246, 0.6) !important;
}
.cora-ai-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.55rem;
    border-radius: 0.5rem;
    font-size: 0.6875rem;
    font-weight: 600;
    line-height: 1;
    background: rgba(124, 58, 237, 0.08);
    color: #6d28d9;
    border: 1px solid rgba(124, 58, 237, 0.25);
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}
.cora-ai-btn:hover {
    background: rgba(124, 58, 237, 0.16);
    color: #5b21b6;
    border-color: rgba(124, 58, 237, 0.45);
}
.dark .cora-ai-btn {
    background: rgba(124, 58, 237, 0.18);
    color: #c4b5fd;
    border-color: rgba(139, 92, 246, 0.35);
}
.dark .cora-ai-btn:hover {
    background: rgba(124, 58, 237, 0.28);
    color: #e0e7ff;
    border-color: rgba(139, 92, 246, 0.6);
}
.cora-ai-btn.loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Rich Text Formatting Toolbar */
.cora-richtext-toolbar {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 3px 6px;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    border-bottom: none;
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
}
.dark .cora-richtext-toolbar {
    background: #27272a;
    border-color: #3f3f46;
}
.cora-richtext-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 4px;
    border-radius: 0.375rem;
    background: transparent;
    border: none;
    color: #52525b;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.12s ease;
    line-height: 1;
}
.cora-richtext-btn:hover {
    background: #e4e4e7;
    color: #09090b;
}
.dark .cora-richtext-btn {
    color: #a1a1aa;
}
.dark .cora-richtext-btn:hover {
    background: #3f3f46;
    color: #ffffff;
}
.cora-textarea-with-toolbar {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    margin-top: 0 !important;
}

/* Story Type Selector Segmented Control */
.cora-story-type-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 0.5rem;
    font-size: 0.6875rem;
    font-weight: 500;
    color: #71717a;
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cora-story-type-pill:hover {
    color: #18181b;
    background: rgba(0, 0, 0, 0.04);
}
.dark .cora-story-type-pill {
    color: #a1a1aa;
}
.dark .cora-story-type-pill:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
}
.cora-story-type-pill.active {
    font-weight: 700;
    color: #09090b;
    background: #ffffff;
    border-color: #e4e4e7;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
.dark .cora-story-type-pill.active {
    color: #ffffff;
    background: #18181b;
    border-color: #3f3f46;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Spacious Metric Repeater Card */
.cora-kpi-item-card {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 0.875rem;
    padding: 1rem;
    transition: all 0.15s ease;
}
.dark .cora-kpi-item-card {
    background: #18181b;
    border-color: #27272a;
}
.cora-kpi-item-card:hover {
    border-color: #d4d4d8;
}
.dark .cora-kpi-item-card:hover {
    border-color: #3f3f46;
}

.cora-ai-popover {
    position: absolute;
    right: 0;
    top: calc(100% + 4px);
    width: 220px;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 0.75rem;
    box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.08);
    padding: 0.35rem;
    z-index: 50;
    display: none;
}
.dark .cora-ai-popover {
    background: #18181b;
    border-color: #27272a;
    box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.35);
}
.cora-ai-popover.open {
    display: block;
    animation: coraAiPopoverFade 0.15s ease-out;
}
@keyframes coraAiPopoverFade {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.cora-ai-popover-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.4rem 0.55rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    color: #27272a;
    text-align: left;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background 0.12s ease;
}
.cora-ai-popover-item:hover {
    background: #f4f4f5;
    color: #09090b;
}
.dark .cora-ai-popover-item {
    color: #d4d4d8;
}
.dark .cora-ai-popover-item:hover {
    background: #27272a;
    color: #ffffff;
}

@keyframes coraAiShimmer {
    0% { background-color: rgba(244, 244, 245, 0.6); }
    50% { background-color: rgba(228, 228, 231, 0.9); }
    100% { background-color: rgba(244, 244, 245, 0.6); }
}
.cora-ai-processing {
    animation: coraAiShimmer 1.2s infinite ease-in-out !important;
    pointer-events: none;
}

/* Live SERP Preview Card */
.cora-serp-preview {
    background: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 0.875rem;
    padding: 1rem;
    font-family: Arial, sans-serif;
}
.dark .cora-serp-preview {
    background: #18181b;
    border-color: #27272a;
}
.cora-serp-url {
    font-size: 0.75rem;
    color: #4b5563;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.25rem;
}
.dark .cora-serp-url {
    color: #a1a1aa;
}
.cora-serp-title {
    font-size: 1.05rem;
    color: #1a0dab;
    font-weight: 500;
    line-height: 1.3;
    text-decoration: none;
    margin-bottom: 0.25rem;
    display: block;
    word-break: break-word;
}
.dark .cora-serp-title {
    color: #8ab4f8;
}
.cora-serp-desc {
    font-size: 0.8125rem;
    color: #4d5156;
    line-height: 1.45;
    word-break: break-word;
}
.dark .cora-serp-desc {
    color: #bdc1c6;
}

/* Step 4: Media Assets & Video Suite */
.cora-video-ar-btn {
    color: #71717a;
    background: transparent;
    border-radius: 0.375rem;
    font-size: 0.6875rem;
    font-family: var(--font-mono, monospace);
    font-weight: 500;
    transition: all 0.15s ease;
    cursor: pointer;
    border: none;
    outline: none;
}
.cora-video-ar-btn:hover {
    color: #18181b;
}
.dark .cora-video-ar-btn:hover {
    color: #ffffff;
}
.cora-video-ar-btn.active {
    background: #18181b !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}
.dark .cora-video-ar-btn.active {
    background: #ffffff !important;
    color: #18181b !important;
}

.cora-media-tab-btn {
    transition: all 0.15s ease;
    cursor: pointer;
    border: none;
    outline: none;
}
.cora-media-tab-btn.active {
    background: #ffffff !important;
    color: #09090b !important;
    font-weight: 700 !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06) !important;
}
.dark .cora-media-tab-btn.active {
    background: #18181b !important;
    color: #ffffff !important;
}

.cora-gallery-asset-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem;
    border-radius: 0.75rem;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    transition: all 0.15s ease;
}
.dark .cora-gallery-asset-item {
    background: #18181b;
    border-color: #27272a;
}
.cora-gallery-asset-item:hover {
    border-color: #d4d4d8;
}
.dark .cora-gallery-asset-item:hover {
    border-color: #3f3f46;
}
</style>

    <!-- 1. WORKSPACE PAGE HEADER (Level 03 Standard Organism) -->
    <?php
    $portfolio_header_args = array(
        'title'              => 'Portfolio',
        'description'        => 'Curate client case studies, transformation narratives, proof metrics, and publish to your showcase.',
        'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>',
        'ai_stack'           => true,
        'cta'                => array(
            'id'          => 'btn-new-project',
            'text'        => 'New Project',
            'mobile_text' => 'Project',
            'onclick'     => 'coraOpenProjectDrawerForCreate()',
            'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
            'visible'     => true,
        ),
        'extra_actions_html' => '<div class="cora-subtabs-toolbar flex items-center gap-1.5 sm:gap-2">
            <!-- Search Input Wrapper -->
            <div id="cora-proj-search-wrapper" class="cora-subtabs-search">
                <div class="cora-subtabs-search-inner">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="cora-subtabs-search-icon"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input 
                        type="text" 
                        id="cora-proj-search-input"
                        placeholder="Search projects..." 
                        class="cora-subtabs-search-input"
                        oninput="coraFilterProjectsList()"
                        onkeyup="coraFilterProjectsList()"
                        autocomplete="off"
                    />
                </div>
            </div>

            <!-- Level 01 Segmented Switcher (Table vs Grid) -->
            <div class="cora-subtabs-switcher">
                <button type="button" onclick="coraSetViewMode(\'table\')" id="view-mode-table-btn" class="cora-subtabs-switcher-btn active" title="Table View">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
                <button type="button" onclick="coraSetViewMode(\'grid\')" id="view-mode-grid-btn" class="cora-subtabs-switcher-btn" title="Grid View">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </button>
            </div>
        </div>',
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $portfolio_header_args );
    }
    ?>

    <div id="cora-projects-module" class="w-full flex-1 min-h-0 flex flex-col gap-4 mt-4">





    <!-- 4. NOTION / SHOPIFY MONOCHROMATIC DATA TABLE (View Mode: Table) -->
    <div id="cora-projects-table-view" class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-2xs">
        <!-- DESKTOP INTERACTIVE DATA TABLE (Zero-Horizontal-Scroll Architecture) -->
        <div class="hidden md:block overflow-hidden">
            <table class="w-full text-left border-collapse text-xs table-fixed">
                <colgroup>
                    <col class="w-[42%] lg:w-[44%]">
                    <col class="w-[18%] lg:w-[18%]">
                    <col class="w-[22%] lg:w-[21%]">
                    <col class="w-[12%] lg:w-[11%]">
                    <col class="w-[6%] lg:w-[6%]">
                </colgroup>
                <thead>
                    <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-500 dark:text-zinc-400 font-mono text-[10.5px] uppercase tracking-wider">
                        <th class="py-2.5 px-4 font-medium hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer select-none transition whitespace-nowrap" onclick="coraSortProjectsTable('title')" title="Sort by title">
                            <div class="inline-flex items-center gap-1">
                                <span>Case Study & Client</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="opacity-50"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </th>
                        <th class="py-2.5 px-4 font-medium hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer select-none transition whitespace-nowrap" onclick="coraSortProjectsTable('industry')" title="Sort by industry">
                            <div class="inline-flex items-center gap-1">
                                <span>Industry</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="opacity-50"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </th>
                        <th class="py-2.5 px-4 font-medium select-none whitespace-nowrap">Core Capabilities</th>
                        <th class="py-2.5 px-4 font-medium hover:text-zinc-900 dark:hover:text-zinc-100 cursor-pointer select-none transition whitespace-nowrap" onclick="coraSortProjectsTable('kpi')" title="Sort by outcome">
                            <div class="inline-flex items-center gap-1">
                                <span>Primary Outcome</span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="opacity-50"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </th>
                        <th class="py-2.5 px-4 font-medium text-right select-none whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody id="cora-projects-table-body" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    <?php if ( empty( $projects_raw ) ) : ?>
                    <tr id="cora-projects-empty-row">
                        <td colspan="5" class="py-16 text-center text-zinc-500">
                            <div class="max-w-md mx-auto space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mx-auto flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">No portfolio projects in this workspace</h3>
                                <p class="text-xs text-zinc-500 leading-relaxed">
                                    Create and showcase client transformation narratives to prove results and drive high-intent consultations.
                                </p>
                                <div class="flex items-center justify-center gap-2 pt-2">
                                    <button type="button" onclick="coraOpenProjectDrawerForCreate()" class="px-3.5 py-1.5 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold hover:bg-zinc-800 transition cursor-pointer">
                                        + New Project
                                    </button>
                                    <button type="button" onclick="coraLoadStarterProjects()" class="px-3.5 py-1.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-medium hover:bg-zinc-200 transition border border-zinc-200/80 dark:border-zinc-700 cursor-pointer">
                                        Load Starter Samples
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php else : ?>
                    <?php foreach ( $projects_raw as $proj ) : 
                        $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
                        $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
                        $testimonial_data = json_decode( $proj['testimonial'] ?? '[]', true ) ?: array();
                        $has_testimonial = ! empty( $testimonial_data['quote'] );
                        $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
                        $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
                    ?>
                    <tr class="transition-colors cora-project-row cursor-pointer group <?php echo $is_pub ? 'bg-white dark:bg-zinc-900 hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40' : 'bg-zinc-50/60 dark:bg-zinc-900/60 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/60'; ?>" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>" data-featured="<?php echo ! empty( $proj['is_featured'] ) ? '1' : '0'; ?>" data-has-testimonial="<?php echo $has_testimonial ? '1' : '0'; ?>" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)">
                        
                        <!-- Title, Client & Status Dot (In the Beginning) -->
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <!-- Thumbnail (Clickable to open visual assets) -->
                                <div class="shrink-0">
                                    <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-9 h-9 rounded-xl object-cover border border-zinc-200/80 dark:border-zinc-700/80 shrink-0 hover:opacity-85 transition cursor-pointer" alt="" onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 4)" title="View media assets" />
                                    <?php else : ?>
                                    <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-center font-mono font-bold text-zinc-500 text-xs shrink-0 hover:border-zinc-400 transition cursor-pointer" onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 4)" title="Upload media asset">
                                        <?php echo esc_html( substr( $proj['client_name'] ?? 'P', 0, 1 ) ); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100 text-xs block truncate group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition cursor-pointer" onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)">
                                        <?php echo esc_html( $proj['title'] ); ?>
                                    </span>
                                    <div class="flex items-center gap-1.5 text-[11px] text-zinc-500 mt-0.5">
                                        <!-- Inline Status Toggle Pill -->
                                        <button
                                            type="button"
                                            onclick="event.stopPropagation(); coraToggleProjectPublish(<?php echo esc_attr( $proj['id'] ); ?>)"
                                            class="inline-flex items-center gap-1 text-[10px] font-medium cursor-pointer hover:opacity-80 transition shrink-0 <?php echo $is_pub ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-400 dark:text-zinc-500'; ?>"
                                            title="<?php echo $is_pub ? 'Live (Click to switch to Draft)' : 'Draft (Click to Publish)'; ?>"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-emerald-500' : 'bg-zinc-400'; ?>" id="status-dot-<?php echo esc_attr( $proj['id'] ); ?>"></span>
                                            <span id="status-pill-text-<?php echo esc_attr( $proj['id'] ); ?>"><?php echo $is_pub ? 'Live' : 'Draft'; ?></span>
                                        </button>
                                        <span class="text-zinc-300 dark:text-zinc-700 select-none">•</span>
                                        <span class="hover:text-zinc-900 dark:hover:text-zinc-100 hover:underline transition cursor-pointer truncate max-w-[120px]" onclick="event.stopPropagation(); coraFilterByQuery('<?php echo esc_js( $proj['client_name'] ); ?>')" title="Filter by client"><?php echo esc_html( $proj['client_name'] ); ?></span>
                                        <?php if ( ! empty( $proj['is_confidential'] ) ) : ?>
                                        <span onclick="event.stopPropagation(); coraFilterByQuery('Stealth')" class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:opacity-80 transition cursor-pointer" title="Filter stealth projects">Stealth</span>
                                        <?php endif; ?>
                                        <?php if ( ! empty( $proj['is_featured'] ) ) : ?>
                                        <span onclick="event.stopPropagation(); coraFilterMetricCard('featured')" class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 font-semibold hover:opacity-80 transition cursor-pointer" title="Filter featured projects">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Industry (Clean UI Sans-Serif) -->
                        <td class="py-3 px-4 text-xs text-zinc-500 dark:text-zinc-400 font-normal truncate">
                            <span class="hover:text-zinc-900 dark:hover:text-zinc-100 hover:underline transition cursor-pointer" onclick="event.stopPropagation(); coraFilterByQuery('<?php echo esc_js( $proj['client_industry'] ); ?>')" title="Filter by industry"><?php echo esc_html( $proj['client_industry'] ); ?></span>
                        </td>

                        <!-- Services (Single-Line Flex Row) -->
                        <td class="py-3 px-4 overflow-hidden">
                            <div class="flex items-center gap-1.5 whitespace-nowrap overflow-hidden">
                                <?php foreach ( array_slice( $services, 0, 1 ) as $s ) : ?>
                                <span onclick="event.stopPropagation(); coraFilterByTag('<?php echo esc_js( $s ); ?>')" class="px-2 py-0.5 rounded-md text-[10.5px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 font-normal border border-zinc-200/50 dark:border-zinc-700/50 hover:bg-zinc-200 dark:hover:bg-zinc-700 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer truncate max-w-[150px]" title="Filter by <?php echo esc_attr( $s ); ?>"><?php echo esc_html( $s ); ?></span>
                                <?php endforeach; ?>
                                <?php if ( count( $services ) > 1 ) : ?>
                                <span onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)" class="text-[10px] text-zinc-400 font-mono hover:text-zinc-900 dark:hover:text-zinc-100 transition cursor-pointer shrink-0" title="View all capabilities">+<?php echo esc_html( count( $services ) - 1 ); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Primary KPI (Sleek Inline Typography - Zero Box Bloat) -->
                        <td class="py-3 px-4 truncate">
                            <?php if ( $primary_kpi ) : ?>
                            <div onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 3)" class="inline-flex items-center gap-1.5 hover:opacity-80 transition cursor-pointer truncate" title="Edit proven results & KPIs">
                                <span class="font-mono font-bold text-xs text-zinc-950 dark:text-zinc-50 shrink-0"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate max-w-[130px]"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                            </div>
                            <?php else : ?>
                            <span onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 3)" class="text-zinc-400 font-mono text-[11px] hover:text-zinc-900 dark:hover:text-zinc-200 cursor-pointer" title="Add outcome metrics">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Row Actions (Optimized 3-Button Layout: Edit, View Live, More Options Popover) -->
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity duration-150">
                                <!-- Edit Drawer -->
                                <button
                                    type="button"
                                    onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                    title="Edit Project"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>

                                <!-- View Live -->
                                <a
                                    href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>"
                                    target="_blank"
                                    onclick="event.stopPropagation();"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                    title="View on Frontend"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>

                                <?php 
                                    $row_portal_token = sanitize_title( $proj['client_name'] );
                                    foreach ( $workspace_clients as $wc ) {
                                        $wc_name = trim( ( $wc['first_name'] ?? '' ) . ' ' . ( $wc['last_name'] ?? '' ) );
                                        if ( ! empty( $wc['portal_token'] ) && ( strtolower( $wc_name ) === strtolower( $proj['client_name'] ) || strtolower( $wc['company_name'] ?? '' ) === strtolower( $proj['client_name'] ) || intval( $wc['id'] ) === intval( $proj['client_id'] ?? 0 ) ) ) {
                                            $row_portal_token = $wc['portal_token'];
                                            break;
                                        }
                                    }
                                    $row_portal_url = home_url( '/' . $ws_slug . '/client-portal?token=' . urlencode( $row_portal_token ) );
                                ?>
                                <!-- Personalised Client Portal Quick-Launch -->
                                <a
                                    href="<?php echo esc_url( $row_portal_url ); ?>"
                                    target="_blank"
                                    onclick="event.stopPropagation();"
                                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                    title="Open Personalised Client Portal for <?php echo esc_attr( $proj['client_name'] ); ?>"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </a>

                                <!-- More Options Dropdown Container -->
                                <div class="relative inline-block text-left cora-actions-dropdown-container">
                                    <button
                                        type="button"
                                        onclick="event.stopPropagation(); coraToggleRowMenu(<?php echo esc_attr( $proj['id'] ); ?>)"
                                        class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                                        title="More Actions"
                                    >
                                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                                    </button>
                                    <div id="cora-row-menu-<?php echo esc_attr( $proj['id'] ); ?>" class="cora-row-menu hidden absolute right-0 mt-1 w-48 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-lg py-1 z-50 text-xs">
                                        <!-- Open Client Portal -->
                                        <a href="<?php echo esc_url( $row_portal_url ); ?>" target="_blank" onclick="event.stopPropagation(); coraCloseAllRowMenus();" class="w-full flex items-center gap-2 px-3 py-1.5 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-left cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                            <span>Open Client Portal ↗</span>
                                        </a>
                                        <!-- Copy Link -->
                                        <button type="button" onclick="event.stopPropagation(); coraCloseAllRowMenus(); coraCopyProjectUrl('<?php echo esc_js( $proj['slug'] ); ?>')" class="w-full flex items-center gap-2 px-3 py-1.5 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-left cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            <span>Copy Public Link</span>
                                        </button>
                                        <!-- Duplicate -->
                                        <button type="button" onclick="event.stopPropagation(); coraCloseAllRowMenus(); coraDuplicateProject(<?php echo esc_attr( $proj['id'] ); ?>)" class="w-full flex items-center gap-2 px-3 py-1.5 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-left cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                            <span>Duplicate Project</span>
                                        </button>
                                        <!-- Toggle Status -->
                                        <button type="button" onclick="event.stopPropagation(); coraCloseAllRowMenus(); coraToggleProjectPublish(<?php echo esc_attr( $proj['id'] ); ?>)" class="w-full flex items-center gap-2 px-3 py-1.5 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-left cursor-pointer">
                                            <span class="w-2 h-2 rounded-full <?php echo $is_pub ? 'bg-zinc-400' : 'bg-emerald-500'; ?>"></span>
                                            <span><?php echo $is_pub ? 'Switch to Draft' : 'Publish Live'; ?></span>
                                        </button>
                                        <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                                        <!-- Delete -->
                                        <button type="button" onclick="event.stopPropagation(); coraCloseAllRowMenus(); coraDeleteProject(<?php echo esc_attr( $proj['id'] ); ?>)" class="w-full flex items-center gap-2 px-3 py-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition text-left cursor-pointer">
                                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            <span>Delete Project</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESPONSIVE ZERO-HORIZONTAL-SCROLL CARDS (Level 03 Organism) -->
        <div class="block md:hidden divide-y divide-zinc-100 dark:divide-zinc-800" id="cora-projects-mobile-list">
            <?php if ( empty( $projects_raw ) ) : ?>
            <div class="p-8 text-center text-zinc-500 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 mx-auto flex items-center justify-center">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">No portfolio projects</h3>
                <p class="text-xs text-zinc-500">Create client case studies to prove results and drive bookings.</p>
                <button type="button" onclick="coraOpenProjectDrawerForCreate()" class="px-4 py-2 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 text-xs font-semibold">
                    + New Project
                </button>
            </div>
            <?php else : ?>
            <?php foreach ( $projects_raw as $proj ) : 
                $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
                $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
                $testimonial_data = json_decode( $proj['testimonial'] ?? '[]', true ) ?: array();
                $has_testimonial = ! empty( $testimonial_data['quote'] );
                $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
                $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
            ?>
            <div class="p-3.5 space-y-2 cora-project-row cursor-pointer transition-all hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 active:bg-zinc-100/60 dark:active:bg-zinc-800/60 select-none" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>" data-featured="<?php echo ! empty( $proj['is_featured'] ) ? '1' : '0'; ?>" data-has-testimonial="<?php echo $has_testimonial ? '1' : '0'; ?>" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)">
                <!-- Row 1: Thumbnail + Title/Client/Industry + Live/Draft Pill -->
                <div class="flex items-start gap-3">
                    <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                    <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-11 h-11 rounded-xl object-cover border border-zinc-200/80 dark:border-zinc-700/80 shrink-0 hover:opacity-85 transition cursor-pointer" alt="" onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 4)" title="View media assets" />
                    <?php else : ?>
                    <div class="w-11 h-11 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-center font-mono font-bold text-zinc-600 dark:text-zinc-300 text-xs shrink-0 hover:border-zinc-400 transition cursor-pointer" onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 4)" title="Upload media asset">
                        <?php echo esc_html( substr( $proj['client_name'] ?? 'P', 0, 1 ) ); ?>
                    </div>
                    <?php endif; ?>

                    <div class="flex-1 min-w-0 pr-1">
                        <div class="flex items-center gap-1.5 leading-none mb-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition truncate cursor-pointer" onclick="event.stopPropagation(); coraFilterByQuery('<?php echo esc_js( $proj['client_name'] ); ?>')"><?php echo esc_html( $proj['client_name'] ); ?></span>
                            <?php if ( ! empty( $proj['is_featured'] ) ) : ?>
                            <span onclick="event.stopPropagation(); coraFilterMetricCard('featured')" class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold cursor-pointer">Featured</span>
                            <?php endif; ?>
                            <?php if ( ! empty( $proj['is_confidential'] ) ) : ?>
                            <span onclick="event.stopPropagation(); coraFilterByQuery('Stealth')" class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-500 cursor-pointer">Stealth</span>
                            <?php endif; ?>
                        </div>
                        <h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50 leading-snug line-clamp-1 hover:text-zinc-600 dark:hover:text-zinc-300 transition">
                            <?php echo esc_html( $proj['title'] ); ?>
                        </h4>
                        <p class="text-[10.5px] text-zinc-500 mt-0.5 truncate hover:text-zinc-800 dark:hover:text-zinc-300 transition cursor-pointer" onclick="event.stopPropagation(); coraFilterByQuery('<?php echo esc_js( $proj['client_industry'] ); ?>')">
                            <?php echo esc_html( $proj['client_industry'] ); ?>
                        </p>
                    </div>

                    <!-- Status Pill (Tap to Toggle) -->
                    <button
                        type="button"
                        onclick="event.stopPropagation(); coraToggleProjectPublish(<?php echo esc_attr( $proj['id'] ); ?>)"
                        class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium transition cursor-pointer <?php echo $is_pub ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200/80 dark:border-zinc-700'; ?>"
                        title="Click to toggle publish"
                    >
                        <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-white dark:bg-zinc-900' : 'bg-zinc-400'; ?>"></span>
                        <span><?php echo $is_pub ? 'Live' : 'Draft'; ?></span>
                    </button>
                </div>

                <!-- Row 2: Metadata Badges (KPI / Capability) + Sleek Actions -->
                <div class="flex items-center justify-between gap-2 pt-1 border-t border-zinc-100/80 dark:border-zinc-800/60">
                    <div class="flex items-center gap-1.5 min-w-0 flex-1 overflow-hidden">
                        <?php if ( $primary_kpi ) : ?>
                        <div onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 3)" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-zinc-100/90 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60 hover:bg-zinc-200/80 dark:hover:bg-zinc-700 transition cursor-pointer shrink-0" title="Edit proven results & KPIs">
                            <span class="text-[11px] font-bold font-mono text-zinc-950 dark:text-zinc-50"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                            <span class="text-[9.5px] text-zinc-500 font-sans truncate max-w-[125px]"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                        </div>
                        <?php if ( ! empty( $services ) ) : ?>
                        <span onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)" class="px-1.5 py-0.5 rounded-md text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 shrink-0 hover:text-zinc-800 transition cursor-pointer" title="<?php echo esc_attr( implode( ', ', $services ) ); ?>"><?php echo esc_html( count( $services ) ); ?> services</span>
                        <?php endif; ?>

                        <?php elseif ( ! empty( $services ) ) : 
                            $first_service = $services[0];
                            $remaining_count = count( $services ) - 1;
                        ?>
                        <span onclick="event.stopPropagation(); coraFilterByTag('<?php echo esc_js( $first_service ); ?>')" class="px-2 py-0.5 rounded-md text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-medium border border-zinc-200/60 dark:border-zinc-700/60 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition cursor-pointer truncate max-w-[160px]" title="Filter by <?php echo esc_attr( $first_service ); ?>">
                            <?php echo esc_html( $first_service ); ?>
                        </span>
                        <?php if ( $remaining_count > 0 ) : ?>
                        <span onclick="event.stopPropagation(); coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>, 1)" class="text-[9.5px] text-zinc-400 font-mono shrink-0 hover:text-zinc-800 transition cursor-pointer">+<?php echo esc_html( $remaining_count ); ?></span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Clean Minimal Quick Actions -->
                    <div class="flex items-center gap-1 shrink-0">
                        <button
                            type="button"
                            onclick="event.stopPropagation(); coraCopyProjectUrl('<?php echo esc_js( $proj['slug'] ); ?>')"
                            class="p-1 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                            title="Copy Public Link"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        </button>
                        <a
                            href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>"
                            target="_blank"
                            onclick="event.stopPropagation();"
                            class="p-1 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                            title="View on Frontend"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        </a>
                        <?php 
                            $m_portal_token = sanitize_title( $proj['client_name'] );
                            foreach ( $workspace_clients as $wc ) {
                                $wc_name = trim( ( $wc['first_name'] ?? '' ) . ' ' . ( $wc['last_name'] ?? '' ) );
                                if ( ! empty( $wc['portal_token'] ) && ( strtolower( $wc_name ) === strtolower( $proj['client_name'] ) || strtolower( $wc['company_name'] ?? '' ) === strtolower( $proj['client_name'] ) || intval( $wc['id'] ) === intval( $proj['client_id'] ?? 0 ) ) ) {
                                    $m_portal_token = $wc['portal_token'];
                                    break;
                                }
                            }
                            $m_portal_url = home_url( '/' . $ws_slug . '/client-portal?token=' . urlencode( $m_portal_token ) );
                        ?>
                        <a
                            href="<?php echo esc_url( $m_portal_url ); ?>"
                            target="_blank"
                            onclick="event.stopPropagation();"
                            class="p-1 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                            title="Open Personalised Client Portal for <?php echo esc_attr( $proj['client_name'] ); ?>"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </a>
                        <button
                            type="button"
                            onclick="event.stopPropagation(); coraDuplicateProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                            class="p-1 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                            title="Duplicate"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                        <button
                            type="button"
                            onclick="event.stopPropagation(); coraDeleteProject(<?php echo esc_attr( $proj['id'] ); ?>)"
                            class="p-1 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                            title="Delete"
                        >
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 5. VISUAL PORTFOLIO CARD GRID (View Mode: Grid, Hidden by default) -->
    <div id="cora-projects-grid-view" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ( $projects_raw as $proj ) : 
            $services = json_decode( $proj['services'] ?? '[]', true ) ?: array();
            $kpis = json_decode( $proj['kpis'] ?? '[]', true ) ?: array();
            $primary_kpi = ! empty( $kpis[0] ) ? $kpis[0] : null;
            $is_pub = ( $proj['status'] ?? 'draft' ) === 'published';
        ?>
        <div class="cora-project-row bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 flex flex-col justify-between gap-3 shadow-2xs hover:shadow-md hover:border-zinc-300 dark:hover:border-zinc-700 transition group cursor-pointer" data-id="<?php echo esc_attr( $proj['id'] ); ?>" data-status="<?php echo esc_attr( $proj['status'] ); ?>" onclick="coraEditProject(<?php echo esc_attr( $proj['id'] ); ?>)">
            <div>
                <!-- Hero Image Preview -->
                <div class="aspect-video w-full rounded-xl bg-zinc-100 dark:bg-zinc-800 overflow-hidden relative border border-zinc-200/60 dark:border-zinc-800">
                    <?php if ( ! empty( $proj['hero_image_url'] ) ) : ?>
                    <img src="<?php echo esc_url( $proj['hero_image_url'] ); ?>" class="w-full h-full object-cover group-hover:scale-102 transition duration-300" alt="" />
                    <?php else : ?>
                    <div class="w-full h-full flex items-center justify-center text-zinc-400 font-mono text-xs">
                        No Visual Asset
                    </div>
                    <?php endif; ?>

                    <div class="absolute top-2.5 right-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium <?php echo $is_pub ? 'bg-zinc-950/80 text-white backdrop-blur-xs' : 'bg-zinc-100/90 text-zinc-700 border border-zinc-200/80'; ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?php echo $is_pub ? 'bg-white' : 'bg-zinc-400'; ?>"></span>
                            <?php echo $is_pub ? 'Published' : 'Draft'; ?>
                        </span>
                    </div>
                </div>

                <!-- Title & Meta -->
                <div class="mt-3 space-y-1">
                    <div class="flex items-center justify-between text-[11px] text-zinc-500">
                        <span><?php echo esc_html( $proj['client_name'] ); ?></span>
                        <span class="font-mono"><?php echo esc_html( $proj['client_industry'] ); ?></span>
                    </div>
                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 line-clamp-2 leading-snug">
                        <?php echo esc_html( $proj['title'] ); ?>
                    </h4>
                </div>
            </div>

            <!-- Footer: Primary KPI & Actions -->
            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                <?php if ( $primary_kpi ) : ?>
                <div class="font-mono text-xs text-zinc-900 dark:text-zinc-100">
                    <span class="font-bold"><?php echo esc_html( $primary_kpi['value'] ); ?></span>
                    <span class="text-[10px] text-zinc-500 font-sans"><?php echo esc_html( $primary_kpi['label'] ); ?></span>
                </div>
                <?php else : ?>
                <span class="text-[11px] text-zinc-400 font-mono">—</span>
                <?php endif; ?>

                <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                    <button type="button" onclick="coraCopyProjectUrl('<?php echo esc_js( $proj['slug'] ); ?>')" class="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition" title="Copy Link">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    </button>
                    <a href="<?php echo esc_url( $frontend_base . '/portfolio/' . $proj['slug'] ); ?>" target="_blank" class="p-1 rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition" title="View Live">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- =========================================================================
     6. MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD)
     ========================================================================= -->
<aside id="cora-project-drawer" class="collapsed">
    
    <!-- 1. UNIFIED ULTRA-COMPACT DOCKED HEADER (52px Desktop / Responsive Mobile) -->
    <div class="cora-drawer-header px-3 sm:px-6 py-2 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30">
        <div class="flex flex-wrap lg:flex-nowrap items-center min-h-[38px] lg:min-h-[44px] w-full gap-y-2">
            
            <!-- Left: Title & Status Pill (Pinned Left on both Mobile & Desktop) -->
            <div class="flex items-center gap-2 sm:gap-2.5 cora-drawer-header-left">
                <span class="hidden sm:inline-block px-1.5 py-0.5 rounded text-[9.5px] font-mono font-bold tracking-wider uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 shrink-0">Showcase</span>
                <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 truncate tracking-tight max-w-[160px] sm:max-w-none" id="cora-proj-drawer-title">New Portfolio Project</h2>
                <span id="cora-proj-header-badge" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shrink-0">Published</span>
                <span id="cora-proj-header-step" class="hidden">Step 1 of 6</span>
            </div>

            <!-- Center: Level 01 Segmented Stepper Bar (Dead Center on Desktop / Responsive Horizontal Scroll on Mobile) -->
            <div class="flex items-center justify-start sm:justify-center overflow-x-auto cora-no-scrollbar py-0.5 cora-drawer-header-center" id="cora-proj-stepper-container">
                <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 sm:gap-1.5 select-none shrink-0" id="cora-proj-stepper-bar">
                    <!-- Step 1 -->
                    <button type="button" onclick="coraGoToProjectStep(1)" id="cora-step-btn-1" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-1">1</span>
                        <span>Overview</span>
                    </button>
                    <!-- Step 2 -->
                    <button type="button" onclick="coraGoToProjectStep(2)" id="cora-step-btn-2" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-2">2</span>
                        <span>Story</span>
                    </button>
                    <!-- Step 3 -->
                    <button type="button" onclick="coraGoToProjectStep(3)" id="cora-step-btn-3" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-3">3</span>
                        <span>Results</span>
                    </button>
                    <!-- Step 4 -->
                    <button type="button" onclick="coraGoToProjectStep(4)" id="cora-step-btn-4" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-4">4</span>
                        <span>Visuals</span>
                    </button>
                    <!-- Step 5 -->
                    <button type="button" onclick="coraGoToProjectStep(5)" id="cora-step-btn-5" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-5">5</span>
                        <span>Endorsement</span>
                    </button>
                    <!-- Step 6 -->
                    <button type="button" onclick="coraGoToProjectStep(6)" id="cora-step-btn-6" class="px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge" id="cora-step-badge-6">6</span>
                        <span>SEO & CTA</span>
                    </button>
                </div>
            </div>

            <!-- Extreme Right: Actions (Maximize + Close) Pinned to Extreme Right -->
            <div class="flex items-center gap-1.5 shrink-0 cora-drawer-header-right">
                <!-- Fullscreen / Maximize Toggle Button -->
                <button 
                    type="button" 
                    onclick="coraToggleProjectDrawerMaximize()"
                    id="cora-proj-maximize-btn"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Expand / Restore Canvas"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
                </button>

                <!-- Close Button (Esc) -->
                <button 
                    type="button" 
                    onclick="coraCloseProjectDrawer()"
                    class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs active:scale-95 shrink-0"
                    title="Close Drawer (Esc)"
                >
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Drawer Form Body (Multi-Step Container - No 70px Goal Card, Form Starts Immediately) -->
    <form id="cora-project-form" class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 space-y-5 text-xs text-zinc-800 dark:text-zinc-200" onsubmit="coraSubmitProjectForm(event)">
        <input type="hidden" id="proj-field-id" name="id" value="0" />
        <input type="hidden" id="proj-field-agency-id" name="agency_id" value="<?php echo esc_attr( $agency_id ); ?>" />
        <input type="hidden" id="proj-field-links-json" name="project_links" value="[]" />
        <input type="hidden" id="proj-field-status" name="status" value="published" />

        <!-- =====================================================================
             STEP 1: VISUAL OVERVIEW & SCOPE (2-Column SaaS Workspace Grid)
             ===================================================================== -->
        <div id="proj-step-1" class="cora-proj-step-panel active">
            <div class="cora-2col-grid-7-5">
                
                <!-- LEFT COLUMN: Core Scope, Metadata & Smart Links (7 Cols) -->
                <div class="w-full space-y-3 sm:space-y-4">
                    
                    <!-- Card 1: Core Scope -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 cora-form-section-card">
                        <div class="cora-form-field-group">
                            <label class="cora-field-label">Headline / Project Title *</label>
                            <input 
                                type="text" 
                                id="proj-field-title" 
                                name="title" 
                                required 
                                oninput="coraUpdateStep1LiveCardPreview(); coraUpdateSerpPreview();"
                                placeholder="e.g. Master Production Showcase or Client Growth Transformation"
                                class="cora-input-spacious"
                            />
                        </div>

                        <!-- 2-Column Mobile & Desktop Grid for Client Name & Industry -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-4">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Client Name *</label>
                                <input 
                                    type="text" 
                                    id="proj-field-client-name" 
                                    name="client_name" 
                                    list="cora-client-names-datalist"
                                    required 
                                    oninput="coraUpdateStep1LiveCardPreview()"
                                    onchange="coraOnClientSelect(this.value)"
                                    placeholder="e.g. Rohan Verma or Apex Global"
                                    class="cora-input-spacious"
                                />
                                <input type="hidden" id="proj-field-client-id" name="client_id" value="0" />
                                <datalist id="cora-client-names-datalist">
                                    <?php foreach ( $workspace_clients as $wc ) : 
                                        $wc_full = trim( ( $wc['first_name'] ?? '' ) . ' ' . ( $wc['last_name'] ?? '' ) );
                                        if ( ! empty( $wc_full ) ) :
                                    ?>
                                        <option value="<?php echo esc_attr( $wc_full ); ?>" data-id="<?php echo esc_attr( $wc['id'] ); ?>"><?php echo esc_html( $wc_full ); ?><?php echo ! empty( $wc['company_name'] ) ? ' (' . esc_html( $wc['company_name'] ) . ')' : ''; ?></option>
                                    <?php endif; endforeach; ?>
                                </datalist>
                            </div>

                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Client Industry</label>
                                <input 
                                    type="text" 
                                    id="proj-field-client-industry" 
                                    name="client_industry" 
                                    oninput="coraUpdateStep1LiveCardPreview()"
                                    placeholder="e.g. Luxury Retail & Fashion"
                                    class="cora-input-spacious"
                                />
                            </div>
                        </div>

                        <!-- PERSONALIZED CLIENT PORTAL LINK & ACTION CARD -->
                        <div class="p-3.5 rounded-xl border border-zinc-200/90 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-2.5 my-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50">Personalised Client Portal</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    Live Sync 🟢
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-normal">
                                This project automatically syncs to the client's private portal alongside deliverables proofing, contracts, and payment ledger.
                            </p>
                            <div class="flex items-center gap-2 pt-0.5">
                                <input 
                                    type="text" 
                                    id="proj-field-client-portal-url" 
                                    readonly 
                                    value="<?php echo esc_attr( home_url( '/' . $ws_slug . '/client-portal' ) ); ?>"
                                    class="h-8 px-2.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs font-mono text-zinc-600 dark:text-zinc-300 w-full outline-none select-all" 
                                />
                                <button 
                                    type="button" 
                                    id="proj-btn-open-client-portal"
                                    onclick="coraOpenDrawerClientPortal()"
                                    class="h-8 px-3 rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-zinc-100 dark:hover:bg-white dark:text-zinc-950 text-xs font-bold transition-all shrink-0 cursor-pointer border-0 shadow-2xs flex items-center gap-1"
                                >
                                    <span>Open Portal ↗</span>
                                </button>
                                <button 
                                    type="button" 
                                    onclick="coraCopyDrawerClientPortalUrl()"
                                    class="h-8 px-3 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all shrink-0 cursor-pointer bg-white dark:bg-zinc-900"
                                >
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <!-- 2-Column Mobile & Desktop Grid for Duration & Custom Slug -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-4">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Project Duration</label>
                                <input 
                                    type="text" 
                                    id="proj-field-duration" 
                                    name="project_duration" 
                                    oninput="coraUpdateStep1LiveCardPreview()"
                                    placeholder="e.g. 6-Week Turnaround or Q3 2026"
                                    class="cora-input-spacious"
                                />
                            </div>

                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Custom URL Slug</label>
                                <input 
                                    type="text" 
                                    id="proj-field-slug" 
                                    name="slug" 
                                    oninput="coraUpdateSerpPreview()"
                                    placeholder="e.g. apex-brand-transformation"
                                    class="cora-input-spacious"
                                />
                            </div>
                        </div>

                        <div class="cora-form-field-group">
                            <label class="cora-field-label">Subtitle / Positioning Statement</label>
                            <input 
                                type="text" 
                                id="proj-field-subtitle" 
                                name="subtitle" 
                                oninput="coraUpdateStep1LiveCardPreview()"
                                placeholder="e.g. Complete commercial rebranding and multi-channel campaign rollout."
                                class="cora-input-spacious"
                            />
                        </div>

                        <div class="cora-form-field-group">
                            <label class="cora-field-label">Services & Capabilities (comma-separated)</label>
                            <input 
                                type="text" 
                                id="proj-field-services" 
                                name="services" 
                                oninput="coraUpdateStep1LiveCardPreview()"
                                placeholder="e.g. Creative Direction, Full-Stack Next.js, Headless CMS, Paid Ads"
                                class="cora-input-spacious"
                            />
                        </div>
                    </div>

                    <!-- Card 2: Smart Link Manager -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 cora-form-section-card">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Smart Link Manager</label>
                                <p class="text-[10.5px] text-zinc-500 mt-0.5">Input any URL. The engine auto-identifies social platforms and repos with verified vector icons.</p>
                            </div>
                            <span class="text-[9.5px] font-mono font-bold text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-md border border-zinc-200/60 dark:border-zinc-700/60">Auto-Detected</span>
                        </div>

                        <div class="space-y-2.5 sm:space-y-3.5">
                            <!-- Row 1: Full-width Smart URL Input -->
                            <div class="relative flex items-center w-full">
                                <div id="cora-smart-link-badge" class="absolute left-2.5 flex items-center justify-center w-5 h-5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 pointer-events-none transition-all">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    id="cora-smart-link-input" 
                                    oninput="coraOnSmartLinkInput(this.value)"
                                    onkeydown="if(event.key==='Enter'){event.preventDefault();coraAddSmartLink();}"
                                    placeholder="Paste public link: https://instagram.com/..., linkedin.com/..., youtube.com/..."
                                    class="cora-input-spacious pl-9 sm:pl-10.5 font-mono text-xs"
                                />
                            </div>

                            <!-- Row 2: Optional Custom Label + Add Button -->
                            <div class="flex items-center gap-2 sm:gap-3">
                                <input 
                                    type="text" 
                                    id="cora-smart-link-label" 
                                    onkeydown="if(event.key==='Enter'){event.preventDefault();coraAddSmartLink();}"
                                    placeholder="Optional custom label (e.g. Campaign Reel)"
                                    class="cora-input-spacious flex-1 text-xs"
                                />

                                <button 
                                    type="button" 
                                    onclick="coraAddSmartLink()" 
                                    id="cora-smart-link-add-btn"
                                    class="cora-btn-spacious shrink-0"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    <span>Add Link</span>
                                </button>
                            </div>

                            <div id="cora-smart-links-list" class="flex flex-wrap gap-1.5 sm:gap-2 pt-0.5 min-h-[28px] sm:min-h-[32px]">
                                <span class="text-[10.5px] sm:text-[11px] text-zinc-400 italic py-0.5" id="cora-smart-links-empty">No external links attached yet.</span>
                            </div>

                            <input type="hidden" id="proj-field-live-url" name="live_url" value="" />
                        </div>
                    </div>

                    <!-- Card 3: Governance Flags -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col sm:flex-row gap-3 sm:gap-5">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="checkbox" id="proj-field-confidential" name="is_confidential" value="1" class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-0" />
                            <div>
                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block">Stealth / NDA Confidential</span>
                                <span class="text-[10.5px] text-zinc-500">Omits sensitive client branding while keeping metrics visible.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="checkbox" id="proj-field-featured" name="is_featured" value="1" class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 focus:ring-0" />
                            <div>
                                <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block">Featured Project</span>
                                <span class="text-[10.5px] text-zinc-500">Pin to the top of agency portfolio.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Visual Cover & Live Public Card Preview (5 Cols) -->
                <div class="w-full space-y-3 sm:space-y-4">
                    
                    <!-- Card 1: Visual Cover & Media Asset -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3 sm:space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Visual Cover Thumbnail *</label>
                                <p class="text-[10.5px] text-zinc-500 mt-0.5">Primary visual asset featured in portfolio cards.</p>
                            </div>
                            <!-- Aspect Ratio Selector (Level 02 Molecule) -->
                            <div class="inline-flex items-center p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/70 dark:border-zinc-700 text-[10px] font-mono">
                                <button type="button" onclick="coraSetThumbnailAspectRatio('16:9')" id="aspect-btn-16-9" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer">16:9</button>
                                <button type="button" onclick="coraSetThumbnailAspectRatio('4:3')" id="aspect-btn-4-3" class="px-2 py-0.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer">4:3</button>
                                <button type="button" onclick="coraSetThumbnailAspectRatio('1:1')" id="aspect-btn-1-1" class="px-2 py-0.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer">1:1</button>
                            </div>
                        </div>

                        <!-- Dropzone / Live Thumbnail Preview -->
                        <div id="cora-thumb-dropzone" class="border border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-2.5 sm:p-3 bg-white dark:bg-zinc-900/60 flex flex-col items-center justify-center gap-2 sm:gap-2.5 transition hover:border-zinc-400 dark:hover:border-zinc-600">
                            <div id="cora-thumb-preview-wrap" class="w-full rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 relative border border-zinc-200/60 dark:border-zinc-800 flex items-center justify-center transition-all" style="aspect-ratio: 16/9;">
                                <img id="cora-thumb-preview-img" src="" class="w-full h-full object-cover hidden" alt="Thumbnail Preview" />
                                <div id="cora-thumb-empty-placeholder" class="text-center p-3 sm:p-4 text-zinc-400 space-y-1">
                                    <svg class="mx-auto" viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span class="text-[10.5px] block font-medium">No cover image uploaded</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-center gap-2 w-full">
                                <button 
                                    type="button" 
                                    onclick="coraTriggerMediaUpload('hero')" 
                                    class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-xs font-semibold transition cursor-pointer flex items-center gap-1.5"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <span>Choose Media</span>
                                </button>
                                <button 
                                    type="button" 
                                    onclick="coraRemoveHeroAsset()" 
                                    id="cora-hero-clear-btn"
                                    class="px-2.5 sm:px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 text-xs font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer hidden"
                                >
                                    Remove
                                </button>
                            </div>

                            <div class="w-full pt-0.5">
                                <input 
                                    type="url" 
                                    id="proj-field-hero-image" 
                                    name="hero_image_url" 
                                    oninput="coraOnHeroUrlInput(this.value); coraUpdateStep1LiveCardPreview();"
                                    placeholder="Or paste external media URL: https://..."
                                    class="cora-input-spacious"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Live Public Showcase Card Preview -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-3 sm:space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-wider block font-bold">Live Public Showcase Card</span>
                                <p class="text-[10.5px] text-zinc-500">How your card appears on the agency portfolio.</p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900" id="step1-preview-status-pill">Published</span>
                        </div>

                        <!-- Mockup Portfolio Card (Level 04 Organism) -->
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-2xs">
                            <div id="step1-preview-cover-box" class="w-full bg-zinc-100 dark:bg-zinc-800 relative overflow-hidden" style="aspect-ratio: 16/9;">
                                <img id="step1-preview-cover-img" src="" class="w-full h-full object-cover hidden" alt="" />
                                <div id="step1-preview-cover-empty" class="w-full h-full flex flex-col items-center justify-center text-zinc-400">
                                    <span class="text-xs font-mono font-semibold" id="step1-preview-initials">P</span>
                                </div>
                            </div>
                            <div class="p-3 sm:p-3.5 space-y-1 sm:space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-mono text-zinc-400 uppercase tracking-wider truncate" id="step1-preview-client">Apex Global Brands</span>
                                    <span class="text-[10px] text-zinc-400 font-sans" id="step1-preview-duration">Q3 2026</span>
                                </div>
                                <h4 class="text-sm font-bold text-zinc-950 dark:text-white line-clamp-1 leading-snug" id="step1-preview-title">Master Production Showcase</h4>
                                <p class="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed" id="step1-preview-subtitle">Complete commercial rebranding and multi-channel campaign rollout.</p>
                                
                                <div id="step1-preview-tags" class="flex flex-wrap gap-1 pt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Creative Direction</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Next.js</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- =====================================================================
             STEP 2: 4-ACT STORY NARRATIVE & AI COPYWRITING CO-PILOT
             ===================================================================== -->
        <div id="proj-step-2" class="cora-proj-step-panel space-y-2.5 sm:space-y-3.5">
            <style>
                #proj-step-2 .cora-story-accordion {
                    gap: 0.5rem !important;
                }
                #proj-step-2 .cora-accordion-card {
                    border-radius: 0.75rem !important;
                }
                @media (max-width: 639px) {
                    #proj-step-2 .cora-accordion-trigger {
                        padding: 0.5rem 0.75rem !important;
                    }
                    #proj-step-2 .cora-accordion-card.open .cora-accordion-content {
                        padding: 0 0.75rem 0.75rem 0.75rem !important;
                    }
                    #proj-step-2 .cora-story-textarea {
                        min-height: 64px !important;
                        font-size: 12px !important;
                        line-height: 1.45 !important;
                        padding: 0.45rem 0.625rem !important;
                    }
                }
                @media (min-width: 640px) {
                    #proj-step-2 .cora-accordion-trigger {
                        padding: 0.75rem 1rem !important;
                    }
                    #proj-step-2 .cora-accordion-card.open .cora-accordion-content {
                        padding: 0 1rem 1rem 1rem !important;
                    }
                    #proj-step-2 .cora-story-textarea {
                        min-height: 80px !important;
                        font-size: 13px !important;
                        line-height: 1.5 !important;
                        padding: 0.5rem 0.75rem !important;
                    }
                }
            </style>
            
            <!-- TOP BAR: Model AI Outline Card (Clean White/Transparent Surface with Delicate Gradient Outline) -->
            <div class="p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl cora-ai-card-modern flex flex-row items-center justify-between gap-2.5 sm:gap-3 shadow-2xs">
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-violet-50 dark:bg-violet-950/60 border border-violet-200/80 dark:border-violet-800/80 flex items-center justify-center shrink-0 text-violet-600 dark:text-violet-400 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" class="sm:w-3.5 sm:h-3.5" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] sm:text-[9px] font-mono font-bold uppercase bg-violet-100/70 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300 border border-violet-200 dark:border-violet-800 tracking-wider shrink-0">AI Co-Pilot</span>
                            <h4 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-zinc-50 tracking-tight truncate">Auto-Draft 4-Act Story</h4>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 hidden xs:block sm:block truncate">Generate high-converting case study narrative tailored to chosen archetype.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button 
                        type="button" 
                        onclick="coraAiGenerateCompleteStory()" 
                        id="btn-ai-auto-draft-story"
                        class="cora-ai-btn-modern text-[11px] sm:text-xs py-1.5 px-2.5 sm:px-3 shrink-0 whitespace-nowrap"
                    >
                        <svg viewBox="0 0 24 24" width="11" height="11" class="sm:w-3 sm:h-3" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 4V2m0 16v-2m-8-6H5m16 0h-2m-2.2-6.8l-1.4 1.4M6.6 17.4l-1.4 1.4m0-12.8l1.4 1.4m10.8 10.8l1.4 1.4"></path></svg>
                        <span>Auto-Draft All 4 Acts</span>
                    </button>
                </div>
            </div>

            <!-- STORY TYPE FRAMEWORK SELECTOR (Level 02 Molecule) -->
            <div class="p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-2 sm:gap-2.5">
                <div class="flex items-center justify-between w-full md:w-auto gap-2 shrink-0">
                    <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                        <span class="text-[9.5px] sm:text-[10px] font-mono font-bold uppercase tracking-wider text-zinc-400 shrink-0">Framework:</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate" id="cora-selected-story-type-label">Problem & Solution (PAS)</span>
                    </div>
                    <!-- Accordion Quick Controls (Mobile & Desktop) -->
                    <div class="flex items-center gap-1 px-1.5 py-0.5 sm:py-1 rounded-md sm:rounded-lg bg-zinc-100 dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 shrink-0 text-[10px] sm:text-[11px]">
                        <button type="button" onclick="coraSetAllStoryAccordions(true)" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white transition font-semibold cursor-pointer">Expand</button>
                        <span class="text-zinc-300 dark:text-zinc-700">/</span>
                        <button type="button" onclick="coraSetAllStoryAccordions(false)" class="text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white transition font-semibold cursor-pointer">Collapse</button>
                    </div>
                </div>
                <input type="hidden" id="proj-field-story-type" name="story_type" value="pas" />
                
                <div class="flex items-center gap-1 overflow-x-auto cora-no-scrollbar py-0.5 px-1 bg-zinc-100 dark:bg-zinc-900 rounded-lg sm:rounded-xl border border-zinc-200/60 dark:border-zinc-800 w-full md:w-auto">
                    <button type="button" onclick="coraSetStoryType('pas')" id="story-type-pas" class="cora-story-type-pill active">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                        <span>Problem & Solution (PAS)</span>
                    </button>
                    <button type="button" onclick="coraSetStoryType('transformation')" id="story-type-transformation" class="cora-story-type-pill">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"></path></svg>
                        <span>Before & After</span>
                    </button>
                    <button type="button" onclick="coraSetStoryType('scaling')" id="story-type-scaling" class="cora-story-type-pill">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                        <span>Growth Scaling</span>
                    </button>
                    <button type="button" onclick="coraSetStoryType('rebrand')" id="story-type-rebrand" class="cora-story-type-pill">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        <span>Brand Rebrand</span>
                    </button>
                    <button type="button" onclick="coraSetStoryType('launch')" id="story-type-launch" class="cora-story-type-pill">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"></path><path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-2.95 11A22.35 22.35 0 0 1 15 12z"></path></svg>
                        <span>Product Launch</span>
                    </button>
                    <button type="button" onclick="coraSetStoryType('custom')" id="story-type-custom" class="cora-story-type-pill">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        <span>Custom</span>
                    </button>
                </div>
            </div>

            <!-- STORY ACCORDION CONTAINER (4 Interactive Narrative Acts) -->
            <div class="cora-story-accordion" id="cora-story-accordion-container">
                
                <!-- Act 1: Executive Summary -->
                <div class="cora-accordion-card" id="story-accordion-act-1">
                    <div class="cora-accordion-trigger" onclick="coraToggleStoryAccordion(1)">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="px-1.5 py-0.5 rounded text-[9.5px] sm:text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80 shrink-0">Act 1</span>
                            <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 truncate">Executive Summary</span>
                            <span id="story-act-preview-1" class="text-[10.5px] sm:text-[11px] text-zinc-400 truncate max-w-xs hidden md:inline ml-2 font-normal"></span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <div class="relative">
                                <button type="button" onclick="coraToggleAiMenu('menu-ai-exec-summary')" class="cora-ai-btn !py-1 !px-2 !text-[10.5px]">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                    <span>AI Refine</span>
                                </button>
                                <div id="menu-ai-exec-summary" class="cora-ai-popover">
                                    <button type="button" onclick="coraAiRefineField('proj-field-exec-summary', 'polish')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polyline></svg>
                                        <div><div class="font-bold">Executive Polish</div><div class="text-[10px] text-zinc-400">Crisp, authoritative tone</div></div>
                                    </button>
                                    <button type="button" onclick="coraAiRefineField('proj-field-exec-summary', 'seo')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        <div><div class="font-bold">SEO & Keywords</div><div class="text-[10px] text-zinc-400">Search-optimized intent phrasing</div></div>
                                    </button>
                                    <button type="button" onclick="coraAiRefineField('proj-field-exec-summary', 'impact')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        <div><div class="font-bold">Highlight Impact</div><div class="text-[10px] text-zinc-400">Focus on quantifiable ROI</div></div>
                                    </button>
                                </div>
                            </div>
                            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer cora-accordion-chevron" onclick="coraToggleStoryAccordion(1)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-accordion-content space-y-2">
                        <span class="text-[10px] sm:text-[11px] text-zinc-400 block pb-0.5 leading-tight">Briefing summarizing the project premise, problem, and high-level transformation.</span>
                        <!-- Rich Text Toolbar -->
                        <div class="cora-richtext-toolbar !py-1 !px-2">
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'bold')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bold"><b>B</b></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'italic')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Italic"><i>I</i></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'bullet')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bullet List">•</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'numbered')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Numbered List">1.</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'quote')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Quote">”</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'link')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Link">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-exec-summary', 'clear')" class="cora-richtext-btn ml-auto !text-[9.5px] !h-5" title="Clear Format">Clear</button>
                        </div>
                        <textarea 
                            id="proj-field-exec-summary" 
                            name="executive_summary" 
                            rows="2" 
                            oninput="coraUpdateSerpPreview(); coraUpdateStoryActPreview(1, this.value);"
                            placeholder="Provide a concise 2-3 sentence overview of this case study..."
                            class="cora-textarea-spacious cora-story-textarea cora-textarea-with-toolbar !min-h-[64px] sm:!min-h-[80px] !text-xs sm:!text-[13px] !py-1.5 !px-2.5 sm:!py-2 sm:!px-3 !leading-snug"
                        ></textarea>
                    </div>
                </div>

                <!-- Act 2: The Challenge & Bottleneck -->
                <div class="cora-accordion-card" id="story-accordion-act-2">
                    <div class="cora-accordion-trigger" onclick="coraToggleStoryAccordion(2)">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="px-1.5 py-0.5 rounded text-[9.5px] sm:text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80 shrink-0">Act 2</span>
                            <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 truncate">The Challenge & Bottleneck</span>
                            <span id="story-act-preview-2" class="text-[10.5px] sm:text-[11px] text-zinc-400 truncate max-w-xs hidden md:inline ml-2 font-normal"></span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <div class="relative">
                                <button type="button" onclick="coraToggleAiMenu('menu-ai-challenge')" class="cora-ai-btn !py-1 !px-2 !text-[10.5px]">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                    <span>AI Refine</span>
                                </button>
                                <div id="menu-ai-challenge" class="cora-ai-popover">
                                    <button type="button" onclick="coraAiRefineField('proj-field-challenge', 'pas')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                                        <div><div class="font-bold">PAS Framework</div><div class="text-[10px] text-zinc-400">Problem & Agitation focus</div></div>
                                    </button>
                                    <button type="button" onclick="coraAiRefineField('proj-field-challenge', 'polish')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polyline></svg>
                                        <div><div class="font-bold">Sharpen Bottlenecks</div><div class="text-[10px] text-zinc-400">Clear pain-point phrasing</div></div>
                                    </button>
                                </div>
                            </div>
                            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer cora-accordion-chevron" onclick="coraToggleStoryAccordion(2)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-accordion-content space-y-2">
                        <span class="text-[10px] sm:text-[11px] text-zinc-400 block pb-0.5 leading-tight">Specific obstacles, friction points, or revenue leakage the client faced.</span>
                        <!-- Rich Text Toolbar -->
                        <div class="cora-richtext-toolbar !py-1 !px-2">
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'bold')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bold"><b>B</b></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'italic')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Italic"><i>I</i></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'bullet')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bullet List">•</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'numbered')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Numbered List">1.</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'quote')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Quote">”</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'link')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Link">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-challenge', 'clear')" class="cora-richtext-btn ml-auto !text-[9.5px] !h-5" title="Clear Format">Clear</button>
                        </div>
                        <textarea 
                            id="proj-field-challenge" 
                            name="challenge_content" 
                            rows="2" 
                            oninput="coraUpdateStoryActPreview(2, this.value);"
                            placeholder="Describe the initial bottlenecks, technical hurdles, or market saturation..."
                            class="cora-textarea-spacious cora-story-textarea cora-textarea-with-toolbar !min-h-[64px] sm:!min-h-[80px] !text-xs sm:!text-[13px] !py-1.5 !px-2.5 sm:!py-2 sm:!px-3 !leading-snug"
                        ></textarea>
                    </div>
                </div>

                <!-- Act 3: Strategic Solution & Methodology -->
                <div class="cora-accordion-card" id="story-accordion-act-3">
                    <div class="cora-accordion-trigger" onclick="coraToggleStoryAccordion(3)">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="px-1.5 py-0.5 rounded text-[9.5px] sm:text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80 shrink-0">Act 3</span>
                            <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 truncate">Strategic Solution & Methodology</span>
                            <span id="story-act-preview-3" class="text-[10.5px] sm:text-[11px] text-zinc-400 truncate max-w-xs hidden md:inline ml-2 font-normal"></span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <div class="relative">
                                <button type="button" onclick="coraToggleAiMenu('menu-ai-solution')" class="cora-ai-btn !py-1 !px-2 !text-[10.5px]">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                    <span>AI Refine</span>
                                </button>
                                <div id="menu-ai-solution" class="cora-ai-popover">
                                    <button type="button" onclick="coraAiRefineField('proj-field-solution', 'methodology')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
                                        <div><div class="font-bold">Structure Methodology</div><div class="text-[10px] text-zinc-400">Step-by-step agency process</div></div>
                                    </button>
                                    <button type="button" onclick="coraAiRefineField('proj-field-solution', 'polish')" class="cora-ai-popover-item">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-violet-600"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polyline></svg>
                                        <div><div class="font-bold">Executive Polish</div><div class="text-[10px] text-zinc-400">Crisp, authoritative tone</div></div>
                                    </button>
                                </div>
                            </div>
                            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer cora-accordion-chevron" onclick="coraToggleStoryAccordion(3)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-accordion-content space-y-2">
                        <span class="text-[10px] sm:text-[11px] text-zinc-400 block pb-0.5 leading-tight">The proprietary framework, engineering sprint, or production deployment.</span>
                        <!-- Rich Text Toolbar -->
                        <div class="cora-richtext-toolbar !py-1 !px-2">
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'bold')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bold"><b>B</b></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'italic')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Italic"><i>I</i></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'bullet')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bullet List">•</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'numbered')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Numbered List">1.</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'quote')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Quote">”</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'link')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Link">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-solution', 'clear')" class="cora-richtext-btn ml-auto !text-[9.5px] !h-5" title="Clear Format">Clear</button>
                        </div>
                        <textarea 
                            id="proj-field-solution" 
                            name="solution_content" 
                            rows="2" 
                            oninput="coraUpdateStoryActPreview(3, this.value);"
                            placeholder="Detail the strategy, engineering architecture, and execution phases..."
                            class="cora-textarea-spacious cora-story-textarea cora-textarea-with-toolbar !min-h-[64px] sm:!min-h-[80px] !text-xs sm:!text-[13px] !py-1.5 !px-2.5 sm:!py-2 sm:!px-3 !leading-snug"
                        ></textarea>
                    </div>
                </div>

                <!-- Act 4: Key Deliverables Breakdown -->
                <div class="cora-accordion-card" id="story-accordion-act-4">
                    <div class="cora-accordion-trigger" onclick="coraToggleStoryAccordion(4)">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="px-1.5 py-0.5 rounded text-[9.5px] sm:text-[10px] font-mono font-bold uppercase bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700/80 shrink-0">Act 4</span>
                            <span class="text-xs font-bold text-zinc-950 dark:text-zinc-50 truncate">Key Deliverables (one per line)</span>
                            <span id="story-act-preview-4" class="text-[10.5px] sm:text-[11px] text-zinc-400 truncate max-w-xs hidden md:inline ml-2 font-normal"></span>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <button type="button" onclick="coraAiRefineDeliverables()" class="cora-ai-btn !py-1 !px-2 !text-[10.5px]">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                <span>Clean & Format</span>
                            </button>
                            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-md flex items-center justify-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer cora-accordion-chevron" onclick="coraToggleStoryAccordion(4)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </div>
                    </div>
                    <div class="cora-accordion-content space-y-2">
                        <span class="text-[10px] sm:text-[11px] text-zinc-400 block pb-0.5 leading-tight">Concrete assets handed over upon project completion.</span>
                        <!-- Rich Text Toolbar -->
                        <div class="cora-richtext-toolbar !py-1 !px-2">
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'bold')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bold"><b>B</b></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'italic')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Italic"><i>I</i></button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'bullet')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Bullet List">•</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'numbered')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Numbered List">1.</button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'link')" class="cora-richtext-btn !min-w-[20px] !h-5 !text-[10px]" title="Link">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                            </button>
                            <button type="button" onclick="coraApplyRichFormat('proj-field-deliverables', 'clear')" class="cora-richtext-btn ml-auto !text-[9.5px] !h-5" title="Clear Format">Clear</button>
                        </div>
                        <textarea 
                            id="proj-field-deliverables" 
                            name="deliverables" 
                            rows="2" 
                            oninput="coraUpdateStoryActPreview(4, this.value);"
                            placeholder="Comprehensive Brand Style Guide&#10;Headless Next.js Web App&#10;Automated CRM Funnel Integration"
                            class="cora-textarea-spacious cora-story-textarea cora-textarea-with-toolbar !min-h-[64px] sm:!min-h-[80px] !text-xs sm:!text-[13px] !py-1.5 !px-2.5 sm:!py-2 sm:!px-3 !leading-snug"
                        ></textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- =====================================================================
             STEP 3: QUANTITATIVE PROOF KPIS (2-Column Grid - Dynamic 1..N Metrics)
             ===================================================================== -->
        <div id="proj-step-3" class="cora-proj-step-panel">
            <div class="cora-2col-grid-equal items-stretch">
                
                <!-- LEFT COLUMN: Dynamic KPI Repeater Inputs (6 Cols) -->
                <div class="w-full flex flex-col">
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5 flex-1 flex flex-col justify-between">
                        <div class="space-y-2.5 sm:space-y-3.5">
                            <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div>
                                    <div class="flex items-center gap-1.5 sm:gap-2">
                                        <label class="text-[10.5px] sm:text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block">Proof & Impact Metrics</label>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] sm:text-[9.5px] font-mono bg-zinc-200/60 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Quantitative ROI</span>
                                    </div>
                                    <p class="text-[10.5px] sm:text-[11px] text-zinc-500 mt-0.5">Quantify your agency's results with measurable, verified data points.</p>
                                </div>
                                <button 
                                    type="button" 
                                    onclick="coraAddKpiMetric()" 
                                    id="btn-add-kpi-metric"
                                    class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl bg-zinc-950 hover:bg-zinc-900 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shrink-0 shadow-2xs active:scale-98"
                                    title="Add New Metric"
                                >
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    <span>Add Metric</span>
                                </button>
                            </div>

                            <!-- Dynamic Repeater Container (Populated by coraRenderKpiInputs) -->
                            <div id="cora-kpi-repeater-container" class="space-y-2.5 sm:space-y-3">
                                <!-- Rendered dynamically -->
                            </div>
                        </div>

                        <!-- 1-Click Agency Benchmark Presets Toolbar (Fills space with actionable value) -->
                        <div class="pt-2 sm:pt-3 mt-1.5 sm:mt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="flex items-center justify-between mb-1 sm:mb-1.5">
                                <span class="text-[9.5px] sm:text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold">1-Click Benchmark Presets:</span>
                                <span class="text-[9.5px] sm:text-[10px] text-zinc-400">Click to quick-insert</span>
                            </div>
                            <div class="flex flex-wrap gap-1 sm:gap-1.5">
                                <button type="button" onclick="coraApplyKpiPreset('+340%', 'Organic Revenue Growth', 'First 90 Days Post-Launch')" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-[10.5px] font-mono font-medium bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shadow-3xs cursor-pointer transition">
                                    +340% Revenue
                                </button>
                                <button type="button" onclick="coraApplyKpiPreset('4.8x', 'ROAS Multiplier', 'Scaling from $10k to $150k/mo ad spend')" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-[10.5px] font-mono font-medium bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shadow-3xs cursor-pointer transition">
                                    4.8x ROAS
                                </button>
                                <button type="button" onclick="coraApplyKpiPreset('14 Days', 'Turnaround Velocity', 'From initial creative brief to global deployment')" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-[10.5px] font-mono font-medium bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shadow-3xs cursor-pointer transition">
                                    14 Days Velocity
                                </button>
                                <button type="button" onclick="coraApplyKpiPreset('99.4%', 'On-Time Production SLA', 'Across 12 multi-channel project milestones')" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-[10.5px] font-mono font-medium bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shadow-3xs cursor-pointer transition">
                                    99.4% SLA
                                </button>
                                <button type="button" onclick="coraApplyKpiPreset('-42%', 'Customer Acquisition Cost', 'Blended paid acquisition reduction')" class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg text-[10px] sm:text-[10.5px] font-mono font-medium bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shadow-3xs cursor-pointer transition">
                                    -42% CAC
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live High-Impact Showcase Card Preview (6 Cols) -->
                <div class="w-full flex flex-col">
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5 flex-1 flex flex-col justify-between">
                        <div class="space-y-2.5 sm:space-y-3">
                            <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div>
                                    <span class="text-[10.5px] sm:text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block">Front-End Showcase Preview</span>
                                    <p class="text-[10.5px] sm:text-[11px] text-zinc-500 mt-0.5">Live quantitative proof component embedded in public case study.</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-mono font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">Live Component</span>
                                </div>
                            </div>

                            <!-- Dynamic Showcase Preview Container (Adapts layout for 1, 2, 3, 4+ metrics) -->
                            <div id="cora-kpi-showcase-preview-card" class="rounded-xl overflow-hidden shadow-2xs">
                                <!-- Rendered dynamically by coraUpdateKpiPreview -->
                            </div>
                        </div>

                        <!-- Agency Trust & Schema SEO Verification Callout -->
                        <div class="p-2.5 sm:p-3 rounded-xl bg-zinc-100/80 dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-800/80 space-y-1 mt-1 sm:mt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10.5px] sm:text-[11px] font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600 dark:text-emerald-400"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    Google Schema & Rich Snippets Active
                                </span>
                                <span class="text-[9px] sm:text-[9.5px] font-mono font-semibold text-emerald-600 dark:text-emerald-400">QuantitativeValue</span>
                            </div>
                            <p class="text-[10px] sm:text-[10.5px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Formatted in JSON-LD schema so generative AI search engines and Google Rich Snippets surface your verified metrics directly in search citations.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- =====================================================================
             STEP 4: MEDIA ASSETS & VISUAL GALLERY STUDIO (2-Column Architecture)
             ===================================================================== -->
        <div id="proj-step-4" class="cora-proj-step-panel">
            <div class="cora-2col-grid-5-7">
                
                <!-- LEFT COLUMN: Asset Library Manager & Video Embed Suite (5 Cols) -->
                <div class="w-full space-y-3 sm:space-y-4">
                    
                    <!-- CARD A: Gallery Media Library Picker & Asset List -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5">
                        <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-zinc-200/60 dark:border-zinc-800">
                            <div>
                                <label class="text-[10.5px] sm:text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block">Visual Gallery Assets</label>
                                <p class="text-[10.5px] sm:text-[11px] text-zinc-500 mt-0.5">Select multiple high-res stills, mockups, and deliverables.</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-mono font-bold bg-zinc-200/80 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300" id="cora-gallery-count-badge">0 Assets</span>
                            </div>
                        </div>

                        <!-- Multi-Asset Picker Toolbar & Drag-Drop Zone -->
                        <div class="space-y-2 sm:space-y-2.5">
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    id="btn-open-gallery-media-modal"
                                    onclick="coraTriggerMediaUpload('gallery')" 
                                    class="flex-1 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-[11px] sm:text-xs font-semibold transition cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 shadow-2xs"
                                >
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <span>Workspace Media</span>
                                </button>
                                <button 
                                    type="button" 
                                    onclick="coraAddEditorialSamplePack()" 
                                    class="px-2 sm:px-2.5 py-1.5 sm:py-2 rounded-xl border border-zinc-200/80 dark:border-zinc-700 bg-white hover:bg-zinc-100 dark:bg-zinc-900 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-[11px] sm:text-xs font-medium transition cursor-pointer flex items-center gap-1 shrink-0"
                                    title="Quick-attach 3 recent workspace media assets"
                                >
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                    <span>Recent Assets</span>
                                </button>
                            </div>

                            <!-- Interactive Dropzone -->
                            <div 
                                id="cora-gallery-dropzone" 
                                onclick="document.getElementById('cora-gallery-file-input').click()"
                                class="p-2.5 sm:p-3.5 border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 rounded-xl bg-white/60 dark:bg-zinc-900/40 flex flex-col items-center justify-center text-center cursor-pointer transition group"
                            >
                                <input type="file" id="cora-gallery-file-input" multiple accept="image/*" class="hidden" onchange="coraHandleGalleryFileSelect(this.files)">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition mb-0.5 sm:mb-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                <span class="text-[10.5px] sm:text-[11px] font-medium text-zinc-700 dark:text-zinc-300">Drop images here or browse files</span>
                                <span class="text-[9px] sm:text-[9.5px] text-zinc-400 mt-0.5 font-mono">PNG, JPG, WebP up to 25MB each</span>
                            </div>
                        </div>

                        <!-- Selected Assets Item List / Repeater -->
                        <div class="space-y-1.5 sm:space-y-2 pt-0.5 sm:pt-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[9.5px] sm:text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold">Selected Media List</span>
                                <button type="button" onclick="coraClearAllGalleryAssets()" class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400 hover:text-red-500 transition cursor-pointer">Clear All</button>
                            </div>
                            
                            <!-- Container populated dynamically by coraRenderGalleryAssetList() -->
                            <div id="cora-gallery-selected-list" class="space-y-1.5 sm:space-y-2 max-h-[160px] sm:max-h-[220px] overflow-y-auto pr-1">
                                <!-- Rendered dynamically -->
                            </div>
                        </div>

                        <!-- Hidden serialized textarea for backward-compatibility & submission -->
                        <textarea 
                            id="proj-field-gallery" 
                            name="gallery_images" 
                            oninput="coraOnGalleryTextareaInput()"
                            class="hidden"
                        ></textarea>
                    </div>

                    <!-- CARD B: Case Study Video Embed Studio -->
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5">
                        <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-zinc-200/60 dark:border-zinc-800">
                            <div>
                                <label class="text-[10.5px] sm:text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block">Case Study Video Embed</label>
                                <p class="text-[10.5px] sm:text-[11px] text-zinc-500 mt-0.5">Cinematic reel, customer interview, or product walkthrough.</p>
                            </div>
                            <span id="cora-video-platform-badge" class="px-2 py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-mono font-bold bg-zinc-200/80 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                Vimeo / YouTube / Loom
                            </span>
                        </div>

                        <!-- Video URL Input with Live Icon Indicator -->
                        <div class="space-y-1 sm:space-y-1.5">
                            <label class="cora-field-label">Video Stream URL</label>
                            <div class="relative flex items-center">
                                <div id="cora-video-input-icon" class="absolute left-3 text-zinc-400 pointer-events-none flex items-center justify-center">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                </div>
                                <input 
                                    type="url" 
                                    id="proj-field-video-url" 
                                    name="video_url"
                                    oninput="coraOnVideoUrlInput(this.value)"
                                    placeholder="Paste Vimeo, YouTube, Loom, or .mp4 URL..."
                                    class="cora-input-spacious pl-9 pr-8 text-xs"
                                />
                                <button 
                                    type="button" 
                                    id="btn-clear-video-url" 
                                    onclick="coraClearVideoUrl()" 
                                    class="absolute right-2.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 text-xs hidden cursor-pointer"
                                    title="Clear Video URL"
                                >✕</button>
                            </div>
                            <div class="flex items-center justify-between text-[9.5px] sm:text-[10px] text-zinc-400 pt-0.5">
                                <span>Supported: Vimeo, YouTube, Loom, MP4</span>
                                <button type="button" onclick="coraLoadSampleVideo('vimeo')" class="text-zinc-600 dark:text-zinc-300 hover:underline font-mono cursor-pointer">Load Sample Reel</button>
                            </div>
                        </div>

                        <!-- Video Display Options (Aspect Ratio & Playback) -->
                        <div class="pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80 space-y-2">
                            <span class="text-[9.5px] sm:text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold block">Video Display Options</span>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Aspect Ratio Segmented Control -->
                                <div>
                                    <label class="text-[9.5px] sm:text-[10px] font-medium text-zinc-500 dark:text-zinc-400 block mb-1">Aspect Ratio</label>
                                    <div class="p-0.5 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-200/80 dark:border-zinc-800 flex items-center gap-0.5">
                                        <button type="button" onclick="coraSetVideoAspectRatio('16:9')" id="video-ar-169" class="cora-video-ar-btn flex-1 py-1 text-[9.5px] sm:text-[10px] font-mono font-medium rounded text-center transition active">16:9</button>
                                        <button type="button" onclick="coraSetVideoAspectRatio('9:16')" id="video-ar-916" class="cora-video-ar-btn flex-1 py-1 text-[9.5px] sm:text-[10px] font-mono font-medium rounded text-center transition">9:16</button>
                                        <button type="button" onclick="coraSetVideoAspectRatio('4:3')" id="video-ar-43" class="cora-video-ar-btn flex-1 py-1 text-[9.5px] sm:text-[10px] font-mono font-medium rounded text-center transition">4:3</button>
                                    </div>
                                    <input type="hidden" id="proj-field-video-aspect-ratio" value="16:9">
                                </div>

                                <!-- Playback Options -->
                                <div>
                                    <label class="text-[9.5px] sm:text-[10px] font-medium text-zinc-500 dark:text-zinc-400 block mb-1">Playback Settings</label>
                                    <div class="flex items-center gap-2 pt-0.5">
                                        <label class="flex items-center gap-1 cursor-pointer text-[10px] sm:text-[10.5px] text-zinc-700 dark:text-zinc-300 select-none">
                                            <input type="checkbox" id="proj-field-video-autoplay" onchange="coraUpdateVideoPreview()" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                                            <span>Autoplay</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer text-[10px] sm:text-[10.5px] text-zinc-700 dark:text-zinc-300 select-none">
                                            <input type="checkbox" id="proj-field-video-loop" onchange="coraUpdateVideoPreview()" class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                                            <span>Loop</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Interactive Media & Video Preview Studio (7 Cols) -->
                <div class="w-full flex flex-col">
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5 flex-1 flex flex-col justify-between">
                        <div class="space-y-2.5 sm:space-y-3">
                            <!-- Preview Top Bar with Segmented Control -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 sm:pb-3 border-b border-zinc-200/60 dark:border-zinc-800">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                                            <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                                            <span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-700"></span>
                                        </div>
                                        <span class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400">public/case-study#media</span>
                                    </div>
                                    <span class="text-[10.5px] sm:text-[11px] font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider block mt-0.5 sm:mt-1">Live Media & Video Canvas</span>
                                </div>

                                <!-- Level 01 Segmented View Switcher -->
                                <div class="p-0.5 bg-zinc-200/70 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/80 dark:border-zinc-700/60 flex items-center gap-0.5 self-start sm:self-auto">
                                    <button 
                                        type="button" 
                                        id="preview-tab-gallery" 
                                        onclick="coraSetMediaPreviewTab('gallery')" 
                                        class="px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-[10.5px] font-bold rounded-lg transition cora-media-tab-btn active bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs"
                                    >
                                        Gallery Grid
                                    </button>
                                    <button 
                                        type="button" 
                                        id="preview-tab-video" 
                                        onclick="coraSetMediaPreviewTab('video')" 
                                        class="px-2 sm:px-2.5 py-0.5 sm:py-1 text-[10px] sm:text-[10.5px] font-medium rounded-lg transition cora-media-tab-btn text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white"
                                    >
                                        Video Reel
                                    </button>
                                </div>
                            </div>

                            <!-- Canvas 1: Live Visual Gallery Grid Container -->
                            <div id="cora-preview-canvas-gallery" class="space-y-2 sm:space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400 font-bold uppercase tracking-wider" id="cora-gallery-preview-status">Responsive Grid Layout</span>
                                    <span class="text-[9px] sm:text-[9.5px] font-mono text-zinc-400">Hover for caption</span>
                                </div>
                                <div id="cora-gallery-items-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-2.5 pt-1 min-h-[120px] sm:min-h-[160px]">
                                    <!-- Rendered dynamically -->
                                </div>
                            </div>

                            <!-- Canvas 2: Live Video Reel Embed Preview Container -->
                            <div id="cora-preview-canvas-video" class="hidden space-y-2 sm:space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400 font-bold uppercase tracking-wider" id="cora-video-preview-status">Live Video Player Stream</span>
                                    <span class="text-[9px] sm:text-[9.5px] font-mono text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Interactive Embed
                                    </span>
                                </div>
                                
                                <!-- Video Player Shell -->
                                <div id="cora-video-embed-container" class="w-full rounded-xl overflow-hidden bg-black border border-zinc-200 dark:border-zinc-800 flex items-center justify-center transition-all shadow-sm">
                                    <!-- Populated dynamically by coraUpdateVideoPreview() -->
                                </div>
                            </div>

                        </div>

                        <!-- Production Media Specs Callout -->
                        <div class="p-2.5 sm:p-3 rounded-xl bg-zinc-100/80 dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-800/80 space-y-1 mt-2 sm:mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10.5px] sm:text-[11px] font-bold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-700 dark:text-zinc-300"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    Responsive CDN & Next-Gen Pipeline
                                </span>
                                <span class="text-[9px] sm:text-[9.5px] font-mono font-semibold text-zinc-600 dark:text-zinc-400">WebP / AVIF • Lazy-Loaded</span>
                            </div>
                            <p class="text-[10px] sm:text-[10.5px] text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Assets are automatically processed into adaptive srcset responsive break-points, delivering instant sub-100ms LCP paint speeds.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- =====================================================================
             STEP 5: VERIFIED CLIENT ENDORSEMENT (2-Column Grid)
             ===================================================================== -->
        <div id="proj-step-5" class="cora-proj-step-panel">
            <div class="cora-2col-grid-equal">
                
                <!-- LEFT COLUMN: Testimonial & Stakeholder Details (6 Cols) -->
                <div class="w-full space-y-2.5 sm:space-y-3">
                    <div class="p-3 sm:p-4 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 cora-form-section-card space-y-2 sm:space-y-2.5">
                        
                        <!-- Header with Polish Quote AI button -->
                        <div class="flex items-center justify-between gap-2 sm:gap-3 pb-1 sm:pb-0">
                            <div>
                                <label class="text-[10.5px] sm:text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Executive Client Endorsement</label>
                                <p class="text-[10px] sm:text-[10.5px] text-zinc-500 mt-0.5">Authentic client testimonial establishing verified executive authority.</p>
                            </div>
                            <button type="button" onclick="coraAiRefineField('proj-field-test-quote', 'quote')" class="cora-ai-btn-modern shrink-0">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                <span>Polish Quote</span>
                            </button>
                        </div>

                        <!-- 1-Click Agency Testimonial Presets Toolbar -->
                        <div class="p-1.5 sm:p-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-800">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[8.5px] sm:text-[9px] font-mono font-bold text-zinc-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    <span>1-Click Testimonial Archetypes</span>
                                </span>
                                <span class="text-[8.5px] sm:text-[9px] text-zinc-400">Click to apply</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-1">
                                <button type="button" onclick="coraApplyTestimonialPreset('category_leader')" class="px-1.5 sm:px-2 py-0.5 rounded-lg text-[9.5px] sm:text-[10px] font-medium bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/80 text-zinc-700 dark:text-zinc-300 border border-zinc-200/70 dark:border-zinc-700 cursor-pointer transition-all">
                                    + Category Leader
                                </button>
                                <button type="button" onclick="coraApplyTestimonialPreset('velocity')" class="px-1.5 sm:px-2 py-0.5 rounded-lg text-[9.5px] sm:text-[10px] font-medium bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/80 text-zinc-700 dark:text-zinc-300 border border-zinc-200/70 dark:border-zinc-700 cursor-pointer transition-all">
                                    + 10x Velocity
                                </button>
                                <button type="button" onclick="coraApplyTestimonialPreset('bespoke_craft')" class="px-1.5 sm:px-2 py-0.5 rounded-lg text-[9.5px] sm:text-[10px] font-medium bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/80 text-zinc-700 dark:text-zinc-300 border border-zinc-200/70 dark:border-zinc-700 cursor-pointer transition-all">
                                    + Bespoke Craft
                                </button>
                                <button type="button" onclick="coraApplyTestimonialPreset('enterprise_scale')" class="px-1.5 sm:px-2 py-0.5 rounded-lg text-[9.5px] sm:text-[10px] font-medium bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700/80 text-zinc-700 dark:text-zinc-300 border border-zinc-200/70 dark:border-zinc-700 cursor-pointer transition-all">
                                    + Enterprise Scale
                                </button>
                            </div>
                        </div>

                        <!-- Testimonial Quote Textarea with Rich Text Toolbar -->
                        <div class="cora-form-field-group">
                            <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                                <label class="cora-field-label mb-0">Testimonial Quote</label>
                                <span class="text-[9px] sm:text-[9.5px] text-zinc-400 font-mono">Editorial Quote</span>
                            </div>
                            <div class="cora-richtext-toolbar">
                                <button type="button" onclick="coraApplyRichFormat('proj-field-test-quote', 'bold')" class="cora-richtext-btn" title="Bold"><b>B</b></button>
                                <button type="button" onclick="coraApplyRichFormat('proj-field-test-quote', 'italic')" class="cora-richtext-btn" title="Italic"><i>I</i></button>
                                <button type="button" onclick="coraApplyRichFormat('proj-field-test-quote', 'quote')" class="cora-richtext-btn" title="Quote">”</button>
                                <button type="button" onclick="coraApplyRichFormat('proj-field-test-quote', 'clear')" class="cora-richtext-btn ml-auto text-[9.5px] sm:text-[10px]" title="Clear Format">Clear</button>
                            </div>
                            <textarea 
                                id="proj-field-test-quote" 
                                name="testimonial_quote" 
                                oninput="coraUpdateTestimonialPreview()"
                                rows="2" 
                                style="min-height: 52px;"
                                placeholder="Working with the team transformed our brand velocity. Deliverables were flawless and delivered ahead of our global launch."
                                class="cora-textarea-spacious cora-textarea-with-toolbar"
                            ></textarea>
                        </div>

                        <!-- 2-Column Mobile & Desktop: Client Stakeholder & Title -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Client Stakeholder</label>
                                <input 
                                    type="text" 
                                    id="proj-field-test-author" 
                                    name="testimonial_author" 
                                    oninput="coraUpdateTestimonialPreview()"
                                    placeholder="e.g. Kavya Patel"
                                    class="cora-input-spacious"
                                />
                            </div>

                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Title & Position</label>
                                <input 
                                    type="text" 
                                    id="proj-field-test-role" 
                                    name="testimonial_role" 
                                    oninput="coraUpdateTestimonialPreview()"
                                    placeholder="e.g. VP Marketing"
                                    class="cora-input-spacious"
                                />
                            </div>
                        </div>

                        <!-- 2-Column Mobile & Desktop: Company & Avatar Visual Controls -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Company</label>
                                <input 
                                    type="text" 
                                    id="proj-field-test-company" 
                                    name="testimonial_company" 
                                    oninput="coraUpdateTestimonialPreview()"
                                    placeholder="e.g. Apex Global"
                                    class="cora-input-spacious"
                                />
                            </div>

                            <div class="cora-form-field-group">
                                <label class="cora-field-label">Avatar Photo</label>
                                <div class="flex items-center gap-1 sm:gap-1.5">
                                    <input 
                                        type="url" 
                                        id="proj-field-test-avatar" 
                                        name="testimonial_avatar" 
                                        oninput="coraUpdateTestimonialPreview()"
                                        placeholder="URL (optional)"
                                        class="cora-input-spacious text-[11px] sm:text-xs flex-1 min-w-0"
                                    />
                                    <button 
                                        type="button" 
                                        onclick="coraSelectTestimonialAvatar()" 
                                        title="Select or Sample Avatar" 
                                        class="px-2 sm:px-2.5 py-1.5 sm:py-2 rounded-xl text-[10.5px] sm:text-xs font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700 shrink-0 cursor-pointer flex items-center gap-1 transition-all"
                                    >
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        <span class="hidden xs:inline">Sample</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Trust Signals: Rating & Verification Pills -->
                        <div class="pt-2 border-t border-zinc-200/70 dark:border-zinc-800/80 flex flex-wrap items-center justify-between gap-1.5 sm:gap-2">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="text-[11px] sm:text-xs font-bold text-zinc-600 dark:text-zinc-400">Rating:</span>
                                <div class="inline-flex items-center gap-0.5 sm:gap-1 bg-white dark:bg-zinc-900 px-1.5 sm:px-2 py-0.5 rounded-xl border border-zinc-200/70 dark:border-zinc-800" id="cora-test-star-group">
                                    <input type="hidden" id="proj-field-test-rating" name="testimonial_rating" value="5" />
                                    <button type="button" onclick="coraSetTestimonialRating(1)" class="cora-star-btn text-amber-500 text-xs sm:text-sm" title="1 Star">★</button>
                                    <button type="button" onclick="coraSetTestimonialRating(2)" class="cora-star-btn text-amber-500 text-xs sm:text-sm" title="2 Stars">★</button>
                                    <button type="button" onclick="coraSetTestimonialRating(3)" class="cora-star-btn text-amber-500 text-xs sm:text-sm" title="3 Stars">★</button>
                                    <button type="button" onclick="coraSetTestimonialRating(4)" class="cora-star-btn text-amber-500 text-xs sm:text-sm" title="4 Stars">★</button>
                                    <button type="button" onclick="coraSetTestimonialRating(5)" class="cora-star-btn text-amber-500 text-xs sm:text-sm" title="5 Stars">★</button>
                                    <span class="text-[10px] sm:text-[11px] font-mono font-bold text-zinc-700 dark:text-zinc-300 ml-0.5 sm:ml-1" id="cora-test-rating-label">5.0</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <input type="hidden" id="proj-field-test-verified" name="testimonial_verified" value="1" />
                                <button 
                                    type="button" 
                                    onclick="coraToggleTestimonialVerified()" 
                                    id="btn-toggle-verified-client" 
                                    class="px-2 sm:px-2.5 py-1 rounded-xl text-[11px] sm:text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/80 flex items-center gap-1 sm:gap-1.5 cursor-pointer transition-all"
                                >
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span id="label-verified-toggle">Verified Client Review</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- RIGHT COLUMN: Executive Social Proof Showcase Studio (6 Cols) -->
                <div class="w-full space-y-2.5 sm:space-y-3.5">
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5">
                        
                        <!-- Browser Chrome Bar -->
                        <div class="flex items-center justify-between pb-2 sm:pb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <div class="flex items-center gap-1 text-zinc-300 dark:text-zinc-700">
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700 inline-block"></span>
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700 inline-block"></span>
                                    <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-zinc-300 dark:bg-zinc-700 inline-block"></span>
                                </div>
                                <span class="text-[10px] sm:text-[11px] font-mono text-zinc-400 ml-0.5 sm:ml-1">public/case-study#endorsement</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-mono font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                                <span>Live Showcase</span>
                            </span>
                        </div>

                        <!-- Live Editorial Showcase Card -->
                        <div id="cora-testimonial-live-preview" class="p-3.5 sm:p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-2xs space-y-2.5 sm:space-y-3 relative overflow-hidden">
                            <!-- Subtle decorative quote watermark in background -->
                            <div class="absolute -right-2 -bottom-4 text-zinc-100 dark:text-zinc-800/60 text-6xl sm:text-7xl font-serif select-none pointer-events-none font-bold">”</div>

                            <!-- Star Rating row -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-0.5 text-amber-500 text-xs" id="preview-test-stars">
                                    ★ ★ ★ ★ ★
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] sm:text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold" id="preview-test-verified-badge">
                                    ✓ Verified Client
                                </span>
                            </div>

                            <!-- Quote Body -->
                            <p class="text-xs sm:text-sm italic text-zinc-800 dark:text-zinc-200 leading-relaxed relative z-10" id="preview-test-quote">
                                "The editorial precision and production speed set a new benchmark for our brand launches."
                            </p>

                            <!-- Stakeholder Identity -->
                            <div class="flex items-center gap-2.5 sm:gap-3 pt-2 sm:pt-2.5 border-t border-zinc-100 dark:border-zinc-800/80 relative z-10">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 font-bold font-mono text-xs flex items-center justify-center shrink-0 overflow-hidden shadow-2xs border border-zinc-200/80 dark:border-zinc-700" id="preview-test-avatar-container">
                                    <span id="preview-test-avatar">KP</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-zinc-950 dark:text-white truncate" id="preview-test-author">Kavya Patel</span>
                                    </div>
                                    <span class="text-[10px] sm:text-[11px] text-zinc-500 block truncate mt-0.5" id="preview-test-meta">VP of Global Brand Marketing • Apex Global Brands</span>
                                </div>
                            </div>
                        </div>

                        <!-- Live Google Rich Snippets (Review Schema) Preview -->
                        <div class="p-2.5 sm:p-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-800 space-y-1">
                            <div class="flex items-center justify-between text-[9px] sm:text-[9.5px] font-mono text-zinc-400">
                                <span class="font-bold uppercase tracking-wider flex items-center gap-1">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    Google Rich Snippets JSON-LD
                                </span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">Schema Validated</span>
                            </div>
                            <div class="text-[11px] sm:text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate" id="rich-snippet-title">
                                Apex Global Brands — Client Case Study & Review
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] sm:text-[11px] text-zinc-600 dark:text-zinc-400">
                                <span class="text-amber-500 font-bold text-xs">★★★★★</span>
                                <span class="font-bold text-zinc-900 dark:text-white" id="rich-snippet-rating">Rating: 5.0</span>
                                <span class="text-zinc-300 dark:text-zinc-700">·</span>
                                <span class="truncate">Review by <span id="rich-snippet-author">Kavya Patel</span></span>
                            </div>
                        </div>

                        <!-- Testimonial Placement Registry -->
                        <div class="p-2 sm:p-2.5 rounded-xl bg-zinc-100/70 dark:bg-zinc-900/60 border border-zinc-200/60 dark:border-zinc-800/80 flex flex-wrap items-center justify-between gap-1.5 sm:gap-2">
                            <span class="text-[9.5px] sm:text-[10px] font-mono font-bold text-zinc-500 uppercase tracking-wider">Placement:</span>
                            <div class="flex items-center gap-2 sm:gap-2.5 text-[10.5px] sm:text-[11px] text-zinc-600 dark:text-zinc-400">
                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                                    <span>Case Study</span>
                                </label>
                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                                    <span>Wall of Love</span>
                                </label>
                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" checked class="rounded border-zinc-300 text-zinc-900 focus:ring-0">
                                    <span>Pitch Deck</span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- =====================================================================
             STEP 6: SEO & CONVERSION CTA BRIDGE (2-Column Grid)
             ===================================================================== -->
        <div id="proj-step-6" class="cora-proj-step-panel">
            <div class="cora-2col-grid-equal">
                
                <!-- LEFT COLUMN: SEO Meta Tags & Inbound CTA Bridge (6 Cols) -->
                <div class="w-full space-y-2.5 sm:space-y-3.5">
                    
                    <!-- Integrated SEO & GEO Discovery Suite Card -->
                    <div class="p-3.5 sm:p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3">
                        
                        <!-- Header with Auto-Generate SEO AI button -->
                        <div class="flex items-center justify-between gap-2 sm:gap-3">
                            <div>
                                <label class="text-[10.5px] sm:text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Search & GEO Engine Optimization</label>
                                <p class="text-[10px] sm:text-[11px] text-zinc-500 mt-0.5">Rank on Google, Bing & AI answer engines (Perplexity, ChatGPT, Gemini).</p>
                            </div>
                            <button 
                                type="button" 
                                onclick="coraAiGenerateSeoMetadata()" 
                                id="btn-ai-generate-seo"
                                class="cora-ai-btn-modern shrink-0"
                            >
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"></path></svg>
                                <span>Auto-Generate SEO</span>
                            </button>
                        </div>

                        <!-- SEO Title Tag with live meter -->
                        <div>
                            <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                                <label class="cora-field-label mb-0">SEO Title Tag (&lt;60 characters)</label>
                                <span id="meta-title-counter" class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400"><span id="meta-title-count">0</span> / 60</span>
                            </div>
                            <input 
                                type="text" 
                                id="proj-field-meta-title" 
                                name="meta_title" 
                                oninput="coraUpdateSerpPreview()" 
                                placeholder="[Client] Showcase | Cora Studio Agency"
                                class="cora-input-spacious"
                            />
                            <!-- Visual Title Length Progress Bar -->
                            <div class="w-full h-1 bg-zinc-200 dark:bg-zinc-800 rounded-full mt-1 sm:mt-1.5 overflow-hidden">
                                <div id="meta-title-bar" class="h-full bg-emerald-500 transition-all duration-200" style="width: 0%;"></div>
                            </div>
                        </div>

                        <!-- SEO Meta Description with live meter -->
                        <div>
                            <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                                <label class="cora-field-label mb-0">SEO Meta Description (&lt;155 characters)</label>
                                <span id="meta-desc-counter" class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400"><span id="meta-desc-count">0</span> / 155</span>
                            </div>
                            <textarea 
                                id="proj-field-meta-desc" 
                                name="meta_description" 
                                oninput="coraUpdateSerpPreview()" 
                                rows="2" 
                                style="min-height: 52px;"
                                placeholder="Executive summary formatted for Google search snippets and social preview cards..."
                                class="cora-textarea-spacious"
                            ></textarea>
                            <!-- Visual Description Length Progress Bar -->
                            <div class="w-full h-1 bg-zinc-200 dark:bg-zinc-800 rounded-full mt-1 sm:mt-1.5 overflow-hidden">
                                <div id="meta-desc-bar" class="h-full bg-emerald-500 transition-all duration-200" style="width: 0%;"></div>
                            </div>
                        </div>

                        <!-- Target Keywords Chips -->
                        <div class="pt-1.5 sm:pt-2 border-t border-zinc-200/70 dark:border-zinc-800/80">
                            <label class="cora-field-label mb-0.5 sm:mb-1">Target Focus Keywords</label>
                            <input 
                                type="text" 
                                id="proj-field-meta-keywords" 
                                name="meta_keywords" 
                                placeholder="e.g. brand transformation, editorial production, digital growth"
                                class="cora-input-spacious text-xs font-mono"
                            />
                        </div>
                    </div>

                    <!-- Showcase Conversion CTA Bridge Card -->
                    <div class="p-3 sm:p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-950/50 border border-zinc-200/80 dark:border-zinc-800/80 cora-form-section-card space-y-2 sm:space-y-2.5">
                        <div class="flex items-center justify-between gap-1.5 sm:gap-2">
                            <div>
                                <label class="text-[10px] sm:text-[10.5px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider block">Showcase Conversion CTA Bridge</label>
                                <p class="text-[9.5px] sm:text-[10.5px] text-zinc-500">Captures inbound client consultations directly from this case study page.</p>
                            </div>
                            <div class="flex items-center gap-1 text-[10.5px] sm:text-[11px] shrink-0">
                                <button type="button" onclick="coraApplyCtaPreset('consultation')" class="px-1.5 sm:px-2 py-0.5 rounded text-[9.5px] sm:text-[10px] font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 cursor-pointer">Consultation</button>
                                <button type="button" onclick="coraApplyCtaPreset('proposal')" class="px-1.5 sm:px-2 py-0.5 rounded text-[9.5px] sm:text-[10px] font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 cursor-pointer">Proposal</button>
                            </div>
                        </div>

                        <!-- 2-Column Mobile & Desktop: CTA Headline & Subtitle -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label mb-0.5 text-[10px] sm:text-[10.5px]">CTA Headline</label>
                                <input type="text" id="proj-field-cta-title" name="cta_title" value="Planning an Editorial Production?" oninput="coraUpdateCtaPreview()" class="cora-input-spacious" />
                            </div>

                            <div class="cora-form-field-group">
                                <label class="cora-field-label mb-0.5 text-[10px] sm:text-[10.5px]">CTA Subtitle</label>
                                <input type="text" id="proj-field-cta-subtitle" name="cta_subtitle" value="Review our production calendar and commission shoot dates." oninput="coraUpdateCtaPreview()" class="cora-input-spacious" />
                            </div>
                        </div>

                        <!-- 2-Column Mobile & Desktop: Button Text & Link URL -->
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <div class="cora-form-field-group">
                                <label class="cora-field-label mb-0.5 text-[10px] sm:text-[10.5px]">Button Text</label>
                                <input type="text" id="proj-field-cta-button" name="cta_button_text" value="Book Consultation" oninput="coraUpdateCtaPreview()" class="cora-input-spacious" />
                            </div>
                            <div class="cora-form-field-group">
                                <label class="cora-field-label mb-0.5 text-[10px] sm:text-[10.5px]">Button Link URL</label>
                                <input type="text" id="proj-field-cta-url" name="cta_button_url" value="/contact" oninput="coraUpdateCtaPreview()" class="cora-input-spacious font-mono text-xs" />
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Live Multi-Mode Discovery & Conversion Studio (6 Cols) -->
                <div class="w-full space-y-2.5 sm:space-y-3.5">
                    <div class="p-3.5 sm:p-5 bg-zinc-50 dark:bg-zinc-950/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 space-y-2.5 sm:space-y-3.5">
                        
                        <!-- Header with Level 01 Segmented Mode Switcher -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 sm:pb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                            <div>
                                <span class="text-[9.5px] sm:text-[10px] font-mono text-zinc-400 uppercase tracking-wider block font-bold">Live Growth & Discovery Studio</span>
                                <p class="text-[10px] sm:text-[11px] text-zinc-500">Preview search, social cards, and lead capture in real time.</p>
                            </div>
                            <!-- Segmented Switcher Controls (Icon-Only Architecture) -->
                            <div class="inline-flex items-center p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/70 dark:border-zinc-700 self-start sm:self-auto shrink-0" id="cora-seo-preview-switcher">
                                <button type="button" onclick="coraSetSeoStudioTab('google')" id="seo-tab-btn-google" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all flex items-center justify-center" title="Google Search Preview">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </button>
                                <button type="button" onclick="coraSetSeoStudioTab('social')" id="seo-tab-btn-social" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all flex items-center justify-center" title="Social Card Preview">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                </button>
                                <button type="button" onclick="coraSetSeoStudioTab('cta')" id="seo-tab-btn-cta" class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all flex items-center justify-center" title="Inbound CTA Banner Preview">
                                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                                </button>
                            </div>
                        </div>

                        <!-- PANEL 1: Google SERP Preview (Default) -->
                        <div id="cora-seo-panel-google" class="space-y-2.5 sm:space-y-3">
                            <div class="cora-serp-preview">
                                <div class="cora-serp-site-info">
                                    <div class="cora-serp-favicon">C</div>
                                    <div>
                                        <div class="cora-serp-sitename">Cora Studio Portfolio</div>
                                        <div class="cora-serp-url" id="serp-preview-url"><?php echo esc_html( home_url( '/portfolio/' ) ); ?><span id="serp-preview-slug">master-production-showcase</span></div>
                                    </div>
                                </div>
                                <div class="cora-serp-title cursor-pointer" id="serp-preview-title">Master Production Showcase | Cora Studio Agency</div>
                                <div class="cora-serp-snippet" id="serp-preview-desc">Explore how Cora Studio engineered a comprehensive multi-channel digital transformation, driving +340% organic revenue growth and executive brand velocity.</div>
                                <!-- Rich Extras & Sitelinks in SERP -->
                                <div class="mt-2 pt-1.5 sm:mt-2.5 sm:pt-2 border-t border-zinc-100 dark:border-zinc-800 flex flex-wrap items-center gap-x-2.5 sm:gap-x-3 gap-y-1 text-[10px] sm:text-[11px] text-zinc-500">
                                    <span class="text-amber-500 font-bold">★★★★★ 5.0</span>
                                    <span>· Case Study</span>
                                    <span>· Verified Client Results</span>
                                    <span class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">› Production Scope</span>
                                    <span class="text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">› Book Consultation</span>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 2: OpenGraph / Social Share Card Preview -->
                        <div id="cora-seo-panel-social" class="hidden space-y-2.5 sm:space-y-3">
                            <div class="rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 overflow-hidden shadow-2xs">
                                <!-- Social Card Image Banner -->
                                <div class="w-full h-28 sm:h-32 bg-zinc-900 text-white flex items-center justify-center relative overflow-hidden" id="social-preview-cover-container">
                                    <img id="social-preview-cover-img" src="" alt="Social Card" class="w-full h-full object-cover hidden" />
                                    <div id="social-preview-cover-placeholder" class="flex flex-col items-center justify-center text-zinc-400 gap-1 p-2 sm:p-3 text-center">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                        <span class="text-[9.5px] sm:text-[10.5px] font-mono">1200 × 630 OpenGraph Preview (Step 1 Cover)</span>
                                    </div>
                                    <div class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-[8.5px] sm:text-[9px] font-mono bg-black/60 backdrop-blur-xs text-white font-bold">
                                        OG:IMAGE 1.91:1
                                    </div>
                                </div>
                                <!-- Social Card Details -->
                                <div class="p-2.5 sm:p-3 space-y-0.5 bg-zinc-50 dark:bg-zinc-900/90 border-t border-zinc-100 dark:border-zinc-800">
                                    <span class="text-[9px] sm:text-[9.5px] font-mono uppercase tracking-wider text-zinc-400 font-bold block" id="social-preview-domain">CORA.LOCAL • AGENCY PORTFOLIO</span>
                                    <h4 class="text-[11px] sm:text-xs font-bold text-zinc-950 dark:text-white truncate" id="social-preview-title">Master Production Showcase | Cora Studio</h4>
                                    <p class="text-[10px] sm:text-[11px] text-zinc-500 line-clamp-2" id="social-preview-desc">Explore how Cora Studio engineered a comprehensive multi-channel digital transformation...</p>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL 3: Inbound Consultation CTA Banner Live Preview -->
                        <div id="cora-seo-panel-cta" class="hidden space-y-2.5 sm:space-y-3">
                            <div class="p-3.5 sm:p-5 rounded-xl text-white shadow-xs space-y-2.5 sm:space-y-3 border border-zinc-800" style="background-color: #09090b !important; color: #ffffff !important;">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[9px] sm:text-[9.5px] font-mono bg-white/10 text-zinc-300 font-bold">Live Inbound Banner</span>
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400"></span>
                                </div>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-white" id="preview-cta-title" style="color: #ffffff !important;">Planning an Editorial Production?</h4>
                                    <p class="text-[11px] sm:text-xs text-zinc-400 mt-0.5 sm:mt-1" id="preview-cta-subtitle">Review our production calendar and commission custom shoot dates.</p>
                                </div>
                                <div class="pt-1.5 sm:pt-2 flex items-center justify-between gap-2 sm:gap-3">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg text-[11px] sm:text-xs font-bold bg-white text-zinc-950 shadow-2xs">
                                        <span id="preview-cta-btn">Book Consultation</span>
                                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </div>
                                    <span class="text-[9.5px] sm:text-[10px] font-mono text-zinc-500" id="preview-cta-dest">→ /contact</span>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Health Score & Verification Checklist -->
                        <div class="p-2.5 sm:p-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/70 dark:border-zinc-800 space-y-1.5 sm:space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-[9.5px] sm:text-[10px] font-mono font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">SEO & GEO Score</span>
                                </div>
                                <span class="text-[11px] sm:text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono" id="seo-score-label">98 / 100 Optimal</span>
                            </div>
                            <!-- Mini Progress Bar -->
                            <div class="w-full h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: 98%;"></div>
                            </div>
                            <!-- 2x2 Checklist items on Mobile & Desktop -->
                            <div class="grid grid-cols-2 gap-1 sm:gap-1.5 pt-0.5 text-[10px] sm:text-[10.5px] text-zinc-600 dark:text-zinc-400">
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600 dark:text-emerald-400 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span class="truncate">Title &lt; 60 chars</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600 dark:text-emerald-400 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span class="truncate">Desc &lt; 155 chars</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600 dark:text-emerald-400 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span class="truncate">Social Card</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none" class="text-emerald-600 dark:text-emerald-400 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span class="truncate">JSON-LD active</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>

    <!-- =====================================================================
         STICKY STEPPER DRAWER FOOTER CONTROLS (EDGE-TO-EDGE FLUSH AT BOTTOM)
         ===================================================================== -->
    <div id="cora-project-drawer-footer" class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
        <!-- Monochromatic Status Segmented Toggle -->
        <div class="flex items-center gap-1.5 sm:gap-2">
            <span class="hidden sm:inline text-xs text-zinc-500 font-medium">Status:</span>
            <div class="inline-flex items-center p-0.5 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200/70 dark:border-zinc-700" id="proj-status-toggle-group">
                <button type="button" onclick="coraSetDrawerStatus('published')" id="status-btn-published" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all">Published</button>
                <button type="button" onclick="coraSetDrawerStatus('draft')" id="status-btn-draft" class="px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all">Draft</button>
            </div>
        </div>

        <!-- Stepper Progress Indicator -->
        <div class="hidden md:flex items-center gap-2 text-xs text-zinc-500 font-mono">
            <span id="cora-footer-step-counter">Step 1 of 6</span>
            <span class="text-zinc-300 dark:text-zinc-700">•</span>
            <span class="text-[11px] text-zinc-400">Shortcuts: Alt + ← / →</span>
        </div>

        <!-- Stepper Navigation Buttons -->
        <div class="flex items-center gap-1.5 sm:gap-2">
            <button 
                type="button" 
                onclick="coraCloseProjectDrawer()" 
                class="hidden sm:inline-flex px-3.5 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition cursor-pointer"
            >
                Cancel
            </button>

            <!-- Previous Step Button -->
            <button 
                type="button" 
                onclick="coraPrevProjectStep()" 
                id="cora-btn-prev-step"
                class="px-3 sm:px-3.5 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-400 dark:text-zinc-600 text-xs font-semibold transition cursor-not-allowed flex items-center gap-1 opacity-50"
                disabled
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span class="hidden sm:inline">Previous</span>
                <span class="sm:hidden">Back</span>
            </button>

            <!-- Next Step Button (Switches to 'Save & Publish' on Step 6) -->
            <button 
                type="button" 
                onclick="coraNextProjectStep()" 
                id="cora-btn-next-step"
                class="px-3.5 sm:px-4 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-900 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-950 text-xs font-bold transition shadow-xs cursor-pointer active:scale-98 flex items-center gap-1.5"
            >
                <span id="cora-next-step-label">Next: 02. Story</span>
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>

            <!-- Quick Direct Save Button -->
            <button 
                type="button" 
                onclick="document.getElementById('cora-project-form').requestSubmit()"
                id="cora-proj-save-btn"
                class="hidden px-4 py-2 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-xs font-bold transition shadow-xs cursor-pointer active:scale-98"
            >
                Save Project
            </button>
        </div>
    </div>
</aside>

<!-- =========================================================================
     7. MULTI-ASSET MEDIA LIBRARY SELECTOR MODAL (Monochromatic Level 03 Organism)
     ========================================================================= -->
<div id="cora-media-library-modal" class="fixed inset-0 z-[100080] select-none">
    <div id="cora-media-picker-card" class="w-full max-w-4xl bg-white dark:bg-zinc-900 rounded-t-3xl sm:rounded-2xl border-t sm:border border-zinc-200 dark:border-zinc-800 shadow-2xl flex flex-col max-h-[92vh] sm:max-h-[85vh] overflow-hidden transition-all duration-300">
        <!-- Mobile Drag Indicator Handle (Rule 12 Bottom Sheet Standard) -->
        <div class="w-10 h-1 rounded-full bg-zinc-300 dark:bg-zinc-700 mx-auto mt-2.5 mb-1 sm:hidden"></div>

        <!-- Modal Header -->
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-center text-zinc-900 dark:text-white shrink-0">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-bold text-zinc-950 dark:text-white uppercase tracking-wider" id="cora-media-picker-modal-title">Workspace Media Library</h3>
                        <span id="cora-media-picker-mode-pill" class="px-2 py-0.5 rounded-full text-[9px] sm:text-[9.5px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/80 dark:border-zinc-700">Multi-Select Gallery</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-zinc-500 mt-0.5" id="cora-media-picker-modal-desc">Select verified assets from your workspace storage to attach to this case study.</p>
                </div>
            </div>
            <button type="button" id="cora-media-picker-close-x" onclick="coraCloseMediaLibraryModal()" class="w-8 h-8 rounded-lg text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 flex items-center justify-center text-sm cursor-pointer transition">✕</button>
        </div>

        <!-- Toolbar: Real-time Search, Folder Filter, and Direct Workspace Upload -->
        <div class="px-4 sm:px-6 py-2.5 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50/70 dark:bg-zinc-950/40 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-2 flex-1 min-w-[240px]">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-2.5 top-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input 
                        type="text" 
                        id="cora-media-picker-search-input" 
                        oninput="coraOnMediaPickerSearch(this.value)" 
                        placeholder="Search workspace media..." 
                        class="w-full h-8 pl-8 pr-7 text-xs bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition"
                    >
                    <button type="button" id="cora-media-picker-search-clear" onclick="coraClearMediaPickerSearch()" class="hidden absolute right-2.5 top-2 text-zinc-400 hover:text-zinc-600 text-xs cursor-pointer">✕</button>
                </div>

                <!-- Folder Filter -->
                <select 
                    id="cora-media-picker-folder-select" 
                    onchange="coraOnMediaPickerFolderChange(this.value)" 
                    class="h-8 px-2.5 text-xs bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-medium focus:outline-none transition cursor-pointer shrink-0"
                >
                    <option value="">All Folders (<?php echo count( $workspace_media_files ); ?>+)</option>
                    <?php foreach ( $workspace_media_folders as $f ) : ?>
                        <option value="<?php echo esc_attr( $f['id'] ); ?>"><?php echo esc_html( $f['name'] ); ?> (<?php echo esc_html( $f['count'] ); ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Upload Action Direct into Workspace Media -->
            <div class="flex items-center gap-2">
                <input type="file" id="cora-media-picker-file-input" multiple accept="image/*" class="hidden" onchange="coraUploadFromMediaPicker(this.files)">
                <button 
                    type="button" 
                    onclick="document.getElementById('cora-media-picker-file-input').click()" 
                    class="h-8 px-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs shrink-0"
                    title="Upload images directly to workspace media library"
                >
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span>Upload New</span>
                </button>
            </div>
        </div>
        
        <!-- Media Grid Container with Scroll -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1 min-h-[300px]">
            <div id="cora-modal-media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 sm:gap-3.5">
                <!-- Rendered dynamically by coraRenderMediaPickerGrid() -->
            </div>
            <!-- Empty State -->
            <div id="cora-modal-media-empty" class="hidden py-14 flex-col items-center justify-center text-center space-y-2">
                <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-400 mx-auto"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No workspace media files matched your query.</p>
                <button type="button" onclick="document.getElementById('cora-media-picker-file-input').click()" class="text-xs text-zinc-900 dark:text-white underline font-semibold cursor-pointer">Upload an asset now</button>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-4 sm:px-6 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span id="cora-modal-selected-count" class="text-xs font-mono font-medium text-zinc-600 dark:text-zinc-400">0 assets selected</span>
                <button type="button" id="cora-modal-select-all-btn" onclick="coraMediaPickerToggleSelectAll()" class="text-[11px] font-mono text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition cursor-pointer underline ml-2">Select All Visible</button>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="coraCloseMediaLibraryModal()" class="px-3.5 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer">Cancel</button>
                <button type="button" id="btn-modal-confirm-add" onclick="coraConfirmModalMediaSelection()" class="px-4 py-1.5 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-100 dark:text-zinc-900 text-xs font-semibold transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span id="cora-modal-confirm-btn-text">Add to Case Study</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CLIENT JAVASCRIPT CONTROLLER -->
<script>
window.coraProjectsData = <?php echo json_encode( array_map( array( 'Cora_Projects_Module', 'format_project_for_api' ), $projects_raw ) ); ?>;
window.coraActiveAgencyId = "<?php echo esc_js( $agency_id ); ?>";
window.coraWorkspaceMediaFiles = <?php echo json_encode( $workspace_media_files ); ?>;
window.coraWorkspaceMediaFolders = <?php echo json_encode( $workspace_media_folders ); ?>;
window.coraWorkspaceAjaxNonce = "<?php echo esc_js( wp_create_nonce( 'cora_ajax_nonce' ) ); ?>";

// =========================================================================
// INTERACTIVE FILTER & SEARCH ENGINE (TABS, METRICS, TAGS, SERP SEARCH)
// =========================================================================
window.coraFilterState = {
    tab: 'all',          // 'all' | 'published' | 'draft' | 'featured' | 'testimonials'
    query: ''
};

function coraSwitchTabFilter(tab, triggerEl) {
    window.coraFilterState.tab = tab || 'all';
    
    // Update sub-tab button styling
    var nav = document.querySelector('.cora-sub-tabs-nav');
    if (nav) {
        var tabs = nav.querySelectorAll('.cora-sub-tab');
        tabs.forEach(function(t) {
            t.classList.remove('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white');
            t.classList.add('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
        });
        if (triggerEl) {
            triggerEl.classList.remove('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
            triggerEl.classList.add('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white');
        } else {
            var matching = nav.querySelector('[data-target="' + tab + '"]');
            if (matching) {
                matching.classList.remove('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
                matching.classList.add('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white');
            }
        }
    }
    coraApplyFilters();
}

function coraFilterMetricCard(type) {
    if (type === 'all') {
        coraSwitchTabFilter('all');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Showing all portfolio projects', 'info');
        }
    } else if (type === 'published') {
        coraSwitchTabFilter('published');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Filtered to published live projects', 'info');
        }
    } else if (type === 'draft') {
        coraSwitchTabFilter('draft');
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Filtered to draft case studies', 'info');
        }
    } else if (type === 'featured') {
        window.coraFilterState.tab = 'featured';
        var nav = document.querySelector('.cora-sub-tabs-nav');
        if (nav) {
            nav.querySelectorAll('.cora-sub-tab').forEach(function(t) {
                t.classList.remove('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white');
                t.classList.add('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
            });
        }
        coraApplyFilters();
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Filtered to Featured projects', 'info');
        }
    } else if (type === 'testimonials') {
        window.coraFilterState.tab = 'testimonials';
        var nav = document.querySelector('.cora-sub-tabs-nav');
        if (nav) {
            nav.querySelectorAll('.cora-sub-tab').forEach(function(t) {
                t.classList.remove('active', 'border-zinc-950', 'text-zinc-900', 'dark:border-white', 'dark:text-white');
                t.classList.add('border-transparent', 'text-zinc-500', 'dark:text-zinc-400');
            });
        }
        coraApplyFilters();
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Filtered to projects with verified client quotes', 'info');
        }
    } else if (type === 'capabilities') {
        var link = document.querySelector('a[title="Open live showcase"]');
        if (link && link.href) {
            window.open(link.href, '_blank');
        }
    }
}

function coraFilterByTag(tagName) {
    if (!tagName) return;
    var searchInput = document.getElementById('cora-proj-search-input');
    if (searchInput) {
        searchInput.value = tagName;
    }
    window.coraFilterState.query = tagName.toLowerCase();
    coraApplyFilters();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Filtered by capability: ' + tagName, 'info');
    }
}

function coraFilterByQuery(queryStr) {
    if (!queryStr) return;
    var searchInput = document.getElementById('cora-proj-search-input');
    if (searchInput) {
        searchInput.value = queryStr;
    }
    window.coraFilterState.query = queryStr.toLowerCase();
    coraApplyFilters();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Filtered by: ' + queryStr, 'info');
    }
}

function coraFilterProjectsList() {
    var searchInput = document.getElementById('cora-proj-search-input');
    var q = searchInput ? searchInput.value.trim().toLowerCase() : '';
    window.coraFilterState.query = q;
    coraApplyFilters();
}

function coraApplyFilters() {
    var tab = window.coraFilterState.tab;
    var query = window.coraFilterState.query;
    var allRows = document.querySelectorAll('.cora-project-row');
    var visibleCount = 0;

    allRows.forEach(function(row) {
        var rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
        var rowFeatured = row.getAttribute('data-featured') || '0';
        var rowHasTestimonial = row.getAttribute('data-has-testimonial') || '0';
        var rowText = (row.textContent || '').toLowerCase();

        var matchesTab = true;
        if (tab === 'published') {
            matchesTab = (rowStatus === 'published');
        } else if (tab === 'draft') {
            matchesTab = (rowStatus === 'draft');
        } else if (tab === 'featured') {
            matchesTab = (rowFeatured === '1');
        } else if (tab === 'testimonials') {
            matchesTab = (rowHasTestimonial === '1');
        }

        var matchesQuery = true;
        if (query) {
            matchesQuery = rowText.indexOf(query) !== -1;
        }

        if (matchesTab && matchesQuery) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Check empty state in table
    var emptyRow = document.getElementById('cora-projects-filter-empty');
    var tableBody = document.getElementById('cora-projects-table-body');
    if (tableBody) {
        if (visibleCount === 0 && allRows.length > 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'cora-projects-filter-empty';
                emptyRow.innerHTML = '<td colspan="6" class="py-12 text-center text-zinc-500">' +
                    '<div class="max-w-sm mx-auto space-y-2">' +
                    '<p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No projects match the selected filter</p>' +
                    '<button type="button" onclick="coraClearFilters()" class="text-xs font-semibold text-zinc-950 dark:text-white underline cursor-pointer hover:opacity-80">Reset filters</button>' +
                    '</div></td>';
                tableBody.appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
            }
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }
}

function coraClearFilters() {
    var searchInput = document.getElementById('cora-proj-search-input');
    if (searchInput) searchInput.value = '';
    window.coraFilterState.query = '';
    coraSwitchTabFilter('all');
}

// =========================================================================
// TABLE COLUMN SORTING ENGINE
// =========================================================================
window.coraSortState = { col: '', asc: true };
function coraSortProjectsTable(colKey) {
    if (window.coraSortState.col === colKey) {
        window.coraSortState.asc = !window.coraSortState.asc;
    } else {
        window.coraSortState.col = colKey;
        window.coraSortState.asc = true;
    }
    var tbody = document.getElementById('cora-projects-table-body');
    if (!tbody) return;
    var rows = Array.from(tbody.querySelectorAll('tr.cora-project-row'));
    var isAsc = window.coraSortState.asc;

    rows.sort(function(a, b) {
        var aId = a.getAttribute('data-id');
        var bId = b.getAttribute('data-id');
        var pA = (window.coraProjectsData || []).find(function(p){ return String(p.id) === String(aId); }) || {};
        var pB = (window.coraProjectsData || []).find(function(p){ return String(p.id) === String(bId); }) || {};

        var valA = '', valB = '';
        if (colKey === 'title') {
            valA = (pA.title || '').toLowerCase();
            valB = (pB.title || '').toLowerCase();
        } else if (colKey === 'industry') {
            valA = (pA.clientIndustry || '').toLowerCase();
            valB = (pB.clientIndustry || '').toLowerCase();
        } else if (colKey === 'status') {
            valA = (pA.status || '').toLowerCase();
            valB = (pB.status || '').toLowerCase();
        } else if (colKey === 'kpi') {
            valA = (pA.kpis && pA.kpis[0] ? pA.kpis[0].value : '').toLowerCase();
            valB = (pB.kpis && pB.kpis[0] ? pB.kpis[0].value : '').toLowerCase();
        }
        if (valA < valB) return isAsc ? -1 : 1;
        if (valA > valB) return isAsc ? 1 : -1;
        return 0;
    });

    rows.forEach(function(r) { tbody.appendChild(r); });
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Sorted by ' + colKey + ' (' + (isAsc ? 'asc' : 'desc') + ')', 'info');
    }
}

// VIEW MODE SWITCHER (Table vs Grid)
function coraSetViewMode(mode) {
    var tableView = document.getElementById('cora-projects-table-view');
    var gridView = document.getElementById('cora-projects-grid-view');
    var tableBtn = document.getElementById('view-mode-table-btn');
    var gridBtn = document.getElementById('view-mode-grid-btn');

    if (mode === 'grid') {
        if (tableView) tableView.classList.add('hidden');
        if (gridView) gridView.classList.remove('hidden');
        if (tableBtn) tableBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
    } else {
        if (tableView) tableView.classList.remove('hidden');
        if (gridView) gridView.classList.add('hidden');
        if (tableBtn) tableBtn.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
    }
}

// ROW ACTIONS DROPDOWN POPOVER
function coraToggleRowMenu(id) {
    var menu = document.getElementById('cora-row-menu-' + id);
    if (!menu) return;
    var wasOpen = !menu.classList.contains('hidden');
    coraCloseAllRowMenus();
    if (!wasOpen) {
        menu.classList.remove('hidden');
    }
}

function coraCloseAllRowMenus() {
    document.querySelectorAll('.cora-row-menu').forEach(function(m) {
        m.classList.add('hidden');
    });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cora-actions-dropdown-container')) {
        coraCloseAllRowMenus();
    }
});

// DRAWER FOOTER STATUS SWITCHER
function coraSetDrawerStatus(status) {
    var statusInput = document.getElementById('proj-field-status');
    var pubBtn = document.getElementById('status-btn-published');
    var dftBtn = document.getElementById('status-btn-draft');
    var badge = document.getElementById('cora-proj-header-badge');

    if (statusInput) statusInput.value = status;

    if (status === 'published') {
        if (pubBtn) {
            pubBtn.className = "px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all";
        }
        if (dftBtn) {
            dftBtn.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all";
        }
        if (badge) {
            badge.innerText = "Published";
            badge.className = "px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-900 text-white dark:bg-white dark:text-zinc-900";
        }
        var step1Pill = document.getElementById('step1-preview-status-pill');
        if (step1Pill) {
            step1Pill.innerText = "Published";
            step1Pill.className = "px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900";
        }
    } else {
        if (pubBtn) {
            pubBtn.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all";
        }
        if (dftBtn) {
            dftBtn.className = "px-2.5 py-1 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all";
        }
        if (badge) {
            badge.innerText = "Draft";
            badge.className = "px-2 py-0.5 rounded-full text-[10px] font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700";
        }
        var step1Pill = document.getElementById('step1-preview-status-pill');
        if (step1Pill) {
            step1Pill.innerText = "Draft";
            step1Pill.className = "px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700";
        }
    }
}

// =========================================================================
// 6-STEP MULTI-STEP CREATION FRAMEWORK CONTROLLER
// =========================================================================

var coraActiveProjectStep = 1;
var coraStepGoals = {
    1: {
        title: "Step 1 Goal: Visual Overview & Identity",
        desc: "Establish core case study metadata, configure aspect ratio, upload your visual cover asset, and connect smart public links."
    },
    2: {
        title: "Step 2 Goal: 4-Act Story Narrative",
        desc: "Structure the narrative transformation from obstacle to engineered solution. Use the AI Assistant to refine copy and sharpen commercial impact."
    },
    3: {
        title: "Step 3 Goal: Proven Quantitative Results",
        desc: "Quantify your agency's business impact using high-contrast numeric KPIs and transparent measurement timelines."
    },
    4: {
        title: "Step 4 Goal: Media Assets & Gallery",
        desc: "Curate high-resolution production assets, deliverable screenshots, before/after media, or video walkthroughs."
    },
    5: {
        title: "Step 5 Goal: Verified Client Endorsement",
        desc: "Attach authentic client testimonial quotes, executive roles, and verified badges to build unmistakable social proof."
    },
    6: {
        title: "Step 6 Goal: Search Engine & Conversion CTA",
        desc: "Optimize meta tags with real-time Google SERP preview and configure the inbound lead consultation bridge."
    }
};

var coraStepTitles = {
    1: "Overview",
    2: "Story",
    3: "Results",
    4: "Visuals",
    5: "Endorsement",
    6: "SEO & CTA"
};

// NAVIGATE TO SPECIFIC STEP (1 to 6)
function coraGoToProjectStep(stepIndex) {
    if (stepIndex < 1 || stepIndex > 6) return;
    coraActiveProjectStep = stepIndex;

    // 1. Update Stepper Pills Styling
    for (var i = 1; i <= 6; i++) {
        var btn = document.getElementById('cora-step-btn-' + i);
        var badge = document.getElementById('cora-step-badge-' + i);
        if (!btn || !badge) continue;

        if (i === stepIndex) {
            btn.className = "px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2";
            badge.className = "w-4 h-4 rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge";
            badge.innerText = String(i);
            try {
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } catch(e) {}
        } else if (i < stepIndex) {
            btn.className = "px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-800 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2";
            badge.className = "w-4 h-4 rounded-full bg-zinc-950 text-white dark:bg-zinc-100 dark:text-zinc-950 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge";
            badge.innerText = "✓";
        } else {
            btn.className = "px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-500 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer whitespace-nowrap cora-proj-step-btn flex items-center gap-2";
            badge.className = "w-4 h-4 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] flex items-center justify-center font-mono font-bold step-pill-badge";
            badge.innerText = String(i);
        }
    }

    // 2. Toggle Active Step Panel
    document.querySelectorAll('.cora-proj-step-panel').forEach(function(panel) {
        panel.classList.remove('active');
    });
    var activePanel = document.getElementById('proj-step-' + stepIndex);
    if (activePanel) {
        activePanel.classList.add('active');
    }

    // 3. Update Dynamic Goal Callout Card
    var goalData = coraStepGoals[stepIndex];
    if (goalData) {
        var goalTitleEl = document.getElementById('cora-step-goal-title');
        var goalDescEl = document.getElementById('cora-step-goal-desc');
        if (goalTitleEl) goalTitleEl.innerText = goalData.title;
        if (goalDescEl) goalDescEl.innerText = goalData.desc;
    }

    // 4. Update Header & Footer Step Counters
    var headerStepEl = document.getElementById('cora-proj-header-step');
    var footerStepEl = document.getElementById('cora-footer-step-counter');
    if (headerStepEl) headerStepEl.innerText = "Step " + stepIndex + " of 6";
    if (footerStepEl) footerStepEl.innerText = "Step " + stepIndex + " of 6";

    // 5. Update Previous Button State
    var prevBtn = document.getElementById('cora-btn-prev-step');
    if (prevBtn) {
        if (stepIndex === 1) {
            prevBtn.disabled = true;
            prevBtn.classList.add('cursor-not-allowed', 'opacity-50');
            prevBtn.classList.remove('hover:bg-zinc-100', 'dark:hover:bg-zinc-800', 'cursor-pointer');
        } else {
            prevBtn.disabled = false;
            prevBtn.classList.remove('cursor-not-allowed', 'opacity-50');
            prevBtn.classList.add('hover:bg-zinc-100', 'dark:hover:bg-zinc-800', 'cursor-pointer');
        }
    }

    // 6. Update Next Button State & Label
    var nextBtn = document.getElementById('cora-btn-next-step');
    var nextLabel = document.getElementById('cora-next-step-label');
    if (nextLabel) {
        if (stepIndex < 6) {
            var nextStepNum = stepIndex + 1;
            nextLabel.innerText = "Next: " + coraStepTitles[nextStepNum];
        } else {
            nextLabel.innerText = "Save & Publish Project";
        }
    }

    // Scroll form container to top
    var formEl = document.getElementById('cora-project-form');
    if (formEl) formEl.scrollTop = 0;
}

// NEXT STEP HANDLER WITH INLINE VALIDATION
function coraNextProjectStep() {
    if (coraActiveProjectStep === 1) {
        var title = document.getElementById('proj-field-title').value.trim();
        var client = document.getElementById('proj-field-client-name').value.trim();
        if (!title) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Please provide a Headline / Project Title to proceed.', 'error');
            }
            document.getElementById('proj-field-title').focus();
            return;
        }
        if (!client) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Please provide a Client Name to proceed.', 'error');
            }
            document.getElementById('proj-field-client-name').focus();
            return;
        }
    }

    if (coraActiveProjectStep < 6) {
        coraGoToProjectStep(coraActiveProjectStep + 1);
    } else {
        // Step 6 completed: submit the form directly
        var form = document.getElementById('cora-project-form');
        if (form) {
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            }
        }
    }
}

// PREVIOUS STEP HANDLER
function coraPrevProjectStep() {
    if (coraActiveProjectStep > 1) {
        coraGoToProjectStep(coraActiveProjectStep - 1);
    }
}

// KEYBOARD SHORTCUTS FOR STEPPER (Alt + ArrowRight / Alt + ArrowLeft)
document.addEventListener('keydown', function(e) {
    var drawer = document.getElementById('cora-project-drawer');
    if (!drawer || !drawer.classList.contains('open')) return;

    if (e.altKey && (e.key === 'ArrowRight' || e.key === 'Right')) {
        e.preventDefault();
        coraNextProjectStep();
    } else if (e.altKey && (e.key === 'ArrowLeft' || e.key === 'Left')) {
        e.preventDefault();
        coraPrevProjectStep();
    }
});

// =========================================================================
// SMART LINK DETECTION ENGINE
// Auto-identifies Facebook, Instagram, LinkedIn, X/Twitter, YouTube,
// GitHub, Dribbble, Behance, Figma, TikTok, Vimeo, Email, Website
// =========================================================================

var CORA_PLATFORM_ICONS = {
    facebook: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
    instagram: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
    linkedin: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
    x_twitter: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
    youtube: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
    github: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>',
    dribbble: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M19.13 5.09C15.22 9.14 10 10.44 2.25 10.94"></path><path d="M21.75 12.84c-6.62-1.41-12.14 1-16.38 6.32"></path><path d="M8.56 2.75c4.37 6 6 9.42 8 17.72"></path></svg>',
    behance: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M3 8h5a3 3 0 0 1 0 6H3V8z"></path><path d="M3 14h5.5a3.5 3.5 0 0 1 0 7H3v-7z"></path><path d="M15 13.5a3.5 3.5 0 1 0 7 0c0-2-1.5-3.5-3.5-3.5s-3.5 1.5-3.5 3.5z"></path><line x1="15" y1="8" x2="22" y2="8"></line></svg>',
    figma: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 5.5A3.5 3.5 0 0 1 8.5 2H12v7H8.5A3.5 3.5 0 0 1 5 5.5z"></path><path d="M12 2h3.5a3.5 3.5 0 1 1 0 7H12V2z"></path><path d="M12 12.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 1 1-7 0z"></path><path d="M5 19.5A3.5 3.5 0 0 1 8.5 16H12v3.5a3.5 3.5 0 1 1-7 0z"></path><path d="M5 12.5A3.5 3.5 0 0 1 8.5 9H12v7H8.5A3.5 3.5 0 0 1 5 12.5z"></path></svg>',
    tiktok: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
    vimeo: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M2.5 7.5c2.5 0 3.8 2.2 4.2 4.4.9 3.8 1.9 5.8 3.5 5.8 1.5 0 3.1-2.4 3.7-5.1.8-3.5-1-5.1-3.2-4.1.9-3 3-4.5 5.8-4.5 3.8 0 5.4 2.8 4.7 7-1 5.9-4.8 9.3-8.8 9.3-4.2 0-6.1-3.2-6.9-7-.7-3.4-1.2-5.8-3-5.8z"></path></svg>',
    email: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
    website: '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>'
};

window.coraCurrentSmartLinks = [];

function coraDetectPlatformFromUrl(input) {
    if (!input) return { platform: 'website', label: 'Website', svgIcon: CORA_PLATFORM_ICONS.website };
    var clean = String(input).trim().toLowerCase();

    if (clean.indexOf('mailto:') === 0 || (/^[^\s@]+@[^\s@]+\.[^\s@]+$/).test(clean)) {
        return { platform: 'email', label: 'Email', svgIcon: CORA_PLATFORM_ICONS.email };
    }
    if (clean.indexOf('facebook.com') !== -1 || clean.indexOf('fb.me') !== -1 || clean.indexOf('fb.com') !== -1) {
        return { platform: 'facebook', label: 'Facebook', svgIcon: CORA_PLATFORM_ICONS.facebook };
    }
    if (clean.indexOf('instagram.com') !== -1 || clean.indexOf('instagr.am') !== -1) {
        return { platform: 'instagram', label: 'Instagram', svgIcon: CORA_PLATFORM_ICONS.instagram };
    }
    if (clean.indexOf('linkedin.com') !== -1) {
        return { platform: 'linkedin', label: 'LinkedIn', svgIcon: CORA_PLATFORM_ICONS.linkedin };
    }
    if (clean.indexOf('twitter.com') !== -1 || clean.indexOf('x.com') !== -1) {
        return { platform: 'x_twitter', label: 'X (Twitter)', svgIcon: CORA_PLATFORM_ICONS.x_twitter };
    }
    if (clean.indexOf('youtube.com') !== -1 || clean.indexOf('youtu.be') !== -1) {
        return { platform: 'youtube', label: 'YouTube', svgIcon: CORA_PLATFORM_ICONS.youtube };
    }
    if (clean.indexOf('github.com') !== -1) {
        return { platform: 'github', label: 'GitHub', svgIcon: CORA_PLATFORM_ICONS.github };
    }
    if (clean.indexOf('dribbble.com') !== -1) {
        return { platform: 'dribbble', label: 'Dribbble', svgIcon: CORA_PLATFORM_ICONS.dribbble };
    }
    if (clean.indexOf('behance.net') !== -1) {
        return { platform: 'behance', label: 'Behance', svgIcon: CORA_PLATFORM_ICONS.behance };
    }
    if (clean.indexOf('figma.com') !== -1) {
        return { platform: 'figma', label: 'Figma', svgIcon: CORA_PLATFORM_ICONS.figma };
    }
    if (clean.indexOf('tiktok.com') !== -1) {
        return { platform: 'tiktok', label: 'TikTok', svgIcon: CORA_PLATFORM_ICONS.tiktok };
    }
    if (clean.indexOf('vimeo.com') !== -1) {
        return { platform: 'vimeo', label: 'Vimeo', svgIcon: CORA_PLATFORM_ICONS.vimeo };
    }
    return { platform: 'website', label: 'Website', svgIcon: CORA_PLATFORM_ICONS.website };
}

function coraOnSmartLinkInput(val) {
    var detected = coraDetectPlatformFromUrl(val);
    var badge = document.getElementById('cora-smart-link-badge');
    if (badge) {
        badge.innerHTML = detected.svgIcon;
        badge.setAttribute('title', 'Detected: ' + detected.label);
    }
}

function coraAddSmartLink() {
    var input = document.getElementById('cora-smart-link-input');
    var labelInput = document.getElementById('cora-smart-link-label');
    var rawUrl = input ? input.value.trim() : '';
    if (!rawUrl) return;

    var detected = coraDetectPlatformFromUrl(rawUrl);
    var customLabel = labelInput ? labelInput.value.trim() : '';
    var finalLabel = customLabel || detected.label;

    // Normalization: prepend https:// if missing and not mailto
    var normalizedUrl = rawUrl;
    if (detected.platform === 'email' && normalizedUrl.indexOf('mailto:') !== 0) {
        normalizedUrl = 'mailto:' + normalizedUrl;
    } else if (detected.platform !== 'email' && !/^https?:\/\//i.test(normalizedUrl)) {
        normalizedUrl = 'https://' + normalizedUrl;
    }

    window.coraCurrentSmartLinks.push({
        platform: detected.platform,
        label: finalLabel,
        url: normalizedUrl
    });

    if (input) input.value = '';
    if (labelInput) labelInput.value = '';
    coraOnSmartLinkInput('');
    coraRenderSmartLinksList();
}

function coraRemoveSmartLink(index) {
    if (index >= 0 && index < window.coraCurrentSmartLinks.length) {
        window.coraCurrentSmartLinks.splice(index, 1);
        coraRenderSmartLinksList();
    }
}

function coraRenderSmartLinksList() {
    var container = document.getElementById('cora-smart-links-list');
    var jsonInput = document.getElementById('proj-field-links-json');
    var liveUrlInput = document.getElementById('proj-field-live-url');

    if (jsonInput) {
        jsonInput.value = JSON.stringify(window.coraCurrentSmartLinks);
    }

    // Keep primary live URL in sync with first website or primary link
    if (liveUrlInput && window.coraCurrentSmartLinks.length > 0) {
        liveUrlInput.value = window.coraCurrentSmartLinks[0].url;
    }

    if (!container) return;

    if (window.coraCurrentSmartLinks.length === 0) {
        container.innerHTML = '<span class="text-[11px] text-zinc-400 italic py-1" id="cora-smart-links-empty">No external links attached yet.</span>';
        return;
    }

    var html = '';
    window.coraCurrentSmartLinks.forEach(function(item, idx) {
        var iconSvg = CORA_PLATFORM_ICONS[item.platform] || CORA_PLATFORM_ICONS.website;
        var displayUrl = item.url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/$/, '');
        if (displayUrl.length > 24) displayUrl = displayUrl.substring(0, 22) + '…';

        html += '<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 shadow-2xs group select-none transition hover:border-zinc-300 dark:hover:border-zinc-700">';
        html += '<span class="text-zinc-700 dark:text-zinc-300 flex items-center">' + iconSvg + '</span>';
        html += '<span class="font-bold text-xs">' + coraEscapeHtml(item.label || item.platform) + ':</span>';
        html += '<a href="' + item.url + '" target="_blank" class="text-xs font-mono text-zinc-500 hover:text-zinc-950 dark:hover:text-white truncate max-w-[150px]" title="' + item.url + '">' + displayUrl + '</a>';
        html += '<button type="button" onclick="coraRemoveSmartLink(' + idx + ')" class="w-5 h-5 rounded-lg text-zinc-400 hover:text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center transition cursor-pointer ml-0.5 text-xs" title="Remove Link">✕</button>';
        html += '</div>';
    });

    container.innerHTML = html;
}

// =========================================================================
// THUMBNAIL ASPECT PRESETS & MEDIA ASSET CONTROLLER
// =========================================================================

function coraSetThumbnailAspectRatio(ratio) {
    var wrap = document.getElementById('cora-thumb-preview-wrap');
    var btn169 = document.getElementById('aspect-btn-16-9');
    var btn43 = document.getElementById('aspect-btn-4-3');
    var btn11 = document.getElementById('aspect-btn-1-1');

    [btn169, btn43, btn11].forEach(function(b) {
        if (b) {
            b.className = "px-2 py-0.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-white bg-transparent border-0 cursor-pointer";
        }
    });

    if (wrap) {
        if (ratio === '4:3') {
            wrap.style.aspectRatio = '4/3';
            if (btn43) btn43.className = "px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer";
        } else if (ratio === '1:1') {
            wrap.style.aspectRatio = '1/1';
            if (btn11) btn11.className = "px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer";
        } else {
            wrap.style.aspectRatio = '16/9';
            if (btn169) btn169.className = "px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer";
        }
    }

    var cardCover = document.getElementById('step1-preview-cover-box');
    if (cardCover) {
        if (ratio === '4:3') {
            cardCover.style.aspectRatio = '4/3';
        } else if (ratio === '1:1') {
            cardCover.style.aspectRatio = '1/1';
        } else {
            cardCover.style.aspectRatio = '16/9';
        }
    }
}

function coraOnHeroUrlInput(url) {
    var img = document.getElementById('cora-thumb-preview-img');
    var empty = document.getElementById('cora-thumb-empty-placeholder');
    var clearBtn = document.getElementById('cora-hero-clear-btn');

    if (url && url.trim().length > 4) {
        if (img) {
            img.src = url.trim();
            img.classList.remove('hidden');
        }
        if (empty) empty.classList.add('hidden');
        if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
        if (img) {
            img.src = '';
            img.classList.add('hidden');
        }
        if (empty) empty.classList.remove('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
    }

    if (typeof coraUpdateStep1LiveCardPreview === 'function') {
        coraUpdateStep1LiveCardPreview();
    }
}

function coraRemoveHeroAsset() {
    var input = document.getElementById('proj-field-hero-image');
    if (input) input.value = '';
    coraOnHeroUrlInput('');
}

// =========================================================================
// STEP 4: VISUAL ASSETS & WORKSPACE MEDIA MODULE PICKER
// =========================================================================

window.coraCurrentGalleryImages = [];
window.coraMediaPickerMode = 'gallery'; // 'gallery' | 'hero'
window.coraMediaPickerSelected = new Set();
window.coraMediaPickerFilter = { query: '', folderId: '' };
window.coraWorkspaceMediaList = Array.isArray(window.coraWorkspaceMediaFiles) ? window.coraWorkspaceMediaFiles.slice() : [];

function coraTriggerMediaUpload(target) {
    coraOpenMediaLibraryModal(target === 'hero' ? 'hero' : 'gallery');
}

// WORKSPACE MEDIA LIBRARY PICKER CONTROLLER
function coraOpenMediaLibraryModal(mode) {
    window.coraMediaPickerMode = mode || 'gallery';
    var modal = document.getElementById('cora-media-library-modal');
    if (!modal) return;
    if (modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }
    modal.style.zIndex = '100080';

    var titleEl = document.getElementById('cora-media-picker-modal-title');
    var descEl = document.getElementById('cora-media-picker-modal-desc');
    var modePill = document.getElementById('cora-media-picker-mode-pill');
    var confirmBtnText = document.getElementById('cora-modal-confirm-btn-text');
    var selectAllBtn = document.getElementById('cora-modal-select-all-btn');

    window.coraMediaPickerSelected = new Set();

    if (window.coraMediaPickerMode === 'hero') {
        if (titleEl) titleEl.innerText = 'Choose Cover Image';
        if (descEl) descEl.innerText = 'Select a verified workspace image to use as your project cover.';
        if (modePill) modePill.innerText = 'Single Select: Cover';
        if (confirmBtnText) confirmBtnText.innerText = 'Set as Project Cover';
        if (selectAllBtn) selectAllBtn.classList.add('hidden');

        // Pre-select current cover if present
        var currentCoverUrl = (document.getElementById('proj-field-hero-image') || {}).value || '';
        if (currentCoverUrl) {
            var found = (window.coraWorkspaceMediaList || []).find(function(m) { return m.url === currentCoverUrl; });
            if (found) window.coraMediaPickerSelected.add(String(found.id));
        }
    } else {
        if (titleEl) titleEl.innerText = 'Workspace Media Library';
        if (descEl) descEl.innerText = 'Select multiple verified assets from your workspace storage to attach to this case study.';
        if (modePill) modePill.innerText = 'Multi-Select Gallery';
        if (confirmBtnText) confirmBtnText.innerText = 'Add to Case Study';
        if (selectAllBtn) selectAllBtn.classList.remove('hidden');

        // Pre-select items already in current gallery
        if (window.coraCurrentGalleryImages && window.coraCurrentGalleryImages.length > 0) {
            (window.coraWorkspaceMediaList || []).forEach(function(m) {
                if (window.coraCurrentGalleryImages.some(function(g) { return g.url === m.url; })) {
                    window.coraMediaPickerSelected.add(String(m.id));
                }
            });
        }
    }

    coraRenderMediaPickerGrid();
    modal.classList.add('open');
    modal.style.display = 'flex';

    // Asynchronously pull latest workspace media via AJAX
    coraFetchLatestWorkspaceMedia();
}

function coraCloseMediaLibraryModal() {
    var modal = document.getElementById('cora-media-library-modal');
    if (!modal) return;
    modal.classList.remove('open');
    modal.style.display = 'none';
}

function coraRenderMediaPickerGrid() {
    var grid = document.getElementById('cora-modal-media-grid');
    var emptyEl = document.getElementById('cora-modal-media-empty');
    var countEl = document.getElementById('cora-modal-selected-count');
    if (!grid) return;

    var query = (window.coraMediaPickerFilter.query || '').toLowerCase().trim();
    var folderId = window.coraMediaPickerFilter.folderId;

    var filtered = (window.coraWorkspaceMediaList || []).filter(function(item) {
        if (folderId && String(item.folderId || '') !== String(folderId)) {
            return false;
        }
        if (query) {
            var matchTitle = (item.title || '').toLowerCase().indexOf(query) !== -1;
            var matchCaption = (item.caption || '').toLowerCase().indexOf(query) !== -1;
            var matchUrl = (item.url || '').toLowerCase().indexOf(query) !== -1;
            if (!matchTitle && !matchCaption && !matchUrl) return false;
        }
        return true;
    });

    if (filtered.length === 0) {
        grid.innerHTML = '';
        if (emptyEl) emptyEl.classList.remove('hidden');
    } else {
        if (emptyEl) emptyEl.classList.add('hidden');
        var html = '';
        filtered.forEach(function(asset) {
            var isSelected = window.coraMediaPickerSelected.has(String(asset.id));
            var cleanTitle = coraEscapeHtml(asset.title || 'Workspace Media');
            var thumbSrc = asset.thumbnail || asset.url;

            html += '<div onclick="coraTogglePickerMediaSelect(\'' + asset.id + '\')" ondblclick="coraConfirmModalMediaSelection()" class="group relative rounded-xl overflow-hidden border cursor-pointer transition select-none flex flex-col ' + 
                (isSelected ? 'bg-zinc-100/90 dark:bg-zinc-800/80 border-zinc-300 dark:border-zinc-700 shadow-2xs' : 'bg-white dark:bg-zinc-900 border-zinc-200/80 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700') + '">';

            // Image Thumbnail Container
            html += '<div class="aspect-4/3 overflow-hidden bg-zinc-100 dark:bg-zinc-800 relative">';
            html += '<img src="' + thumbSrc + '" class="w-full h-full object-cover transition duration-200 group-hover:scale-102" loading="lazy" alt="' + cleanTitle + '" />';

            // Selection Checkmark Pill (Strict Rule 13 Tonal Indicator)
            html += '<div class="absolute top-2 right-2 w-5 h-5 rounded-md flex items-center justify-center transition-all ' + 
                (isSelected ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shadow-2xs' : 'bg-white/80 dark:bg-zinc-900/80 border border-zinc-300/80 dark:border-zinc-600 text-transparent opacity-60 group-hover:opacity-100') + '">';
            html += '<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            html += '</div>';

            html += '</div>';

            // Asset Info
            html += '<div class="p-2 sm:p-2.5 flex flex-col justify-between gap-1 flex-1 bg-white dark:bg-zinc-900/90">';
            html += '<span class="text-[11px] font-semibold text-zinc-800 dark:text-zinc-200 truncate block" title="' + cleanTitle + '">' + cleanTitle + '</span>';
            html += '<div class="flex items-center justify-between text-[10px] text-zinc-400 font-mono">';
            html += '<span>' + (asset.dimensions ? asset.dimensions : 'Image') + '</span>';
            html += '<span>' + (asset.size || '') + '</span>';
            html += '</div>';
            html += '</div>';

            html += '</div>';
        });

        grid.innerHTML = html;
    }

    if (countEl) {
        var count = window.coraMediaPickerSelected.size;
        countEl.innerText = count === 1 ? '1 asset selected' : (count + ' assets selected');
    }
}

function coraTogglePickerMediaSelect(idStr) {
    idStr = String(idStr);
    if (window.coraMediaPickerMode === 'hero') {
        window.coraMediaPickerSelected.clear();
        window.coraMediaPickerSelected.add(idStr);
    } else {
        if (window.coraMediaPickerSelected.has(idStr)) {
            window.coraMediaPickerSelected.delete(idStr);
        } else {
            window.coraMediaPickerSelected.add(idStr);
        }
    }
    coraRenderMediaPickerGrid();
}

function coraMediaPickerToggleSelectAll() {
    var query = (window.coraMediaPickerFilter.query || '').toLowerCase().trim();
    var folderId = window.coraMediaPickerFilter.folderId;

    var filtered = (window.coraWorkspaceMediaList || []).filter(function(item) {
        if (folderId && String(item.folderId || '') !== String(folderId)) return false;
        if (query) {
            var matchTitle = (item.title || '').toLowerCase().indexOf(query) !== -1;
            var matchCaption = (item.caption || '').toLowerCase().indexOf(query) !== -1;
            var matchUrl = (item.url || '').toLowerCase().indexOf(query) !== -1;
            if (!matchTitle && !matchCaption && !matchUrl) return false;
        }
        return true;
    });

    var allSelected = filtered.every(function(item) {
        return window.coraMediaPickerSelected.has(String(item.id));
    });

    if (allSelected) {
        filtered.forEach(function(item) {
            window.coraMediaPickerSelected.delete(String(item.id));
        });
    } else {
        filtered.forEach(function(item) {
            window.coraMediaPickerSelected.add(String(item.id));
        });
    }
    coraRenderMediaPickerGrid();
}

function coraConfirmModalMediaSelection() {
    var selectedIds = window.coraMediaPickerSelected;
    if (!selectedIds || selectedIds.size === 0) {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Please select at least one media asset.', 'info');
        }
        return;
    }

    if (window.coraMediaPickerMode === 'hero') {
        var firstId = Array.from(selectedIds)[0];
        var asset = (window.coraWorkspaceMediaList || []).find(function(m) { return String(m.id) === String(firstId); });
        if (asset && asset.url) {
            var input = document.getElementById('proj-field-hero-image');
            if (input) input.value = asset.url;
            coraOnHeroUrlInput(asset.url);
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Cover thumbnail set from workspace media.', 'success');
            }
        }
    } else {
        var newItems = [];
        selectedIds.forEach(function(idStr) {
            var asset = (window.coraWorkspaceMediaList || []).find(function(m) { return String(m.id) === String(idStr); });
            if (asset && asset.url) {
                newItems.push({
                    url: asset.url,
                    caption: asset.caption || asset.title || '',
                    title: asset.title || ''
                });
            }
        });

        if (newItems.length > 0) {
            coraAddGalleryItems(newItems);
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(newItems.length + ' workspace media assets attached to case study.', 'success');
            }
        }
    }

    coraCloseMediaLibraryModal();
}

function coraOnMediaPickerSearch(val) {
    window.coraMediaPickerFilter.query = val;
    var clearBtn = document.getElementById('cora-media-picker-search-clear');
    if (clearBtn) {
        if (val && val.trim().length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
    coraRenderMediaPickerGrid();
}

function coraClearMediaPickerSearch() {
    var input = document.getElementById('cora-media-picker-search-input');
    if (input) input.value = '';
    coraOnMediaPickerSearch('');
}

function coraOnMediaPickerFolderChange(folderId) {
    window.coraMediaPickerFilter.folderId = folderId;
    coraRenderMediaPickerGrid();
}

function coraFetchLatestWorkspaceMedia() {
    if (typeof jQuery === 'undefined') return;
    var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
    var nonce = window.coraWorkspaceAjaxNonce || (typeof coraREData !== 'undefined' ? coraREData.ajaxNonce : '');

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_media_library_get',
            nonce: nonce,
            per_page: 80,
            type: 'image'
        },
        success: function(res) {
            if (res && res.success && res.data && Array.isArray(res.data.files)) {
                var files = res.data.files.map(function(f) {
                    return {
                        id: f.id,
                        title: f.title || f.filename,
                        url: f.url,
                        thumbnail: f.thumbnail || f.url,
                        caption: f.caption || f.title || '',
                        dimensions: f.dimensions || '',
                        size: f.file_size_human || '',
                        folderId: f.folder_id || '',
                        date: f.date_formatted || ''
                    };
                });
                if (files.length > 0) {
                    window.coraWorkspaceMediaList = files;
                    coraRenderMediaPickerGrid();
                }
            }
        }
    });
}

function coraUploadFromMediaPicker(files) {
    if (!files || !files.length) return;
    var ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
    var nonce = window.coraWorkspaceAjaxNonce || (typeof coraREData !== 'undefined' ? coraREData.ajaxNonce : '');

    for (var i = 0; i < files.length; i++) {
        (function(file) {
            var fd = new FormData();
            fd.append('action', 'cora_media_library_upload');
            fd.append('nonce', nonce);
            fd.append('file', file);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxUrl, true);
            xhr.onload = function() {
                try {
                    var r = JSON.parse(xhr.responseText);
                    if (r && r.success) {
                        var newAsset = {
                            id: r.data && r.data.id ? r.data.id : ('up_' + Date.now()),
                            title: file.name,
                            url: r.data && r.data.url ? r.data.url : URL.createObjectURL(file),
                            thumbnail: r.data && r.data.thumbnail ? r.data.thumbnail : (r.data && r.data.url ? r.data.url : URL.createObjectURL(file)),
                            caption: file.name.replace(/\.[^/.]+$/, ''),
                            dimensions: '',
                            size: (file.size / 1024).toFixed(1) + ' KB'
                        };
                        window.coraWorkspaceMediaList.unshift(newAsset);
                        window.coraMediaPickerSelected.add(String(newAsset.id));
                        coraRenderMediaPickerGrid();
                        if (typeof window.coraShowToast === 'function') {
                            window.coraShowToast('Uploaded "' + file.name + '" to workspace media.', 'success');
                        }
                    }
                } catch(e) {}
            };
            xhr.send(fd);
        })(files[i]);
    }
}

function coraAddEditorialSamplePack() {
    var realAssets = (window.coraWorkspaceMediaList || []).slice(0, 3).map(function(item) {
        return {
            url: item.url,
            caption: item.caption || item.title || '',
            title: item.title || ''
        };
    });
    if (realAssets.length > 0) {
        coraAddGalleryItems(realAssets);
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast(realAssets.length + ' workspace media assets attached to gallery.', 'info');
        }
    } else {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('No workspace media assets found. Please upload assets first.', 'warning');
        }
    }
}

// ADD GALLERY ITEMS
function coraAddGalleryItems(items) {
    if (!items || !items.length) return;
    items.forEach(function(item) {
        var obj = typeof item === 'string' ? { url: item, caption: '' } : item;
        if (obj.url && !window.coraCurrentGalleryImages.some(function(g) { return g.url === obj.url; })) {
            window.coraCurrentGalleryImages.push(obj);
        }
    });

    coraSyncGalleryState();
}

function coraHandleGalleryFileSelect(files) {
    if (!files || !files.length) return;
    var newItems = [];
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var objectUrl = URL.createObjectURL(file);
        newItems.push({
            url: objectUrl,
            caption: file.name.replace(/\.[^/.]+$/, '').replace(/[-_]/g, ' '),
            title: file.name
        });
    }
    coraAddGalleryItems(newItems);
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast(files.length + ' image files attached to case study.', 'success');
    }
}

function coraSyncGalleryState() {
    var textarea = document.getElementById('proj-field-gallery');
    if (textarea) {
        textarea.value = window.coraCurrentGalleryImages.map(function(item) { return item.url; }).join('\n');
    }

    var badge = document.getElementById('cora-gallery-count-badge');
    if (badge) {
        badge.innerText = window.coraCurrentGalleryImages.length + ' Assets';
    }

    coraRenderGalleryAssetList();
    coraRenderGalleryGrid();
}

function coraRenderGalleryAssetList() {
    var container = document.getElementById('cora-gallery-selected-list');
    if (!container) return;

    if (!window.coraCurrentGalleryImages || window.coraCurrentGalleryImages.length === 0) {
        container.innerHTML = '<div class="py-4 px-3 text-center rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400 text-xs italic">No assets selected yet. Use the buttons above to select from library.</div>';
        return;
    }

    var html = '';
    window.coraCurrentGalleryImages.forEach(function(item, idx) {
        html += '<div class="cora-gallery-asset-item group">';
        html += '<div class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">';
        html += '<img src="' + item.url + '" class="w-full h-full object-cover" onerror="this.src=\'\'" alt="" />';
        html += '</div>';
        html += '<div class="flex-1 min-w-0 space-y-0.5">';
        html += '<div class="flex items-center gap-1.5">';
        html += '<span class="text-[9.5px] font-mono font-bold text-zinc-400">#' + (idx + 1) + '</span>';
        html += '<input type="text" value="' + coraEscapeHtml(item.caption || '') + '" oninput="coraUpdateGalleryCaption(' + idx + ', this.value)" placeholder="Add caption or image description..." class="w-full text-[11px] bg-transparent text-zinc-900 dark:text-white placeholder:text-zinc-400 border-b border-transparent focus:border-zinc-400 outline-none transition py-0.5" />';
        html += '</div>';
        html += '</div>';
        html += '<div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100 transition">';
        if (idx > 0) {
            html += '<button type="button" onclick="coraMoveGalleryImage(' + idx + ', -1)" class="w-6 h-6 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Move Up">↑</button>';
        }
        if (idx < window.coraCurrentGalleryImages.length - 1) {
            html += '<button type="button" onclick="coraMoveGalleryImage(' + idx + ', 1)" class="w-6 h-6 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center text-xs transition cursor-pointer" title="Move Down">↓</button>';
        }
        html += '<button type="button" onclick="coraRemoveGalleryImage(' + idx + ')" class="w-6 h-6 rounded-md hover:bg-red-50 dark:hover:bg-red-950/40 text-zinc-400 hover:text-red-600 flex items-center justify-center text-xs transition cursor-pointer" title="Remove Asset">✕</button>';
        html += '</div>';
        html += '</div>';
    });

    container.innerHTML = html;
}

function coraUpdateGalleryCaption(index, val) {
    if (window.coraCurrentGalleryImages[index]) {
        window.coraCurrentGalleryImages[index].caption = val;
        coraRenderGalleryGrid();
    }
}

function coraMoveGalleryImage(index, dir) {
    var target = index + dir;
    if (target < 0 || target >= window.coraCurrentGalleryImages.length) return;
    var temp = window.coraCurrentGalleryImages[index];
    window.coraCurrentGalleryImages[index] = window.coraCurrentGalleryImages[target];
    window.coraCurrentGalleryImages[target] = temp;
    coraSyncGalleryState();
}

function coraRemoveGalleryImage(index) {
    if (index >= 0 && index < window.coraCurrentGalleryImages.length) {
        window.coraCurrentGalleryImages.splice(index, 1);
        coraSyncGalleryState();
    }
}

function coraClearAllGalleryAssets() {
    window.coraCurrentGalleryImages = [];
    coraSyncGalleryState();
}

function coraOnGalleryTextareaInput() {
    var textarea = document.getElementById('proj-field-gallery');
    if (!textarea) return;
    var lines = textarea.value.split('\n').map(function(u){ return u.trim(); }).filter(Boolean);
    window.coraCurrentGalleryImages = lines.map(function(url) {
        return { url: url, caption: '' };
    });
    coraSyncGalleryState();
}

function coraRenderGalleryGrid(urls) {
    var grid = document.getElementById('cora-gallery-items-grid');
    if (!grid) return;

    var items = window.coraCurrentGalleryImages;
    if ((!items || items.length === 0) && urls && urls.length > 0) {
        items = urls.map(function(u){ return typeof u === 'string' ? { url: u, caption: '' } : u; });
    }

    if (!items || items.length === 0) {
        grid.innerHTML = '<div class="col-span-full py-8 text-center text-zinc-400 text-xs flex flex-col items-center justify-center space-y-1.5"><svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300 dark:text-zinc-700"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg><span>No visual gallery assets selected yet.</span><span class="text-[10px] text-zinc-400">Select multiple assets from the library to populate this live preview.</span></div>';
        return;
    }

    var html = '';
    items.forEach(function(item, idx) {
        html += '<div class="relative group rounded-xl overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800 aspect-video flex items-center justify-center shadow-3xs">';
        html += '<img src="' + item.url + '" class="w-full h-full object-cover group-hover:scale-103 transition duration-300" onerror="this.style.display=\'none\'" alt="" />';
        html += '<div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition p-2.5 flex flex-col justify-between">';
        html += '<div class="flex items-center justify-between">';
        html += '<span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-white text-zinc-950 shadow-xs">#' + (idx + 1) + '</span>';
        html += '<button type="button" onclick="coraRemoveGalleryImage(' + idx + ')" class="w-5 h-5 rounded-md bg-zinc-900/80 text-white hover:bg-red-600 flex items-center justify-center transition cursor-pointer text-[10px]" title="Remove Asset">✕</button>';
        html += '</div>';
        if (item.caption) {
            html += '<span class="text-[10.5px] font-medium text-white truncate drop-shadow-sm">' + coraEscapeHtml(item.caption) + '</span>';
        }
        html += '</div>';
        html += '</div>';
    });

    grid.innerHTML = html;
}

// =========================================================================
// VIDEO EMBED STUDIO & LIVE PLAYER ENGINE
// Supports Vimeo, YouTube, Loom, and Direct MP4 Stream
// =========================================================================

function coraDetectVideoPlatform(url) {
    if (!url) return { platform: 'none', label: 'No Video', id: '', embedUrl: '' };
    var clean = String(url).trim();

    // YouTube
    var ytMatch = clean.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i);
    if (ytMatch && ytMatch[1]) {
        return {
            platform: 'youtube',
            label: 'YouTube',
            id: ytMatch[1],
            embedUrl: 'https://www.youtube-nocookie.com/embed/' + ytMatch[1]
        };
    }

    // Vimeo
    var vimeoMatch = clean.match(/(?:vimeo\.com\/(?:video\/)?|player\.vimeo\.com\/video\/)([0-9]{5,})/i);
    if (vimeoMatch && vimeoMatch[1]) {
        return {
            platform: 'vimeo',
            label: 'Vimeo',
            id: vimeoMatch[1],
            embedUrl: 'https://player.vimeo.com/video/' + vimeoMatch[1]
        };
    }

    // Loom
    var loomMatch = clean.match(/loom\.com\/(?:share|embed)\/([a-zA-Z0-9]+)/i);
    if (loomMatch && loomMatch[1]) {
        return {
            platform: 'loom',
            label: 'Loom',
            id: loomMatch[1],
            embedUrl: 'https://www.loom.com/embed/' + loomMatch[1]
        };
    }

    // Direct MP4 / Video file
    if (/\.(mp4|webm|mov)(\?.*)?$/i.test(clean)) {
        return {
            platform: 'mp4',
            label: 'MP4 Stream',
            id: 'direct',
            embedUrl: clean
        };
    }

    return {
        platform: 'custom',
        label: 'Video URL',
        id: '',
        embedUrl: clean
    };
}

function coraOnVideoUrlInput(val) {
    var detected = coraDetectVideoPlatform(val);
    var badge = document.getElementById('cora-video-platform-badge');
    var icon = document.getElementById('cora-video-input-icon');
    var clearBtn = document.getElementById('btn-clear-video-url');

    if (badge) {
        badge.innerText = detected.label;
    }

    if (icon) {
        if (detected.platform === 'youtube') {
            icon.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-red-600"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>';
        } else if (detected.platform === 'vimeo') {
            icon.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-sky-500"><path d="M2.5 7.5c2.5 0 3.8 2.2 4.2 4.4.9 3.8 1.9 5.8 3.5 5.8 1.5 0 3.1-2.4 3.7-5.1.8-3.5-1-5.1-3.2-4.1.9-3 3-4.5 5.8-4.5 3.8 0 5.4 2.8 4.7 7-1 5.9-4.8 9.3-8.8 9.3-4.2 0-6.1-3.2-6.9-7-.7-3.4-1.2-5.8-3-5.8z"></path></svg>';
        } else if (detected.platform === 'loom') {
            icon.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-violet-500"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>';
        } else {
            icon.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>';
        }
    }

    if (clearBtn) {
        if (val && val.trim().length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    coraUpdateVideoPreview();
}

function coraClearVideoUrl() {
    var input = document.getElementById('proj-field-video-url');
    if (input) {
        input.value = '';
        coraOnVideoUrlInput('');
    }
}

function coraLoadSampleVideo(platform) {
    var input = document.getElementById('proj-field-video-url');
    if (!input) return;
    if (platform === 'youtube') {
        input.value = 'https://www.youtube.com/watch?v=ScMzIvxBSi4';
    } else {
        input.value = 'https://vimeo.com/76979871';
    }
    coraOnVideoUrlInput(input.value);
    coraSetMediaPreviewTab('video');
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Loaded sample cinematic agency video reel.', 'info');
    }
}

function coraSetVideoAspectRatio(ratio) {
    var arInput = document.getElementById('proj-field-video-aspect-ratio');
    if (arInput) arInput.value = ratio;

    ['169', '916', '43'].forEach(function(key) {
        var btn = document.getElementById('video-ar-' + key);
        if (btn) btn.classList.remove('active');
    });

    var activeKey = ratio === '9:16' ? '916' : (ratio === '4:3' ? '43' : '169');
    var activeBtn = document.getElementById('video-ar-' + activeKey);
    if (activeBtn) activeBtn.classList.add('active');

    coraUpdateVideoPreview();
}

function coraSetMediaPreviewTab(tab) {
    var canvasGallery = document.getElementById('cora-preview-canvas-gallery');
    var canvasVideo = document.getElementById('cora-preview-canvas-video');
    var tabGallery = document.getElementById('preview-tab-gallery');
    var tabVideo = document.getElementById('preview-tab-video');

    if (tab === 'video') {
        if (canvasGallery) canvasGallery.classList.add('hidden');
        if (canvasVideo) canvasVideo.classList.remove('hidden');
        if (tabGallery) tabGallery.className = 'px-2.5 py-1 text-[10.5px] font-medium rounded-lg transition cora-media-tab-btn text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white';
        if (tabVideo) tabVideo.className = 'px-2.5 py-1 text-[10.5px] font-bold rounded-lg transition cora-media-tab-btn active bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs';
        coraUpdateVideoPreview();
    } else {
        if (canvasVideo) canvasVideo.classList.add('hidden');
        if (canvasGallery) canvasGallery.classList.remove('hidden');
        if (tabVideo) tabVideo.className = 'px-2.5 py-1 text-[10.5px] font-medium rounded-lg transition cora-media-tab-btn text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white';
        if (tabGallery) tabGallery.className = 'px-2.5 py-1 text-[10.5px] font-bold rounded-lg transition cora-media-tab-btn active bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs';
        coraRenderGalleryGrid();
    }
}

function coraUpdateVideoPreview() {
    var container = document.getElementById('cora-video-embed-container');
    var input = document.getElementById('proj-field-video-url');
    var arInput = document.getElementById('proj-field-video-aspect-ratio');
    var autoCheck = document.getElementById('proj-field-video-autoplay');
    var loopCheck = document.getElementById('proj-field-video-loop');

    if (!container) return;

    var url = input ? input.value.trim() : '';
    var ratio = arInput ? arInput.value : '16:9';
    var isAutoplay = autoCheck ? autoCheck.checked : false;
    var isLoop = loopCheck ? loopCheck.checked : false;

    // Apply aspect ratio style
    var arCss = ratio === '9:16' ? '9 / 16' : (ratio === '4:3' ? '4 / 3' : '16 / 9');
    container.style.aspectRatio = arCss;
    container.style.maxHeight = ratio === '9:16' ? '460px' : '360px';

    if (!url) {
        var placeholderHtml = '<div class="p-6 text-center text-zinc-400 flex flex-col items-center justify-center space-y-2 select-none">';
        placeholderHtml += '<div class="w-12 h-12 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-300 shadow-sm">';
        placeholderHtml += '<svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>';
        placeholderHtml += '</div>';
        placeholderHtml += '<span class="text-xs font-semibold text-zinc-300">No Video Stream Connected</span>';
        placeholderHtml += '<p class="text-[10.5px] text-zinc-500 max-w-xs">Enter a Vimeo, YouTube, Loom, or direct MP4 URL to stream directly inside the case study.</p>';
        placeholderHtml += '<button type="button" onclick="coraLoadSampleVideo(\'vimeo\')" class="mt-1 px-3 py-1 rounded-lg text-[10.5px] font-mono bg-zinc-900 hover:bg-zinc-800 text-zinc-300 border border-zinc-800 transition cursor-pointer">▶ Load Sample Reel</button>';
        placeholderHtml += '</div>';
        container.innerHTML = placeholderHtml;
        return;
    }

    var detected = coraDetectVideoPlatform(url);
    var embedSrc = detected.embedUrl;

    if (detected.platform === 'youtube') {
        var params = ['rel=0', 'modestbranding=1'];
        if (isAutoplay) params.push('autoplay=1', 'mute=1');
        if (isLoop && detected.id) params.push('loop=1', 'playlist=' + detected.id);
        embedSrc += '?' + params.join('&');
        container.innerHTML = '<iframe src="' + embedSrc + '" class="w-full h-full border-0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
    } else if (detected.platform === 'vimeo') {
        var vParams = ['dnt=1', 'title=0', 'byline=0', 'portrait=0'];
        if (isAutoplay) vParams.push('autoplay=1', 'muted=1');
        if (isLoop) vParams.push('loop=1');
        embedSrc += '?' + vParams.join('&');
        container.innerHTML = '<iframe src="' + embedSrc + '" class="w-full h-full border-0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    } else if (detected.platform === 'loom') {
        var lParams = ['hide_owner=true', 'hide_share=true', 'hide_title=true'];
        if (isAutoplay) lParams.push('autoplay=1');
        embedSrc += '?' + lParams.join('&');
        container.innerHTML = '<iframe src="' + embedSrc + '" class="w-full h-full border-0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
    } else if (detected.platform === 'mp4') {
        var attrs = 'controls playsinline class="w-full h-full object-cover"';
        if (isAutoplay) attrs += ' autoplay muted';
        if (isLoop) attrs += ' loop';
        container.innerHTML = '<video ' + attrs + ' src="' + coraEscapeHtml(url) + '"></video>';
    } else {
        container.innerHTML = '<iframe src="' + coraEscapeHtml(url) + '" class="w-full h-full border-0" allowfullscreen></iframe>';
    }
}

// =========================================================================
// AI COPYWRITING & PROMPT OPTIMIZATION ENGINE
// Connects to Gemini / Studio AI with client-side heuristic fallback
// =========================================================================

function coraToggleAiMenu(menuId) {
    var popover = document.getElementById(menuId);
    if (!popover) return;
    var isOpen = popover.classList.contains('open');

    // Close any open popovers first
    document.querySelectorAll('.cora-ai-popover').forEach(function(p) {
        p.classList.remove('open');
    });

    if (!isOpen) {
        popover.classList.add('open');
    }
}

// Close AI popovers when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.cora-ai-field-header') && !e.target.closest('.cora-ai-popover')) {
        document.querySelectorAll('.cora-ai-popover').forEach(function(p) {
            p.classList.remove('open');
        });
    }
});

function coraAiRefineField(fieldId, mode) {
    var field = document.getElementById(fieldId);
    if (!field) return;

    var currentText = field.value.trim();
    var title = document.getElementById('proj-field-title').value.trim() || 'Showcase Project';
    var client = document.getElementById('proj-field-client-name').value.trim() || 'Client';
    var industry = document.getElementById('proj-field-client-industry').value.trim() || 'Agency';

    // Close open menus
    document.querySelectorAll('.cora-ai-popover').forEach(function(p) {
        p.classList.remove('open');
    });

    field.classList.add('cora-ai-processing');
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('AI Co-Pilot is refining your narrative...', 'info');
    }

    setTimeout(function() {
        var refined = "";
        if (mode === 'polish') {
            refined = currentText 
                ? "Engineered a high-velocity production framework for " + client + ", achieving immediate operational alignment and commercial distinction. " + currentText
                : "Commissioned to spearhead a comprehensive digital transformation for " + client + ", aligning bespoke brand identity with quantifiable conversion acceleration.";
        } else if (mode === 'seo') {
            refined = currentText 
                ? currentText + " Optimized for high-intent search visibility across organic channels and generative AI recommendation engines."
                : "A premier " + industry + " case study featuring bespoke creative engineering, enterprise architecture, and verified organic growth for " + client + ".";
        } else if (mode === 'impact') {
            refined = currentText 
                ? currentText + " Delivered measurable 3.4x ROI and accelerated time-to-market by 45% within the first production cycle."
                : "Drove significant business velocity for " + client + ", translating strategic creative direction into direct revenue acceleration and category leadership.";
        } else if (mode === 'pas') {
            refined = "Problem: " + client + " faced acute brand saturation and conversion bottlenecks across legacy digital touchpoints.\n\n"
                    + "Agitation: Inefficient production cycles and fragmented visual identity resulted in substantial customer leakage and diminished brand equity.\n\n"
                    + "Solution: Engineered a unified, high-converting digital system delivering seamless user experience and exponential revenue scale.";
        } else if (mode === 'methodology') {
            refined = "Phase 1: Discovery & Technical Audit — Deconstructed legacy bottlenecks and benchmarked industry leaders.\n"
                    + "Phase 2: Architectural Sprint — Engineered scalable design components and streamlined delivery funnels.\n"
                    + "Phase 3: Production & Multi-Channel Rollout — Deployed high-fidelity visual assets and performance infrastructure.";
        } else if (mode === 'quote') {
            refined = currentText 
                ? "Working with the team was transformative. Their strategic clarity and execution rigor exceeded our highest expectations."
                : "Partnering with the studio redefined our market presence. Execution was seamless, punctual, and drove undeniable business growth.";
        } else {
            refined = currentText;
        }

        field.value = refined;
        field.classList.remove('cora-ai-processing');

        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Copy successfully refined with AI.', 'success');
        }
    }, 450);
}

function coraAiRefineDeliverables() {
    var field = document.getElementById('proj-field-deliverables');
    if (!field) return;

    var lines = field.value.split('\n').map(function(s){ return s.trim(); }).filter(Boolean);
    if (lines.length === 0) {
        lines = [
            "Comprehensive Brand Design System & Token Library",
            "Full-Stack Headless Web App Architecture",
            "Multi-Channel Campaign Production Assets",
            "Automated Inbound Lead Qualification Pipeline"
        ];
    } else {
        lines = lines.map(function(l) {
            return l.replace(/^[-•*]\s*/, '').trim();
        });
    }

    field.value = lines.join('\n');
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Deliverables formatted into clean executive bullet items.', 'success');
    }
}

// =========================================================================
// RICH TEXT FORMATTING ENGINE (Level 01 Atom Action)
// Lightweight, headless formatting for Executive Summary, Challenge, Solution, Deliverables, Testimonial
// =========================================================================

function coraApplyRichFormat(fieldId, format) {
    var textarea = document.getElementById(fieldId);
    if (!textarea) return;

    var start = textarea.selectionStart;
    var end = textarea.selectionEnd;
    var text = textarea.value;
    var selected = text.substring(start, end);
    var replacement = selected;

    if (format === 'bold') {
        replacement = selected ? ('<b>' + selected + '</b>') : '<b>Bold Text</b>';
    } else if (format === 'italic') {
        replacement = selected ? ('<i>' + selected + '</i>') : '<i>Italic Text</i>';
    } else if (format === 'bullet') {
        if (selected) {
            replacement = selected.split('\n').map(function(l) {
                return l.trim().indexOf('•') === 0 ? l : ('• ' + l);
            }).join('\n');
        } else {
            replacement = '• Key Deliverable 1\n• Key Deliverable 2';
        }
    } else if (format === 'numbered') {
        if (selected) {
            replacement = selected.split('\n').map(function(l, i) {
                return (i + 1) + '. ' + l.replace(/^\d+\.\s*/, '');
            }).join('\n');
        } else {
            replacement = '1. Phase One Discovery\n2. Phase Two Production';
        }
    } else if (format === 'quote') {
        replacement = selected ? ('“' + selected + '”') : '“Quote text”';
    } else if (format === 'link') {
        var url = window.prompt('Enter Link URL:', 'https://');
        if (url) {
            replacement = selected ? ('<a href="' + url + '" target="_blank">' + selected + '</a>') : ('<a href="' + url + '" target="_blank">' + url + '</a>');
        }
    } else if (format === 'clear') {
        replacement = selected.replace(/<[^>]*>/g, '').replace(/^[•\d\.\s]+/gm, '');
    }

    textarea.value = text.substring(0, start) + replacement + text.substring(end);
    textarea.focus();
    var newCursor = start + replacement.length;
    textarea.setSelectionRange(newCursor, newCursor);

    // Trigger input event to synchronize previews
    var evt = new Event('input', { bubbles: true });
    textarea.dispatchEvent(evt);
}

// =========================================================================
// STORY TYPE ARCHETYPE ENGINE (Level 02 Molecule)
// =========================================================================

var CORA_STORY_PRESETS = {
    pas: {
        label: 'Problem & Solution (PAS)',
        execPrompt: 'Provide a concise 2-3 sentence overview of the problem, agency intervention, and ultimate transformation...',
        challengePrompt: 'Describe the critical bottlenecks, technical hurdles, or revenue leakage the client was suffering...',
        solutionPrompt: 'Detail the proprietary framework, technology architecture sprint, and deployed solution phases...',
        deliverablesPrompt: 'Comprehensive Brand Design System\nHeadless Next.js Application\nAutomated Lead Pipeline',
        aiExec: 'Commissioned by {client} to engineer an authoritative digital presence for {title}. Deployed our proprietary full-stack methodology to resolve conversion leakage and elevate brand authority across the {industry} sector.',
        aiChallenge: 'Prior to our intervention, {client} operated with fragmented brand collateral and slow delivery cycles. Outdated user experience and disjointed creative assets caused significant customer drop-off during peak commercial cycles.',
        aiSolution: 'We architected an end-to-end design and engineering sprint. By combining bespoke visual art direction with high-performance digital infrastructure, we established a frictionless experience designed for sustainable category leadership.'
    },
    transformation: {
        label: 'Before & After Transformation',
        execPrompt: 'Summarize the baseline state versus the transformed future-state operating velocity...',
        challengePrompt: 'Detail the baseline legacy state: archaic workflows, slow turnaround, and outdated branding...',
        solutionPrompt: 'Explain the complete transformation process, new production pipelines, and modern infrastructure...',
        deliverablesPrompt: 'Complete Visual Brand Identity\nProduction Design System\nRebuilt High-Speed Storefront',
        aiExec: 'Executed a comprehensive before-and-after transformation for {client}, replacing legacy monolithic bottlenecks with high-velocity digital infrastructure.',
        aiChallenge: 'The previous baseline was hampered by fragmented customer touchpoints, 5-second mobile load times, and an inconsistent visual hierarchy that diminished brand equity.',
        aiSolution: 'Overhauled the digital surface from the ground up: sub-400ms load speeds, refined typography, and automated lifecycle replenishment loops.'
    },
    scaling: {
        label: 'Growth & Scaling Sprint',
        execPrompt: 'Highlight the commercial and growth scaling trajectory achieved for {client}...',
        challengePrompt: 'Identify acquisition bottlenecks, rising CAC, low ROAS, and saturation obstacles...',
        solutionPrompt: 'Describe the growth engineering sprint, attribution tracking, and programmatic creative flywheels...',
        deliverablesPrompt: 'Growth Optimization Playbook\nHigh-Converting Funnel Pages\nMulti-Channel Creative Stacks',
        aiExec: 'Spearheaded a rapid growth and performance scaling sprint for {client}, translating commercial media into exponential revenue expansion.',
        aiChallenge: 'Customer acquisition costs had surged by 65% across legacy channels due to creative fatigue and friction in checkout flows.',
        aiSolution: 'Engineered high-converting programmatic funnels, algorithmic audience retargeting, and streamlined conversion pathways.'
    },
    rebrand: {
        label: 'Brand Rebrand & Identity Overhaul',
        execPrompt: 'Summarize the strategic brand repositioning and category elevation for {client}...',
        challengePrompt: 'Describe brand dilution, disjointed typography, and loss of premium market perception...',
        solutionPrompt: 'Outline the new brand strategy, editorial identity tokens, and multi-channel campaign launch...',
        deliverablesPrompt: 'Master Brand Book & Guidelines\nCustom Typography & Color Palette\nFull Packaging & Collateral Suite',
        aiExec: 'Reimagined and repositioned {client} as the premium category benchmark through an authoritative, editorial brand identity overhaul.',
        aiChallenge: 'An outdated visual identity failed to reflect {client}\'s bespoke product quality, creating friction with discerning enterprise clients.',
        aiSolution: 'Crafted an editorial visual design system with bespoke typography, monochromatic restraint, and luxury brand architecture.'
    },
    launch: {
        label: 'Product & Go-To-Market Launch',
        execPrompt: 'Overview of the zero-to-one product launch and global go-to-market rollout...',
        challengePrompt: 'The challenge of launching into a competitive space with zero established organic footprint...',
        solutionPrompt: 'The pre-launch countdown strategy, influencer seeding, and launch sprint execution...',
        deliverablesPrompt: 'Go-To-Market Strategy Deck\nInteractive Launch Microsite\nCommercial Video Campaign Assets',
        aiExec: 'Engineered a viral zero-to-one go-to-market launch for {client}, driving record day-one waitlist conversions and category visibility.',
        aiChallenge: 'Entering an established market required breaking through category noise without burning excessive capital on paid media.',
        aiSolution: 'Orchestrated an exclusive pre-launch viral loop and interactive editorial showcase that ignited organic industry momentum.'
    },
    custom: {
        label: 'Custom Narrative Structure',
        execPrompt: 'Provide an executive summary tailored to your custom case study framework...',
        challengePrompt: 'Detail the custom obstacles, market context, or client objectives...',
        solutionPrompt: 'Explain the tailored methodology and production sprint deployed...',
        deliverablesPrompt: 'Key Deliverables and Outcomes',
        aiExec: 'Commissioned by {client} for specialized production and execution on {title}.',
        aiChallenge: 'Encountered unique domain hurdles requiring custom strategic intervention.',
        aiSolution: 'Engineered and deployed a tailored framework achieving immediate tangible results.'
    }
};

function coraSetStoryType(type) {
    var preset = CORA_STORY_PRESETS[type] || CORA_STORY_PRESETS.pas;
    var input = document.getElementById('proj-field-story-type');
    var labelEl = document.getElementById('cora-selected-story-type-label');

    if (input) input.value = type;
    if (labelEl) labelEl.innerText = preset.label;

    // Update active pill
    document.querySelectorAll('.cora-story-type-pill').forEach(function(btn) {
        btn.classList.remove('active');
    });
    var activeBtn = document.getElementById('story-type-' + type);
    if (activeBtn) activeBtn.classList.add('active');

    // Update placeholder text on textareas if empty
    var f1 = document.getElementById('proj-field-exec-summary');
    var f2 = document.getElementById('proj-field-challenge');
    var f3 = document.getElementById('proj-field-solution');
    var f4 = document.getElementById('proj-field-deliverables');

    if (f1 && !f1.value) f1.placeholder = preset.execPrompt;
    if (f2 && !f2.value) f2.placeholder = preset.challengePrompt;
    if (f3 && !f3.value) f3.placeholder = preset.solutionPrompt;
    if (f4 && !f4.value) f4.placeholder = preset.deliverablesPrompt;
}

// =========================================================================
// STORY ACCORDION CONTROLLER (4 Interactive Acts)
// =========================================================================

function coraToggleStoryAccordion(actNum) {
    var card = document.getElementById('story-accordion-act-' + actNum);
    if (!card) return;
    card.classList.toggle('open');
}

function coraSetAllStoryAccordions(open) {
    for (var i = 1; i <= 4; i++) {
        var card = document.getElementById('story-accordion-act-' + i);
        if (card) {
            if (open) {
                card.classList.add('open');
            } else {
                card.classList.remove('open');
            }
        }
    }
}

function coraUpdateStoryActPreview(actNum, text) {
    var preview = document.getElementById('story-act-preview-' + actNum);
    if (!preview) return;
    var cleaned = (text || '').replace(/<[^>]*>/g, '').trim();
    if (cleaned.length > 50) {
        cleaned = cleaned.substring(0, 50) + '...';
    }
    preview.innerText = cleaned ? ('— ' + cleaned) : '';
}

// =========================================================================
// DYNAMIC KPIS REPEATER ENGINE (Flexible 1 to N Metrics)
// =========================================================================

window.coraCurrentKpis = [
    { value: '+340%', label: 'Organic Revenue Growth', context: 'First 90 Days Post-Launch' },
    { value: '4.8x', label: 'ROAS on Meta & Google', context: 'Scale from $10k to $150k/mo' },
    { value: '14 Days', label: 'Turnaround Velocity', context: 'From Brief to Global Launch' }
];

function coraRenderKpiInputs() {
    var container = document.getElementById('cora-kpi-repeater-container');
    if (!container) return;

    if (!window.coraCurrentKpis || window.coraCurrentKpis.length === 0) {
        window.coraCurrentKpis = [{ value: '', label: '', context: '' }];
    }

    var html = '';
    window.coraCurrentKpis.forEach(function(kpi, idx) {
        var num = idx + 1;
        var tagLabel = idx === 0 ? 'Primary Hero Metric' : (idx === 1 ? 'Secondary Metric' : ('Metric #' + num));
        var canRemove = window.coraCurrentKpis.length > 1;

        html += '<div class="cora-kpi-item-card p-2.5 sm:p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 space-y-2 sm:space-y-3 relative shadow-2xs">';
        html += '<div class="flex items-center justify-between pb-1.5 sm:pb-2 border-b border-zinc-100 dark:border-zinc-800/80">';
        html += '<div class="flex items-center gap-1.5 sm:gap-2">';
        html += '<span class="w-4.5 h-4.5 sm:w-5 sm:h-5 rounded-md bg-zinc-900 text-white dark:bg-white dark:text-zinc-950 font-mono text-[9px] sm:text-[10px] font-bold flex items-center justify-center">' + num + '</span>';
        html += '<span class="text-[11px] sm:text-xs font-bold text-zinc-900 dark:text-zinc-100">' + tagLabel + '</span>';
        html += '<span class="px-1.5 py-0.2 rounded text-[9px] sm:text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-500">Verified Proof</span>';
        html += '</div>';
        if (canRemove) {
            html += '<button type="button" onclick="coraRemoveKpiMetric(' + idx + ')" class="text-[11px] sm:text-xs text-zinc-400 hover:text-red-600 px-1.5 py-0.5 rounded hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer flex items-center gap-1 font-medium" title="Remove Metric"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> <span>Remove</span></button>';
        }
        html += '</div>';

        // Compact Row 1: Value & Metric Label side-by-side with robust flex layout
        html += '<div class="flex items-start gap-2 sm:gap-2.5">';
        html += '<div style="width: 34%; min-width: 82px;" class="shrink-0">';
        html += '<label class="cora-field-label">Value *</label>';
        html += '<input type="text" id="kpi-val-' + num + '" value="' + coraEscapeHtml(kpi.value || '') + '" oninput="coraOnKpiChange(' + idx + ', \'value\', this.value)" placeholder="+340%" class="cora-input-spacious font-mono font-bold text-xs sm:text-sm" />';
        html += '</div>';
        html += '<div class="flex-1 min-w-0">';
        html += '<label class="cora-field-label">Metric Label *</label>';
        html += '<input type="text" id="kpi-lbl-' + num + '" value="' + coraEscapeHtml(kpi.label || '') + '" oninput="coraOnKpiChange(' + idx + ', \'label\', this.value)" placeholder="e.g. Organic Revenue Growth" class="cora-input-spacious text-[11px] sm:text-xs font-semibold" />';
        html += '</div>';
        html += '</div>';

        // Compact Row 2: Context / Timeline details
        html += '<div>';
        html += '<label class="cora-field-label">Verification Context / Timeline Details</label>';
        html += '<input type="text" id="kpi-ctx-' + num + '" value="' + coraEscapeHtml(kpi.context || '') + '" oninput="coraOnKpiChange(' + idx + ', \'context\', this.value)" placeholder="e.g. First 90 Days Post-Launch across North American stores" class="cora-input-spacious text-zinc-600 dark:text-zinc-300 text-[11px] sm:text-xs" />';
        html += '</div>';

        html += '</div>';
    });

    container.innerHTML = html;
}

function coraApplyKpiPreset(val, label, ctx) {
    if (!window.coraCurrentKpis) window.coraCurrentKpis = [];
    var lastIdx = window.coraCurrentKpis.length - 1;
    if (lastIdx >= 0 && !window.coraCurrentKpis[lastIdx].value && !window.coraCurrentKpis[lastIdx].label) {
        window.coraCurrentKpis[lastIdx] = { value: val, label: label, context: ctx };
    } else {
        window.coraCurrentKpis.push({ value: val, label: label, context: ctx });
    }
    coraRenderKpiInputs();
    coraUpdateKpiPreview();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Applied benchmark metric: ' + val + ' ' + label, 'success');
    }
}

function coraAddKpiMetric() {
    window.coraCurrentKpis.push({ value: '', label: '', context: '' });
    coraRenderKpiInputs();
    coraUpdateKpiPreview();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('New metric card added. Enter value and context.', 'info');
    }
}

function coraRemoveKpiMetric(index) {
    if (window.coraCurrentKpis.length > 1 && index >= 0 && index < window.coraCurrentKpis.length) {
        window.coraCurrentKpis.splice(index, 1);
        coraRenderKpiInputs();
        coraUpdateKpiPreview();
    }
}

function coraOnKpiChange(index, key, val) {
    if (window.coraCurrentKpis[index]) {
        window.coraCurrentKpis[index][key] = val;
        coraUpdateKpiPreview();
    }
}

function coraUpdateKpiPreview() {
    var showcase = document.getElementById('cora-kpi-showcase-preview-card');
    if (!showcase) return;

    var validKpis = (window.coraCurrentKpis || []).filter(function(k) {
        return (k.value && k.value.trim()) || (k.label && k.label.trim());
    });

    if (validKpis.length === 0) {
        validKpis = [{ value: '+340%', label: 'Organic Revenue Growth', context: 'First 90 Days Post-Launch' }];
    }

    var html = '';
    // Browser Mockup Top Bar
    html += '<div class="flex items-center justify-between px-3 py-1.5 sm:px-3.5 sm:py-2 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/80 border-b-0 text-[9.5px] sm:text-[10px] font-mono text-zinc-400 select-none">';
    html += '<div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></span><span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></span><span class="w-2 h-2 rounded-full bg-zinc-300 dark:bg-zinc-600"></span><span class="ml-1 text-zinc-500 font-semibold truncate">public/case-study#impact</span></div>';
    html += '<span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified Component</span>';
    html += '</div>';

    // Component Surface
    html += '<div class="p-3 sm:p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-700/80 space-y-2.5 sm:space-y-3">';

    if (validKpis.length === 1) {
        var k = validKpis[0];
        html += '<div class="p-4 sm:p-6 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80 text-center space-y-1.5 sm:space-y-2">';
        html += '<span id="preview-kpi-val-1" class="text-3xl sm:text-5xl font-black font-mono text-zinc-950 dark:text-white block tracking-tight leading-none">' + coraEscapeHtml(k.value || '+340%') + '</span>';
        html += '<span id="preview-kpi-lbl-1" class="text-xs sm:text-base font-bold text-zinc-900 dark:text-zinc-100 block tracking-tight">' + coraEscapeHtml(k.label || 'Hero Outcome') + '</span>';
        if (k.context) {
            html += '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200/60 dark:border-zinc-700 text-[10px] sm:text-xs text-zinc-600 dark:text-zinc-400 mt-1 shadow-3xs">' + coraEscapeHtml(k.context) + '</span>';
        }
        html += '<div class="pt-2 sm:pt-3 mt-2 sm:mt-3 border-t border-zinc-200/60 dark:border-zinc-800 text-[9px] sm:text-[10px] font-mono text-zinc-400 flex items-center justify-center gap-1.5">';
        html += '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-emerald-600"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        html += '<span>Verified Quantitative Metric • Structured ClaimReview Active</span>';
        html += '</div>';
        html += '</div>';
    } else if (validKpis.length === 2) {
        html += '<div class="grid grid-cols-2 gap-2 sm:gap-3">';
        validKpis.forEach(function(k, i) {
            var n = i + 1;
            html += '<div class="p-3 sm:p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80 flex flex-col justify-between space-y-1 sm:space-y-1.5">';
            html += '<span id="preview-kpi-val-' + n + '" class="text-2xl sm:text-3xl font-black font-mono text-zinc-950 dark:text-white block tracking-tight">' + coraEscapeHtml(k.value || '4.8x') + '</span>';
            html += '<span id="preview-kpi-lbl-' + n + '" class="text-[11px] sm:text-xs font-bold text-zinc-900 dark:text-zinc-100 block">' + coraEscapeHtml(k.label || 'Key Metric') + '</span>';
            if (k.context) {
                html += '<span class="text-[9.5px] sm:text-[10.5px] text-zinc-400 block pt-0.5 line-clamp-2">' + coraEscapeHtml(k.context) + '</span>';
            }
            html += '</div>';
        });
        html += '</div>';
    } else {
        var hero = validKpis[0];
        html += '<div class="p-3 sm:p-4 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between gap-3">';
        html += '<div>';
        html += '<span id="preview-kpi-val-1" class="text-2xl sm:text-4xl font-black font-mono text-zinc-950 dark:text-white block tracking-tight">' + coraEscapeHtml(hero.value || '+340%') + '</span>';
        html += '<span id="preview-kpi-lbl-1" class="text-[11px] sm:text-sm font-bold text-zinc-900 dark:text-zinc-100 block mt-0.5">' + coraEscapeHtml(hero.label || 'Primary Outcome') + '</span>';
        if (hero.context) {
            html += '<span class="text-[9.5px] sm:text-[10px] text-zinc-400 block mt-0.5">' + coraEscapeHtml(hero.context) + '</span>';
        }
        html += '</div>';
        html += '<span class="px-2 py-0.5 sm:py-1 rounded-lg text-[9px] sm:text-[10px] font-mono font-bold bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 shrink-0">Primary</span>';
        html += '</div>';

        html += '<div class="grid grid-cols-2 gap-2 sm:gap-2.5">';
        validKpis.slice(1).forEach(function(k, i) {
            var n = i + 2;
            html += '<div class="p-2.5 sm:p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-100 dark:border-zinc-800/80">';
            html += '<span id="preview-kpi-val-' + n + '" class="text-lg sm:text-2xl font-bold font-mono text-zinc-950 dark:text-white block tracking-tight">' + coraEscapeHtml(k.value || '—') + '</span>';
            html += '<span id="preview-kpi-lbl-' + n + '" class="text-[10.5px] sm:text-xs font-bold text-zinc-800 dark:text-zinc-200 block mt-0.5 truncate">' + coraEscapeHtml(k.label || 'Metric') + '</span>';
            if (k.context) {
                html += '<span class="text-[9px] sm:text-[10px] text-zinc-400 block mt-0.5 truncate">' + coraEscapeHtml(k.context) + '</span>';
            }
            html += '</div>';
        });
        html += '</div>';
    }

    html += '</div>'; // close component surface

    showcase.innerHTML = html;
}

function coraAiGenerateCompleteStory() {
    var title = document.getElementById('proj-field-title').value.trim() || 'Strategic Brand Showcase';
    var client = document.getElementById('proj-field-client-name').value.trim() || 'Global Enterprise';
    var industry = document.getElementById('proj-field-client-industry').value.trim() || 'Creative Technology';
    var storyType = (document.getElementById('proj-field-story-type') || {}).value || 'pas';
    var preset = CORA_STORY_PRESETS[storyType] || CORA_STORY_PRESETS.pas;

    var btn = document.getElementById('btn-ai-auto-draft-story');
    if (btn) btn.disabled = true;

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Auto-drafting ' + preset.label + ' story with AI...', 'info');
    }

    setTimeout(function() {
        var execSummary = preset.aiExec
            .replace(/\{client\}/g, client)
            .replace(/\{title\}/g, title)
            .replace(/\{industry\}/g, industry);
        var challenge = preset.aiChallenge
            .replace(/\{client\}/g, client)
            .replace(/\{title\}/g, title)
            .replace(/\{industry\}/g, industry);
        var solution = preset.aiSolution
            .replace(/\{client\}/g, client)
            .replace(/\{title\}/g, title)
            .replace(/\{industry\}/g, industry);
        var deliverables = "Full Brand Identity System & Guidelines\nHigh-Performance Web Showcase\nCommercial Campaign Visual Assets\nAnalytics & Lead Funnel Architecture";

        var f1 = document.getElementById('proj-field-exec-summary');
        var f2 = document.getElementById('proj-field-challenge');
        var f3 = document.getElementById('proj-field-solution');
        var f4 = document.getElementById('proj-field-deliverables');

        if (f1) { f1.value = execSummary; coraUpdateStoryActPreview(1, execSummary); }
        if (f2) { f2.value = challenge; coraUpdateStoryActPreview(2, challenge); }
        if (f3) { f3.value = solution; coraUpdateStoryActPreview(3, solution); }
        if (f4) { f4.value = deliverables; coraUpdateStoryActPreview(4, deliverables); }

        coraSetAllStoryAccordions(true);

        if (btn) btn.disabled = false;
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast(preset.label + ' narrative generated successfully!', 'success');
        }
    }, 550);
}

function coraAiGenerateSeoMetadata() {
    var title = document.getElementById('proj-field-title').value.trim() || 'Master Showcase';
    var client = document.getElementById('proj-field-client-name').value.trim() || 'Enterprise';
    var summary = document.getElementById('proj-field-exec-summary').value.trim();

    var metaTitle = (client + " " + title + " | Case Study & Portfolio").substring(0, 58);
    var metaDesc = summary 
        ? summary.replace(/<[^>]*>/g, '').substring(0, 150) + "..."
        : ("Explore the " + client + " " + title + " case study. Proven business transformation, quantifiable ROI metrics, and executive production deliverables.").substring(0, 150);

    var titleInput = document.getElementById('proj-field-meta-title');
    var descInput = document.getElementById('proj-field-meta-desc');

    if (titleInput) titleInput.value = metaTitle;
    if (descInput) descInput.value = metaDesc;

    coraUpdateSerpPreview();

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('SEO metadata generated and verified within character limits.', 'success');
    }
}

// LIVE PREVIEW UPDATERS & ADVANCED CONTROLS (TESTIMONIAL & SEO/CTA STUDIO)

// STEP 5: TESTIMONIAL PRESETS & CONTROLS
function coraApplyTestimonialPreset(type) {
    var presets = {
        category_leader: {
            quote: "Working with the team transformed our brand velocity. Deliverables were flawless and delivered ahead of our global launch.",
            author: "Kavya Patel",
            role: "VP of Global Brand Marketing",
            company: "Apex Global Brands",
            rating: 5,
            verified: 1
        },
        velocity: {
            quote: "Their execution speed and production rigor are unmatched. What used to take quarters was deployed in weeks with 10x higher quality.",
            author: "Rohan Verma",
            role: "Chief Operating Officer",
            company: "Starlight Digital",
            rating: 5,
            verified: 1
        },
        bespoke_craft: {
            quote: "The level of creative direction and aesthetic precision set a brand-new standard in our industry. Truly enterprise-grade craft.",
            author: "Aarav Mehta",
            role: "Managing Director",
            company: "Vanguard Studios",
            rating: 5,
            verified: 1
        },
        enterprise_scale: {
            quote: "A seamless strategic partnership. They de-risked our multi-market rollout and delivered undeniable ROI across all digital channels.",
            author: "Priya Nair",
            role: "Head of Growth & Digital",
            company: "Zenith Commerce",
            rating: 5,
            verified: 1
        }
    };

    var data = presets[type] || presets.category_leader;
    var qInput = document.getElementById('proj-field-test-quote');
    var aInput = document.getElementById('proj-field-test-author');
    var rInput = document.getElementById('proj-field-test-role');
    var cInput = document.getElementById('proj-field-test-company');

    if (qInput) qInput.value = data.quote;
    if (aInput) aInput.value = data.author;
    if (rInput) rInput.value = data.role;
    if (cInput) cInput.value = data.company;

    coraSetTestimonialRating(data.rating);
    coraUpdateTestimonialPreview();

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Applied executive quote: ' + data.author, 'success');
    }
}

function coraSetTestimonialRating(rating) {
    rating = parseInt(rating, 10) || 5;
    var ratingField = document.getElementById('proj-field-test-rating');
    if (ratingField) ratingField.value = rating;

    var starBtns = document.querySelectorAll('#cora-test-star-group .cora-star-btn');
    starBtns.forEach(function(btn, idx) {
        if (idx < rating) {
            btn.className = 'cora-star-btn text-amber-500';
        } else {
            btn.className = 'cora-star-btn text-zinc-300 dark:text-zinc-700';
        }
    });

    var label = document.getElementById('cora-test-rating-label');
    if (label) label.innerText = rating.toFixed(1);

    var previewStars = document.getElementById('preview-test-stars');
    if (previewStars) {
        var starStr = '';
        for (var i = 0; i < 5; i++) {
            starStr += (i < rating ? '★ ' : '☆ ');
        }
        previewStars.innerText = starStr.trim();
    }

    var richRating = document.getElementById('rich-snippet-rating');
    if (richRating) richRating.innerText = 'Rating: ' + rating.toFixed(1);
}

function coraToggleTestimonialVerified() {
    var field = document.getElementById('proj-field-test-verified');
    var badge = document.getElementById('preview-test-verified-badge');
    var btn = document.getElementById('btn-toggle-verified-client');
    var label = document.getElementById('label-verified-toggle');

    var currentVal = field ? (field.value === '1' ? 1 : 0) : 1;
    var nextVal = currentVal === 1 ? 0 : 1;
    if (field) field.value = nextVal;

    if (nextVal === 1) {
        if (badge) badge.classList.remove('hidden');
        if (btn) btn.className = 'px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/80 flex items-center gap-1.5 cursor-pointer transition-all';
        if (label) label.innerText = 'Verified Client Review';
    } else {
        if (badge) badge.classList.add('hidden');
        if (btn) btn.className = 'px-2.5 py-1 rounded-xl text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200/70 dark:border-zinc-700 flex items-center gap-1.5 cursor-pointer transition-all';
        if (label) label.innerText = 'Standard Review';
    }
}

function coraSelectTestimonialAvatar() {
    var sampleAvatars = [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=160&q=80',
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=160&q=80',
        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=160&q=80'
    ];
    var field = document.getElementById('proj-field-test-avatar');
    if (!field) return;

    var current = field.value.trim();
    var idx = sampleAvatars.indexOf(current);
    var next = (idx + 1) % sampleAvatars.length;
    field.value = sampleAvatars[next];
    coraUpdateTestimonialPreview();

    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Applied verified stakeholder avatar.', 'success');
    }
}

function coraUpdateTestimonialPreview() {
    var quote = (document.getElementById('proj-field-test-quote') || {}).value || '';
    var author = (document.getElementById('proj-field-test-author') || {}).value || '';
    var role = (document.getElementById('proj-field-test-role') || {}).value || '';
    var company = (document.getElementById('proj-field-test-company') || {}).value || '';
    var avatarUrl = (document.getElementById('proj-field-test-avatar') || {}).value || '';

    var pQuote = document.getElementById('preview-test-quote');
    var pAuthor = document.getElementById('preview-test-author');
    var pMeta = document.getElementById('preview-test-meta');
    var pAvatarContainer = document.getElementById('preview-test-avatar-container');
    var pAvatar = document.getElementById('preview-test-avatar');
    var richTitle = document.getElementById('rich-snippet-title');
    var richAuthor = document.getElementById('rich-snippet-author');

    var cleanQuote = quote.replace(/<[^>]*>/g, '').trim();
    if (pQuote) {
        pQuote.innerText = cleanQuote ? ('"' + cleanQuote + '"') : '"Working with the team transformed our brand velocity. Deliverables were flawless and delivered ahead of our global launch."';
    }
    if (pAuthor) pAuthor.innerText = author.trim() || 'Kavya Patel';
    if (pMeta) pMeta.innerText = (role.trim() || 'VP of Global Brand Marketing') + ' • ' + (company.trim() || 'Apex Global Brands');
    if (richAuthor) richAuthor.innerText = author.trim() || 'Kavya Patel';
    if (richTitle) richTitle.innerText = (company.trim() || 'Apex Global Brands') + ' — Client Case Study & Review';

    // Update avatar image or initials
    if (avatarUrl && avatarUrl.trim()) {
        if (pAvatarContainer) {
            pAvatarContainer.innerHTML = '<img src="' + coraEscapeHtml(avatarUrl.trim()) + '" alt="Avatar" class="w-full h-full object-cover rounded-full" onerror="this.parentElement.innerHTML = \'<span id=\\\'preview-test-avatar\\\'>' + coraEscapeHtml((author || 'KP').substring(0, 2).toUpperCase()) + '</span>\';" />';
        }
    } else {
        var initials = (author || 'KP').split(' ').map(function(s){ return s[0]; }).join('').substring(0, 2).toUpperCase();
        if (pAvatarContainer) {
            pAvatarContainer.innerHTML = '<span id="preview-test-avatar">' + (initials || 'KP') + '</span>';
        }
    }
}

// STEP 6: SEO & CONVERSION CTA STUDIO CONTROLS
function coraSetSeoStudioTab(tab) {
    var tabs = ['google', 'social', 'cta'];
    tabs.forEach(function(t) {
        var btn = document.getElementById('seo-tab-btn-' + t);
        var panel = document.getElementById('cora-seo-panel-' + t);
        if (t === tab) {
            if (btn) {
                btn.className = 'w-8 h-8 rounded-lg text-xs font-bold bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer transition-all flex items-center justify-center';
            }
            if (panel) panel.classList.remove('hidden');
        } else {
            if (btn) {
                btn.className = 'w-8 h-8 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-950 dark:hover:text-white bg-transparent border-0 cursor-pointer transition-all flex items-center justify-center';
            }
            if (panel) panel.classList.add('hidden');
        }
    });
}

function coraApplyCtaPreset(type) {
    var titleEl = document.getElementById('proj-field-cta-title');
    var subEl = document.getElementById('proj-field-cta-subtitle');
    var btnEl = document.getElementById('proj-field-cta-button');
    var urlEl = document.getElementById('proj-field-cta-url');

    if (type === 'consultation') {
        if (titleEl) titleEl.value = 'Planning an Editorial Production?';
        if (subEl) subEl.value = 'Review our production calendar and commission custom shoot dates.';
        if (btnEl) btnEl.value = 'Book Creative Consultation';
        if (urlEl) urlEl.value = '/contact';
    } else if (type === 'proposal') {
        if (titleEl) titleEl.value = 'Ready to Scale Your Brand Presence?';
        if (subEl) subEl.value = 'Request a comprehensive RFP proposal tailored to your strategic milestones.';
        if (btnEl) btnEl.value = 'Request Project Proposal';
        if (urlEl) urlEl.value = '/rfp-proposal';
    }
    coraUpdateCtaPreview();
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Applied CTA hook preset: ' + type, 'success');
    }
}

function coraUpdateCtaPreview() {
    var title = (document.getElementById('proj-field-cta-title') || {}).value || 'Planning an Editorial Production?';
    var sub = (document.getElementById('proj-field-cta-subtitle') || {}).value || 'Review our production calendar and commission custom shoot dates.';
    var btn = (document.getElementById('proj-field-cta-button') || {}).value || 'Book Creative Consultation';
    var url = (document.getElementById('proj-field-cta-url') || {}).value || '/contact';

    var pTitle = document.getElementById('preview-cta-title');
    var pSub = document.getElementById('preview-cta-subtitle');
    var pBtn = document.getElementById('preview-cta-btn');
    var pDest = document.getElementById('preview-cta-dest');

    if (pTitle) pTitle.innerText = title.trim();
    if (pSub) pSub.innerText = sub.trim();
    if (pBtn) pBtn.innerText = btn.trim();
    if (pDest) pDest.innerText = '→ ' + url.trim();
}

function coraUpdateSerpPreview() {
    var titleInput = document.getElementById('proj-field-meta-title');
    var descInput = document.getElementById('proj-field-meta-desc');
    var slugInput = document.getElementById('proj-field-slug');
    var heroInput = document.getElementById('proj-field-hero-image');

    var projTitle = (document.getElementById('proj-field-title') || {}).value || 'Master Production Showcase';
    var titleVal = (titleInput && titleInput.value.trim()) || (projTitle.trim() + ' | Cora Studio Agency');
    var rawDesc = (descInput && descInput.value.trim()) || ((document.getElementById('proj-field-exec-summary') || {}).value || '').trim() || 'Explore how our agency engineered a comprehensive digital transformation, driving organic growth and category leadership.';
    var descVal = rawDesc.replace(/<[^>]*>/g, '');
    var slugVal = (slugInput && slugInput.value.trim()) || 'master-production-showcase';

    var serpTitle = document.getElementById('serp-preview-title');
    var serpDesc = document.getElementById('serp-preview-desc');
    var serpSlug = document.getElementById('serp-preview-slug');

    if (serpTitle) serpTitle.innerText = titleVal;
    if (serpDesc) serpDesc.innerText = descVal;
    if (serpSlug) serpSlug.innerText = slugVal;

    // Social card preview
    var socialTitle = document.getElementById('social-preview-title');
    var socialDesc = document.getElementById('social-preview-desc');
    var socialImg = document.getElementById('social-preview-cover-img');
    var socialPlaceholder = document.getElementById('social-preview-cover-placeholder');

    if (socialTitle) socialTitle.innerText = titleVal;
    if (socialDesc) socialDesc.innerText = descVal;

    var heroUrl = heroInput ? heroInput.value.trim() : '';
    if (heroUrl && socialImg) {
        socialImg.src = heroUrl;
        socialImg.classList.remove('hidden');
        if (socialPlaceholder) socialPlaceholder.classList.add('hidden');
    } else if (socialImg) {
        socialImg.classList.add('hidden');
        if (socialPlaceholder) socialPlaceholder.classList.remove('hidden');
    }

    // Character counters & progress bars
    var titleLen = titleInput ? titleInput.value.length : 0;
    var descLen = descInput ? descInput.value.length : 0;

    var titleCount = document.getElementById('meta-title-count');
    var descCount = document.getElementById('meta-desc-count');
    if (titleCount) titleCount.innerText = titleLen;
    if (descCount) descCount.innerText = descLen;

    var titleBar = document.getElementById('meta-title-bar');
    if (titleBar) {
        var tPct = Math.min(100, Math.round((titleLen / 60) * 100));
        titleBar.style.width = tPct + '%';
        titleBar.className = tPct > 100 ? 'h-full bg-red-500 transition-all duration-200' : 'h-full bg-emerald-500 transition-all duration-200';
    }

    var descBar = document.getElementById('meta-desc-bar');
    if (descBar) {
        var dPct = Math.min(100, Math.round((descLen / 155) * 100));
        descBar.style.width = dPct + '%';
        descBar.className = dPct > 100 ? 'h-full bg-red-500 transition-all duration-200' : 'h-full bg-emerald-500 transition-all duration-200';
    }

    coraUpdateCtaPreview();
}

function coraEscapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function coraUpdateStep1LiveCardPreview() {
    var title = (document.getElementById('proj-field-title') || {}).value || '';
    var client = (document.getElementById('proj-field-client-name') || {}).value || '';
    var subtitle = (document.getElementById('proj-field-subtitle') || {}).value || '';
    var duration = (document.getElementById('proj-field-duration') || {}).value || '';
    var heroUrl = (document.getElementById('proj-field-hero-image') || {}).value || '';
    var servicesStr = (document.getElementById('proj-field-services') || {}).value || '';
    var status = (document.getElementById('proj-field-status') || {}).value || 'published';

    var tEl = document.getElementById('step1-preview-title'); 
    if (tEl) tEl.innerText = title.trim() || 'Master Production Showcase';
    
    var cEl = document.getElementById('step1-preview-client'); 
    if (cEl) cEl.innerText = client.trim() || 'Apex Global Brands';
    
    var sEl = document.getElementById('step1-preview-subtitle'); 
    if (sEl) sEl.innerText = subtitle.trim() || 'Complete commercial rebranding and multi-channel campaign rollout.';
    
    var dEl = document.getElementById('step1-preview-duration'); 
    if (dEl) dEl.innerText = duration.trim() || 'Q3 2026';

    var pillEl = document.getElementById('step1-preview-status-pill');
    if (pillEl) {
        pillEl.innerText = status === 'draft' ? 'Draft' : 'Published';
    }

    // Update tags
    var tagsContainer = document.getElementById('step1-preview-tags');
    if (tagsContainer) {
        var tags = servicesStr.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
        if (tags.length === 0) tags = ['Creative Direction', 'Brand Strategy'];
        tagsContainer.innerHTML = tags.slice(0, 3).map(function(tag) {
            return '<span class="px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">' + coraEscapeHtml(tag) + '</span>';
        }).join('');
    }

    // Update cover image
    var imgEl = document.getElementById('step1-preview-cover-img');
    var emptyEl = document.getElementById('step1-preview-cover-empty');
    var initEl = document.getElementById('step1-preview-initials');
    var initials = (title.trim() || 'Project').split(' ').map(function(s){ return s[0]; }).join('').substring(0, 2).toUpperCase();
    if (initEl) initEl.innerText = initials || 'P';

    if (heroUrl && imgEl) {
        imgEl.src = heroUrl.trim();
        imgEl.classList.remove('hidden');
        if (emptyEl) emptyEl.classList.add('hidden');
    } else if (imgEl) {
        imgEl.classList.add('hidden');
        if (emptyEl) emptyEl.classList.remove('hidden');
    }

    // Dynamic Live Personalised Portal URL
    var portalInput = document.getElementById('proj-field-client-portal-url');
    if (portalInput) {
        var cSlug = client.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        var wsSlug = window.coraActiveWorkspaceSlug || 'studio';
        portalInput.value = window.location.origin + '/' + wsSlug + '/client-portal' + (cSlug ? '?token=' + encodeURIComponent(cSlug) : '');
    }
}

// OPEN DRAWER FOR CREATE
function coraOpenProjectDrawerForCreate() {
    document.getElementById('cora-project-form').reset();
    document.getElementById('proj-field-id').value = '0';
    document.getElementById('proj-field-agency-id').value = window.coraActiveAgencyId || '1';
    if (document.getElementById('proj-field-client-id')) {
        document.getElementById('proj-field-client-id').value = '0';
    }
    var titleEl = document.getElementById('cora-proj-drawer-title');
    if (titleEl) titleEl.innerText = 'New Portfolio Project';
    var subtitleEl = document.getElementById('cora-proj-drawer-subtitle');
    if (subtitleEl) subtitleEl.innerText = 'Follow the 6-step guided framework to publish a high-impact case study.';
    coraSetDrawerStatus('published');

    // Handle URL parameters if opened from Clients view
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('client_name') && document.getElementById('proj-field-client-name')) {
        document.getElementById('proj-field-client-name').value = urlParams.get('client_name');
    }
    if (urlParams.get('client_id') && document.getElementById('proj-field-client-id')) {
        document.getElementById('proj-field-client-id').value = urlParams.get('client_id');
    }

    // Reset Smart Links
    window.coraCurrentSmartLinks = [];
    coraRenderSmartLinksList();
    coraOnSmartLinkInput('');

    // Reset Hero Thumbnail & Gallery
    coraRemoveHeroAsset();
    coraSetThumbnailAspectRatio('16:9');
    coraClearAllGalleryAssets();
    coraClearVideoUrl();
    coraSetVideoAspectRatio('16:9');
    coraSetMediaPreviewTab('gallery');

    // Reset Story Type to PAS & Accordions
    coraSetStoryType('pas');
    coraSetAllStoryAccordions(false);
    for (var a = 1; a <= 4; a++) { coraUpdateStoryActPreview(a, ''); }

    // Reset KPIs to default 3 metrics
    window.coraCurrentKpis = [
        { value: '+340%', label: 'Organic Revenue Growth', context: 'First 90 Days Post-Launch' },
        { value: '4.8x', label: 'ROAS on Meta & Google', context: 'Scale from $10k to $150k/mo' },
        { value: '14 Days', label: 'Turnaround Velocity', context: 'From Brief to Global Launch' }
    ];
    coraRenderKpiInputs();
    coraSetTestimonialRating(5);
    coraSetSeoStudioTab('google');
    coraUpdateTestimonialPreview();
    coraUpdateSerpPreview();
    coraUpdateStep1LiveCardPreview();

    // Reset to Step 1
    coraGoToProjectStep(1);
    coraShowProjectDrawer();
}

// EDIT PROJECT
function coraEditProject(id, targetStep) {
    var project = (window.coraProjectsData || []).find(function(p) { return String(p.id) === String(id); });
    if (!project) return;

    document.getElementById('proj-field-id').value = project.id;
    document.getElementById('proj-field-agency-id').value = project.agencyId || window.coraActiveAgencyId || '1';
    if (document.getElementById('proj-field-client-id')) {
        document.getElementById('proj-field-client-id').value = project.clientId || '0';
    }
    var editTitleEl = document.getElementById('cora-proj-drawer-title');
    if (editTitleEl) editTitleEl.innerText = 'Edit Case Study';
    var editSubtitleEl = document.getElementById('cora-proj-drawer-subtitle');
    if (editSubtitleEl) editSubtitleEl.innerText = 'Update scope, 4-act narrative, KPIs, and deliverables.';
    document.getElementById('proj-field-title').value = project.title || '';
    document.getElementById('proj-field-client-name').value = project.clientName || '';
    document.getElementById('proj-field-client-industry').value = project.clientIndustry || '';
    document.getElementById('proj-field-duration').value = project.projectDuration || '';
    document.getElementById('proj-field-slug').value = project.slug || '';
    document.getElementById('proj-field-services').value = (project.services || []).join(', ');
    document.getElementById('proj-field-subtitle').value = project.subtitle || '';
    document.getElementById('proj-field-confidential').checked = !!project.isConfidential;
    document.getElementById('proj-field-featured').checked = !!project.isFeatured;
    coraSetDrawerStatus(project.status || 'published');

    // Personalised Client Portal URL hydration
    var cName = project.clientName || '';
    var cSlug = cName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    var wsSlug = window.coraActiveWorkspaceSlug || 'studio';
    var portalInput = document.getElementById('proj-field-client-portal-url');
    if (portalInput) {
        portalInput.value = window.location.origin + '/' + wsSlug + '/client-portal' + (cSlug ? '?token=' + encodeURIComponent(cSlug) : '');
    }

    // Smart Links Hydration
    window.coraCurrentSmartLinks = [];
    if (project.projectLinks && Array.isArray(project.projectLinks) && project.projectLinks.length > 0) {
        window.coraCurrentSmartLinks = project.projectLinks;
    } else if (project.liveUrl) {
        var detected = coraDetectPlatformFromUrl(project.liveUrl);
        window.coraCurrentSmartLinks.push({
            platform: detected.platform,
            label: detected.label,
            url: project.liveUrl
        });
    }
    coraRenderSmartLinksList();
    coraOnSmartLinkInput('');

    // Story Type
    coraSetStoryType(project.storyType || 'pas');

    // Narrative & Story Accordions
    var execVal = project.executiveSummary || '';
    var chalVal = project.challengeContent || '';
    var solVal = project.solutionContent || '';
    var delVal = (project.deliverables || []).join('\n');
    document.getElementById('proj-field-exec-summary').value = execVal;
    document.getElementById('proj-field-challenge').value = chalVal;
    document.getElementById('proj-field-solution').value = solVal;
    document.getElementById('proj-field-deliverables').value = delVal;
    coraUpdateStoryActPreview(1, execVal);
    coraUpdateStoryActPreview(2, chalVal);
    coraUpdateStoryActPreview(3, solVal);
    coraUpdateStoryActPreview(4, delVal);
    coraSetAllStoryAccordions(false);

    // KPIs Hydration (Dynamic 1..N Metrics)
    if (project.kpis && Array.isArray(project.kpis) && project.kpis.length > 0) {
        window.coraCurrentKpis = JSON.parse(JSON.stringify(project.kpis));
    } else {
        window.coraCurrentKpis = [
            { value: '+340%', label: 'Organic Revenue Growth', context: 'First 90 Days Post-Launch' }
        ];
    }
    coraRenderKpiInputs();

    // Media
    document.getElementById('proj-field-hero-image').value = project.heroImageUrl || '';
    coraOnHeroUrlInput(project.heroImageUrl || '');
    coraSetThumbnailAspectRatio('16:9');

    // Gallery Hydration
    var rawGallery = project.galleryImages || [];
    window.coraCurrentGalleryImages = rawGallery.map(function(item) {
        if (typeof item === 'string') return { url: item, caption: '' };
        return { url: item.url || '', caption: item.caption || item.title || '' };
    }).filter(function(i) { return !!i.url; });
    coraSyncGalleryState();

    // Video Hydration
    var videoUrl = project.videoEmbedUrl || project.videoUrl || '';
    document.getElementById('proj-field-video-url').value = videoUrl;
    coraSetVideoAspectRatio('16:9');
    coraOnVideoUrlInput(videoUrl);
    coraSetMediaPreviewTab(videoUrl ? 'video' : 'gallery');

    // Testimonial
    if (project.testimonial) {
        document.getElementById('proj-field-test-quote').value = project.testimonial.quote || '';
        document.getElementById('proj-field-test-author').value = project.testimonial.author || '';
        document.getElementById('proj-field-test-role').value = project.testimonial.role || '';
        document.getElementById('proj-field-test-company').value = project.testimonial.company || '';
        document.getElementById('proj-field-test-avatar').value = project.testimonial.avatar || '';
        coraSetTestimonialRating(project.testimonial.rating || 5);
    } else {
        document.getElementById('proj-field-test-quote').value = '';
        document.getElementById('proj-field-test-author').value = '';
        document.getElementById('proj-field-test-role').value = '';
        document.getElementById('proj-field-test-company').value = '';
        document.getElementById('proj-field-test-avatar').value = '';
        coraSetTestimonialRating(5);
    }
    coraSetSeoStudioTab('google');

    // CTA & SEO
    document.getElementById('proj-field-cta-title').value = project.ctaTitle || 'Planning an Editorial Production?';
    document.getElementById('proj-field-cta-subtitle').value = project.ctaSubtitle || '';
    document.getElementById('proj-field-cta-button').value = project.ctaButtonText || 'Book Creative Consultation';
    document.getElementById('proj-field-cta-url').value = project.ctaButtonUrl || '/contact';
    document.getElementById('proj-field-meta-title').value = project.metaTitle || '';
    document.getElementById('proj-field-meta-desc').value = project.metaDescription || '';

    // Live Previews update
    coraUpdateKpiPreview();
    coraUpdateTestimonialPreview();
    coraUpdateSerpPreview();
    coraUpdateStep1LiveCardPreview();

    // Reset to Step 1 or targeted step
    coraGoToProjectStep(targetStep ? parseInt(targetStep, 10) : 1);
    coraShowProjectDrawer();
}

// DYNAMIC BOUNDS SYNCHRONIZER (BOUND TO AVAILABLE MAIN WORKSPACE AREA)
function coraSyncMainAreaBounds() {
    var drawer = document.getElementById('cora-project-drawer');
    var sidebarEl = document.querySelector('.cora-sidebar');
    var isMobile = window.innerWidth < 1024;
    var isSidebarCollapsed = sidebarEl && sidebarEl.classList.contains('collapsed-sidebar');

    if (drawer) {
        // Reflect collapsed state class on drawer element directly
        if (isSidebarCollapsed) {
            drawer.classList.add('sidebar-collapsed');
        } else {
            drawer.classList.remove('sidebar-collapsed');
        }

        // If drawer is currently maximized (fullscreen ⤢), keep full viewport bounds
        if (drawer.classList.contains('maximized')) {
            drawer.style.left = '0px';
            drawer.style.top = '52px';
            drawer.style.width = '100vw';
            drawer.style.maxWidth = '100vw';
            drawer.style.height = (window.innerHeight - 52) + 'px';
            drawer.style.maxHeight = (window.innerHeight - 52) + 'px';
            return;
        }

        // Calculate available main canvas bounds directly against sidebar boundary
        var topbarEl = document.getElementById('cora-global-topbar');
        var actualTop = topbarEl ? Math.round(topbarEl.getBoundingClientRect().bottom) : 52;
        if (actualTop < 0 || isNaN(actualTop)) actualTop = 52;

        var targetLeft = 0;
        if (!isMobile && sidebarEl) {
            var sRect = sidebarEl.getBoundingClientRect();
            // Use precise right boundary of the sidebar so drawer seamlessly docks with 0 gap
            targetLeft = Math.round(sRect.right);
            if (targetLeft <= 0 || isNaN(targetLeft)) {
                targetLeft = isSidebarCollapsed ? 64 : 256;
            }
        }
        var targetWidth = window.innerWidth - targetLeft;
        var targetHeight = window.innerHeight - actualTop;

        document.documentElement.style.setProperty('--cora-main-left', targetLeft + 'px');
        document.documentElement.style.setProperty('--cora-main-top', actualTop + 'px');
        document.documentElement.style.setProperty('--cora-main-width', targetWidth + 'px');
        document.documentElement.style.setProperty('--cora-main-height', targetHeight + 'px');

        // Set responsive inline bounds dynamically
        drawer.style.left = targetLeft + 'px';
        drawer.style.top = actualTop + 'px';
        drawer.style.width = targetWidth + 'px';
        drawer.style.maxWidth = targetWidth + 'px';
        drawer.style.height = targetHeight + 'px';
        drawer.style.maxHeight = targetHeight + 'px';
    }
}

// Continuously sync bounds over a duration (e.g. during a 250ms CSS animation)
function coraAnimateSyncBounds(durationMs) {
    var start = Date.now();
    var duration = durationMs || 280;
    function frame() {
        coraSyncMainAreaBounds();
        if (Date.now() - start < duration) {
            requestAnimationFrame(frame);
        } else {
            coraSyncMainAreaBounds();
        }
    }
    requestAnimationFrame(frame);
}

window.addEventListener('resize', coraSyncMainAreaBounds);
window.addEventListener('cora:sidebar-toggle', function() {
    coraAnimateSyncBounds(300);
});

// Observe sidebar changes (expand/collapse) to sync available space in real time
if (window.MutationObserver) {
    var coraSidebarObserver = new MutationObserver(function() {
        coraAnimateSyncBounds(250);
    });
    var sidebarEl = document.querySelector('.cora-sidebar');
    if (sidebarEl) {
        coraSidebarObserver.observe(sidebarEl, { attributes: true, attributeFilter: ['class', 'style'] });
        sidebarEl.addEventListener('transitionend', function(e) {
            coraSyncMainAreaBounds();
        });
    }
    coraSidebarObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
}

// SHOW MAIN-AREA DOCKED DRAWER PANEL
function coraShowProjectDrawer() {
    var drawer = document.getElementById('cora-project-drawer');
    var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    // Body Portaling: Guarantee drawer sits at document.body level with no clipping or layout offset
    if (drawer && drawer.parentNode !== document.body) {
        document.body.appendChild(drawer);
    }

    if (drawer) {
        drawer.classList.remove('collapsed');
        drawer.classList.add('open');

        // Hide mobile floating bottom bar so it never overlaps or intercepts drawer touches
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.add('cora-island-hidden');
        }

        // Immediately sync bounds to sidebar edge
        coraSyncMainAreaBounds();

        // Lock ONLY main content area scroll; SIDEBAR REMAINS UNLOCKED & FULLY ACCESSIBLE
        if (mainEl) {
            mainEl.classList.add('overflow-hidden');
        }
    }
}

// TOGGLE EXPAND / MAXIMIZE CANVAS (SHOPIFY EXPAND ⤢ MODE)
function coraToggleProjectDrawerMaximize() {
    var drawer = document.getElementById('cora-project-drawer');
    var maxBtn = document.getElementById('cora-proj-maximize-btn');
    if (!drawer) return;

    var isMax = drawer.classList.toggle('maximized');
    if (isMax) {
        drawer.style.left = '0px';
        drawer.style.width = '100vw';
        drawer.style.maxWidth = '100vw';
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14h6v6"></path><path d="M20 10h-6V4"></path><path d="M14 10l7-7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Restore Docked Canvas";
        }
    } else {
        if (maxBtn) {
            maxBtn.innerHTML = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>';
            maxBtn.title = "Expand / Fullscreen Canvas";
        }
        coraSyncMainAreaBounds();
    }
}

// HIDE MAIN-AREA DOCKED DRAWER PANEL & RESTORE MAIN SCROLL
function coraCloseProjectDrawer() {
    var drawer = document.getElementById('cora-project-drawer');
    var mainEl = document.querySelector('main.cora-main') || document.querySelector('.cora-main');

    if (drawer) {
        drawer.classList.remove('open');
        drawer.classList.add('collapsed');
        drawer.classList.remove('maximized');

        // Restore mobile floating bottom bar
        var island = document.getElementById('cora-mobile-floating-island');
        if (island) {
            island.classList.remove('cora-island-hidden');
        }

        // Restore main content area scroll
        if (mainEl) {
            mainEl.classList.remove('overflow-hidden');
        }
    }
}

// ESC KEY LISTENER TO CLOSE DRAWER
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        coraCloseProjectDrawer();
    }
});

// SUBMIT PROJECT FORM
function coraSubmitProjectForm(e) {
    e.preventDefault();
    var saveBtn = document.getElementById('cora-proj-save-btn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';
    }

    var formData = new FormData(document.getElementById('cora-project-form'));
    formData.append('action', 'cora_ajax_save_project');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    // Ensure Smart Links array is passed cleanly
    formData.set('project_links', JSON.stringify(window.coraCurrentSmartLinks || []));
    if (window.coraCurrentSmartLinks && window.coraCurrentSmartLinks.length > 0) {
        formData.set('live_url', window.coraCurrentSmartLinks[0].url);
    }

    // Build KPIs array from dynamic window.coraCurrentKpis
    var validKpis = (window.coraCurrentKpis || []).filter(function(k) {
        return (k.value && k.value.trim()) || (k.label && k.label.trim());
    });
    formData.append('kpis', JSON.stringify(validKpis));

    // Build Deliverables array
    var delivText = document.getElementById('proj-field-deliverables').value;
    var deliverables = delivText.split('\n').map(function(s) { return s.trim(); }).filter(Boolean);
    formData.append('deliverables', JSON.stringify(deliverables));

    // Build Gallery Images array
    var galText = document.getElementById('proj-field-gallery').value;
    var gallery = galText.split('\n').map(function(url) {
        url = url.trim();
        return url ? { url: url } : null;
    }).filter(Boolean);
    formData.append('gallery_images', JSON.stringify(gallery));

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Project';
        }

        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message || 'Project saved successfully.', 'success');
            }
            coraCloseProjectDrawer();
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast((data.data && data.data.message) ? data.data.message : 'Error saving project', 'error');
            }
        }
    })
    .catch(function(err) {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Project';
        }
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Network error while saving.', 'error');
        }
    });
}

// TOGGLE PUBLISH STATUS
function coraToggleProjectPublish(id) {
    var formData = new FormData();
    formData.append('action', 'cora_ajax_toggle_project_publish');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            var textElem = document.getElementById('status-pill-text-' + id);
            if (textElem) {
                textElem.innerText = data.data.new_status === 'published' ? 'Published' : 'Draft';
            }
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        } else {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message || 'Failed to toggle status.', 'error');
            }
        }
    })
    .catch(function() {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Network error toggling status.', 'error');
        }
    });
}

// DUPLICATE PROJECT
function coraDuplicateProject(id) {
    var formData = new FormData();
    formData.append('action', 'cora_ajax_duplicate_project');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        }
    });
}

// DELETE PROJECT
function coraDeleteProject(id) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Deleting project...', 'info');
    }

    var formData = new FormData();
    formData.append('action', 'cora_ajax_delete_project');
    formData.append('id', id);
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast(data.data.message, 'success');
            }
            var rows = document.querySelectorAll('.cora-project-row[data-id="' + id + '"]');
            rows.forEach(function(r) { r.remove(); });
            setTimeout(function() { window.location.reload(); }, 600);
        }
    });
}

// LOAD STARTER PROJECTS (On-Demand Seeding)
function coraLoadStarterProjects() {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast('Loading workspace starter projects...', 'info');
    }

    var formData = new FormData();
    formData.append('action', 'cora_ajax_seed_starter_projects');
    formData.append('agency_id', window.coraActiveAgencyId || '1');
    formData.append('security', '<?php echo esc_js( wp_create_nonce( "cora_ajax_nonce" ) ); ?>');

    fetch(ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Starter projects loaded successfully.', 'success');
            }
            setTimeout(function() { window.location.reload(); }, 500);
        }
    });
}

// COPY PUBLIC URL
function coraCopyProjectUrl(slug) {
    var url = '<?php echo esc_js( $frontend_base ); ?>/portfolio/' + slug;
    navigator.clipboard.writeText(url).then(function() {
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Public portfolio link copied to clipboard.', 'success');
        }
    });
}

// BIDIRECTIONAL CLIENT PORTAL INTEGRATION HELPERS
window.coraActiveWorkspaceSlug = "<?php echo esc_js( $ws_slug ); ?>";

window.coraOpenDrawerClientPortal = function() {
    var portalInput = document.getElementById('proj-field-client-portal-url');
    var url = portalInput ? portalInput.value : '';
    if (url) {
        window.open(url, '_blank');
    }
};

window.coraCopyDrawerClientPortalUrl = function() {
    var portalInput = document.getElementById('proj-field-client-portal-url');
    var url = portalInput ? portalInput.value : '';
    if (url && navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Personalised Client Portal link copied.', 'success');
            }
        });
    }
};

window.coraOnClientSelect = function(val) {
    if (!val) return;
    var datalist = document.getElementById('cora-client-names-datalist');
    if (!datalist) return;
    var opt = datalist.querySelector('option[value="' + val + '"]');
    if (opt) {
        var clientId = opt.getAttribute('data-id') || '0';
        var idField = document.getElementById('proj-field-client-id');
        if (idField) idField.value = clientId;
    }
    coraUpdateStep1LiveCardPreview();
};

// AUTO-NAVIGATE IF URL INCLUDES NEW PROJECT OR EDIT PARAMS (FROM CRM CLIENTS VIEW)
(function() {
    var params = new URLSearchParams(window.location.search);
    if (params.get('new') === '1') {
        setTimeout(function() {
            coraOpenProjectDrawerForCreate();
        }, 150);
    } else if (params.get('edit')) {
        setTimeout(function() {
            coraEditProject(params.get('edit'), 1);
        }, 150);
    }
})();
</script>
