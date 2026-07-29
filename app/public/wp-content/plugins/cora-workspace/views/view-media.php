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
#cora-page-media { margin: 0 !important; padding: 0 !important; width: 100% !important; overflow: visible !important; height: auto !important; }

/* ─── Reset & Clean Layout Canvas ─────────────────────────────────────────── */
#cm-root {
    display: flex;
    flex-direction: column;
    width: 100%;
    min-height: calc(100vh - 80px);
    height: auto !important;
    overflow: visible !important;
    background: #fff;
    position: relative;
    margin: 0;
}

/* ─── Top header bar ─────────────────────────────────────────────────────── */
#cm-header { flex-shrink:0; border-bottom:1px solid #e4e4e7; background:#fff; }
#cm-header-top { display:flex; align-items:center; gap:10px; padding:11px 18px; flex-wrap:wrap; }
.cm-h-title { font-size:15px; font-weight:800; color:#09090b; letter-spacing:-.02em; }
.cm-h-subtitle { font-size:11px; color:#a1a1aa; font-weight:500; }
.cm-h-sep { flex:1; }
.cm-h-storage-label { font-size:10.5px; color:inherit; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
.cm-sbar-w { height:3px; width:45px; background:#e4e4e7; border-radius:99px; overflow:hidden; }
.cm-sbar   { height:100%; border-radius:99px; transition:width .4s; }

#cm-storage-wrap { padding: 3px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; cursor: pointer; border: 1px solid #e4e4e7; background: #fff; display: inline-flex; align-items: center; gap: 6px; user-select: none; }
.cm-storage-ring-svg { transform: rotate(-90deg); flex-shrink: 0; }
.cm-ring-fill { transition: stroke-dasharray .4s ease, stroke .3s ease; }
.cm-hbtn { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #e4e4e7; background:#fff; color:#3f3f46; transition:all .12s; white-space:nowrap; }
.cm-hbtn:hover { background:#f4f4f5; border-color:#d4d4d8; }
.cm-hbtn.primary { background:#09090b; color:#fff; border-color:#09090b; }
.cm-hbtn.primary:hover { background:#27272a; }
.cm-toggle { display:flex; border:1px solid #e4e4e7; border-radius:8px; overflow:hidden; }
.cm-toggle button { padding:5px 9px; background:#fff; border:none; cursor:pointer; color:#a1a1aa; display:flex; align-items:center; transition:background .12s; }
.cm-toggle button.on { background:#09090b; color:#fff; }
.cm-toggle button + button { border-left:1px solid #e4e4e7; }

/* ─── Dedicated Folders Section ────────────────────────────────────────── */
#cm-folders-section {
    padding: 16px 20px 14px;
    border-bottom: 1px solid #f4f4f5;
    background: #fff;
    flex-shrink: 0;
}
.cm-folders-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.cm-folders-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 800;
    color: #09090b;
    letter-spacing: -.02em;
}
#cm-folders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}
.cm-fcard {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #fafafa;
    border: 1px solid #e4e4e7;
    border-radius: 10px;
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
    min-width: 0;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
}
.cm-fcard:hover {
    background: #fff;
    border-color: #a1a1aa;
    box-shadow: 0 4px 12px rgba(0,0,0,.05);
}
.cm-fcard.active {
    background: #f4f4f5;
    border: 1px solid #d4d4d8;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.cm-fcard-chk {
    accent-color: #09090b;
    cursor: pointer;
    width: 15px;
    height: 15px;
    margin-right: 6px;
    flex-shrink: 0;
}
.cm-fcard-toggle {
    border: 1px dashed #d4d4d8;
    color: #52525b;
}
.cm-fcard-left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
    overflow: hidden;
}
.cm-fcard-icon {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: #f4f4f5;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #3f3f46;
    flex-shrink: 0;
}
.cm-fcard-info {
    min-width: 0;
    flex: 1;
    overflow: hidden;
}
.cm-fcard-name {
    font-size: 12px;
    font-weight: 700;
    color: #09090b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    width: 100%;
}
.cm-fcard-ct {
    font-size: 10px;
    color: #71717a;
    font-weight: 500;
}
.cm-fcard-opt {
    color: #a1a1aa;
    padding: 4px 6px;
    border-radius: 6px;
    transition: color .12s, background .12s;
    font-weight: 700;
}
.cm-fcard-opt:hover {
    color: #09090b;
    background: #f4f4f5;
}
.cm-ctx-item {
    padding: 8px 10px;
    font-size: 12px;
    font-weight: 500;
    color: #27272a;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background .12s, color .12s;
    user-select: none;
}
.cm-ctx-item:hover {
    background: #f4f4f5;
    color: #09090b;
}
.cm-ctx-item.del {
    color: #ef4444;
}
.cm-ctx-item.del:hover {
    background: #fef2f2;
    color: #dc2626;
}

/* ─── Breadcrumbs & Sorting Row ────────────────────────────────────────── */
#cm-breadcrumbs-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px 4px;
    background: #fff;
    flex-wrap: wrap;
    gap: 10px;
    flex-shrink: 0;
}
.cm-bc-path {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #09090b;
}
.cm-bc-sep { color: #a1a1aa; font-weight: 400; }
.cm-bc-active { color: #09090b; }
.cm-bc-badge {
    font-size: 11px;
    font-weight: 600;
    background: #f4f4f5;
    color: #71717a;
    padding: 2px 8px;
    border-radius: 999px;
}
.cm-sort-group {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #71717a;
    font-weight: 600;
}

/* ─── Toolbar (search + filters) ────────────────────────────────────────── */
#cm-toolbar { display:flex; align-items:center; gap:8px; padding:8px 20px 14px; border-bottom:1px solid #f4f4f5; flex-wrap:wrap; flex-shrink:0; background:#fff; }
.cm-search-wrap { position:relative; flex:1; min-width:160px; max-width:320px; }
.cm-search-wrap svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); pointer-events:none; color:#a1a1aa; }
.cm-search { width:100%; border:1px solid #e4e4e7; border-radius:8px; padding:6px 10px 6px 30px; font-size:12px; background:#fafafa; outline:none; box-sizing:border-box; }
.cm-search:focus { border-color:#71717a; background:#fff; }
.cm-sel { border:1px solid #e4e4e7; border-radius:8px; padding:6px 10px; font-size:11px; font-weight:500; color:#3f3f46; background:#fff; outline:none; cursor:pointer; transition:all .12s ease; height:32px; box-sizing:border-box; }
.cm-sel:hover { border-color:#a1a1aa; color:#18181b; }
.cm-sel:focus { border-color:#09090b; }
.cm-bulk-bar { display:none; align-items:center; gap:8px; padding:6px 12px; background:#18181b; color:#fff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,.15); transition:all .15s ease; }
.cm-bulk-bar.on { display:flex; }
.cm-bulk-bar select.cm-sel { background:#27272a; border-color:#3f3f46; color:#f4f4f5; }
.cm-bulk-bar .cm-hbtn { background:#27272a; border-color:#3f3f46; color:#f4f4f5; }
.cm-bulk-bar .cm-hbtn:hover { background:#3f3f46; color:#fff; }
#cm-mobile-bottom-bar { display: none; }
.cm-cell:hover .cm-chk, .cm-chk.checked { display:flex; }

/* ─── Media Card Metadata Footer ────────────────────────────────────────── */
.cm-cell-meta {
    padding: 9px 12px;
    background: #fff;
    border-top: 1px solid #f4f4f5;
}
.cm-cell-sub {
    font-size: 10px;
    color: #71717a;
    font-family: ui-monospace, monospace;
    margin-top: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cm-cell-opt {
    color: #a1a1aa;
    padding: 1px 4px;
    border-radius: 4px;
    font-weight: 700;
}
.cm-cell-opt:hover {
    color: #09090b;
    background: #f4f4f5;
}

/* ─── Upload progress ────────────────────────────────────────────────────── */
#cm-uprog { display:none; flex-direction:column; max-height:150px; overflow-y:auto; background:#fafafa; border-bottom:1px solid #e4e4e7; flex-shrink:0; }
#cm-uprog.on { display:flex; }
.cm-urow { display:flex; align-items:center; gap:9px; padding:7px 18px; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-ubar-w { flex:1; height:3px; background:#e4e4e7; border-radius:99px; overflow:hidden; }
.cm-ubar   { height:100%; background:#18181b; border-radius:99px; transition:width .3s; }
.cm-ubar.err { background:#ef4444; }

/* ─── Grid canvas ────────────────────────────────────────────────────────── */
#cm-canvas {
    flex: 1;
    overflow: visible !important;
    height: auto !important;
    padding: 20px 22px;
}
#cm-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:16px; }
.cm-cell { position:relative; border:1px solid #e4e4e7; border-radius:12px; overflow:hidden; cursor:pointer; transition:all .18s ease; background:#fafafa; }
.cm-cell:hover { box-shadow:0 8px 24px rgba(0,0,0,.08); border-color:#a1a1aa; transform:translateY(-1px); }
.cm-cell.sel { border-color:#09090b; box-shadow:0 0 0 2px #09090b; }
.cm-thumb { width:100%; aspect-ratio:16/10; object-fit:cover; display:block; }
.cm-cell-icon { width:100%; aspect-ratio:16/10; display:flex; align-items:center; justify-content:center; background:#f4f4f5; }
.cm-cell-name { padding:10px 12px; font-size:12px; font-weight:600; color:#18181b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; border-top:1px solid #f4f4f5; background:#fff; }
.cm-cell-type { position:absolute; top:9px; right:9px; background:rgba(0,0,0,.65); backdrop-filter:blur(4px); color:#fff; font-size:9.5px; font-weight:700; padding:3px 8px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
.cm-chk { position:absolute; top:9px; left:9px; width:18px; height:18px; border-radius:6px; border:2px solid rgba(255,255,255,.9); background:rgba(0,0,0,.25); display:none; align-items:center; justify-content:center; cursor:pointer; transition:background .1s; }
.cm-bulk-mode .cm-chk { display:flex; }
.cm-chk.checked { background:#09090b; border-color:#09090b; }
.cm-chk svg { display:none; }
.cm-chk.checked svg { display:block; }

/* ─── List view ──────────────────────────────────────────────────────────── */
#cm-list { display:none; border:1px solid #e4e4e7; border-radius:12px; overflow:hidden; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.cm-ltable { width:100%; border-collapse:collapse; font-size:12px; }
.cm-ltable thead th { padding:10px 14px; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#71717a; border-bottom:1px solid #e4e4e7; background:#fafafa; cursor:pointer; user-select:none; }
.cm-ltable thead th:hover { color:#09090b; }
.cm-ltable tbody tr { border-bottom:1px solid #f4f4f5; cursor:pointer; transition:background .12s ease; }
.cm-ltable tbody tr:last-child { border-bottom:none; }
.cm-ltable tbody tr:hover { background:#fafafa; }
.cm-ltable tbody tr.sel { background:#f4f4f5; }
.cm-ltable td { padding:10px 14px; color:#3f3f46; vertical-align:middle; }
.cm-lthumb { width:36px; height:36px; border-radius:8px; object-fit:cover; flex-shrink:0; }

/* ─── Empty / Loading ────────────────────────────────────────────────────── */
#cm-empty { display:none; flex-direction:column; align-items:center; justify-content:center; height:100%; min-height:280px; gap:10px; text-align:center; padding:40px; }
#cm-loading { display:none; padding:16px 18px; }

/* ─── Centered Modal Dialog Pop-ups ───────────────────────────────────────── */
#cm-folder-dlg, #cm-gallery-dlg, #cm-folder-settings-dlg, #cm-detail {
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(9, 9, 11, 0.6); backdrop-filter: blur(4px);
    display: flex; align-items: center; justify-content: center;
    padding: 20px; opacity: 0; pointer-events: none;
    transition: opacity .2s ease-in-out;
}
#cm-folder-dlg.open, #cm-gallery-dlg.open, #cm-folder-settings-dlg.open, #cm-detail.open {
    opacity: 1; pointer-events: auto;
}
#cm-folder-card, #cm-gallery-card, #cm-folder-settings-card {
    background: #fff; border: 1px solid #e4e4e7; border-radius: 16px;
    width: 100%; max-width: 500px; max-height: 90vh;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
    display: flex; flex-direction: column; overflow: hidden;
    transform: scale(0.95); transition: transform .2s ease-in-out;
}
#cm-detail-card {
    background: #fff; border: 1px solid #e4e4e7; border-radius: 16px;
    width: 100%; max-width: 680px; max-height: 90vh;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
    display: flex; flex-direction: column; overflow: hidden;
    transform: scale(0.95); transition: transform .2s ease-in-out;
}
#cm-folder-dlg.open #cm-folder-card,
#cm-gallery-dlg.open #cm-gallery-card,
#cm-folder-settings-dlg.open #cm-folder-settings-card,
#cm-detail.open #cm-detail-card {
    transform: scale(1) !important;
}
#cm-detail-header { flex-shrink:0; padding:14px 18px; border-bottom:1px solid #e4e4e7; display:flex; align-items:center; gap:8px; background:#fff; }
#cm-detail-header h3 { flex:1; font-size:14px; font-weight:700; color:#09090b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0; }
.cm-preview { flex-shrink:0; height:260px; background:#09090b; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.cm-preview img,.cm-preview video { max-height:100%; max-width:100%; object-fit:contain; }
.cm-detail-tabs { display:flex; border-bottom:1px solid #e4e4e7; flex-shrink:0; background:#fff; }
.cm-dtab { padding:8px 14px; font-size:11px; font-weight:600; color:#a1a1aa; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:color .12s; }
.cm-dtab.active { color:#09090b; border-bottom-color:#09090b; }
.cm-dbody { flex:1; overflow-y:auto; padding:16px; }
.cm-field { margin-bottom:12px; }
.cm-field label { display:block; font-size:10px; font-weight:700; color:#a1a1aa; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px; }
.cm-field input,.cm-field textarea,.cm-field select { width:100%; border:1px solid #e4e4e7; border-radius:7px; padding:6px 9px; font-size:12px; color:#18181b; background:#fff; outline:none; transition:border .15s; box-sizing:border-box; resize:vertical; }
.cm-field input:focus,.cm-field textarea:focus,.cm-field select:focus { border-color:#71717a; }
.cm-meta-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px solid #f4f4f5; font-size:11px; }
.cm-meta-row .mk { color:#a1a1aa; font-size:10px; font-weight:600; text-transform:uppercase; }
.cm-meta-row .mv { color:#3f3f46; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:flex; align-items:center; gap:4px; }
#cm-detail-footer { flex-shrink:0; padding:12px 16px; border-top:1px solid #e4e4e7; background:#fafafa; display:flex; gap:6px; }
.cm-dfbtn { flex:1; padding:8px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:1px solid #e4e4e7; background:#fff; color:#3f3f46; transition:all .12s; }
.cm-dfbtn:hover { background:#f4f4f5; }
.cm-dfbtn.save { background:#09090b; color:#fff; border-color:#09090b; }
.cm-dfbtn.save:hover { background:#27272a; }
.cm-dfbtn.del { color:#dc2626; border-color:#fecaca; }

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
    position:fixed; inset:0; z-index:99999;
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
#cm-confirm-modal { position:fixed; inset:0; z-index:100000; background:rgba(9,9,11,.5); backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
#cm-confirm-modal.open { display:flex; }
#cm-confirm-card { background:#fff; border:1px solid #e4e4e7; border-radius:14px; padding:24px; max-width:420px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,.15); }
#cm-confirm-card h4 { margin:0 0 6px; font-size:14px; font-weight:700; }
#cm-confirm-card p { margin:0 0 20px; font-size:12px; color:#71717a; }
#cm-confirm-card .btns { display:flex; gap:8px; justify-content:flex-end; }

/* ─── Drawer Overlays ─────────────────────────────────────────────────── */
#cm-gallery-dlg.open #cm-gallery-card,
#cm-folder-settings-dlg.open #cm-folder-settings-card { transform:translateX(0) !important; }
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
.cora-dark-theme #cm-mobile-bottom-bar { background: rgba(17,17,19,0.95); border-color: #27272a; }
.cora-dark-theme #cm-header-top #cm-storage-wrap { background: #1c1c1e; border-color: #27272a; color: #fafafa; }
.cora-dark-theme #cm-storage-analytics-card { background: #18181b; border-color: #27272a; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
.cora-dark-theme #cm-storage-analytics-card span { color: #fafafa; }
.cora-dark-theme #cm-sa-free { color: #fafafa !important; }
.cora-dark-theme .cm-ring-bg { stroke: #27272a; }

/* ─── Mobile Filter Drawer & Chips ─────────────────────────────────────── */
#cm-btn-mobile-filter { display: none; }
#cm-mobile-filter-dlg {
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(9, 9, 11, 0.6); backdrop-filter: blur(4px);
    display: flex; align-items: flex-end; justify-content: center;
    padding: 0; opacity: 0; pointer-events: none;
    transition: opacity .25s ease-in-out;
}
#cm-mobile-filter-dlg.open {
    opacity: 1; pointer-events: auto;
}
#cm-mobile-filter-card {
    background: #fff; border-top-left-radius: 20px; border-top-right-radius: 20px;
    width: 100%; max-width: 600px; max-height: 85vh;
    box-shadow: 0 -10px 40px rgba(0,0,0,.2);
    display: flex; flex-direction: column; overflow: hidden;
    transform: translateY(100%); transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
}
#cm-mobile-filter-dlg.open #cm-mobile-filter-card {
    transform: translateY(0);
}
.cora-dark-theme #cm-mobile-filter-card {
    background: #111113; border-color: #27272a; color: #fafafa;
}

.cm-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    background: #f4f4f5;
    border: 1px solid #e4e4e7;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: #18181b;
    cursor: pointer;
    transition: all .12s ease;
}
.cm-chip:hover {
    background: #e4e4e7;
    color: #09090b;
}
.cm-chip-close {
    font-size: 10px;
    color: #71717a;
    font-weight: 700;
    margin-left: 2px;
}
.cora-dark-theme .cm-chip {
    background: #27272a;
    border-color: #3f3f46;
    color: #f4f4f5;
}

.cm-lmobile-sub { display: none; }
.cm-lmain-sub-desktop { display: block; }

/* ─── Responsive ─────────────────────────────────────────────────────────── */
@media (max-width: 768px) {
    #cm-toolbar {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px 10px;
        width: 100%;
        box-sizing: border-box;
        flex-wrap: nowrap;
    }
    .cm-search-wrap {
        flex: 1 1 auto;
        min-width: 0;
        width: auto;
    }
    #cm-btn-mobile-filter {
        display: inline-flex !important;
        flex-shrink: 0;
        padding: 6px 10px;
        font-size: 11px;
    }
    #cm-toolbar > button[onclick*="cmResetFilters"] {
        display: inline-flex !important;
        flex-shrink: 0;
        padding: 6px 10px;
        font-size: 11px;
    }
    #cm-toolbar select.cm-sel {
        display: none !important;
    }

    /* List Table Mobile Touch Card View */
    .cm-ltable, .cm-ltable tbody {
        display: block;
        width: 100%;
    }
    .cm-ltable thead {
        display: none !important;
    }
    .cm-ltable tbody tr {
        display: flex;
        flex-direction: column;
        padding: 12px 42px 12px 12px;
        border-bottom: 1px solid #f4f4f5;
        position: relative;
        box-sizing: border-box;
        width: 100%;
    }
    .cm-ltable td.cm-lcell-author,
    .cm-ltable td.cm-lcell-folder,
    .cm-ltable td.cm-lcell-date,
    .cm-ltable td.cm-lcell-size,
    .cm-ltable td.cm-lcell-type,
    .cm-ltable td.cm-lcell-chk {
        display: none !important;
    }
    .cm-bulk-mode .cm-ltable td.cm-lcell-chk {
        display: block !important;
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
    }
    .cm-ltable td.cm-lcell-main {
        display: block;
        padding: 0;
        border: none;
        width: 100%;
    }
    .cm-ltable td.cm-lcell-act {
        display: block;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        border: none;
    }
    .cm-lmobile-sub {
        display: flex !important;
        font-size: 10.5px;
        color: #71717a;
        font-family: ui-monospace, monospace;
        margin-top: 3px;
        align-items: center;
        gap: 4px;
    }
    .cm-lmain-sub-desktop {
        display: none !important;
    }
    #cm-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px 6px;
        gap: 8px;
        width: 100%;
        box-sizing: border-box;
        flex-wrap: nowrap;
    }
    #cm-header-top #cm-bulk-btn {
        display: inline-flex !important;
    }
    #cm-header-top button.primary {
        display: none !important;
    }
    .cm-h-title {
        font-size: 14px;
    }
    .cm-h-subtitle {
        display: none;
    }

    #cm-folders-section {
        padding: 12px 14px;
        width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    #cm-folders-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px;
        width: 100%;
        box-sizing: border-box;
        overflow: hidden;
    }
    .cm-fcard {
        padding: 8px 10px;
        font-size: 11px;
    }
    .cm-fcard-name {
        font-size: 11px;
    }
    .cm-fcard-opt {
        padding: 4px 6px;
        margin-left: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    #cm-mobile-bottom-bar {
        display: flex;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 900;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        border-top: 1px solid #e4e4e7;
        padding: 10px 16px;
        justify-content: space-between;
        gap: 10px;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
    }
    #cm-mobile-bottom-bar .cm-hbtn {
        flex: 1;
        justify-content: center;
    }

    #cm-bulk-bar.on {
        position: fixed;
        bottom: 70px;
        left: 12px;
        right: 12px;
        width: auto;
        z-index: 99999;
        background: #18181b;
        color: #f4f4f5;
        border: 1px solid #3f3f46;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        overflow: hidden;
        flex-wrap: wrap;
        box-sizing: border-box;
    }
    #cm-bulk-bar .cm-bulk-row-1 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    #cm-bulk-bar .cm-bulk-row-2 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 6px;
        width: 100%;
    }
    #cm-bulk-bar .cm-bulk-row-2 button,
    #cm-bulk-bar .cm-bulk-row-2 select {
        width: 100%;
        justify-content: center;
        padding: 6px 4px;
        font-size: 10.5px;
    }

    #cm-root, #cm-canvas {
        padding-bottom: 90px !important;
    }
}
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
            <div class="cm-h-storage cm-storage-healthy" style="display:none;position:relative" id="cm-storage-wrap" onmouseenter="cmShowStorageAnalytics(true)" onmouseleave="cmShowStorageAnalytics(false)" onclick="cmToggleStorageAnalytics(event)">
                <svg class="cm-storage-ring-svg" width="28" height="28" viewBox="0 0 36 36">
                    <path class="cm-ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="#e4e4e7" stroke-width="3" fill="none"/>
                    <path id="cm-ring-fill" class="cm-ring-fill" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="#10b981" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                </svg>
                <span id="cm-storage-pct" style="font-size:11px;font-weight:700">14.6%</span>

                <!-- Hover / Click Detailed Analytics Popover Card -->
                <div id="cm-storage-analytics-card" style="display:none;position:absolute;top:calc(100% + 8px);right:0;width:240px;background:#fff;border:1px solid #e4e4e7;border-radius:12px;padding:12px 14px;box-shadow:0 10px 30px rgba(0,0,0,0.12);z-index:9999;pointer-events:none">
                    <div style="font-size:11px;font-weight:700;color:#18181b;margin-bottom:4px;display:flex;justify-content:space-between">
                        <span>Workspace Storage</span>
                        <span id="cm-sa-pct-text" style="color:#10b981">14.6% Used</span>
                    </div>
                    <div style="font-size:10px;color:#71717a;margin-bottom:8px" id="cm-sa-human">— used of 5 GB</div>
                    <div style="height:6px;width:100%;background:#f4f4f5;border-radius:99px;overflow:hidden;margin-bottom:8px">
                        <div id="cm-sa-bar" style="height:100%;width:14.6%;background:#10b981;border-radius:99px"></div>
                    </div>
                    <div style="font-size:10px;color:#52525b;display:flex;justify-content:space-between">
                        <span>Available:</span>
                        <strong id="cm-sa-free" style="color:#09090b">—</strong>
                    </div>
                </div>
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

    <!-- ─── TOOLBAR (search + filters) ─────────────────────────── -->
    <div id="cm-toolbar">
        <div class="cm-search-wrap">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="cm-search" class="cm-search" placeholder="Search files…" oninput="cmOnSearch(this.value)">
        </div>
        <button id="cm-btn-mobile-filter" class="cm-hbtn" onclick="cmOpenMobileFilters()"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg> Filters <span id="cm-filter-count-badge" class="cm-bc-badge" style="display:none">0</span></button>
        <select id="cm-ft" class="cm-sel" onchange="cmFilter()">
            <option value="all">All Types</option>
            <option value="image">Images</option>
            <option value="video">Videos</option>
            <option value="audio">Audio</option>
            <option value="document">Documents</option>
        </select>
        <select id="cm-fculling" class="cm-sel" onchange="cmFilter()">
            <option value="">All Culling</option>
            <option value="starred">Starred (4+ Stars)</option>
            <option value="green">Approved</option>
            <option value="yellow">In Review</option>
            <option value="red">Rejected</option>
        </select>
        <select id="cm-fd" class="cm-sel" onchange="cmFilter()"><option value="">All Dates</option></select>
        <select id="cm-fdoctype" class="cm-sel" onchange="cmFilter()">
            <option value="">Resolution / Document</option>
            <?php foreach ($all_doc_types as $dt): ?><option value="<?php echo esc_attr($dt); ?>"><?php echo esc_html($dt); ?></option><?php endforeach; ?>
        </select>
        <?php if ($is_admin): ?><select id="cm-fa" class="cm-sel" onchange="cmFilter()"><option value="">All Uploaders</option></select><?php endif; ?>

        <button onclick="cmResetFilters()" class="cm-hbtn" style="font-size:11px;padding:5px 9px">
            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 .49-3.5"></path></svg>
            Reset
        </button>

        <div id="cm-active-filter-chips" style="display:none;width:100%;flex-wrap:wrap;gap:6px;padding-top:6px"></div>

        <!-- Bulk action bar -->
        <div id="cm-bulk-bar" class="cm-bulk-bar">
            <div class="cm-bulk-row-1" style="display:flex;align-items:center;gap:8px">
                <span id="cm-bulk-ct" style="font-size:11px;font-weight:700;color:#f4f4f5">0 selected</span>
                <button onclick="cmListAll(true)" class="cm-hbtn" style="font-size:11px;padding:4px 9px">Select All</button>
                <button onclick="cmToggleBulk()" class="cm-hbtn" style="font-size:11px;padding:4px 9px;margin-left:auto">✕ Close</button>
            </div>
            <div class="cm-bulk-row-2" style="display:flex;align-items:center;gap:6px">
                <select id="cm-bulk-folder" class="cm-sel" style="font-size:11px"><option value="">Move to folder…</option></select>
                <button onclick="cmBulkMove()" class="cm-hbtn" style="font-size:11px;padding:4px 9px">Move</button>
                <div style="position:relative;display:inline-block">
                    <button onclick="cmToggleBulkColorMenu(event)" class="cm-hbtn" style="font-size:11px;padding:4px 9px">Folder Color ▾</button>
                    <div id="cm-bulk-color-menu" style="display:none;position:absolute;bottom:100%;left:0;margin-bottom:6px;background:#18181b;border:1px solid #3f3f46;border-radius:10px;padding:8px;gap:6px;box-shadow:0 6px 16px rgba(0,0,0,.3);z-index:900;align-items:center">
                        <div onclick="cmBulkColorFolders('#3b82f6')" style="width:18px;height:18px;border-radius:50%;background:#3b82f6;cursor:pointer" title="Blue"></div>
                        <div onclick="cmBulkColorFolders('#ef4444')" style="width:18px;height:18px;border-radius:50%;background:#ef4444;cursor:pointer" title="Red"></div>
                        <div onclick="cmBulkColorFolders('#f59e0b')" style="width:18px;height:18px;border-radius:50%;background:#f59e0b;cursor:pointer" title="Orange"></div>
                        <div onclick="cmBulkColorFolders('#10b981')" style="width:18px;height:18px;border-radius:50%;background:#10b981;cursor:pointer" title="Green"></div>
                        <div onclick="cmBulkColorFolders('#8b5cf6')" style="width:18px;height:18px;border-radius:50%;background:#8b5cf6;cursor:pointer" title="Purple"></div>
                        <div onclick="cmBulkColorFolders('#ec4899')" style="width:18px;height:18px;border-radius:50%;background:#ec4899;cursor:pointer" title="Pink"></div>
                        <div onclick="cmBulkColorFolders('#64748b')" style="width:18px;height:18px;border-radius:50%;background:#64748b;cursor:pointer" title="Slate"></div>
                        <div onclick="cmBulkColorFolders('#09090b')" style="width:18px;height:18px;border-radius:50%;background:#09090b;cursor:pointer;border:1px solid #3f3f46" title="Dark"></div>
                    </div>
                </div>
                <button onclick="cmBulkAddGallery()" class="cm-hbtn" style="font-size:11px;padding:4px 9px">Gallery</button>
                <button onclick="cmBulkZip()" class="cm-hbtn" style="font-size:11px;padding:4px 9px">ZIP</button>
                <button onclick="cmBulkDelete()" class="cm-hbtn" style="font-size:11px;padding:4px 9px;color:#f87171;border-color:#7f1d1d">Delete</button>
            </div>
        </div>
    </div>

    <!-- ─── DEDICATED FOLDERS SECTION ─────────────────────────── -->
    <div id="cm-folders-section">
        <div class="cm-folders-header">
            <div class="cm-folders-title">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                Folders
            </div>
            <button class="cm-hbtn" onclick="cmPromptFolder(null)">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                New folder
            </button>
        </div>
        <!-- Dynamic Folder Cards Grid -->
        <div id="cm-folders-grid"></div>
    </div>

    <!-- ─── BREADCRUMBS & SORTING ROW ─────────────────────────── -->
    <div id="cm-breadcrumbs-bar">
        <div class="cm-bc-path">
            <span onclick="cmSelectFolder(null)" style="cursor:pointer">All Media</span>
            <span id="cm-bc-subpath" style="display:inline-flex;align-items:center;gap:6px"></span>
            <span class="cm-bc-badge" id="cm-bc-count">0 files</span>
        </div>
        <div class="cm-sort-group">
            <label>Sort by:</label>
            <select id="cm-sort-select" class="cm-sel" onchange="cmOnSortChange(this.value)">
                <option value="date-DESC">Date added (Newest)</option>
                <option value="date-ASC">Date added (Oldest)</option>
                <option value="title-ASC">Name (A - Z)</option>
                <option value="title-DESC">Name (Z - A)</option>
                <option value="size-DESC">File Size (Largest)</option>
            </select>
        </div>
    </div>

    <!-- Upload progress -->
    <div id="cm-uprog"></div>

    <!-- ─── MAIN CANVAS ──────────────────────────────────────────── -->
    <div id="cm-canvas">
        <!-- Skeleton -->
        <div id="cm-loading" style="display:none">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px">
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
                    <th style="width:40px;text-align:right"></th>
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

    <!-- ─── MOBILE BOTTOM BAR ─────────────────────────────────── -->
    <div id="cm-mobile-bottom-bar">
        <button class="cm-hbtn" onclick="cmPromptFolder(null)">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Folder
        </button>
        <button class="cm-hbtn primary" onclick="document.getElementById('cm-file-input').click()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Upload
        </button>
    </div>
</div><!-- /cm-root -->

<!-- ═══ DETAIL POP-UP MODAL ══════════════════════════════════════════════════ -->
<div id="cm-detail" onclick="if(event.target===this)cmCloseDetail()">
  <div id="cm-detail-card">
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

        <!-- Culling & Photography Tags -->
        <div class="cm-field">
            <label>Culling Star Rating</label>
            <div id="cm-d-stars" style="display:flex;gap:4px;padding:4px 0">
                <!-- 5 clickable stars injected dynamically by JS -->
            </div>
        </div>
        <div class="cm-field">
            <label>Color Label</label>
            <div id="cm-d-labels" style="display:flex;gap:6px;padding:4px 0">
                <!-- Color dots injected dynamically by JS -->
            </div>
        </div>
        <div class="cm-field">
            <label>Shoot / Collection Tags</label>
            <input type="text" id="cm-d-shoot-tags" placeholder="e.g. Meera Wedding 2025, Pre-Shoot..." onchange="cmSaveShootTags(this.value)">
        </div>

        <div class="cm-field"><label>Title (Max 200 chars)</label><input type="text" id="cm-d-title" placeholder="File title…" maxlength="200"></div>
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

        <!-- AI Auto-Tag Placeholder -->
        <div style="margin-top:12px;padding:12px;border:1px solid #e4e4e7;border-radius:8px;background:#fafafa;opacity:0.7">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
                <span style="font-size:11px;font-weight:700;color:#18181b">Smart Auto-Tagging</span>
                <span style="font-size:9px;font-weight:700;background:#e4e4e7;color:#52525b;padding:1px 6px;border-radius:4px;text-transform:uppercase">Coming Soon</span>
            </div>
            <p style="font-size:10px;color:#71717a;margin:0">AI vision auto-detects subjects, lighting, and duplicates across shoot sessions.</p>
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
</div>

<!-- ═══ FLOATING FOLDER COMMAND CONTEXT MENU ═════════════════════════════════ -->
<div id="cm-folder-ctx-menu" class="cm-ctx-menu" style="display:none;position:fixed;z-index:99999;background:#fff;border:1px solid #e4e4e7;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.15);padding:6px;width:210px">
    <div class="cm-ctx-item" onclick="cmFolderCtxAction('settings')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        Folder Settings & Rename
    </div>
    <div class="cm-ctx-item" onclick="cmFolderCtxAction('subfolder')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Subfolder Inside
    </div>
    <div class="cm-ctx-item" onclick="cmFolderCtxAction('share_link')">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
        Copy Public Share Link
    </div>
    <div class="cm-ctx-sep" style="height:1px;background:#f4f4f5;margin:4px 0"></div>
    <div class="cm-ctx-item del" onclick="cmFolderCtxAction('delete')" style="color:#ef4444">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
        Delete Folder
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

<!-- ═══ MOBILE FILTER DRAWER MODAL ═══════════════════════════════════════════ -->
<div id="cm-mobile-filter-dlg" onclick="if(event.target===this)cmCloseMobileFilters()">
    <div id="cm-mobile-filter-card">
        <div class="cm-drawer-header">
            <h3 style="margin:0;font-size:15px;font-weight:800;letter-spacing:-.02em;display:flex;align-items:center;gap:6px">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter Media
            </h3>
            <button style="background:none;border:none;color:#a1a1aa;cursor:pointer;padding:4px" onclick="cmCloseMobileFilters()">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cm-drawer-body" style="padding:20px;display:flex;flex-direction:column;gap:14px">
            <div class="cm-field">
                <label>Media Type</label>
                <select id="cm-m-ft" class="cm-sel" style="width:100%">
                    <option value="all">All Types</option>
                    <option value="image">Images</option>
                    <option value="video">Videos</option>
                    <option value="audio">Audio</option>
                    <option value="document">Documents</option>
                </select>
            </div>
            <div class="cm-field">
                <label>Culling Status</label>
                <select id="cm-m-fculling" class="cm-sel" style="width:100%">
                    <option value="">All Culling</option>
                    <option value="starred">Starred (4+ Stars)</option>
                    <option value="green">Approved</option>
                    <option value="yellow">In Review</option>
                    <option value="red">Rejected</option>
                </select>
            </div>
            <div class="cm-field">
                <label>Date Added</label>
                <select id="cm-m-fd" class="cm-sel" style="width:100%">
                    <option value="">All Dates</option>
                </select>
            </div>
            <div class="cm-field">
                <label>Document / Resolution Type</label>
                <select id="cm-m-fdoctype" class="cm-sel" style="width:100%">
                    <option value="">Resolution / Document</option>
                    <?php foreach ($all_doc_types as $dt): ?><option value="<?php echo esc_attr($dt); ?>"><?php echo esc_html($dt); ?></option><?php endforeach; ?>
                </select>
            </div>
            <?php if ($is_admin): ?>
            <div class="cm-field">
                <label>Uploader</label>
                <select id="cm-m-fa" class="cm-sel" style="width:100%">
                    <option value="">All Uploaders</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <div class="cm-drawer-footer" style="display:flex;gap:10px;justify-content:space-between">
            <button onclick="cmResetMobileFilters()" class="cm-hbtn" style="flex:1;justify-content:center">Reset All</button>
            <button onclick="cmApplyMobileFilters()" class="cm-hbtn primary" style="flex:1;justify-content:center">Apply Filters</button>
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
            <!-- Folder Name -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Folder Name *</label>
                <input type="text" id="cm-folder-name" placeholder="e.g. Rahul & Neha Pre-Shoot..." maxlength="60" style="width:100%;border:1px solid #e4e4e7;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;box-sizing:border-box">
            </div>

            <!-- Parent Folder -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Parent Folder</label>
                <select id="cm-folder-parent" style="width:100%;border:1px solid #e4e4e7;border-radius:8px;padding:8px 12px;font-size:12px;outline:none;background:#fff;box-sizing:border-box">
                    <option value="0">Root (Main Workspace)</option>
                </select>
            </div>

            <!-- Description / Notes -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Notes / Description (Optional)</label>
                <textarea id="cm-folder-desc" placeholder="Add internal notes or client instructions..." rows="3" style="width:100%;border:1px solid #e4e4e7;border-radius:8px;padding:8px 12px;font-size:12px;outline:none;box-sizing:border-box;resize:vertical"></textarea>
            </div>

            <!-- Folder Color Tag -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Folder Color Tag</label>
                <input type="hidden" id="cm-folder-color" value="#3b82f6">
                <div id="cm-folder-color-swatches" style="display:flex;gap:8px;align-items:center;padding:4px 0"></div>
            </div>

            <!-- Auto Share Link -->
            <div style="margin-bottom:18px;border-top:1px solid #f4f4f5;padding-top:14px">
                <label style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:#27272a;cursor:pointer">
                    <input type="checkbox" id="cm-folder-autoshare" style="width:16px;height:16px;accent-color:#09090b">
                    Auto-generate public share link for this folder
                </label>
            </div>
        </div>
        <div class="cm-drawer-footer">
            <button onclick="document.getElementById('cm-folder-dlg').classList.remove('open')" class="cm-hbtn">Cancel</button>
            <button onclick="cmCreateFolder()" class="cm-hbtn primary">Create Folder</button>
        </div>
    </div>
</div>

<!-- ═══ CLIENT GALLERY DRAWER ════════════════════════════════════════════════ -->
<div id="cm-gallery-dlg" onclick="if(event.target===this)document.getElementById('cm-gallery-dlg').classList.remove('open')">
    <div id="cm-gallery-card" style="background:#fff;border-left:1px solid #e4e4e7;height:100%;width:100%;max-width:420px;box-shadow:-10px 0 30px rgba(0,0,0,.15);display:flex;flex-direction:column;transform:translateX(100%);transition:transform .25s ease">
        <div class="cm-drawer-header">
            <h3 style="margin:0;font-size:15px;font-weight:800;letter-spacing:-.02em">Add to Client Gallery</h3>
            <button style="background:none;border:none;color:#a1a1aa;cursor:pointer;padding:4px" onclick="document.getElementById('cm-gallery-dlg').classList.remove('open')">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cm-drawer-body" style="padding:20px;flex:1;overflow-y:auto">
            <p style="font-size:11px;color:#71717a;margin:0 0 12px">Select an existing gallery or create a new gallery to add selected files.</p>
            <div id="cm-gallery-list" style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
                <!-- Injected via JS -->
            </div>
            <div style="border-top:1px solid #f4f4f5;padding-top:14px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Create New Gallery</label>
                <div style="display:flex;gap:6px">
                    <input type="text" id="cm-new-gallery-name" placeholder="Gallery title (e.g. Rahul & Neha Shoot)..." style="flex:1;border:1px solid #e4e4e7;border-radius:8px;padding:7px 10px;font-size:12px;outline:none">
                    <button onclick="cmCreateGalleryFromPicker()" class="cm-hbtn primary" style="font-size:11px">Create</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ FOLDER SETTINGS & SHARING DRAWER ══════════════════════════════════ -->
<div id="cm-folder-settings-dlg" onclick="if(event.target===this)document.getElementById('cm-folder-settings-dlg').classList.remove('open')">
    <div id="cm-folder-settings-card">
        <div class="cm-drawer-header">
            <h3 style="margin:0;font-size:15px;font-weight:800;letter-spacing:-.02em" id="cm-fs-title">Folder Settings</h3>
            <button style="background:none;border:none;color:#a1a1aa;cursor:pointer;padding:4px" onclick="document.getElementById('cm-folder-settings-dlg').classList.remove('open')">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cm-drawer-body" style="padding:20px;flex:1;overflow-y:auto">
            <input type="hidden" id="cm-fs-id">
            
            <!-- Folder Rename -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Folder Name</label>
                <div style="display:flex;gap:6px">
                    <input type="text" id="cm-fs-name-input" style="flex:1;border:1px solid #e4e4e7;border-radius:8px;padding:7px 10px;font-size:13px;outline:none">
                    <button onclick="cmSaveFolderSettings()" class="cm-hbtn primary" style="font-size:11px">Save</button>
                </div>
            </div>

            <!-- Folder Color Tag -->
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:6px">Folder Color Tag</label>
                <input type="hidden" id="cm-fs-color" value="#3b82f6">
                <div id="cm-fs-color-swatches" style="display:flex;gap:8px;align-items:center;padding:4px 0"></div>
            </div>

            <!-- Folder Share Link -->
            <div style="margin-bottom:18px;border-top:1px solid #f4f4f5;padding-top:16px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:4px">Public Share Link</label>
                <p style="font-size:11px;color:#71717a;margin:0 0 8px">Generate a link to share all media assets in this folder with clients.</p>
                <div style="display:flex;gap:6px;margin-bottom:8px">
                    <input type="text" id="cm-fs-url-input" readonly placeholder="Click Generate to create share link..." style="flex:1;border:1px solid #e4e4e7;border-radius:8px;padding:7px 10px;font-size:11px;background:#fafafa;outline:none;color:#52525b">
                    <button onclick="cmGenerateFolderShare()" class="cm-hbtn" style="font-size:11px">Generate</button>
                    <button onclick="cmCopyFolderShareUrl()" class="cm-hbtn primary" style="font-size:11px">Copy</button>
                </div>
            </div>

            <!-- Email Folder Share -->
            <div style="margin-bottom:18px;border-top:1px solid #f4f4f5;padding-top:16px">
                <label style="display:block;font-size:11px;font-weight:700;color:#27272a;margin-bottom:4px">Email Folder to Client</label>
                <p style="font-size:11px;color:#71717a;margin:0 0 8px">Send an email invitation directly to your client with the secure folder access link.</p>
                <div style="display:flex;gap:6px">
                    <input type="email" id="cm-fs-email-input" placeholder="client@example.com" style="flex:1;border:1px solid #e4e4e7;border-radius:8px;padding:7px 10px;font-size:11px;outline:none">
                    <button onclick="cmEmailFolderShare()" class="cm-hbtn primary" style="font-size:11px">Send Email</button>
                </div>
            </div>

            <!-- Folder Actions -->
            <div style="border-top:1px solid #f4f4f5;padding-top:16px">
                <label style="display:block;font-size:11px;font-weight:700;color:#dc2626;margin-bottom:6px">Danger Zone</label>
                <button onclick="cmDeleteFolderFromSettings()" class="cm-hbtn" style="width:100%;justify-content:center;color:#dc2626;border-color:#fecaca;font-size:12px">Delete Folder</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ RIGHT-CLICK CONTEXT MENU ═════════════════════════════════════════════ -->
<div id="cm-ctx-menu" style="display:none;position:fixed;z-index:99999;width:200px;background:#fff;border:1px solid #e4e4e7;border-radius:10px;box-shadow:0 12px 30px rgba(0,0,0,.15);padding:6px 0;font-size:12px">
    <div style="padding:6px 12px;border-bottom:1px solid #f4f4f5;font-weight:700;font-size:10px;color:#a1a1aa;text-transform:uppercase;letter-spacing:.05em" id="cm-ctx-title">Media Options</div>
    <div onclick="cmCtxAction('detail')" style="padding:6px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;color:#27272a" class="cm-ctx-item"><span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></span> Open Details</div>
    <div onclick="cmCtxAction('copy')" style="padding:6px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;color:#27272a" class="cm-ctx-item"><span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg></span> Copy Share Link</div>
    <div onclick="cmCtxAction('gallery')" style="padding:6px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;color:#27272a" class="cm-ctx-item"><span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></span> Add to Gallery</div>
    <div style="height:1px;background:#f4f4f5;margin:4px 0"></div>
    <div style="padding:6px 12px;font-size:10px;font-weight:700;color:#a1a1aa;text-transform:uppercase">Star Rating</div>
    <div style="padding:4px 12px;display:flex;gap:4px" id="cm-ctx-stars"></div>
    <div style="padding:6px 12px;font-size:10px;font-weight:700;color:#a1a1aa;text-transform:uppercase">Color Label</div>
    <div style="padding:4px 12px;display:flex;gap:6px" id="cm-ctx-labels"></div>
    <div style="height:1px;background:#f4f4f5;margin:4px 0"></div>
    <div onclick="cmCtxAction('download')" style="padding:6px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;color:#27272a" class="cm-ctx-item"><span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg></span> Download File</div>
    <div onclick="cmCtxAction('delete')" style="padding:6px 12px;cursor:pointer;display:flex;align-items:center;gap:8px;color:#dc2626" class="cm-ctx-item"><span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span> Delete Permanently</div>
</div>

<script>
(function(){
'use strict';
var CM = {
    view:'grid', files:[], folders:[], galleries:[], activeGallery:null, folder:null, selIds:[], selFolderIds:[], bulk:false,
    page:1, pages:1, total:0, perPage:40,
    filters:{q:'',type:'all',culling:'',date:'',author:''},
    sortBy:'date', sortDir:'DESC', active:null, ctxFile:null, searchT:null, confirmCb:null, upQ:0,
    foldersExpanded: false, storage_bytes: 0, limit_bytes: 5 * 1024 * 1024 * 1024
};
window.CM = CM;

var LABEL_MAP = {
    none:   { hex: '#d4d4d8', label: 'None' },
    red:    { hex: '#ef4444', label: 'Rejected' },
    yellow: { hex: '#facc15', label: 'In Review' },
    green:  { hex: '#10b981', label: 'Approved' },
    blue:   { hex: '#3b82f6', label: 'Selected' },
    purple: { hex: '#a855f7', label: 'Exported' }
};

// ── INIT ────────────────────────────────────────────────────────────────────
window.cmInit = function() {
    CM.view = localStorage.getItem('cora_media_view') || 'grid';
    if (window.innerWidth <= 768) {
        CM.view = 'grid';
    }
    cmSetView(CM.view, true);
    cmLoadFolders();
    cmLoadGalleries();
    cmLoadFiles();
    cmLoadStorage();
    cmLoadFilterOpts();
    cmSetupDrop();
    document.getElementById('cm-confirm-ok').addEventListener('click', function() {
        document.getElementById('cm-confirm-modal').classList.remove('open');
        if (CM.confirmCb) { CM.confirmCb(); CM.confirmCb = null; }
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#cm-ctx-menu')) cmHideContextMenu();
        if (!e.target.closest('#cm-folder-ctx-menu')) cmHideFolderContextMenu();
    });
    var op = document.getElementById('cm-wm-op');
    if (op) op.addEventListener('input', function() { document.getElementById('cm-wm-op-v').textContent = this.value + '%'; });
};

// ── GALLERIES ───────────────────────────────────────────────────────────────
window.cmLoadGalleries = function() {
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_get_galleries', nonce: coraREData.ajaxNonce
    }, success: function(r) {
        if (r.success) {
            CM.galleries = r.data || [];
            cmRenderGalleries();
        }
    }});
};

window.cmRenderGalleries = function() {
    var el = document.getElementById('cm-gallery-list');
    if (el) {
        if (!CM.galleries.length) {
            el.innerHTML = '<p style="font-size:11px;color:#a1a1aa">No galleries created yet.</p>';
        } else {
            el.innerHTML = CM.galleries.map(function(g) {
                var count = (g.file_ids || []).length;
                return '<div onclick="cmAddToGallery(\'' + g.id + '\')" style="padding:8px 12px;border:1px solid #e4e4e7;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;background:#fff">' +
                    '<div><strong style="display:block;font-size:12px;color:#18181b">' + esc(g.name) + '</strong><span style="font-size:10px;color:#71717a">' + count + ' items</span></div>' +
                    '<span style="font-size:12px;color:#a1a1aa">+ Add</span>' +
                '</div>';
            }).join('');
        }
    }

    var tabsEl = document.getElementById('cm-gallery-tabs');
    if (tabsEl) {
        tabsEl.innerHTML = CM.galleries.map(function(g) {
            var count = (g.file_ids || []).length;
            var active = CM.activeGallery === g.id ? ' active' : '';
            return '<div class="cm-ftab' + active + '" onclick="cmSelectGallery(\'' + g.id + '\', this)">' +
                '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" style="display:inline;vertical-align:-1px;margin-right:2px"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> ' + esc(g.name) + ' <span class="ct">' + count + '</span>' +
            '</div>';
        }).join('');
    }
};

window.cmSelectGallery = function(galleryId, el) {
    document.querySelectorAll('.cm-ftab').forEach(function(t) { t.classList.remove('active'); });
    if (el) el.classList.add('active');
    CM.activeGallery = CM.activeGallery === galleryId ? null : galleryId;
    cmRender();
};

window.cmBulkAddGallery = function() {
    if (!CM.selIds.length) { coraShowToast('Select files first.'); return; }
    cmRenderGalleries();
    document.getElementById('cm-gallery-dlg').classList.add('open');
};

window.cmCreateGalleryFromPicker = function() {
    var name = document.getElementById('cm-new-gallery-name').value.trim();
    if (!name) { coraShowToast('Enter gallery name.'); return; }
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_create_gallery', nonce: coraREData.ajaxNonce, name: name
    }, success: function(r) {
        if (r.success) {
            CM.galleries.push(r.data);
            document.getElementById('cm-new-gallery-name').value = '';
            if (CM.selIds.length) {
                cmAddToGallery(r.data.id);
            } else {
                cmRenderGalleries();
                coraShowToast('Gallery created.');
            }
        }
    }});
};

window.cmAddToGallery = function(galleryId) {
    if (!CM.selIds.length) return;
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_add_to_gallery', nonce: coraREData.ajaxNonce, gallery_id: galleryId, file_ids: CM.selIds.join(',')
    }, success: function(r) {
        if (r.success) {
            document.getElementById('cm-gallery-dlg').classList.remove('open');
            coraShowToast('Added to gallery!');
            cmLoadGalleries();
        }
    }});
};

// ── BATCH ZIP ───────────────────────────────────────────────────────────────
window.cmBulkZip = function() {
    if (!CM.selIds.length) { coraShowToast('Select files first.'); return; }
    coraShowToast('Compressing ZIP archive...');
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_batch_download_zip', nonce: coraREData.ajaxNonce, ids: CM.selIds.join(',')
    }, success: function(r) {
        if (r.success && r.data && r.data.url) {
            coraShowToast(r.data.count + ' files compressed. Downloading...');
            var a = document.createElement('a'); a.href = r.data.url; a.download = 'cora_media.zip'; a.click();
        } else {
            coraShowToast(r.data || 'ZIP creation failed.');
        }
    }});
};

// ── CONTEXT MENU ────────────────────────────────────────────────────────────
window.cmShowContextMenu = function(e, f) {
    e.preventDefault();
    if (typeof cmHideFolderContextMenu === 'function') cmHideFolderContextMenu();
    CM.ctxFile = f;
    document.getElementById('cm-ctx-title').textContent = f.title || f.filename;

    // Stars
    var sEl = document.getElementById('cm-ctx-stars');
    var rating = f.rating || 0;
    var sHtml = '';
    for (var i = 1; i <= 5; i++) {
        sHtml += '<span onclick="cmSetRating(' + f.id + ',' + i + ');cmHideContextMenu()" style="cursor:pointer;font-size:14px;color:' + (i <= rating ? '#facc15' : '#d4d4d8') + '">★</span>';
    }
    sEl.innerHTML = sHtml;

    // Color labels
    var lEl = document.getElementById('cm-ctx-labels');
    var curLabel = f.label || 'none';
    var lHtml = '';
    Object.keys(LABEL_MAP).forEach(function(k) {
        var active = k === curLabel ? 'box-shadow:0 0 0 2px #09090b' : '';
        lHtml += '<div onclick="cmSetLabel(' + f.id + ',\'' + k + '\');cmHideContextMenu()" style="width:12px;height:12px;border-radius:50%;background:' + LABEL_MAP[k].hex + ';cursor:pointer;' + active + '" title="' + LABEL_MAP[k].label + '"></div>';
    });
    lEl.innerHTML = lHtml;

    var menu = document.getElementById('cm-ctx-menu');
    menu.style.display = 'block';
    var x = e.clientX, y = e.clientY;
    if (x + 210 > window.innerWidth) x = window.innerWidth - 215;
    if (y + 280 > window.innerHeight) y = window.innerHeight - 285;
    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
};

window.cmHideContextMenu = function() {
    document.getElementById('cm-ctx-menu').style.display = 'none';
};

window.cmCtxAction = function(act) {
    var f = CM.ctxFile;
    cmHideContextMenu();
    if (!f) return;
    if (act === 'detail') cmOpenDetail(f);
    else if (act === 'copy') { navigator.clipboard.writeText(f.url); coraShowToast('Link copied.'); }
    else if (act === 'gallery') { CM.selIds = [f.id]; cmBulkAddGallery(); }
    else if (act === 'download') { var a = document.createElement('a'); a.href = f.url; a.download = f.filename; a.click(); }
    else if (act === 'delete') { cmDeletePrompt([f.id]); }
};

// ── FLOATING FOLDER CONTEXT MENU ───────────────────────────────────────────
window._activeFolderCtxObj = null;

window.cmShowFolderContextMenu = function(e, folderObj) {
    if (e) {
        if (typeof e.preventDefault === 'function') e.preventDefault();
        if (typeof e.stopPropagation === 'function') e.stopPropagation();
    }
    if (typeof cmHideContextMenu === 'function') cmHideContextMenu();
    window._activeFolderCtxObj = folderObj;

    var menu = document.getElementById('cm-folder-ctx-menu');
    if (!menu) return;
    menu.style.display = 'block';

    var x = 0, y = 0;
    if (e && typeof e.clientX === 'number' && (e.clientX !== 0 || e.clientY !== 0)) {
        x = e.clientX;
        y = e.clientY;
    } else if (e && e.target) {
        var rect = e.target.getBoundingClientRect();
        x = rect.left;
        y = rect.bottom + 4;
    }

    var menuW = menu.offsetWidth || 210;
    var menuH = menu.offsetHeight || 160;
    if (x + menuW > window.innerWidth - 10) {
        x = Math.max(10, window.innerWidth - menuW - 10);
    }
    if (y + menuH > window.innerHeight - 10) {
        y = Math.max(10, window.innerHeight - menuH - 10);
    }

    menu.style.left = x + 'px';
    menu.style.top = y + 'px';
};

window.cmHideFolderContextMenu = function() {
    var menu = document.getElementById('cm-folder-ctx-menu');
    if (menu) menu.style.display = 'none';
};

window.cmFolderCtxAction = function(action) {
    var folder = window._activeFolderCtxObj;
    cmHideFolderContextMenu();
    if (!folder) return;

    if (action === 'settings') {
        cmOpenFolderSettings(folder.id, folder.name);
    } else if (action === 'subfolder') {
        cmPromptFolder(folder.id);
    } else if (action === 'share_link') {
        if (folder.share_url) {
            navigator.clipboard.writeText(folder.share_url);
            coraShowToast('Folder link copied to clipboard!');
        } else {
            coraShowToast('Generating share link...');
            $.ajax({
                url: coraREData.ajaxUrl,
                type: 'POST',
                data: { action: 'cora_media_library_share_folder', nonce: coraREData.ajaxNonce, folder_id: folder.id },
                success: function(r) {
                    if (r.success && r.data && r.data.share_url) {
                        folder.share_url = r.data.share_url;
                        navigator.clipboard.writeText(r.data.share_url);
                        coraShowToast('Folder link copied to clipboard!');
                    } else {
                        coraShowToast(r.data || 'Share link generation failed.');
                    }
                },
                error: function() {
                    coraShowToast('Failed to generate share link.');
                }
            });
        }
    } else if (action === 'delete') {
        cmConfirmDeleteFolder(folder);
    }
};

window.cmConfirmDeleteFolder = function(folderObj) {
    if (!folderObj) return;
    document.getElementById('cm-confirm-title').textContent = 'Delete folder "' + (folderObj.name || '') + '"?';
    document.getElementById('cm-confirm-desc').textContent = 'This will permanently remove the folder. Files inside will become unorganized.';
    document.getElementById('cm-confirm-modal').classList.add('open');
    CM.confirmCb = function() {
        coraShowToast('Deleting folder...');
        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: { action: 'cora_media_library_delete_folder', nonce: coraREData.ajaxNonce, term_id: folderObj.id },
            success: function(r) {
                if (r.success) {
                    coraShowToast('Folder deleted.');
                    cmLoadFolders();
                    cmLoadFiles();
                } else {
                    coraShowToast(r.data || 'Failed to delete folder.');
                }
            }
        });
    };
};

// ── CULLING / RATING / LABELS ──────────────────────────────────────────────
window.cmSetRating = function(id, rating) {
    var f = CM.files.find(function(x) { return x.id === id; });
    if (f && f.rating === rating) rating = 0; // toggle off
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_save_media_rating', nonce: coraREData.ajaxNonce, id: id, rating: rating
    }, success: function(r) {
        if (r.success) {
            if (f) f.rating = rating;
            cmRender();
            if (CM.active && CM.active.id === id) cmRenderDetailCulling(f);
        }
    }});
};

window.cmSetLabel = function(id, label) {
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_save_media_label', nonce: coraREData.ajaxNonce, id: id, label: label
    }, success: function(r) {
        if (r.success) {
            var f = CM.files.find(function(x) { return x.id === id; });
            if (f) f.label = label;
            cmRender();
            if (CM.active && CM.active.id === id) cmRenderDetailCulling(f);
            coraShowToast('Label: ' + (LABEL_MAP[label] ? LABEL_MAP[label].label : label));
        }
    }});
};

window.cmSaveShootTags = function(tags) {
    if (!CM.active) return;
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_save_media_shoot_tags', nonce: coraREData.ajaxNonce, id: CM.active.id, shoot_tags: tags
    }, success: function(r) {
        if (r.success) {
            CM.active.shoot_tags = tags;
            coraShowToast('Shoot tags updated.');
        }
    }});
};

function cmRenderDetailCulling(f) {
    if (!f) return;
    // Stars
    var sEl = document.getElementById('cm-d-stars');
    if (sEl) {
        var sHtml = '';
        for (var i = 1; i <= 5; i++) {
            sHtml += '<span onclick="cmSetRating(' + f.id + ',' + i + ')" style="cursor:pointer;font-size:18px;color:' + (i <= (f.rating||0) ? '#facc15' : '#d4d4d8') + '">★</span>';
        }
        sEl.innerHTML = sHtml;
    }
    // Labels
    var lEl = document.getElementById('cm-d-labels');
    if (lEl) {
        var curLabel = f.label || 'none';
        var lHtml = '';
        Object.keys(LABEL_MAP).forEach(function(k) {
            var active = k === curLabel ? 'box-shadow:0 0 0 2px #09090b' : '';
            lHtml += '<div onclick="cmSetLabel(' + f.id + ',\'' + k + '\')" style="width:16px;height:16px;border-radius:50%;background:' + LABEL_MAP[k].hex + ';cursor:pointer;' + active + '" title="' + LABEL_MAP[k].label + '"></div>';
        });
        lEl.innerHTML = lHtml;
    }
    // Shoot tags
    var tEl = document.getElementById('cm-d-shoot-tags');
    if (tEl) tEl.value = f.shoot_tags || '';
}

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
window.cmRender = function() {
    var culling = document.getElementById('cm-fculling') ? document.getElementById('cm-fculling').value : '';
    var galleryFileIds = null;
    if (CM.activeGallery) {
        var g = CM.galleries.find(function(x) { return x.id === CM.activeGallery; });
        galleryFileIds = g ? (g.file_ids || []).map(Number) : [];
    }
    var filtered = CM.files.filter(function(f) {
        if (galleryFileIds !== null && galleryFileIds.indexOf(parseInt(f.id)) === -1) return false;
        if (culling === 'starred') return (f.rating || 0) >= 4;
        if (culling === 'green' || culling === 'yellow' || culling === 'red') return (f.label || 'none') === culling;
        return true;
    });
    if (!filtered.length && CM.files.length) {
        document.getElementById('cm-empty').style.display = 'flex';
    } else {
        document.getElementById('cm-empty').style.display = 'none';
    }
    CM.view === 'grid' ? cmRenderGrid(filtered) : cmRenderList(filtered);
};

window.cmRenderGrid = function(files) {
    files = files || CM.files;
    var g = document.getElementById('cm-grid'); g.innerHTML = '';
    if (!files.length) return;
    var frag = document.createDocumentFragment();
    files.forEach(function(f) {
        var d = document.createElement('div');
        d.className = 'cm-cell' + (CM.selIds.indexOf(f.id) > -1 ? ' sel' : '');
        d.dataset.id = f.id;
        var chkClass = 'cm-chk' + (CM.selIds.indexOf(f.id) > -1 ? ' checked' : '');
        
        var thumb = '';
        if (f.type_category === 'image' && f.thumbnail) {
            thumb = '<img class="cm-thumb" src="' + f.thumbnail + '" alt="" loading="lazy">';
        } else if (f.type_category === 'video') {
            thumb = '<div class="cm-cell-icon" style="position:relative">' +
                '<svg viewBox="0 0 24 24" width="36" height="36" stroke="#a1a1aa" stroke-width="1.5" fill="none"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2"></rect></svg>' +
                '<span style="position:absolute;bottom:6px;right:6px;background:rgba(0,0,0,.75);color:#fff;font-size:9px;font-weight:700;padding:2px 5px;border-radius:4px;font-family:monospace">03:24</span>' +
            '</div>';
        } else if (f.type_category === 'document') {
            thumb = '<div class="cm-cell-icon">' +
                '<div style="width:40px;height:48px;border:1.5px solid #e4e4e7;border-radius:6px;background:#fff;display:flex;align-items:center;justify-content:center;color:#ef4444;font-weight:800;font-size:11px">PDF</div>' +
            '</div>';
        } else {
            thumb = '<div class="cm-cell-icon">' + cmIcon(f.type_category, 36) + '</div>';
        }
        
        var stars = f.rating ? ' <span style="color:#facc15">★' + f.rating + '</span>' : '';
        var dot = (f.label && f.label !== 'none' && LABEL_MAP[f.label]) ? '<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:' + LABEL_MAP[f.label].hex + ';margin-right:4px"></span>' : '';
        var dimSize = (f.dimensions ? f.dimensions + ' • ' : '') + (f.file_size_human || '');

        d.innerHTML =
            '<div class="' + chkClass + '" data-id="' + f.id + '">' +
                '<svg viewBox="0 0 24 24" width="10" height="10" stroke="#fff" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
            '</div>' +
            thumb +
            '<span class="cm-cell-type">' + esc(f.type_category) + '</span>' +
            '<div class="cm-cell-meta">' +
                '<div class="cm-cell-name" title="' + esc(f.filename) + '">' + dot + esc(f.title || f.filename) + stars + '</div>' +
                '<div class="cm-cell-sub"><span>' + esc(dimSize) + '</span><span class="cm-cell-opt" title="File Details">⋮</span></div>' +
            '</div>';
        
        d.querySelector('.cm-chk').addEventListener('click', function(e) {
            e.stopPropagation(); cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1);
        });
        var opt = d.querySelector('.cm-cell-opt');
        if (opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation(); cmOpenDetail(f);
            });
        }
        d.addEventListener('click', function() {
            if (CM.bulk) { cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1); }
            else { cmOpenDetail(f); }
        });
        d.addEventListener('contextmenu', function(e) {
            cmShowContextMenu(e, f);
        });
        frag.appendChild(d);
    });
    g.appendChild(frag);
    if (CM.bulk) g.classList.add('cm-bulk-mode'); else g.classList.remove('cm-bulk-mode');
};

window.cmRenderList = function(files) {
    files = files || CM.files;
    var tb = document.getElementById('cm-list-body'); tb.innerHTML = '';
    var listAll = document.getElementById('cm-list-all');
    if (listAll) {
        listAll.style.display = '';
        listAll.checked = CM.files.length > 0 && CM.selIds.length === CM.files.length;
    }
    files.forEach(function(f) {
        var tr = document.createElement('tr');
        tr.className = CM.selIds.indexOf(f.id) > -1 ? 'sel' : ''; tr.dataset.id = f.id;
        var lthumb = f.type_category === 'image' && f.thumbnail
            ? '<img class="cm-lthumb" src="' + f.thumbnail + '" alt="">'
            : '<div class="cm-lthumb" style="display:flex;align-items:center;justify-content:center;background:#f4f4f5">' + cmIcon(f.type_category, 14) + '</div>';
        
        var stars = f.rating ? ' <span style="color:#facc15">★' + f.rating + '</span>' : '';
        var dot = (f.label && f.label !== 'none' && LABEL_MAP[f.label]) ? '<span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:' + LABEL_MAP[f.label].hex + ';margin-right:4px"></span>' : '';
        var subLine = [f.file_size_human, f.date_formatted, f.type_category].filter(Boolean).join(' • ');

        tr.innerHTML =
            '<td class="cm-lcell-chk"><input type="checkbox" style="accent-color:#09090b;cursor:pointer;width:15px;height:15px"' + (CM.selIds.indexOf(f.id) > -1 ? ' checked' : '') + '></td>' +
            '<td class="cm-lcell-main"><div style="display:flex;align-items:center;gap:10px;min-width:0">' + lthumb +
                '<div style="min-width:0;flex:1"><div style="font-weight:600;color:#18181b;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="' + esc(f.title || f.filename) + '">' + dot + esc(f.title || f.filename) + stars + '</div>' +
                '<div class="cm-lmain-sub-desktop" style="font-size:10px;color:#a1a1aa;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="' + esc(f.filename) + '">' + esc(f.filename) + '</div>' +
                '<div class="cm-lmobile-sub">' + esc(subLine) + '</div></div></div></td>' +
            '<td class="cm-lcell-author">' + esc(f.author_name) + '</td>' +
            '<td class="cm-lcell-folder">' + (f.folder_name ? '<span style="background:#f4f4f5;color:#52525b;border-radius:4px;padding:2px 7px;font-size:10px">' + esc(f.folder_name) + '</span>' : '<span style="color:#d4d4d8">—</span>') + '</td>' +
            '<td class="cm-lcell-date" style="color:#71717a">' + esc(f.date_formatted) + '</td>' +
            '<td class="cm-lcell-size" style="color:#71717a;font-family:ui-monospace,monospace;font-size:11px">' + esc(f.file_size_human) + '</td>' +
            '<td class="cm-lcell-type"><span style="background:#f4f4f5;color:#52525b;border-radius:4px;padding:2px 7px;font-size:10px;font-weight:600;text-transform:capitalize">' + esc(f.type_category) + '</span></td>' +
            '<td class="cm-lcell-act" style="text-align:right"><button class="cm-cell-opt" title="Actions" onclick="event.stopPropagation();cmShowContextMenu(event, ' + JSON.stringify(f).replace(/"/g, '&quot;') + ')">⋮</button></td>';
        tr.querySelector('input[type=checkbox]').addEventListener('change', function(e) { cmToggleSel(f.id, e.target.checked); e.stopPropagation(); });
        tr.addEventListener('click', function(e) { if (e.target.type === 'checkbox') return; if (CM.bulk) { cmToggleSel(f.id, CM.selIds.indexOf(f.id) === -1); } else { cmOpenDetail(f); } });
        tr.addEventListener('contextmenu', function(e) { cmShowContextMenu(e, f); });
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

    cmRenderDetailCulling(f);
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
    var disallowed = ['php', 'exe', 'sh', 'bat', 'cmd', 'js', 'html', 'htm', 'py', 'pl', 'cgi', 'jar', 'vbs'];
    var maxFileSize = 100 * 1024 * 1024;
    var currentStorage = (typeof CM.storage_bytes === 'number') ? CM.storage_bytes : 0;
    var limitStorage = (typeof CM.limit_bytes === 'number' && CM.limit_bytes > 0) ? CM.limit_bytes : (5 * 1024 * 1024 * 1024);

    Array.from(files).forEach(function(f) {
        var parts = f.name ? f.name.split('.') : [];
        var ext = parts.length > 1 ? parts.pop().toLowerCase() : '';
        if (disallowed.indexOf(ext) !== -1) {
            coraShowToast('Upload rejected: Security risk file extension is not allowed.');
            return;
        }
        if (f.size > maxFileSize) {
            coraShowToast('Upload rejected: File size exceeds 100 MB limit.');
            return;
        }
        if ((currentStorage + f.size) > limitStorage) {
            coraShowToast('Upload rejected: Exceeds 5 GB workspace storage quota.');
            return;
        }
        currentStorage += f.size;
        cmUploadOne(f);
    });
    var el = document.getElementById('cm-file-input');
    if (el) el.value = '';
};
window.cmUploadOne = function(file) {
    var MAX = 100 * 1024 * 1024;
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
window.cmToggleFoldersExpand = function() {
    CM.foldersExpanded = !CM.foldersExpanded;
    cmRenderFolderTabs();
};

window.cmRenderFolderTabs = function() {
    var container = document.getElementById('cm-folders-grid');
    if (!container) return;
    container.innerHTML = '';

    var totalCt = CM.total_count || 0;
    var unorgCt = CM.unorganised_count || 0;
    var items = [];

    // 1. All Media Global Folder Card
    var allActive = (CM.folder === null || CM.folder === undefined || CM.folder === '') ? ' active' : '';
    var allCard = document.createElement('div');
    allCard.className = 'cm-fcard' + allActive;
    allCard.innerHTML =
        '<div class="cm-fcard-left">' +
            '<div class="cm-fcard-icon">' +
                '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>' +
            '</div>' +
            '<div class="cm-fcard-info">' +
                '<div class="cm-fcard-name">All Media</div>' +
                '<div class="cm-fcard-ct">' + totalCt + ' items</div>' +
            '</div>' +
        '</div>';
    allCard.addEventListener('click', function() { cmSelectFolder(null); });
    items.push(allCard);

    // 2. Unorganised Folder Card
    var unorgActive = (CM.folder == -1) ? ' active' : '';
    var unorgCard = document.createElement('div');
    unorgCard.className = 'cm-fcard' + unorgActive;
    unorgCard.innerHTML =
        '<div class="cm-fcard-left">' +
            '<div class="cm-fcard-icon">' +
                '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>' +
            '</div>' +
            '<div class="cm-fcard-info">' +
                '<div class="cm-fcard-name">Unorganized</div>' +
                '<div class="cm-fcard-ct">' + unorgCt + ' items</div>' +
            '</div>' +
        '</div>';
    unorgCard.addEventListener('click', function() { cmSelectFolder(-1); });
    items.push(unorgCard);

    // 3. Custom Folder Cards
    var searchQ = (CM.filters.q || '').trim().toLowerCase();
    CM.folders.forEach(function(folder) {
        var matchParent = !searchQ || folder.name.toLowerCase().indexOf(searchQ) !== -1;
        var matchingChildren = (folder.children || []).filter(function(s) {
            return !searchQ || s.name.toLowerCase().indexOf(searchQ) !== -1;
        });

        if (!searchQ || matchParent || matchingChildren.length > 0) {
            if (!searchQ || matchParent) {
                var fColor = folder.color || '#3b82f6';
                var isAct = (CM.folder == folder.id) ? ' active' : '';
                var fcard = document.createElement('div');
                fcard.className = 'cm-fcard' + isAct;
                fcard.style.borderLeft = '3px solid ' + fColor;
                var chkHtml = CM.bulk ? '<input type="checkbox" class="cm-fcard-chk" ' + (CM.selFolderIds.indexOf(folder.id) > -1 ? 'checked' : '') + ' onclick="event.stopPropagation();cmToggleFolderSel(' + folder.id + ',this.checked)">' : '';
                fcard.innerHTML =
                    chkHtml +
                    '<div class="cm-fcard-left">' +
                        '<div class="cm-fcard-icon">' +
                            '<svg viewBox="0 0 24 24" width="18" height="18" stroke="' + fColor + '" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>' +
                        '</div>' +
                        '<div class="cm-fcard-info">' +
                            '<div class="cm-fcard-name" title="' + esc(folder.name) + '">' + esc(folder.name) + '</div>' +
                            '<div class="cm-fcard-ct">' + (folder.count || 0) + ' items</div>' +
                        '</div>' +
                    '</div>' +
                    '<span class="cm-fcard-opt" title="Folder Settings & Sharing">⋮</span>';
                
                fcard.addEventListener('click', function() {
                    if (CM.bulk) {
                        var isChecked = CM.selFolderIds.indexOf(folder.id) === -1;
                        cmToggleFolderSel(folder.id, isChecked);
                        var chk = fcard.querySelector('.cm-fcard-chk');
                        if (chk) chk.checked = isChecked;
                    } else {
                        cmSelectFolder(folder.id);
                    }
                });
                var opt = fcard.querySelector('.cm-fcard-opt');
                if (opt) {
                    opt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        cmShowFolderContextMenu(e, folder);
                    });
                }
                fcard.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cmShowFolderContextMenu(e, folder);
                });
                items.push(fcard);
            }

            // Subfolders
            matchingChildren.forEach(function(s) {
                var sColor = s.color || '#3b82f6';
                var sAct = (CM.folder == s.id) ? ' active' : '';
                var scard = document.createElement('div');
                scard.className = 'cm-fcard' + sAct;
                scard.style.borderLeft = '3px solid ' + sColor;
                var sChkHtml = CM.bulk ? '<input type="checkbox" class="cm-fcard-chk" ' + (CM.selFolderIds.indexOf(s.id) > -1 ? 'checked' : '') + ' onclick="event.stopPropagation();cmToggleFolderSel(' + s.id + ',this.checked)">' : '';
                scard.innerHTML =
                    sChkHtml +
                    '<div class="cm-fcard-left">' +
                        '<div class="cm-fcard-icon" style="background:#fafafa">' +
                            '<svg viewBox="0 0 24 24" width="16" height="16" stroke="' + sColor + '" stroke-width="2" fill="none"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>' +
                        '</div>' +
                        '<div class="cm-fcard-info">' +
                            '<div class="cm-fcard-name" title="' + esc(s.name) + '">↳ ' + esc(s.name) + '</div>' +
                            '<div class="cm-fcard-ct">' + (s.count || 0) + ' items</div>' +
                        '</div>' +
                    '</div>' +
                    '<span class="cm-fcard-opt" title="Folder Settings & Sharing">⋮</span>';
                scard.addEventListener('click', function() {
                    if (CM.bulk) {
                        var isChecked = CM.selFolderIds.indexOf(s.id) === -1;
                        cmToggleFolderSel(s.id, isChecked);
                        var chk = scard.querySelector('.cm-fcard-chk');
                        if (chk) chk.checked = isChecked;
                    } else {
                        cmSelectFolder(s.id);
                    }
                });
                var sOpt = scard.querySelector('.cm-fcard-opt');
                if (sOpt) {
                    sOpt.addEventListener('click', function(e) {
                        e.stopPropagation();
                        cmShowFolderContextMenu(e, s);
                    });
                }
                scard.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    cmShowFolderContextMenu(e, s);
                });
                items.push(scard);
            });
        }
    });

    if (!CM.foldersExpanded && items.length > 6) {
        for (var i = 0; i < 5; i++) {
            container.appendChild(items[i]);
        }
        var remaining = items.length - 5;
        var toggleCard = document.createElement('div');
        toggleCard.className = 'cm-fcard cm-fcard-toggle';
        toggleCard.innerHTML =
            '<div class="cm-fcard-left">' +
                '<div class="cm-fcard-icon">' +
                    '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>' +
                '</div>' +
                '<div class="cm-fcard-info">' +
                    '<div class="cm-fcard-name">+ Show ' + remaining + ' more</div>' +
                '</div>' +
            '</div>';
        toggleCard.addEventListener('click', function() { cmToggleFoldersExpand(); });
        container.appendChild(toggleCard);
    } else if (CM.foldersExpanded) {
        items.forEach(function(item) {
            container.appendChild(item);
        });
        var collapseCard = document.createElement('div');
        collapseCard.className = 'cm-fcard cm-fcard-toggle';
        collapseCard.innerHTML =
            '<div class="cm-fcard-left">' +
                '<div class="cm-fcard-icon">' +
                    '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>' +
                '</div>' +
                '<div class="cm-fcard-info">' +
                    '<div class="cm-fcard-name">Show less ▲</div>' +
                '</div>' +
            '</div>';
        collapseCard.addEventListener('click', function() { cmToggleFoldersExpand(); });
        container.appendChild(collapseCard);
    } else {
        items.forEach(function(item) {
            container.appendChild(item);
        });
    }

    if (CM.filters && CM.filters.q) {
        var q = CM.filters.q.trim().toLowerCase();
        var cards = container.querySelectorAll('.cm-fcard');
        cards.forEach(function(card) {
            if (card.classList.contains('cm-fcard-toggle')) {
                card.style.display = q ? 'none' : '';
                return;
            }
            var nameEl = card.querySelector('.cm-fcard-name');
            var name = nameEl ? nameEl.textContent.toLowerCase() : '';
            if (!q || name.indexOf(q) !== -1) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
};
window.cmPopulateBulkFolderSelect = function() {
    var bs = document.getElementById('cm-bulk-folder'); if(!bs)return; bs.innerHTML = '<option value="">Move to folder…</option>';
    CM.folders.forEach(function(f) { bs.innerHTML += '<option value="'+f.id+'">'+esc(f.name)+'</option>'; (f.children||[]).forEach(function(s){bs.innerHTML+='<option value="'+s.id+'">  ↳ '+esc(s.name)+'</option>';});});
};
window.cmSelectFolder = function(id) {
    CM.folder = id; CM.page = 1;
    cmRenderFolderTabs();
    cmUpdateBreadcrumbs();
    cmLoadFiles();
};
window.cmUpdateBreadcrumbs = function() {
    var subEl = document.getElementById('cm-bc-subpath');
    var countEl = document.getElementById('cm-bc-count');
    if (!subEl) return;

    var chevronSvg = '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" style="color:#a1a1aa;margin:0 2px"><polyline points="9 18 15 12 9 6"></polyline></svg>';

    if (CM.folder === null || CM.folder === undefined || CM.folder === '') {
        subEl.innerHTML = '';
    } else if (CM.folder == -1) {
        subEl.innerHTML = chevronSvg + '<span class="cm-bc-active">Unorganized</span>';
    } else {
        var fName = 'Folder';
        CM.folders.forEach(function(f) {
            if (f.id == CM.folder) fName = f.name;
            (f.children || []).forEach(function(s) {
                if (s.id == CM.folder) fName = s.name;
            });
        });
        subEl.innerHTML = chevronSvg + '<span class="cm-bc-active">' + esc(fName) + '</span>';
    }

    if (countEl) {
        countEl.textContent = (CM.total || 0) + ' files';
    }
};
window.cmPromptFolder = function(parentId) {
    document.getElementById('cm-folder-name').value = '';
    var parentSel = document.getElementById('cm-folder-parent');
    if (parentSel) {
        parentSel.innerHTML = '<option value="0">Root (Main Workspace)</option>';
        CM.folders.forEach(function(f) {
            parentSel.innerHTML += '<option value="' + f.id + '">' + esc(f.name) + '</option>';
        });
        if (parentId) {
            parentSel.value = parentId;
        } else {
            parentSel.value = '0';
        }
    }
    if (document.getElementById('cm-folder-desc')) document.getElementById('cm-folder-desc').value = '';
    if (document.getElementById('cm-folder-autoshare')) document.getElementById('cm-folder-autoshare').checked = false;
    document.getElementById('cm-folder-dlg').classList.add('open');
    setTimeout(function() { document.getElementById('cm-folder-name').focus(); }, 80);
};

window.cmCreateFolder = function() {
    var name = document.getElementById('cm-folder-name').value.trim();
    var parent_id = document.getElementById('cm-folder-parent') ? document.getElementById('cm-folder-parent').value : 0;
    var desc = document.getElementById('cm-folder-desc') ? document.getElementById('cm-folder-desc').value.trim() : '';
    var auto_share = document.getElementById('cm-folder-autoshare') ? (document.getElementById('cm-folder-autoshare').checked ? 1 : 0) : 0;

    if (!name) { coraShowToast('Enter a folder name.'); return; }
    coraShowToast('Creating folder...');
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_create_folder', nonce: coraREData.ajaxNonce,
        name: name, parent_id: parent_id, description: desc, auto_share: auto_share
    }, success: function(r) {
        document.getElementById('cm-folder-dlg').classList.remove('open');
        if (r.success) {
            coraShowToast(r.data.message || 'Folder created.');
            cmLoadFolders();
            if (r.data && r.data.id) {
                setTimeout(function() { cmOpenFolderSettings(r.data.id, name); }, 300);
            }
        } else {
            coraShowToast(r.data.message || r.data || 'Could not create folder.');
        }
    }});
};

