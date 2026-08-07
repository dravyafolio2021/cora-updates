<?php
/**
 * Made in Cora - Floating Backlink Badge Template
 * Adheres strictly to monochromatic design system specifications.
 */
?>
<!-- Made in Cora Backlink Badge -->
<a href="https://cora.local" target="_blank" rel="noopener" id="cora-backlink-badge" class="cora-backlink-badge-pill" style="
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 2147483647;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background-color: #ffffff;
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(9, 9, 11, 0.14);
    text-decoration: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
    font-size: 15px;
    font-weight: 800;
    color: #09090b;
    line-height: 1;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
    cursor: pointer;
    user-select: none;
" onmouseover="this.style.transform='translateY(-2px) scale(1.02)'; this.style.boxShadow='0 10px 28px rgba(9, 9, 11, 0.18)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 8px 24px rgba(9, 9, 11, 0.14)';">
    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: #09090b; flex-shrink: 0; display: block;">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
    </svg>
    <span style="color: #09090b; letter-spacing: -0.02em; display: inline-block; vertical-align: middle;">Made in Cora</span>
</a>

<style>
@media print {
    #cora-backlink-badge {
        display: none !important;
    }
}
</style>
