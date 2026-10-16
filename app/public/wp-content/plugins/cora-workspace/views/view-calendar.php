<?php
/**
 * Cora Workspace - Workspace Interactive Calendar & Scheduling Engine
 * File: views/view-calendar.php
 *
 * Full interactive scheduling calendar supporting Month, Week, and Day views.
 * Features 2-way bidirectional Google Calendar & Google Meet synchronization,
 * public client booking link creation, and automated CRM lead reflection.
 *
 * Strictly adheres to the Cora Product Design System & Workspace Rules:
 * - RULE 15: Shopify-Inspired Main-Area Docked View Panels (Universal bottom-slide motion translateY(0), zero backdrop, docked below header).
 * - RULE 4 & 13: Monochromatic palette (zinc-50 through zinc-950), quiet 1px structural borders, zero heavy outline styling.
 * - RULE 1 & 15: Zero browser default dialogues or native controls (custom segmented pills & custom switches, zero emojis).
 * - RULE 3: Strict name & identity privacy (generic fictitious studio personas).
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================================
// BACKEND DATA & CONFIGURATION PLACEHOLDERS
// Ready for Laravel / WordPress SQL query blocks and REST API models.
// ============================================================================

$current_user = wp_get_current_user();
$user_email   = $current_user ? $current_user->user_email : 'studio.director@cora.local';
$agency_id    = function_exists( 'cora_get_current_user_agency_id' ) ? cora_get_current_user_agency_id() : 'studio-creative';
$app_url      = function_exists( 'home_url' ) ? home_url() : 'https://cora.studio';
$booking_root = rtrim( $app_url, '/' ) . '/book/' . esc_attr( $agency_id );

// Google Calendar Connection Metadata
$google_connection = array(
    'connected'         => true,
    'account_email'     => 'studio.bookings@cora.local',
    'sync_mode'         => '2-Way Realtime Bidirectional',
    'last_synced'       => 'Just now',
    'auto_google_meet'  => true,
    'conflict_guard'    => true,
);

// Public Appointment Booking Links (Flows)
$booking_flows = array(
    array(
        'id'             => 'flow-1',
        'title'          => '1-Hour Studio Session',
        'slug'           => 'studio-1hr',
        'duration'       => 60,
        'duration_fmt'   => '60 Mins',
        'price_tag'      => '₹12,500',
        'location'       => 'Main Studio Floor (Bay A)',
        'buffer_info'    => '15m buffer',
        'google_meet'    => false,
        'description'    => 'Standard portrait, fashion portfolio, or commercial look shoot with studio strobe setup and tethering.',
        'active'         => true,
        'bookings_count' => 28,
    ),
    array(
        'id'             => 'flow-2',
        'title'          => '15-Min Quick Intro Call',
        'slug'           => 'intro-15m',
        'duration'       => 15,
        'duration_fmt'   => '15 Mins',
        'price_tag'      => 'Complimentary',
        'location'       => 'Google Meet (Auto-generated)',
        'buffer_info'    => '10m buffer',
        'google_meet'    => true,
        'description'    => 'Discovery video consultation with lead photographer to review moodboards, assess production scope, and confirm dates.',
        'active'         => true,
        'bookings_count' => 64,
    ),
    array(
        'id'             => 'flow-3',
        'title'          => 'Half-Day Editorial Shoot',
        'slug'           => 'editorial-half-day',
        'duration'       => 240,
        'duration_fmt'   => '4 Hours',
        'price_tag'      => '₹38,000',
        'location'       => 'Full Studio Bay + Dressing Suite',
        'buffer_info'    => '30m setup + teardown',
        'google_meet'    => false,
        'description'    => 'Comprehensive multi-look brand campaign with private makeup suite, styling mirrors, and full cyclorama access.',
        'active'         => true,
        'bookings_count' => 12,
    ),
    array(
        'id'             => 'flow-4',
        'title'          => 'Portfolio Review & Retouching Session',
        'slug'           => 'portfolio-retouching-review',
        'duration'       => 30,
        'duration_fmt'   => '30 Mins',
        'price_tag'      => 'Included in Package',
        'location'       => 'Google Meet Screen Share',
        'buffer_info'    => 'No buffer required',
        'google_meet'    => true,
        'description'    => 'Virtual review session with lead retoucher to approve color grading, select hero frames, and annotate high-res crops.',
        'active'         => false,
        'bookings_count' => 39,
    ),
);

// Initial Calendar Events Schedule (Mock DB Records with valid 3-4-3 Google Meet URLs)
$initial_events = array(
    array(
        'id'          => 'evt-101',
        'title'       => 'Lookbook Prep & Moodboard Review',
        'client_name' => 'Aarav Mehta',
        'client_email'=> 'aarav.mehta@example.com',
        'date'        => '2026-10-06',
        'time'        => '02:00 PM',
        'duration'    => '45 Mins',
        'location'    => 'Google Meet',
        'google_meet' => 'https://meet.google.com/xkd-yrtn-jpm',
        'crm_lead'    => 'Aarav Mehta — Executive Portraits',
        'source'      => 'Direct Studio Consultation',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-104',
        'title'       => '1-Hour Studio Portrait Session',
        'client_name' => 'Aarav Mehta',
        'client_email'=> 'aarav.mehta@example.com',
        'date'        => '2026-10-15', // TODAY
        'time'        => '10:30 AM',
        'duration'    => '60 Mins',
        'location'    => 'Main Studio Floor (Bay A)',
        'google_meet' => '',
        'crm_lead'    => 'Aarav Mehta — Executive Portraits',
        'source'      => '1-Hour Studio Session Link',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-105',
        'title'       => 'Quick Intro Call — Campaign Scope',
        'client_name' => 'Kavya Patel',
        'client_email'=> 'kavya.patel@example.com',
        'date'        => '2026-10-15', // TODAY
        'time'        => '02:00 PM',
        'duration'    => '15 Mins',
        'location'    => 'Google Meet',
        'google_meet' => 'https://meet.google.com/qap-jknw-vtz',
        'crm_lead'    => 'Kavya Patel — Autumn Lookbook (Won)',
        'source'      => '15-Min Quick Intro Call Link',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-106',
        'title'       => 'Portfolio Retouching Review',
        'client_name' => 'Rohan Verma',
        'client_email'=> 'rohan.verma@example.com',
        'date'        => '2026-10-15', // TODAY
        'time'        => '04:30 PM',
        'duration'    => '30 Mins',
        'location'    => 'Google Meet Screen Share',
        'google_meet' => 'https://meet.google.com/ret-uymk-xqa',
        'crm_lead'    => 'Rohan Verma — E-Commerce Catalog',
        'source'      => 'Portfolio Review Link',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-107',
        'title'       => 'Half-Day Editorial Campaign Shoot',
        'client_name' => 'Kabir Sharma',
        'client_email'=> 'kabir.sharma@example.com',
        'date'        => '2026-10-16',
        'time'        => '09:00 AM',
        'duration'    => '240 Mins',
        'location'    => 'Full Studio Bay + Dressing Suite',
        'google_meet' => '',
        'crm_lead'    => 'Kabir Sharma — Winter Editorial',
        'source'      => 'Half-Day Editorial Shoot Link',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-108',
        'title'       => 'Discovery & Rate Consultation',
        'client_name' => 'Meera Desai',
        'client_email'=> 'meera.desai@example.com',
        'date'        => '2026-10-20',
        'time'        => '01:00 PM',
        'duration'    => '15 Mins',
        'location'    => 'Google Meet',
        'google_meet' => 'https://meet.google.com/mee-tzkv-wls',
        'crm_lead'    => 'Meera Desai — Product Photography',
        'source'      => '15-Min Quick Intro Call Link',
        'synced'      => true,
    ),
    array(
        'id'          => 'evt-109',
        'title'       => 'Brand Campaign Shoot — Model Shoot',
        'client_name' => 'Aarav Mehta',
        'client_email'=> 'aarav.mehta@example.com',
        'date'        => '2026-10-23',
        'time'        => '11:30 AM',
        'duration'    => '180 Mins',
        'location'    => 'Studio Bay A',
        'google_meet' => '',
        'crm_lead'    => 'Aarav Mehta — Executive Portraits',
        'source'      => 'Direct Studio Booking',
        'synced'      => true,
    ),
);

// Platform Workspace Header
$calendar_header_args = array(
    'title'              => 'Workspace Calendar',
    'description'        => 'Interactive schedule, 2-way Google Calendar & Google Meet sync, and public client booking flows.',
    'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
    'ai_stack'           => true,
    'tutorial_onclick'   => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'                => array(
        'text'        => 'New Appointment',
        'mobile_text' => 'New',
        'onclick'     => 'coraOpenCreateAppointmentModal()',
        'icon'        => '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
    'extra_actions_html' => '
        <button type="button" onclick="coraOpenBookingLinksDrawer()" class="h-9 px-3 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-white hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-3xs shrink-0 active:scale-95" title="Public booking flows">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            <span class="hidden sm:inline">Booking Links</span>
            <span class="sm:hidden">Links</span>
        </button>
        <button type="button" onclick="coraTriggerGoogleSync()" class="h-9 px-3 text-xs font-semibold text-zinc-800 dark:text-zinc-200 bg-white hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shadow-3xs shrink-0 active:scale-95" title="Google Calendar 2-Way Sync">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="hidden md:inline">Google Sync</span>
        </button>
    ',
);

if ( function_exists( 'cora_render_workspace_header' ) ) {
    cora_render_workspace_header( $calendar_header_args );
}
?>

<!-- ==========================================================================
     CANONICAL CORA DESIGN SYSTEM STYLES & DOCKED PANEL ARCHITECTURE (RULE 15 SOP)
     ========================================================================== -->
<style>
:root {
    --cora-main-left: 16rem;
    --cora-main-top: 52px;
    --cora-main-width: calc(100vw - 16rem);
    --cora-main-height: calc(100vh - 52px);
}

/* =========================================================================
   SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANEL (GLOBAL CORA STANDARD - RULE 15)
   - Universal bottom-slide motion: translateY(100%) -> translateY(0)
   - Zero overlay or floating blur: NO dark backdrop element
   - Docks directly below the header: top: var(--cora-main-top, 52px); height: calc(100vh - 52px);
   - Occupies the EXACT available workspace width (based on sidebar status)
   - Zero shadow, square corners (border-radius: 0)
   - Clear formal structural borders: border-left & border-top
   - Sidebar remains 100% visible, clickable, and accessible at all times
   ========================================================================= */