// ── FILTERS / SORT / PAGINATION ───────────────────────────────────────────────
window.cmOnSearch = function(v) { clearTimeout(CM.searchT); CM.searchT=setTimeout(function(){CM.filters.q=v;CM.page=1;cmLoadFiles();cmRenderFolderTabs();},350); };
window.cmFilter = function() {
    var ft = document.getElementById('cm-ft') ? document.getElementById('cm-ft').value : 'all';
    var fc = document.getElementById('cm-fculling') ? document.getElementById('cm-fculling').value : '';
    var fd = document.getElementById('cm-fd') ? document.getElementById('cm-fd').value : '';
    var fdt = document.getElementById('cm-fdoctype') ? document.getElementById('cm-fdoctype').value : '';
    var fa = document.getElementById('cm-fa') ? document.getElementById('cm-fa').value : '';
    
    CM.filters.type     = ft;
    CM.filters.culling  = fc;
    CM.filters.date     = fd;
    CM.filters.doc_type = fdt;
    CM.filters.author   = fa;

    var mft = document.getElementById('cm-m-ft'); if (mft) mft.value = ft;
    var mfc = document.getElementById('cm-m-fculling'); if (mfc) mfc.value = fc;
    var mfd = document.getElementById('cm-m-fd'); if (mfd) mfd.value = fd;
    var mfdt = document.getElementById('cm-m-fdoctype'); if (mfdt) mfdt.value = fdt;
    var mfa = document.getElementById('cm-m-fa'); if (mfa) mfa.value = fa;

    var activeCount = 0;
    var chips = [];

    if (ft && ft !== 'all') {
        activeCount++;
        var ftEl = document.getElementById('cm-ft');
        var ftLabel = (ftEl && ftEl.selectedIndex >= 0) ? ftEl.options[ftEl.selectedIndex].text : ft;
        chips.push({ key: 'type', label: ftLabel, reset: function() { var el = document.getElementById('cm-ft'); if (el) el.value = 'all'; cmFilter(); } });
    }
    if (fc && fc !== '') {
        activeCount++;
        var fcEl = document.getElementById('cm-fculling');
        var fcLabel = (fcEl && fcEl.selectedIndex >= 0) ? fcEl.options[fcEl.selectedIndex].text : fc;
        chips.push({ key: 'culling', label: fcLabel, reset: function() { var el = document.getElementById('cm-fculling'); if (el) el.value = ''; cmFilter(); } });
    }
    if (fd && fd !== '') {
        activeCount++;
        var fdEl = document.getElementById('cm-fd');
        var fdLabel = (fdEl && fdEl.selectedIndex >= 0) ? fdEl.options[fdEl.selectedIndex].text : fd;
        chips.push({ key: 'date', label: fdLabel, reset: function() { var el = document.getElementById('cm-fd'); if (el) el.value = ''; cmFilter(); } });
    }
    if (fdt && fdt !== '') {
        activeCount++;
        var fdtEl = document.getElementById('cm-fdoctype');
        var fdtLabel = (fdtEl && fdtEl.selectedIndex >= 0) ? fdtEl.options[fdtEl.selectedIndex].text : fdt;
        chips.push({ key: 'doc_type', label: fdtLabel, reset: function() { var el = document.getElementById('cm-fdoctype'); if (el) el.value = ''; cmFilter(); } });
    }
    if (fa && fa !== '') {
        activeCount++;
        var faEl = document.getElementById('cm-fa');
        var faLabel = (faEl && faEl.selectedIndex >= 0) ? faEl.options[faEl.selectedIndex].text : fa;
        chips.push({ key: 'author', label: faLabel, reset: function() { var el = document.getElementById('cm-fa'); if (el) el.value = ''; cmFilter(); } });
    }

    var badge = document.getElementById('cm-filter-count-badge');
    if (badge) {
        badge.textContent = activeCount;
        badge.style.display = activeCount > 0 ? 'inline-block' : 'none';
    }

    var chipsContainer = document.getElementById('cm-active-filter-chips');
    if (chipsContainer) {
        if (chips.length > 0) {
            chipsContainer.style.display = 'flex';
            chipsContainer.innerHTML = '';
            chips.forEach(function(chip) {
                var btn = document.createElement('button');
                btn.className = 'cm-chip';
                btn.innerHTML = esc(chip.label) + ' <span class="cm-chip-close">✕</span>';
                btn.onclick = function() { chip.reset(); };
                chipsContainer.appendChild(btn);
            });
        } else {
            chipsContainer.style.display = 'none';
            chipsContainer.innerHTML = '';
        }
    }

    CM.page = 1;
    cmLoadFiles();
};
window.cmResetFilters = function() {
    CM.filters = {q:'', type:'all', culling:'', date:'', author:'', doc_type:''};
    var searchEl = document.getElementById('cm-search'); if (searchEl) searchEl.value = '';
    var ftEl = document.getElementById('cm-ft'); if (ftEl) ftEl.value = 'all';
    var fcEl = document.getElementById('cm-fculling'); if (fcEl) fcEl.value = '';
    var fdEl = document.getElementById('cm-fd'); if (fdEl) fdEl.value = '';
    var fdtEl = document.getElementById('cm-fdoctype'); if (fdtEl) fdtEl.value = '';
    var faEl = document.getElementById('cm-fa'); if (faEl) faEl.value = '';

    var mftEl = document.getElementById('cm-m-ft'); if (mftEl) mftEl.value = 'all';
    var mfcEl = document.getElementById('cm-m-fculling'); if (mfcEl) mfcEl.value = '';
    var mfdEl = document.getElementById('cm-m-fd'); if (mfdEl) mfdEl.value = '';
    var mfdtEl = document.getElementById('cm-m-fdoctype'); if (mfdtEl) mfdtEl.value = '';
    var mfaEl = document.getElementById('cm-m-fa'); if (mfaEl) mfaEl.value = '';

    cmFilter();
    coraShowToast('Filters reset.');
};

