/**
 * Cora Elementor Reskin JS
 * Restores default Elementor panels, widgets, tabs, and titles.
 * Injects only the custom topbar navigation and breadcrumbs layout.
 */
function initCoraReskin() {
    // Override the default browser alert inside the editor iframe
    window.alert = function(msg) {
        if (window.parent && window.parent.coraShowToast) {
            window.parent.coraShowToast(msg);
        } else {
            console.log("Intercepted Elementor Alert: ", msg);
        }
    };


    // Auto-click "Take Over" modal button if it appears
    const checkTakeOver = setInterval(() => {
        const dialogButtons = document.querySelectorAll('button');
        dialogButtons.forEach(btn => {
            if (btn.textContent.trim() === 'Take Over') {
                btn.click();
            }
        });
    }, 500);

    // MutationObserver to white-label WordPress references and logos dynamically
    const observer = new MutationObserver(() => {
        // Hide panel footer exit options
        document.querySelectorAll('#elementor-panel-footer-exit-to-dashboard, .elementor-panel-menu-item-exit, .elementor-panel-menu-item-exit-to-dashboard').forEach(el => {
            el.style.setProperty('display', 'none', 'important');
        });
        
        // Hide menu items or text blocks containing WordPress/Exit to references
        document.querySelectorAll('.elementor-panel-menu-item, .elementor-panel-menu-item-title, [class*="menu-item"]').forEach(el => {
            const text = el.textContent || '';
            if (text.toLowerCase().includes('wordpress') || text.toLowerCase().includes('exit to')) {
                el.style.setProperty('display', 'none', 'important');
                const parentLi = el.closest('li');
                if (parentLi) {
                    parentLi.style.setProperty('display', 'none', 'important');
                }
            }
        });

        // Hide specific top bar features: Angie, Checklist, What's New
        document.querySelectorAll('[data-tooltip="Angie"], [aria-label="Angie"], [title="Angie"], [data-tooltip="Checklist"], [aria-label="Checklist"], [title="Checklist"], [data-tooltip="What\'s New"], [aria-label="What\'s New"], [title="What\'s New"]').forEach(el => {
            el.style.setProperty('display', 'none', 'important');
            const wrapper = el.closest('span');
            if (wrapper) {
                wrapper.style.setProperty('display', 'none', 'important');
            }
        });
        // Hide the native Elementor topbar — all navigation lives in the parent topbar now
        const nativeHeader = document.querySelector('header.MuiAppBar-root, .e-top-bar, #e-top-bar, .elementor-editor-top-bar');
        if (nativeHeader) {
            nativeHeader.style.setProperty('display', 'none', 'important');
        }
        // Hide onboarding toasts/dialogues
        const onboardingToast = document.getElementById('elementor-toast');
        if (onboardingToast) {
            onboardingToast.style.setProperty('display', 'none', 'important');
        }
        // Replace Elementor Logo button with Cora logo
        document.querySelectorAll('button').forEach(btn => {
            const svg = btn.querySelector('svg');
            if (svg) {
                const title = svg.querySelector('title');
                if (title && title.textContent === 'Elementor Logo') {
                    btn.setAttribute('title', 'Cora Logo');
                    btn.setAttribute('aria-label', 'Cora Logo');
                    btn.setAttribute('data-tooltip', 'Cora Logo');
                    const logoUrl = window.location.origin + '/wp-content/plugins/cora-real-estate/assets/images/cora-favicon.png';
                    btn.innerHTML = `<img src="${logoUrl}" style="width: 20px; height: 20px; object-fit: contain;" alt="Cora Logo" />`;
                }
            }
        });

        // ── Remove "Edit with AI" sparkle buttons ──
        // Elementor dynamically injects .e-ai-button next to text, image and
        // other controls. Remove them as soon as they appear in the DOM.
        document.querySelectorAll('.e-ai-button, [class*="e-ai-button"]').forEach(el => {
            el.remove();
        });

        // ── Block all plugin install / upsell notice banners ──
        // Elementor injects .elementor-control-notice blocks inside widget panels
        // that contain "Install now" buttons pointing to wp-admin/update.php.
        // We remove them entirely so no user can trigger a plugin install from here.
        document.querySelectorAll(
            '.elementor-control-notice, ' +
            '[class*="elementor-control-notice"], ' +
            '.e-notice, .e-notice-bar, ' +
            '[class*="e-notice"], ' +
            '.elementor-promotion, ' +
            '.elementor-upgrade-notice, ' +
            '.elementor-go-pro, ' +
            '[class*="go-pro"]'
        ).forEach(el => {
            el.remove();
        });

        // Also scrub any lingering "Install now" / "install-plugin" anchor or button CTAs
        document.querySelectorAll(
            'a[href*="install-plugin"], ' +
            'button[data-settings*="install-plugin"]'
        ).forEach(el => {
            const notice = el.closest('.elementor-control-notice, [class*="elementor-control-notice"]');
            if (notice) {
                notice.remove();
            } else {
                el.remove();
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoraReskin);
} else {
    initCoraReskin();
}