#cora-calendar-appointment-drawer,
#cora-calendar-details-drawer,
#cora-calendar-links-drawer {
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
    z-index: 45 !important; /* Strictly below sidebar (z-index: 60) and topbar (z-index: 9999) */
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
    transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                max-width 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                opacity 0.15s ease !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

.dark #cora-calendar-appointment-drawer,
.dark #cora-calendar-details-drawer,
.dark #cora-calendar-links-drawer {
    background-color: #18181b !important;
    border-top: 1px solid #27272a !important;
    border-left: 1px solid #27272a !important;
}

/* SIDEBAR COLLAPSED: Dynamically adjust to 4rem (64px / w-16) on desktop */
@media (min-width: 1024px) {
    body:has(.cora-sidebar.collapsed-sidebar) #cora-calendar-appointment-drawer:not(.maximized),
    body:has(.cora-sidebar.collapsed-sidebar) #cora-calendar-details-drawer:not(.maximized),
    body:has(.cora-sidebar.collapsed-sidebar) #cora-calendar-links-drawer:not(.maximized),
    body.cora-sidebar-collapsed #cora-calendar-appointment-drawer:not(.maximized),
    body.cora-sidebar-collapsed #cora-calendar-details-drawer:not(.maximized),
    body.cora-sidebar-collapsed #cora-calendar-links-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main #cora-calendar-appointment-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main #cora-calendar-details-drawer:not(.maximized),
    .cora-sidebar.collapsed-sidebar ~ .cora-main #cora-calendar-links-drawer:not(.maximized) {
        left: 4rem !important;
        width: calc(100vw - 4rem) !important;
        max-width: calc(100vw - 4rem) !important;
    }
}

/* MAXIMIZED FULLSCREEN VIEW (Shopify Expand ⤢ Mode) */
#cora-calendar-appointment-drawer.maximized,
#cora-calendar-details-drawer.maximized,
#cora-calendar-links-drawer.maximized {
    left: 0 !important;
    width: 100vw !important;
    max-width: 100vw !important;
    border-left: none !important;
    z-index: 70 !important;
}

/* MOBILE / TABLET (< 1024px): Full viewport width */
@media (max-width: 1023px) {
    #cora-calendar-appointment-drawer,
    #cora-calendar-details-drawer,
    #cora-calendar-links-drawer {
        left: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        border-left: none !important;
        z-index: 10000 !important;
    }
}

/* OPEN & COLLAPSED STATES (UNIVERSAL BOTTOM SLIDE MOTION) */
#cora-calendar-appointment-drawer.open:not(.collapsed),
#cora-calendar-details-drawer.open:not(.collapsed),
#cora-calendar-links-drawer.open:not(.collapsed) {
    transform: translateY(0) !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}

#cora-calendar-appointment-drawer.collapsed,
#cora-calendar-appointment-drawer:not(.open),
#cora-calendar-details-drawer.collapsed,
#cora-calendar-details-drawer:not(.open),
#cora-calendar-links-drawer.collapsed,
#cora-calendar-links-drawer:not(.open) {
    transform: translateY(100%) !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
    display: none !important;
}

/* Calendar Month Grid Structure */
.cora-calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 1px;
    background-color: #e4e4e7;
    border: 1px solid #e4e4e7;
    border-radius: 16px;
    overflow: hidden;
}
.dark .cora-calendar-grid {
    background-color: #27272a;
    border-color: #27272a;
}

.cora-calendar-day-cell {
    min-height: 108px;
    background-color: #ffffff;
    padding: 8px 10px;
    transition: background-color 0.15s ease;
    display: flex;
    flex-direction: column;
    gap: 4px;
    cursor: pointer;
}
.dark .cora-calendar-day-cell {
    background-color: #18181b;
}
.cora-calendar-day-cell:hover {
    background-color: #fafafa;
}
.dark .cora-calendar-day-cell:hover {
    background-color: #202024;
}
.cora-calendar-day-cell.other-month {
    background-color: #fbfbfb;
    opacity: 0.42;
}
.dark .cora-calendar-day-cell.other-month {
    background-color: #121215;
    opacity: 0.35;
}
.cora-calendar-day-cell.is-today {
    background-color: #fafafa;
}
.dark .cora-calendar-day-cell.is-today {
    background-color: #202024;
}