window.cmOnSortChange = function(val) {
    if (!val) return;
    var parts = val.split('-');
    CM.sortBy = parts[0];
    CM.sortDir = parts[1] || 'DESC';
    CM.page = 1;
    cmLoadFiles();
};
window.cmSort = function(col) { if(CM.sortBy===col) CM.sortDir=CM.sortDir==='ASC'?'DESC':'ASC'; else{CM.sortBy=col;CM.sortDir='DESC';} cmLoadFiles(); };
window.cmUpdatePag = function() {
    var pag = document.getElementById('cm-pag');
    if (!pag) return;
    if (CM.pages <= 1) { pag.style.display='none'; return; }
    pag.style.display = 'flex';
    document.getElementById('cm-pag-info').textContent = 'Page ' + CM.page + ' of ' + CM.pages + ' (' + CM.total + ' files)';
    document.getElementById('cm-prev').disabled = CM.page <= 1;
    document.getElementById('cm-next').disabled = CM.page >= CM.pages;
};
window.cmPage = function(d) { var n=CM.page+d; if(n<1||n>CM.pages) return; CM.page=n; cmLoadFiles(); };

window.cmOpenMobileFilters = function() {
    var ft = document.getElementById('cm-ft');
    var fc = document.getElementById('cm-fculling');
    var fd = document.getElementById('cm-fd');
    var fdt = document.getElementById('cm-fdoctype');
    var fa = document.getElementById('cm-fa');

    var mft = document.getElementById('cm-m-ft');
    var mfc = document.getElementById('cm-m-fculling');
    var mfd = document.getElementById('cm-m-fd');
    var mfdt = document.getElementById('cm-m-fdoctype');
    var mfa = document.getElementById('cm-m-fa');

    if (ft && mft) mft.value = ft.value;
    if (fc && mfc) mfc.value = fc.value;
    if (fd && mfd) mfd.value = fd.value;
    if (fdt && mfdt) mfdt.value = fdt.value;
    if (fa && mfa) mfa.value = fa.value;

    var dlg = document.getElementById('cm-mobile-filter-dlg');
    if (dlg) dlg.classList.add('open');
};

