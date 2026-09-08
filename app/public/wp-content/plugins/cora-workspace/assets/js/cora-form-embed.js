/**
 * Cora Universal Form Embed & Integration Engine
 * @version 1.0.0
 * Lightweight, zero-dependency client script for external landing pages, Webflow,
 * WordPress, Carrd, Shopify, Squarespace, and custom HTML documents.
 */
(function() {
    'use strict';

    // Prevent double initialization
    if (window.CoraForm && window.CoraForm._initialized) {
        return;
    }

    // Auto-detect Cora platform origin from script source tag
    var scriptOrigin = '';
    try {
        var scripts = document.getElementsByTagName('script');
        for (var i = scripts.length - 1; i >= 0; i--) {
            var src = scripts[i].src || '';
            if (src.indexOf('cora-form-embed.js') !== -1) {
                var a = document.createElement('a');
                a.href = src;
                scriptOrigin = a.protocol + '//' + a.host;
                break;
            }
        }
    } catch (e) {}

    var CoraForm = {
        _initialized: true,
        baseOrigin: scriptOrigin || (window.location ? (window.location.protocol + '//' + window.location.host) : ''),
        activeOverlay: null,

        /**
         * Initialize auto-discovery on page load
         */
        init: function() {
            var self = this;
            self._bindMessageListener();
            self._mountInlineContainers();
            self._bindTriggerButtons();

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    self._mountInlineContainers();
                    self._bindTriggerButtons();
                });
            }
        },

        /**
         * Listen for postMessage events from embedded Cora Form iframes
         */
        _bindMessageListener: function() {
            var self = this;
            window.addEventListener('message', function(event) {
                if (!event.data || typeof event.data !== 'object') return;

                var data = event.data;

                // 1. Auto-resize iframe height dynamically
                if (data.type === 'cora-form-resize' && data.height) {
                    var targetHeight = parseInt(data.height, 10);
                    if (targetHeight > 0) {
                        var iframes = document.querySelectorAll('iframe');
                        for (var i = 0; i < iframes.length; i++) {
                            var ifr = iframes[i];
                            // Match by sender window or id/data-key
                            var matchesWindow = false;
                            try {
                                matchesWindow = ifr.contentWindow === event.source;
                            } catch (err) {}

                            var matchesKey = data.formKey && (
                                ifr.id === 'cora-form-' + data.formKey ||
                                ifr.dataset.coraForm === data.formKey ||
                                (ifr.src && ifr.src.indexOf(data.formKey) !== -1)
                            );

                            if (matchesWindow || matchesKey) {
                                ifr.style.height = (targetHeight + 4) + 'px';
                                ifr.style.overflow = 'hidden';
                                break;
                            }
                        }
                    }
                }

                // 2. Submission success event dispatching
                if (data.type === 'cora-form-submitted') {
                    // Dispatch CustomEvent for Google Tag Manager, GA4, Meta Pixel, etc.
                    try {
                        var customEvt = new CustomEvent('cora:form:submitted', {
                            detail: data,
                            bubbles: true,
                            cancelable: true
                        });
                        window.dispatchEvent(customEvt);
                        document.dispatchEvent(customEvt);
                    } catch (err) {}

                    if (typeof window.onCoraFormSubmitted === 'function') {
                        window.onCoraFormSubmitted(data);
                    }

                    // If inside active popup/drawer overlay, auto-close after 3s if configured
                    if (self.activeOverlay && self.activeOverlay._autoClose) {
                        setTimeout(function() {
                            self.close();
                        }, 2500);
                    }
                }
            });
        },

        /**
         * Auto-mount inline embed containers (.cora-form-embed)
         */
        _mountInlineContainers: function() {
            var containers = document.querySelectorAll('.cora-form-embed, [data-cora-form]:not(iframe)');
            for (var i = 0; i < containers.length; i++) {
                var el = containers[i];
                if (el.dataset.coraMounted) continue;

                var formKey = el.dataset.coraForm || el.getAttribute('data-form');
                if (!formKey) continue;

                var isTransparent = el.dataset.transparent === 'true' || el.getAttribute('data-transparent') === '1';
                var isBorderless = el.dataset.borderless === 'true' || el.getAttribute('data-borderless') === '1';
                var hideBranding = el.dataset.hideBranding === 'true' || el.getAttribute('data-hide-branding') === '1';
                var theme = el.dataset.theme || el.getAttribute('data-theme') || '';

                var urlParams = ['embed=1'];
                if (isTransparent) urlParams.push('transparent=1');
                if (isBorderless) urlParams.push('borderless=1');
                if (hideBranding) urlParams.push('hide_branding=1');
                if (theme) urlParams.push('theme=' + encodeURIComponent(theme));

                var base = el.dataset.coraHost || this.baseOrigin;
                if (base.endsWith('/')) base = base.slice(0, -1);

                var src = base + '/shared-form/' + encodeURIComponent(formKey) + '?' + urlParams.join('&');

                var iframe = document.createElement('iframe');
                iframe.id = 'cora-form-' + formKey;
                iframe.src = src;
                iframe.width = '100%';
                iframe.height = el.dataset.height || '520';
                iframe.frameBorder = '0';
                iframe.scrolling = 'no';
                iframe.setAttribute('allow', 'camera; microphone; autoplay; encrypted-media;');
                iframe.style.border = 'none';
                iframe.style.width = '100%';
                iframe.style.minHeight = '350px';
                iframe.style.overflow = 'hidden';
                iframe.style.transition = 'height 0.25s ease';
                iframe.style.backgroundColor = isTransparent ? 'transparent' : '#ffffff';

                el.innerHTML = '';
                el.appendChild(iframe);
                el.dataset.coraMounted = 'true';
            }
        },

        /**
         * Auto-bind buttons with data-cora-open-form="frm_xxx"
         */
        _bindTriggerButtons: function() {
            var self = this;
            var buttons = document.querySelectorAll('[data-cora-open-form]');
            for (var i = 0; i < buttons.length; i++) {
                var btn = buttons[i];
                if (btn.dataset.coraBound) continue;
                btn.dataset.coraBound = 'true';
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var key = this.getAttribute('data-cora-open-form');
                    var mode = this.getAttribute('data-cora-mode') || 'drawer';
                    var theme = this.getAttribute('data-cora-theme') || '';
                    self.open(key, { mode: mode, theme: theme });
                });
            }
        },

        /**
         * Open a form in a slide-out drawer or modal overlay
         * @param {string} formKey 
         * @param {object} options 
         */
        open: function(formKey, options) {
            options = options || {};
            var self = this;
            if (this.activeOverlay) {
                this.close();
            }

            var mode = options.mode || 'drawer'; // 'drawer' or 'modal'
            var theme = options.theme || '';
            var hideBranding = options.hideBranding !== undefined ? options.hideBranding : false;
            var autoClose = options.autoClose !== undefined ? options.autoClose : true;

            var base = options.host || this.baseOrigin;
            if (base.endsWith('/')) base = base.slice(0, -1);

            var query = ['embed=1'];
            if (theme) query.push('theme=' + encodeURIComponent(theme));
            if (hideBranding) query.push('hide_branding=1');
            if (mode === 'drawer') query.push('transparent=1');

            var src = base + '/shared-form/' + encodeURIComponent(formKey) + '?' + query.join('&');

            // 1. Overlay Backdrop
            var overlay = document.createElement('div');
            overlay.id = 'cora-form-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.backgroundColor = 'rgba(9, 9, 11, 0.5)';
            overlay.style.backdropFilter = 'blur(6px)';
            overlay.style.webkitBackdropFilter = 'blur(6px)';
            overlay.style.zIndex = '999999';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            overlay.style.display = 'flex';
            overlay.style.alignItems = mode === 'drawer' ? 'stretch' : 'center';
            overlay.style.justifyContent = mode === 'drawer' ? 'flex-end' : 'center';
            overlay.style.padding = mode === 'drawer' ? '0' : '16px';
            overlay.style.boxSizing = 'border-box';

            // 2. Sheet / Card Container
            var sheet = document.createElement('div');
            sheet.style.backgroundColor = '#ffffff';
            sheet.style.display = 'flex';
            sheet.style.flexDirection = 'column';
            sheet.style.position = 'relative';
            sheet.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
            sheet.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
            sheet.style.overflow = 'hidden';

            if (mode === 'drawer') {
                sheet.style.width = '100%';
                sheet.style.maxWidth = '580px';
                sheet.style.height = '100%';
                sheet.style.transform = 'translateX(100%)';
                sheet.style.borderLeft = '1px solid #e4e4e7';
            } else {
                sheet.style.width = '100%';
                sheet.style.maxWidth = '640px';
                sheet.style.maxHeight = '90vh';
                sheet.style.borderRadius = '16px';
                sheet.style.transform = 'scale(0.95)';
                sheet.style.border = '1px solid #e4e4e7';
            }

            // 3. Header Bar with Close Button
            var header = document.createElement('div');
            header.style.display = 'flex';
            header.style.alignItems = 'center';
            header.style.justifyContent = 'space-between';
            header.style.padding = '12px 18px';
            header.style.borderBottom = '1px solid #f4f4f5';
            header.style.backgroundColor = '#fafafa';
            header.style.shrink = '0';

            var titleSpan = document.createElement('span');
            titleSpan.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Inter", sans-serif';
            titleSpan.style.fontSize = '12px';
            titleSpan.style.fontWeight = '600';
            titleSpan.style.color = '#71717a';
            titleSpan.textContent = 'Intake Form';

            var closeBtn = document.createElement('button');
            closeBtn.setAttribute('type', 'button');
            closeBtn.setAttribute('aria-label', 'Close form');
            closeBtn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" style="display:block;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            closeBtn.style.background = 'transparent';
            closeBtn.style.border = 'none';
            closeBtn.style.padding = '6px';
            closeBtn.style.borderRadius = '8px';
            closeBtn.style.cursor = 'pointer';
            closeBtn.style.color = '#71717a';
            closeBtn.style.transition = 'color 0.15s, background 0.15s';
            closeBtn.onmouseenter = function() { closeBtn.style.backgroundColor = '#f4f4f5'; closeBtn.style.color = '#09090b'; };
            closeBtn.onmouseleave = function() { closeBtn.style.backgroundColor = 'transparent'; closeBtn.style.color = '#71717a'; };
            closeBtn.onclick = function() { self.close(); };

            header.appendChild(titleSpan);
            header.appendChild(closeBtn);

            // 4. Iframe body
            var ifr = document.createElement('iframe');
            ifr.id = 'cora-form-overlay-frame';
            ifr.src = src;
            ifr.style.width = '100%';
            ifr.style.height = '100%';
            ifr.style.flex = '1';
            ifr.style.border = 'none';
            ifr.setAttribute('allow', 'camera; microphone; autoplay; encrypted-media;');

            sheet.appendChild(header);
            sheet.appendChild(ifr);
            overlay.appendChild(sheet);

            // Click outside backdrop to close
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    self.close();
                }
            });

            // Escape key listener
            var onKeydown = function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    self.close();
                }
            };
            window.addEventListener('keydown', onKeydown);

            overlay._onKeydown = onKeydown;
            overlay._sheet = sheet;
            overlay._mode = mode;
            overlay._autoClose = autoClose;

            document.body.appendChild(overlay);
            this.activeOverlay = overlay;

            // Trigger smooth animation
            requestAnimationFrame(function() {
                overlay.style.opacity = '1';
                if (mode === 'drawer') {
                    sheet.style.transform = 'translateX(0)';
                } else {
                    sheet.style.transform = 'scale(1)';
                }
            });
        },

        /**
         * Close and cleanly unmount active drawer or modal
         */
        close: function() {
            if (!this.activeOverlay) return;
            var overlay = this.activeOverlay;
            var sheet = overlay._sheet;
            var mode = overlay._mode;

            overlay.style.opacity = '0';
            if (sheet) {
                if (mode === 'drawer') {
                    sheet.style.transform = 'translateX(100%)';
                } else {
                    sheet.style.transform = 'scale(0.95)';
                }
            }

            if (overlay._onKeydown) {
                window.removeEventListener('keydown', overlay._onKeydown);
            }

            setTimeout(function() {
                if (overlay && overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 260);

            this.activeOverlay = null;
        }
    };

    // Auto-init and expose to window
    CoraForm.init();
    window.CoraForm = CoraForm;
})();