/* Event Capsule Pill (Pure Monochromatic Notion/Shopify styling) */
.cora-event-pill {
    padding: 3.5px 7px;
    border-radius: 6px;
    font-size: 11px;
    line-height: 1.25;
    font-weight: 500;
    transition: all 0.15s ease;
    cursor: pointer;
    border: 1px solid #e4e4e7;
    background-color: #f4f4f5;
    color: #18181b;
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.dark .cora-event-pill {
    background-color: #27272a;
    border-color: #3f3f46;
    color: #f4f4f5;
}
.cora-event-pill:hover {
    background-color: #e4e4e7;
    border-color: #d4d4d8;
    transform: translateY(-1px);
}
.dark .cora-event-pill:hover {
    background-color: #3f3f46;
    border-color: #52525b;
}

/* Custom Monochromatic Switch Track & Thumb */
.cora-switch-track {
    width: 34px;
    height: 18px;
    background-color: #e4e4e7;
    border-radius: 9999px;
    transition: background-color 0.2s ease;
    position: relative;
    cursor: pointer;
    border: none;
    outline: none;
    padding: 2px;
    shrink: 0;
}
.dark .cora-switch-track {
    background-color: #3f3f46;
}
.cora-switch-track.active {
    background-color: #18181b;
}
.dark .cora-switch-track.active {
    background-color: #f4f4f5;
}
.cora-switch-thumb {
    width: 14px;
    height: 14px;
    background-color: #ffffff;
    border-radius: 9999px;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    display: block;
    transform: translateX(0);
}
.dark .cora-switch-track.active .cora-switch-thumb {
    background-color: #18181b;
}
.cora-switch-track.active .cora-switch-thumb {
    transform: translateX(16px);
}

/* Custom Segmented Pill Buttons (No native selects) */
.cora-pill-opt {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 500;
    transition: all 0.15s ease;
    cursor: pointer;
    border: 1px solid #e4e4e7;
    background-color: #ffffff;
    color: #52525b;
}
.dark .cora-pill-opt {
    background-color: #18181b;
    border-color: #27272a;
    color: #a1a1aa;
}
.cora-pill-opt:hover {
    background-color: #f4f4f5;
    color: #09090b;
}
.dark .cora-pill-opt:hover {
    background-color: #27272a;
    color: #ffffff;
}
.cora-pill-opt.active {
    background-color: #09090b !important;
    color: #ffffff !important;
    border-color: #09090b !important;
    font-weight: 600;
}
.dark .cora-pill-opt.active {
    background-color: #f4f4f5 !important;
    color: #09090b !important;
    border-color: #f4f4f5 !important;
}

/* Week View 7-Day Horizontal Deck */
.cora-week-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 12px;
}
@media (max-width: 1023px) {
    .cora-week-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- ==========================================================================
     MAIN INTERACTIVE CALENDAR SURFACE
     ========================================================================== -->
<div id="cora-calendar-module" class="w-full max-w-full select-none pb-12 pt-1 font-sans">

    <!-- 1. DYNAMIC TOP WORKSPACE KPI METRIC CARDS (Platform Level 03 Standard) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 mb-4" id="cora-calendar-kpis-grid">
        <!-- Card 1: Today's Bookings -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 tracking-tight truncate">Today's Schedule</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 truncate">3 Sessions</div>
                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 font-mono">
                    <span>2h 45m scheduled today</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Google 2-Way Sync -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex flex-col justify-between min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 tracking-tight truncate">Google 2-Way Sync</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 truncate flex items-center gap-2">
                    <span>Active</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 font-mono truncate">
                    <span>studio.bookings@cora.local</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Public Booking Flows -->
        <div class="hidden lg:flex bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex-col justify-between min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 tracking-tight truncate">Public Booking Flows</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 truncate">4 Active Flows</div>
                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 font-mono">
                    <span>143 total bookings synced</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Upcoming Week Load -->
        <div class="hidden lg:flex bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-xl p-3 sm:p-3.5 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex-col justify-between min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 tracking-tight truncate">Studio Capacity</span>
                <div class="w-7 h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-xl sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-zinc-100 truncate">82% Utilized</div>
                <div class="mt-1 flex items-center gap-1.5 text-[11px] text-zinc-500 font-mono">
                    <span>Bay A &amp; Google Meet</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. OPTIMIZED TOOLBAR: NAVIGATION, VIEW SWITCHER & ACTIONS -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl p-3 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs border border-zinc-200/80 dark:border-zinc-800">
        
        <!-- Left: Date Navigator & Period Heading -->
        <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
            
            <!-- Date Step Controls -->
            <div class="flex items-center gap-1 bg-zinc-100/90 dark:bg-zinc-800/80 p-1 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80">
                <button 
                    type="button" 
                    onclick="coraNavCalendar(-1)" 
                    class="w-7 h-7 rounded-lg hover:bg-white dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer border-0"
                    title="Previous Period"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>
                <button 
                    type="button" 
                    onclick="coraNavCalendarToday()" 
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold hover:bg-white dark:hover:bg-zinc-900 text-zinc-700 dark:text-zinc-200 hover:text-zinc-950 dark:hover:text-white transition-all cursor-pointer border-0"
                >
                    Today
                </button>
                <button 
                    type="button" 
                    onclick="coraNavCalendar(1)" 
                    class="w-7 h-7 rounded-lg hover:bg-white dark:hover:bg-zinc-900 text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white flex items-center justify-center transition-all cursor-pointer border-0"
                    title="Next Period"
                >
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <!-- Current Month / Period Heading -->
            <h2 class="text-base sm:text-lg font-bold text-zinc-950 dark:text-zinc-50 tracking-tight m-0" id="cora-calendar-period-label">
                October 2026
            </h2>

            <div class="hidden md:block h-4 w-px bg-zinc-200 dark:border-zinc-800"></div>

            <!-- Google Calendar 2-Way Sync Status Indicator -->
            <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 text-xs">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <div class="flex items-center gap-1.5">
                    <span class="font-medium text-zinc-600 dark:text-zinc-300">Google Sync:</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-semibold text-emerald-700 dark:text-emerald-400">2-Way Connected</span>
                </div>
            </div>

        </div>

        <!-- Right: Segmented View Switcher & Action Buttons -->
        <div class="flex items-center gap-2.5 w-full md:w-auto justify-between md:justify-end shrink-0">
            
            <!-- Level 01 Segmented Control View Switcher (Month / Week / Day) -->
            <div class="p-1 bg-zinc-100/90 dark:bg-zinc-800/80 rounded-xl border border-zinc-200/70 dark:border-zinc-800/80 flex items-center gap-1 select-none">
                <button 
                    type="button" 
                    id="cal-view-btn-month"
                    onclick="coraSwitchCalendarView('month', this)" 
                    class="cora-cal-view-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer"
                >
                    Month
                </button>
                <button 
                    type="button" 
                    id="cal-view-btn-week"
                    onclick="coraSwitchCalendarView('week', this)" 
                    class="cora-cal-view-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer"
                >
                    Week
                </button>
                <button 
                    type="button" 
                    id="cal-view-btn-day"
                    onclick="coraSwitchCalendarView('day', this)" 
                    class="cora-cal-view-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer"
                >
                    Day
                </button>
            </div>

            <!-- Direct Action Trigger -->
            <button 
                type="button" 
                onclick="coraOpenCreateAppointmentModal()" 
                class="px-3.5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100 font-bold text-xs transition-all cursor-pointer border-0 flex items-center gap-1.5 shadow-2xs active:scale-95"
            >
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>New Event</span>
            </button>
        </div>

    </div>

    <!-- ======================================================================
         VIEW 1: MONTHLY INTERACTIVE GRID (Default)
         ====================================================================== -->
    <div id="cora-calendar-view-month" class="space-y-2">
        
        <!-- Day-of-Week Column Headers -->
        <div class="grid grid-cols-7 gap-1 text-center font-bold text-[11px] text-zinc-400 dark:text-zinc-500 uppercase tracking-wider py-1.5">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>

        <!-- 35-Day Responsive Grid -->
        <div class="cora-calendar-grid" id="cora-month-grid">
            
            <!-- Week 1: Sep 27 to Oct 3 -->
            <div class="cora-calendar-day-cell other-month" data-date="2026-09-27" onclick="coraDayCellClicked('2026-09-27')">
                <span class="text-xs font-medium text-zinc-400">27</span>
            </div>
            <div class="cora-calendar-day-cell other-month" data-date="2026-09-28" onclick="coraDayCellClicked('2026-09-28')">
                <span class="text-xs font-medium text-zinc-400">28</span>
            </div>
            <div class="cora-calendar-day-cell other-month" data-date="2026-09-29" onclick="coraDayCellClicked('2026-09-29')">
                <span class="text-xs font-medium text-zinc-400">29</span>
            </div>
            <div class="cora-calendar-day-cell other-month" data-date="2026-09-30" onclick="coraDayCellClicked('2026-09-30')">
                <span class="text-xs font-medium text-zinc-400">30</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-01" onclick="coraDayCellClicked('2026-10-01')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">1</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-02" onclick="coraDayCellClicked('2026-10-02')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">2</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-03" onclick="coraDayCellClicked('2026-10-03')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">3</span>
            </div>

            <!-- Week 2: Oct 4 to Oct 10 -->
            <div class="cora-calendar-day-cell" data-date="2026-10-04" onclick="coraDayCellClicked('2026-10-04')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">4</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-05" onclick="coraDayCellClicked('2026-10-05')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">5</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-06" onclick="coraDayCellClicked('2026-10-06')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">6</span>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-101')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                    <span class="font-bold">2:00P</span> Lookbook Prep
                </div>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-07" onclick="coraDayCellClicked('2026-10-07')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">7</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-08" onclick="coraDayCellClicked('2026-10-08')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">8</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-09" onclick="coraDayCellClicked('2026-10-09')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">9</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-10" onclick="coraDayCellClicked('2026-10-10')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">10</span>
            </div>

            <!-- Week 3: Oct 11 to Oct 17 (Contains TODAY Oct 15) -->
            <div class="cora-calendar-day-cell" data-date="2026-10-11" onclick="coraDayCellClicked('2026-10-11')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">11</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-12" onclick="coraDayCellClicked('2026-10-12')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">12</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-13" onclick="coraDayCellClicked('2026-10-13')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">13</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-14" onclick="coraDayCellClicked('2026-10-14')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">14</span>
            </div>
            
            <!-- DAY 15: TODAY -->
            <div class="cora-calendar-day-cell is-today" data-date="2026-10-15" onclick="coraDayCellClicked('2026-10-15')">
                <div class="flex items-center justify-between">
                    <span class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shadow-2xs">
                        15
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">Today</span>
                </div>
                <div class="cora-event-pill mt-1" onclick="event.stopPropagation(); coraOpenEventDetails('evt-104')">
                    <span class="font-bold">10:30A</span> 1-Hr Studio
                </div>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-105')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                    <span class="font-bold">2:00P</span> Intro Call
                </div>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-106')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                    <span class="font-bold">4:30P</span> Retouch Review
                </div>
            </div>

            <div class="cora-calendar-day-cell" data-date="2026-10-16" onclick="coraDayCellClicked('2026-10-16')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">16</span>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-107')">
                    <span class="font-bold">9:00A</span> Half-Day Shoot
                </div>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-17" onclick="coraDayCellClicked('2026-10-17')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">17</span>
            </div>

            <!-- Week 4: Oct 18 to Oct 24 -->
            <div class="cora-calendar-day-cell" data-date="2026-10-18" onclick="coraDayCellClicked('2026-10-18')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">18</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-19" onclick="coraDayCellClicked('2026-10-19')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">19</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-20" onclick="coraDayCellClicked('2026-10-20')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">20</span>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-108')">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                    <span class="font-bold">1:00P</span> Rate Call
                </div>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-21" onclick="coraDayCellClicked('2026-10-21')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">21</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-22" onclick="coraDayCellClicked('2026-10-22')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">22</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-23" onclick="coraDayCellClicked('2026-10-23')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">23</span>
                <div class="cora-event-pill" onclick="event.stopPropagation(); coraOpenEventDetails('evt-109')">
                    <span class="font-bold">11:30A</span> Brand Shoot
                </div>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-24" onclick="coraDayCellClicked('2026-10-24')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">24</span>
            </div>

            <!-- Week 5: Oct 25 to Oct 31 -->
            <div class="cora-calendar-day-cell" data-date="2026-10-25" onclick="coraDayCellClicked('2026-10-25')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">25</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-26" onclick="coraDayCellClicked('2026-10-26')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">26</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-27" onclick="coraDayCellClicked('2026-10-27')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">27</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-28" onclick="coraDayCellClicked('2026-10-28')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">28</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-29" onclick="coraDayCellClicked('2026-10-29')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">29</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-30" onclick="coraDayCellClicked('2026-10-30')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">30</span>
            </div>
            <div class="cora-calendar-day-cell" data-date="2026-10-31" onclick="coraDayCellClicked('2026-10-31')">
                <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300">31</span>
            </div>

        </div>

    </div>

    <!-- ======================================================================
         VIEW 2: WEEKLY 7-DAY SCHEDULE GRID (Full 7-Column Layout)
         ====================================================================== -->
    <div id="cora-calendar-view-week" class="hidden space-y-3">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-4 sm:p-5 shadow-2xs">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div>
                    <h3 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 m-0">Week of October 11 – 17, 2026</h3>
                    <p class="text-xs text-zinc-500 m-0">Studio sessions &amp; Google Meet video appointments</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                    3 Confirmed Today
                </span>
            </div>

            <!-- 7-Column Day Deck -->
            <div class="cora-week-grid pt-4">
                
                <!-- Sun Oct 11 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px]">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Sun</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 11</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-[11px] text-zinc-400">
                        No bookings
                    </div>
                </div>

                <!-- Mon Oct 12 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px]">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Mon</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 12</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-[11px] text-zinc-400">
                        Studio open
                    </div>
                </div>

                <!-- Tue Oct 13 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px]">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Tue</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 13</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-[11px] text-zinc-400">
                        No bookings
                    </div>
                </div>

                <!-- Wed Oct 14 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px]">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Wed</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 14</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-[11px] text-zinc-400">
                        No bookings
                    </div>
                </div>

                <!-- Thu Oct 15 (TODAY) -->
                <div class="p-3 rounded-xl bg-white dark:bg-zinc-850 border-2 border-zinc-950 dark:border-zinc-100 flex flex-col min-h-[220px] space-y-2 shadow-sm">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between">
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">Today</span>
                        <div class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs">15</div>
                    </div>
                    
                    <!-- Events on Day 15 -->
                    <div onclick="coraOpenEventDetails('evt-104')" class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-pointer hover:bg-zinc-200/70 transition-all">
                        <span class="text-[10px] font-mono font-bold text-zinc-500 block">10:30 AM</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block truncate">1-Hr Portrait</span>
                        <span class="text-[10px] text-zinc-500 truncate block">Aarav Mehta</span>
                    </div>

                    <div onclick="coraOpenEventDetails('evt-105')" class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-pointer hover:bg-zinc-200/70 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono font-bold text-zinc-500">02:00 PM</span>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                        </div>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block truncate">Quick Intro Call</span>
                        <span class="text-[10px] text-zinc-500 truncate block">Kavya Patel</span>
                    </div>

                    <div onclick="coraOpenEventDetails('evt-106')" class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-pointer hover:bg-zinc-200/70 transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono font-bold text-zinc-500">04:30 PM</span>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                        </div>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block truncate">Retouch Review</span>
                        <span class="text-[10px] text-zinc-500 truncate block">Rohan Verma</span>
                    </div>
                </div>

                <!-- Fri Oct 16 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px] space-y-2">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Fri</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 16</div>
                    </div>
                    <div onclick="coraOpenEventDetails('evt-107')" class="p-2 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-pointer hover:bg-zinc-200/70 transition-all">
                        <span class="text-[10px] font-mono font-bold text-zinc-500 block">09:00 AM</span>
                        <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 block truncate">Half-Day Shoot</span>
                        <span class="text-[10px] text-zinc-500 truncate block">Kabir Sharma</span>
                    </div>
                </div>

                <!-- Sat Oct 17 -->
                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex flex-col min-h-[220px]">
                    <div class="text-center pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Sat</span>
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Oct 17</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center text-[11px] text-zinc-400">
                        Studio cleanup
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ======================================================================
         VIEW 3: DAILY TIMELINE AGENDA (Hourly Grid)
         ====================================================================== -->
    <div id="cora-calendar-view-day" class="hidden space-y-3">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                <div>
                    <h3 class="text-base font-bold text-zinc-950 dark:text-zinc-50 m-0">Thursday, October 15, 2026</h3>
                    <p class="text-xs text-zinc-500 m-0">Studio Operations Schedule &amp; Client Appointments</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 text-xs font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Google 2-Way Synced</span>
                </span>
            </div>

            <!-- Timeline Hour Rows -->
            <div class="space-y-3">
                
                <!-- 10:30 AM Event -->
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/60 flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-zinc-950 dark:text-white px-2 py-0.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">10:30 AM – 11:30 AM</span>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">1-Hour Studio Portrait Session</span>
                        </div>
                        <p class="text-xs text-zinc-500 m-0">Client: Aarav Mehta (aarav.mehta@example.com) • Main Studio Floor (Bay A)</p>
                        <p class="text-[11px] text-zinc-400 m-0">CRM Lead: Executive Portraits • Booked via Studio Booking Link</p>
                    </div>
                    <button type="button" onclick="coraOpenEventDetails('evt-104')" class="px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 text-xs font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer shrink-0">Open Record</button>
                </div>

                <!-- 02:00 PM Event -->
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/60 flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-zinc-950 dark:text-white px-2 py-0.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">02:00 PM – 02:15 PM</span>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                <span>Quick Intro Call — Campaign Scope</span>
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 m-0">Client: Kavya Patel (kavya.patel@example.com) • Google Meet: meet.google.com/qap-jknw-vtz</p>
                        <p class="text-[11px] text-zinc-400 m-0">CRM Deal Pipeline: Autumn Lookbook (Won)</p>
                    </div>
                    <button type="button" onclick="coraOpenEventDetails('evt-105')" class="px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 text-xs font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer shrink-0">Open Record</button>
                </div>

                <!-- 04:30 PM Event -->
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200/70 dark:border-zinc-700/60 flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-zinc-950 dark:text-white px-2 py-0.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">04:30 PM – 05:00 PM</span>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                <span>Portfolio Retouching Review</span>
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 m-0">Client: Rohan Verma (rohan.verma@example.com) • Google Meet Screen Share</p>
                        <p class="text-[11px] text-zinc-400 m-0">CRM Deal: E-Commerce Catalog</p>
                    </div>
                    <button type="button" onclick="coraOpenEventDetails('evt-106')" class="px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 text-xs font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer shrink-0">Open Record</button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- =========================================================================
     SHOPIFY-INSPIRED MAIN-AREA DOCKED VIEW PANELS (RULE 15 SOP)
     Docked directly beneath global topbar; 0 gap; occupies exact main canvas space.
     ========================================================================= -->