window.cmCloseMobileFilters = function() {
    var dlg = document.getElementById('cm-mobile-filter-dlg');
    if (dlg) dlg.classList.remove('open');
};

window.cmApplyMobileFilters = function() {
    var ft = document.getElementById('cm-ft');
    var fc = document.getElementById('cm-fculling');
    var fd = document.getElementById('cm-fd');
    var fdt = document.getElementById('cm-fdoctype');
    var fa = document.getElementById('cm-fa');

    var mft = document.getElementById('cm-m-ft');
    var mfc = document.getElementById('cm-m-fculling');
    var mfd = document.getElementById('cm-m-fd');
    var mfdt = document.getElementById('cm-m-fdoctype');
    var mfa = document.getElementById('cm-m-fa');

    if (ft && mft) ft.value = mft.value;
    if (fc && mfc) fc.value = mfc.value;
    if (fd && mfd) fd.value = mfd.value;
    if (fdt && mfdt) fdt.value = mfdt.value;
    if (fa && mfa) fa.value = mfa.value;

    cmFilter();
    cmCloseMobileFilters();
};

window.cmResetMobileFilters = function() {
    cmResetFilters();
    cmCloseMobileFilters();
};

window.cmLoadFilterOpts = function() {
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_months',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;
        var s=document.getElementById('cm-fd');
        var ms=document.getElementById('cm-m-fd');
        (r.data.months||[]).forEach(function(m){
            var optHtml = '<option value="'+esc(m.value)+'">'+esc(m.label)+'</option>';
            if(s) s.innerHTML += optHtml;
            if(ms) ms.innerHTML += optHtml;
        });
    }});
    var as=document.getElementById('cm-fa');
    var mas=document.getElementById('cm-m-fa');
    if(as || mas) $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_uploaders',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;
        (r.data.uploaders||[]).forEach(function(u){
            var optHtml = '<option value="'+u.id+'">'+esc(u.name)+'</option>';
            if(as) as.innerHTML += optHtml;
            if(mas) mas.innerHTML += optHtml;
        });
    }});
};

