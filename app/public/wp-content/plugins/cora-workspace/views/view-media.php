<?php
/**
 * Cora — Media Library
 * Top-bar folder nav, full-width canvas, slide-in detail overlay.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$is_admin        = current_user_can( 'manage_options' );
$current_user_id = get_current_user_id();
$listings        = cora_db_get_properties();
$clients         = cora_db_get_clients();
$leads           = cora_db_get_leads();
$all_doc_types   = array( 'Agreement / Contract', 'KYC Document', 'Brochure', 'Floor Plan', 'NOC / Approval', 'Invoice', 'Other' );
?>
<style>
/* ─── Escape parent wrapper padding so media is truly full-bleed ─────────── */
/* The .cora-content-wrapper has p-3.5/p-6/p-8 & space-y-6 — we negate that */
#cora-page-media { margin: -1.5rem -1.5rem -1.5rem -1.5rem !important; padding: 0 !important; }
@media (min-width: 640px)  { #cora-page-media { margin: -1.5rem -1.5rem -1.5rem -1.5rem !important; } }
@media (min-width: 768px)  { #cora-page-media { margin: -2rem -2rem -2rem -2rem !important; } }
/* Also neutralize the space-y-6 gap from parent flex */
#cora-page-media + * { margin-top: 0 !important; }

/* ─── Reset & full-bleed canvas ─────────────────────────────────────────── */
/* Height = full viewport minus the Cora topnav (~56px) + any extra wrapper chrome */
#cm-root {
    display: flex;
    flex-direction: column;
    height: calc(100dvh - 56px);
    max-height: calc(100dvh - 56px);
    overflow: hidden;
    background: #fff;
    position: relative;
    /* Ensure cm-root takes exactly the right height regardless of parent padding */
    margin: 0;
    border-radius: 0;
}

/* ─── Top header bar ─────────────────────────────────────────────────────── */
#cm-header { flex-shrink:0; border-bottom:1px solid #e4e4e7; background:#fff; }
#cm-header-top { display:flex; align-items:center; gap:10px; padding:11px 18px; flex-wrap:wrap; }
.cm-h-title { font-size:15px; font-weight:800; color:#09090b; letter-spacing:-.02em; }
.cm-h-subtitle { font-size:11px; color:#a1a1aa; font-weight:500; }
.cm-h-sep { flex:1; }
.cm-h-storage { display:flex; flex-direction:column; gap:3px; min-width:130px; }
.cm-h-storage-label { font-size:10px; color:#a1a1aa; font-weight:600; display:flex; justify-content:space-between; }
.cm-sbar-w { height:3px; background:#f4f4f5; border-radius:99px; overflow:hidden; }
.cm-sbar   { height:100%; background:#18181b; border-radius:99px; transition:width .4s; }
.cm-hbtn { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #e4e4e7; background:#fff; color:#3f3f46; transition:all .12s; white-space:nowrap; }
.cm-hbtn:hover { background:#f4f4f5; border-color:#d4d4d8; }
.cm-hbtn.primary { background:#09090b; color:#fff; border-color:#09090b; }
.cm-hbtn.primary:hover { background:#27272a; }
.cm-toggle { display:flex; border:1px solid #e4e4e7; border-radius:8px; overflow:hidden; }
.cm-toggle button { padding:5px 9px; background:#fff; border:none; cursor:pointer; color:#a1a1aa; display:flex; align-items:center; transition:background .12s; }
.cm-toggle button.on { background:#09090b; color:#fff; }
.cm-toggle button + button { border-left:1px solid #e4e4e7; }

/* ─── Folder tab bar ─────────────────────────────────────────────────────── */
#cm-tab-bar { display:flex; align-items:center; gap:0; padding:0 14px; overflow-x:auto; scrollbar-width:none; border-bottom:1px solid #f4f4f5; flex-shrink:0; background:#fafafa; }
#cm-tab-bar::-webkit-scrollbar { display:none; }
.cm-ftab { display:inline-flex; align-items:center; gap:5px; padding:8px 14px; font-size:12px; font-weight:600; color:#71717a; cursor:pointer; white-space:nowrap; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .12s,border-color .12s; position:relative; }
.cm-ftab:hover { color:#18181b; }
.cm-ftab.active { color:#09090b; border-bottom-color:#09090b; }
.cm-ftab .ct { font-size:10px; background:#f4f4f5; color:#71717a; border-radius:999px; padding:1px 6px; margin-left:1px; }
.cm-ftab.active .ct { background:#18181b; color:#fff; }
.cm-ftab-add { padding:8px 10px; color:#a1a1aa; cursor:pointer; display:flex; align-items:center; border:none; background:none; transition:color .12s; }
.cm-ftab-add:hover { color:#18181b; }

/* ─── Toolbar (search + filters) ────────────────────────────────────────── */
#cm-toolbar { display:flex; align-items:center; gap:8px; padding:9px 18px; border-bottom:1px solid #f4f4f5; flex-wrap:wrap; flex-shrink:0; background:#fff; }
.cm-search-wrap { position:relative; flex:1; min-width:160px; max-width:320px; }
.cm-search-wrap svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); pointer-events:none; color:#a1a1aa; }
.cm-search { width:100%; border:1px solid #e4e4e7; border-radius:8px; padding:6px 10px 6px 30px; font-size:12px; background:#fafafa; outline:none; box-sizing:border-box; }
.cm-search:focus { border-color:#71717a; background:#fff; }
.cm-sel { border:1px solid #e4e4e7; border-radius:8px; padding:6px 9px; font-size:11px; color:#52525b; background:#fff; outline:none; cursor:pointer; }
.cm-bulk-bar { display:none; align-items:center; gap:7px; padding:4px 10px; background:#f4f4f5; border-radius:8px; }
.cm-bulk-bar.on { display:flex; }

/* ─── Upload progress ────────────────────────────────────────────────────── */
#cm-uprog { display:none; flex-direction:column; max-height:150px; overflow-y:auto; background:#fafafa; border-bottom:1px solid #e4e4e7; flex-shrink:0; }
#cm-uprog.on { display:flex; }
.cm-urow { display:flex; align-items:center; gap:9px; padding:7px 18px; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-ubar-w { flex:1; height:3px; background:#e4e4e7; border-radius:99px; overflow:hidden; }
.cm-ubar   { height:100%; background:#18181b; border-radius:99px; transition:width .3s; }
.cm-ubar.err { background:#ef4444; }

/* ─── Grid canvas ────────────────────────────────────────────────────────── */
#cm-canvas { flex:1; overflow-y:auto; padding:16px 18px; }
#cm-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; }
.cm-cell { position:relative; border:1px solid #e4e4e7; border-radius:10px; overflow:hidden; cursor:pointer; transition:box-shadow .15s,border-color .15s; background:#fafafa; }
.cm-cell:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); border-color:#a1a1aa; }
.cm-cell.sel { border-color:#09090b; box-shadow:0 0 0 2px #09090b; }
.cm-thumb { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; }
.cm-cell-icon { width:100%; aspect-ratio:4/3; display:flex; align-items:center; justify-content:center; background:#f4f4f5; }
.cm-cell-name { padding:6px 8px; font-size:11px; font-weight:500; color:#52525b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; border-top:1px solid #f4f4f5; background:#fff; }
.cm-cell-type { position:absolute; top:7px; right:7px; background:rgba(0,0,0,.55); color:#fff; font-size:9px; font-weight:700; padding:2px 6px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
.cm-chk { position:absolute; top:7px; left:7px; width:16px; height:16px; border-radius:5px; border:2px solid rgba(255,255,255,.8); background:rgba(0,0,0,.2); display:none; align-items:center; justify-content:center; cursor:pointer; transition:background .1s; }
.cm-bulk-mode .cm-chk { display:flex; }
.cm-chk.checked { background:#09090b; border-color:#09090b; }
.cm-chk svg { display:none; }
.cm-chk.checked svg { display:block; }

/* ─── List view ──────────────────────────────────────────────────────────── */
#cm-list { display:none; }
.cm-ltable { width:100%; border-collapse:collapse; font-size:12px; }
.cm-ltable thead th { padding:9px 12px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#a1a1aa; border-bottom:1px solid #e4e4e7; background:#fafafa; cursor:pointer; user-select:none; }
.cm-ltable thead th:hover { color:#18181b; }
.cm-ltable tbody tr { border-bottom:1px solid #f4f4f5; cursor:pointer; transition:background .1s; }
.cm-ltable tbody tr:hover { background:#fafafa; }
.cm-ltable tbody tr.sel { background:#f4f4f5; }
.cm-ltable td { padding:9px 12px; color:#3f3f46; vertical-align:middle; }
.cm-lthumb { width:36px; height:36px; border-radius:7px; object-fit:cover; flex-shrink:0; }

/* ─── Empty / Loading ────────────────────────────────────────────────────── */
#cm-empty { display:none; flex-direction:column; align-items:center; justify-content:center; height:100%; min-height:280px; gap:10px; text-align:center; padding:40px; }
#cm-loading { display:none; padding:16px 18px; }
.cm-sk { background:#f4f4f5; border-radius:10px; aspect-ratio:4/3; animation:cm-pulse 1.4s ease-in-out infinite; }
@keyframes cm-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
#cm-pag { display:none; align-items:center; justify-content:space-between; padding:10px 18px; border-top:1px solid #f4f4f5; font-size:11px; color:#71717a; flex-shrink:0; }
#cm-pag button { padding:5px 14px; border:1px solid #e4e4e7; border-radius:7px; font-size:11px; font-weight:600; background:#fff; cursor:pointer; }
#cm-pag button:hover { background:#f4f4f5; }
#cm-pag button:disabled { opacity:.4; cursor:default; }

/* ─── Drop overlay ───────────────────────────────────────────────────────── */
#cm-drop { position:absolute; inset:0; background:rgba(9,9,11,.6); backdrop-filter:blur(4px); z-index:800; display:none; align-items:center; justify-content:center; pointer-events:none; }
#cm-drop.on { display:flex; }
.cm-drop-box { border:2px dashed rgba(255,255,255,.5); border-radius:16px; padding:40px 64px; text-align:center; color:#fff; }

/* ─── Detail overlay (right slide-in) ───────────────────────────────────── */
#cm-detail { position:fixed; top:0; right:0; bottom:0; width:360px; background:#fff; border-left:1px solid #e4e4e7; box-shadow:-8px 0 32px rgba(0,0,0,.08); display:flex; flex-direction:column; transform:translateX(100%); transition:transform .25s cubic-bezier(.4,0,.2,1); z-index:500; }
#cm-detail.open { transform:translateX(0); }
#cm-detail-header { flex-shrink:0; padding:13px 16px; border-bottom:1px solid #e4e4e7; display:flex; align-items:center; gap:8px; }
#cm-detail-header h3 { flex:1; font-size:13px; font-weight:700; color:#09090b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0; }
.cm-preview { flex-shrink:0; height:180px; background:#09090b; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.cm-preview img,.cm-preview video { max-height:100%; max-width:100%; object-fit:contain; }
.cm-detail-tabs { display:flex; border-bottom:1px solid #e4e4e7; flex-shrink:0; }
.cm-dtab { padding:8px 14px; font-size:11px; font-weight:600; color:#a1a1aa; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .12s; }
.cm-dtab.active { color:#09090b; border-bottom-color:#09090b; }
.cm-dbody { flex:1; overflow-y:auto; padding:14px; }
.cm-field { margin-bottom:12px; }
.cm-field label { display:block; font-size:10px; font-weight:700; color:#a1a1aa; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px; }
.cm-field input,.cm-field textarea,.cm-field select { width:100%; border:1px solid #e4e4e7; border-radius:7px; padding:6px 9px; font-size:12px; color:#18181b; background:#fff; outline:none; transition:border .15s; box-sizing:border-box; resize:vertical; }
.cm-field input:focus,.cm-field textarea:focus,.cm-field select:focus { border-color:#71717a; }
.cm-meta-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-meta-row .mk { color:#a1a1aa; font-size:10px; font-weight:600; text-transform:uppercase; }
.cm-meta-row .mv { color:#3f3f46; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center; gap:4px; }
#cm-detail-footer { flex-shrink:0; padding:10px 14px; border-top:1px solid #e4e4e7; background:#fafafa; display:flex; gap:6px; }
.cm-dfbtn { flex:1; padding:8px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #e4e4e7; background:#fff; color:#3f3f46; transition:all .12s; }
.cm-dfbtn:hover { background:#f4f4f5; }
.cm-dfbtn.save { background:#09090b; color:#fff; border-color:#09090b; }
.cm-dfbtn.save:hover { background:#27272a; }
.cm-dfbtn.del { color:#dc2626; border-color:#fecaca; }
.cm-dfbtn.del:hover { background:#fef2f2; }

/* ─── Share link rows ────────────────────────────────────────────────────── */
.cm-share-row { display:flex; align-items:center; gap:6px; padding:6px 0; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-share-row .su { flex:1; min-width:0; }
.cm-share-row .su strong { display:block; color:#3f3f46; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cm-share-row .se { color:#a1a1aa; font-size:10px; }

/* ─── Activity ───────────────────────────────────────────────────────────── */
.cm-act-row { display:flex; gap:8px; padding:6px 0; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-act-dot { width:6px; height:6px; border-radius:50%; background:#d4d4d8; margin-top:4px; flex-shrink:0; }

/* ─── Image Editor modal ─────────────────────────────────────────────────── */
#cm-editor-modal {
    position:fixed; inset:0; z-index:9999;
    background:rgba(9,9,11,.8); backdrop-filter:blur(5px);
    display:none; align-items:center; justify-content:center;
    padding:24px 16px;
    overflow:hidden;
}
#cm-editor-modal.open { display:flex; }
#cm-editor-card {
    background:#fff; border-radius:14px; width:100%; max-width:860px;
    height:640px;
    max-height:calc(100dvh - 48px);
    min-height:350px;
    display:flex; flex-direction:column;
    overflow:hidden;
    box-shadow:0 30px 60px rgba(0,0,0,.3);
}
#cm-editor-card-header { flex-shrink:0; padding:13px 20px; border-bottom:1px solid #e4e4e7; display:flex; align-items:center; gap:8px; }
#cm-editor-card-header h3 { flex:1; font-size:13px; font-weight:700; margin:0; }
#cm-editor-canvas { flex:1; background:#09090b; display:flex; align-items:center; justify-content:center; overflow:hidden; min-height:0; position:relative; }
#cm-editor-canvas img { max-width:90%; max-height:90%; object-fit:contain; }
#cm-editor-bar {
    flex-shrink:0; background:#fafafa; border-top:1px solid #e4e4e7;
    padding:11px 16px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;
}
.cm-ebtn { display:inline-flex; align-items:center; gap:5px; padding:6px 11px; border:1px solid #e4e4e7; border-radius:7px; font-size:11px; font-weight:600; color:#3f3f46; background:#fff; cursor:pointer; transition:all .12s; white-space:nowrap; }
.cm-ebtn:hover { border-color:#71717a; color:#09090b; }
.cm-ebtn.primary { background:#09090b; color:#fff; border-color:#09090b; }
.cm-scale-group { display:flex; align-items:center; gap:5px; }
.cm-scale-group input { width:60px; border:1px solid #e4e4e7; border-radius:6px; padding:5px 7px; font-size:11px; text-align:center; outline:none; }

/* ─── Confirm modal ──────────────────────────────────────────────────────── */
#cm-confirm-modal { position:fixed; inset:0; z-index:10000; background:rgba(9,9,11,.5); backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
#cm-confirm-modal.open { display:flex; }
#cm-confirm-card { background:#fff; border:1px solid #e4e4e7; border-radius:14px; padding:24px; max-width:420px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,.15); }
#cm-confirm-card h4 { margin:0 0 6px; font-size:14px; font-weight:700; }
#cm-confirm-card p { margin:0 0 20px; font-size:12px; color:#71717a; }
#cm-confirm-card .btns { display:flex; gap:8px; justify-content:flex-end; }

/* ─── New folder drawer ──────────────────────────────────────────────────── */
#cm-folder-dlg { position:fixed; inset:0; z-index:99999; background:rgba(9,9,11,.4); backdrop-filter:blur(2px); display:flex; justify-content:flex-end; opacity:0; pointer-events:none; transition:opacity .25s ease; }
#cm-folder-dlg.open { opacity:1; pointer-events:auto; }
#cm-folder-card { background:#fff; border-left:1px solid #e4e4e7; height:100%; width:100%; max-width:420px; box-shadow:-10px 0 30px rgba(0,0,0,.15); display:flex; flex-direction:column; transform:translateX(100%); transition:transform .25s ease; }
#cm-folder-dlg.open #cm-folder-card { transform:translateX(0); }
.cm-drawer-header { padding:20px; border-bottom:1px solid #e4e4e7; display:flex; align-items:center; justify-content:space-between; background:#fafafa; }
.cora-dark-theme .cm-drawer-header { border-color:#27272a; background:#1c1c1e; }
.cm-drawer-body { flex:1; overflow-y:auto; padding:24px; }
.cm-drawer-footer { padding:20px; border-top:1px solid #e4e4e7; display:flex; align-items:center; justify-content:flex-end; gap:12px; background:#fafafa; }
.cora-dark-theme .cm-drawer-footer { border-color:#27272a; background:#1c1c1e; }

/* ─── Dark mode ──────────────────────────────────────────────────────────── */
.cora-dark-theme #cm-root,
.cora-dark-theme #cm-header,
.cora-dark-theme #cm-tab-bar,
.cora-dark-theme #cm-toolbar,
.cora-dark-theme #cm-canvas { background:#111113; }
.cora-dark-theme #cm-tab-bar { background:#111113; border-color:#27272a; }
.cora-dark-theme #cm-header { border-color:#27272a; }
.cora-dark-theme #cm-toolbar { border-color:#27272a; }
.cora-dark-theme .cm-ftab { color:#71717a; }
.cora-dark-theme .cm-ftab.active { color:#fafafa; border-color:#fafafa; }
.cora-dark-theme .cm-cell { background:#1c1c1e; border-color:#27272a; }
.cora-dark-theme .cm-cell-name { background:#1c1c1e; color:#a1a1aa; border-color:#27272a; }
.cora-dark-theme .cm-cell-icon { background:#27272a; }
.cora-dark-theme .cm-hbtn { background:#1c1c1e; border-color:#27272a; color:#d4d4d8; }
.cora-dark-theme .cm-sel,.cora-dark-theme .cm-search { background:#1c1c1e; border-color:#27272a; color:#d4d4d8; }
.cora-dark-theme #cm-detail { background:#111113; border-color:#27272a; }
.cora-dark-theme #cm-detail-footer { background:#1c1c1e; border-color:#27272a; }
.cora-dark-theme .cm-field input,.cora-dark-theme .cm-field textarea,.cora-dark-theme .cm-field select { background:#1c1c1e; border-color:#27272a; color:#fafafa; }
.cora-dark-theme #cm-editor-card { background:#111113; }
.cora-dark-theme #cm-editor-bar { background:#1c1c1e; border-color:#27272a; }
.cora-dark-theme .cm-ebtn { background:#27272a; border-color:#3f3f46; color:#d4d4d8; }
.cora-dark-theme #cm-confirm-card,
.cora-dark-theme #cm-folder-card { background:#111113; border-color:#27272a; }
.cora-dark-theme #cm-folder-card input { background:#1c1c1e; border-color:#27272a; color:#fafafa; }
.cora-dark-theme #cm-folder-card h3 { color:#fafafa; }
.cora-dark-theme #cm-folder-card label { color:#d4d4d8; }
.cora-dark-theme .cm-h-title { color:#fafafa; }
.cora-dark-theme .cm-ltable thead th { background:#1c1c1e; }
.cora-dark-theme .cm-ltable tbody tr:hover { background:#1c1c1e; }

/* ─── Responsive ─────────────────────────────────────────────────────────── */
@media(max-width:640px){
    #cm-grid { grid-template-columns:repeat(2,1fr); }
    #cm-detail { width:100vw; }
}
</style>

<!-- ═══════════════════════════════════════════════════════════ ROOT -->
<div id="cm-root">

    <!-- ─── HEADER ──────────────────────────────────────────────── -->
    <div id="cm-header">
        <div id="cm-header-top">
            <!-- Icon + title -->
            <span style="color:#a1a1aa;flex-shrink:0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            </span>
            <div>
                <div class="cm-h-title">Media Library</div>
                <div class="cm-h-subtitle">Property photos, documents, floor plans, and all agency assets</div>
            </div>

            <div class="cm-h-sep"></div>

            <!-- Storage bar -->
            <div class="cm-h-storage" style="display:none" id="cm-storage-wrap">
                <div class="cm-h-storage-label"><span id="cm-storage-lbl">—</span><span id="cm-storage-pct">—</span></div>
                <div class="cm-sbar-w" style="min-width:120px"><div class="cm-sbar" id="cm-storage-bar" style="width:0%"></div></div>
            </div>

            <!-- View toggle -->
            <div class="cm-toggle">
                <button id="cm-btn-grid" onclick="cmSetView('grid')" class="on" title="Grid">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>
                </button>
                <button id="cm-btn-list" onclick="cmSetView('list')" title="List">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
            </div>

            <button id="cm-bulk-btn" onclick="cmToggleBulk()" class="cm-hbtn">Select</button>
            <button onclick="document.getElementById('cm-file-input').click()" class="cm-hbtn primary">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                Upload
            </button>
            <input type="file" id="cm-file-input" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.zip" style="display:none" onchange="cmHandleFiles(this.files)">
        </div>

        <!-- ─ FOLDER TAB BAR ──────────────────────────────────── -->
        <div id="cm-tab-bar">
            <div class="cm-ftab active" id="cm-ftab-all" onclick="cmSelectFolder(null,this)" data-folder-id="">
                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                All Media <span class="ct" id="cm-ftab-all-ct">—</span>
            </div>
            <div class="cm-ftab" id="cm-ftab-none" onclick="cmSelectFolder(-1,this)" data-folder-id="-1">
                Unorganised <span class="ct" id="cm-ftab-none-ct">—</span>
            </div>
            <!-- Dynamic folder tabs injected by JS -->
            <div id="cm-folder-tabs"></div>
            <button class="cm-ftab-add" onclick="cmPromptFolder(null)" title="New folder">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
        </div>
    </div>

    <!-- ─── TOOLBAR (search + filters) ─────────────────────────── -->
    <div id="cm-toolbar">
        <div class="cm-search-wrap">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="cm-search" class="cm-search" placeholder="Search files…" oninput="cmOnSearch(this.value)">
        </div>
        <select id="cm-ft" class="cm-sel" onchange="cmFilter()">
            <option value="all">All Types</option><option value="image">Images</option><option value="video">Videos</option><option value="audio">Audio</option><option value="document">Documents</option>
        </select>
        <select id="cm-fd" class="cm-sel" onchange="cmFilter()"><option value="">All Dates</option></select>
        <?php if ($is_admin): ?><select id="cm-fa" class="cm-sel" onchange="cmFilter()"><option value="">All Uploaders</option></select><?php endif; ?>

        <!-- Bulk action bar -->
        <div id="cm-bulk-bar" class="cm-bulk-bar">
            <span id="cm-bulk-ct" style="font-size:11px;font-weight:700;color:#3f3f46">0 selected</span>
            <select id="cm-bulk-folder" class="cm-sel" style="font-size:11px"><option value="">Move to folder…</option></select>
            <button onclick="cmBulkMove()" class="cm-hbtn" style="font-size:11px;padding:4px 9px">Move</button>
            <button onclick="cmBulkDelete()" class="cm-hbtn" style="font-size:11px;padding:4px 9px;color:#dc2626;border-color:#fecaca">Delete</button>
        </div>
    </div>

    <!-- Upload progress -->
    <div id="cm-uprog"></div>

    <!-- ─── MAIN CANVAS ──────────────────────────────────────────── -->
    <div id="cm-canvas">
        <!-- Skeleton -->
        <div id="cm-loading" style="display:none">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px">
                <?php for($i=0;$i<8;$i++): ?><div class="cm-sk"></div><?php endfor; ?>
            </div>
        </div>
        <!-- Empty -->
        <div id="cm-empty">
            <div style="width:52px;height:52px;border-radius:14px;background:#f4f4f5;display:flex;align-items:center;justify-content:center">
                <svg viewBox="0 0 24 24" width="22" height="22" stroke="#a1a1aa" stroke-width="1.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#3f3f46;margin:0" id="cm-empty-title">No files here</p>
            <p style="font-size:12px;color:#a1a1aa;margin:0">Upload files or drag and drop anywhere on this page.</p>
            <button onclick="document.getElementById('cm-file-input').click()" class="cm-hbtn primary" style="margin-top:6px">Upload Files</button>
        </div>
        <!-- Grid -->
        <div id="cm-grid"></div>
        <!-- List -->
        <div id="cm-list">
            <table class="cm-ltable">
                <thead><tr>
                    <th style="width:34px"><input type="checkbox" id="cm-list-all" onchange="cmListAll(this.checked)" style="display:none;accent-color:#09090b"></th>
                    <th onclick="cmSort('title')">File</th>
                    <th onclick="cmSort('author')">Uploader</th>
                    <th onclick="cmSort('folder')">Folder</th>
                    <th onclick="cmSort('date')">Date ↕</th>
                    <th onclick="cmSort('size')">Size</th>
                    <th>Type</th>
                </tr></thead>
                <tbody id="cm-list-body"></tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div id="cm-pag">
        <span id="cm-pag-info"></span>
        <div style="display:flex;gap:6px">
            <button id="cm-prev" onclick="cmPage(-1)">← Prev</button>
            <button id="cm-next" onclick="cmPage(1)">Next →</button>
        </div>
    </div>

    <!-- Drop overlay -->
    <div id="cm-drop">
        <div class="cm-drop-box">
            <svg viewBox="0 0 24 24" width="36" height="36" stroke="rgba(255,255,255,.7)" stroke-width="1.5" fill="none" style="margin:0 auto 10px;display:block"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <p style="font-size:15px;font-weight:700;margin:0">Drop files to upload</p>
            <p style="font-size:12px;margin:6px 0 0;opacity:.7">Images, Videos, Audio, PDFs — max 25 MB each</p>
        </div>
    </div>
</div><!-- /cm-root -->

<!-- ═══ DETAIL PANEL (right slide-in overlay) ══════════════════════════════ -->
<div id="cm-detail">
    <div id="cm-detail-header">
        <h3 id="cm-d-name">File Details</h3>
        <button id="cm-d-edit-btn" onclick="cmOpenEditor()" class="cm-hbtn" style="display:none;font-size:11px;padding:4px 9px">
            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Edit
        </button>
        <button onclick="cmCloseDetail()" style="width:26px;height:26px;border-radius:7px;border:none;background:none;cursor:pointer;color:#a1a1aa;display:flex;align-items:center;justify-content:center" title="Close">
            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <div class="cm-preview" id="cm-d-preview">
        <svg viewBox="0 0 24 24" width="40" height="40" stroke="rgba(255,255,255,.15)" stroke-width="1.2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
    </div>

    <!-- Tabs -->
    <div class="cm-detail-tabs">
        <div class="cm-dtab active" onclick="cmDTab('details',this)">Details</div>
        <div class="cm-dtab" onclick="cmDTab('share',this)">Share</div>
        <div class="cm-dtab" onclick="cmDTab('activity',this)">Activity</div>
    </div>

    <!-- Tab: Details -->
    <div class="cm-dbody" id="cm-dtab-details">
        <!-- Meta rows -->
        <div style="margin-bottom:12px;border:1px solid #f4f4f5;border-radius:8px;overflow:hidden">
            <div class="cm-meta-row" style="padding:5px 10px"><span class="mk">URL</span><span class="mv"><span id="cm-m-url" style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span><button onclick="cmCopyUrl()" style="border:none;background:none;cursor:pointer;color:#a1a1aa;padding:0;flex-shrink:0" title="Copy"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button></span></div>
            <div class="cm-meta-row" style="padding:5px 10px"><span class="mk">Type</span><span class="mv" id="cm-m-type"></span></div>
            <div class="cm-meta-row" style="padding:5px 10px"><span class="mk">Size</span><span class="mv" id="cm-m-size"></span></div>
            <div class="cm-meta-row" id="cm-m-dim-row" style="padding:5px 10px;display:none"><span class="mk">Dimensions</span><span class="mv" id="cm-m-dim"></span></div>
            <div class="cm-meta-row" style="padding:5px 10px"><span class="mk">Uploaded</span><span class="mv" id="cm-m-date"></span></div>
            <div class="cm-meta-row" style="padding:5px 10px;border-bottom:none"><span class="mk">By</span><span class="mv" id="cm-m-author"></span></div>
        </div>

        <div class="cm-field"><label>Title</label><input type="text" id="cm-d-title" placeholder="File title…"></div>
        <div class="cm-field" id="cm-d-alt-wrap"><label>Alt Text</label><input type="text" id="cm-d-alt" placeholder="Describe image for accessibility…"></div>
        <div class="cm-field"><label>Caption</label><textarea id="cm-d-caption" rows="2" placeholder="Short caption…"></textarea></div>
        <div class="cm-field"><label>Description</label><textarea id="cm-d-desc" rows="2" placeholder="Longer description…"></textarea></div>
        <div class="cm-field" id="cm-d-doctype-wrap"><label>Document Type</label>
            <select id="cm-d-doctype"><option value="">— Select type —</option>
                <?php foreach ($all_doc_types as $dt): ?><option value="<?php echo esc_attr($dt); ?>"><?php echo esc_html($dt); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="cm-field"><label>Move to Folder</label><select id="cm-d-folder"><option value="">— No folder —</option></select></div>
        <div class="cm-field"><label>Link to Record</label>
            <select id="cm-d-record"><option value="">— None —</option>
                <optgroup label="Properties"><?php foreach ($listings as $l): ?><option value="property:<?php echo esc_attr($l['id']??''); ?>"><?php echo esc_html($l['title']??($l['property_name']??'Listing')); ?></option><?php endforeach; ?></optgroup>
                <optgroup label="Clients"><?php foreach ($clients as $c): ?><option value="client:<?php echo esc_attr($c['id']??''); ?>"><?php echo esc_html($c['client_name']??($c['name']??'Client')); ?></option><?php endforeach; ?></optgroup>
            </select>
        </div>
        <!-- Watermark -->
        <div id="cm-d-wm" style="display:none;padding-top:10px;border-top:1px solid #f4f4f5;margin-top:4px">
            <p style="font-size:10px;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.05em;margin:0 0 8px">Watermark</p>
            <div style="display:flex;gap:7px;margin-bottom:7px;flex-wrap:wrap">
                <select id="cm-wm-pos" class="cm-sel" style="flex:1;font-size:11px"><option value="bottom-right">Bottom Right</option><option value="bottom-left">Bottom Left</option><option value="center">Center</option></select>
                <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#71717a"><label>Opacity</label><input type="range" id="cm-wm-op" min="10" max="60" value="30" style="width:70px"><span id="cm-wm-op-v">30%</span></div>
            </div>
            <button onclick="cmAddWatermark()" class="cm-hbtn" style="width:100%;justify-content:center;font-size:11px">Add Watermark (New Copy)</button>
        </div>
        <div id="cm-d-restore" style="display:none;padding-top:8px">
            <button onclick="cmRestoreOriginal()" class="cm-hbtn" style="width:100%;justify-content:center;font-size:11px">↩ Restore Original</button>
        </div>
    </div>

    <!-- Tab: Share -->
    <div class="cm-dbody" id="cm-dtab-share" style="display:none">
        <p style="font-size:12px;font-weight:600;color:#3f3f46;margin:0 0 10px">Create Share Link</p>
        <div style="display:flex;gap:7px;margin-bottom:12px">
            <select id="cm-share-exp" class="cm-sel" style="flex:1"><option value="24h">24 hours</option><option value="7d" selected>7 days</option><option value="30d">30 days</option><option value="never">No expiry</option></select>
            <button onclick="cmCreateShareLink()" class="cm-hbtn primary" style="font-size:11px;padding:6px 12px">Create</button>
        </div>
        <div id="cm-share-list"><p style="font-size:11px;color:#a1a1aa">No active share links.</p></div>
    </div>

    <!-- Tab: Activity -->
    <div class="cm-dbody" id="cm-dtab-activity" style="display:none">
        <p style="font-size:10px;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.05em;margin:0 0 8px">Access Log</p>
        <div id="cm-act-list"><p style="font-size:11px;color:#a1a1aa">Loading…</p></div>
    </div>

    <div id="cm-detail-footer">
        <button onclick="cmSaveDetail()" id="cm-d-save" class="cm-dfbtn save">Save Changes</button>
        <button onclick="cmCloseDetail()" class="cm-dfbtn">Cancel</button>
        <button onclick="cmDeletePrompt(null)" class="cm-dfbtn del" style="flex:0;padding:8px 12px" title="Delete">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
        </button>
    </div>
</div>

<!-- ═══ IMAGE EDITOR MODAL ═══════════════════════════════════════════════════ -->
<div id="cm-editor-modal">
    <div id="cm-editor-card">
        <div id="cm-editor-card-header">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#a1a1aa" stroke-width="1.8" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            <h3>Image Editor</h3>
            <span id="cm-editor-fname" style="font-size:11px;color:#a1a1aa;font-weight:500"></span>
            <div style="flex:1"></div>
            <button onclick="cmCloseEditor()" style="width:28px;height:28px;border-radius:8px;border:1px solid #e4e4e7;background:#fff;cursor:pointer;color:#71717a;display:flex;align-items:center;justify-content:center">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div id="cm-editor-canvas">
            <img id="cm-editor-img" src="" alt="">
        </div>
        <div id="cm-editor-bar">
            <button class="cm-ebtn" onclick="cmEdit('rotate_left')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 .49-3.5"></path></svg>Rotate L
            </button>
            <button class="cm-ebtn" onclick="cmEdit('rotate_right')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-.49-3.5"></path></svg>Rotate R
            </button>
            <button class="cm-ebtn" onclick="cmEdit('flip_h')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="2" x2="12" y2="22" stroke-dasharray="3,3"></line><path d="M20 8l-6 4 6 4V8z M4 8l6 4-6 4V8z"></path></svg>Flip H
            </button>
            <button class="cm-ebtn" onclick="cmEdit('flip_v')">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><line x1="2" y1="12" x2="22" y2="12" stroke-dasharray="3,3"></line><path d="M8 20l4-6 4 6H8z M8 4l4 6 4-6H8z"></path></svg>Flip V
            </button>
            <div style="width:1px;height:20px;background:#e4e4e7;margin:0 4px"></div>
            <div class="cm-scale-group">
                <span style="font-size:11px;color:#71717a;font-weight:600">Scale:</span>
                <input type="number" id="cm-sc-w" placeholder="W px" class="cm-scale-input">
                <span style="font-size:11px;color:#a1a1aa">×</span>
                <input type="number" id="cm-sc-h" placeholder="H px" class="cm-scale-input">
                <button class="cm-ebtn" onclick="cmEditScale()">Apply</button>
            </div>
            <div style="margin-left:auto;display:flex;gap:6px">
                <button onclick="cmRestoreFromEditor()" class="cm-ebtn" style="color:#71717a">↩ Restore</button>
                <button onclick="cmCloseEditor()" class="cm-ebtn primary">Done</button>
            </div>
        </div>
    </div>
</div>
<style>
.cm-scale-input { width:64px; border:1px solid #e4e4e7; border-radius:6px; padding:5px 7px; font-size:11px; text-align:center; outline:none; }
.cm-scale-input:focus { border-color:#71717a; }
</style>

<!-- ═══ CONFIRM DELETE ════════════════════════════════════════════════════════ -->
<div id="cm-confirm-modal">
    <div id="cm-confirm-card">
        <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:16px">
            <div style="width:38px;height:38px;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="#dc2626" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
            </div>
            <div><h4 id="cm-confirm-title">Delete file?</h4><p id="cm-confirm-desc">This cannot be undone.</p></div>
        </div>
        <div class="btns">
            <button onclick="cmCancelConfirm()" class="cm-hbtn">Cancel</button>
            <button id="cm-confirm-ok" class="cm-hbtn" style="background:#dc2626;color:#fff;border-color:#dc2626">Delete Permanently</button>
        </div>
    </div>
</div>

<!-- ═══ NEW FOLDER DRAWER ════════════════════════════════════════════════════ -->
<div id="cm-folder-dlg" onclick="if(event.target===this)document.getElementById('cm-folder-dlg').classList.remove('open')">
    <div id="cm-folder-card">
        <div class="cm-drawer-header">
            <h3 style="margin:0;font-size:15px;font-weight:800;letter-spacing:-.02em">Create New Folder</h3>
            <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer p-1" style="background:none;border:none;color:#a1a1aa;cursor:pointer" onclick="document.getElementById('cm-folder-dlg').classList.remove('open')">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cm-drawer-body">
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Folder Name</label>
                <input type="text" id="cm-folder-name" placeholder="e.g. Exterior & Façade" maxlength="60" style="width:100%;border:1px solid #e4e4e7;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box">
                <input type="hidden" id="cm-folder-parent" value="0">
            </div>
        </div>
        <div class="cm-drawer-footer">
            <button onclick="document.getElementById('cm-folder-dlg').classList.remove('open')" class="cm-hbtn">Cancel</button>
            <button onclick="cmCreateFolder()" class="cm-hbtn primary">Create Folder</button>
        </div>
    </div>
</div>

<script>
(function(){
'use strict';
var CM = {
    view:'grid', files:[], folders:[], folder:null, selIds:[], bulk:false,
    page:1, pages:1, total:0, perPage:40,
    filters:{q:'',type:'all',date:'',author:''},
    sortBy:'date', sortDir:'DESC', active:null, searchT:null, confirmCb:null, upQ:0
};

// ── INIT ────────────────────────────────────────────────────────────────────
window.cmInit = function() {
    CM.view = localStorage.getItem('cora_media_view') || 'grid';
    cmSetView(CM.view, true);
    cmLoadFolders();
    cmLoadFiles();
    cmLoadStorage();
    cmLoadFilterOpts();
    cmSetupDrop();
    document.getElementById('cm-confirm-ok').addEventListener('click', function() {
        document.getElementById('cm-confirm-modal').classList.remove('open');
        if (CM.confirmCb) { CM.confirmCb(); CM.confirmCb = null; }
    });
    var op = document.getElementById('cm-wm-op');
    if (op) op.addEventListener('input', function() { document.getElementById('cm-wm-op-v').textContent = this.value + '%'; });
};

// ── VIEW TOGGLE ──────────────────────────────────────────────────────────────
window.cmSetView = function(v, silent) {
    CM.view = v; localStorage.setItem('cora_media_view', v);
    document.getElementById('cm-grid').style.display   = (v === 'grid') ? 'grid' : 'none';
    document.getElementById('cm-list').style.display   = (v === 'list') ? 'block' : 'none';
    document.getElementById('cm-btn-grid').className   = (v === 'grid') ? 'on' : '';
    document.getElementById('cm-btn-list').className   = (v === 'list') ? 'on' : '';
    if (!silent) cmRender();
};

// ── LOAD FILES ───────────────────────────────────────────────────────────────
window.cmLoadFiles = function() {
    document.getElementById('cm-loading').style.display = 'block';
    document.getElementById('cm-empty').style.display   = 'none';
    document.getElementById('cm-grid').innerHTML        = '';
    document.getElementById('cm-list-body').innerHTML   = '';
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_get', nonce: coraREData.ajaxNonce,
        paged: CM.page, per_page: CM.perPage,
        search: CM.filters.q, type: CM.filters.type, date: CM.filters.date, author: CM.filters.author,
        folder_id: CM.folder, orderby: CM.sortBy, order: CM.sortDir
    }, success: function(r) {
        document.getElementById('cm-loading').style.display = 'none';
        if (!r.success) { coraShowToast('Could not load media.'); return; }
        CM.files = r.data.files || []; CM.pages = r.data.total_pages || 1; CM.total = r.data.total || 0;
        if (!CM.files.length) document.getElementById('cm-empty').style.display = 'flex';
        else cmRender();
        cmUpdatePag();
    }, error: function() {
        document.getElementById('cm-loading').style.display = 'none';
        coraShowToast('Failed to load media library.');
    }});
};

// ── RENDER ───────────────────────────────────────────────────────────────────
window.cmRender = function() { CM.view === 'grid' ? cmRenderGrid() : cmRenderList(); };

window.cmRenderGrid = function() {
    var g = document.getElementById('cm-grid'); g.innerHTML = '';
    if (!CM.files.length) return;
    var frag = document.createDocumentFragment();
    CM.files.forEach(function(f) {
        var d = document.createElement('div');
        d.className = 'cm-cell' + (CM.selIds.indexOf(f.id) > -1 ? ' sel' : '');
        d.dataset.id = f.id;
        var chkClass = 'cm-chk' + (CM.selIds.indexOf(f.id) > -1 ? ' checked' : '');
        var thumb = f.type_category === 'image' && f.thumbnail
            ? '<img class="cm-thumb" src="' + f.thumbnail + '" alt="" loading="lazy">'
            : '<div class="cm-cell-icon">' + cmIcon(f.type_category, 28) + '</div>';
        d.innerHTML =
            '<div class="' + chkClass + '" data-id="' + f.id + '">' +
                '<svg viewBox="0 0 24 24" width="10" height="10" stroke="#fff" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
            '</div>' +
            thumb +
            '<span class="cm-cell-type">' + esc(f.type_category) + '</span>' +
            '<div class="cm-cell-name" title="' + esc(f.filename) + '">' + esc(f.title || f.filename) + '</div>';
        d.querySelector('.cm-chk').addEventListener('click', function(e) {
            e.stopPropagation(); cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1);
        });
        d.addEventListener('click', function() {
            if (CM.bulk) { cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1); }
            else { cmOpenDetail(f); }
        });
        frag.appendChild(d);
    });
    g.appendChild(frag);
    if (CM.bulk) g.classList.add('cm-bulk-mode'); else g.classList.remove('cm-bulk-mode');
};

window.cmRenderList = function() {
    var tb = document.getElementById('cm-list-body'); tb.innerHTML = '';
    document.getElementById('cm-list-all').style.display = CM.bulk ? '' : 'none';
    CM.files.forEach(function(f) {
        var tr = document.createElement('tr');
        tr.className = CM.selIds.indexOf(f.id) > -1 ? 'sel' : ''; tr.dataset.id = f.id;
        var lthumb = f.type_category === 'image' && f.thumbnail
            ? '<img class="cm-lthumb" src="' + f.thumbnail + '" alt="">'
            : '<div class="cm-lthumb" style="display:flex;align-items:center;justify-content:center;background:#f4f4f5">' + cmIcon(f.type_category, 14) + '</div>';
        tr.innerHTML =
            '<td><input type="checkbox" style="accent-color:#09090b;' + (CM.bulk ? '' : 'display:none') + '"' + (CM.selIds.indexOf(f.id) > -1 ? ' checked' : '') + '></td>' +
            '<td><div style="display:flex;align-items:center;gap:9px">' + lthumb +
                '<div><div style="font-weight:600;color:#18181b;font-size:12px">' + esc(f.title || f.filename) + '</div>' +
                '<div style="font-size:10px;color:#a1a1aa">' + esc(f.filename) + '</div></div></div></td>' +
            '<td>' + esc(f.author_name) + '</td>' +
            '<td>' + (f.folder_name ? '<span style="background:#f4f4f5;color:#52525b;border-radius:4px;padding:2px 7px;font-size:10px">' + esc(f.folder_name) + '</span>' : '<span style="color:#d4d4d8">—</span>') + '</td>' +
            '<td style="color:#71717a">' + esc(f.date_formatted) + '</td>' +
            '<td style="color:#71717a">' + esc(f.file_size_human) + '</td>' +
            '<td><span style="background:#f4f4f5;color:#52525b;border-radius:4px;padding:2px 7px;font-size:10px;font-weight:600;text-transform:capitalize">' + esc(f.type_category) + '</span></td>';
        tr.querySelector('input[type=checkbox]').addEventListener('change', function(e) { cmToggleSel(f.id, e.target.checked); e.stopPropagation(); });
        tr.addEventListener('click', function(e) { if (e.target.type === 'checkbox') return; if (CM.bulk) { cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1); } else { cmOpenDetail(f); } });
        tb.appendChild(tr);
    });
};

// ── DETAIL PANEL ─────────────────────────────────────────────────────────────
window.cmOpenDetail = function(f) {
    CM.active = f;
    var panel = document.getElementById('cm-detail');
    panel.classList.add('open');

    // Preview
    var prev = document.getElementById('cm-d-preview');
    if (f.type_category === 'image') {
        prev.innerHTML = '<img src="' + f.url + '?t=' + Date.now() + '" alt="" style="max-height:100%;max-width:100%;object-fit:contain">';
    } else if (f.type_category === 'video') {
        prev.innerHTML = '<video src="' + f.url + '" controls style="max-height:100%;max-width:100%"></video>';
    } else {
        prev.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:20px;color:#fff">' + cmIcon(f.type_category, 40) + '<a href="' + f.url + '" target="_blank" style="color:#fff;font-size:11px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.3);border-radius:6px;padding:5px 12px">Open ↗</a></div>';
    }

    // Toggle fields
    var isImg = f.type_category === 'image';
    document.getElementById('cm-d-edit-btn').style.display = isImg ? 'inline-flex' : 'none';
    document.getElementById('cm-d-alt-wrap').style.display = isImg ? 'block' : 'none';
    document.getElementById('cm-d-doctype-wrap').style.display = !isImg ? 'block' : 'none';
    document.getElementById('cm-d-wm').style.display = isImg ? 'block' : 'none';
    document.getElementById('cm-d-restore').style.display = f.has_original ? 'block' : 'none';

    // Meta
    document.getElementById('cm-d-name').textContent = f.filename;
    document.getElementById('cm-m-url').textContent  = f.url;
    document.getElementById('cm-m-url').title        = f.url;
    document.getElementById('cm-m-type').textContent = f.mime_type;
    document.getElementById('cm-m-size').textContent = f.file_size_human;
    document.getElementById('cm-m-date').textContent = f.date_formatted;
    document.getElementById('cm-m-author').textContent = f.author_name;
    var dr = document.getElementById('cm-m-dim-row');
    if (f.dimensions) { dr.style.display = 'flex'; document.getElementById('cm-m-dim').textContent = f.dimensions; }
    else dr.style.display = 'none';

    // Editable fields
    document.getElementById('cm-d-title').value   = f.title || '';
    document.getElementById('cm-d-alt').value     = f.alt || '';
    document.getElementById('cm-d-caption').value = f.caption || '';
    document.getElementById('cm-d-desc').value    = f.description || '';
    document.getElementById('cm-d-doctype').value = f.doc_type || '';

    // Folder dropdown
    var fs = document.getElementById('cm-d-folder');
    fs.innerHTML = '<option value="">— No folder —</option>';
    CM.folders.forEach(function(fo) {
        var o = document.createElement('option'); o.value = fo.id; o.textContent = fo.name;
        if (fo.id == f.folder_id) o.selected = true; fs.appendChild(o);
        (fo.children || []).forEach(function(s) {
            var so = document.createElement('option'); so.value = s.id; so.textContent = '  ↳ ' + s.name;
            if (s.id == f.folder_id) so.selected = true; fs.appendChild(so);
        });
    });

    // Record link
    var rs = document.getElementById('cm-d-record'); rs.selectedIndex = 0;
    if (f.linked_record) {
        var val = f.linked_record.type + ':' + f.linked_record.id;
        for (var i = 0; i < rs.options.length; i++) { if (rs.options[i].value === val) { rs.selectedIndex = i; break; } }
    }

    cmRenderShareLinks(f.share_links || []);
    cmDTab('details', document.querySelector('.cm-dtab'));
};

window.cmCloseDetail = function() {
    CM.active = null;
    document.getElementById('cm-detail').classList.remove('open');
};

window.cmDTab = function(tab, btn) {
    ['details','share','activity'].forEach(function(t) { document.getElementById('cm-dtab-' + t).style.display = 'none'; });
    document.querySelectorAll('.cm-dtab').forEach(function(b) { b.classList.remove('active'); });
    document.getElementById('cm-dtab-' + tab).style.display = 'block';
    if (btn) btn.classList.add('active');
    if (tab === 'activity' && CM.active) cmLoadActivity(CM.active.id);
};

// ── SAVE / DELETE ─────────────────────────────────────────────────────────────
window.cmSaveDetail = function() {
    if (!CM.active) return;
    var btn = document.getElementById('cm-d-save'); btn.textContent = 'Saving…'; btn.disabled = true;
    var rs = document.getElementById('cm-d-record'), lr = null;
    if (rs.value) { var p = rs.value.split(':'); lr = JSON.stringify({type:p[0],id:p[1]}); }
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action:'cora_media_library_update', nonce:coraREData.ajaxNonce,
        attachment_id: CM.active.id,
        title: document.getElementById('cm-d-title').value,
        alt: document.getElementById('cm-d-alt').value,
        caption: document.getElementById('cm-d-caption').value,
        description: document.getElementById('cm-d-desc').value,
        doc_type: document.getElementById('cm-d-doctype').value,
        folder_id: document.getElementById('cm-d-folder').value,
        linked_record: lr
    }, success: function(r) {
        btn.textContent = 'Save Changes'; btn.disabled = false;
        if (r.success) {
            coraShowToast('File details updated.');
            var idx = CM.files.findIndex(function(f) { return f.id === CM.active.id; });
            if (idx > -1) { CM.files[idx] = r.data.file; CM.active = r.data.file; }
            cmRender(); cmLoadFolders();
        } else coraShowToast(r.data && r.data.message ? r.data.message : 'Save failed.');
    }, error: function() { btn.textContent = 'Save Changes'; btn.disabled = false; coraShowToast('Network error.'); }});
};

window.cmDeletePrompt = function(ids) {
    var d = ids || (CM.active ? [CM.active.id] : []); if (!d.length) return;
    var nm = d.length === 1 ? ((CM.files.find(function(f){return f.id===d[0];})||{}).filename||'file') : d.length + ' files';
    document.getElementById('cm-confirm-title').textContent = 'Delete ' + nm + '?';
    document.getElementById('cm-confirm-desc').textContent  = 'This will permanently remove the file(s) and cannot be undone.';
    document.getElementById('cm-confirm-modal').classList.add('open');
    CM.confirmCb = function() { cmDoDelete(d); };
};
window.cmCancelConfirm = function() { document.getElementById('cm-confirm-modal').classList.remove('open'); CM.confirmCb = null; };
window.cmDoDelete = function(ids) {
    $.ajax({ url:coraREData.ajaxUrl, type:'POST', data:{action:'cora_media_library_delete',nonce:coraREData.ajaxNonce,ids:ids},
    success:function(r) {
        if (r.success) {
            coraShowToast(r.data.message||'Deleted.');
            if (CM.active && ids.indexOf(CM.active.id) > -1) cmCloseDetail();
            CM.selIds = CM.selIds.filter(function(x){return ids.indexOf(x)===-1;});
            cmLoadFiles(); cmLoadFolders(); cmLoadStorage();
        } else coraShowToast('Delete failed.');
    }});
};
window.cmCopyUrl = function() { if (!CM.active) return; navigator.clipboard.writeText(CM.active.url).then(function(){coraShowToast('URL copied.');}); };

// ── UPLOAD ────────────────────────────────────────────────────────────────────
window.cmHandleFiles = function(files) {
    if (!files || !files.length) return;
    Array.from(files).forEach(function(f) { cmUploadOne(f); });
    document.getElementById('cm-file-input').value = '';
};
window.cmUploadOne = function(file) {
    var MAX = 25 * 1024 * 1024;
    var prog = document.getElementById('cm-uprog'); prog.classList.add('on');
    var rid = 'up' + Date.now() + Math.random().toString(36).slice(2);
    var row = document.createElement('div'); row.className = 'cm-urow'; row.id = rid;
    row.innerHTML = '<span style="width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:0;font-weight:600;color:#3f3f46" title="' + esc(file.name) + '">' + esc(file.name) + '</span><div class="cm-ubar-w"><div class="cm-ubar" id="' + rid + '-b" style="width:0%"></div></div><span id="' + rid + '-l" style="white-space:nowrap;color:#a1a1aa;font-size:10px;width:30px;text-align:right">0%</span>';
    prog.appendChild(row); CM.upQ++;
    function setErr(msg) { var bar=document.getElementById(rid+'-b'); bar.classList.add('err'); bar.style.width='100%'; document.getElementById(rid+'-l').textContent='✕'; row.title=msg; done(); }
    function done() { CM.upQ--; if(CM.upQ<=0) setTimeout(function(){prog.classList.remove('on');prog.innerHTML='';},2500); }
    if (file.size > MAX) { setErr('File exceeds 25 MB.'); return; }
    var fd = new FormData(); fd.append('action','cora_media_library_upload'); fd.append('nonce',coraREData.ajaxNonce); fd.append('file',file);
    if (CM.folder && CM.folder > 0) fd.append('folder_id', CM.folder);
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', function(e) { if(e.lengthComputable){var p=Math.round(e.loaded/e.total*100);document.getElementById(rid+'-b').style.width=p+'%';document.getElementById(rid+'-l').textContent=p+'%';} });
    xhr.addEventListener('load', function() {
        done(); try {
            var r = JSON.parse(xhr.responseText);
            if (r.success) {
                document.getElementById(rid+'-b').style.width='100%'; document.getElementById(rid+'-l').textContent='✓'; document.getElementById(rid+'-l').style.color='#22c55e';
                coraShowToast(r.data.message || 'Uploaded.');
                CM.files.unshift(r.data.file||{}); document.getElementById('cm-empty').style.display='none'; cmRender();
                cmLoadFolders(); cmLoadStorage();
            } else setErr((r.data&&r.data.message)||'Upload failed.');
        } catch(e) { setErr('Server error.'); }
    });
    xhr.addEventListener('error', function() { setErr('Network error.'); });
    xhr.open('POST', coraREData.ajaxUrl, true); xhr.send(fd);
};

// ── DRAG & DROP ───────────────────────────────────────────────────────────────
window.cmSetupDrop = function() {
    var ov = document.getElementById('cm-drop'), ct = 0;
    document.addEventListener('dragenter', function(e) { if(e.dataTransfer.types.indexOf('Files')>-1){ct++;ov.classList.add('on');} });
    document.addEventListener('dragleave', function() { ct--;if(ct<=0){ct=0;ov.classList.remove('on');} });
    document.addEventListener('dragover', function(e) { e.preventDefault(); });
    document.addEventListener('drop', function(e) { e.preventDefault(); ct=0; ov.classList.remove('on'); if(e.dataTransfer.files.length) cmHandleFiles(e.dataTransfer.files); });
};

// ── FOLDERS ───────────────────────────────────────────────────────────────────
window.cmLoadFolders = function() {
    $.ajax({ url:coraREData.ajaxUrl, type:'POST', data:{action:'cora_media_library_get_folders',nonce:coraREData.ajaxNonce},
    success:function(r) { 
        if(!r.success) return; 
        CM.folders = r.data.folders||[]; 
        CM.total_count = r.data.total_count || 0;
        CM.unorganised_count = r.data.unorganised_count || 0;
        cmRenderFolderTabs(); 
        cmPopulateBulkFolderSelect(); 
    }});
};
window.cmRenderFolderTabs = function() {
    var container = document.getElementById('cm-folder-tabs'); container.innerHTML = '';
    
    // Set All Media count badge from backend data
    document.getElementById('cm-ftab-all-ct').textContent = CM.total_count || 0;
    
    // Set Unorganised count badge from backend data
    var unorgCt = document.getElementById('cm-ftab-none-ct');
    if (unorgCt) {
        unorgCt.textContent = CM.unorganised_count || 0;
    }

    CM.folders.forEach(function(folder) {
        var tab = document.createElement('div'); tab.className = 'cm-ftab' + (CM.folder == folder.id ? ' active' : '');
        tab.dataset.folderId = folder.id;
        tab.innerHTML = esc(folder.name) + ' <span class="ct">' + folder.count + '</span>';
        tab.addEventListener('click', function() { cmSelectFolder(folder.id, tab); });
        container.appendChild(tab);
        (folder.children || []).forEach(function(s) {
            var st = document.createElement('div'); st.className = 'cm-ftab' + (CM.folder == s.id ? ' active' : '');
            st.dataset.folderId = s.id;
            st.innerHTML = '↳ ' + esc(s.name) + ' <span class="ct">' + s.count + '</span>';
            st.addEventListener('click', function() { cmSelectFolder(s.id, st); });
            container.appendChild(st);
        });
    });
};
window.cmPopulateBulkFolderSelect = function() {
    var bs = document.getElementById('cm-bulk-folder'); bs.innerHTML = '<option value="">Move to folder…</option>';
    CM.folders.forEach(function(f) { bs.innerHTML += '<option value="'+f.id+'">'+esc(f.name)+'</option>'; (f.children||[]).forEach(function(s){bs.innerHTML+='<option value="'+s.id+'">  ↳ '+esc(s.name)+'</option>';});});
};
window.cmSelectFolder = function(id, el) {
    CM.folder = id; CM.page = 1;
    document.querySelectorAll('.cm-ftab').forEach(function(t){t.classList.remove('active');});
    el.classList.add('active'); cmLoadFiles();
};
window.cmPromptFolder = function() {
    document.getElementById('cm-folder-name').value = '';
    document.getElementById('cm-folder-parent').value = '0';
    document.getElementById('cm-folder-dlg').classList.add('open');
    setTimeout(function(){document.getElementById('cm-folder-name').focus();},80);
};
window.cmCreateFolder = function() {
    var name = document.getElementById('cm-folder-name').value.trim();
    if (!name) { coraShowToast('Enter a folder name.'); return; }
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_create_folder',nonce:coraREData.ajaxNonce,name:name,parent_id:0},
    success:function(r){document.getElementById('cm-folder-dlg').classList.remove('open'); if(r.success){coraShowToast(r.data.message||'Folder created.');cmLoadFolders();}else coraShowToast('Could not create folder.');}});
};

// ── FILTERS / SORT / PAGINATION ───────────────────────────────────────────────
window.cmOnSearch = function(v) { clearTimeout(CM.searchT); CM.searchT=setTimeout(function(){CM.filters.q=v;CM.page=1;cmLoadFiles();},350); };
window.cmFilter = function() {
    CM.filters.type   = document.getElementById('cm-ft').value;
    CM.filters.date   = document.getElementById('cm-fd').value;
    var a = document.getElementById('cm-fa'); CM.filters.author = a ? a.value : '';
    CM.page = 1; cmLoadFiles();
};
window.cmSort = function(col) { if(CM.sortBy===col) CM.sortDir=CM.sortDir==='ASC'?'DESC':'ASC'; else{CM.sortBy=col;CM.sortDir='DESC';} cmLoadFiles(); };
window.cmUpdatePag = function() {
    var pag = document.getElementById('cm-pag');
    if (CM.pages <= 1) { pag.style.display='none'; return; }
    pag.style.display = 'flex';
    document.getElementById('cm-pag-info').textContent = 'Page ' + CM.page + ' of ' + CM.pages + ' (' + CM.total + ' files)';
    document.getElementById('cm-prev').disabled = CM.page <= 1;
    document.getElementById('cm-next').disabled = CM.page >= CM.pages;
};
window.cmPage = function(d) { var n=CM.page+d; if(n<1||n>CM.pages) return; CM.page=n; cmLoadFiles(); };
window.cmLoadFilterOpts = function() {
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_months',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;var s=document.getElementById('cm-fd');(r.data.months||[]).forEach(function(m){s.innerHTML+='<option value="'+esc(m.value)+'">'+esc(m.label)+'</option>';});}});
    var as=document.getElementById('cm-fa');
    if(as) $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_uploaders',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;(r.data.uploaders||[]).forEach(function(u){as.innerHTML+='<option value="'+u.id+'">'+esc(u.name)+'</option>';});}});
};

// ── BULK ──────────────────────────────────────────────────────────────────────
window.cmToggleBulk = function() {
    CM.bulk = !CM.bulk; CM.selIds = [];
    document.getElementById('cm-bulk-btn').textContent = CM.bulk ? 'Cancel' : 'Select';
    var bb = document.getElementById('cm-bulk-bar');
    bb.style.display = CM.bulk ? 'flex' : 'none'; bb.className = 'cm-bulk-bar' + (CM.bulk ? ' on' : '');
    cmRender(); cmBulkCt();
};
window.cmToggleSel = function(id, on) {
    if (on) { if(CM.selIds.indexOf(id)===-1) CM.selIds.push(id); } else { CM.selIds=CM.selIds.filter(function(x){return x!==id;}); }
    var gc=document.querySelector('.cm-cell[data-id="'+id+'"]');
    if(gc){ gc.classList.toggle('sel',on); var chk=gc.querySelector('.cm-chk'); if(chk) chk.classList.toggle('checked',on); }
    var lc=document.querySelector('#cm-list-body tr[data-id="'+id+'"]'); if(lc){ lc.classList.toggle('sel',on); var cb=lc.querySelector('input[type=checkbox]'); if(cb) cb.checked=on; }
    cmBulkCt();
};
window.cmListAll = function(ch) { CM.files.forEach(function(f){cmToggleSel(f.id,ch);}); };
window.cmBulkCt = function() { document.getElementById('cm-bulk-ct').textContent = CM.selIds.length + ' selected'; };
window.cmBulkMove = function() {
    var fid=document.getElementById('cm-bulk-folder').value; if(!fid||!CM.selIds.length){coraShowToast('Select files and a destination folder.');return;}
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_move',nonce:coraREData.ajaxNonce,attachment_ids:CM.selIds,folder_id:fid},
    success:function(r){if(r.success){coraShowToast(r.data.message);CM.selIds=[];cmLoadFiles();cmLoadFolders();}else coraShowToast('Move failed.');}});
};
window.cmBulkDelete = function() { if(!CM.selIds.length){coraShowToast('Select files first.');return;} cmDeletePrompt(CM.selIds.slice()); };

// ── SHARE LINKS ───────────────────────────────────────────────────────────────
window.cmRenderShareLinks = function(links) {
    var el=document.getElementById('cm-share-list');
    if(!links||!links.length){el.innerHTML='<p style="font-size:11px;color:#a1a1aa">No active share links.</p>';return;}
    el.innerHTML=links.map(function(l){return '<div class="cm-share-row"><div class="su"><strong>'+esc(l.url)+'</strong><span class="se">'+esc(l.expiry_label||'')+'</span></div><button onclick="navigator.clipboard.writeText(\''+esc(l.url)+'\');coraShowToast(\'Link copied.\')" class="cm-hbtn" style="font-size:10px;padding:3px 8px">Copy</button><button onclick="cmRevokeLink(\''+esc(l.token)+'\')" class="cm-hbtn" style="font-size:10px;padding:3px 8px;color:#dc2626;border-color:#fecaca">Revoke</button></div>';}).join('');
};
window.cmCreateShareLink = function() {
    if(!CM.active)return;
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_create_share',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id,expiry:document.getElementById('cm-share-exp').value},
    success:function(r){if(r.success){CM.active.share_links=CM.active.share_links||[];CM.active.share_links.push(r.data.link);cmRenderShareLinks(CM.active.share_links);navigator.clipboard.writeText(r.data.link.url).then(function(){coraShowToast('Link created & copied.');});}else coraShowToast('Could not create link.');}});
};
window.cmRevokeLink = function(token) {
    if(!CM.active)return;
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_revoke_share',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id,token:token},
    success:function(r){if(r.success){CM.active.share_links=(CM.active.share_links||[]).filter(function(l){return l.token!==token;});cmRenderShareLinks(CM.active.share_links);coraShowToast('Link revoked.');}else coraShowToast('Revoke failed.');}});
};

// ── ACTIVITY ──────────────────────────────────────────────────────────────────
window.cmLoadActivity = function(id) {
    var el=document.getElementById('cm-act-list'); el.innerHTML='<p style="font-size:11px;color:#a1a1aa">Loading…</p>';
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_activity',nonce:coraREData.ajaxNonce,attachment_id:id},
    success:function(r){if(!r.success||!r.data.log.length){el.innerHTML='<p style="font-size:11px;color:#a1a1aa">No activity recorded.</p>';return;}
    el.innerHTML=r.data.log.slice(0,30).map(function(e){return '<div class="cm-act-row"><div class="cm-act-dot"></div><div><div style="font-weight:600;color:#3f3f46">'+esc(e.user_name)+'</div><div style="color:#a1a1aa;font-size:10px">'+esc(e.time_formatted)+' · '+esc(e.via)+'</div></div></div>';}).join('');}});
};

// ── IMAGE EDITOR ──────────────────────────────────────────────────────────────
window.cmOpenEditor = function() {
    if(!CM.active||CM.active.type_category!=='image')return;
    document.getElementById('cm-editor-modal').classList.add('open');
    var img = document.getElementById('cm-editor-img');
    img.src=CM.active.url+'?t='+Date.now();
    document.getElementById('cm-editor-fname').textContent=CM.active.filename;
    
    document.getElementById('cm-sc-w').value = '';
    document.getElementById('cm-sc-h').value = '';
    
    img.onload = function() {
        if (img.naturalWidth && img.naturalHeight) {
            window.CM_editor_aspect = img.naturalWidth / img.naturalHeight;
            document.getElementById('cm-sc-w').placeholder = img.naturalWidth;
            document.getElementById('cm-sc-h').placeholder = img.naturalHeight;
        }
    };
};

// Add input event listeners for auto-scaling aspect ratio
document.addEventListener('DOMContentLoaded', function() {
    var scW = document.getElementById('cm-sc-w');
    var scH = document.getElementById('cm-sc-h');
    if (scW && scH) {
        scW.addEventListener('input', function() {
            var w = parseInt(this.value);
            if (w && window.CM_editor_aspect) {
                scH.value = Math.round(w / window.CM_editor_aspect);
            } else if (!this.value) {
                scH.value = '';
            }
        });
        scH.addEventListener('input', function() {
            var h = parseInt(this.value);
            if (h && window.CM_editor_aspect) {
                scW.value = Math.round(h * window.CM_editor_aspect);
            } else if (!this.value) {
                scW.value = '';
            }
        });
    }
});

window.cmCloseEditor = function() {
    document.getElementById('cm-editor-modal').classList.remove('open');
    if(CM.active) {
        document.getElementById('cm-d-preview').innerHTML='<img src="'+CM.active.url+'?t='+Date.now()+'" alt="" style="max-height:100%;max-width:100%;object-fit:contain">';
        
        if (CM.active.edited) {
            CM.active.edited = false;
            coraShowToast('Finalizing image edits...');
            $.ajax({
                url: coraREData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cora_media_library_regenerate_thumbnails',
                    nonce: coraREData.ajaxNonce,
                    attachment_id: CM.active.id
                },
                success: function(r) {
                    if (r.success) {
                        coraShowToast('Thumbnails updated successfully.');
                        cmLoadFiles();
                    } else {
                        coraShowToast('Failed to update thumbnails.');
                    }
                }
            });
        }
    }
};
window.cmEdit = function(op) {
    if(!CM.active)return;
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_image_edit',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id,operation:op},
    success:function(r){if(r.success){
        var img = document.getElementById('cm-editor-img');
        img.src=r.data.url;
        CM.active.has_original=true;
        CM.active.edited=true;
        document.getElementById('cm-d-restore').style.display='block';
        coraShowToast('Image updated.');
    }else coraShowToast('Edit failed.');}});
};
window.cmEditScale = function() {
    var w=parseInt(document.getElementById('cm-sc-w').value),h=parseInt(document.getElementById('cm-sc-h').value);
    if(!w&&!h){coraShowToast('Enter width or height.');return;}
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_image_edit',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id,operation:'scale',width:w||null,height:h||null},
    success:function(r){if(r.success){
        var img = document.getElementById('cm-editor-img');
        img.src=r.data.url;
        CM.active.edited=true;
        coraShowToast('Image scaled.');
    }else coraShowToast('Scale failed.');}});
};
window.cmRestoreOriginal = function() {
    if(!CM.active)return;
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_restore',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id},
    success:function(r){if(r.success){coraShowToast('Original restored.');document.getElementById('cm-d-preview').innerHTML='<img src="'+r.data.url+'" style="max-height:100%;max-width:100%;object-fit:contain">';document.getElementById('cm-d-restore').style.display='none';cmLoadFiles();}else coraShowToast('Restore failed.');}});
};
window.cmRestoreFromEditor = function() { cmRestoreOriginal(); cmCloseEditor(); };
window.cmAddWatermark = function() {
    if(!CM.active)return;
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_watermark',nonce:coraREData.ajaxNonce,attachment_id:CM.active.id,position:document.getElementById('cm-wm-pos').value,opacity:document.getElementById('cm-wm-op').value},
    success:function(r){if(r.success){coraShowToast('Watermarked copy created.');cmLoadFiles();}else coraShowToast('Watermark failed.');}});
};

// ── STORAGE ───────────────────────────────────────────────────────────────────
window.cmLoadStorage = function() {
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_storage',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;var d=r.data,p=Math.min(100,d.percent_used);
        document.getElementById('cm-storage-lbl').textContent=d.total_human+' / '+d.limit_human;
        document.getElementById('cm-storage-pct').textContent=p+'%';
        document.getElementById('cm-storage-bar').style.width=p+'%';
        document.getElementById('cm-storage-wrap').style.display='flex';
    }});
};

// ── HELPERS ───────────────────────────────────────────────────────────────────
function esc(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
function cmIcon(type,sz){sz=sz||22;var ic={image:'<svg viewBox="0 0 24 24" width="'+sz+'" height="'+sz+'" stroke="#a1a1aa" stroke-width="1.5" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>',video:'<svg viewBox="0 0 24 24" width="'+sz+'" height="'+sz+'" stroke="#a1a1aa" stroke-width="1.5" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>',audio:'<svg viewBox="0 0 24 24" width="'+sz+'" height="'+sz+'" stroke="#a1a1aa" stroke-width="1.5" fill="none"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',document:'<svg viewBox="0 0 24 24" width="'+sz+'" height="'+sz+'" stroke="#a1a1aa" stroke-width="1.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>'};return ic[type]||ic.document;}

// ── BOOT ──────────────────────────────────────────────────────────────────────
if(typeof coraREData!=='undefined'){
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',cmInit);
    else cmInit();
}
})();
</script>