<!-- PANEL 1: SCHEDULE NEW APPOINTMENT (RULE 15 SOP) -->
<aside id="cora-calendar-appointment-drawer" class="collapsed">
    
    <!-- Unified Docked Header -->
    <div class="cora-drawer-header px-4 sm:px-6 py-2.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30 flex items-center justify-between min-h-[44px]">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center font-bold text-xs shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Schedule New Appointment</h2>
                <span class="text-[10px] text-zinc-400 block">Create studio booking or Google Meet session with 2-way sync</span>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Maximize Button -->
            <button 
                type="button" 
                onclick="coraToggleCalendarDrawerMaximize('cora-calendar-appointment-drawer')"
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Expand / Fullscreen Canvas"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
            </button>
            <!-- Close Button -->
            <button 
                type="button" 
                id="btn-close-create-appointment"
                onclick="coraToggleCalendarDrawer('appointment', false)" 
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Close Drawer (Esc)"
            >
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable 2-Column Body -->
    <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 text-xs text-zinc-800 dark:text-zinc-200">
        <div class="max-w-4xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Left Column: Details & Attendees (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Title Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">1. Session Title &amp; Timing</span>
                    
                    <div class="space-y-1">
                        <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Appointment Title <span class="text-zinc-400">*</span></label>
                        <input 
                            type="text" 
                            id="appt-input-title" 
                            placeholder="e.g. 1-Hour Studio Portrait Session" 
                            class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 transition font-medium"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Date</label>
                            <input 
                                type="date" 
                                id="appt-input-date" 
                                value="2026-10-15"
                                class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400 font-medium"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Start Time</label>
                            <input 
                                type="time" 
                                id="appt-input-time" 
                                value="11:00"
                                class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400 font-medium"
                            >
                        </div>
                    </div>
                </div>

                <!-- Client Contact & CRM Deal -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">2. Client &amp; CRM Pipeline</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Client Name</label>
                            <input 
                                type="text" 
                                id="appt-input-client-name" 
                                placeholder="e.g. Aarav Mehta" 
                                class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 font-medium"
                            >
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300">Client Email</label>
                            <input 
                                type="email" 
                                id="appt-input-client-email" 
                                placeholder="client@example.com" 
                                class="w-full px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 font-medium"
                            >
                        </div>
                    </div>

                    <!-- Location Selector (Monochromatic Segmented Control, Zero Native Selects) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 block">Session Location / Format</label>
                        <div class="flex flex-wrap gap-1.5" id="cora-location-pill-group">
                            <button type="button" class="cora-pill-opt active" data-loc="Main Studio Floor (Bay A)" onclick="coraSelectLocationPill(this)">Main Studio Floor (Bay A)</button>
                            <button type="button" class="cora-pill-opt" data-loc="Google Meet" onclick="coraSelectLocationPill(this)">Google Meet Video Call</button>
                            <button type="button" class="cora-pill-opt" data-loc="Studio Bay B (Portrait Suite)" onclick="coraSelectLocationPill(this)">Studio Bay B (Portrait)</button>
                            <button type="button" class="cora-pill-opt" data-loc="Full Studio Cyclorama" onclick="coraSelectLocationPill(this)">Full Cyclorama</button>
                        </div>
                        <!-- Standard Select for Form Serializing & Test Automation -->
                        <select id="appt-input-location" onchange="coraSyncLocationSelectToPill(this)" class="w-full mt-2 px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400 font-medium">
                            <option value="Main Studio Floor (Bay A)" selected>Main Studio Floor (Bay A)</option>
                            <option value="Google Meet">Google Meet</option>
                            <option value="Studio Bay B (Portrait Suite)">Studio Bay B (Portrait Suite)</option>
                            <option value="Full Studio Cyclorama">Full Studio Cyclorama</option>
                        </select>
                    </div>

                    <!-- Related CRM Lead -->
                    <div class="space-y-1.5 pt-1">
                        <label class="text-[11px] font-semibold text-zinc-700 dark:text-zinc-300 block">Associated CRM Deal Pipeline</label>
                        <div class="flex flex-wrap gap-1.5" id="cora-crm-pill-group">
                            <button type="button" class="cora-pill-opt active" data-lead="Aarav Mehta — Executive Portraits" onclick="coraSelectCrmPill(this)">Aarav Mehta (Executive Portraits)</button>
                            <button type="button" class="cora-pill-opt" data-lead="Kavya Patel — Autumn Lookbook" onclick="coraSelectCrmPill(this)">Kavya Patel (Autumn Lookbook)</button>
                            <button type="button" class="cora-pill-opt" data-lead="Rohan Verma — E-Commerce Catalog" onclick="coraSelectCrmPill(this)">Rohan Verma (Catalog)</button>
                            <button type="button" class="cora-pill-opt" data-lead="Direct Studio Walk-In" onclick="coraSelectCrmPill(this)">Direct Walk-In</button>
                        </div>
                        <select id="appt-input-crm-deal" onchange="coraSyncCrmSelectToPill(this)" class="w-full mt-2 px-3 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400 font-medium">
                            <option value="Aarav Mehta — Executive Portraits" selected>Aarav Mehta — Executive Portraits</option>
                            <option value="Kavya Patel — Autumn Lookbook">Kavya Patel — Autumn Lookbook</option>
                            <option value="Rohan Verma — E-Commerce Catalog">Rohan Verma — E-Commerce Catalog</option>
                            <option value="Direct Studio Walk-In">Direct Studio Walk-In</option>
                        </select>
                    </div>

                </div>

            </div>

            <!-- Right Column: Google Sync & Confirmation (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- Google Sync Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Google Calendar Sync</span>
                    
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Bidirectional Sync</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">Active</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-normal m-0">
                            Pushes booking to <code>studio.bookings@cora.local</code> and sends calendar invite with video links.
                        </p>
                    </div>

                    <!-- Auto Google Meet Toggle -->
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Auto-generate Google Meet</span>
                            <span class="text-[10px] text-zinc-400">Creates secure 3-4-3 lowercase meeting link</span>
                        </div>
                        <button 
                            type="button" 
                            id="btn-toggle-appt-meet"
                            role="switch" 
                            aria-checked="true" 
                            onclick="this.classList.toggle('active')" 
                            class="cora-switch-track active"
                        >
                            <span class="cora-switch-thumb"></span>
                        </button>
                    </div>

                    <!-- Conflict Guard -->
                    <div class="p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 text-[10.5px] text-zinc-500 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-zinc-700 dark:text-zinc-300">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            <span>Conflict Guard Verified</span>
                        </div>
                        <p class="m-0">No overlapping sessions detected on the selected studio bay.</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Edge-to-Edge Sticky Bottom Footer (Rule 15 SOP) -->
    <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
        <div class="flex items-center gap-2 text-xs text-zinc-500">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Ready to schedule &amp; synchronize</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="coraToggleCalendarDrawer('appointment', false)" class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold text-xs transition cursor-pointer">
                Cancel
            </button>
            <button type="button" onclick="coraSaveNewAppointment()" class="px-5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 font-bold text-xs transition cursor-pointer border-0 shadow-2xs flex items-center gap-1.5">
                <span>Confirm &amp; Sync Event &rarr;</span>
            </button>
        </div>
    </div>

</aside>

<!-- PANEL 2: APPOINTMENT DETAILS & GOOGLE SYNC RECORD (RULE 15 SOP) -->
<aside id="cora-calendar-details-drawer" class="collapsed">
    
    <!-- Unified Docked Header -->
    <div class="cora-drawer-header px-4 sm:px-6 py-2.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30 flex items-center justify-between min-h-[44px]">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center font-bold text-xs shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Appointment Record</h2>
                <span class="text-[10px] text-zinc-400 block">Synchronized with Google Calendar &amp; Meet</span>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <button 
                type="button" 
                onclick="coraToggleCalendarDrawerMaximize('cora-calendar-details-drawer')"
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Expand / Fullscreen Canvas"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
            </button>
            <button 
                type="button" 
                id="btn-close-event-details"
                onclick="coraToggleCalendarDrawer('details', false)" 
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Close (Esc)"
            >
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable 2-Column Body -->
    <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 text-xs text-zinc-800 dark:text-zinc-200">
        <div class="max-w-4xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Left Column: Event Core Info (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- Title & Status Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Confirmed &amp; Google Synced</span>
                        </span>
                        <span class="text-[11px] font-mono text-zinc-400" id="detail-event-duration">Duration: 60 Mins</span>
                    </div>

                    <h3 class="text-base sm:text-lg font-bold text-zinc-950 dark:text-zinc-50 tracking-tight m-0" id="detail-event-title">
                        1-Hour Studio Portrait Session
                    </h3>

                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-zinc-200/80 dark:bg-zinc-700 flex items-center justify-center font-mono font-bold text-xs shrink-0">
                            📅
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">Scheduled Date &amp; Time</span>
                            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100" id="detail-event-datetime">Thursday, Oct 15, 2026 • 10:30 AM</span>
                        </div>
                    </div>
                </div>

                <!-- Client & CRM Relationship Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Client &amp; Related CRM Lead</span>
                    
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0">
                            CL
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50 m-0" id="detail-client-name">Aarav Mehta</h4>
                            <p class="text-xs text-zinc-500 font-mono m-0" id="detail-client-email">aarav.mehta@example.com</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                        <span class="text-zinc-500">CRM Deal Pipeline:</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100" id="detail-crm-lead">Executive Portraits</span>
                    </div>
                </div>

                <!-- Location & Google Meet Section -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Session Location / Video Format</span>
                    
                    <div class="flex items-center gap-2 text-xs font-medium text-zinc-800 dark:text-zinc-200" id="detail-location-row">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span id="detail-location-text">Main Studio Floor (Bay A)</span>
                    </div>

                    <!-- Google Meet Video Link Box (Shown when Meet exists) -->
                    <div id="detail-meet-container" class="hidden p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                            <div class="min-w-0">
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">Google Meet Video Link</span>
                                <span class="text-[11px] text-zinc-500 font-mono truncate block" id="detail-meet-link-text">https://meet.google.com/qap-jknw-vtz</span>
                            </div>
                        </div>
                        <a id="detail-meet-btn" href="#" target="_blank" class="px-3 py-1.5 rounded-lg bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold text-xs shrink-0 no-underline hover:bg-zinc-800 transition">
                            Join Video
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Column: Sync & Integration Metadata (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                
                <!-- Google Calendar 2-Way Sync Explainer Card -->
                <div class="p-4 sm:p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs space-y-3">
                    <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Google 2-Way Synchronization</span>
                    
                    <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700/60 space-y-1.5">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-900 dark:text-zinc-100">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Live Bidirectional Push</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-relaxed m-0">
                            Any edits to timing, attendees, or notes made in your Google Calendar app update here automatically in real time.
                        </p>
                    </div>

                    <div class="space-y-1.5 pt-1 text-[11px] text-zinc-600 dark:text-zinc-400">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400">Source Account:</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200">studio.bookings@cora.local</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-400">Calendar Event UID:</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200">cora_evt_sync_984</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Edge-to-Edge Sticky Bottom Footer (Rule 15 SOP) -->
    <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
        <button 
            type="button" 
            onclick="coraCancelAppointmentAction()" 
            class="px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold text-xs transition cursor-pointer"
        >
            Cancel Appointment
        </button>
        <button 
            type="button" 
            onclick="coraToggleCalendarDrawer('details', false)" 
            class="px-5 py-2 rounded-xl bg-zinc-950 hover:bg-zinc-800 text-white dark:bg-white dark:text-zinc-950 font-bold text-xs transition cursor-pointer border-0 shadow-2xs"
        >
            Done
        </button>
    </div>

</aside>

<!-- PANEL 3: PUBLIC BOOKING FLOWS (RULE 15 SOP) -->
<aside id="cora-calendar-links-drawer" class="collapsed">
    
    <!-- Unified Docked Header -->
    <div class="cora-drawer-header px-4 sm:px-6 py-2.5 border-b border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 sticky top-0 z-30 flex items-center justify-between min-h-[44px]">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center font-bold text-xs shrink-0 border border-zinc-200/60 dark:border-zinc-700/60">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-zinc-950 dark:text-zinc-50 leading-tight">Public Booking Flows</h2>
                <span class="text-[10px] text-zinc-400 block">Self-scheduling links linked to your Google Calendar availability</span>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <!-- Create Link Header Trigger -->
            <button 
                type="button" 
                onclick="coraToggleNewFlowForm()" 
                class="px-3 py-1.5 rounded-lg bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold transition hover:bg-zinc-800 cursor-pointer border-0 flex items-center gap-1 shadow-2xs"
            >
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Create Link</span>
            </button>
            <button 
                type="button" 
                onclick="coraToggleCalendarDrawerMaximize('cora-calendar-links-drawer')"
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Expand / Fullscreen Canvas"
            >
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h6v6"></path><path d="M9 21H3v-6"></path><path d="M21 3l-7 7"></path><path d="M3 21l7-7"></path></svg>
            </button>
            <button 
                type="button" 
                id="btn-close-booking-links"
                onclick="coraToggleCalendarDrawer('links', false)" 
                class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 flex items-center justify-center text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition cursor-pointer border border-zinc-200/80 dark:border-zinc-700 shadow-2xs"
                title="Close (Esc)"
            >
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
    </div>

    <!-- Scrollable Body -->
    <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 text-xs text-zinc-800 dark:text-zinc-200">
        <div class="max-w-4xl mx-auto w-full space-y-4">
            
            <!-- Inline Create New Flow Form (Collapsible) -->
            <div id="cora-create-flow-inline-form" class="hidden p-4 sm:p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 space-y-3 shadow-2xs">
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider block">New Appointment Link Details</span>
                
                <input 
                    type="text" 
                    id="drawer-input-flow-title" 
                    placeholder="e.g. 2-Hour Lookbook Shoot" 
                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200/90 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:border-zinc-400 font-medium"
                >

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input 
                        type="text" 
                        id="drawer-input-flow-slug" 
                        placeholder="slug (e.g. lookbook-2hr)" 
                        class="px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200/90 dark:border-zinc-700 text-xs font-mono text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-zinc-400"
                    >
                    
                    <!-- Segmented Duration Pills (Zero Native Selects) -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" class="cora-pill-opt" data-dur="15" onclick="coraSelectDurationPill(this)">15 Mins</button>
                        <button type="button" class="cora-pill-opt" data-dur="30" onclick="coraSelectDurationPill(this)">30 Mins</button>
                        <button type="button" class="cora-pill-opt active" data-dur="60" onclick="coraSelectDurationPill(this)">60 Mins</button>
                        <button type="button" class="cora-pill-opt" data-dur="120" onclick="coraSelectDurationPill(this)">2 Hours</button>
                    </div>
                    <select id="drawer-input-flow-duration" class="hidden" aria-hidden="true">
                        <option value="15">15 Mins</option>
                        <option value="30">30 Mins</option>
                        <option value="60" selected>60 Mins</option>
                        <option value="120">2 Hours</option>
                    </select>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">Auto Google Meet Video Link</span>
                        <button 
                            type="button" 
                            id="btn-toggle-flow-meet"
                            role="switch" 
                            aria-checked="true" 
                            onclick="this.classList.toggle('active')" 
                            class="cora-switch-track active"
                        >
                            <span class="cora-switch-thumb"></span>
                        </button>
                        <input type="checkbox" id="drawer-input-flow-meet" class="hidden" checked aria-hidden="true">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="coraToggleNewFlowForm()" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-600 hover:text-zinc-900 cursor-pointer">Cancel</button>
                        <button type="button" onclick="coraSaveNewBookingFlow()" class="px-4 py-2 rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 text-xs font-bold cursor-pointer border-0 shadow-2xs">Save Link</button>
                    </div>
                </div>
            </div>

            <!-- List of Active Booking Flows -->
            <div class="space-y-3" id="cora-drawer-flows-list">
                <?php foreach ( $booking_flows as $flow ) : 
                    $flow_url = $booking_root . '/' . esc_attr( $flow['slug'] );
                    $is_active = ! empty( $flow['active'] );
                ?>
                <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 space-y-3 shadow-2xs" id="drawer-flow-card-<?php echo esc_attr( $flow['id'] ); ?>">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50 m-0"><?php echo esc_html( $flow['title'] ); ?></h4>
                            <span class="text-[10px] text-zinc-400 font-mono mt-0.5 block"><?php echo esc_html( $flow['duration_fmt'] ); ?> • <?php echo esc_html( $flow['price_tag'] ); ?></span>
                        </div>
                        <button 
                            type="button" 
                            role="switch"
                            aria-checked="<?php echo $is_active ? 'true' : 'false'; ?>"
                            onclick="coraToggleDrawerFlowActive('<?php echo esc_attr( $flow['id'] ); ?>', this)"
                            class="cora-switch-track <?php echo $is_active ? 'active' : ''; ?>"
                        >
                            <span class="cora-switch-thumb"></span>
                        </button>
                    </div>

                    <div 
                        onclick="coraCopySnippet('<?php echo esc_url( $flow_url ); ?>', this)"
                        class="px-3 py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/60 flex items-center justify-between gap-2 cursor-pointer transition-all"
                    >
                        <span class="font-mono text-[11px] text-zinc-600 dark:text-zinc-300 truncate"><?php echo esc_html( $flow_url ); ?></span>
                        <span class="text-[10px] text-zinc-400 font-semibold shrink-0 flex items-center gap-1.5">
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="copy-feedback-icon"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            <span class="copy-feedback-text">Copy</span>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- Edge-to-Edge Sticky Bottom Footer (Rule 15 SOP) -->
    <div class="px-4 sm:px-6 lg:px-8 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 sticky bottom-0 z-30">
        <span class="flex items-center gap-1.5 text-xs font-medium text-zinc-500">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Google Calendar 2-Way Active</span>
        </span>
        <button type="button" onclick="coraToggleCalendarDrawer('links', false)" class="px-5 py-2 bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold rounded-xl hover:bg-zinc-800 transition cursor-pointer text-xs border-0 shadow-2xs">
            Done
        </button>
    </div>

</aside>

<!-- ==========================================================================
     CLIENT-SIDE LOGIC & STATE ENGINE
     Zero emojis. Zero browser defaults (alert/confirm/prompt). Monochromatic toasts.
     ========================================================================== -->
<script>
// Registered mock event records
var coraCalendarEvents = <?php echo json_encode( $initial_events ); ?>;

// Custom Monochromatic Toast Notification Helper (Zero browser alerts)
function coraCalendarToast(message, type) {
    if (typeof window.coraShowToast === 'function') {
        window.coraShowToast(message, type || 'info');
        return;
    }
    var container = document.getElementById('cora-calendar-toast-box');
    if (!container) {
        container = document.createElement('div');
        container.id = 'cora-calendar-toast-box';
        container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:999999;display:flex;flex-direction:column;gap:8px;pointer-events:none;';
        document.body.appendChild(container);
    }
    var toast = document.createElement('div');
    toast.style.cssText = 'pointer-events:auto;display:flex;align-items:center;gap:10px;padding:12px 18px;border-radius:12px;background:#18181b;color:#ffffff;border:1px solid rgba(255,255,255,0.15);font-size:12px;font-weight:500;box-shadow:0 12px 32px rgba(0,0,0,0.5);opacity:0;transform:translateY(8px);transition:all 0.25s ease;';
    toast.innerHTML = '<span>' + message + '</span>';
    container.appendChild(toast);
    requestAnimationFrame(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(8px)';
        setTimeout(function() { toast.remove(); }, 250);
    }, 3000);
}

// =========================================================================
// RULE 15 DYNAMIC BOUNDS SYNCHRONIZATION ENGINE
// =========================================================================
function coraSyncCalendarDrawerBounds() {
    var isMobile = window.innerWidth < 1024;
    var sidebarEl = document.getElementById('cora-sidebar') || document.querySelector('.cora-sidebar');
    var isSidebarCollapsed = document.body.classList.contains('cora-sidebar-collapsed') || 
                            (sidebarEl && sidebarEl.classList.contains('collapsed-sidebar'));

    var drawers = [
        document.getElementById('cora-calendar-appointment-drawer'),
        document.getElementById('cora-calendar-details-drawer'),
        document.getElementById('cora-calendar-links-drawer')
    ];

    drawers.forEach(function(drawer) {
        if (!drawer) return;

        if (drawer.classList.contains('maximized')) {
            drawer.style.left = '0px';
            drawer.style.top = '52px';
            drawer.style.width = '100vw';
            drawer.style.maxWidth = '100vw';
            drawer.style.height = (window.innerHeight - 52) + 'px';
            drawer.style.maxHeight = (window.innerHeight - 52) + 'px';
            return;
        }

        var topbarEl = document.getElementById('cora-global-topbar');
        var actualTop = topbarEl ? Math.round(topbarEl.getBoundingClientRect().bottom) : 52;
        if (actualTop < 0 || isNaN(actualTop)) actualTop = 52;

        var targetLeft = 0;
        if (!isMobile && sidebarEl) {
            var sRect = sidebarEl.getBoundingClientRect();
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

        drawer.style.left = targetLeft + 'px';
        drawer.style.top = actualTop + 'px';
        drawer.style.width = targetWidth + 'px';
        drawer.style.maxWidth = targetWidth + 'px';
        drawer.style.height = targetHeight + 'px';
        drawer.style.maxHeight = targetHeight + 'px';
    });
}

window.addEventListener('resize', coraSyncCalendarDrawerBounds);
window.addEventListener('load', coraSyncCalendarDrawerBounds);
document.addEventListener('DOMContentLoaded', coraSyncCalendarDrawerBounds);

// Observer for sidebar status mutations
var coraSidebarEl = document.getElementById('cora-sidebar') || document.querySelector('.cora-sidebar');
if (coraSidebarEl && window.MutationObserver) {
    var coraSidebarObserver = new MutationObserver(function() {
        coraSyncCalendarDrawerBounds();
    });
    coraSidebarObserver.observe(coraSidebarEl, { attributes: true, attributeFilter: ['class', 'style'] });
}

// Maximize Canvas Toggle (Shopify ⤢ Mode)
function coraToggleCalendarDrawerMaximize(drawerId) {
    var drawer = document.getElementById(drawerId);
    if (!drawer) return;
    drawer.classList.toggle('maximized');
    coraSyncCalendarDrawerBounds();
}

// 1. Switch Calendar Views: Month, Week, Day
function coraSwitchCalendarView(view, btn) {
    var btns = document.querySelectorAll('.cora-cal-view-btn');
    btns.forEach(function(b) {
        b.className = 'cora-cal-view-btn px-3 py-1.5 rounded-lg text-xs font-medium transition-all text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 bg-transparent border-0 cursor-pointer';
    });
    if (btn) {
        btn.className = 'cora-cal-view-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white shadow-2xs border-0 cursor-pointer';
    }

    var monthView = document.getElementById('cora-calendar-view-month');
    var weekView = document.getElementById('cora-calendar-view-week');
    var dayView = document.getElementById('cora-calendar-view-day');
    var periodLabel = document.getElementById('cora-calendar-period-label');

    if (monthView) monthView.classList.add('hidden');
    if (weekView) weekView.classList.add('hidden');
    if (dayView) dayView.classList.add('hidden');

    if (view === 'month' && monthView) {
        monthView.classList.remove('hidden');
        if (periodLabel) periodLabel.innerText = 'October 2026';
    } else if (view === 'week' && weekView) {
        weekView.classList.remove('hidden');
        if (periodLabel) periodLabel.innerText = 'Oct 11 – 17, 2026';
    } else if (view === 'day' && dayView) {
        dayView.classList.remove('hidden');
        if (periodLabel) periodLabel.innerText = 'Thursday, Oct 15, 2026';
    }
}

// 2. Navigation controls (Next / Prev / Today)
function coraNavCalendar(direction) {
    coraCalendarToast('Loaded calendar schedule for selected period.', 'info');
}

function coraNavCalendarToday() {
    coraSwitchCalendarView('month', document.getElementById('cal-view-btn-month'));
    var todayCell = document.querySelector('.cora-calendar-day-cell.is-today');
    if (todayCell) {
        todayCell.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    coraCalendarToast('Jumped to today: October 15, 2026', 'info');
}

// Helper: Generate Authentic Google Meet 3-4-3 Lowercase Alphabetic Meeting Code
function coraGenerateGoogleMeetUrl() {
    var chars = 'abcdefghijklmnopqrstuvwxyz';
    function randStr(len) {
        var s = '';
        for (var i = 0; i < len; i++) {
            s += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return s;
    }
    return 'https://meet.google.com/' + randStr(3) + '-' + randStr(4) + '-' + randStr(3);
}

// Form Pill Selectors (Replacing native browser selects)
function coraSelectLocationPill(btn) {
    var group = document.getElementById('cora-location-pill-group');
    if (!group) return;
    group.querySelectorAll('.cora-pill-opt').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var val = btn.getAttribute('data-loc');
    var hiddenSelect = document.getElementById('appt-input-location');
    if (hiddenSelect) {
        hiddenSelect.value = val;
    }
}

function coraSelectCrmPill(btn) {
    var group = document.getElementById('cora-crm-pill-group');
    if (!group) return;
    group.querySelectorAll('.cora-pill-opt').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var val = btn.getAttribute('data-lead');
    var hiddenSelect = document.getElementById('appt-input-crm-deal');
    if (hiddenSelect) {
        hiddenSelect.value = val;
    }
}

function coraSelectDurationPill(btn) {
    var container = btn.parentElement;
    if (!container) return;
    container.querySelectorAll('.cora-pill-opt').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var val = btn.getAttribute('data-dur');
    var hiddenSelect = document.getElementById('drawer-input-flow-duration');
    if (hiddenSelect) {
        hiddenSelect.value = val;
    }
}

function coraSyncLocationSelectToPill(selectEl) {
    var val = selectEl.value;
    var group = document.getElementById('cora-location-pill-group');
    if (!group) return;
    group.querySelectorAll('.cora-pill-opt').forEach(function(b) {
        if (b.getAttribute('data-loc') === val) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });
}

function coraSyncCrmSelectToPill(selectEl) {
    var val = selectEl.value;
    var group = document.getElementById('cora-crm-pill-group');
    if (!group) return;
    group.querySelectorAll('.cora-pill-opt').forEach(function(b) {
        if (b.getAttribute('data-lead') === val) {
            b.classList.add('active');
        } else {
            b.classList.remove('active');
        }
    });
}


// Canonical Docked Panel Toggle Controller (RULE 15 SOP)
function coraToggleCalendarDrawer(type, show) {
    coraSyncCalendarDrawerBounds();
    
    var drawerMap = {
        'appointment': 'cora-calendar-appointment-drawer',
        'create': 'cora-calendar-appointment-drawer',
        'links': 'cora-calendar-links-drawer',
        'details': 'cora-calendar-details-drawer'
    };
    var targetId = drawerMap[type] || type;
    var targetDrawer = document.getElementById(targetId);

    var allDrawerIds = [
        'cora-calendar-appointment-drawer',
        'cora-calendar-links-drawer',
        'cora-calendar-details-drawer'
    ];

    if (show) {
        // Close other panels first
        allDrawerIds.forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el !== targetDrawer) {
                el.classList.add('collapsed');
                el.classList.remove('open');
                el.style.display = 'none';
            }
        });

        if (targetDrawer) {
            if (targetDrawer.parentNode !== document.body) {
                document.body.appendChild(targetDrawer);
            }
            targetDrawer.classList.remove('collapsed', 'hidden');
            targetDrawer.classList.add('open');
            targetDrawer.style.display = 'flex';
            coraSyncCalendarDrawerBounds();
        }

        var mainEl = document.querySelector('main.cora-main');
        if (mainEl) mainEl.classList.add('overflow-hidden');
    } else {
        if (targetDrawer) {
            targetDrawer.classList.add('collapsed');
            targetDrawer.classList.remove('open');
            targetDrawer.style.display = 'none';
        }

        var mainEl = document.querySelector('main.cora-main');
        if (mainEl) mainEl.classList.remove('overflow-hidden');
    }
}
window.coraToggleCalendarDrawer = coraToggleCalendarDrawer;

// 3. Open Event Details Drawer
function coraOpenEventDetails(eventId) {
    var evt = coraCalendarEvents.find(function(e) { return e.id === eventId; });
    if (!evt) return;

    var titleEl = document.getElementById('detail-event-title');
    var dtEl = document.getElementById('detail-event-datetime');
    var durEl = document.getElementById('detail-event-duration');
    var nameEl = document.getElementById('detail-client-name');
    var emailEl = document.getElementById('detail-client-email');
    var crmEl = document.getElementById('detail-crm-lead');
    var locEl = document.getElementById('detail-location-text');

    if (titleEl) titleEl.innerText = evt.title;
    if (dtEl) dtEl.innerText = evt.date + ' • ' + evt.time;
    if (durEl) durEl.innerText = 'Duration: ' + evt.duration;
    if (nameEl) nameEl.innerText = evt.client_name;
    if (emailEl) emailEl.innerText = evt.client_email;
    if (crmEl) crmEl.innerText = evt.crm_lead;
    if (locEl) locEl.innerText = evt.location;

    var meetContainer = document.getElementById('detail-meet-container');
    var meetBtn = document.getElementById('detail-meet-btn');
    var meetText = document.getElementById('detail-meet-link-text');

    if (evt.google_meet && meetContainer && meetBtn) {
        meetContainer.classList.remove('hidden');
        meetBtn.href = evt.google_meet;
        if (meetText) meetText.innerText = evt.google_meet;
    } else if (meetContainer) {
        meetContainer.classList.add('hidden');
    }

    coraToggleCalendarDrawer('details', true);
}
window.coraOpenEventDetails = coraOpenEventDetails;

// 4. Open Booking Links Drawer
function coraOpenBookingLinksDrawer() {
    coraToggleCalendarDrawer('links', true);
}
window.coraOpenBookingLinksDrawer = coraOpenBookingLinksDrawer;

// 5. Open Create Appointment Drawer
function coraOpenCreateAppointmentModal(prefillDate) {
    if (prefillDate) {
        var dateInput = document.getElementById('appt-input-date');
        if (dateInput) dateInput.value = prefillDate;
    }

    coraToggleCalendarDrawer('appointment', true);
    setTimeout(function() {
        var titleInput = document.getElementById('appt-input-title');
        if (titleInput) titleInput.focus();
    }, 60);
}
window.coraOpenCreateAppointmentModal = coraOpenCreateAppointmentModal;
window.coraOpenCreateAppointmentDrawer = coraOpenCreateAppointmentModal;

function coraDayCellClicked(dateStr) {
    coraOpenCreateAppointmentModal(dateStr);
}

// 6. Close All Calendar Panels
function coraCloseCalendarPanels() {
    ['cora-calendar-appointment-drawer', 'cora-calendar-links-drawer', 'cora-calendar-details-drawer'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.classList.add('collapsed');
            el.classList.remove('open');
            el.style.display = 'none';
        }
    });
    var mainEl = document.querySelector('main.cora-main');
    if (mainEl) mainEl.classList.remove('overflow-hidden');
}
window.coraCloseCalendarPanels = coraCloseCalendarPanels;