// ── BULK ──────────────────────────────────────────────────────────────────────
window.cmToggleBulk = function() {
    CM.bulk = !CM.bulk;
    if (!CM.bulk) {
        CM.selIds = [];
        CM.selFolderIds = [];
    }
    var btn = document.getElementById('cm-bulk-btn');
    if (btn) btn.textContent = CM.bulk ? 'Cancel' : 'Select';
    var bb = document.getElementById('cm-bulk-bar');
    if (bb) {
        bb.style.display = CM.bulk ? 'flex' : 'none';
        bb.className = 'cm-bulk-bar' + (CM.bulk ? ' on' : '');
    }
    var listAll = document.getElementById('cm-list-all');
    if (listAll) listAll.checked = CM.bulk && CM.files.length > 0 && CM.selIds.length === CM.files.length;
    cmRenderFolderTabs();
    cmRender();
    cmUpdateBulkCounter();
};

window.cmToggleFolderSel = function(id, checked) {
    id = parseInt(id, 10) || id;
    if (checked) {
        if (CM.selFolderIds.indexOf(id) === -1) CM.selFolderIds.push(id);
        if (!CM.bulk) {
            CM.bulk = true;
            var btn = document.getElementById('cm-bulk-btn');
            if (btn) btn.textContent = 'Cancel';
            var bb = document.getElementById('cm-bulk-bar');
            if (bb) {
                bb.style.display = 'flex';
                bb.className = 'cm-bulk-bar on';
            }
            cmRenderFolderTabs();
            cmRender();
        }
    } else {
        CM.selFolderIds = CM.selFolderIds.filter(function(x) { return x != id; });
    }
    cmUpdateBulkCounter();
};

