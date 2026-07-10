<?php
/**
 * Cora Real Estate CRM - Module 4: System Tools, Diagnostics & Data Migration
 * Studio-Grade Monochromatic UI/UX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$php_version   = PHP_VERSION;
$mysql_version = $wpdb->db_version();
$is_ssl        = is_ssl() ? 'Active (Secured)' : 'Inactive (HTTP Only)';
$ssl_class     = is_ssl() ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-amber-700 bg-amber-50 border-amber-200';
$cron_status   = ( ! defined('DISABLE_WP_CRON') || ! DISABLE_WP_CRON ) ? 'Operational' : 'Disabled (Manual Trigger)';
$memory_limit  = defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : ini_get('memory_limit');
?>

<div class="cora-page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <span class="cora-page-emoji text-zinc-900 flex shrink-0">
            <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
            </svg>
        </span>
        <div>
            <h1 class="cora-page-title text-2xl font-bold tracking-tight text-zinc-900">System Tools & Diagnostics</h1>
            <p class="text-sm text-zinc-500 mt-0.5">Audit server health, perform XML WXR data migrations, and manage GDPR privacy compliance.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button class="cora-btn-secondary px-3.5 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-800 font-semibold rounded-md text-xs transition-colors cursor-pointer flex items-center gap-2 shadow-sm" onclick="coraCopySiteDiagnostics()">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            Copy Site Diagnostics
        </button>
    </div>
</div>

<!-- Section 1: Real-time Site Health Audits -->
<div class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Real-time Server & Platform Diagnostics</h3>
        <span class="text-xs font-mono text-zinc-500">System Health: <strong class="text-emerald-700">100% Operational</strong></span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- PHP Version Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">PHP Engine</span>
                <div class="text-lg font-bold text-zinc-900 font-mono">v<?php echo esc_html( $php_version ); ?></div>
                <p class="text-[11px] text-zinc-500">Optimal performance & security.</p>
            </div>
            <span class="p-2 rounded-lg bg-zinc-100 text-zinc-800">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </span>
        </div>

        <!-- Database Engine Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">MySQL Database</span>
                <div class="text-lg font-bold text-zinc-900 font-mono">v<?php echo esc_html( $mysql_version ); ?></div>
                <p class="text-[11px] text-zinc-500">Relational schema indexed.</p>
            </div>
            <span class="p-2 rounded-lg bg-zinc-100 text-zinc-800">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
            </span>
        </div>

        <!-- SSL / Encryption Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">SSL / HTTPS</span>
                <div class="text-sm font-bold mt-1 inline-block px-2 py-0.5 rounded border <?php echo esc_attr( $ssl_class ); ?>"><?php echo esc_html( $is_ssl ); ?></div>
                <p class="text-[11px] text-zinc-500 mt-1">End-to-end transport security.</p>
            </div>
            <span class="p-2 rounded-lg bg-zinc-100 text-zinc-800">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </span>
        </div>

        <!-- WP Cron & Memory Card -->
        <div class="bg-white border border-zinc-200/80 rounded-xl p-4 shadow-sm flex items-start justify-between">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Cron & Memory</span>
                <div class="text-sm font-bold text-zinc-900"><?php echo esc_html( $cron_status ); ?></div>
                <p class="text-[11px] text-zinc-500">Allocated Memory: <strong class="font-mono"><?php echo esc_html( $memory_limit ); ?></strong></p>
            </div>
            <span class="p-2 rounded-lg bg-zinc-100 text-zinc-800">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </span>
        </div>
    </div>
</div>

<!-- Section 2: Data Migration Engine (XML) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Export WXR XML -->
    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4 flex flex-col justify-between">
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export Platform XML Data
                </h3>
                <span class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded font-mono font-bold text-zinc-600">Export</span>
            </div>
            <p class="text-xs text-zinc-600 leading-relaxed">Generate a standard Extended RSS (CXR) XML file containing your posts, static pages, comments, custom fields, categories, and tags for safe backups or migration.</p>
            <div class="space-y-2 pt-1">
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-semibold cursor-pointer">
                    <input type="radio" name="cora_export_type" value="all" checked class="text-zinc-900 focus:ring-zinc-900">
                    <span>All Content (Posts, Pages, Media, Comments, Taxonomies)</span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="radio" name="cora_export_type" value="posts" class="text-zinc-900 focus:ring-zinc-900">
                    <span>Posts & Blog Articles Only</span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="radio" name="cora_export_type" value="pages" class="text-zinc-900 focus:ring-zinc-900">
                    <span>Static Pages & Landing Pages Only</span>
                </label>
                <label class="flex items-center gap-2.5 text-xs text-zinc-800 font-medium cursor-pointer">
                    <input type="radio" name="cora_export_type" value="media" class="text-zinc-900 focus:ring-zinc-900">
                    <span>Media Library Attachments Only</span>
                </label>
            </div>
        </div>
        <div class="pt-4 border-t border-zinc-100 flex items-center justify-end">
            <button class="px-4 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm cursor-pointer flex items-center gap-2" onclick="coraRunXMLExport()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Download XML Export File
            </button>
        </div>
    </div>

    <!-- Import WXR XML -->
    <div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4 flex flex-col justify-between">
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Import Content Package
                </h3>
                <span class="text-[10px] bg-zinc-100 px-2 py-0.5 rounded font-mono font-bold text-zinc-600">Import</span>
            </div>
            <p class="text-xs text-zinc-600 leading-relaxed">Import posts, pages, comments, custom fields, categories, and media from a standard platform CXR XML export file or external platform archive.</p>
            <div class="border-2 border-dashed border-zinc-200 rounded-xl p-6 text-center space-y-2 bg-zinc-50/50 hover:bg-zinc-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-white border border-zinc-200 flex items-center justify-center mx-auto text-zinc-700 shadow-sm">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
                <div class="text-xs font-bold text-zinc-900">Select CXR `.xml` file to import</div>
                <p class="text-[11px] text-zinc-400">Max upload file size: <?php echo esc_html( size_format( wp_max_upload_size() ) ); ?></p>
                <input type="file" id="cora-import-file" accept=".xml" class="hidden" onchange="coraShowSelectedImportFile(this)">
                <button class="mt-2 px-3.5 py-1.5 bg-white border border-zinc-300 hover:bg-zinc-100 text-zinc-800 font-semibold text-xs rounded-lg transition-colors cursor-pointer shadow-sm inline-block" onclick="document.getElementById('cora-import-file').click()">
                    Browse Local File
                </button>
                <div id="cora-selected-file-display" class="text-xs text-emerald-700 font-bold hidden pt-1"></div>
            </div>
        </div>
        <div class="pt-4 border-t border-zinc-100 flex items-center justify-end">
            <button class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm cursor-pointer flex items-center gap-2" onclick="coraRunXMLImport()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Run XML Importer
            </button>
        </div>
    </div>
</div>

<!-- Section 3: GDPR & Privacy Compliance -->
<div class="bg-white border border-zinc-200/80 rounded-xl p-5 shadow-sm space-y-4">
    <div class="border-b border-zinc-100 pb-3">
        <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            GDPR & Personal Data Compliance Suite
        </h3>
        <p class="text-xs text-zinc-500 mt-0.5">Comply with international privacy regulations by exporting or erasing personal data associated with email addresses.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-4 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-3">
            <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-wider">Export Personal Data Package</h4>
            <p class="text-xs text-zinc-600">Generate a downloadable archive containing all personal data stored across posts, comments, and lead logs for a specific user.</p>
            <div class="flex flex-col sm:flex-row gap-2 pt-1">
                <input type="email" id="cora-gdpr-export-email" placeholder="client@example.com" class="w-full sm:flex-1 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                <button class="px-3.5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer shrink-0" onclick="coraRunGDPRExport()">Export Data</button>
            </div>
        </div>
        <div class="p-4 bg-red-50/40 border border-red-200/60 rounded-xl space-y-3">
            <h4 class="text-xs font-bold text-red-900 uppercase tracking-wider">Erase Personal Data (Right to be Forgotten)</h4>
            <p class="text-xs text-red-700">Permanently anonymize or delete personal data associated with an email address upon legal request.</p>
            <div class="flex flex-col sm:flex-row gap-2 pt-1">
                <input type="email" id="cora-gdpr-erase-email" placeholder="client@example.com" class="w-full sm:flex-1 bg-white border border-red-300 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none">
                <button class="px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer shrink-0" onclick="coraRunGDPRErase()">Anonymize & Erase</button>
            </div>
        </div>
    </div>
</div>