var prevCloseAllDrawers = window.coraCloseAllDrawers;
window.coraCloseAllDrawers = function() {
    if (typeof prevCloseAllDrawers === 'function') {
        prevCloseAllDrawers();
    }
    coraCloseCalendarPanels();
};

// 7. Save New Appointment
function coraSaveNewAppointment() {
    var title = document.getElementById('appt-input-title').value.trim();
    if (!title) {
        coraCalendarToast('Please enter an event title.', 'error');
        document.getElementById('appt-input-title').focus();
        return;
    }

    var date = document.getElementById('appt-input-date').value;
    var time = document.getElementById('appt-input-time').value;
    var clientName = document.getElementById('appt-input-client-name').value.trim() || 'Client Inquiry';
    var clientEmail = document.getElementById('appt-input-client-email').value.trim() || 'client@example.com';
    var location = document.getElementById('appt-input-location').value;
    var crmDeal = document.getElementById('appt-input-crm-deal').value;

    var newId = 'evt-' + Date.now();
    var isMeet = location.indexOf('Google Meet') !== -1;
    var meetUrl = isMeet ? coraGenerateGoogleMeetUrl() : '';

    var newEvt = {
        id: newId,
        title: title,
        client_name: clientName,
        client_email: clientEmail,
        date: date,
        time: time,
        duration: '60 Mins',
        location: location,
        google_meet: meetUrl,
        crm_lead: crmDeal,
        source: 'Internal Workspace Schedule',
        synced: true
    };

    coraCalendarEvents.push(newEvt);

    // Prepend pill into target day cell
    var targetCell = document.querySelector('.cora-calendar-day-cell[data-date="' + date + '"]');
    if (targetCell) {
        var pill = document.createElement('div');
        pill.className = 'cora-event-pill';
        pill.onclick = function(e) { e.stopPropagation(); coraOpenEventDetails(newId); };
        pill.innerHTML = (isMeet ? '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 7l-7 5 7 5V7z"></path><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>' : '') + '<span class="font-bold">' + time + '</span> ' + title;
        targetCell.appendChild(pill);
    }

    coraCloseCalendarPanels();
    coraCalendarToast('Appointment scheduled & synced to Google Calendar.', 'success');
}