window.cmToggleSel = function(id, on) {
    if (on) {
        if (CM.selIds.indexOf(id) === -1) CM.selIds.push(id);
        if (!CM.bulk) {
            CM.bulk = true;
            var btn = document.getElementById('cm-bulk-btn');
            if (btn) btn.textContent = 'Cancel';
            var bb = document.getElementById('cm-bulk-bar');
            if (bb) {
                bb.style.display = 'flex';
                bb.className = 'cm-bulk-bar on';
            }
            var g = document.getElementById('cm-grid');
            if (g) g.classList.add('cm-bulk-mode');
            cmRenderFolderTabs();
        }
    } else {
        CM.selIds = CM.selIds.filter(function(x) { return x !== id; });
    }
    var gc = document.querySelector('.cm-cell[data-id="' + id + '"]');
    if (gc) {
        gc.classList.toggle('sel', on);
        var chk = gc.querySelector('.cm-chk');
        if (chk) chk.classList.toggle('checked', on);
    }
    var lc = document.querySelector('#cm-list-body tr[data-id="' + id + '"]');
    if (lc) {
        lc.classList.toggle('sel', on);
        var cb = lc.querySelector('input[type=checkbox]');
        if (cb) cb.checked = on;
    }
    var listAll = document.getElementById('cm-list-all');
    if (listAll) {
        listAll.checked = CM.files.length > 0 && CM.selIds.length === CM.files.length;
    }
    cmUpdateBulkCounter();
};

