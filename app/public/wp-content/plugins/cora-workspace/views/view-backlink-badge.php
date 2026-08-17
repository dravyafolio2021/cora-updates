<?php
/**
 * Made in Cora - Floating Backlink Badge Template
 * Adheres strictly to monochromatic design system specifications.
 */
?>
<!-- Made in Cora Backlink Badge -->
<a href="https://heycora.in" target="_blank" rel="noopener noreferrer" id="cora-backlink-badge" class="cora-backlink-badge-pill" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: #ffffff;
    border: 1px solid #e4e4e7;
    border-radius: 9999px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    text-decoration: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: #09090b;
    line-height: 1;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
    user-select: none;
" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, 0.12)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 16px rgba(0, 0, 0, 0.08)';">
    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: #09090b; flex-shrink: 0; display: block;">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
    </svg>
    <span style="color: #09090b; letter-spacing: -0.01em; display: inline-block;">Made in Cora</span>
</a>

<style>
@media (max-width: 640px) {
    #cora-backlink-badge {
        bottom: 12px !important;
        right: 12px !important;
        padding: 6px 12px !important;
        font-size: 11px !important;
        gap: 6px !important;
    }
}
@media print {
    #cora-backlink-badge {
        display: none !important;
    }
}
</style>