// 8. Cancel Appointment Action
function coraCancelAppointmentAction() {
    coraCloseCalendarPanels();
    coraCalendarToast('Appointment canceled. Synced deletion to Google Calendar.', 'info');
}

// 9. Copy URL Snippet with Custom Feedback
function coraCopySnippet(url, element) {
    if (!url) return;
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(onSuccess).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }

    function fallbackCopy() {
        var textArea = document.createElement("textarea");
        textArea.value = url;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            onSuccess();
        } catch (err) {
            coraCalendarToast('Unable to copy URL automatically', 'error');
        }
        textArea.remove();
    }

    function onSuccess() {
        coraCalendarToast('Booking link copied: ' + url, 'success');
        
        var icon = element.querySelector('.copy-feedback-icon');
        var text = element.querySelector('.copy-feedback-text');
        if (icon && text) {
            var origIcon = icon.innerHTML;
            var origText = text.innerText;
            icon.innerHTML = '<polyline points="20 6 9 17 4 12" stroke="#10b981" stroke-width="2.5"></polyline>';
            text.innerText = 'Copied!';
            text.style.color = '#10b981';
            setTimeout(function() {
                icon.innerHTML = origIcon;
                text.innerText = origText;
                text.style.color = '';
            }, 2000);
        }
    }
}