window.cmListAll = function(ch) {
    if (typeof ch === 'undefined') ch = true;
    CM.files.forEach(function(f) { cmToggleSel(f.id, ch); });
    if (ch) {
        var allFids = [];
        (CM.folders || []).forEach(function(folder) {
            allFids.push(folder.id);
            (folder.children || []).forEach(function(child) {
                allFids.push(child.id);
            });
        });
        CM.selFolderIds = allFids;
    } else {
        CM.selFolderIds = [];
    }
    cmRenderFolderTabs();
    cmUpdateBulkCounter();
};

window.cmUpdateBulkCounter = window.cmBulkCt = function() {
    var fct = CM.selFolderIds.length, mct = CM.selIds.length;
    var txt = [];
    if (fct > 0) txt.push(fct + ' folder' + (fct > 1 ? 's' : ''));
    if (mct > 0) txt.push(mct + ' file' + (mct > 1 ? 's' : ''));
    var el = document.getElementById('cm-bulk-ct');
    if (el) el.textContent = txt.join(', ') || '0 selected';
};

window.cmBulkMove = function() {
    var fid=document.getElementById('cm-bulk-folder').value; if(!fid||!CM.selIds.length){coraShowToast('Select files and a destination folder.');return;}
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_move',nonce:coraREData.ajaxNonce,attachment_ids:CM.selIds,folder_id:fid},
    success:function(r){if(r.success){coraShowToast(r.data.message);CM.selIds=[];cmLoadFiles();cmLoadFolders();}else coraShowToast('Move failed.');}});
};