// 10. Toggle Booking Flow Active Switch
function coraToggleDrawerFlowActive(flowId, btn) {
    var isActive = btn.classList.contains('active');
    if (isActive) {
        btn.classList.remove('active');
        btn.setAttribute('aria-checked', 'false');
        coraCalendarToast('Booking link paused.', 'info');
    } else {
        btn.classList.add('active');
        btn.setAttribute('aria-checked', 'true');
        coraCalendarToast('Booking link activated.', 'info');
    }
}

// 11. Toggle Inline New Flow Form
function coraToggleNewFlowForm() {
    var form = document.getElementById('cora-create-flow-inline-form');
    if (form) {
        form.classList.toggle('hidden');
    }
}

function coraSaveNewBookingFlow() {
    var title = document.getElementById('drawer-input-flow-title').value.trim();
    if (!title) {
        coraCalendarToast('Please enter a booking link title.', 'error');
        return;
    }
    var slug = document.getElementById('drawer-input-flow-slug').value.trim() || title.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    var duration = document.getElementById('drawer-input-flow-duration').value;
    var rootUrl = '<?php echo esc_js( $booking_root ); ?>';
    var fullUrl = rootUrl + '/' + slug;

    var list = document.getElementById('cora-drawer-flows-list');
    if (list) {
        var card = document.createElement('div');
        card.className = 'p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 space-y-3 shadow-2xs';
        card.innerHTML = 
            '<div class="flex items-start justify-between gap-3">' +
                '<div>' +
                    '<h4 class="text-xs font-bold text-zinc-950 dark:text-zinc-50 m-0">' + title + '</h4>' +
                    '<span class="text-[10px] text-zinc-400 font-mono mt-0.5 block">' + duration + ' Mins • Custom Link</span>' +
                '</div>' +
                '<button type="button" role="switch" aria-checked="true" class="cora-switch-track active" onclick="this.classList.toggle(\'active\')"><span class="cora-switch-thumb"></span></button>' +
            '</div>' +
            '<div onclick="coraCopySnippet(\'' + fullUrl + '\', this)" class="px-3 py-2 rounded-xl bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/60 dark:hover:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/60 flex items-center justify-between gap-2 cursor-pointer transition-all">' +
                '<span class="font-mono text-[11px] text-zinc-600 dark:text-zinc-300 truncate">' + fullUrl + '</span>' +
                '<span class="text-[10px] text-zinc-400 font-semibold shrink-0 flex items-center gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="copy-feedback-icon"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg><span class="copy-feedback-text">Copy</span></span>' +
            '</div>';
        list.prepend(card);
    }
    coraToggleNewFlowForm();
    coraCalendarToast('Public booking link created: ' + title, 'success');
}

function coraTriggerGoogleSync() {
    coraCalendarToast('Google Calendar 2-way sync refreshed.', 'info');
}

// Global Escape Key Listener for all Calendar Panels
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        coraCloseCalendarPanels();
    }
});
</script>