window.cmBulkDeleteFolders = function() {
    if (!CM.selFolderIds.length) {
        coraShowToast('Select folders first.');
        return;
    }
    var count = CM.selFolderIds.length;
    document.getElementById('cm-confirm-title').textContent = 'Delete ' + count + ' folder' + (count > 1 ? 's' : '') + '?';
    document.getElementById('cm-confirm-desc').textContent = 'This will permanently remove the selected folder(s). Files inside will become unorganized.';
    document.getElementById('cm-confirm-modal').classList.add('open');
    CM.confirmCb = function() {
        var ids = CM.selFolderIds.slice();
        var done = 0;
        coraShowToast('Deleting folders...');
        ids.forEach(function(id) {
            $.ajax({
                url: coraREData.ajaxUrl,
                type: 'POST',
                data: { action: 'cora_media_library_delete_folder', nonce: coraREData.ajaxNonce, term_id: id },
                complete: function() {
                    done++;
                    if (done === ids.length) {
                        CM.selFolderIds = [];
                        coraShowToast('Folder(s) deleted.');
                        cmLoadFolders();
                        cmLoadFiles();
                        cmUpdateBulkCounter();
                    }
                }
            });
        });
    };
};

window.cmBulkDelete = function() {
    if (CM.selFolderIds.length > 0) {
        cmBulkDeleteFolders();
    }
    if (CM.selIds.length > 0) {
        cmDeletePrompt(CM.selIds.slice());
    }
    if (!CM.selFolderIds.length && !CM.selIds.length) {
        coraShowToast('Select items to delete.');
    }
};

window.cmBulkColorFolders = function(color) {
    if (!CM.selFolderIds.length) {
        coraShowToast('Select folders first.');
        return;
    }
    var fMap = {};
    (CM.folders || []).forEach(function(f) {
        fMap[f.id] = f.name;
        (f.children || []).forEach(function(s) {
            fMap[s.id] = s.name;
        });
    });

    var ids = CM.selFolderIds.slice();
    var done = 0;
    coraShowToast('Updating folder colors...');
    ids.forEach(function(id) {
        var name = fMap[id] || 'Folder';
        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_media_library_rename_folder',
                nonce: coraREData.ajaxNonce,
                term_id: id,
                name: name,
                color: color
            },
            complete: function() {
                done++;
                if (done === ids.length) {
                    coraShowToast('Folder colors updated!');
                    cmLoadFolders();
                }
            }
        });
    });
    var menu = document.getElementById('cm-bulk-color-menu');
    if (menu) menu.style.display = 'none';
};

window.cmToggleBulkColorMenu = function(e) {
    if (e) e.stopPropagation();
    var menu = document.getElementById('cm-bulk-color-menu');
    if (!menu) return;
    var isOpen = menu.style.display === 'flex';
    menu.style.display = isOpen ? 'none' : 'flex';
};

window.cmRenderSwatches = function(containerId, inputId, activeColor) {
    var colors = ['#3b82f6', '#ef4444', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899', '#64748b', '#09090b'];
    var container = typeof containerId === 'string' ? document.getElementById(containerId) : containerId;
    if (!container) return;
    if (inputId) {
        var inputEl = document.getElementById(inputId);
        if (inputEl) inputEl.value = activeColor || colors[0];
    }
    container.innerHTML = colors.map(function(c) {
        var activeStyle = (c.toLowerCase() === (activeColor || '').toLowerCase()) ? 'outline:2px solid #09090b;outline-offset:2px;transform:scale(1.1);' : '';
        return '<div onclick="cmSelectSwatch(\'' + containerId + '\', \'' + inputId + '\', \'' + c + '\')" style="width:20px;height:20px;border-radius:50%;background:' + c + ';cursor:pointer;transition:all .15s;' + activeStyle + '" data-color="' + c + '"></div>';
    }).join('');
};

window.cmSelectSwatch = function(containerId, inputId, color) {
    if (inputId) {
        var inputEl = document.getElementById(inputId);
        if (inputEl) inputEl.value = color;
    }
    cmRenderSwatches(containerId, inputId, color);
};

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

// ── FOLDER SETTINGS & SHARING ──────────────────────────────────────────────────
window.cmOpenFolderSettings = function(folderId, name) {
    if (!folderId) return;
    var folderObj = null;
    (CM.folders || []).forEach(function(x) {
        if (x.id == folderId) folderObj = x;
        (x.children || []).forEach(function(ch) {
            if (ch.id == folderId) folderObj = ch;
        });
    });
    if (!name && folderObj) {
        name = folderObj.name;
    }
    var color = (folderObj && folderObj.color) ? folderObj.color : '#3b82f6';
    document.getElementById('cm-fs-id').value = folderId;
    document.getElementById('cm-fs-name-input').value = name || '';
    document.getElementById('cm-fs-title').textContent = 'Folder Settings: ' + (name || '');
    document.getElementById('cm-fs-url-input').value = '';
    cmRenderSwatches('cm-fs-color-swatches', 'cm-fs-color', color);
    if (typeof cmHideContextMenu === 'function') cmHideContextMenu();
    var dlg = document.getElementById('cm-folder-ctx-menu') || document.getElementById('cm-folder-settings-dlg');
    if (dlg) dlg.classList.add('open');
};

window.cmSaveFolderSettings = window.cmSaveRenameFolder = function() {
    var id = document.getElementById('cm-fs-id').value;
    var name = document.getElementById('cm-fs-name-input').value.trim();
    var color = document.getElementById('cm-fs-color') ? document.getElementById('cm-fs-color').value : '#3b82f6';
    if (!id || !name) { coraShowToast('Folder name required.'); return; }
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_rename_folder', nonce: coraREData.ajaxNonce, term_id: id, name: name, color: color
    }, success: function(r) {
        if (r.success) {
            coraShowToast('Folder updated!');
            var dlg = document.getElementById('cm-folder-ctx-menu') || document.getElementById('cm-folder-settings-dlg');
            if (dlg) dlg.classList.remove('open');
            cmLoadFolders();
        } else coraShowToast(r.data || 'Save failed.');
    }});
};

window.cmGenerateFolderShare = function() {
    var id = document.getElementById('cm-fs-id').value;
    if (!id) return;
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_share_folder', nonce: coraREData.ajaxNonce, folder_id: id
    }, success: function(r) {
        if (r.success && r.data && r.data.share_url) {
            document.getElementById('cm-fs-url-input').value = r.data.share_url;
            coraShowToast('Folder share link generated!');
        } else coraShowToast(r.data || 'Share link generation failed.');
    }});
};

window.cmCopyFolderShareUrl = function() {
    var url = document.getElementById('cm-fs-url-input').value;
    if (!url) { cmGenerateFolderShare(); return; }
    navigator.clipboard.writeText(url);
    coraShowToast('Folder link copied to clipboard!');
};

window.cmEmailFolderShare = function() {
    var id = document.getElementById('cm-fs-id').value;
    var email = document.getElementById('cm-fs-email-input').value.trim();
    var shareUrl = document.getElementById('cm-fs-url-input').value;

    if (!email) { coraShowToast('Please enter client email.'); return; }
    if (!shareUrl) {
        $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
            action: 'cora_media_library_share_folder', nonce: coraREData.ajaxNonce, folder_id: id
        }, success: function(r) {
            if (r.success && r.data && r.data.share_url) {
                document.getElementById('cm-fs-url-input').value = r.data.share_url;
                cmSendFolderEmail(id, email, r.data.share_url);
            } else coraShowToast(r.data || 'Could not generate share link.');
        }});
    } else {
        cmSendFolderEmail(id, email, shareUrl);
    }
};

function cmSendFolderEmail(id, email, shareUrl) {
    coraShowToast('Sending email invitation...');
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_email_folder', nonce: coraREData.ajaxNonce, folder_id: id, email: email, share_url: shareUrl
    }, success: function(r) {
        if (r.success) {
            coraShowToast('Folder access emailed to ' + email);
            document.getElementById('cm-fs-email-input').value = '';
        } else {
            coraShowToast(r.data || 'Email failed.');
        }
    }});
}

window.cmDeleteFolderFromSettings = function() {
    var id = document.getElementById('cm-fs-id').value;
    if (!id) return;
    $.ajax({ url: coraREData.ajaxUrl, type: 'POST', data: {
        action: 'cora_media_library_delete_folder', nonce: coraREData.ajaxNonce, term_id: id
    }, success: function(r) {
        if (r.success) {
            coraShowToast('Folder deleted.');
            var dlg = document.getElementById('cm-folder-ctx-menu') || document.getElementById('cm-folder-settings-dlg');
            if (dlg) dlg.classList.remove('open');
            cmLoadFolders();
        }
    }});
};

// ── STORAGE ───────────────────────────────────────────────────────────────────
window._cmStorageAnalyticsPinned = false;

window.cmShowStorageAnalytics = function(show) {
    var card = document.getElementById('cm-storage-analytics-card');
    if (!card) return;
    if (window._cmStorageAnalyticsPinned && !show) return;
    card.style.display = show ? 'block' : 'none';
};

window.cmToggleStorageAnalytics = function(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    var card = document.getElementById('cm-storage-analytics-card');
    if (!card) return;
    window._cmStorageAnalyticsPinned = !window._cmStorageAnalyticsPinned;
    card.style.display = window._cmStorageAnalyticsPinned ? 'block' : 'none';
};

document.addEventListener('click', function(e) {
    var wrap = document.getElementById('cm-storage-wrap');
    var card = document.getElementById('cm-storage-analytics-card');
    if (card && wrap && !wrap.contains(e.target)) {
        window._cmStorageAnalyticsPinned = false;
        card.style.display = 'none';
    }
});

function cmFormatBytes(b) {
    if (!b || b <= 0) return '0 B';
    var k = 1024, sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    var i = Math.floor(Math.log(b) / Math.log(k));
    return parseFloat((b / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

window.cmLoadStorage = function() {
    $.ajax({url:coraREData.ajaxUrl,type:'POST',data:{action:'cora_media_library_get_storage',nonce:coraREData.ajaxNonce},
    success:function(r){if(!r.success)return;var d=r.data,p=Math.min(100,Math.max(0,parseFloat(d.percent_used)||0));
        var formattedPct = p.toFixed(1) + '%';
        if (p % 1 === 0) formattedPct = p + '%';
        if (typeof d.total_bytes !== 'undefined') CM.storage_bytes = d.total_bytes;
        if (typeof d.limit_bytes !== 'undefined') CM.limit_bytes = d.limit_bytes;

        var strokeColor = '#10b981';
        var statusClass = 'cm-storage-healthy';
        if (p >= 90) {
            strokeColor = '#ef4444';
            statusClass = 'cm-storage-critical';
        } else if (p >= 70) {
            strokeColor = '#f59e0b';
            statusClass = 'cm-storage-warning';
        }

        var ringFill = document.getElementById('cm-ring-fill');
        if (ringFill) {
            ringFill.setAttribute('stroke-dasharray', p + ', 100');
            ringFill.setAttribute('stroke', strokeColor);
        }

        var storagePct = document.getElementById('cm-storage-pct');
        if (storagePct) storagePct.textContent = formattedPct;

        var saPctText = document.getElementById('cm-sa-pct-text');
        if (saPctText) {
            saPctText.textContent = formattedPct + ' Used';
            saPctText.style.color = strokeColor;
        }

        var saHuman = document.getElementById('cm-sa-human');
        if (saHuman) {
            saHuman.textContent = (d.total_human || '0 B') + ' used of ' + (d.limit_human || 'Unlimited');
        }

        var saBar = document.getElementById('cm-sa-bar');
        if (saBar) {
            saBar.style.width = p + '%';
            saBar.style.backgroundColor = strokeColor;
        }

        var saFree = document.getElementById('cm-sa-free');
        if (saFree) {
            if (typeof d.limit_bytes !== 'undefined' && typeof d.total_bytes !== 'undefined' && d.limit_bytes > 0) {
                var freeBytes = Math.max(0, d.limit_bytes - d.total_bytes);
                saFree.textContent = cmFormatBytes(freeBytes);
            } else {
                saFree.textContent = '—';
            }
        }

        var wrap = document.getElementById('cm-storage-wrap');
        if (wrap) {
            wrap.classList.remove('cm-storage-healthy', 'cm-storage-warning', 'cm-storage-critical');
            wrap.classList.add(statusClass);
            wrap.style.display = 'inline-flex';
        }
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
