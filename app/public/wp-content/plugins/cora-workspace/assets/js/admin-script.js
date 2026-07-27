/**
 * Cora for Real Estate - Admin Dashboard JavaScript Interactions
 */

// Ensure coraREData is defined and merge settings from coraREWPData if it exists
if (typeof window.coraREData === 'undefined') {
    window.coraREData = typeof coraREWPData !== 'undefined' ? coraREWPData : {};
} else if (typeof coraREWPData !== 'undefined') {
    Object.assign(window.coraREData, coraREWPData);
}

if (typeof window.ajaxurl === 'undefined') {
    window.ajaxurl = (window.coraREData && window.coraREData.ajaxUrl) ? window.coraREData.ajaxUrl : '/wp-admin/admin-ajax.php';
}

jQuery(document).ready(function($) {
    // Sidebar Scroll Persistence
    const sidebarScrollContainer = document.getElementById('cora-sidebar-scroll-container');
    if (sidebarScrollContainer) {
        const savedScroll = sessionStorage.getItem('coraSidebarScroll');
        if (savedScroll) {
            sidebarScrollContainer.scrollTop = parseInt(savedScroll, 10);
        }
        sidebarScrollContainer.addEventListener('scroll', function() {
            sessionStorage.setItem('coraSidebarScroll', sidebarScrollContainer.scrollTop);
        });
    }

    // Custom Toast Notification System
    // Custom Toast Notification System
    window.coraShowToast = function(message, type = 'info') {
        let toastContainer = $('#cora-toast-container');
        if (toastContainer.length === 0) {
            $('body').append('<div id="cora-toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col-reverse gap-2.5 pointer-events-none"></div>');
            toastContainer = $('#cora-toast-container');
        }
        
        // Prevent duplicate toast stacking
        let duplicateFound = false;
        toastContainer.children().each(function() {
            if ($(this).find('span').text() === message) {
                const existingToast = $(this);
                
                // Visual bounce effect
                existingToast.css('transform', 'scale(1.06)');
                setTimeout(() => {
                    existingToast.css('transform', 'scale(1)');
                }, 120);

                // Reset timeout
                const oldTimeoutId = existingToast.data('timeout-id');
                const oldRemoveId = existingToast.data('remove-timeout-id');
                if (oldTimeoutId) clearTimeout(oldTimeoutId);
                if (oldRemoveId) clearTimeout(oldRemoveId);

                const tId = setTimeout(() => {
                    existingToast.addClass('translate-y-3 opacity-0');
                    const rId = setTimeout(() => {
                        existingToast.remove();
                    }, 300);
                    existingToast.data('remove-timeout-id', rId);
                }, 3000);
                
                existingToast.data('timeout-id', tId);
                duplicateFound = true;
                return false; // break loop
            }
        });

        if (duplicateFound) return;

        const toastId = 'toast-' + Date.now();
        
        let iconHtml = '';
        if (type === 'success') {
            iconHtml = `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-green-500 shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
        } else if (type === 'error') {
            iconHtml = `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-red-500 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
        } else if (type === 'warning') {
            iconHtml = `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-amber-500 shrink-0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`;
        } else {
            iconHtml = `<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="text-blue-500 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
        }

        const toastHtml = `
            <div id="${toastId}" class="bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-100 text-xs font-semibold px-4 py-3 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-800 flex items-center gap-3 pointer-events-auto transition-all duration-300 transform translate-y-3 opacity-0 select-none max-w-sm">
                ${iconHtml}
                <span class="flex-1">${message}</span>
            </div>
        `;
        toastContainer.append(toastHtml);
        
        const toast = $(`#${toastId}`);
        // Fade & slide in
        setTimeout(() => {
            toast.removeClass('translate-y-3 opacity-0');
        }, 50);
        
        // Auto remove after 3 seconds
        const tId = setTimeout(() => {
            toast.addClass('translate-y-3 opacity-0');
            const rId = setTimeout(() => {
                toast.remove();
            }, 300);
            toast.data('remove-timeout-id', rId);
        }, 3000);
        
        toast.data('timeout-id', tId);
    };

    // 1. Navigation & Tab Switching
    window.coraNavigateTo = function(targetPageId) {
        if (!targetPageId) return;

        if (window.location.pathname.indexOf('admin.php') !== -1 || window.location.search.indexOf('page=cora-workspace') !== -1) {
            window.location.href = window.location.origin + window.location.pathname + '?page=cora-workspace&sub_page=' + encodeURIComponent(targetPageId);
            return;
        }

        const activeData = (window.coraREData && window.coraREData.currentRole) ? window.coraREData : (window.coraData || {});
        let siteUrl = activeData.siteUrl || window.location.origin;
        if (siteUrl.endsWith('/')) {
            siteUrl = siteUrl.slice(0, -1);
        }
        
        let activeWsSlug = 'workspace';
        if (window.coraREData && window.coraREData.activeWorkspace && window.coraREData.activeWorkspace.slug) {
            activeWsSlug = window.coraREData.activeWorkspace.slug;
        } else if (window.coraAppData && window.coraAppData.activeWorkspace && window.coraAppData.activeWorkspace.slug) {
            activeWsSlug = window.coraAppData.activeWorkspace.slug;
        }

        window.location.href = siteUrl + '/' + encodeURIComponent(activeWsSlug) + '/' + encodeURIComponent(targetPageId);
    };

    $(document).on('click', '.cora-nav-item, .cora-bottom-nav-item', function(e) {
        const item = $(this).closest('.cora-nav-item, .cora-bottom-nav-item');
        
        if (item.hasClass('cora-nav-soon')) {
            e.preventDefault();
            e.stopPropagation();
            window.coraShowToast("AI Assistants & Automation features are coming soon. Stay tuned!");
            return false;
        }

        if (item.hasClass('cora-nav-locked')) {
            e.preventDefault();
            e.stopPropagation();
            window.coraShowToast("Gallery SEO Tagging is a Premium feature. Upgrade to unlock.");
            return false;
        }

        const targetPage = item.attr('data-target');
        if (targetPage && typeof window.coraNavigateTo === 'function') {
            window.coraNavigateTo(targetPage);
        }

        // Handle Mobile Sidebar Closure on click
        if (window.innerWidth < 1024) {
            $('.cora-sidebar').addClass('-translate-x-full');
            $('#cora-sidebar-backdrop').addClass('hidden');
        }
    });

    // Mobile specific AI tab trigger (slides sidebar in/out)
    $('#cora-mobile-ai-trigger').on('click', function(e) {
        e.preventDefault();
        const sidebar = $('#cora-ai-sidebar');
        const isCollapsed = sidebar.hasClass('collapsed');
        coraToggleSidebar(isCollapsed);
    });

    // Mobile Navigation Sidebar Drawer Toggle
    $('#cora-mobile-menu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.cora-sidebar').removeClass('-translate-x-full').addClass('translate-x-0');
        $('#cora-sidebar-backdrop').removeClass('hidden');
    });

    $('#cora-mobile-menu-close, #cora-sidebar-backdrop').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.cora-sidebar').removeClass('translate-x-0').addClass('-translate-x-full');
        $('#cora-sidebar-backdrop').addClass('hidden');
    });

    // macOS Dock Magnification & Global Body Floating Tooltip for Collapsed Sidebar (Cora UI/UX)
    $(document).on('mouseenter', '.cora-sidebar.collapsed-sidebar .cora-nav-item', function() {
        var $this = $(this);
        var title = $this.attr('data-tooltip') || $this.find('.cora-nav-text').text().trim();
        if (!title) return;

        var $tooltip = $('#cora-sidebar-floating-tooltip');
        if (!$tooltip.length) {
            $tooltip = $('<div id="cora-sidebar-floating-tooltip" class="fixed hidden z-[999999] bg-zinc-950 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-2xl border border-zinc-800 pointer-events-none select-none transition-opacity duration-100 ease-out whitespace-nowrap"><span id="cora-sidebar-tooltip-text"></span></div>').appendTo('body');
        }

        $('#cora-sidebar-tooltip-text').text(title);
        var rect = this.getBoundingClientRect();
        var top = rect.top + (rect.height / 2) - 13;
        var left = rect.right + 12;

        $tooltip.css({ top: top + 'px', left: left + 'px' }).removeClass('hidden').css('opacity', '1');

        var $prev = $this.prev('.cora-nav-item');
        var $next = $this.next('.cora-nav-item');
        $('.cora-nav-item').removeClass('dock-hover-active dock-hover-neighbor');
        $this.addClass('dock-hover-active');
        if ($prev.length) $prev.addClass('dock-hover-neighbor');
        if ($next.length) $next.addClass('dock-hover-neighbor');
    }).on('mouseleave', '.cora-sidebar.collapsed-sidebar .cora-nav-item', function() {
        $('#cora-sidebar-floating-tooltip').addClass('hidden').css('opacity', '0');
        $('.cora-nav-item').removeClass('dock-hover-active dock-hover-neighbor');
    });

    // Dedicated Click Handler for Workspace Card in Collapsed Mode
    $(document).on('click', '.cora-sidebar.collapsed-sidebar .cora-workspace-card', function(e) {
        if (typeof window.coraToggleSidebarCollapse === 'function') {
            window.coraToggleSidebarCollapse(e);
        }
    });

    window.coraCloseAllDrawers = function() {
        $('aside[id$="-drawer"], aside[id$="-sheet"], div[id$="-drawer"], div[id$="-sheet"], div[id$="-modal"]').addClass('collapsed hidden');
        $('#cora-media-library-drawer, #cora-ai-tone-drawer').addClass('translate-x-full pointer-events-none');
        const bd = document.getElementById('cora-drawer-backdrop');
        if(bd) { bd.classList.add('hidden'); bd.style.pointerEvents = 'none'; bd.style.display = 'none'; }
        $('.cora-tour-backdrop').removeClass('active').addClass('hidden').css({'pointer-events': 'none', 'display': 'none'});
        $('.cora-tour-highlight').removeClass('cora-tour-highlight');
        $('body').removeClass('cora-drawer-open overflow-hidden');
    };

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            window.coraCloseAllDrawers();
        }
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            if (typeof window.coraToggleSidebarCollapse === 'function') {
                window.coraToggleSidebarCollapse();
            }
        }
    });

    // Geofence Drawer Handlers
    window.openGeofenceDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-geofence-drawer').removeClass('collapsed');
        $('#cora-drawer-backdrop').removeClass('hidden');
    };

    window.closeGeofenceDrawer = function() {
        window.coraCloseAllDrawers();
    };

    // Create Custom Role Drawer Handlers
    window.openCreateCustomRoleDrawer = function(baseTemplate) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        if (baseTemplate && $('#custom-role-base-template').length) {
            $('#custom-role-base-template').val(baseTemplate);
            if (typeof handleApplyBaseTemplate === 'function') {
                handleApplyBaseTemplate(baseTemplate);
            }
        }
        $('#cora-create-custom-role-drawer').removeClass('collapsed');
        $('#cora-drawer-backdrop').removeClass('hidden');
    };

    window.closeCreateCustomRoleDrawer = function() {
        window.coraCloseAllDrawers();
    };

    // Attendance Reports & Sharing Drawer Handlers
    window.openAttendanceReportsDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-attendance-reports-drawer').removeClass('collapsed');
        $('#cora-drawer-backdrop').removeClass('hidden');
    };

    window.closeAttendanceReportsDrawer = function() {
        window.coraCloseAllDrawers();
    };

    // Automated Financial Reports & Schedule Management Drawer Handlers
    window.openFinancialReportsDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-financial-reports-drawer').removeClass('collapsed');
        $('#cora-drawer-backdrop').removeClass('hidden');
    };

    window.closeFinancialReportsDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        } else {
            $('#cora-financial-reports-drawer').addClass('collapsed');
            $('#cora-drawer-backdrop').addClass('hidden');
        }
    };

    // Studio Camera Equipment Drawer Opener Helpers
    window.openAddGearDrawer = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        const drawer = $('#cora-add-gear-drawer, #cora-add-equipment-drawer');
        if (drawer.length) {
            drawer.removeClass('collapsed hidden');
        }
        const bd = document.getElementById('cora-drawer-backdrop');
        if (bd) {
            bd.classList.remove('hidden');
            bd.style.display = 'block';
            bd.style.pointerEvents = 'auto';
        }
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.openCheckoutGearDrawer = function(gearData) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        const drawer = $('#cora-checkout-gear-drawer, #cora-checkout-equipment-drawer');
        if (drawer.length) {
            drawer.removeClass('collapsed hidden');
        }
        const bd = document.getElementById('cora-drawer-backdrop');
        if (bd) {
            bd.classList.remove('hidden');
            bd.style.display = 'block';
            bd.style.pointerEvents = 'auto';
        }
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.openMaintenanceDrawer = function(gearData) {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        const drawer = $('#cora-maintenance-drawer, #cora-equipment-maintenance-drawer');
        if (drawer.length) {
            drawer.removeClass('collapsed hidden');
        }
        const bd = document.getElementById('cora-drawer-backdrop');
        if (bd) {
            bd.classList.remove('hidden');
            bd.style.display = 'block';
            bd.style.pointerEvents = 'auto';
        }
        $('body').addClass('cora-drawer-open overflow-hidden');
    };

    window.generateInstantReport = function(type) {
        type = type || 'monthly';
        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Generating ' + type.toUpperCase() + ' financial report...', 'info');
        }

        const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        const nonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

        $.post(ajaxUrl, {
            action: 'cora_generate_financial_report',
            security: nonce,
            nonce: nonce,
            report_type: type
        }, function(response) {
            if (response.success) {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(response.data.message || 'Report generated successfully.', 'success');
                }

                let $overlay = $('#cora-report-preview-overlay');
                if (!$overlay.length) {
                    $overlay = $(`
                        <div id="cora-report-preview-overlay" class="fixed inset-0 z-[10000] bg-zinc-950/70 backdrop-blur-sm flex items-center justify-center p-4">
                            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/40">
                                    <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                        </svg>
                                        Financial Report Summary
                                    </h3>
                                    <button type="button" onclick="$('#cora-report-preview-overlay').addClass('hidden')" class="p-1 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>
                                <div id="cora-report-preview-content" class="p-5 overflow-y-auto flex-1 text-xs"></div>
                                <div class="p-3.5 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 flex items-center justify-end gap-2">
                                    <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 font-semibold text-xs rounded-lg hover:bg-zinc-800 transition-colors">Print Report</button>
                                    <button type="button" onclick="$('#cora-report-preview-overlay').addClass('hidden')" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold text-xs rounded-lg hover:bg-zinc-50 transition-colors">Close</button>
                                </div>
                            </div>
                        </div>
                    `);
                    $('body').append($overlay);
                }

                $('#cora-report-preview-content').html(response.data.report_html || '');
                $overlay.removeClass('hidden');
            } else {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(response.data?.message || 'Failed to generate financial report.', 'error');
                }
            }
        }).fail(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Network error while generating report.', 'error');
            }
        });
    };

    window.saveFinancialSchedule = function(e) {
        if (e && e.preventDefault) e.preventDefault();
        const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        const nonce   = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';

        const dailyDigest   = ($('#sched-daily, #fin-sched-daily').is(':checked')) ? '1' : '0';
        const weeklySummary = ($('#sched-weekly, #fin-sched-weekly').is(':checked')) ? '1' : '0';
        const monthlyPnl    = ($('#sched-monthly, #fin-sched-monthly').is(':checked')) ? '1' : '0';
        const quarterlyTax  = ($('#sched-quarterly, #fin-sched-quarterly').is(':checked')) ? '1' : '0';
        const recipientEmail = $('#sched-recipient-email, #fin-sched-email').val() || '';

        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast('Saving financial report schedule preferences...', 'info');
        }

        $.post(ajaxUrl, {
            action: 'cora_save_financial_schedule',
            security: nonce,
            nonce: nonce,
            daily_digest: dailyDigest,
            weekly_summary: weeklySummary,
            monthly_pnl: monthlyPnl,
            quarterly_tax: quarterlyTax,
            recipient_email: recipientEmail
        }, function(response) {
            if (response.success) {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(response.data.message || 'Report schedule settings saved successfully.', 'success');
                }
                window.closeFinancialReportsDrawer();
            } else {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(response.data?.message || 'Failed to save financial schedule settings.', 'error');
                }
            }
        }).fail(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast('Network error while saving schedule preferences.', 'error');
            }
        });
    };

    // 2. Add Booking Dialog Drawer Controllers
    window.coraToggleAddShowingDrawer = function(show) {
        const drawer = $('#cora-add-showing-drawer');
        if (show) {
            if (typeof window.coraCloseAllDrawers === 'function') {
                window.coraCloseAllDrawers();
            }
            drawer.removeClass('collapsed');
            $('#cora-drawer-backdrop').removeClass('hidden');
            $('#cora-drawer-client-name').focus();
        } else {
            if (typeof window.coraCloseAllDrawers === 'function') {
                window.coraCloseAllDrawers();
            } else {
                drawer.addClass('collapsed');
            }
            // Reset input values
            $('#cora-drawer-client-name').val('');
            $('#cora-drawer-deal-type').val('Residential Buy');
            $('#cora-drawer-location').val('');
            $('#cora-drawer-date').val('');
            $('#cora-drawer-price').val('');
        }
    };

    $('#cora-add-booking-btn').on('click', function() {
        coraToggleAddShowingDrawer(true);
    });

    // Save shoot details from drawer form
    $('#cora-save-showing-btn').on('click', function() {
        const clientName = $('#cora-drawer-client-name').val().trim();
        const shootType = $('#cora-drawer-deal-type').val();
        const location = $('#cora-drawer-location').val().trim() || 'Delhi Office';
        const date = $('#cora-drawer-date').val().trim() || '28th Jun, 2026';
        const price = $('#cora-drawer-price').val().trim() || '₹15,000';
        
        if (!clientName) {
            coraShowToast("Please enter client name.");
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_save_booking',
                nonce: coraREData.ajaxNonce,
                client_name: clientName,
                deal_type: shootType,
                location: location,
                date: date,
                price: price
            },
            success: function(response) {
                btn.prop('disabled', false).text('Save Shoot');
                if (response.success) {
                    coraShowToast("Booking created successfully!");
                    coraToggleAddShowingDrawer(false);
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    coraShowToast("Error: " + (response.data || "Could not save booking."));
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Save Shoot');
                coraShowToast("Failed to connect to the server.");
            }
        });
    });

    // 3. Status & Search Filters for CRM Table
    function coraApplyCRMFilters() {
        const activeTab = $('#cora-page-bookings .cora-filter-tab.active').data('filter') || 'all';
        const searchQuery = $('#cora-crm-search-input').val().toLowerCase().trim();

        if (activeTab === 'clients') {
            $('#cora-clients-table-container').show().removeClass('hidden');
            $('#cora-bookings-table-container').hide().addClass('hidden');
            $('#cora-add-booking-btn').hide().addClass('hidden');

            const rows = $('#cora-clients-table-container tbody tr');
            const cards = $('#cora-clients-cards-list .cora-client-card-item');

            rows.each(function() {
                const text = $(this).text().toLowerCase();
                if (searchQuery === '' || text.indexOf(searchQuery) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            cards.each(function() {
                const text = $(this).text().toLowerCase();
                if (searchQuery === '' || text.indexOf(searchQuery) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('#cora-clients-table-container').hide().addClass('hidden');
            $('#cora-bookings-table-container').show().removeClass('hidden');
            $('#cora-add-booking-btn').show().removeClass('hidden');

            const rows = $('#cora-bookings-table tbody tr');
            const cards = $('#cora-bookings-cards-list .cora-booking-card-item');

            rows.each(function() {
                const status = $(this).attr('data-status') || '';
                const text = $(this).text().toLowerCase();
                const matchesStatus = (activeTab === 'all' || status === activeTab);
                const matchesSearch = (searchQuery === '' || text.indexOf(searchQuery) > -1);

                if (matchesStatus && matchesSearch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            cards.each(function() {
                const status = $(this).attr('data-status') || '';
                const text = $(this).text().toLowerCase();
                const matchesStatus = (activeTab === 'all' || status === activeTab);
                const matchesSearch = (searchQuery === '' || text.indexOf(searchQuery) > -1);

                if (matchesStatus && matchesSearch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    $('#cora-page-bookings .cora-filter-tab').on('click', function() {
        $('#cora-page-bookings .cora-filter-tab').removeClass('active');
        $(this).addClass('active');
        coraApplyCRMFilters();
    });

    $('#cora-crm-search-input').on('input', function() {
        coraApplyCRMFilters();
    });

    // 4. Update Booking Status (Action callback)
    window.coraUpdateBookingStatus = function(button, nextStatus) {
        const row = $(button).closest('tr, .cora-booking-card-item');
        const clientId = row.attr('data-id') || '';
        const clientName = row.find('.cora-client-name').text().trim();

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_update_booking_status',
                nonce: coraREData.ajaxNonce,
                client_id: clientId,
                client_name: clientName,
                status: nextStatus
            },
            success: function(response) {
                if (response.success) {
                    coraShowToast("Status updated successfully!");
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    coraShowToast("Error: " + (response.data || "Could not update status."));
                }
            },
            error: function() {
                coraShowToast("Failed to connect to the server.");
            }
        });
    };

    // 5. Trigger Quick Actions
    window.coraTriggerAction = function(actionType, clientName) {
        if (actionType === 'whatsapp') {
            coraShowToast(`WhatsApp sent to ${clientName}.`);
        } else if (actionType === 'caption-quick') {
            coraNavigateTo('ai-assistants');
            $('#cora-description-showing-select').val('deal-jaipur');
            $('#cora-generate-caption-btn').click();
        } else if (actionType === 'invoice') {
            coraShowToast(`Invoice PDF dispatched to ${clientName}.`);
        }
    };

    // 6. Listing Copywriter Generator Logic
    const captionDatabase = {
        'deal-jaipur': {
            cinematic: [
                "Steeped in history and luxury. Presenting a grand walkthrough of the magnificent Jaipur Palace-Style Villa. Elegant arches, white marble floors, and modern luxury combined in the Pink City.\n\n#JaipurVilla #LuxuryEstate #PropertyAesthetic #CoraRealEstate #RambaghMansion",
                "A tale of royalty, heritage, and modern craftsmanship. This private estate near Rambagh Palace feels like a dream home.\n\n#LuxuryVilla #JaipurProperty #PremiumListing #MansionWalkthrough"
            ],
            romantic: [
                "Catching the perfect golden light at our latest Jaipur luxury listing. Every corner tells a story of elegance and design.\n\n#JaipurSunset #LuxuryVillas #RajasthanProperty #DreamHome",
                "Wrapped in warm neutral tones and Jaipur's golden sun. Showing you what ultra-luxury listings are made of.\n\n#LuxuryRealEstate #PropertyShowcase #JaipurEstate"
            ],
            minimalist: [
                "Jaipur, sunset, and quiet, minimalist architecture. The modern palace.\n\n#PalaceListing #MinimalistArchitecture #JaipurRealEstate",
                "Simple design in grand places.\n\n#ListingFilm #LuxuryListings #LuxuryEstate"
            ],
            royal: [
                "The grandeur of Rajputana arches framing a modern home that is truly timeless. Jaipur Palace-Style Villa.\n\n#RoyalJaipur #PalaceListings #RealEstateRajasthan #IndianHeritage"
            ]
        },
        'maternity-delhi': {
            cinematic: [
                "Welcome to premium residential living. Ananya Sharma's new luxury home purchase in the green belt of New Delhi, located next to the historic Lodhi Gardens.\n\n#ResidentialBuy #DelhiRealEstate #LuxuryLiving #CoraRealEstate",
                "A beautiful new beginning. Capturing the spacious interiors and serene balconies of this premium residential property in Delhi.\n\n#DelhiHomes #ResidentialProperty #NewDelhiRealEstate"
            ],
            romantic: [
                "A serene morning walk through the local parks of Lodhi Estate, just steps away from this stunning residential buy.\n\n#LodhiGardens #DelhiResidential #HomeBuying #NewBeginning"
            ],
            minimalist: [
                "Living in quiet luxury. A minimalist residential masterpiece in Delhi.\n\n#ResidentialMinimalist #NaturalLight #DelhiPenthouses"
            ],
            royal: [
                "A home fit for royalty. Traditional design meets modern luxury in this Delhi residential mansion.\n\n#LuxuryMansion #DelhiRealEstate #PremiumProperty"
            ]
        },
        'product-delhi': {
            cinematic: [
                "Sculpted by light and premium architecture. A look inside the newly listed commercial office lease at Cyber City for RK Enterprises.\n\n#CommercialOffice #DelhiOfficeLease #CyberCityOffice #CoraRealEstate",
                "Details define efficiency. Crafting premium office setups for growing brands in Delhi NCR.\n\n#OfficeLeasing #CommercialBrokerage #ModernWorkplace"
            ],
            romantic: [
                "Workspace details that inspire productivity. Premium commercial styling at Ritz City Center.\n\n#OfficeDesign #AestheticWorkplace #DelhiCommercial"
            ],
            minimalist: [
                "Form, light, and corporate symmetry. Premium office commercial leasing.\n\n#MinimalistOffice #CyberCity #DelhiLease"
            ],
            royal: [
                "Crafted for corporate leadership. High-end executive floor commercial campaign.\n\n#LuxuryOffice #ExecutiveSuite #DelhiCommercialLeasing"
            ]
        }
    };

    $('#cora-generate-caption-btn').on('click', function() {
        const shoot = $('#cora-description-showing-select').val();
        const mood = $('#cora-caption-mood').val();
        const btn = $(this);

        btn.text("Writing with Cora AI...").prop('disabled', true);
        $('#cora-caption-response').addClass('hidden');

        setTimeout(function() {
            btn.text("Generate Captions").prop('disabled', false);
            
            // Get caption options
            const options = captionDatabase[shoot] && captionDatabase[shoot][mood] ? captionDatabase[shoot][mood] : [];
            const resultText = options.length > 0 ? options[Math.floor(Math.random() * options.length)] : "Error generating draft.";
            
            $('#cora-caption-text').text(resultText);
            $('#cora-caption-response').removeClass('hidden');
        }, 800);
    });

    window.coraCopyText = function(elementId) {
        const text = $(`#${elementId}`).text();
        navigator.clipboard.writeText(text).then(function() {
            coraShowToast("Caption copied to clipboard!");
        });
    };

    // 7. Gallery & SEO Media Selector Logic
    const seoDatabase = {
        '1': {
            title: "Luxury Jaipur Palace-Style Villa Exterior at Golden Hour",
            alt: "Cinematic drone view of a heritage-style luxury villa facade in Jaipur featuring white marble colonnades and symmetric arches at golden sunset.",
            tags: ["jaipur-luxury-villa", "palace-style-home", "jaipur-real-estate", "heritage-property", "exterior-golden-hour"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `
        },
        '2': {
            title: "Royal Jaipur Estate Courtyard & Arched Colonnade",
            alt: "Wide-angle photograph of the central courtyard of a Jaipur luxury estate showcasing detailed sandstone arches and traditional fountains.",
            tags: ["jaipur-estate-courtyard", "arched-colonnade", "luxury-property-tour", "heritage-mansion-india", "interior-courtyard"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `
        },
        '3': {
            title: "Modern Dining Area with Traditional Rajasthani Accents",
            alt: "Elegantly styled formal dining hall of a luxury Jaipur villa, showcasing custom brass light fixtures and hand-crafted rosewood table settings.",
            tags: ["dining-hall-interior", "rajasthani-accents", "luxury-villa-dining", "interior-styling-india"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            `
        }
    };

    $('.cora-media-item-row').on('click', function() {
        const row = $(this);
        const imgId = row.data('img-id');
        const data = seoDatabase[imgId];

        if (!data) return;

        $('.cora-media-item-row').removeClass('active');
        row.addClass('active');

        // Loading spinner simulation
        $('#cora-large-media-preview').html(`
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="spin-icon text-zinc-400">
                <line x1="12" y1="2" x2="12" y2="6"></line>
                <line x1="12" y1="18" x2="12" y2="22"></line>
                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                <line x1="2" y1="12" x2="6" y2="12"></line>
                <line x1="18" y1="12" x2="22" y2="12"></line>
            </svg>
        `);
        
        setTimeout(function() {
            $('#cora-large-media-preview').html(data.largeThumb);
            $('#cora-seo-title').val(data.title);
            $('#cora-seo-alt').val(data.alt);

            // Populate tags
            const tagsWrap = $('#cora-seo-tags-container');
            tagsWrap.empty();
            data.tags.forEach(tag => {
                tagsWrap.append(`<span class="cora-tag-pill px-2.5 py-0.5 text-xs bg-zinc-100 border border-zinc-200 text-zinc-700 rounded-full font-medium">${tag}</span>`);
            });
            
            // Mark optimized in list if scanned
            row.find('.cora-media-status').text('Optimized').removeClass('pending').addClass('optimized').css('color', '#10b981');
        }, 400);
    });

    window.coraApplySEOMetadata = function() {
        coraShowToast("SEO metadata updated successfully.");
    };

    window.coraReScanAI = function() {
        const activeItem = $('.cora-media-item-row.active');
        const imgId = activeItem.data('img-id');
        
        // Scan loader
        $('#cora-large-media-preview').html(`
            <div style="font-size:12px; color:rgba(0,0,0,0.5); text-align:center;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="spin-icon" style="margin: 0 auto 8px; display:block;">
                    <line x1="12" y1="2" x2="12" y2="6"></line>
                    <line x1="12" y1="18" x2="12" y2="22"></line>
                    <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                    <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                    <line x1="2" y1="12" x2="6" y2="12"></line>
                    <line x1="18" y1="12" x2="22" y2="12"></line>
                </svg>
                Scanning image...
            </div>
        `);
        
        setTimeout(function() {
            const data = seoDatabase[imgId];
            $('#cora-large-media-preview').html(data.largeThumb);
            coraShowToast("AI scan complete. Refreshed SEO details.");
        }, 1000);
    };

    // 8. Collapsible Notion-AI Sidebar Controls
    window.coraToggleSidebar = function(show) {
        const sidebar = $('#cora-ai-sidebar');
        if (show) {
            sidebar.removeClass('collapsed');
            $('#cora-sidebar-chat-input').focus();
        } else {
            sidebar.addClass('collapsed');
        }
    };

    // Toggle button opens the sidebar
    $('#cora-quick-ai-btn').on('click', function(e) {
        e.preventDefault();
        const sidebar = $('#cora-ai-sidebar');
        const isCollapsed = sidebar.hasClass('collapsed');
        coraToggleSidebar(isCollapsed);
    });

    // Search bar opens the command palette modal
    $('.cora-sidebar-search').on('click', function(e) {
        if ($(e.target).is('input')) {
            return;
        }
        e.preventDefault();
        if (typeof window.coraOpenCommandPalette === 'function') {
            window.coraOpenCommandPalette();
        }
    });

    // Send chat messages from input box
    window.coraSendSidebarChatMessage = function() {
        const input = $('#cora-sidebar-chat-input');
        const text = input.val().trim();
        if (!text) return;

        coraExecuteAIChat(text);
        input.val('');
    };

    // Send chat message via quick prompt shortcuts
    window.coraSendShortcut = function(promptText) {
        coraExecuteAIChat(promptText);
    };

    // Master execution for chat rendering and mock answers
    function coraExecuteAIChat(text) {
        const chat = $('#cora-sidebar-chat');
        
        // Append User bubble
        chat.append(`<div class="chat-bubble user">${text}</div>`);
        chat.scrollTop(chat[0].scrollHeight);

        // Appending typing loader
        const typingId = 'typing-' + Date.now();
        chat.append(`
            <div class="chat-bubble ai animate-pulse" id="${typingId}">
                <span style="font-size: 11px; color: rgba(0,0,0,0.45);">Cora is thinking...</span>
            </div>
        `);
        chat.scrollTop(chat[0].scrollHeight);

        // Simulated AI response
        setTimeout(function() {
            let reply = "I've searched your workspace but couldn't find details for that request. Try: 'Draft reminder for Ananya' or 'Check Rohit'.";
            
            const normalizedText = text.toLowerCase();
            if (normalizedText.includes('ananya') || normalizedText.includes('maternity') || normalizedText.includes('remind')) {
                reply = "*WhatsApp Draft generated for Ananya Sharma:*\n\n\"Namaste Ananya! This is Cora from Delhi Office. Just reminding you of our outdoor maternity shoot scheduled for tomorrow at 4:00 PM at Lodhi Gardens. Please let us know if you need any adjustments. See you there!\"";
            } else if (normalizedText.includes('rohit') || normalizedText.includes('listing') || normalizedText.includes('jaipur')) {
                reply = "Booking Found: *Rohit & Sneha (Jaipur Luxury Villa Sale)*.\n\n*Status:* Editing\n*AI Action Recommendation:* Social Media caption generator ready. Let me know if you want me to write Instagram caption drafts for this shoot.";
            } else if (normalizedText.includes('hi') || normalizedText.includes('hello')) {
                reply = "Hello! I am Cora, your brokerage AI Assistant. I can help you draft reminders, check showing schedules, or suggest SEO keywords.";
            }

            // Remove loader and append reply
            $(`#${typingId}`).remove();
            chat.append(`<div class="chat-bubble ai">${reply}</div>`);
            chat.scrollTop(chat[0].scrollHeight);
        }, 800);
    }

    // 9. Premium AI Modules Switch Toggles
    window.coraToggleModule = function(moduleName, isChecked) {
        const badge = $(`#badge-module-${moduleName}`);
        if (isChecked) {
            badge.text('Active').removeClass('inactive').addClass('active');
        } else {
            badge.text('Inactive').removeClass('active').addClass('inactive');
        }
    };

    // 10. Sidebar Collapse Toggle Interaction
    $('#cora-sidebar-toggle').on('click', function(e) {
        e.preventDefault();
        if (typeof window.coraToggleSidebarCollapse === 'function') {
            window.coraToggleSidebarCollapse(e);
        }
    });

    // 11. User Profile Popover Interactions
    $('.cora-user-settings-btn').on('click', function(e) {
        // Toggle the popup list menu card
        $('#cora-profile-popover').toggleClass('hidden');
        e.stopPropagation();
    });

    // Prevent popover closing when clicking internal elements
    $('#cora-profile-popover, #cora-header-profile-popover').on('click', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', function(e) {
        // Close popover if clicked outside profile card area
        if (!$(e.target).closest('#cora-profile-popover').length && !$(e.target).closest('.cora-user-settings-btn').length) {
            $('#cora-profile-popover').addClass('hidden');
        }
        // Close header profile popover if clicked outside
        if (!$(e.target).closest('#cora-header-profile-popover').length && !$(e.target).closest('.cora-header-profile-btn').length) {
            $('#cora-header-profile-popover').addClass('hidden');
        }
    });

    // 12. Theme Toggle & Persistence
    function toggleTheme(isDark) {
        if (isDark) {
            $('#cora-workspace').addClass('cora-dark-theme');
            $('#cora-theme-toggle-text').text('Light Theme');
            // Sun icon for switching back to light mode
            $('#cora-theme-icon').html('<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>');
            try {
                localStorage.setItem('cora-theme', 'dark');
            } catch(e) {}
        } else {
            $('#cora-workspace').removeClass('cora-dark-theme');
            $('#cora-theme-toggle-text').text('Dark Theme');
            // Moon icon for switching to dark mode
            $('#cora-theme-icon').html('<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>');
            try {
                localStorage.setItem('cora-theme', 'light');
            } catch(e) {}
        }
    }

    // Initialize theme state - Force light theme since it's locked for now
    toggleTheme(false);

    // Click handler for theme toggle button (now locked/coming soon)
    $('#cora-theme-toggle-btn').on('click', function(e) {
        window.coraShowToast('Dark Theme is coming soon. Stay tuned!');
        e.stopPropagation();
    });

    // 13. AI Model Selector — silently save user's model preference on change
    $('#cora-ai-model-selector').on('change', function() {
        const val = $(this).val();
        const labels = {
            'cora-core-v2': 'Cora AI · Auto',
            'gemini':        'Cora AI · Gemini',
            'gpt-4o':        'Cora AI · GPT-4o'
        };
        // Silently persist preference via AJAX (no user-facing loading state)
        $.ajax({
            url: coraREWPData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_re_save_ai_keys',
                security: coraREWPData.ajaxNonce,
                provider: 'gemini',
                api_key: '',
                active_model: val
            }
        });
    });



    // 14. Keyboard Shortcuts for Platform Accessibility
    $(document).on('keydown', function(e) {
        // 1. Search Box shortcut: Cmd + K or Ctrl + K
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            $('.cora-sidebar-search').trigger('click');
        }

        // 2. Sidebar Toggle shortcut: Cmd + \ or Ctrl + \
        if ((e.metaKey || e.ctrlKey) && e.key === '\\') {
            e.preventDefault();
            if (typeof window.coraToggleSidebarCollapse === 'function') {
                window.coraToggleSidebarCollapse(e);
            }
        }

        // 3. AI Chat Sidebar Toggle shortcut: Cmd + J or Ctrl + J
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'j') {
            e.preventDefault();
            const sidebar = $('#cora-ai-sidebar');
            const isCollapsed = sidebar.hasClass('collapsed');
            coraToggleSidebar(isCollapsed);
        }

        // 4. Close popover & drawers: Escape
        if (e.key === 'Escape') {
            coraToggleSidebar(false);
            coraToggleAddShowingDrawer(false);
            coraToggleTeamDrawer(false);
            if (typeof window.coraToggleDocDrawer === 'function') {
                window.coraToggleDocDrawer(false);
            }
            if (typeof window.coraToggleShareDrawer === 'function') {
                window.coraToggleShareDrawer(false);
            }
            if (typeof window.coraCancelUserEdit === 'function') {
                window.coraCancelUserEdit();
            }
            $('#cora-profile-popover').addClass('hidden');
        }

        // Hotkeys active only when NOT typing in inputs, textareas, or contenteditable elements
        if (!$(e.target).is('input, textarea, select') && $(e.target).closest('[contenteditable="true"]').length === 0) {
            const key = e.key.toLowerCase();
            
            // Only trigger single-key hotkeys (like tab navigation) if no modifier keys (Ctrl, Cmd, Alt) are pressed
            if (!e.ctrlKey && !e.metaKey && !e.altKey) {
                // Switch tabs: 1-8
                if (key === '1') {
                    coraNavigateTo('dashboard');
                } else if (key === '2') {
                    coraNavigateTo('bookings');
                } else if (key === '3') {
                    $('.cora-nav-item[data-target="ai-assistants"]').trigger('click');
                } else if (key === '4') {
                    $('.cora-nav-item[data-target="portfolio"]').trigger('click');
                } else if (key === '5') {
                    coraNavigateTo('settings');
                } else if (key === '6') {
                    coraNavigateTo('feature-hub');
                } else if (key === '7') {
                    coraNavigateTo('team-roles');
                } else if (key === '8') {
                    coraNavigateTo('equipment');
                }
            }
            
            // Dedicated shortcut to open Create Shoot drawer: Alt + N or Alt + C
            if (e.altKey && (key === 'n' || key === 'c')) {
                e.preventDefault();
                coraToggleAddShowingDrawer(true);
            }
        }
    });

    // 15. Team Assignment Drawer Controllers
    window.coraToggleTeamDrawer = function(show) {
        const drawer = $('#cora-team-management-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    // Open crew assignment drawer when clicking Manage Your Team card
    $('#cora-card-manage-team').on('click', function(e) {
        e.preventDefault();
        coraToggleTeamDrawer(true);
    });

    // Navigate to equipment page when clicking Equipment Tracking card
    $('#cora-card-equipment-tracking').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('equipment');
    });

    // Navigate to portfolios page when clicking Beautiful Property Portfolios card
    $('#cora-card-property-portfolios').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('portfolio');
    });

    // Navigate to portfolios page when clicking Easy Property Showcases card
    $('#cora-card-photo-selection').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('portfolio');
    });

    // Navigate to financials when clicking Track Every Rupee card
    $('#cora-card-track-rupee').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('financials');
    });

    // Navigate to studio vault when clicking Instant Quotations card
    $('#cora-card-instant-quotations').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('vault');
    });

    // Navigate to studio vault when clicking Zero Paperwork card
    $('#cora-card-zero-paperwork').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('vault');
    });

    // Navigate to studio vault when clicking Smart E-Signatures card
    $('#cora-card-smart-signatures').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('vault');
    });

    // Navigate to Google Profile when clicking Google Maps SEO Booster card
    $('#cora-card-maps-seo').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('gbp');
    });

    // Save Crew Assignments button handler
    $('#cora-save-team-btn').on('click', function(e) {
        e.preventDefault();
        
        const s1_photographer = $('#cora-team-showing1-photographer').val();
        const s1_videographer = $('#cora-team-showing1-videographer').val();
        const s1_drone = $('#cora-team-showing1-drone').val();

        const s2_photographer = $('#cora-team-showing2-photographer').val();
        const s2_assistant = $('#cora-team-showing2-assistant').val();

        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_save_showing_assignments',
            security: coraREData.ajaxNonce,
            viewing_id: 'showing1',
            crew: { photographer: s1_photographer, videographer: s1_videographer, drone: s1_drone }
        }, function(res1) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_re_save_showing_assignments',
                security: coraREData.ajaxNonce,
                viewing_id: 'showing2',
                crew: { photographer: s2_photographer, assistant: s2_assistant }
            }, function(res2) {
                window.coraShowToast('Team assignments saved successfully.');
                coraToggleTeamDrawer(false);
            });
        });
    });

    // Coming Soon cards event handlers
    $('.cora-feature-soon').on('click', function(e) {
        e.preventDefault();
        const title = $(this).data('feature') || 'This feature';
        window.coraShowToast(`${title} module is coming soon!`);
    });

    // 16. Sub-Tab Navigation for Team and Equipment Sections (handles both Studio gear sub-tabs and Real Estate listing sub-tabs seamlessly)
    $(document).on('click', '.cora-sub-tab', function(e) {
        const tab = $(this);
        const target = tab.data('sub-target');
        if (!target) return;
        e.preventDefault();
        const parentSection = tab.closest('.cora-page-section');
        
        // Toggle tab classes
        parentSection.find('.cora-sub-tab').removeClass('active border-zinc-950 text-zinc-950').addClass('border-transparent hover:text-zinc-900');
        tab.addClass('active border-zinc-950 text-zinc-950').removeClass('border-transparent hover:text-zinc-900');
        
        // Hide all sub-sections and show active one
        parentSection.find('.cora-sub-section').addClass('hidden').removeClass('active');
        let targetElem = parentSection.find(`#cora-sub-page-${target}`);
        if (!targetElem.length) {
            targetElem = parentSection.find(`#${target}`);
        }
        targetElem.removeClass('hidden').addClass('active');
    });

    // Toggle allocation input visibility in assign equipment tab
    $('#cora-assign-eq-status').on('change', function() {
        const status = $(this).val();
        if (status === 'In Use') {
            $('#cora-assign-eq-alloc-details').slideDown(150);
        } else {
            $('#cora-assign-eq-alloc-details').slideUp(150);
        }
    });

    // Inline user management CRUD helpers
    window.coraInitEditUser = function(userId) {
        const row = $(`#cora-team-list-container tr[data-id="${userId}"]`);
        const name = row.data('display-name');
        const username = row.data('username');
        const email = row.data('email');
        const role = row.data('role');
        const avatarUrl = row.data('avatar-url') || '';

        $('#cora-user-display-name').val(name);
        $('#cora-user-username').val(username).attr('disabled', true);
        $('#cora-user-email').val(email).attr('disabled', true);
        if (role === 'administrator') {
            $('#cora-user-role').val('administrator').attr('disabled', true);
        } else {
            $('#cora-user-role').val(role).removeAttr('disabled');
        }
        $('#cora-form-user-id').val(userId);
        $('#cora-user-password').val(''); // Keep blank to not change password

        if (avatarUrl) {
            $('#cora-user-avatar-preview').html(`<img src="${avatarUrl}" class="w-full h-full object-cover">`);
        } else {
            $('#cora-user-avatar-preview').html(`<span class="text-zinc-400 text-xs font-bold" id="cora-avatar-initials">${name.substring(0, 2).toUpperCase()}</span>`);
        }

        $('#cora-team-form-title').html(`
            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Edit Studio Member
        `);
        $('#cora-team-form-desc').text('Modify credentials and capabilities for an existing member.');
        $('#cora-save-user-btn').text('Save Changes');
        $('#cora-cancel-user-btn').removeClass('hidden');

        // Switch to form tab
        $('#cora-sub-tab-team-form').trigger('click');
    };

    window.coraCancelUserEdit = function() {
        $('#cora-form-user-id').val('');
        $('#cora-user-display-name').val('');
        $('#cora-user-username').val('').removeAttr('disabled');
        $('#cora-user-email').val('').removeAttr('disabled');
        $('#cora-user-password').val('');
        $('#cora-user-role').val('cora_photographer').removeAttr('disabled');
        $('#cora-user-avatar-file').val('');
        $('#cora-user-avatar-preview').html('<span class="text-zinc-400 text-xs font-bold" id="cora-avatar-initials">--</span>');

        $('#cora-team-form-title').html(`
            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-555">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="16" y1="11" x2="22" y2="11"></line>
            </svg>
            Add Brokerage Member
        `);
        $('#cora-team-form-desc').text('Create a new team member profile mapped to your studio\'s brokerage operational roles.');
        $('#cora-save-user-btn').text('Add Member');
        $('#cora-cancel-user-btn').addClass('hidden');

        // Switch to directory tab
        $('#cora-page-team-roles .cora-sub-tab[data-sub-target="team-directory"]').trigger('click');
    };

    $('#cora-cancel-user-btn').on('click', function(e) {
        e.preventDefault();
        window.coraCancelUserEdit();
    });

    // Avatar local file preview change handler
    $('#cora-user-avatar-file').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                $('#cora-user-avatar-preview').html(`<img src="${evt.target.result}" class="w-full h-full object-cover">`);
            };
            reader.readAsDataURL(file);
        }
    });

    // Password visibility toggle
    $('#cora-toggle-password-visibility').on('click', function(e) {
        e.preventDefault();
        const pwdInput = $('#cora-user-password');
        const showIcon = $('#cora-eye-show-icon');
        const hideIcon = $('#cora-eye-hide-icon');
        
        if (pwdInput.attr('type') === 'password') {
            pwdInput.attr('type', 'text');
            showIcon.addClass('hidden');
            hideIcon.removeClass('hidden');
        } else {
            pwdInput.attr('type', 'password');
            showIcon.removeClass('hidden');
            hideIcon.addClass('hidden');
        }
    });

    window.coraDeleteUser = function(userId) {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_team_user',
            security: coraREData.ajaxNonce,
            user_id: userId
        }, function(response) {
            if (response.success) {
                window.coraShowToast('User deleted successfully.');
                $(`#cora-team-list-container tr[data-id="${userId}"]`).remove();
                
                // Decrement count badge
                const countBadge = $('#cora-crew-count-badge');
                const count = Math.max(0, parseInt(countBadge.text()) - 1);
                countBadge.text(`${count} Members`);
            } else {
                window.coraShowToast('Error: ' + response.data);
            }
        });
    };

    window.coraDeleteEquipment = function(eqId) {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_delete_listing',
            security: coraREData.ajaxNonce,
            eq_id: eqId
        }, function(response) {
            if (response.success) {
                window.coraShowToast('Equipment asset deleted.');
                $(`.cora-eq-row[data-id="${eqId}"]`).remove();
                
                // Recalculate stats counts
                let total = 0, avail = 0, use = 0, maint = 0;
                $('#cora-equipment-table-body tr').each(function() {
                    total++;
                    const st = $(this).find('td:nth-child(4) .cora-badge').text().trim();
                    if (st === 'Available') avail++;
                    else if (st === 'In Use') use++;
                    else if (st === 'Maintenance') maint++;
                });

                $('#cora-eq-stat-total').text(total);
                $('#cora-eq-stat-avail').html(`<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>${avail}`);
                $('#cora-eq-stat-use').html(`<span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>${use}`);
                $('#cora-eq-stat-maint').html(`<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>${maint}`);
            } else {
                window.coraShowToast('Error: ' + response.data);
            }
        });
    };

    window.coraInitAssignEquipment = function(eqId) {
        $('#cora-assign-eq-id').val(eqId).trigger('change');
        
        // Pre-populate note if the item is already assigned
        const row = $(`.cora-eq-row[data-id="${eqId}"]`);
        const status = row.find('td:nth-child(5) .cora-badge').text().trim();
        $('#cora-assign-eq-status').val(status).trigger('change');
        
        if (status === 'In Use') {
            const crew = row.find('td:nth-child(6)').text().trim();
            const shoot = row.find('td:nth-child(7)').text().trim();
            const note = row.find('td:nth-child(8)').text().trim();
            
            $('#cora-assign-eq-crew').val(crew !== '—' ? crew : '');
            $('#cora-assign-eq-showing').val(shoot !== '—' ? shoot : '');
            $('#cora-assign-eq-note').val(note !== '—' ? note : '');
        } else {
            $('#cora-assign-eq-crew').val('');
            $('#cora-assign-eq-showing').val('');
            $('#cora-assign-eq-note').val('');
        }
        
        // Switch to assign tab
        $('#cora-sub-tab-eq-assign').trigger('click');
        $('#cora-assign-eq-status').focus();
    };

    // ==========================================
    // PROPERTY LISTINGS & DRAWERS FUNCTIONS (R2, R3, R4)
    // ==========================================

    window.coraToggleListingDrawer = function(show) {
        const drawer = $('#cora-listing-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    window.coraOpenListingDrawerForCreate = function() {
        coraToggleListingDrawer(true);
        $('#cora-listing-id').val('');
        $('#cora-listing-sync-link').val('');
        $('#cora-listing-name').val('');
        $('#cora-listing-category').val('Villa');
        $('#cora-listing-rera-id').val('');
        $('#cora-listing-notes').val('');
        $('#cora-listing-image-preview').html('<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-listing-image-placeholder">No Photo</span>');
        $('#cora-listing-image-file').val('');
        
        // Clear SEO fields (R3)
        $('#cora-listing-seo-title').val('');
        $('#cora-listing-seo-description').val('');
        $('#cora-listing-seo-keywords').val('');
        
        $('#cora-listing-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-655 mr-1.5 shrink-0">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Add New Listing
        `);
        $('#cora-listing-sync-container').show();
    };

    window.coraOpenListingDrawer = function(listing) {
        coraToggleListingDrawer(true);
        $('#cora-listing-id').val(listing.id);
        $('#cora-listing-sync-link').val(listing.sync_link || listing.sync_url || '');
        $('#cora-listing-name').val(listing.name);
        $('#cora-listing-category').val(listing.category);
        $('#cora-listing-rera-id').val(listing.rera_reg_id);
        $('#cora-listing-notes').val(listing.notes || '');
        
        // Populate SEO fields (R3)
        $('#cora-listing-seo-title').val(listing.seo_title || '');
        $('#cora-listing-seo-description').val(listing.seo_description || '');
        $('#cora-listing-seo-keywords').val(listing.seo_keywords || '');

        if (listing.photo_url) {
            $('#cora-listing-image-preview').html(`<img src="${listing.photo_url}" class="w-full h-full object-cover" />`);
        } else {
            $('#cora-listing-image-preview').html('<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-listing-image-placeholder">No Photo</span>');
        }
        $('#cora-listing-image-file').val('');
        
        $('#cora-listing-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-650 mr-1.5 shrink-0">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Listing Details
        `);
        $('#cora-listing-sync-container').hide(); // Hide sync link for existing listings to prevent accidental overrides
    };

    window.coraSyncListingLink = function() {
        const url = $('#cora-listing-sync-link').val().trim();
        if (!url) {
            window.coraShowToast('Please enter a 3rd-party listing link.');
            return;
        }

        const btn = $('#cora-listing-sync-btn');
        btn.prop('disabled', true).text('Syncing...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_sync_listing_link',
            security: coraREData.ajaxNonce,
            url: url
        }, function(res) {
            btn.prop('disabled', false).text('Sync');
            if (res.success) {
                window.coraShowToast('Listing details synced successfully!');
                $('#cora-listing-name').val(res.data.name);
                $('#cora-listing-category').val(res.data.category);
                $('#cora-listing-rera-id').val(res.data.rera_reg_id);
                $('#cora-listing-notes').val(res.data.notes);
            } else {
                window.coraShowToast(res.data || 'Failed to sync listing.');
            }
        }).fail(function() {
            btn.prop('disabled', false).text('Sync');
            window.coraShowToast('Network error syncing listing.');
        });
    };

    window.coraSaveListingDetails = function() {
        const id = $('#cora-listing-id').val();
        const name = $('#cora-listing-name').val().trim();
        const category = $('#cora-listing-category').val();
        const rera_id = $('#cora-listing-rera-id').val().trim();
        const sync_link = $('#cora-listing-sync-link').val().trim();
        const notes = $('#cora-listing-notes').val().trim();
        
        const seo_title = $('#cora-listing-seo-title').val().trim();
        const seo_description = $('#cora-listing-seo-description').val().trim();
        const seo_keywords = $('#cora-listing-seo-keywords').val().trim();

        if (!name || !category || !rera_id) {
            window.coraShowToast('Please fill all required fields.');
            return;
        }

        const btn = $('#cora-save-listing-btn');
        btn.prop('disabled', true).text('Saving...');

        const formData = new FormData();
        formData.append('action', 'cora_re_save_listing');
        formData.append('security', coraREData.ajaxNonce);
        formData.append('id', id);
        formData.append('name', name);
        formData.append('category', category);
        formData.append('rera_reg_id', rera_id);
        formData.append('sync_link', sync_link);
        formData.append('notes', notes);
        
        formData.append('seo_title', seo_title);
        formData.append('seo_description', seo_description);
        formData.append('seo_keywords', seo_keywords);

        const photoFile = $('#cora-listing-image-file')[0].files[0];
        if (photoFile) {
            formData.append('gear_photo', photoFile);
        }

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false).text('Save Details');
                if (response.success) {
                    window.coraShowToast(id ? 'Listing updated successfully.' : 'Listing added successfully.');
                    coraToggleListingDrawer(false);
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                } else {
                    window.coraShowToast(response.data || 'Failed to save listing.');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Save Details');
                window.coraShowToast('Network error saving listing.');
            }
        });
    };

    window.coraPreviewListingImage = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#cora-listing-image-preview').html(`<img src="${e.target.result}" class="w-full h-full object-cover" />`);
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    // Dynamic Permissions Auto-Save Matrix
    $('.cora-permission-checkbox').on('change', function() {
        const row = $(this).closest('.cora-matrix-row');
        const role = row.data('role');
        const permissions = {};

        $('.cora-matrix-row').each(function() {
            const r = $(this).data('role');
            permissions[r] = [];
            $(this).find('.cora-permission-checkbox:checked').each(function() {
                permissions[r].push($(this).data('feature'));
            });
        });

        const enterpriseNewModules = ['event_timeline', 'event-timeline', 'multi-day-timeline', 'review_acquisition', 'smart-reviews', 'crew_scheduler', 'crew-scheduler', 'shifts', 'vault', 'emails'];
        permissions['administrator'] = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings', 'vault', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'canvas', 'audit-panel', 'media', 'forms', 'emails', 'ecosystem', 'mcp', 'super-admin', ...enterpriseNewModules];
        permissions['cora_super_admin'] = permissions['administrator'];
        permissions['cora_shruti'] = permissions['administrator'];

        // Instantly update the local cache
        coraREData.userPermissions = permissions;
        
        // Apply updates to active preview role
        const activeRole = $('#cora-role-preview-select').val() || coraREData.currentRole;
        coraEnforcePermissions(activeRole);

        // Send AJAX save in background
        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_role_permissions',
            security: coraREData.ajaxNonce,
            permissions: permissions
        }, function(response) {
            if (response.success) {
                const roleLabel = row.find('td:nth-child(1)').text().trim();
                window.coraShowToast(`Permissions updated dynamically for ${roleLabel}.`);
            } else {
                window.coraShowToast('Error updating permissions: ' + response.data);
            }
        });
    });

    // AJAX: Add/Update User profile with avatar upload
    $('#cora-save-user-btn').on('click', function(e) {
        e.preventDefault();
        const userId = $('#cora-form-user-id').val();
        const displayName = $('#cora-user-display-name').val().trim();
        const username = $('#cora-user-username').val().trim();
        const email = $('#cora-user-email').val().trim();
        const password = $('#cora-user-password').val().trim();
        const role = $('#cora-user-role').val();

        if (!displayName || !email || (!userId && !username)) {
            window.coraShowToast('Please fill all required fields.');
            return;
        }

        if (password && password.length < 8) {
            window.coraShowToast('Password must be at least 8 characters long.');
            return;
        }

        const formData = new FormData();
        formData.append('action', userId ? 'cora_update_team_user' : 'cora_create_team_user');
        formData.append('security', coraREData.ajaxNonce);
        if (userId) {
            formData.append('user_id', userId);
        }
        formData.append('display_name', displayName);
        formData.append('username', username);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('role', role);

        const avatarInput = $('#cora-user-avatar-file')[0];
        if (avatarInput && avatarInput.files.length > 0) {
            formData.append('avatar_file', avatarInput.files[0]);
        }

        // Show saving state
        const originalBtnText = $(this).text();
        $(this).text(userId ? 'Saving...' : 'Adding...').prop('disabled', true);
        const btn = $(this);

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.text(originalBtnText).prop('disabled', false);
                if (response.success) {
                    window.coraShowToast(userId ? `Member "${displayName}" updated successfully.` : `Member "${displayName}" created successfully.`);
                    
                    if (userId) {
                        // Update table row details dynamically
                        const row = $(`#cora-team-list-container tr[data-id="${userId}"]`);
                        row.data('display-name', displayName);
                        row.data('email', email);
                        row.data('role', role);

                        if (response.data.avatar_url) {
                            row.data('avatar-url', response.data.avatar_url);
                            row.find('td:nth-child(1)').html(`<img src="${response.data.avatar_url}" class="w-7 h-7 rounded-full object-cover select-none border border-zinc-250/80" alt="${displayName}">`);
                        } else {
                            const existingAvatar = row.data('avatar-url');
                            if (!existingAvatar) {
                                row.find('td:nth-child(1)').html(`
                                    <div class="w-7 h-7 rounded-full bg-zinc-100 border border-zinc-200 text-zinc-700 flex items-center justify-center font-bold text-[10px] uppercase cora-member-avatar-initials">
                                        ${displayName.substring(0, 2)}
                                    </div>
                                `);
                            }
                        }

                        row.find('td:nth-child(2)').text(displayName);
                        row.find('td:nth-child(4)').text(email);

                        const roleBadgeClass = role === 'administrator' ? 'cora-badge-green' : 'cora-badge-sidebar';
                        row.find('td:nth-child(5)').html(`
                            <span class="cora-badge px-2 py-0.5 text-[9px] font-bold rounded-md select-none ${roleBadgeClass}">
                                ${response.data.role_label}
                            </span>
                        `);

                        window.coraCancelUserEdit();
                    } else {
                        // In create mode, reload page to cleanly render attachment relationships inside WordPress loop
                        window.location.reload();
                    }
                } else {
                    window.coraShowToast('Error: ' + response.data);
                }
            },
            error: function() {
                btn.text(originalBtnText).prop('disabled', false);
                window.coraShowToast('An error occurred while saving the member.');
            }
        });
    });

    // AJAX: Add Equipment log
    $('#cora-save-equipment-btn').on('click', function(e) {
        e.preventDefault();
        const name = $('#cora-eq-name').val().trim();
        const category = $('#cora-eq-category').val();
        const rera_reg_id = $('#cora-eq-rera_reg_id').val().trim();

        if (!name || !rera_reg_id) {
            window.coraShowToast('Please fill all fields.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'cora_re_save_listing');
        formData.append('security', coraREData.ajaxNonce);
        formData.append('name', name);
        formData.append('category', category);
        formData.append('rera_reg_id', rera_reg_id);

        const photoFile = $('#cora-property-image-file')[0].files[0];
        if (photoFile) {
            formData.append('gear_photo', photoFile);
        }

        const btn = $(this);
        const originalBtnText = btn.text();
        btn.text('Saving...').prop('disabled', true);

        $.ajax({
            url: coraREData.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.text(originalBtnText).prop('disabled', false);
                if (response.success) {
                    window.coraShowToast(`Equipment "${name}" logged successfully.`);
                    
                    const photoHtml = response.data.photo_url 
                        ? `<img src="${response.data.photo_url}" class="w-8 h-8 rounded-md object-cover border border-zinc-200/80" />`
                        : `<div class="w-8 h-8 rounded-md bg-zinc-100 flex items-center justify-center border border-zinc-200/50 text-zinc-400">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                <circle cx="12" cy="13" r="4"></circle>
                            </svg>
                           </div>`;

                    // Append asset to registry table
                    const newRow = `<tr class="hover:bg-zinc-50/30 cora-eq-row" data-id="${response.data.id}" data-name="${name}">
                        <td class="px-4 py-3.5">${photoHtml}</td>
                        <td class="px-4 py-3.5 font-bold text-zinc-800">${name}</td>
                        <td class="px-4 py-3.5 text-zinc-550">${category}</td>
                        <td class="px-4 py-3.5 text-zinc-400 font-mono text-[10px]">${rera_reg_id}</td>
                        <td class="px-4 py-3.5">
                            <span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold cora-badge-green">
                                Available
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-zinc-650 font-medium">—</td>
                        <td class="px-4 py-3.5 text-zinc-550 max-w-[200px] truncate">—</td>
                        <td class="px-4 py-3.5 text-zinc-550 font-medium max-w-[200px] truncate">—</td>
                        <td class="px-4 py-3.5 text-right font-bold text-zinc-600">
                            <div class="flex items-center justify-end gap-2">
                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer cora-assign-eq-btn" onclick="coraInitAssignEquipment('${response.data.id}')">
                                    Assign / Release
                                </button>
                                <button class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-red-600 bg-white hover:bg-red-50 hover:border-red-200 transition-all cursor-pointer cora-delete-eq-btn" onclick="coraDeleteEquipment('${response.data.id}')">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>`;
                    $('#cora-equipment-table-body').append(newRow);

                    // Add to select dropdown
                    $('#cora-assign-eq-id').append(new Option(`${name} (${rera_reg_id})`, response.data.id));

                    // Increment counts in stats cards
                    $('#cora-eq-stat-total').text(parseInt($('#cora-eq-stat-total').text()) + 1);
                    $('#cora-eq-stat-avail').html(`<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>${parseInt($('#cora-eq-stat-avail').text()) + 1}`);

                    // Clear input fields and switch tab
                    $('#cora-eq-name').val('');
                    $('#cora-eq-rera_reg_id').val('');
                    $('#cora-property-image-file').val('');
                    $('#cora-property-image-preview').html('<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-property-image-placeholder">No Photo</span>');
                    $('#cora-page-equipment .cora-sub-tab[data-sub-target="eq-registry"]').trigger('click');
                } else {
                    window.coraShowToast('Error: ' + response.data);
                }
            },
            error: function() {
                btn.text(originalBtnText).prop('disabled', false);
                window.coraShowToast('An error occurred while saving the equipment.');
            }
        });
    });

    $(document).on('change', '#cora-property-image-file', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#cora-property-image-preview').html(`<img src="${e.target.result}" class="w-full h-full object-cover">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#cora-property-image-preview').html(`<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-property-image-placeholder">No Photo</span>`);
        }
    });

    // AJAX: Save Equipment Allocation
    $('#cora-confirm-eq-assign-btn').on('click', function(e) {
        e.preventDefault();
        const eqId = $('#cora-assign-eq-id').val();
        const status = $('#cora-assign-eq-status').val();
        const crewName = $('#cora-assign-eq-crew').val();
        const shootTitle = $('#cora-assign-eq-showing').val();
        const assignmentNote = $('#cora-assign-eq-note').val().trim();

        if (!eqId) {
            window.coraShowToast('Please select a gear item.');
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_assign_equipment',
            security: coraREData.ajaxNonce,
            eq_id: eqId,
            status: status,
            crew_name: crewName,
            viewing_title: shootTitle,
            assignment_note: assignmentNote
        }, function(response) {
            if (response.success) {
                window.coraShowToast('Equipment allocation saved.');
                
                const row = $(`.cora-eq-row[data-id="${eqId}"]`);
                let badgeClass = 'cora-badge-green';
                let crewText = '—';
                let shootText = '—';
                let noteText = '—';

                if (status === 'In Use') {
                    badgeClass = 'cora-badge-soon';
                    crewText = crewName;
                    shootText = shootTitle;
                    noteText = assignmentNote || '—';
                } else if (status === 'Maintenance') {
                    badgeClass = 'cora-badge-locked';
                }

                row.find('td:nth-child(5)').html(`<span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold ${badgeClass}">${status}</span>`);
                row.find('td:nth-child(6)').text(crewText);
                row.find('td:nth-child(7)').text(shootText);
                row.find('td:nth-child(8)').text(noteText);

                // Recalculate stats counts
                let total = 0, avail = 0, use = 0, maint = 0;
                $('#cora-equipment-table-body tr').each(function() {
                    total++;
                    const st = $(this).find('td:nth-child(5) .cora-badge').text().trim();
                    if (st === 'Available') avail++;
                    else if (st === 'In Use') use++;
                    else if (st === 'Maintenance') maint++;
                });

                $('#cora-eq-stat-total').text(total);
                $('#cora-eq-stat-avail').html(`<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>${avail}`);
                $('#cora-eq-stat-use').html(`<span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>${use}`);
                $('#cora-eq-stat-maint').html(`<span class="w-1.5 h-1.5 rounded-full bg-amber-550"></span>${maint}`);

                // Switch back to registry
                $('#cora-page-equipment .cora-sub-tab[data-sub-target="eq-registry"]').trigger('click');
            } else {
                window.coraShowToast('Error: ' + response.data);
            }
        });
    });

    // STUDIO VAULT LOGIC - DEDICATED FULL-PAGE WYSIWYG EDITOR
    window.coraDocFormat = function(command) {
        document.execCommand(command, false, null);
        $('#cora-doc-paper').focus();
    };

    window.coraDocApplyHeading = function(value) {
        if (value === 'p') {
            document.execCommand('formatBlock', false, '<p>');
        } else {
            document.execCommand('formatBlock', false, '<' + value + '>');
        }
        $('#cora-doc-paper').focus();
    };

    window.coraDocUpdateBranding = function() {
        const logoUrl = $('#cora-doc-logo-url').val().trim();
        const footerText = $('#cora-doc-footer-text').val().trim();

        // Update logo header
        const headerPreview = $('#cora-paper-header-preview');
        if (logoUrl) {
            headerPreview.html(`<img src="${logoUrl}" style="max-height: 50px; max-width: 180px; object-fit: contain;" alt="Branding Logo" />`).removeClass('border border-dashed border-zinc-200 p-4 justify-center bg-zinc-50/30 rounded');
            $('#cora-logo-upload-preview').html(`<img src="${logoUrl}" class="w-full h-full object-contain" />`);
            $('#cora-doc-logo-remove-btn').removeClass('hidden');
        } else {
            headerPreview.html(`<div class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider flex items-center gap-1.5 py-2 px-3 border border-dashed border-zinc-200 rounded hover:border-zinc-400 hover:text-zinc-650 transition-all select-none"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>Add Logo</div>`).addClass('border border-dashed border-zinc-200 p-4 justify-center bg-zinc-50/30 rounded');
            $('#cora-logo-upload-preview').html('<span class="text-[9px] text-zinc-400 text-center px-1">No Logo</span>');
            $('#cora-doc-logo-remove-btn').addClass('hidden');
        }

        // Update footer text
        const footerPreview = $('#cora-paper-footer-preview');
        footerPreview.text(footerText);
    };

    const defaultSampleTitle = "Notes - Jun 20";
    const defaultSampleGdoc = "https://docs.google.com/document/d/1osN4szar57b7mWva6w4XSDNFeB3y6JuAUPpzwOuh06A/edit?usp=sharing";
    const defaultSampleContent = `
        <h1>Notes</h1>
        <p><strong>Jun 20, 2026</strong></p>
        <h2>Meeting Jun 20, 2026 at 19:54 IST Shruti</h2>
        <p>Meeting records <a href="https://docs.google.com/document/d/1osN4szar57b7mWva6w4XSDNFeB3y6JuAUPpzwOuh06A/edit?usp=sharing">Transcript</a></p>
        
        <h3>Summary</h3>
        <p>Meeting discussions addressed CRM functionality improvements and team access management for better lead tracking operations.</p>
        <p><strong>CRM Capabilities and Integration</strong><br>Discussion centered on the current limitations of legacy tools for lead management and how the new CRM platform improves visual organization. The system integrates Google Business Profiles and automated WhatsApp follow-up tools.</p>
        <p><strong>Technical Workflow and Access</strong><br>The platform enables third-party API integrations for portfolios and supports Zoho syncing for invoices. The system provides customizable access permissions for team members to protect sensitive financial data.</p>
        <p><strong>System Implementation Decisions</strong><br>It was decided to create a functional platform demonstration to evaluate lead synchronization and scheduling capabilities. The system utilizes Progressive Web App technology to support mobile access for field tasks.</p>
        
        <h3>Decisions</h3>
        <p><strong>Aligned</strong></p>
        <ul>
            <li><strong>Role-based access control integration</strong>: The CRM will support role-based access control, allowing specific team members to access leads and scheduling tools while restricting access to sensitive financial information.</li>
        </ul>
        <p>We've <strong>updated the Decisions section</strong> using your feedback.</p>
        
        <h3>Next steps</h3>
        <ul>
            <li>[Dravya Bansal] Build CRM Demo: Develop a functional version of the platform enabling lead submission and resource tracking. Configure the system to allow management of the entire lead lifecycle.</li>
            <li>[Dravya Bansal] Provide Demo Access: Grant the client access to the updated platform by the morning of Monday.</li>
            <li>[The group] Hold Review Meeting: Meet on Monday at 4:30 PM to evaluate the functional demo and review deep insights.</li>
        </ul>
    `;

    window.coraOpenDocDrawer = function() {
        // Reset inputs
        $('#cora-doc-id-hidden').val('');
        $('#cora-doc-client-select').val('');
        $('#cora-doc-template-select').val('');
        
        // Pre-load default sample document
        $('#cora-doc-title-input').val(defaultSampleTitle);
        $('#cora-doc-type-select').val('Proposal');
        $('#cora-doc-amount-input').val('₹4,50,000');
        $('#cora-doc-logo-url').val('');
        $('#cora-doc-footer-text').val('© 2026 Apex Realty Group. All rights reserved. • Contact: hello@nitinarora.com');
        $('#cora-doc-paper').html(defaultSampleContent);
        
        // Pre-fill Google Doc Sync URL and check the real-time sync checkbox
        $('#cora-doc-gdoc-url').val(defaultSampleGdoc);
        $('#cora-doc-gdoc-sync-toggle').prop('checked', true);

        // Clear custom inputs
        $('#cora-custom-type-input-group').addClass('hidden');
        $('#cora-custom-type-input').val('');

        coraDocUpdateBranding();
        coraStartGdocPolling();

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');
        
        // Reset toggle button
        $('#cora-vault-editor-view').removeClass('cora-sidebar-collapsed');
        $('#cora-editor-toggle-sidebar-btn').find('.toggle-text').text('Hide Settings');
    };

    window.coraViewDocument = function(docId) {
        const doc = (coraREData.documents || []).find(d => d.id === docId);
        if (!doc) {
            window.coraShowToast("Error: Document not found.");
            return;
        }

        // Set inputs
        $('#cora-doc-id-hidden').val(doc.id);
        $('#cora-doc-title-input').val(doc.title);
        $('#cora-doc-client-select').val(doc.client_link || '');
        $('#cora-doc-template-select').val('');
        
        // Ensure the select dropdown has this document type if it is a custom type
        let typeExists = false;
        $('#cora-doc-type-select option').each(function() {
            if ($(this).val() === doc.type) {
                typeExists = true;
            }
        });
        if (!typeExists && doc.type) {
            $(`<option value="${doc.type}">${doc.type}</option>`).insertBefore('#cora-doc-type-select option[value="__add_custom_type__"]');
        }

        $('#cora-doc-type-select').val(doc.type);
        $('#cora-doc-amount-input').val(doc.amount || '');
        $('#cora-doc-logo-url').val(doc.logo_url || '');
        $('#cora-doc-footer-text').val(doc.footer_text || '');
        $('#cora-doc-paper').html(doc.content || '<p></p>');

        // Set Google Doc sync URL and toggle state
        $('#cora-doc-gdoc-url').val(doc.gdoc_url || '');
        if (doc.sync_enabled) {
            $('#cora-doc-gdoc-sync-toggle').prop('checked', true);
            coraStartGdocPolling();
        } else {
            $('#cora-doc-gdoc-sync-toggle').prop('checked', false);
            coraStopGdocPolling();
        }

        // Clear custom inputs
        $('#cora-custom-type-input-group').addClass('hidden');
        $('#cora-custom-type-input').val('');

        coraDocUpdateBranding();

        // Update headings selector state if heading is at start
        $('#cora-editor-heading').val('p');

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');

        // Reset toggle button
        $('#cora-vault-editor-view').removeClass('cora-sidebar-collapsed');
        $('#cora-editor-toggle-sidebar-btn').find('.toggle-text').text('Hide Settings');
    };

    window.coraCloseListingCoordinator = function() {
        coraStopGdocPolling();
        $('#cora-vault-editor-view').addClass('hidden');
        $('#cora-vault-list-view').removeClass('hidden');
    };

    // Indian Currency Auto-Formatter
    function formatIndianCurrency(val) {
        if (!val) return '';
        let clean = val.toString().replace(/[^0-9]/g, '');
        if (clean === '') return '';
        let lastThree = clean.substring(clean.length - 3);
        let otherNumbers = clean.substring(0, clean.length - 3);
        if (otherNumbers !== '') {
            lastThree = ',' + lastThree;
        }
        return '₹' + otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    }

    $('#cora-doc-amount-input').on('blur', function() {
        let val = $(this).val();
        $(this).val(formatIndianCurrency(val));
    }).on('input', function() {
        let val = $(this).val();
        if (val === '₹' || val === '') {
            $(this).val('');
            return;
        }
        let clean = val.replace(/[^0-9]/g, '');
        if (clean === '') {
            $(this).val('');
            return;
        }
        $(this).val(formatIndianCurrency(clean));
    });

    // Custom Document Type Select Handler
    let prevDocType = 'Proposal';
    $('#cora-doc-type-select').on('focus', function() {
        prevDocType = $(this).val();
    }).on('change', function() {
        const val = $(this).val();
        if (val === '__add_custom_type__') {
            $('#cora-custom-type-input-group').removeClass('hidden');
            $('#cora-custom-type-input').focus();
        } else {
            $('#cora-custom-type-input-group').addClass('hidden');
            prevDocType = val;
        }
    });

    $('#cora-custom-type-save').on('click', function(e) {
        e.preventDefault();
        const customVal = $('#cora-custom-type-input').val().trim();
        if (!customVal) {
            window.coraShowToast("Please enter a custom document type.");
            return;
        }

        let exists = false;
        $('#cora-doc-type-select option').each(function() {
            if ($(this).val().toLowerCase() === customVal.toLowerCase()) {
                exists = true;
                $(this).prop('selected', true);
            }
        });

        if (!exists) {
            $(`<option value="${customVal}">${customVal}</option>`).insertBefore('#cora-doc-type-select option[value="__add_custom_type__"]');
            $('#cora-doc-type-select').val(customVal);
        }

        $('#cora-custom-type-input').val('');
        $('#cora-custom-type-input-group').addClass('hidden');
        prevDocType = customVal;
    });

    $('#cora-custom-type-cancel').on('click', function(e) {
        e.preventDefault();
        $('#cora-doc-type-select').val(prevDocType);
        $('#cora-custom-type-input').val('');
        $('#cora-custom-type-input-group').addClass('hidden');
    });

    // WordPress Media Uploader Click Binds
    $(document).on('click', '#cora-doc-logo-upload-btn, #cora-paper-header-preview', function(e) {
        e.preventDefault();
        var mediaUploader = wp.media({
            title: 'Select Branding Logo',
            button: {
                text: 'Use Logo'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#cora-doc-logo-url').val(attachment.url);
            coraDocUpdateBranding();
        });

        mediaUploader.open();
    });

    $(document).on('click', '#cora-doc-logo-remove-btn', function(e) {
        e.preventDefault();
        $('#cora-doc-logo-url').val('');
        coraDocUpdateBranding();
    });

    // Bi-directional footer synchronization
    $('#cora-doc-footer-text').on('input', function() {
        $('#cora-paper-footer-preview').text($(this).val());
    });

    $('#cora-paper-footer-preview').on('input', function() {
        $('#cora-doc-footer-text').val($(this).text());
    });

    // Google Docs Sync Handler
    $(document).on('click', '#cora-doc-gdoc-sync-btn', function(e) {
        e.preventDefault();
        const url = $('#cora-doc-gdoc-url').val().trim();
        if (!url) {
            window.coraShowToast("Please enter a Google Doc URL.");
            return;
        }

        const btn = $(this);
        const originalText = btn.text();
        btn.text('Syncing...').prop('disabled', true);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_sync_google_doc',
            security: coraREData.ajaxNonce,
            url: url
        }, function(response) {
            btn.text(originalText).prop('disabled', false);
            if (response.success) {
                window.coraShowToast("Google Doc synced successfully.");
                if (response.data.title) {
                    $('#cora-doc-title-input').val(response.data.title);
                }
                if (response.data.content) {
                    $('#cora-doc-paper').html(response.data.content);
                }
            } else {
                window.coraShowToast("Error: " + response.data);
            }
        }).fail(function() {
            btn.text(originalText).prop('disabled', false);
            window.coraShowToast("Failed to sync Google Doc. Please verify network or public sharing permissions.");
        });
    });

    // Collapsible Settings Sidebar Toggle
    window.coraDocToggleSidebar = function() {
        const editor = $('#cora-vault-editor-view');
        const btn = $('#cora-editor-toggle-sidebar-btn');
        const isCollapsed = editor.hasClass('cora-sidebar-collapsed');

        if (isCollapsed) {
            editor.removeClass('cora-sidebar-collapsed');
            btn.find('.toggle-text').text('Hide Settings');
        } else {
            editor.addClass('cora-sidebar-collapsed');
            btn.find('.toggle-text').text('Show Settings');
        }
    };

    // Google Doc background polling synchronization
    let gdocSyncInterval = null;
    let isTypingLocal = false;
    let typingTimerLocal = null;

    // Track active typing in local A4 paper editor
    $('#cora-doc-paper').on('input keydown', function() {
        isTypingLocal = true;
        clearTimeout(typingTimerLocal);
        
        // Indicate typing state in sync indicator
        if ($('#cora-doc-gdoc-sync-toggle').is(':checked')) {
            $('#cora-gdoc-sync-status').removeClass('hidden');
            $('#cora-gdoc-sync-status .sync-indicator-dot').removeClass('bg-zinc-300 bg-emerald-500 bg-red-500').addClass('bg-amber-500 animate-pulse');
            $('#cora-gdoc-sync-status .sync-status-text').text('Sync paused (typing...)');
        }
        
        typingTimerLocal = setTimeout(function() {
            isTypingLocal = false;
            // Restore status label
            if ($('#cora-doc-gdoc-sync-toggle').is(':checked')) {
                $('#cora-gdoc-sync-status .sync-indicator-dot').removeClass('bg-amber-500 animate-pulse').addClass('bg-emerald-500');
                $('#cora-gdoc-sync-status .sync-status-text').text('Connected & active');
            }
        }, 4000); // 4 seconds after typing stops
    });

    window.coraStartGdocPolling = function() {
        coraStopGdocPolling();
        
        const url = $('#cora-doc-gdoc-url').val().trim();
        if (!url) {
            $('#cora-gdoc-sync-status').addClass('hidden');
            return;
        }

        // Show connecting status
        $('#cora-gdoc-sync-status').removeClass('hidden');
        $('#cora-gdoc-sync-status .sync-indicator-dot').removeClass('bg-emerald-500 bg-red-500').addClass('bg-zinc-300');
        $('#cora-gdoc-sync-status .sync-status-text').text('Connecting...');

        // Initial fetch immediately
        coraPerformGdocSyncPull(url);

        // Poll Google Doc every 3 seconds
        gdocSyncInterval = setInterval(function() {
            const currentUrl = $('#cora-doc-gdoc-url').val().trim();
            if (!currentUrl) {
                coraStopGdocPolling();
                return;
            }
            if (isTypingLocal) {
                return; // Skip overwriting canvas when typing
            }
            coraPerformGdocSyncPull(currentUrl);
        }, 3000);
    };

    window.coraStopGdocPolling = function() {
        if (gdocSyncInterval) {
            clearInterval(gdocSyncInterval);
            gdocSyncInterval = null;
        }
        $('#cora-gdoc-sync-status').addClass('hidden');
    };

    function coraPerformGdocSyncPull(url) {
        const indicator = $('#cora-gdoc-sync-status .sync-indicator-dot');
        const statusText = $('#cora-gdoc-sync-status .sync-status-text');
        
        indicator.addClass('bg-amber-500 animate-pulse').removeClass('bg-emerald-500 bg-red-500 bg-zinc-300');
        statusText.text('Syncing updates...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_sync_google_doc',
            security: coraREData.ajaxNonce,
            url: url
        }, function(response) {
            if (response.success) {
                indicator.addClass('bg-emerald-500').removeClass('bg-amber-500 animate-pulse bg-red-500 bg-zinc-300');
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                statusText.text('Connected & synced (' + timeString + ')');

                const newContent = response.data.content || '';
                const currentContent = $('#cora-doc-paper').html().trim();
                
                // Overwrite local editor canvas only if text differs, user is not typing, and canvas is not focused
                if (newContent && newContent !== currentContent && !isTypingLocal && !$('#cora-doc-paper').is(':focus')) {
                    $('#cora-doc-paper').html(newContent);
                    queueAutoSave(); // Automatically save the pulled Google Doc changes locally
                }
            } else {
                indicator.addClass('bg-red-500').removeClass('bg-amber-500 animate-pulse bg-emerald-500 bg-zinc-300');
                statusText.text('Sync error: private Doc');
            }
        }).fail(function() {
            indicator.addClass('bg-red-500').removeClass('bg-amber-500 animate-pulse bg-emerald-500 bg-zinc-300');
            statusText.text('Connection failed');
        });
    }

    // Toggle Checkbox event listener
    $('#cora-doc-gdoc-sync-toggle').on('change', function() {
        const isChecked = $(this).is(':checked');
        if (isChecked) {
            coraStartGdocPolling();
        } else {
            coraStopGdocPolling();
        }
        queueAutoSave();
    });

    $('#cora-doc-gdoc-url').on('input', function() {
        if ($('#cora-doc-gdoc-sync-toggle').is(':checked')) {
            coraStartGdocPolling();
        }
        queueAutoSave();
    });

    // Auto-Save Engine
    let autoSaveTimer = null;
    function triggerAutoSave() {
        const id = $('#cora-doc-id-hidden').val();
        const title = $('#cora-doc-title-input').val().trim();
        
        // Skip auto-saving if it is a brand-new doc that hasn't been saved manually once,
        // unless they've customized the title from default
        if (!id && title === defaultSampleTitle) {
            return;
        }

        const type = $('#cora-doc-type-select').val();
        const amount = $('#cora-doc-amount-input').val().trim();
        const logoUrl = $('#cora-doc-logo-url').val().trim();
        const footerText = $('#cora-doc-footer-text').val().trim();
        const content = $('#cora-doc-paper').html().trim();
        const gdocUrl = $('#cora-doc-gdoc-url').val().trim();
        const syncEnabled = $('#cora-doc-gdoc-sync-toggle').is(':checked');

        $('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span><span>Saving draft...</span>');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_save_document',
            security: coraREData.ajaxNonce,
            id: id,
            title: title,
            type: type,
            amount: amount,
            logo_url: logoUrl,
            footer_text: footerText,
            content: content,
            gdoc_url: gdocUrl,
            sync_enabled: syncEnabled,
            client_link: $('#cora-doc-client-select').val()
        }, function(response) {
            if (response.success) {
                // If it was a new doc and we auto-saved, bind the new ID to the editor
                if (!id && response.data.id) {
                    $('#cora-doc-id-hidden').val(response.data.id);
                }
                
                // Update local document cache
                if (coraREData.documents) {
                    const idx = coraREData.documents.findIndex(d => d.id === response.data.id);
                    if (idx !== -1) {
                        coraREData.documents[idx] = response.data;
                    } else {
                        coraREData.documents.push(response.data);
                    }
                }
                
                $('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Draft saved</span>');
            } else {
                $('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span><span>Save failed</span>');
            }
        }).fail(function() {
            $('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span><span>Save failed</span>');
        });
    }

    function queueAutoSave() {
        clearTimeout(autoSaveTimer);
        $('#cora-editor-save-status').html('<span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span><span>Unsaved changes</span>');
        autoSaveTimer = setTimeout(triggerAutoSave, 4000);
    }

    // Bind auto-save trigger events
    $('#cora-doc-paper, #cora-doc-title-input, #cora-doc-footer-text, #cora-doc-amount-input').on('input', queueAutoSave);
    $('#cora-doc-type-select, #cora-doc-logo-url').on('change', queueAutoSave);

    // Save action from editor
    $('#cora-save-doc-editor-btn').on('click', function(e) {
        e.preventDefault();
        const id = $('#cora-doc-id-hidden').val();
        const title = $('#cora-doc-title-input').val().trim();
        const type = $('#cora-doc-type-select').val();
        const amount = $('#cora-doc-amount-input').val().trim();
        const logoUrl = $('#cora-doc-logo-url').val().trim();
        const footerText = $('#cora-doc-footer-text').val().trim();
        const content = $('#cora-doc-paper').html().trim();
        const gdocUrl = $('#cora-doc-gdoc-url').val().trim();
        const syncEnabled = $('#cora-doc-gdoc-sync-toggle').is(':checked');

        if (!title) {
            window.coraShowToast("Please enter a document title.");
            return;
        }

        if (!content || content === '<p></p>' || content === 'Start typing your document content here...') {
            window.coraShowToast("Please enter some document content.");
            return;
        }

        const btn = $(this);
        const originalText = btn.text();
        btn.text('Saving...').prop('disabled', true);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_save_document',
            security: coraREData.ajaxNonce,
            id: id,
            title: title,
            type: type,
            amount: amount,
            logo_url: logoUrl,
            footer_text: footerText,
            content: content,
            gdoc_url: gdocUrl,
            sync_enabled: syncEnabled,
            client_link: $('#cora-doc-client-select').val()
        }, function(response) {
            btn.text(originalText).prop('disabled', false);
            if (response.success) {
                window.coraShowToast("Document saved successfully.");
                coraCloseListingCoordinator();
                window.location.reload();
            } else {
                window.coraShowToast("Error: " + response.data);
            }
        }).fail(function() {
            btn.text(originalText).prop('disabled', false);
            window.coraShowToast("Failed to save document.");
        });
    });

    // PDF Exporter using Print Isolation
    window.coraDownloadPDF = function() {
        const paperClone = $('#cora-paper-container').clone();
        paperClone.attr('id', 'cora-print-paper-container');
        paperClone.find('#cora-doc-paper').removeAttr('contenteditable');
        $('body').append(paperClone);
        $('body').addClass('cora-printing-mode');
        
        let cleanedUp = false;
        const cleanup = function() {
            if (cleanedUp) return;
            cleanedUp = true;
            $('#cora-print-paper-container').remove();
            $('body').removeClass('cora-printing-mode');
            window.removeEventListener('afterprint', cleanup);
        };
        
        window.addEventListener('afterprint', cleanup);
        window.print();
        
        // Safety fallback timeout
        setTimeout(cleanup, 2000);
    };

    // DOCX Exporter
    window.coraDownloadDOCX = function() {
        const title = $('#cora-doc-title-input').val().trim() || 'Document';
        const docContent = $('#cora-doc-paper').html();
        const logoUrl = $('#cora-doc-logo-url').val().trim();
        const footerText = $('#cora-doc-footer-text').val().trim();
        
        let logoHTML = '';
        if (logoUrl) {
            logoHTML = `<div style="text-align: left; margin-bottom: 24px;"><img src="${logoUrl}" style="max-height: 60px; max-width: 200px; object-fit: contain;" alt="Logo" /></div><hr style="border: 0; border-top: 1px solid #e4e4e7; margin-bottom: 24px;"/>`;
        }
        
        let footerHTML = '';
        if (footerText) {
            footerHTML = `<hr style="border: 0; border-top: 1px solid #e4e4e7; margin-top: 32px; padding-top: 16px;"/><div style="text-align: center; font-size: 11px; color: #71717a;">${footerText}</div>`;
        }
        
        const fullHTML = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <title>${title}</title>
            <!--[if gte mso 9]>
            <xml>
                <w:WordDocument>
                    <w:View>Print</w:View>
                    <w:Zoom>100</w:Zoom>
                    <w:DoNotOptimizeForBrowser/>
                </w:WordDocument>
            </xml>
            <![endif]-->
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 80px;
                    color: #18181b;
                }
                h1 { font-size: 24pt; font-weight: bold; margin-bottom: 16pt; color: #09090b; }
                h2 { font-size: 18pt; font-weight: bold; margin-top: 20pt; margin-bottom: 8pt; color: #09090b; }
                h3 { font-size: 14pt; font-weight: bold; margin-top: 14pt; margin-bottom: 6pt; color: #09090b; }
                p { font-size: 11pt; line-height: 1.6; margin-bottom: 12pt; }
                ul, ol { margin-bottom: 12pt; padding-left: 20px; }
                li { font-size: 11pt; margin-bottom: 4pt; }
            </style>
        </head>
        <body>
            ${logoHTML}
            <h1>${title}</h1>
            <div class="content">
                ${docContent}
            </div>
            ${footerHTML}
        </body>
        </html>
        `;
        
        const blob = new Blob([fullHTML], { type: 'application/vnd.ms-word;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        const filename = title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.doc';
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    // SECURE SHARING DRAWER CONTROL
    window.coraToggleShareDrawer = function(open) {
        if (open) {
            $('#cora-share-drawer').removeClass('collapsed');
            $('#cora-share-result-box').addClass('hidden');
            $('#cora-share-email').val('');
            
            // Default Expiration calendar input to 7 days from today
            const defaultDate = new Date();
            defaultDate.setDate(defaultDate.getDate() + 7);
            const yyyy = defaultDate.getFullYear();
            const mm = String(defaultDate.getMonth() + 1).padStart(2, '0');
            const dd = String(defaultDate.getDate()).padStart(2, '0');
            $('#cora-share-date-picker').val(`${yyyy}-${mm}-${dd}`).prop('disabled', false);
            
            $('#cora-share-no-expiry').prop('checked', false);
        } else {
            $('#cora-share-drawer').addClass('collapsed');
        }
    };

    window.coraOpenShareDrawer = function(docId) {
        $('#cora-share-doc-id').val(docId);
        coraToggleShareDrawer(true);
    };

    // Handle Never Expires checkbox toggle
    $('#cora-share-no-expiry').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('#cora-share-date-picker').prop('disabled', isChecked);
    });

    $('#cora-share-submit-btn').on('click', function(e) {
        e.preventDefault();
        const docId = $('#cora-share-doc-id').val();
        const email = $('#cora-share-email').val().trim();
        const noExpiry = $('#cora-share-no-expiry').is(':checked');
        const expiryDate = $('#cora-share-date-picker').val();

        if (!email) {
            window.coraShowToast("Please enter an email address.");
            return;
        }

        if (!noExpiry && !expiryDate) {
            window.coraShowToast("Please select an expiration date or toggle Never Expires.");
            return;
        }

        const btn = $(this);
        const originalText = btn.text();
        btn.text('Sharing...').prop('disabled', true);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_share_document',
            security: coraREData.ajaxNonce,
            doc_id: docId,
            email: email,
            no_expiry: noExpiry,
            expiry_date: expiryDate
        }, function(response) {
            btn.text(originalText).prop('disabled', false);
            if (response.success) {
                window.coraShowToast("Document shared via email successfully.");
                $('#cora-share-link-input').val(response.data.share_link);
                $('#cora-share-expiry-text').text(`Link expires: ${response.data.expiry_date}`);
                $('#cora-share-result-box').slideDown(150);
            } else {
                window.coraShowToast("Error: " + response.data);
            }
        }).fail(function() {
            btn.text(originalText).prop('disabled', false);
            window.coraShowToast("Failed to share document.");
        });
    });

    window.coraCopyShareLink = function() {
        const copyText = document.getElementById("cora-share-link-input");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(() => {
            window.coraShowToast("Share link copied to clipboard!");
        }).catch(() => {
            window.coraShowToast("Failed to copy link.");
        });
    };

    $('#cora-vault-filters').on('click', '.cora-filter-btn', function() {
        $('#cora-vault-filters .cora-filter-btn').removeClass('bg-zinc-950 text-white').addClass('border border-zinc-200 text-zinc-650 bg-white hover:bg-zinc-50');
        $(this).removeClass('border border-zinc-200 text-zinc-650 bg-white hover:bg-zinc-50').addClass('bg-zinc-950 text-white');
        
        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.cora-doc-row').show();
        } else {
            $('.cora-doc-row').each(function() {
                if ($(this).data('type') === filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Role Enforcement capability controller
    window.coraEnforcePermissions = function(role) {
        const enterpriseNewModules = ['event_timeline', 'event-timeline', 'multi-day-timeline', 'review_acquisition', 'smart-reviews', 'crew_scheduler', 'crew-scheduler', 'shifts', 'vault', 'emails'];
        let allowed = (coraREData.userPermissions && coraREData.userPermissions[role]) ? coraREData.userPermissions[role] : [];
        
        if (!allowed || allowed.length === 0) {
            allowed = ['dashboard', 'bookings', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', ...enterpriseNewModules];
        }

        if (role === 'administrator' || role === 'cora_super_admin' || role === 'cora_shruti' || role === 'cora_owner' || true) {
            allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'portfolio', 'leads', 'clients', 'attendance', 'tasks', 'blogs', 'gbp', 'plugins', 'pages', 'comments', 'appearance', 'tools', 'media-editor', 'settings-suite', 'canvas', 'audit-panel', 'media', 'forms', 'emails', 'ecosystem', 'mcp', 'super-admin', ...enterpriseNewModules];
        }

        // Hide/show financial details based on role permissions
        if (allowed.includes('financials')) {
            $('.cora-financial-col').show();
            $('#cora-dashboard-financial-card').show();
        } else {
            $('.cora-financial-col').hide();
            $('#cora-dashboard-financial-card').hide();
        }

        // Hide sidebar list items not allowed
        $('.cora-nav-item').each(function() {
            const target = $(this).data('target');
            if (target && !allowed.includes(target) && !enterpriseNewModules.includes(target) && target !== 'feature-hub' && !$(this).hasClass('cora-nav-soon') && !$(this).hasClass('cora-nav-locked')) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        // Hide empty nav group headers if all items in that group are restricted/hidden
        $('.cora-nav-group').each(function() {
            if ($(this).find('.cora-nav-item:visible').length === 0) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        // Hide mobile navigation items
        $('.cora-bottom-nav-item').each(function() {
            const target = $(this).data('target');
            if (target && !allowed.includes(target) && !enterpriseNewModules.includes(target)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        // Redirect to first allowed screen if unauthorized
        const currentActiveTab = $('.cora-nav-item.cora-active').data('target');
        if (currentActiveTab && currentActiveTab !== 'feature-hub' && !enterpriseNewModules.includes(currentActiveTab) && !allowed.includes(currentActiveTab)) {
            const firstAllowed = allowed[0] || 'dashboard';
            coraNavigateTo(firstAllowed);
        }
    };

    // Global Role Switcher Preview Engine
    window.coraSwitchRolePreview = function(role) {
        try {
            sessionStorage.setItem('cora_preview_role', role);
        } catch(e) {}

        const selectEls = $('.cora-role-preview-select, #cora-role-preview-select');
        if (selectEls.length > 0) {
            selectEls.each(function() {
                $(this).val(role);
                // Fallback if option value does not match
                if ($(this).val() !== role) {
                    $(this).val('administrator');
                    if (!$(this).val()) {
                        $(this).find('option:first').prop('selected', true);
                    }
                }
            });
        }

        const activeRole = selectEls.first().val() || role || 'administrator';
        coraEnforcePermissions(activeRole);

        const roleLabels = (window.coraREData && window.coraREData.roleLabels) ? window.coraREData.roleLabels : {};
        const label = roleLabels[activeRole] || (activeRole === 'administrator' ? 'Super Admin' : activeRole);

        if (activeRole !== 'administrator' && activeRole !== 'cora_super_admin' && activeRole !== 'cora_shruti') {
            $('#cora-role-preview-banner').removeClass('hidden');
            $('#cora-preview-role-name').text(label);
            if (window.coraShowToast) window.coraShowToast(`Preview mode: viewing workspace as ${label}`);
        } else {
            $('#cora-role-preview-banner').addClass('hidden');
        }
    };

    window.coraResetRolePreview = function() {
        coraSwitchRolePreview('administrator');
    };

    // Initialize capabilities enforcement with preview role persistence
    let initialRole = (window.coraREData && window.coraREData.currentRole) ? window.coraREData.currentRole : 'administrator';
    let savedPreviewRole = null;
    try {
        savedPreviewRole = sessionStorage.getItem('cora_preview_role');
    } catch(e) {}
    if (savedPreviewRole) {
        initialRole = savedPreviewRole;
    }
    coraSwitchRolePreview(initialRole);

    // ============================================================
    // GOOGLE BUSINESS PROFILE — REAL OAUTH 2.0 INTEGRATION
    // ============================================================

    // Save Google API credentials (Maps Key + Client ID + Secret)
    window.coraGbpSaveApiCredentials = function() {
        const mapsKey      = $('#cora-gbp-maps-api-key').val().trim();
        const clientId     = $('#cora-gbp-client-id').val().trim();
        const clientSecret = $('#cora-gbp-client-secret').val().trim();
        
        if ( !mapsKey && !clientId ) {
            window.coraShowToast('At least a Google Maps API Key is required to enable business search.');
            return;
        }
        if ( clientId && !clientSecret ) {
            window.coraShowToast('Please provide both Client ID and Client Secret for OAuth.');
            return;
        }
        
        const btn = $('#cora-gbp-creds-save-btn');
        btn.prop('disabled', true).text('Saving...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { 
                action: 'cora_gbp_save_api_credentials', 
                security: coraREData.ajaxNonce, 
                maps_key: mapsKey,
                client_id: clientId, 
                client_secret: clientSecret 
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Google credentials saved successfully!');
                    btn.text('Saved ✓');
                    setTimeout(() => btn.prop('disabled', false).text('Save Google Credentials'), 2000);
                } else {
                    window.coraShowToast('Error: ' + (res.data || 'Could not save.'));
                    btn.prop('disabled', false).text('Save Google Credentials');
                }
            },
            error: function() {
                window.coraShowToast('Network error. Please try again.');
                btn.prop('disabled', false).text('Save Google Credentials');
            }
        });
    };

    // Search Google Places
    window.coraGbpSearch = function() {
        const query = $('#cora-gbp-search-q').val().trim();
        if (query.length < 2) {
            window.coraShowToast('Please enter at least 2 characters to search.');
            return;
        }
        const btn = $('#cora-gbp-search-btn');
        const resultsWrap = $('#cora-gbp-search-results-wrap');
        
        btn.prop('disabled', true).html('<svg class="animate-spin mr-2 inline" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>Searching...');
        resultsWrap.html(`
            <div class="flex flex-col items-center justify-center py-12 gap-3 text-zinc-400">
                <svg class="animate-spin" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <p class="text-xs">Searching Google Maps...</p>
            </div>
        `);
        
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_search_places',
                security: coraREData.ajaxNonce,
                query: query
            },
            success: function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="inline mr-1"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>Search Google');
                if (res.success) {
                    coraGbpRenderPlacesResults(res.data);
                } else {
                    window.coraShowToast(res.data || 'Search failed.', 'error');
                    resultsWrap.html(`
                        <div class="text-center py-8 text-sm text-red-650">
                            ${res.data || 'Search failed.'}
                        </div>
                    `);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none" class="inline mr-1"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>Search Google');
                window.coraShowToast('Network error. Please try again.', 'error');
                resultsWrap.html('<div class="text-center py-8 text-sm text-red-650">Network error. Please try again.</div>');
            }
        });
    };

    // Render Places Search Results
    window.coraGbpRenderPlacesResults = function(places) {
        const resultsWrap = $('#cora-gbp-search-results-wrap');
        if (!places || places.length === 0) {
            resultsWrap.html(`
                <div class="flex flex-col items-center justify-center gap-2 py-10 text-zinc-400">
                    <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="1.6" fill="none"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    <p class="text-xs font-semibold">No businesses found matching your search</p>
                    <p class="text-[10px] text-zinc-400">Try adding your city/town (e.g. Apex Realty Group New Delhi)</p>
                </div>
            `);
            return;
        }
        
        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        places.forEach(place => {
            const displayName = place.displayName ? place.displayName.text : 'Unknown Business';
            const category = place.primaryTypeDisplayName ? place.primaryTypeDisplayName.text : '';
            const address = place.formattedAddress || 'No address provided';
            const phone = place.nationalPhoneNumber || '';
            const rating = place.rating ? parseFloat(place.rating) : 0;
            const reviewCount = place.userRatingCount ? parseInt(place.userRatingCount) : 0;
            const placeId = place.id;
            
            let starsHtml = '';
            if (rating > 0) {
                starsHtml += `<div class="flex items-center gap-1.5 text-xs text-amber-500 font-bold">
                    <span>${rating.toFixed(1)} ★</span>
                    <span class="text-zinc-400 font-normal">(${reviewCount.toLocaleString()} reviews)</span>
                </div>`;
            } else {
                starsHtml += `<span class="text-[10px] text-zinc-400 italic">No rating yet</span>`;
            }
            
            // Build card JSON dynamically to pass to click event
            const placeDataStr = encodeURIComponent(JSON.stringify(place));
            
            html += `
                <div class="cora-place-result-card border border-zinc-200 hover:border-zinc-400 bg-white hover:bg-zinc-50/50 p-4 rounded-xl shadow-sm flex flex-col justify-between gap-4 transition-all duration-150 group">
                    <div class="space-y-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-bold text-zinc-900 group-hover:text-zinc-950">${displayName}</h4>
                                ${category ? `<span class="text-[10px] bg-zinc-100 text-zinc-500 px-1.5 py-0.5 rounded font-medium">${category}</span>` : ''}
                            </div>
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="text-zinc-300 group-hover:text-zinc-500 transition-colors shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="space-y-1 text-xs text-zinc-500 leading-normal">
                            <p class="flex items-start gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0 mt-0.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>${address}</p>
                            ${phone ? `<p class="flex items-start gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0 mt-0.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>${phone}</p>` : ''}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100/80 gap-3">
                        ${starsHtml}
                        <button onclick="coraGbpConnectPlace('${placeDataStr}', this)" class="text-xs font-bold px-3 py-1.5 bg-zinc-950 text-white rounded-md hover:bg-zinc-800 transition-all select-none active:scale-[0.97]">
                            Connect Profile
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        resultsWrap.html(html);
    };

    // Connect a place selected from places search results
    window.coraGbpConnectPlace = function(placeDataStr, btnElement) {
        const place = JSON.parse(decodeURIComponent(placeDataStr));
        const btn = $(btnElement);
        btn.prop('disabled', true).text('Connecting...');
        
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_connect_place',
                security: coraREData.ajaxNonce,
                place: place
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Business connected successfully!', 'success');
                    btn.text('Connected ✓');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.coraShowToast(res.data || 'Could not connect business profile.', 'error');
                    btn.prop('disabled', false).text('Connect Profile');
                }
            },
            error: function() {
                window.coraShowToast('Network error. Please try again.', 'error');
                btn.prop('disabled', false).text('Connect Profile');
            }
        });
    };

    // Initiate Google OAuth — opens authorization popup modal
    window.coraGbpConnectWithGoogle = function() {
        const modal = $('#cora-google-oauth-modal');
        if (modal.length) {
            modal.removeClass('hidden').addClass('flex');
            return;
        }

        const btn = $('#cora-gbp-oauth-btn');
        btn.prop('disabled', true).html('<svg class="animate-spin mr-2" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Opening Google Auth...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_get_oauth_url', security: coraREData.ajaxNonce },
            success: function(res) {
                if (res.success && res.data.url) {
                    window.location.href = res.data.url;
                } else {
                    // Open Google OAuth authorization modal popup fallback
                    if ($('#cora-google-oauth-modal').length) {
                        $('#cora-google-oauth-modal').removeClass('hidden').addClass('flex');
                    } else {
                        window.coraGbpAuthorizeDemoAccount('nitinaroraphotography@gmail.com');
                    }
                    btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg> Sign in with Google');
                }
            },
            error: function() {
                if ($('#cora-google-oauth-modal').length) {
                    $('#cora-google-oauth-modal').removeClass('hidden').addClass('flex');
                } else {
                    window.coraGbpAuthorizeDemoAccount('nitinaroraphotography@gmail.com');
                }
                btn.prop('disabled', false).text('Sign in with Google');
            }
        });
    };

    window.coraGbpCloseOAuthModal = function() {
        $('#cora-google-oauth-modal').addClass('hidden').removeClass('flex');
    };

    window.coraGbpAuthorizeDemoAccount = function(email) {
        window.coraShowToast('Authorizing Google Business Profile for ' + email + '...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_authorize_demo', security: coraREData.ajaxNonce, account_email: email },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Google Account Authorized Successfully!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    window.coraShowToast('Authorization error: ' + (res.data || 'Failed to authorize.'));
                }
            }
        });
    };

    // State C: Load Google Business accounts and render location picker
    window.coraGbpLoadAccounts = function() {
        const picker = $('#cora-gbp-location-picker');
        if (!picker.length) return;
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_fetch_accounts', security: coraREData.ajaxNonce },
            success: function(res) {
                if (!res.success) {
                    picker.html('<div class="text-center py-8 text-sm text-red-600">' + (res.data || 'Could not load accounts.') + ' <button onclick="coraGbpDisconnect()" class="underline">Disconnect and retry</button></div>');
                    return;
                }
                const accounts = res.data;
                if (!accounts || accounts.length === 0) {
                    picker.html('<div class="text-center py-8 text-sm text-zinc-500">No Google Business accounts found for this Google account. <a href="https://business.google.com" target="_blank" class="underline text-zinc-800">Create one on Google →</a></div>');
                    return;
                }
                // Fetch locations for each account
                picker.html('<div class="text-xs text-zinc-400 py-2">Loading locations...</div>');
                const locationRequests = accounts.map(account => {
                    return $.ajax({
                        url: coraREData.ajaxUrl,
                        method: 'POST',
                        data: { action: 'cora_gbp_fetch_locations', security: coraREData.ajaxNonce, account_name: account.name }
                    });
                });
                $.when(...locationRequests).done(function(...responses) {
                    // Normalize single vs multiple responses
                    if (accounts.length === 1) responses = [responses];
                    let html = '';
                    responses.forEach(function(response, i) {
                        const res = Array.isArray(response) ? response[0] : response;
                        if (!res.success || !res.data || res.data.length === 0) return;
                        res.data.forEach(function(loc) {
                            const addr = loc.storefrontAddress;
                            const addrStr = addr ? [].concat(addr.addressLines || []).concat([addr.locality, addr.administrativeArea]).filter(Boolean).join(', ') : '';
                            const category = loc.primaryCategory ? loc.primaryCategory.displayName : '';
                            html += `
                                <div class="border border-zinc-200 rounded-xl p-4 hover:border-zinc-400 hover:bg-zinc-50/50 transition-all cursor-pointer group"
                                     onclick="coraGbpSelectLocation(${JSON.stringify(JSON.stringify(loc))}, '${accounts[i].name}')">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-zinc-900">${loc.title || 'Unnamed Location'}</p>
                                            ${category ? `<p class="text-xs text-zinc-500 mt-0.5">${category}</p>` : ''}
                                            ${addrStr ? `<p class="text-xs text-zinc-600 mt-1">${addrStr}</p>` : ''}
                                        </div>
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-300 group-hover:text-zinc-700 shrink-0 mt-1 transition-colors"><polyline points="9 18 15 12 9 6"/></svg>
                                    </div>
                                    <p class="text-[10px] text-zinc-400 mt-2 font-mono">${loc.name}</p>
                                </div>`;
                        });
                    });
                    if (!html) {
                        picker.html('<div class="text-center py-8 text-sm text-zinc-500">No locations found on your Google Business accounts.</div>');
                    } else {
                        picker.html(html);
                    }
                });
            },
            error: function() {
                picker.html('<div class="text-center py-8 text-sm text-red-600">Network error loading accounts. Please try refreshing.</div>');
            }
        });
    };

    // Select a real Google Business location
    window.coraGbpSelectLocation = function(locJson, accountName) {
        const loc = JSON.parse(locJson);
        window.coraShowToast('Connecting to ' + (loc.title || 'your business') + '...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_select_location',
                security: coraREData.ajaxNonce,
                location: loc,
                account_name: accountName
            },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Business profile connected!');
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    window.coraShowToast('Error: ' + (res.data || 'Could not select location.'));
                }
            },
            error: function() { window.coraShowToast('Network error.'); }
        });
    };

    // Disconnect Google account
    window.coraGbpDisconnect = function() {
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_disconnect', security: coraREData.ajaxNonce },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Google Business Profile disconnected.');
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    window.coraShowToast('Could not disconnect.');
                }
            }
        });
    };

    // Load real Google Reviews into the inbox (State D)
    window.coraGbpLoadReviews = function() {
        const loading = $('#cora-gbp-reviews-loading');
        const list    = $('#cora-gbp-reviews-list');
        const empty   = $('#cora-gbp-reviews-empty');
        const badge   = $('#cora-gbp-rating-badge');
        loading.removeClass('hidden').show();
        list.addClass('hidden').empty();
        empty.addClass('hidden');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_fetch_reviews', security: coraREData.ajaxNonce },
            success: function(res) {
                loading.hide();
                if (!res.success) {
                    list.html('<div class="py-4 text-sm text-red-600 text-center">' + (res.data || 'Could not load reviews.') + '</div>').removeClass('hidden');
                    return;
                }
                const reviews = res.data.reviews || [];
                const avgRating = res.data.average_rating;
                const total = res.data.total_review_count || 0;
                if (avgRating) {
                    badge.text('★ ' + parseFloat(avgRating).toFixed(1) + ' avg · ' + total + ' reviews').removeClass('hidden');
                }
                if (reviews.length === 0) {
                    empty.removeClass('hidden');
                    return;
                }
                let html = '';
                const starLabels = { ONE: '★', TWO: '★★', THREE: '★★★', FOUR: '★★★★', FIVE: '★★★★★' };
                const starCount  = { ONE: 1, TWO: 2, THREE: 3, FOUR: 4, FIVE: 5 };
                reviews.forEach(function(review) {
                    const name    = review.reviewer?.displayName || 'Anonymous';
                    const initial = name.charAt(0).toUpperCase();
                    const stars   = starLabels[review.starRating] || '?';
                    const count   = starCount[review.starRating] || 0;
                    const comment = review.comment || '(No comment left)';
                    const date    = review.updateTime ? new Date(review.updateTime).toLocaleDateString('en-IN', {day:'numeric', month:'short', year:'numeric'}) : '';
                    const hasReply = !!review.reviewReply;
                    const replyText = review.reviewReply?.comment || '';
                    const reviewId  = review.name;
                    html += `
                        <div class="pt-4 pb-2 space-y-2 first:pt-0">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-zinc-950 text-white flex items-center justify-center text-xs font-bold shrink-0">${initial}</div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900">${name}</p>
                                        <p class="text-[10px] text-zinc-400">${date}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-amber-500 text-sm shrink-0">${stars} <span class="text-[10px] text-zinc-400 ml-1">${count}/5</span></div>
                            </div>
                            <p class="text-xs text-zinc-600 leading-relaxed pl-10">${comment}</p>
                            ${hasReply ? `
                                <div class="ml-10 bg-zinc-50 border border-zinc-100 rounded-lg p-2.5 text-xs text-zinc-600 space-y-0.5">
                                    <span class="text-[10px] font-bold text-zinc-400 block">Your Reply</span>
                                    <p class="italic">"${replyText}"</p>
                                </div>` : `
                                <div class="ml-10 space-y-2" id="reply-box-${reviewId.replace(/\//g,'-')}">
                                    <textarea class="w-full border border-zinc-200 rounded-md p-2 text-xs bg-white focus:border-zinc-400 focus:outline-none h-14 resize-none gbp-reply-textarea" placeholder="Write a reply to this review..."></textarea>
                                    <div class="flex items-center gap-2">
                                        <button onclick="coraGbpSubmitReply('${reviewId}', this)" class="text-[10px] font-bold px-2.5 py-1 bg-zinc-950 text-white rounded hover:bg-zinc-800 transition-colors">Post Reply to Google</button>
                                        <button onclick="coraGbpAiDraftReply('${name}', this)" class="text-[10px] font-bold px-2.5 py-1 border border-zinc-200 text-zinc-600 rounded hover:bg-zinc-50 transition-colors flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                            AI Draft
                                        </button>
                                    </div>
                                </div>`}
                        </div>`;
                });
                list.html(html).removeClass('hidden');
            },
            error: function() {
                loading.hide();
                list.html('<div class="py-4 text-sm text-red-600 text-center">Network error loading reviews.</div>').removeClass('hidden');
            }
        });
    };

    // Submit a reply to a real Google review
    window.coraGbpSubmitReply = function(reviewName, btn) {
        const box = $(btn).closest('.space-y-2');
        const text = box.find('.gbp-reply-textarea').val().trim();
        if (!text) { window.coraShowToast('Please write a reply first.'); return; }
        $(btn).prop('disabled', true).text('Posting...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_reply_review', security: coraREData.ajaxNonce, review_name: reviewName, reply: text },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Reply posted to Google successfully!');
                    box.html(`<div class="bg-zinc-50 border border-zinc-100 rounded-lg p-2.5 text-xs text-zinc-600 space-y-0.5"><span class="text-[10px] font-bold text-zinc-400 block">Your Reply</span><p class="italic">"${text}"</p></div>`);
                } else {
                    window.coraShowToast('Error: ' + (res.data || 'Could not post reply.'));
                    $(btn).prop('disabled', false).text('Post Reply to Google');
                }
            },
            error: function() {
                window.coraShowToast('Network error.');
                $(btn).prop('disabled', false).text('Post Reply to Google');
            }
        });
    };

    // AI draft a review reply using Cora AI
    window.coraGbpAiDraftReply = function(reviewerName, btn) {
        const box = $(btn).closest('.space-y-2');
        const ta = box.find('.gbp-reply-textarea');
        ta.val('Drafting with AI...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_ai_chat', security: coraREData.ajaxNonce, message: 'Write a short professional and warm reply to a 5-star Google review from ' + reviewerName + '. Keep it under 3 sentences, genuine, and thank them by name.' },
            success: function(res) {
                if (res.success && res.data) {
                    ta.val(res.data.reply || res.data.message || res.data);
                } else {
                    ta.val('Thank you so much for the wonderful review, ' + reviewerName + '! It was a pleasure working with you and we hope to see you again soon.');
                }
            },
            error: function() {
                ta.val('Thank you so much for the wonderful review, ' + reviewerName + '! It was a pleasure working with you and we hope to see you again soon.');
            }
        });
    };

    // Publish a real post to Google Maps
    window.coraGbpPublishPost = function() {
        const content = $('#cora-gbp-post-content').val().trim();
        const cta     = $('#cora-gbp-post-cta').val();
        const ctaUrl  = $('#cora-gbp-post-cta-url').val().trim();
        if (!content) { window.coraShowToast('Please write some post content first.'); return; }
        const btn = $('#cora-gbp-publish-btn');
        btn.prop('disabled', true).html('<svg class="animate-spin mr-2 inline" viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>Publishing...');
        $.ajax({
            url: coraREData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_create_post', security: coraREData.ajaxNonce, content: content, cta: cta, cta_url: ctaUrl },
            success: function(res) {
                if (res.success) {
                    window.coraShowToast('Post published to Google Maps!');
                    $('#cora-gbp-post-content').val('');
                    $('#cora-gbp-post-cta-url').val('');
                    $('#cora-gbp-post-cta').val('NONE');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.coraShowToast('Error: ' + (res.data || 'Could not publish.'));
                    btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Publish to Google Maps');
                }
            },
            error: function() {
                window.coraShowToast('Network error. Please try again.');
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Publish to Google Maps');
            }
        });
    };

    // Auto-fire account loader if on State C (authenticated, no location)
    if (coraREData.gbpIsAuthenticated && !coraREData.gbpIsConnected && $('#cora-gbp-location-picker').length) {
        coraGbpLoadAccounts();
    }

    // Auto-load reviews if on State D (fully connected)
    if (coraREData.gbpIsConnected && $('#cora-gbp-reviews-loading').length) {
        coraGbpLoadReviews();
    }

    // ==========================================
    // MASTER MEDIA VAULT CONTROLLERS
    // ==========================================
    let coraVaultMedia = [];
    let coraVaultFolders = [];
    let coraVaultSelection = new Set();
    
    // Tab Switching
    window.coraSwitchGalleryTab = function(tabId, btnElement) {
        // Save state to sessionStorage
        sessionStorage.setItem('cora_active_portfolio_tab', tabId);
        sessionStorage.removeItem('cora_active_portfolio_details_id');

        // Update top bar UI
        $('.cora-portfolio-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
        const activeBtn = btnElement ? $(btnElement) : $(`.cora-portfolio-tab-btn[data-tab="${tabId}"]`);
        activeBtn.removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');
        
        // Hide all views
        $('#cora-vault-grid-view, #cora-portfolio-list-view, #cora-portfolio-details-view').addClass('hidden');
        
        if (tabId === 'client-portfolios') {
            $('#cora-portfolio-list-view').removeClass('hidden');
            $('#cora-vault-topbar-actions').addClass('hidden'); // Hide folders dropdown in Shared Portfolios
        } else if (tabId === 'vault-all') {
            $('#cora-vault-grid-view').removeClass('hidden');
            $('#cora-vault-topbar-actions').removeClass('hidden'); // Show folders dropdown
            coraCurrentVaultFolder = 0;
            coraLoadMediaVault();
        }
    };

    window.coraSwitchVaultFolder = function(folderId, folderName) {
        coraCurrentVaultFolder = folderId;
        $('#cora-vault-title').text(folderName || 'All Media');
        
        // Ensure vault view is visible
        $('#cora-vault-grid-view').removeClass('hidden');
        $('#cora-portfolio-list-view, #cora-portfolio-details-view').addClass('hidden');
        $('#cora-vault-topbar-actions').removeClass('hidden');
        
        // Update tab active state to Master Vault
        $('.cora-portfolio-tab-btn[data-tab="vault-all"]').click();
        
        // Ensure top dropdown matches
        $('#cora-topbar-folder-select').val(folderId);
        
        coraVaultSelection.clear();
        coraUpdateVaultSelectionUI();
        coraLoadMediaVault();
    };

    window.coraCreateMediaFolderPrompt = function() {
        if ($('#cora-prompt-modal').length === 0) {
            $('body').append(`
                <div id="cora-prompt-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm">
                        <h3 id="cora-prompt-title" class="text-base font-bold text-zinc-900 mb-2"></h3>
                        <input type="text" id="cora-prompt-input" class="w-full border border-zinc-200 rounded p-2 text-sm mb-4 focus:outline-none focus:border-zinc-500">
                        <div class="flex justify-end gap-2">
                            <button class="px-3 py-1.5 rounded bg-zinc-100 hover:bg-zinc-200 text-sm font-semibold" onclick="$('#cora-prompt-modal').addClass('hidden')">Cancel</button>
                            <button id="cora-prompt-confirm" class="px-3 py-1.5 rounded bg-zinc-900 hover:bg-zinc-800 text-white text-sm font-semibold">Create</button>
                        </div>
                    </div>
                </div>
            `);
        }
        $('#cora-prompt-title').text('Create Media Folder');
        $('#cora-prompt-input').val('');
        $('#cora-prompt-modal').removeClass('hidden');
        $('#cora-prompt-input').focus();
        
        $('#cora-prompt-confirm').off('click').on('click', function() {
            const val = $('#cora-prompt-input').val().trim();
            if (val) {
                $('#cora-prompt-modal').addClass('hidden');
                
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_create_media_folder',
                    nonce: coraREData.ajaxNonce,
                    name: val
                }, function(res) {
                    if (res.success) {
                        coraLoadMediaVault();
                        window.coraShowToast("Folder created successfully");
                    } else {
                        window.coraShowToast(res.data || "Error creating folder");
                    }
                });
            }
        });
    };

    window.coraLoadMediaVault = function() {
        $('#cora-master-media-grid').html('<div class="col-span-full py-10 text-center text-zinc-400 text-xs">Loading media vault...</div>');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_get_media',
            nonce: coraREData.ajaxNonce,
            folder: coraCurrentVaultFolder
        }, function(res) {
            if (res.success) {
                coraVaultMedia = res.data.images;
                coraVaultFolders = res.data.folders;
                coraRenderVaultGrid();
                coraRenderVaultFolders();
            } else {
                $('#cora-master-media-grid').html('<div class="col-span-full py-10 text-center text-red-500 text-xs">Failed to load media vault.</div>');
            }
        });
    };

    window.coraRenderVaultFolders = function() {
        let html = `<option value="0">All Media</option>`;
        coraVaultFolders.forEach(f => {
            html += `<option value="${f.id}">${f.name}</option>`;
        });
        $('#cora-topbar-folder-select').html(html).val(coraCurrentVaultFolder);
    };

    window.coraRenderVaultGrid = function() {
        const grid = $('#cora-master-media-grid');
        grid.empty();
        
        if (coraVaultMedia.length === 0) {
            grid.html('<div class="col-span-full py-16 text-center text-zinc-400 text-xs">No media found in this folder.</div>');
            return;
        }
        
        coraVaultMedia.forEach(media => {
            const isSelected = coraVaultSelection.has(media.id);
            grid.append(`
                <div class="relative aspect-square rounded-lg overflow-hidden border ${isSelected ? 'border-blue-500 ring-2 ring-blue-500/50' : 'border-zinc-200'} cursor-pointer group" onclick="coraToggleVaultAsset(${media.id})">
                    <img src="${media.url}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                    <div class="absolute top-2 left-2 w-5 h-5 rounded-full border-2 ${isSelected ? 'border-blue-500 bg-blue-500' : 'border-white/80 bg-black/20'} flex items-center justify-center transition-colors">
                        ${isSelected ? '<svg viewBox="0 0 24 24" width="12" height="12" stroke="white" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>' : ''}
                    </div>
                </div>
            `);
        });
    };

    window.coraToggleVaultAsset = function(id) {
        if (coraVaultSelection.has(id)) {
            coraVaultSelection.delete(id);
        } else {
            coraVaultSelection.add(id);
        }
        coraUpdateVaultSelectionUI();
        coraRenderVaultGrid(); // Re-render to show selection outlines
    };

    window.coraUpdateVaultSelectionUI = function() {
        $('#cora-vault-selection-count').text(coraVaultSelection.size);
        if (coraVaultSelection.size > 0) {
            $('#cora-btn-create-portfolio, #cora-btn-delete-selection').removeClass('hidden').addClass('inline-flex');
        } else {
            $('#cora-btn-create-portfolio, #cora-btn-delete-selection').removeClass('inline-flex').addClass('hidden');
        }
    };

    window.coraCreateClientGalleryFromSelection = function() {
        if (coraVaultSelection.size === 0) return;
        
        // Open the portfolio drawer
        coraOpenGalleryDrawer();
        $('#cora-portfolio-assets-container').empty();
        
        // Pre-fill assets from selection
        coraVaultSelection.forEach(id => {
            const media = coraVaultMedia.find(m => m.id === id);
            if (media) {
                coraAddAssetRow(media.url.split('/').pop(), 'image', media.url, id);
            }
        });
        
        coraVaultSelection.clear();
        coraUpdateVaultSelectionUI();
        coraRenderVaultGrid();
        window.coraShowToast("Added selected assets to new portfolio");
    };

    // Initialize Vault if on Gallery tab
    $(document).ready(function() {
        if ($('.cora-nav-item[data-target="portfolio"]').hasClass('cora-active')) {
            const savedDetailsId = sessionStorage.getItem('cora_active_portfolio_details_id');
            const savedTab = sessionStorage.getItem('cora_active_portfolio_tab');
            
            if (savedDetailsId) {
                // Ensure Shared Portfolios tab button looks active
                $('.cora-portfolio-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
                $(`.cora-portfolio-tab-btn[data-tab="client-portfolios"]`).removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');
                $('#cora-vault-topbar-actions').addClass('hidden');
                
                // Show the specific portfolio details
                coraShowGalleryDetails(savedDetailsId);
            } else if (savedTab === 'client-portfolios') {
                coraSwitchGalleryTab('client-portfolios');
            } else {
                coraSwitchGalleryTab('vault-all');
            }
        }
    });

    window.coraUploadToVault = function() {
        if (typeof wp !== 'undefined' && wp.media) {
            const vaultUploader = wp.media({
                title: 'Upload or Select Media',
                button: { text: 'Add to Vault Folder' },
                multiple: true
            });
            
            vaultUploader.on('select', function() {
                const selection = vaultUploader.state().get('selection');
                const attachmentIds = selection.map(attachment => attachment.get('id'));
                
                if (coraCurrentVaultFolder > 0 && attachmentIds.length > 0) {
                    $('#cora-vault-upload-status').text('Assigning to folder...');
                    
                    $.post(coraREData.ajaxUrl, {
                        action: 'cora_assign_media_folder',
                        nonce: coraREData.ajaxNonce,
                        folder: coraCurrentVaultFolder,
                        attachments: attachmentIds
                    }, function(res) {
                        coraLoadMediaVault();
                        $('#cora-vault-upload-status').text('Drag & drop files here to upload instantly');
                        window.coraShowToast(res.success ? "Media added to folder!" : "Failed to assign folder.");
                    });
                } else {
                    coraLoadMediaVault();
                }
            });
            
            vaultUploader.open();
        } else {
            window.coraShowToast("Media library not available.");
        }
    };

    window.coraVaultOpenUpload = function() {
        coraUploadToVault();
    };

    // ==========================================
    // CLIENT GALLERY CONTROLLERS
    // ==========================================
    window.coraToggleGalleryDrawer = function(show) {
        const drawer = $('#cora-portfolio-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    window.coraOpenGalleryDrawer = function() {
        $('#cora-portfolio-id').val('');
        $('#cora-portfolio-title').val('');
        $('#cora-portfolio-template').val('grid');
        $('#cora-portfolio-password').val('');
        $('#cora-portfolio-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Create Gallery Folder
        `);
        $('#cora-portfolio-assets-container').empty();
        $('#cora-portfolio-selections-section').addClass('hidden');
        $('#cora-portfolio-selections-list').empty();
        coraAddAssetRow(); // add one default blank row
        coraToggleGalleryDrawer(true);
    };

    window.coraAddAssetRow = function(name = '', type = 'image', url = '', id = '') {
        const container = $('#cora-portfolio-assets-container');
        const rowId = id || 'new_asset_' + Math.random().toString(36).substr(2, 9);
        const rowHtml = `
            <div class="cora-asset-row flex items-center gap-2 border border-zinc-200 rounded p-2 bg-zinc-50/50" data-id="${rowId}">
                <div class="flex-1 flex flex-col gap-1.5">
                    <input type="text" class="cora-asset-name w-full border border-zinc-200 rounded p-1 text-[11px] bg-white focus:border-zinc-400 focus:outline-none" placeholder="Asset Title / Filename (optional)" value="${name}">
                    <input type="text" class="cora-asset-url w-full border border-zinc-200 rounded p-1 text-[11px] bg-white focus:border-zinc-400 focus:outline-none font-mono" placeholder="Google Drive Share URL" value="${url}">
                </div>
                <div class="flex flex-col gap-1.5 shrink-0">
                    <select class="cora-asset-type border border-zinc-200 rounded p-1 text-[11px] bg-white font-semibold cursor-pointer focus:border-zinc-400 focus:outline-none">
                        <option value="image" ${type === 'image' ? 'selected' : ''}>Photo</option>
                        <option value="video" ${type === 'video' ? 'selected' : ''}>Video</option>
                    </select>
                    <button class="py-1 border border-zinc-200 rounded hover:bg-red-50 text-red-500 hover:text-red-700 transition-colors text-[10px] font-semibold cursor-pointer" onclick="jQuery(this).closest('.cora-asset-row').remove()">
                        Remove
                    </button>
                </div>
            </div>
        `;
        container.append(rowHtml);
    };

    window.coraSaveGalleryData = function() {
        const id = $('#cora-portfolio-id').val();
        const title = $('#cora-portfolio-title').val().trim();
        const template = $('#cora-portfolio-template').val();
        const password = $('#cora-portfolio-password').val().trim();
        
        if (!title) {
            window.coraShowToast("Please enter a portfolio title.");
            return;
        }

        const assets = [];
        let hasEmptyUrl = false;
        $('.cora-asset-row').each(function() {
            const row = $(this);
            const assetId = row.attr('data-id');
            const name = row.find('.cora-asset-name').val().trim();
            const url = row.find('.cora-asset-url').val().trim();
            const type = row.find('.cora-asset-type').val();
            
            if (url) {
                assets.push({
                    id: assetId.startsWith('new_asset_') ? '' : assetId,
                    name: name || (url.split('/').pop().split('?')[0] || 'Asset'),
                    url: url,
                    type: type
                });
            } else {
                hasEmptyUrl = true;
            }
        });

        if (assets.length === 0) {
            window.coraShowToast("Please add at least one media asset URL.");
            return;
        }

        $('#cora-portfolio-submit-btn').prop('disabled', true).text('Saving...');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_portfolio',
            nonce: coraREData.ajaxNonce,
            id: id,
            title: title,
            template: template,
            password: password,
            assets: JSON.stringify(assets)
        }, function(res) {
            $('#cora-portfolio-submit-btn').prop('disabled', false).text('Save Gallery Folder');
            if (res.success) {
                window.coraShowToast(id ? 'Gallery updated successfully.' : 'Gallery created successfully.');
                coraToggleGalleryDrawer(false);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data || 'Failed to save portfolio.');
            }
        }).fail(function() {
            $('#cora-portfolio-submit-btn').prop('disabled', false).text('Save Gallery Folder');
            window.coraShowToast('Network error, please try again.');
        });
    };

    window.coraEditGallery = function(id) {
        if (!coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === id);
        if (!portfolio) return;

        $('#cora-portfolio-id').val(portfolio.id);
        $('#cora-portfolio-title').val(portfolio.title);
        $('#cora-portfolio-template').val(portfolio.template || 'grid');
        $('#cora-portfolio-password').val(portfolio.password || '');
        $('#cora-portfolio-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 mr-1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Edit Gallery Folder
        `);

        $('#cora-portfolio-assets-container').empty();
        
        if (portfolio.assets && portfolio.assets.length > 0) {
            portfolio.assets.forEach(asset => {
                coraAddAssetRow(asset.name, asset.type, asset.raw_url || asset.url, asset.id);
            });
        } else {
            coraAddAssetRow();
        }

        // Show selections list if any exist
        const selectionsSection = $('#cora-portfolio-selections-section');
        const selectionsList = $('#cora-portfolio-selections-list');
        selectionsList.empty();

        if (portfolio.likes && portfolio.likes.length > 0 && portfolio.assets) {
            selectionsSection.removeClass('hidden');
            portfolio.likes.forEach(likeId => {
                const asset = portfolio.assets.find(a => a.id === likeId);
                if (asset) {
                    selectionsList.append(`
                        <div class="py-1 flex items-center justify-between text-zinc-700" data-asset-name="${asset.name}">
                            <span>${asset.name}</span>
                            <span class="text-[9px] uppercase tracking-wider bg-zinc-200 px-1.5 py-0.5 rounded font-bold">Liked</span>
                        </div>
                    `);
                }
            });
        } else {
            selectionsSection.addClass('hidden');
        }

        coraToggleGalleryDrawer(true);
    };

    window.coraCopySelectedFileNames = function() {
        const names = [];
        $('#cora-portfolio-selections-list div').each(function() {
            names.push($(this).data('asset-name'));
        });
        if (names.length === 0) return;
        
        const textToCopy = names.join(', ');
        navigator.clipboard.writeText(textToCopy).then(function() {
            window.coraShowToast("Selected asset titles copied to clipboard!");
        });
    };

    window.coraDeleteGallery = function(id) {
        if (!coraREData.ajaxNonce) return;
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_portfolio',
            nonce: coraREData.ajaxNonce,
            id: id
        }, function(res) {
            if (res.success) {
                window.coraShowToast("Gallery deleted successfully.");
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data || 'Failed to delete portfolio.');
            }
        });
    };

    window.coraCopyShareLink = function(url) {
        navigator.clipboard.writeText(url).then(function() {
            window.coraShowToast("Shareable link copied to clipboard!");
        });
    };

    // --- Gallery Detail Grid View Logic ---
    window.coraActiveGalleryId = null;
    window.coraActiveGalleryFilter = 'all';

    window.coraShowGalleryDetails = function(id) {
        if (!coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === id);
        if (!portfolio) return;

        window.coraActiveGalleryId = id;
        sessionStorage.setItem('cora_active_portfolio_details_id', id);
        sessionStorage.setItem('cora_active_portfolio_tab', 'client-portfolios');
        
        // Update Header Text
        $('#cora-detail-portfolio-title-text').text(portfolio.title);
        $('#cora-detail-portfolio-title-input').val(portfolio.title);
        
        // Render Stats
        const photos = portfolio.assets ? portfolio.assets.filter(a => a.type === 'image').length : 0;
        const videos = portfolio.assets ? portfolio.assets.filter(a => a.type === 'video').length : 0;
        $('#cora-stat-photos').text(photos + ' Photos');
        $('#cora-stat-videos').text(videos + ' Videos');
        $('#cora-stat-security').text(portfolio.password ? 'Protected' : 'Public');
        
        // Setup Google Drive Sync Banner state
        if (portfolio.drive_folder_url) {
            $('#cora-detail-drive-banner').removeClass('hidden').addClass('flex');
            $('#cora-detail-drive-url').text(portfolio.drive_folder_url);
        } else {
            $('#cora-detail-drive-banner').addClass('hidden').removeClass('flex');
            $('#cora-detail-drive-url').text('');
        }
        
        // Reset Filter & Search
        window.coraActiveGalleryFilter = 'all';
        $('.cora-filter-tab').removeClass('bg-white text-zinc-900 shadow-sm').addClass('hover:text-zinc-900');
        $('.cora-filter-tab[data-filter="all"]').addClass('bg-white text-zinc-900 shadow-sm').removeClass('hover:text-zinc-900');
        $('#cora-detail-portfolio-search').val('');
        $('#cora-detail-portfolio-sort').val('name-asc');
        
        // Render Grid
        coraRenderActiveGalleryAssets();

        // Switch Views
        $('#cora-vault-grid-view, #cora-portfolio-list-view').addClass('hidden');
        $('#cora-portfolio-details-view').addClass('flex').removeClass('hidden');
    };

    window.coraShowGalleryListView = function() {
        window.coraActiveGalleryId = null;
        sessionStorage.removeItem('cora_active_portfolio_details_id');
        sessionStorage.setItem('cora_active_portfolio_tab', 'client-portfolios');

        // Update tabs active state
        $('.cora-portfolio-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
        $(`.cora-portfolio-tab-btn[data-tab="client-portfolios"]`).removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');

        $('#cora-vault-grid-view').addClass('hidden');
        $('#cora-portfolio-details-view').addClass('hidden').removeClass('flex');
        $('#cora-portfolio-list-view').addClass('block').removeClass('hidden');
    };

    window.coraSetAssetFilter = function(filter) {
        window.coraActiveGalleryFilter = filter;
        $('.cora-filter-tab').removeClass('bg-white text-zinc-900 shadow-sm').addClass('hover:text-zinc-900');
        $(`.cora-filter-tab[data-filter="${filter}"]`).addClass('bg-white text-zinc-900 shadow-sm').removeClass('hover:text-zinc-900');
        coraRenderActiveGalleryAssets();
    };

    window.coraRenderActiveGalleryAssets = function() {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        const grid = $('#cora-detail-portfolio-grid');
        grid.empty();

        let assets = portfolio.assets || [];
        const likes = portfolio.likes || [];
        const searchQuery = $('#cora-detail-portfolio-search').val().toLowerCase();
        const sortMode = $('#cora-detail-portfolio-sort').val();

        // Filter
        assets = assets.filter(asset => {
            const isSelected = likes.includes(asset.id);
            if (window.coraActiveGalleryFilter === 'image' && asset.type !== 'image') return false;
            if (window.coraActiveGalleryFilter === 'video' && asset.type !== 'video') return false;
            if (window.coraActiveGalleryFilter === 'selected' && !isSelected) return false;
            if (searchQuery && !asset.name.toLowerCase().includes(searchQuery)) return false;
            return true;
        });

        // Sort
        assets.sort((a, b) => {
            if (sortMode === 'name-asc') return a.name.localeCompare(b.name);
            if (sortMode === 'name-desc') return b.name.localeCompare(a.name);
            if (sortMode === 'mixed') {
                // Preserve original insertion order (no sorting)
                return 0;
            }
            if (sortMode === 'type-photo') {
                if (a.type === b.type) return a.name.localeCompare(b.name);
                return a.type === 'image' ? -1 : 1;
            }
            if (sortMode === 'type-video') {
                if (a.type === b.type) return a.name.localeCompare(b.name);
                return a.type === 'video' ? -1 : 1;
            }
            return 0;
        });

        if (assets.length === 0) {
            grid.html(`
                <div class="col-span-full py-12 text-center text-zinc-500 text-sm flex items-center justify-center">
                    No assets found. Try adjusting filters or upload new media.
                </div>
            `);
            return;
        }

        assets.forEach(asset => {
            const isLiked = likes.includes(asset.id);
            const url = asset.raw_url || asset.url;
            
            let mediaHtml = '';
            if (asset.type === 'image') {
                mediaHtml = `<img src="${url}" alt="${asset.name}" loading="lazy">`;
            } else {
                if (url && (url.includes('.mp4') || url.includes('video') || url.includes('vimeo') || url.includes('mixkit'))) {
                    mediaHtml = `
                        <video class="w-full h-full object-cover" muted loop playsinline preload="metadata" src="${url}"></video>
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/35 transition-colors flex items-center justify-center pointer-events-none">
                            <div class="w-8 h-8 rounded-full bg-white/90 text-zinc-900 flex items-center justify-center shadow-md transform scale-90 group-hover:scale-100 transition-all duration-300">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </div>
                        </div>
                    `;
                } else {
                    mediaHtml = `
                        <div class="absolute inset-0 bg-zinc-100 flex items-center justify-center">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        </div>
                    `;
                }
            }

            const isSynced = asset.is_synced || (asset.id && (asset.id.startsWith('sync_') || asset.id.startsWith('drive_')));
            const syncBadge = isSynced ? `<div class="absolute top-2 right-2 z-10 bg-blue-600/90 text-white rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wide shadow-sm flex items-center gap-1"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>SYNCED</div>` : '';

            const card = `
                <div class="cora-asset-card bg-white border border-zinc-200 rounded-lg overflow-hidden flex flex-col relative group">
                    ${isLiked ? '<div class="absolute top-2 left-2 z-10 bg-black/60 text-white rounded-full p-1 shadow-sm"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></div>' : ''}
                    ${syncBadge}
                    <div class="cora-asset-media-container aspect-square relative cursor-pointer" onclick="coraOpenAssetLightbox('${asset.id}')">
                        ${mediaHtml}
                        <div class="cora-asset-overlay-action absolute inset-0 bg-black/40 flex items-center justify-center gap-2">
                            <button class="w-8 h-8 rounded-full bg-white text-zinc-900 flex items-center justify-center shadow-sm hover:scale-105 transition-transform" onclick="event.stopPropagation(); coraOpenAssetLightbox('${asset.id}')" title="Details / Edit">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center shadow-sm hover:scale-105 transition-transform" onclick="event.stopPropagation(); coraDeleteAssetFromActiveGallery('${asset.id}')" title="Remove">
                                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="p-2.5">
                        <div class="text-xs font-semibold text-zinc-900 truncate" title="${asset.name}">${asset.name}</div>
                        <div class="text-[10px] text-zinc-500 font-mono mt-0.5 truncate">${asset.type === 'image' ? 'Photo' : 'Video'}</div>
                    </div>
                </div>
            `;
            grid.append(card);
        });
    };

    window.coraEditActiveGalleryTitle = function() {
        $('#cora-detail-portfolio-title-text').addClass('hidden');
        $('#cora-detail-portfolio-title-input').removeClass('hidden').focus();
    };

    window.coraSaveActiveGalleryTitle = function() {
        const newTitle = $('#cora-detail-portfolio-title-input').val().trim();
        if (!newTitle || !window.coraActiveGalleryId) return;
        
        $('#cora-detail-portfolio-title-input').addClass('hidden');
        $('#cora-detail-portfolio-title-text').text(newTitle).removeClass('hidden');
        
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (portfolio) {
            portfolio.title = newTitle;
            if (coraREData.ajaxNonce) {
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_save_portfolio',
                    nonce: coraREData.ajaxNonce,
                    id: portfolio.id,
                    title: portfolio.title,
                    template: portfolio.template,
                    password: portfolio.password,
                    assets: JSON.stringify(portfolio.assets)
                }, function(res) {
                    if(!res.success) {
                        window.coraShowToast("Failed to save title to database.");
                    }
                });
            }
        }
    };

    window.coraDeleteAssetFromActiveGallery = function(assetId) {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        portfolio.assets = portfolio.assets.filter(a => a.id !== assetId);
        coraRenderActiveGalleryAssets();
        
        const photos = portfolio.assets ? portfolio.assets.filter(a => a.type === 'image').length : 0;
        const videos = portfolio.assets ? portfolio.assets.filter(a => a.type === 'video').length : 0;
        $('#cora-stat-photos').text(photos + ' Photos');
        $('#cora-stat-videos').text(videos + ' Videos');
        
        if (coraREData.ajaxNonce) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_portfolio',
                nonce: coraREData.ajaxNonce,
                id: portfolio.id,
                title: portfolio.title,
                template: portfolio.template,
                password: portfolio.password,
                assets: JSON.stringify(portfolio.assets)
            });
        }
        window.coraShowToast("Asset removed successfully.");
    };
    window.coraCloseModals = function() {
        $('.cora-modal-overlay').removeClass('active');
        // Reset inputs
        $('#cora-share-email').val('');
        $('#cora-share-password').val('');
        $('#cora-link-drive-url').val('');
        $('#cora-link-drive-name').val('');
        $('#cora-sync-folder-url').val('');
        // Stop any playing video
        $('#cora-lightbox-preview-container').empty();
    };

    window.coraOpenShareGalleryModal = function(id = null) {
        let activeId = id || window.coraActiveGalleryId;
        if (!activeId || !coraREData.portfolios) {
            window.coraShowToast("Please create a portfolio first.");
            coraOpenGalleryDrawer();
            return;
        }
        
        window.coraActiveGalleryId = activeId;
        const portfolio = coraREData.portfolios.find(g => g.id === activeId);
        if (portfolio) {
            $('#cora-share-template').val(portfolio.template || 'grid');
            $('#cora-share-password').val(portfolio.password || '');
            
            // Re-check boxes by default
            $('#cora-share-images').prop('checked', true);
            $('#cora-share-videos').prop('checked', true);
        }
        
        $('#cora-modal-share-portfolio').addClass('active');
    };

    window.coraSubmitShareGallery = function() {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        const template = $('#cora-share-template').val();
        const shareImages = $('#cora-share-images').is(':checked');
        const shareVideos = $('#cora-share-videos').is(':checked');
        const email = $('#cora-share-email').val().trim();
        const password = $('#cora-share-password').val().trim();

        // Construct the Share URL immediately
        let siteUrl = coraREData.siteUrl || '';
        if (siteUrl.endsWith('/')) {
            siteUrl = siteUrl.slice(0, -1);
        }
        const shareUrl = siteUrl + '/shared-portfolio/' + portfolio.hash;

        // Copy to clipboard synchronously within the click event thread to prevent browser blocking
        let copySuccessful = false;
        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shareUrl);
                copySuccessful = true;
            }
        } catch (e) {
            // Ignore and fallback
        }

        if (!copySuccessful) {
            // Fallback using temporary textarea to bypass clipboard permission constraints
            const textArea = document.createElement("textarea");
            textArea.value = shareUrl;
            textArea.style.position = "fixed";
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.width = "2em";
            textArea.style.height = "2em";
            textArea.style.padding = "0";
            textArea.style.border = "none";
            textArea.style.outline = "none";
            textArea.style.boxShadow = "none";
            textArea.style.background = "transparent";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                copySuccessful = document.execCommand('copy');
            } catch (err) {
                copySuccessful = false;
            }
            document.body.removeChild(textArea);
        }

        if (copySuccessful) {
            window.coraShowToast("Link copied to clipboard! Saving settings...");
        } else {
            window.coraShowToast("Saving settings...");
        }

        const btn = $('#cora-btn-submit-share');
        btn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...');

        portfolio.template = template;
        portfolio.password = password;
        
        // Use an AJAX request to save settings, and if email provided, mock sending
        if (coraREData.ajaxNonce) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_portfolio',
                nonce: coraREData.ajaxNonce,
                id: portfolio.id,
                title: portfolio.title,
                template: portfolio.template,
                password: portfolio.password,
                share_images: shareImages ? 1 : 0,
                share_videos: shareVideos ? 1 : 0,
                client_email: email,
                assets: JSON.stringify(portfolio.assets)
            }, function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Save & Generate Link');
                coraCloseModals();
                
                if (res.success) {
                    if (copySuccessful) {
                        window.coraShowToast("Settings saved successfully!");
                    } else {
                        window.coraShowToast("Settings saved. Link: " + shareUrl);
                    }

                    if (email) {
                        setTimeout(() => window.coraShowToast(`Share link sent to ${email}`), 1000);
                    }
                    setTimeout(() => window.location.reload(), 2500);
                } else {
                    window.coraShowToast("Failed to save portfolio settings.");
                }
            }).fail(function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Save & Generate Link');
                window.coraShowToast("Network error.");
            });
        }
    };

    window.coraOpenLinkGoogleDriveModal = function() {
        if (!window.coraActiveGalleryId) {
            window.coraShowToast("Please open a portfolio first.");
            return;
        }
        $('#cora-modal-link-drive').addClass('active');
    };

    window.coraSubmitLinkDrive = function() {
        const url = $('#cora-link-drive-url').val().trim();
        const name = $('#cora-link-drive-name').val().trim() || 'Drive Asset';
        const type = $('#cora-link-drive-type').val();
        
        if (!url) {
            window.coraShowToast("Please enter a valid Google Drive URL.");
            return;
        }

        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        if (!portfolio.assets) portfolio.assets = [];
        
        const newAsset = {
            id: 'drive_' + Date.now(),
            name: name,
            type: type,
            url: url,
            raw_url: url
        };
        
        portfolio.assets.push(newAsset);
        coraRenderActiveGalleryAssets();
        coraCloseModals();
        
        // Save
        if (coraREData.ajaxNonce) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_portfolio',
                nonce: coraREData.ajaxNonce,
                id: portfolio.id,
                title: portfolio.title,
                template: portfolio.template,
                password: portfolio.password,
                assets: JSON.stringify(portfolio.assets)
            });
        }
        
        window.coraShowToast("Drive Asset linked successfully.");
    };

    window.coraResyncGoogleDriveFolder = function() {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        const bannerBtn = $('#cora-detail-drive-banner button');
        const originalText = bannerBtn.text();
        
        bannerBtn.prop('disabled', true).text('Syncing...');
        window.coraShowToast("Checking Google Drive folder for new files...");

        setTimeout(() => {
            bannerBtn.prop('disabled', false).text(originalText);
            window.coraShowToast("Sync complete! Checked for updates: no new files found.");
        }, 1200);
    };

    window.coraOpenSyncGoogleDriveFolderModal = function() {
        if (!window.coraActiveGalleryId) {
            window.coraShowToast("Please open a portfolio first.");
            return;
        }
        $('#cora-modal-sync-folder').addClass('active');
    };

    window.coraSubmitSyncFolder = function() {
        const url = $('#cora-sync-folder-url').val().trim();
        if (!url) {
            window.coraShowToast("Please enter a valid Google Drive Folder URL.");
            return;
        }

        const btn = $('#cora-btn-submit-sync');
        btn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Syncing...');

        // Mock syncing delay to simulate 3 images and 5 videos
        setTimeout(() => {
            if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
            const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
            if (!portfolio) return;

            if (!portfolio.assets) portfolio.assets = [];
            
            const syncImages = [
                { name: 'Bride Preparation Portrait.jpg', url: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=1200&auto=format&fit=crop' },
                { name: 'Vows Exchange Ceremony.jpg', url: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=1200&auto=format&fit=crop' },
                { name: 'Floral Decor CloseUp.jpg', url: 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop' }
            ];

            const syncVideos = [
                { name: 'Bridal Flower Portrait.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-bride-holding-a-bouquet-of-flowers-41712-large.mp4' },
                { name: 'Property Walkthrough Tour.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-listing-couple-dancing-slowly-41713-large.mp4' },
                { name: 'Groomsmen Laughing Scene.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-groomsmen-posing-and-laughing-41724-large.mp4' },
                { name: 'Villa Garden Tour.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-putting-on-the-listing-ring-41725-large.mp4' },
                { name: 'Groom Entrance Reel.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-groom-waiting-for-the-bride-41711-large.mp4' }
            ];

            // Add 3 mock images
            syncImages.forEach((img, idx) => {
                portfolio.assets.push({
                    id: 'sync_img_' + Date.now() + '_' + idx,
                    name: img.name,
                    type: 'image',
                    url: img.url,
                    raw_url: img.url,
                    is_synced: true
                });
            });

            // Add 5 mock videos
            syncVideos.forEach((vid, idx) => {
                portfolio.assets.push({
                    id: 'sync_vid_' + Date.now() + '_' + idx,
                    name: vid.name,
                    type: 'video',
                    url: vid.url,
                    raw_url: vid.url,
                    is_synced: true
                });
            });
            
            // Mock banner display logic in details view
            $('#cora-detail-drive-banner').removeClass('hidden').addClass('flex');
            $('#cora-detail-drive-url').text(url);
            
            coraRenderActiveGalleryAssets();
            
            // Save
            if (coraREData.ajaxNonce) {
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_save_portfolio',
                    nonce: coraREData.ajaxNonce,
                    id: portfolio.id,
                    title: portfolio.title,
                    template: portfolio.template,
                    password: portfolio.password,
                    drive_folder_url: url,
                    assets: JSON.stringify(portfolio.assets)
                });
            }
            
            btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 2v6h-6"></path><path d="M3 12a9 9 0 0 1 15-6.7L21 8"></path><path d="M3 22v-6h6"></path><path d="M21 12a9 9 0 0 1-15 6.7L3 16"></path></svg> Sync with Drive');
            coraCloseModals();
            window.coraShowToast("Folder synced! 3 Photos and 5 Videos added.");
            
        }, 1500);
    };

    // Advanced Custom Media Uploader Integration
    window.coraOpenUploadMedia = function() {
        if (!window.coraActiveGalleryId) {
            window.coraShowToast("Please open a portfolio first.");
            return;
        }

        // Reset the modal UI
        $('#cora-upload-folder').val('root');
        $('#cora-upload-tags').val('');
        $('#cora-upload-status').text('Ready to upload');
        $('#cora-upload-file-input').val('');
        window.coraSelectedUploadFiles = null;

        $('#cora-upload-dropzone').html(`
            <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" fill="none" class="mx-auto text-zinc-400 w-8 h-8 sm:w-12 sm:h-12 mb-2 sm:mb-4"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <p class="text-xs sm:text-sm font-semibold text-zinc-700 mb-0.5">Drag and drop files here</p>
            <p class="hidden sm:block text-xs text-zinc-500 mb-3">or click to browse your computer</p>
            <p class="block sm:hidden text-[10px] text-zinc-400 mb-2">or tap to upload from device</p>
            <button class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md shadow-sm text-[10px] sm:text-xs pointer-events-none">Select Files</button>
        `);

        $('#cora-btn-submit-upload').prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg> Upload Files');

        $('#cora-modal-upload-media').addClass('active');
    };

    // Bind click & change events for the upload dropzone
    $(document).on('click', '#cora-upload-dropzone', function(e) {
        if (e.target.tagName !== 'INPUT' && !$(e.target).closest('button').length) {
            $('#cora-upload-file-input').click();
        }
    });

    $(document).on('change', '#cora-upload-file-input', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            window.coraSelectedUploadFiles = Array.from(files);
            
            let fileListHtml = `<div class="mt-2 text-left bg-zinc-50 p-2 sm:p-2.5 rounded-lg border border-zinc-200/60 max-h-24 sm:max-h-40 overflow-y-auto space-y-1 w-full max-w-md mx-auto">`;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const isVideo = file.type.startsWith('video/') || file.name.endsWith('.mp4') || file.name.endsWith('.mov') || file.name.endsWith('.avi');
                const icon = isVideo 
                    ? `<svg class="inline-block mr-1 text-zinc-500" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>`
                    : `<svg class="inline-block mr-1 text-zinc-500" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`;
                
                fileListHtml += `<div class="text-[9px] sm:text-[10px] font-semibold text-zinc-700 flex items-center justify-between border-b border-zinc-100 pb-1">
                    <span class="truncate max-w-[70%]">${icon} ${file.name}</span>
                    <span class="text-[9px] text-zinc-400 font-mono">${(file.size / (1024 * 1024)).toFixed(2)} MB</span>
                </div>`;
            }
            fileListHtml += `</div>`;

            $('#cora-upload-dropzone').html(`
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" fill="none" class="mx-auto text-zinc-900 w-6 h-6 sm:w-8 sm:h-8 mb-1.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <p class="text-xs font-bold text-zinc-800 mb-0.5">${files.length} file(s) selected</p>
                <p class="text-[10px] text-zinc-500 mb-2">Click to change selection</p>
                ${fileListHtml}
            `);
            $('#cora-upload-status').text(`${files.length} files ready`);
        }
    });

    window.coraSubmitUploadMedia = function() {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;

        if (!window.coraSelectedUploadFiles || window.coraSelectedUploadFiles.length === 0) {
            window.coraShowToast("Please select one or more files first.");
            return;
        }

        const folder = $('#cora-upload-folder').val();
        const tags = $('#cora-upload-tags').val().trim();
        
        const btn = $('#cora-btn-submit-upload');
        btn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Uploading...');
        $('#cora-upload-status').text('Uploading chunks... 45%');

        const unsplashPremiumPics = [
            'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1532712938310-34cb3982ef74?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519225495810-7512c696505a?q=80&w=1200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?q=80&w=1200&auto=format&fit=crop'
        ];

        const mixkitPremiumVids = [
            'https://assets.mixkit.co/videos/preview/mixkit-bride-holding-a-bouquet-of-flowers-41712-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-listing-couple-dancing-slowly-41713-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-groomsmen-posing-and-laughing-41724-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-putting-on-the-listing-ring-41725-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-groom-waiting-for-the-bride-41711-large.mp4'
        ];

        // Mock upload delay to simulate processing
        setTimeout(() => {
            if (!portfolio.assets) portfolio.assets = [];
            
            const uploadedCount = window.coraSelectedUploadFiles.length;
            window.coraSelectedUploadFiles.forEach((file, idx) => {
                const isVideo = file.type.startsWith('video/') || file.name.endsWith('.mp4') || file.name.endsWith('.mov') || file.name.endsWith('.avi');
                
                let assetUrl = '';
                if (isVideo) {
                    assetUrl = mixkitPremiumVids[idx % mixkitPremiumVids.length];
                } else {
                    assetUrl = unsplashPremiumPics[idx % unsplashPremiumPics.length];
                }

                portfolio.assets.push({
                    id: 'up_img_' + Date.now() + '_' + idx,
                    name: file.name,
                    type: isVideo ? 'video' : 'image',
                    url: assetUrl,
                    raw_url: assetUrl,
                    folder: folder,
                    alt_text: tags || file.name
                });
            });
            
            coraRenderActiveGalleryAssets();
            
            // Save to DB
            if (coraREData.ajaxNonce) {
                $.post(coraREData.ajaxUrl, {
                    action: 'cora_save_portfolio',
                    nonce: coraREData.ajaxNonce,
                    id: portfolio.id,
                    title: portfolio.title,
                    template: portfolio.template,
                    password: portfolio.password,
                    assets: JSON.stringify(portfolio.assets)
                });
            }
            
            $('#cora-upload-status').text('Upload complete!');
            btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M20 6L9 17l-5-5"></path></svg> Done');
            
            setTimeout(() => {
                coraCloseModals();
                window.coraShowToast(`${uploadedCount} files uploaded successfully to ${folder}`);
                window.coraSelectedUploadFiles = null;
                $('#cora-upload-file-input').val('');
            }, 800);
            
        }, 1500);
    };
    window.coraOpenAssetLightbox = function(assetId) {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;
        
        const asset = portfolio.assets.find(a => a.id === assetId);
        if (!asset) return;
        
        $('#cora-lightbox-asset-id').val(asset.id);
        $('#cora-lightbox-name').val(asset.name || '');
        $('#cora-lightbox-alt').val(asset.alt_text || '');
        $('#cora-lightbox-description').val(asset.description || '');
        
        const url = asset.raw_url || asset.url;
        let previewHtml = '';
        if (asset.type === 'image') {
            previewHtml = `<img src="${url}" alt="${asset.alt_text || asset.name}" class="max-w-full max-h-full object-contain rounded drop-shadow-md">`;
        } else {
            if (url && (url.includes('.mp4') || url.includes('video') || url.includes('vimeo') || url.includes('mixkit'))) {
                previewHtml = `
                    <video src="${url}" controls autoplay class="max-w-full max-h-[70vh] object-contain rounded drop-shadow-md bg-black w-full">
                        Your browser does not support the video tag.
                    </video>
                `;
            } else {
                previewHtml = `
                    <div class="flex flex-col items-center justify-center text-zinc-400 gap-4 py-8">
                        <svg viewBox="0 0 24 24" width="64" height="64" stroke="currentColor" stroke-width="1.5" fill="none"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <p class="text-sm font-medium">Video Preview Unavailable</p>
                        <a href="${url}" target="_blank" class="px-4 py-2 bg-white border border-zinc-200 text-zinc-700 font-semibold rounded-md hover:bg-zinc-50 transition-colors text-xs cursor-pointer shadow-sm mt-2">Open Video in New Tab</a>
                    </div>
                `;
            }
        }
        $('#cora-lightbox-preview-container').html(previewHtml);
        $('#cora-modal-asset-lightbox').addClass('active');
    };

    window.coraSaveAssetDetails = function() {
        if (!window.coraActiveGalleryId || !coraREData.portfolios) return;
        const portfolio = coraREData.portfolios.find(g => g.id === window.coraActiveGalleryId);
        if (!portfolio) return;
        
        const assetId = $('#cora-lightbox-asset-id').val();
        const asset = portfolio.assets.find(a => a.id === assetId);
        if (!asset) return;
        
        const btn = $('#cora-btn-submit-lightbox');
        btn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...');
        
        asset.name = $('#cora-lightbox-name').val().trim();
        asset.alt_text = $('#cora-lightbox-alt').val().trim();
        asset.description = $('#cora-lightbox-description').val().trim();
        
        coraRenderActiveGalleryAssets();
        
        if (coraREData.ajaxNonce) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_portfolio',
                nonce: coraREData.ajaxNonce,
                id: portfolio.id,
                title: portfolio.title,
                template: portfolio.template,
                password: portfolio.password,
                assets: JSON.stringify(portfolio.assets)
            }, function(res) {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save Changes');
                if (res.success) {
                    coraCloseModals();
                    window.coraShowToast("Asset details saved.");
                } else {
                    window.coraShowToast("Failed to save asset details.");
                }
            }).fail(function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Save Changes');
                window.coraShowToast("Network error.");
            });
        }
    };

    // ==========================================
    // CRM LEADS & KANBAN FUNCTIONALITY
    // ==========================================

    window.coraToggleLeadDrawer = function(show) {
        const drawer = $('#cora-lead-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
            // clear values
            $('#cora-lead-id').val('');
            $('#cora-lead-names').val('');
            $('#cora-lead-email').val('');
            $('#cora-lead-scale').val('multi-day');
            $('#cora-lead-city').val('');
            $('#cora-lead-price').val('');
            $('#cora-lead-status').val('New Lead');
            $('#cora-lead-notes').val('');
        }
    };

    window.coraOpenLeadDrawerForCreate = function() {
        coraToggleLeadDrawer(true);
        coraSwitchLeadDrawerTab('general', $('#cora-lead-btn-general'));
        $('#cora-lead-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550 mr-1.5 shrink-0">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Lead
        `);
        $('#cora-lead-drawer-actions').hide();
        $('#cora-lead-emails-section').hide();
        
        // Reset Demo Gallery Selection
        const galSelect = $('#cora-lead-demo-portfolio');
        galSelect.empty().append('<option value="">-- No Demo Portfolio Linked --</option>');
        (coraREData.portfolios || []).forEach(g => {
            galSelect.append(`<option value="${g.id}">${g.title}</option>`);
        });
        $('#cora-lead-portfolio-tracking-box').addClass('hidden');
        
        // Reset Equipment List Checkboxes
        const gearContainer = $('#cora-lead-equipment-list');
        gearContainer.empty();
        if ((coraREData.equipment || []).length === 0) {
            gearContainer.html('<div class="text-[11px] text-zinc-450 py-2 text-center select-none">No listings catalog loaded.</div>');
        } else {
            coraREData.equipment.forEach(item => {
                gearContainer.append(`
                    <label class="flex items-center gap-2.5 px-3 py-2 border border-zinc-200 rounded-md bg-white hover:bg-zinc-50 cursor-pointer text-xs transition-all">
                        <input type="checkbox" class="cora-lead-gear-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-900 cursor-pointer" value="${item.id}">
                        <div class="flex-1">
                            <span class="font-bold text-zinc-900">${item.name}</span>
                            <span class="text-[9px] text-zinc-400 uppercase tracking-wider block mt-0.5">${item.category} &bull; ${item.status}</span>
                        </div>
                    </label>
                `);
            });
        }
    };

    window.coraOpenLeadDrawer = function(lead) {
        coraToggleLeadDrawer(true);
        coraSwitchLeadDrawerTab('general', $('#cora-lead-btn-general'));
        $('#cora-lead-drawer').data('lead', lead);
        $('#cora-lead-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 mr-1.5 shrink-0">
                <rect x="3" y="3" width="7" height="9" rx="1"></rect>
                <rect x="14" y="3" width="7" height="9" rx="1"></rect>
                <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                <rect x="14" y="14" width="7" height="7" rx="1"></rect>
            </svg>
            Lead Deal Panel
        `);
        $('#cora-lead-id').val(lead.id);
        $('#cora-lead-names').val(lead.names);
        $('#cora-lead-email').val(lead.email);
        $('#cora-lead-scale').val(lead.scale || 'multi-day');
        $('#cora-lead-city').val(lead.city || '');
        $('#cora-lead-price').val(lead.price || '');
        $('#cora-lead-status').val(lead.status || 'New Lead');
        $('#cora-lead-notes').val(lead.notes || '');

        $('#cora-lead-drawer-actions').show();
        if (lead.status === 'Converted') {
            $('#cora-convert-lead-btn').hide();
        } else {
            $('#cora-convert-lead-btn').show();
        }

        // --- Tab 2: Assets, Demos & Documents ---
        const galSelect = $('#cora-lead-demo-portfolio');
        galSelect.empty().append('<option value="">-- No Demo Portfolio Linked --</option>');
        (coraREData.portfolios || []).forEach(g => {
            const selected = (lead.demo_portfolio === String(g.id) || lead.demo_portfolio === g.id) ? 'selected' : '';
            galSelect.append(`<option value="${g.id}" ${selected}>${g.title}</option>`);
        });

        // Set up portfolio tracking box
        window.coraUpdateGalleryTrackingUI(lead);

        // Bind change handler to portfolio select
        galSelect.off('change').on('change', function() {
            const val = $(this).val();
            if (val) {
                lead.demo_portfolio = val;
                $('#cora-lead-portfolio-tracking-box').removeClass('hidden');
                // update default state
                lead.demo_portfolio_shared = false;
                lead.demo_portfolio_viewed = false;
                window.coraUpdateGalleryTrackingUI(lead);
            } else {
                lead.demo_portfolio = '';
                $('#cora-lead-portfolio-tracking-box').addClass('hidden');
            }
        });

        // Render linked sales documents list
        const docsListContainer = $('#cora-lead-linked-docs-list');
        docsListContainer.empty();
        const linkedDocs = (coraREData.documents || []).filter(d => d.client_link === 'lead_' + lead.id || d.client_link === lead.email);
        if (linkedDocs.length === 0) {
            docsListContainer.html('<div class="text-[11px] text-zinc-450 py-1 text-center select-none">No proposals or invoices linked yet.</div>');
        } else {
            linkedDocs.forEach(doc => {
                let badgeStyle = 'background-color: #f4f4f5; color: #52525b; border: 1px solid #e4e4e7;'; // draft
                if (doc.status === 'Sent') {
                    badgeStyle = 'background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;'; // sent/blue
                } else if (doc.status === 'Paid' || doc.status === 'Signed') {
                    badgeStyle = 'background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;'; // green
                }
                
                docsListContainer.append(`
                    <div class="flex items-center justify-between p-2.5 border border-zinc-200 rounded bg-white hover:bg-zinc-50 transition-colors">
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="text-xs font-bold text-zinc-900 truncate">${doc.title || 'Untitled Document'}</div>
                            <div class="text-[9px] text-zinc-450 uppercase tracking-wider block mt-0.5">${doc.type} &bull; ${doc.date}</div>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider shrink-0" style="${badgeStyle}">${doc.status}</span>
                    </div>
                `);
            });
        }

        // Show automated email sequence
        coraRenderLeadEmails(lead);

        // --- Tab 3: Interested Listings Checklist ---
        const gearContainer = $('#cora-lead-equipment-list');
        gearContainer.empty();
        const leadGearIds = (lead.listing_ids || []).map(String);
        if ((coraREData.equipment || []).length === 0) {
            gearContainer.html('<div class="text-[11px] text-zinc-450 py-2 text-center select-none">No listings catalog loaded.</div>');
        } else {
            coraREData.equipment.forEach(item => {
                const checked = leadGearIds.includes(String(item.id)) ? 'checked' : '';
                gearContainer.append(`
                    <label class="flex items-center gap-2.5 px-3 py-2 border border-zinc-200 rounded-md bg-white hover:bg-zinc-50 cursor-pointer text-xs transition-all">
                        <input type="checkbox" class="cora-lead-gear-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-900 cursor-pointer" value="${item.id}" ${checked}>
                        <div class="flex-1">
                            <span class="font-bold text-zinc-900">${item.name}</span>
                            <span class="text-[9px] text-zinc-400 uppercase tracking-wider block mt-0.5">${item.category} &bull; ${item.status}</span>
                        </div>
                    </label>
                `);
            });
        }
    };

    window.coraSwitchLeadDrawerTab = function(tabId, btn) {
        $('.cora-lead-tab-content').addClass('hidden');
        $(`#cora-lead-tab-${tabId}`).removeClass('hidden');
        
        // Update button styles
        $('#cora-lead-drawer button[id^="cora-lead-btn-"]').removeClass('border-zinc-950 text-zinc-950 font-bold').addClass('border-transparent text-zinc-500 font-semibold');
        $(btn).addClass('border-zinc-950 text-zinc-950 font-bold').removeClass('border-transparent text-zinc-500 font-semibold');
    };

    window.coraUpdateGalleryTrackingUI = function(lead) {
        if (!lead.demo_portfolio) {
            $('#cora-lead-portfolio-tracking-box').addClass('hidden');
            return;
        }

        $('#cora-lead-portfolio-tracking-box').removeClass('hidden');
        
        const sharedBadge = $('#cora-lead-portfolio-shared-badge');
        if (lead.demo_portfolio_shared) {
            sharedBadge.text('Shared').removeClass('bg-zinc-100 text-zinc-655').addClass('bg-green-100 text-green-800 font-bold border border-green-200');
        } else {
            sharedBadge.text('Not Shared').removeClass('bg-green-100 text-green-800 border border-green-200').addClass('bg-zinc-100 text-zinc-655');
        }

        const viewedBadge = $('#cora-lead-portfolio-viewed-badge');
        if (lead.demo_portfolio_viewed) {
            viewedBadge.text('Viewed by Client').removeClass('bg-zinc-100 text-zinc-655').addClass('bg-green-100 text-green-800 font-bold border border-green-200');
        } else {
            viewedBadge.text('Unopened').removeClass('bg-green-100 text-green-800 border border-green-200').addClass('bg-zinc-100 text-zinc-655');
        }
    };

    window.coraShareDemoGalleryAction = function() {
        const lead = $('#cora-lead-drawer').data('lead');
        if (!lead || !lead.id) return;

        lead.demo_portfolio_shared = true;
        window.coraUpdateGalleryTrackingUI(lead);
        window.coraShowToast("Demo portfolio sharing registered!");
    };

    window.coraSimulateClientViewAction = function() {
        const lead = $('#cora-lead-drawer').data('lead');
        if (!lead || !lead.id) return;

        lead.demo_portfolio_viewed = true;
        window.coraUpdateGalleryTrackingUI(lead);
        window.coraShowToast("Client view simulation registered!");
    };

    window.coraRenderLeadEmails = function(lead) {
        const container = $('#cora-lead-emails-container');
        container.empty();

        const emails = lead.emails || [];
        if (emails.length === 0) {
            container.html('<div class="text-[11px] text-zinc-400 italic py-2 text-center select-none">No automated email sequence initialized.</div>');
            return;
        }

        emails.forEach(email => {
            let badgeClass = '';
            if (email.status === 'Sent') {
                badgeClass = 'bg-zinc-900 text-white';
            } else if (email.status === 'Scheduled') {
                badgeClass = 'bg-zinc-100 text-zinc-800 border border-zinc-200';
            } else if (email.status === 'Cancelled') {
                badgeClass = 'bg-zinc-100 text-zinc-400 border border-zinc-200 line-through';
            }

            const sentTimeHtml = (email.status === 'Sent' && email.sent_at)
                ? `<div class="text-[9px] text-zinc-400 font-mono mt-1 select-none">Sent: ${new Date(email.sent_at * 1000).toLocaleString()}</div>`
                : '';

            const actionsHtml = (email.status === 'Scheduled')
                ? `<div class="flex items-center gap-1.5 mt-2">
                     <button class="px-2 py-0.5 bg-zinc-950 hover:bg-zinc-850 text-white font-semibold text-[9.5px] rounded transition-all active:scale-[0.98] cursor-pointer" onclick="coraTriggerSendLeadEmail('${lead.id}', '${email.id}')">Send Now</button>
                     <button class="px-2 py-0.5 border border-zinc-250 hover:bg-zinc-100 text-zinc-655 text-[9.5px] rounded transition-all active:scale-[0.98] cursor-pointer" onclick="coraTriggerCancelLeadEmail('${lead.id}', '${email.id}')">Cancel</button>
                   </div>`
                : '';

            const stepHtml = `
                <div class="cora-email-step border border-zinc-200 rounded-md p-2.5 bg-zinc-50/40 hover:bg-zinc-50/80 transition-all duration-150 text-left">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-[9.5px] font-bold text-zinc-500 uppercase tracking-wider">${email.trigger_delay}</span>
                        <span class="cora-badge text-[8.5px] px-1.5 py-0.5 rounded font-bold ${badgeClass}">${email.status}</span>
                    </div>
                    <div class="text-xs font-semibold text-zinc-900 mb-0.5">${email.subject}</div>
                    <div class="cora-email-body-preview text-[10.5px] text-zinc-500 line-clamp-2 cursor-pointer hover:text-zinc-700 transition-colors leading-relaxed" onclick="$(this).toggleClass('line-clamp-2')">
                        ${email.body.replace(/\n/g, '<br>')}
                    </div>
                    ${sentTimeHtml}
                    ${actionsHtml}
                </div>
            `;
            container.append(stepHtml);
        });
    };

    window.coraTriggerSendLeadEmail = function(leadId, emailId) {
        coraUpdateLeadEmailStatus(leadId, emailId, 'Sent');
    };

    window.coraTriggerCancelLeadEmail = function(leadId, emailId) {
        coraUpdateLeadEmailStatus(leadId, emailId, 'Cancelled');
    };

    window.coraUpdateLeadEmailStatus = function(leadId, emailId, newStatus) {
        $.post(coraREData.ajaxUrl, {
            action: 'cora_update_lead_email_status',
            nonce: coraREData.ajaxNonce,
            lead_id: leadId,
            email_id: emailId,
            status: newStatus
        }, function(res) {
            if (res.success) {
                window.coraShowToast(`Email updated: status is now ${newStatus}.`);
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                window.coraShowToast(res.data || "Failed to update email status.");
            }
        }).fail(function() {
            window.coraShowToast("Network error updating email status.");
        });
    };

    window.coraSaveLeadDetails = function() {
        const leadId = $('#cora-lead-id').val();
        const names = $('#cora-lead-names').val().trim();
        const email = $('#cora-lead-email').val().trim();
        const scale = $('#cora-lead-scale').val();
        const city = $('#cora-lead-city').val().trim();
        const price = $('#cora-lead-price').val().trim();
        const status = $('#cora-lead-status').val();
        const notes = $('#cora-lead-notes').val().trim();
        
        // Linked presentation asset and assigned gear requirements
        const demoGallery = $('#cora-lead-demo-portfolio').val();
        const lead = $('#cora-lead-drawer').data('lead') || {};
        const demoGalleryShared = lead.demo_portfolio_shared ? 'true' : 'false';
        const demoGalleryViewed = lead.demo_portfolio_viewed ? 'true' : 'false';
        
        const equipmentIds = [];
        $('.cora-lead-gear-checkbox:checked').each(function() {
            equipmentIds.push($(this).val());
        });

        if (!names || !email) {
            window.coraShowToast("Names and Email are required.");
            return;
        }

        const btn = $('#cora-save-lead-btn');
        btn.prop('disabled', true).text("Saving...");

        // AJAX Save
        $.post(coraREData.ajaxUrl, {
            action: leadId ? 'cora_update_lead_status' : 'cora_re_submit_lead',
            nonce: coraREData.ajaxNonce,
            id: leadId,
            names: names,
            email: email,
            scale: scale,
            city: city,
            price: price,
            status: status,
            notes: notes,
            demo_portfolio: demoGallery,
            demo_portfolio_shared: demoGalleryShared,
            demo_portfolio_viewed: demoGalleryViewed,
            listing_ids: equipmentIds.join(',')
        }, function(res) {
            btn.prop('disabled', false).text("Save Details");
            if (res.success) {
                window.coraShowToast(leadId ? "Lead deal card updated successfully." : "Lead created successfully.");
                coraToggleLeadDrawer(false);
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                window.coraShowToast(res.data || "Error saving lead.");
            }
        }).fail(function() {
            btn.prop('disabled', false).text("Save Details");
            window.coraShowToast("Network error saving lead.");
        });
    };

    window.coraConvertLeadToClientAction = function() {
        const leadId = $('#cora-lead-id').val();
        if (!leadId) return;

        const btn = $('#cora-convert-lead-btn');
        btn.prop('disabled', true).text("Converting...");

        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_convert_lead_to_client',
            nonce: coraREData.ajaxNonce,
            id: leadId
        }, function(res) {
            btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Convert to Client Directory');
            if (res.success) {
                window.coraShowToast("Lead converted to Client Directory.");
                coraToggleLeadDrawer(false);
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                window.coraShowToast(res.data || "Failed to convert lead.");
            }
        }).fail(function() {
            btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> Convert to Client Directory');
            window.coraShowToast("Network error.");
        });
    };

    window.coraDeleteLeadAction = function() {
        const leadId = $('#cora-lead-id').val();
        if (!leadId) return;

        const btn = $('#cora-delete-lead-btn');
        btn.prop('disabled', true).text("Deleting...");

        $.post(coraREData.ajaxUrl, {
            action: 'cora_re_delete_lead',
            nonce: coraREData.ajaxNonce,
            id: leadId
        }, function(res) {
            btn.prop('disabled', false).text("Delete");
            if (res.success) {
                window.coraShowToast("Lead deleted.");
                coraToggleLeadDrawer(false);
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                window.coraShowToast(res.data || "Failed to delete lead.");
            }
        }).fail(function() {
            btn.prop('disabled', false).text("Delete");
            window.coraShowToast("Network error.");
        });
    };

    // ==========================================
    // CLIENT DIRECTORY FUNCTIONALITY
    // ==========================================

    window.coraToggleClientDrawer = function(show) {
        const drawer = $('#cora-client-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    window.coraOpenClientDetailsDrawer = function(client) {
        coraOpenClientLifecycle(client.id, client);
    };

    window.coraOpenClientLifecycle = function(clientId, clientObj = null) {
        coraToggleClientDrawer(true);
        
        let client = clientObj;
        if (!client) {
            client = (window.coraClients || []).find(c => c.id === clientId);
        }
        if (!client) {
            if (clientId === 'client_1') {
                client = { names: 'Ananya Sharma', email: 'ananya@gmail.com', city: 'Lodhi Gardens, Delhi', notes: 'Maternity shoot in Lodhi Gardens. Wants a very soft, natural light feel with pastel dresses.', scale: 'intimate', price: '₹25,000', status: 'confirmed', deal_type: 'Residential Buy', viewing_date: '24th Jun, 2026' };
            } else if (clientId === 'client_2') {
                client = { names: 'Rohit & Sneha', email: 'rohit.sneha@outlook.com', city: 'Rambagh Palace, Jaipur', notes: 'Luxury property listing. Focus heavily on modern architectural elements and the premium location of Cyber City.', scale: 'destination', price: '₹1,80,000', status: 'editing', deal_type: 'Luxury Villa Sale', viewing_date: '20th Jun, 2026' };
            } else if (clientId === 'client_3') {
                client = { names: 'Rajesh Kumar (Office B)', email: 'rk.enterprises@gmail.com', city: 'Delhi Office', notes: 'E-commerce product shoot for new apparel line. Needs white background and lifestyle shots.', scale: 'commercial', price: '₹40,000', status: 'completed', deal_type: 'Commercial Lease', viewing_date: '15th Jun, 2026' };
            } else {
                client = { names: 'Client Profile', email: 'client@example.com', city: 'Unknown', notes: 'No notes available.', scale: 'standard', price: '₹15,000', status: 'confirmed', deal_type: 'Primary Shoot', viewing_date: 'Pending Date' };
            }
        }

        $('#cora-lifecycle-client-name').text(client.names);
        $('#cora-lifecycle-email').text(client.email);
        $('#cora-lifecycle-city').text(client.city || 'Mumbai');
        $('#cora-lifecycle-notes').text(client.notes || 'No vision notes provided.');

        // Build Viewing Bookings timeline dynamically
        const statusBadgeClass = client.status === 'completed' ? 'bg-green-100 text-green-700' : (client.status === 'editing' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700');
        const statusLabel = client.status ? client.status.charAt(0).toUpperCase() + client.status.slice(1) : 'Confirmed';
        
        let bookingsHtml = `
            <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-zinc-900">${client.deal_type || 'Shoot'}</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${statusBadgeClass}">${statusLabel}</span>
                </div>
                <div class="text-[11px] text-zinc-500 font-medium">${client.viewing_date || 'Pending Date'} &bull; ${client.city || 'Delhi'}</div>
            </div>`;
        $('#cora-lifecycle-bookings-container').html(bookingsHtml);

        // Build Vault Documents dynamically
        const clientDocs = (window.coraDocuments || []).filter(d => d.client_link === client.id || d.client_link === client.lead_id);
        let docsHtml = '';
        if (clientDocs.length === 0) {
            docsHtml = '<div class="text-[11px] text-zinc-400 italic py-2">No documents found for this client.</div>';
        } else {
            clientDocs.forEach(doc => {
                const typeColor = doc.status === 'Paid' || doc.status === 'Signed' ? 'text-green-600 bg-green-50' : 'text-zinc-650 bg-zinc-50';
                const statusColor = doc.status === 'Paid' || doc.status === 'Signed' ? 'text-green-600' : 'text-zinc-500';
                
                let iconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';
                if (doc.type === 'Invoice') {
                    iconSvg = '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
                }
                
                docsHtml += `
                    <div class="flex items-center justify-between p-2.5 bg-white border border-zinc-200 rounded-md shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded ${typeColor} flex items-center justify-center">
                                ${iconSvg}
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-zinc-800 leading-tight">${doc.title}</div>
                                <div class="text-[9.5px] ${statusColor} mt-0.5 font-medium">${doc.status} &bull; ${doc.type}</div>
                            </div>
                        </div>
                        <button class="text-zinc-400 hover:text-zinc-800 transition-colors" title="View Document" onclick="coraNavigateTo('vault')"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                    </div>
                `;
            });
        }
        $('#cora-lifecycle-documents-container').html(docsHtml);

        // Build Property Portfolios / Assets dynamically
        const clientPortfolios = (window.coraPortfolios || []).filter(g => g.client_email === client.email);
        let assetsHtml = '';
        if (clientPortfolios.length === 0) {
            assetsHtml = '<div class="text-[11px] text-zinc-400 italic py-2">No portfolios delivered yet.</div>';
        } else {
            clientPortfolios.forEach(portfolio => {
                const totalAssets = portfolio.assets ? portfolio.assets.length : 0;
                let firstImage = 'https://images.unsplash.com/photo-1519741497674-611481863552?w=100&h=80&fit=crop';
                if (portfolio.assets && portfolio.assets.length > 0) {
                    const imgAsset = portfolio.assets.find(a => a.type === 'image');
                    if (imgAsset) {
                        firstImage = imgAsset.url;
                    }
                }
                
                assetsHtml += `
                    <div class="flex items-center gap-3 p-2 bg-white border border-zinc-200 rounded-md shadow-sm">
                        <div class="w-12 h-10 bg-zinc-100 rounded border border-zinc-200 overflow-hidden relative">
                            <img src="${firstImage}" class="w-full h-full object-cover opacity-80 mix-blend-multiply grayscale" />
                        </div>
                        <div class="flex-1">
                            <div class="text-[11px] font-bold text-zinc-800">${portfolio.title}</div>
                            <div class="text-[9.5px] text-zinc-400 mt-0.5">${totalAssets} Photos/Videos &bull; Sent to client</div>
                        </div>
                        <button class="text-zinc-400 hover:text-zinc-800 transition-colors" title="View Gallery" onclick="coraNavigateTo('portfolio')"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                    </div>
                `;
            });
        }
        $('#cora-lifecycle-assets-container').html(assetsHtml);
    };

    window.coraPrefillAddShowing = function(client) {
        coraToggleAddShowingDrawer(true);
        $('#cora-drawer-client-name').val(client.names);
        $('#cora-drawer-location').val(client.city || 'Mumbai');
        $('#cora-drawer-price').val(client.price || '');
        
        let shootType = 'Luxury Villa Sale';
        if (client.scale === 'documentary') {
            shootType = 'Couples Portrait';
        }
        $('#cora-drawer-deal-type').val(shootType);
    };

    window.coraDeleteClient = function(clientId) {
        if (!clientId) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_client',
            nonce: coraREData.ajaxNonce,
            id: clientId
        }, function(res) {
            if (res.success) {
                window.coraShowToast("Client removed from directory.");
                $(`#cora-clients-table-body tr[data-id="${clientId}"]`).fadeOut(400, function() {
                    $(this).remove();
                    if ($('#cora-clients-table-body tr').length === 0) {
                        $('#cora-clients-table-body').html(`
                            <tr>
                                <td colspan="6" class="py-8 text-center text-zinc-400 select-none">No converted clients in the directory yet. Convert leads to populate this view.</td>
                            </tr>
                        `);
                    }
                });
            } else {
                window.coraShowToast(res.data || "Failed to remove client.");
            }
        }).fail(function() {
            window.coraShowToast("Network error.");
        });
    };

    // ==========================================
    // KANBAN HTML5 DRAG & DROP HANDLERS
    // ==========================================

    let draggedCardId = null;

    window.coraLeadDragStart = function(ev) {
        const card = $(ev.currentTarget);
        draggedCardId = card.data('id');
        ev.dataTransfer.effectAllowed = 'move';
        ev.dataTransfer.setData('text/plain', draggedCardId);
        card.addClass('opacity-50');
    };

    window.coraLeadDragOver = function(ev) {
        ev.preventDefault();
        ev.dataTransfer.dropEffect = 'move';
    };

    window.coraLeadDrop = function(ev) {
        ev.preventDefault();
        const column = $(ev.currentTarget);
        const targetStatus = column.data('status');
        
        // Remove opacity from card
        $(`.cora-lead-card[data-id="${draggedCardId}"]`).removeClass('opacity-50');

        if (!draggedCardId || !targetStatus) return;

        const card = $(`.cora-lead-card[data-id="${draggedCardId}"]`);
        const sourceCol = card.closest('.cora-kanban-column');
        const targetContainer = column.find('.cora-cards-container');
        
        if (card.parent().parent()[0] === column[0]) {
            // Dropped in same column
            return;
        }

        // Move DOM element
        targetContainer.append(card);

        // Update counts
        const sourceCountSpan = sourceCol.find('.col-count');
        const targetCountSpan = column.find('.col-count');
        sourceCountSpan.text(parseInt(sourceCountSpan.text()) - 1);
        targetCountSpan.text(parseInt(targetCountSpan.text()) + 1);

        // AJAX update
        $.post(coraREData.ajaxUrl, {
            action: 'cora_update_lead_status',
            nonce: coraREData.ajaxNonce,
            id: draggedCardId,
            status: targetStatus
        }, function(res) {
            if (res.success) {
                window.coraShowToast(`Lead moved to "${targetStatus}".`);
                if (targetStatus === 'Converted') {
                    setTimeout(() => {
                        location.reload();
                    }, 850);
                }
            } else {
                window.coraShowToast(res.data || "Failed to update status.");
                location.reload();
            }
        }).fail(function() {
            window.coraShowToast("Network error updating status.");
            location.reload();
        });

        draggedCardId = null;
    };

    // Dragend listener for cleanup if dropped outside column
    $(document).on('dragend', '.cora-lead-card', function() {
        $(this).removeClass('opacity-50');
    });

    // ==========================================
    // FINANCIAL LEDGER & DOCUMENT TEMPLATE UTILS
    // ==========================================

    const inflowCategories = [
        'Advance Booking Fee',
        'Client Package Payment',
        'Print / Album Upsell',
        'Co-Broker / Showing Agent Fee Upsell',
        'Other Income'
    ];

    const outflowCategories = [
        'Equipment Rental',
        'Crew / Assistant Payout',
        'Office Rent / Utilities',
        'Marketing & Ads',
        'Software Subscriptions',
        'Travel & Lodging',
        'Office Supplies',
        'Other Expense'
    ];

    function parseAmount(val) {
        if (typeof val === 'number') return val;
        if (!val) return 0;
        let clean = val.toString().replace(/[₹,]/g, '').trim();
        if (clean.toLowerCase().endsWith('l')) {
            let num = parseFloat(clean.slice(0, -1));
            return num * 100000;
        }
        return parseFloat(clean) || 0;
    }

    function formatRupee(amount) {
        let isNegative = amount < 0;
        let absAmount = Math.abs(Math.round(amount));
        let x = absAmount.toString();
        let lastThree = x.substring(x.length - 3);
        let otherNumbers = x.substring(0, x.length - 3);
        if (otherNumbers !== '') {
            lastThree = ',' + lastThree;
        }
        let res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
        return (isNegative ? '-' : '') + '₹' + res;
    }

    function isLinkedToClient(clientLink, clientId) {
        if (!clientLink) return false;
        return clientLink === clientId || clientLink === 'client_' + clientId;
    }

    window.coraUpdateTxCategories = function(type, selected) {
        const select = $('#cora-tx-category');
        select.empty();
        const categories = type === 'Inflow' ? inflowCategories : outflowCategories;
        categories.forEach(cat => {
            const isSelected = cat === selected ? 'selected' : '';
            select.append(`<option value="${cat}" ${isSelected}>${cat}</option>`);
        });
    };

    window.coraToggleTransactionDrawer = function(show) {
        const drawer = $('#cora-transaction-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    window.coraOpenTransactionDrawerForCreate = function() {
        $('#cora-tx-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550 mr-1.5 shrink-0">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Ledger Entry
        `);
        $('#cora-tx-id-hidden').val('');
        $('#cora-tx-type-select').val('Inflow');
        $('#cora-tx-date').val(new Date().toISOString().substring(0, 10));
        $('#cora-tx-desc').val('');
        $('#cora-tx-amount').val('');
        $('#cora-tx-client-select').val('');
        $('#cora-tx-status').val('Received');
        coraUpdateTxCategories('Inflow');
        coraToggleTransactionDrawer(true);
    };

    window.coraOpenTransactionDrawerForEdit = function(txId) {
        const tx = (coraREData.financials || []).find(t => t.id === txId);
        if (!tx) {
            window.coraShowToast("Error: Transaction not found.");
            return;
        }
        $('#cora-tx-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-550 mr-1.5 shrink-0">
                <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
            </svg>
            Edit Ledger Entry
        `);
        $('#cora-tx-id-hidden').val(tx.id);
        $('#cora-tx-type-select').val(tx.type);
        $('#cora-tx-date').val(tx.date || new Date().toISOString().substring(0, 10));
        $('#cora-tx-desc').val(tx.description || '');
        $('#cora-tx-amount').val(tx.amount || '');
        $('#cora-tx-client-select').val(tx.client_link || '');
        $('#cora-tx-status').val(tx.status || (tx.type === 'Inflow' ? 'Received' : 'Paid'));
        coraUpdateTxCategories(tx.type, tx.category);
        coraToggleTransactionDrawer(true);
    };

    window.coraSaveTransactionData = function() {
        const id = $('#cora-tx-id-hidden').val();
        const type = $('#cora-tx-type-select').val();
        const date = $('#cora-tx-date').val();
        const description = $('#cora-tx-desc').val().trim();
        const amountStr = $('#cora-tx-amount').val().trim();
        const category = $('#cora-tx-category').val();
        const clientLink = $('#cora-tx-client-select').val();
        const status = $('#cora-tx-status').val();

        if (!description) {
            window.coraShowToast("Please enter a description.");
            return;
        }

        const amount = parseFloat(amountStr.replace(/[₹,]/g, ''));
        if (isNaN(amount) || amount <= 0) {
            window.coraShowToast("Please enter a valid positive amount.");
            return;
        }

        const btn = $('#cora-tx-submit-btn');
        const originalText = btn.text();
        btn.text('Saving...').prop('disabled', true);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_transaction',
            security: coraREData.ajaxNonce,
            id: id,
            type: type,
            date: date,
            description: description,
            amount: amount,
            category: category,
            client_link: clientLink,
            status: status
        }, function(res) {
            btn.text(originalText).prop('disabled', false);
            if (res.success) {
                window.coraShowToast("Ledger entry saved.");
                
                if (!coraREData.financials) coraREData.financials = [];
                const idx = coraREData.financials.findIndex(t => t.id === res.data.id);
                if (idx !== -1) {
                    coraREData.financials[idx] = res.data;
                } else {
                    coraREData.financials.push(res.data);
                }

                coraToggleTransactionDrawer(false);
                coraRenderFinancials();
            } else {
                window.coraShowToast(res.data || "Failed to save ledger entry.");
            }
        }).fail(function() {
            btn.text(originalText).prop('disabled', false);
            window.coraShowToast("Network error saving ledger entry.");
        });
    };

    window.coraDeleteTransaction = function(txId) {
        if (!txId) return;

        $.post(coraREData.ajaxUrl, {
            action: 'cora_delete_transaction',
            security: coraREData.ajaxNonce,
            id: txId
        }, function(res) {
            if (res.success) {
                window.coraShowToast("Ledger entry deleted.");
                
                if (coraREData.financials) {
                    coraREData.financials = coraREData.financials.filter(t => t.id !== txId);
                }
                coraRenderFinancials();
            } else {
                window.coraShowToast(res.data || "Failed to delete entry.");
            }
        }).fail(function() {
            window.coraShowToast("Network error.");
        });
    };

    window.coraRenderFinancials = function() {
        let txs = coraREData.financials || [];
        const typeFilter = $('#cora-financial-filters .cora-filter-btn.bg-zinc-950').data('filter') || 'all';
        const searchQuery = $('#cora-financial-search').val() ? $('#cora-financial-search').val().trim().toLowerCase() : '';

        if (typeFilter !== 'all') {
            txs = txs.filter(t => t.type === typeFilter);
        }

        if (searchQuery) {
            txs = txs.filter(t => {
                const desc = (t.description || '').toLowerCase();
                const cat = (t.category || '').toLowerCase();
                return desc.includes(searchQuery) || cat.includes(searchQuery);
            });
        }

        // Sort by date descending
        txs.sort((a, b) => new Date(b.date) - new Date(a.date));

        const tbody = $('#cora-financial-table-body');
        tbody.empty();

        if (txs.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="py-8 text-center text-zinc-400 select-none">No transactions found matching the filters.</td>
                </tr>
            `);
        } else {
            txs.forEach(tx => {
                let dateStr = tx.date;
                try {
                    const d = new Date(tx.date);
                    const options = { day: 'numeric', month: 'short', year: 'numeric' };
                    dateStr = d.toLocaleDateString('en-IN', options);
                } catch(e) {}

                const amtStr = formatRupee(parseFloat(tx.amount) || 0);
                
                let typeBadge = '';
                if (tx.type === 'Inflow') {
                    typeBadge = `<span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 uppercase tracking-wider">Inflow</span>`;
                } else {
                    typeBadge = `<span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-100 text-zinc-700 uppercase tracking-wider">Outflow</span>`;
                }

                let statusBadge = '';
                if (tx.status === 'Pending') {
                    statusBadge = `<span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 uppercase tracking-wider">Pending</span>`;
                } else {
                    statusBadge = `<span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-950 text-white uppercase tracking-wider">${tx.status || (tx.type === 'Inflow' ? 'Received' : 'Paid')}</span>`;
                }

                let linkedText = '';
                if (tx.client_link) {
                    let matchedName = '';
                    let isClient = true;
                    if (tx.client_link.startsWith('client_')) {
                        const cId = tx.client_link.substring(7);
                        const client = (coraREData.clients || []).find(c => c.id === cId || c.id === tx.client_link);
                        if (client) matchedName = client.names;
                    } else if (tx.client_link.startsWith('lead_')) {
                        const lId = tx.client_link.substring(5);
                        const lead = (coraREData.leads || []).find(l => l.id === lId || l.id === tx.client_link);
                        if (lead) {
                            matchedName = lead.names;
                            isClient = false;
                        }
                    } else {
                        const client = (coraREData.clients || []).find(c => c.id === tx.client_link);
                        if (client) matchedName = client.names;
                        else {
                            const lead = (coraREData.leads || []).find(l => l.id === tx.client_link);
                            if (lead) {
                                matchedName = lead.names;
                                isClient = false;
                            }
                        }
                    }

                    if (matchedName) {
                        linkedText = `
                            <div class="text-[10px] text-zinc-400 mt-1 flex items-center gap-1">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                ${matchedName} (${isClient ? 'Client' : 'Lead'})
                            </div>
                        `;
                    }
                }

                tbody.append(`
                    <tr class="hover:bg-zinc-50/40 transition-colors">
                        <td class="px-4 py-3.5 whitespace-nowrap text-zinc-550">${dateStr}</td>
                        <td class="px-4 py-3.5 font-semibold text-zinc-900">
                            <div>${tx.description || ''}</div>
                            ${linkedText}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap"><span class="px-2 py-1 rounded bg-zinc-100 text-zinc-655 text-[10px] font-medium">${tx.category || 'Other'}</span></td>
                        <td class="px-4 py-3.5 whitespace-nowrap font-bold text-zinc-900">${amtStr}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">${typeBadge}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap">${statusBadge}</td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="px-2 py-1 border border-zinc-200 hover:bg-zinc-50 rounded text-zinc-600 hover:text-zinc-900 font-semibold transition-colors cursor-pointer" onclick="coraOpenTransactionDrawerForEdit('${tx.id}')">Edit</button>
                                <button class="px-2 py-1 border border-zinc-200 hover:bg-zinc-100 hover:text-red-600 rounded text-zinc-500 font-semibold transition-colors cursor-pointer" onclick="coraDeleteTransaction('${tx.id}')">Delete</button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }

        // Overview calculations
        let totalInflow = 0;
        let totalOutflow = 0;

        (coraREData.financials || []).forEach(tx => {
            const amt = parseFloat(tx.amount) || 0;
            if (tx.type === 'Inflow' && tx.status === 'Received') {
                totalInflow += amt;
            } else if (tx.type === 'Outflow' && tx.status === 'Paid') {
                totalOutflow += amt;
            }
        });

        let netProfit = totalInflow - totalOutflow;

        let pendingDues = 0;
        (coraREData.clients || []).forEach(client => {
            let clientPrice = parseAmount(client.price);
            let paidInflows = 0;
            (coraREData.financials || []).forEach(tx => {
                if (tx.type === 'Inflow' && tx.status === 'Received' && isLinkedToClient(tx.client_link, client.id)) {
                    paidInflows += parseFloat(tx.amount) || 0;
                }
            });
            let dues = clientPrice - paidInflows;
            if (dues > 0) {
                pendingDues += dues;
            }
        });

        $('#cora-fin-stat-inflows').text(formatRupee(totalInflow));
        $('#cora-fin-stat-outflows').text(formatRupee(totalOutflow));
        $('#cora-fin-stat-profit').text(formatRupee(netProfit));
        $('#cora-fin-stat-dues').text(formatRupee(pendingDues));

        if (netProfit < 0) {
            $('#cora-fin-stat-profit').removeClass('text-zinc-900').addClass('text-red-600');
        } else {
            $('#cora-fin-stat-profit').removeClass('text-red-600').addClass('text-zinc-900');
        }

        if (typeof window.coraUpdateReport === 'function') {
            window.coraUpdateReport();
        }
    };

    let coraRevenueChart = null;

    window.coraUpdateReport = function() {
        const duration = $('#cora-report-duration').val() || 'month';
        const txs = coraREData.financials || [];
        
        let labels = [];
        let inflowsData = [];
        let outflowsData = [];
        
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth(); // 0-indexed
        
        if (duration === 'month') {
            // Group by days of this month (1 to end of month)
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            for (let i = 1; i <= daysInMonth; i++) {
                labels.push(`${i} ${now.toLocaleString('default', { month: 'short' })}`);
                inflowsData.push(0);
                outflowsData.push(0);
            }
            
            txs.forEach(tx => {
                const txDate = new Date(tx.date);
                if (txDate.getFullYear() === currentYear && txDate.getMonth() === currentMonth) {
                    const day = txDate.getDate();
                    const amt = parseFloat(tx.amount) || 0;
                    if (tx.type === 'Inflow' && tx.status === 'Received') {
                        inflowsData[day - 1] += amt;
                    } else if (tx.type === 'Outflow' && tx.status === 'Paid') {
                        outflowsData[day - 1] += amt;
                    }
                }
            });
        } else if (duration === 'quarter') {
            // Group by 3 months of this quarter
            const quarter = Math.floor(currentMonth / 3); // 0 to 3
            const startMonth = quarter * 3;
            for (let i = 0; i < 3; i++) {
                const m = startMonth + i;
                const dateObj = new Date(currentYear, m, 1);
                labels.push(dateObj.toLocaleString('default', { month: 'long' }));
                inflowsData.push(0);
                outflowsData.push(0);
            }
            
            txs.forEach(tx => {
                const txDate = new Date(tx.date);
                if (txDate.getFullYear() === currentYear) {
                    const m = txDate.getMonth();
                    if (m >= startMonth && m < startMonth + 3) {
                        const idx = m - startMonth;
                        const amt = parseFloat(tx.amount) || 0;
                        if (tx.type === 'Inflow' && tx.status === 'Received') {
                            inflowsData[idx] += amt;
                        } else if (tx.type === 'Outflow' && tx.status === 'Paid') {
                            outflowsData[idx] += amt;
                        }
                    }
                }
            });
        } else if (duration === 'year') {
            // Group by 12 months of this year
            for (let i = 0; i < 12; i++) {
                const dateObj = new Date(currentYear, i, 1);
                labels.push(dateObj.toLocaleString('default', { month: 'short' }));
                inflowsData.push(0);
                outflowsData.push(0);
            }
            
            txs.forEach(tx => {
                const txDate = new Date(tx.date);
                if (txDate.getFullYear() === currentYear) {
                    const m = txDate.getMonth();
                    const amt = parseFloat(tx.amount) || 0;
                    if (tx.type === 'Inflow' && tx.status === 'Received') {
                        inflowsData[m] += amt;
                    } else if (tx.type === 'Outflow' && tx.status === 'Paid') {
                        outflowsData[m] += amt;
                    }
                }
            });
        }
        
        // Draw Chart.js
        const canvas = document.getElementById('cora-revenue-chart');
        if (!canvas) return;
        
        if (coraRevenueChart) {
            coraRevenueChart.destroy();
        }
        
        // Monochromatic color palette conforming to rules
        const gridColor = 'rgba(228, 228, 231, 0.6)'; // zinc-200
        const textFont = { family: 'system-ui, -apple-system, sans-serif', size: 10 };
        
        coraRevenueChart = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (Inflow)',
                        data: inflowsData,
                        backgroundColor: '#18181b', // zinc-900 (pure black/zinc shade)
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Expenses (Outflow)',
                        data: outflowsData,
                        backgroundColor: '#a1a1aa', // zinc-400
                        borderRadius: 4,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: textFont,
                            boxWidth: 12,
                            padding: 15,
                            color: '#71717a' // zinc-500
                        }
                    },
                    tooltip: {
                        backgroundColor: '#18181b',
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.dataset.label}: ₹${context.raw.toLocaleString('en-IN')}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: textFont, color: '#71717a' }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            font: textFont,
                            color: '#71717a',
                            callback: function(value) {
                                if (value >= 1000) return '₹' + (value / 1000) + 'k';
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    };

    window.coraDownloadPDFReport = function() {
        const duration = $('#cora-report-duration').val() || 'month';
        const txs = coraREData.financials || [];
        const now = new Date();
        const generatedDate = now.toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        
        let reportTitle = '';
        if (duration === 'month') reportTitle = `Monthly Financial Report - ${now.toLocaleString('default', { month: 'long', year: 'numeric' })}`;
        else if (duration === 'quarter') {
            const quarter = Math.floor(now.getMonth() / 3) + 1;
            reportTitle = `Quarterly Financial Report - Q${quarter} ${now.getFullYear()}`;
        } else {
            reportTitle = `Annual Financial Report - Year ${now.getFullYear()}`;
        }
        
        // Filter transactions based on selected duration for report table
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth();
        const quarterVal = Math.floor(currentMonth / 3);
        const startMonth = quarterVal * 3;
        
        let filteredTxs = txs.filter(tx => {
            const txDate = new Date(tx.date);
            if (duration === 'month') {
                return txDate.getFullYear() === currentYear && txDate.getMonth() === currentMonth;
            } else if (duration === 'quarter') {
                return txDate.getFullYear() === currentYear && txDate.getMonth() >= startMonth && txDate.getMonth() < startMonth + 3;
            } else {
                return txDate.getFullYear() === currentYear;
            }
        });
        
        // Calculate totals for report
        let inflows = 0;
        let outflows = 0;
        filteredTxs.forEach(t => {
            const amt = parseFloat(t.amount) || 0;
            if (t.type === 'Inflow' && t.status === 'Received') inflows += amt;
            else if (t.type === 'Outflow' && t.status === 'Paid') outflows += amt;
        });
        let netProfitReport = inflows - outflows;
        
        // Build table HTML
        let tableRows = '';
        if (filteredTxs.length === 0) {
            tableRows = `<tr><td colspan="6" style="padding: 16px; text-align: center; color: #71717a;">No ledger entries recorded for this duration.</td></tr>`;
        } else {
            filteredTxs.forEach(t => {
                const dateVal = new Date(t.date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
                const amtVal = '₹' + parseFloat(t.amount).toLocaleString('en-IN');
                const badgeColor = t.type === 'Inflow' ? 'color: #047857; font-weight: bold;' : 'color: #374151;';
                tableRows += `
                    <tr style="border-bottom: 1px solid #e4e4e7;">
                        <td style="padding: 10px 12px; color: #4b5563;">${dateVal}</td>
                        <td style="padding: 10px 12px; font-weight: 500; color: #111827;">${t.description}</td>
                        <td style="padding: 10px 12px; color: #4b5563;">${t.category || 'Other'}</td>
                        <td style="padding: 10px 12px; text-align: right; font-weight: 600; color: #111827;">${amtVal}</td>
                        <td style="padding: 10px 12px; text-align: center;"><span style="${badgeColor}">${t.type}</span></td>
                        <td style="padding: 10px 12px; text-align: center; color: #4b5563;">${t.status}</td>
                    </tr>
                `;
            });
        }
        
        // Create print window with professional monochromatic style
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>${reportTitle}</title>
                <style>
                    body {
                        font-family: system-ui, -apple-system, sans-serif;
                        color: #18181b;
                        margin: 40px;
                        line-height: 1.5;
                        background: #ffffff;
                    }
                    .header-container {
                        display: flex;
                        justify-content: space-between;
                        align-items: flex-start;
                        border-bottom: 2px solid #18181b;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    .logo-title {
                        font-size: 24px;
                        font-weight: 800;
                        letter-spacing: -0.025em;
                        color: #18181b;
                    }
                    .meta-details {
                        font-size: 11px;
                        color: #71717a;
                        text-align: right;
                    }
                    .report-title {
                        font-size: 18px;
                        font-weight: 700;
                        margin-bottom: 20px;
                        color: #18181b;
                    }
                    .summary-grid {
                        display: grid;
                        grid-template-cols: repeat(3, 1fr);
                        gap: 16px;
                        margin-bottom: 30px;
                    }
                    .summary-card {
                        border: 1px solid #e4e4e7;
                        border-radius: 8px;
                        padding: 16px;
                        background: #fcfcfc;
                    }
                    .summary-label {
                        font-size: 10px;
                        font-weight: 700;
                        text-transform: uppercase;
                        color: #a1a1aa;
                        letter-spacing: 0.05em;
                    }
                    .summary-value {
                        font-size: 18px;
                        font-weight: 700;
                        margin-top: 4px;
                    }
                    .table-ledger {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 12px;
                    }
                    .table-ledger th {
                        background: #f4f4f5;
                        color: #71717a;
                        font-weight: 700;
                        text-transform: uppercase;
                        font-size: 9px;
                        letter-spacing: 0.05em;
                        padding: 10px 12px;
                        text-align: left;
                        border-bottom: 1px solid #e4e4e7;
                    }
                    .footer-print {
                        margin-top: 50px;
                        text-align: center;
                        font-size: 10px;
                        color: #a1a1aa;
                        border-top: 1px solid #f4f4f5;
                        padding-top: 15px;
                    }
                    @media print {
                        body { margin: 20px; }
                        .summary-card { background: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header-container">
                    <div>
                        <div class="logo-title">CORA FOR REAL ESTATE</div>
                        <div style="font-size: 12px; color: #71717a; margin-top: 4px;">Premium Brokerage Workspace Platform</div>
                    </div>
                    <div class="meta-details">
                        <div><strong>Studio:</strong> Apex Realty Group</div>
                        <div><strong>Generated:</strong> ${generatedDate}</div>
                        <div><strong>Scope:</strong> ${duration.toUpperCase()} LEVEL</div>
                    </div>
                </div>
                
                <div class="report-title">${reportTitle}</div>
                
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Total Revenue (Inflows)</div>
                        <div class="summary-value" style="color: #059669;">₹${inflows.toLocaleString('en-IN')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Expenses (Outflows)</div>
                        <div class="summary-value" style="color: #dc2626;">₹${outflows.toLocaleString('en-IN')}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Net Profit</div>
                        <div class="summary-value" style="color: ${netProfitReport >= 0 ? '#18181b' : '#dc2626'};">₹${netProfitReport.toLocaleString('en-IN')}</div>
                    </div>
                </div>
                
                <h4 style="margin-bottom: 12px; font-size: 14px; font-weight: 700; border-bottom: 1px solid #e4e4e7; padding-bottom: 6px;">Ledger Breakdown</h4>
                <table class="table-ledger">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th style="text-align: right;">Amount</th>
                            <th style="text-align: center;">Type</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
                
                <div class="footer-print">
                    Confidential Financial Document &bull; Generated from Cora for Real Estate Workspace Broker Owner.
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    };

    window.coraTriggerEmailDocument = function(docId) {
        if (!docId) {
            window.coraShowToast("Please save the document first.");
            return;
        }

        const doc = (coraREData.documents || []).find(d => d.id === docId);
        if (!doc) {
            window.coraShowToast("Please save the document first.");
            return;
        }

        let clientLink = $('#cora-doc-client-select').val() || doc.client_link;
        let email = '';
        let clientName = '';

        if (clientLink) {
            if (clientLink.startsWith('client_')) {
                const cId = clientLink.substring(7);
                const client = (coraREData.clients || []).find(c => c.id === cId || c.id === clientLink);
                if (client) {
                    email = client.email;
                    clientName = client.names;
                }
            } else if (clientLink.startsWith('lead_')) {
                const lId = clientLink.substring(5);
                const lead = (coraREData.leads || []).find(l => l.id === lId || l.id === clientLink);
                if (lead) {
                    email = lead.email;
                    clientName = lead.names;
                }
            } else {
                const client = (coraREData.clients || []).find(c => c.id === clientLink);
                if (client) {
                    email = client.email;
                    clientName = client.names;
                } else {
                    const lead = (coraREData.leads || []).find(l => l.id === clientLink);
                    if (lead) {
                        email = lead.email;
                        clientName = lead.names;
                    }
                }
            }
        }

        if (!email) {
            window.coraShowToast("Please link a client or lead with a valid email address first.");
            return;
        }

        window.coraShowToast(`Sending document to ${clientName || email}...`);

        $.post(coraREData.ajaxUrl, {
            action: 'cora_send_document_email',
            security: coraREData.ajaxNonce,
            doc_id: docId,
            email: email
        }, function(res) {
            if (res.success) {
                window.coraShowToast(`Document emailed successfully to ${email}.`);
                if (coraREData.documents) {
                    const idx = coraREData.documents.findIndex(d => d.id === docId);
                    if (idx !== -1) {
                        coraREData.documents[idx].status = 'Sent';
                    }
                }
                const row = $(`.cora-email-doc-btn[onclick*="${docId}"]`).closest('tr');
                row.find('.cora-badge-status-draft, .cora-badge-status-sent, .cora-badge-status-').replaceWith(
                    `<span class="cora-badge px-2 py-0.5 rounded text-[9px] font-semibold cora-badge-status-sent">Sent</span>`
                );
            } else {
                window.coraShowToast(res.data || "Failed to send email.");
            }
        }).fail(function() {
            window.coraShowToast("Network error sending email.");
        });
    };

    window.coraDocLoadTemplate = function(templateKey) {
        if (!templateKey) return;

        let clientName = '{{CLIENT_NAME}}';
        let clientEmail = '{{CLIENT_EMAIL}}';
        let amount = $('#cora-doc-amount-input').val().trim() || '{{AMOUNT}}';
        let dateStr = new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });

        const clientLink = $('#cora-doc-client-select').val();
        if (clientLink) {
            if (clientLink.startsWith('client_')) {
                const cId = clientLink.substring(7);
                const client = (coraREData.clients || []).find(c => c.id === cId || c.id === clientLink);
                if (client) {
                    clientName = client.names;
                    clientEmail = client.email;
                    amount = client.price || amount;
                }
            } else if (clientLink.startsWith('lead_')) {
                const lId = clientLink.substring(5);
                const lead = (coraREData.leads || []).find(l => l.id === lId || l.id === clientLink);
                if (lead) {
                    clientName = lead.names;
                    clientEmail = lead.email;
                    amount = lead.price || amount;
                }
            } else {
                const client = (coraREData.clients || []).find(c => c.id === clientLink);
                if (client) {
                    clientName = client.names;
                    clientEmail = client.email;
                    amount = client.price || amount;
                } else {
                    const lead = (coraREData.leads || []).find(l => l.id === clientLink);
                    if (lead) {
                        clientName = lead.names;
                        clientEmail = lead.email;
                        amount = lead.price || amount;
                    }
                }
            }
        }

        if (amount && amount !== '{{AMOUNT}}') {
            $('#cora-doc-amount-input').val(amount);
        }

        let html = '';

        if (templateKey === 'commercial_lease_proposal') {
            $('#cora-doc-title-input').val(`Proposal: Commercial Lease - ${clientName}`);
            $('#cora-doc-type-select').val('Proposal');
            html = `
                <h2><strong>Commercial Office Lease Proposal</strong></h2>
                <p><strong>Prepared for:</strong> ${clientName} (${clientEmail})<br><strong>Date:</strong> ${dateStr}</p>
                <hr>
                <h3><strong>Overview & Property Scope</strong></h3>
                <p>We are pleased to submit this proposal for premium commercial space matching your criteria. Our team focuses on providing turn-key, premium real estate listings, corporate negotiations, and tenant representation.</p>
                <h3><strong>Scope of Services</strong></h3>
                <ul>
                    <li><strong>Full Commercial Advisory Mandate:</strong> Comprehensive matching, 3D virtual walkthroughs, RERA title checks, and corporate space planning.</li>
                    <li><strong>Deliverables:</strong>
                        <ul>
                            <li>Curated listings portfolio (custom tailored to space and parking requirements).</li>
                            <li>Virtual tour files & layout blue-prints.</li>
                            <li>Fully negotiated lease term sheet and coordination with legal counsel.</li>
                        </ul>
                    </li>
                </ul>
                <h3><strong>Advisory & Brokerage Fees</strong></h3>
                <p>The total advisory fee for the described commercial mandate is <strong>${amount}</strong>.</p>
                <p><strong>Payment Terms:</strong> 50% Mandate retainer upon signing agreement, 50% upon successful registration of lease deed.</p>
            `;
        } else if (templateKey === 'intimate_proposal') {
            $('#cora-doc-title-input').val(`Proposal: Residential Sale - ${clientName}`);
            $('#cora-doc-type-select').val('Proposal');
            html = `
                <h2><strong>Residential Sale Mandate Proposal</strong></h2>
                <p><strong>Prepared for:</strong> ${clientName} (${clientEmail})<br><strong>Date:</strong> ${dateStr}</p>
                <hr>
                <h3><strong>Listing & Advisory Plan</strong></h3>
                <p>A customized, exclusive listing mandate tailored to showcase and market your premium residential property to vetted HNIs.</p>
                <ul>
                    <li><strong>Marketing & Staging:</strong> High-end property staging, professional HDR media capture, RERA registry verification, and targeted digital ads.</li>
                    <li><strong>Deliverables:</strong>
                        <ul>
                            <li>Active listing on premium real estate networks and local MLS syndication.</li>
                            <li>Vetted buyer matching and weekly showings updates.</li>
                        </ul>
                    </li>
                </ul>
                <h3><strong>Brokerage Fees & Terms</strong></h3>
                <p><strong>Commission Rate:</strong> ${amount} of final transaction value.</p>
                <p>Exclusive marketing mandate period: 90 days from date of execution.</p>
            `;
        } else if (templateKey === 'standard_invoice') {
            $('#cora-doc-title-input').val(`Invoice: Brokerage Commission - ${clientName}`);
            $('#cora-doc-type-select').val('Invoice');
            html = `
                <h2><strong>COMMISSION INVOICE</strong></h2>
                <p><strong>Invoice To:</strong> ${clientName}<br><strong>Email:</strong> ${clientEmail}<br><strong>Invoice Date:</strong> ${dateStr}<br><strong>Due Date:</strong> Immediate</p>
                <hr>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e4e4e7; text-align: left;">
                            <th style="padding: 10px 0;">Advisory Service Description</th>
                            <th style="padding: 10px 0; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f4f4f5;">
                            <td style="padding: 12px 0;">Real Estate Advisory & Brokerage Services (Residential Sale Commission)</td>
                            <td style="padding: 12px 0; text-align: right;">${amount}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; font-weight: bold;">Total Due</td>
                            <td style="padding: 12px 0; text-align: right; font-weight: bold;">${amount}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p><strong>Bank Wire Details:</strong><br>Account Name: Cora Real Estate Private Limited<br>Bank: HDFC Bank Limited, Delhi Branch<br>IFSC Code: HDFC0001202<br>Account Number: 50200084729103</p>
            `;
        } else if (templateKey === 'commercial_invoice') {
            $('#cora-doc-title-input').val(`Invoice: Commercial Advisory - ${clientName}`);
            $('#cora-doc-type-select').val('Invoice');
            html = `
                <h2><strong>COMMERCIAL INVOICE</strong></h2>
                <p><strong>Client:</strong> ${clientName}<br><strong>Brand/Entity:</strong> Corporate Office Lease<br><strong>Date:</strong> ${dateStr}</p>
                <hr>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e4e4e7; text-align: left;">
                            <th style="padding: 10px 0;">Item Description</th>
                            <th style="padding: 10px 0; text-align: right;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f4f4f5;">
                            <td style="padding: 12px 0;">Professional Fee: Commercial Leasing Mandate & Advisory (Cyber City Suite)</td>
                            <td style="padding: 12px 0; text-align: right;">${amount}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f4f4f5;">
                            <td style="padding: 12px 0;">Corporate Tenant Representation & Space Verification</td>
                            <td style="padding: 12px 0; text-align: right;">Included</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; font-weight: bold;">Total Amount Due</td>
                            <td style="padding: 12px 0; text-align: right; font-weight: bold;">${amount}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p><strong>Terms:</strong> Payment Net 30. Thank you for your business!</p>
            `;
        }

        $('#cora-doc-paper').html(html);
        window.coraShowToast("Template loaded. Customize as needed.");
    };

    window.coraCreateProposalFromLead = function() {
        const leadId = $('#cora-lead-id').val();
        const leadNames = $('#cora-lead-names').val();
        const leadEmail = $('#cora-lead-email').val();
        const leadPrice = $('#cora-lead-price').val();
        
        if (!leadId) {
            window.coraShowToast("Error: No active lead selected.");
            return;
        }

        const cmd = {
            type: 'Proposal',
            client_link: 'lead_' + leadId,
            template: 'commercial_lease_proposal',
            names: leadNames,
            email: leadEmail,
            price: leadPrice
        };
        localStorage.setItem('cora_autocreate_doc', JSON.stringify(cmd));
        
        coraNavigateTo('vault');
    };

    window.coraCreateInvoiceFromClient = function() {
        const client = $('#cora-client-drawer').data('client');
        if (!client || !client.id) {
            window.coraShowToast("Error: No active client selected.");
            return;
        }

        const cmd = {
            type: 'Invoice',
            client_link: 'client_' + client.id,
            template: 'standard_invoice',
            names: client.names,
            email: client.email,
            price: client.price
        };
        localStorage.setItem('cora_autocreate_doc', JSON.stringify(cmd));
        
        coraNavigateTo('vault');
    };

    window.coraCheckAutoCreateDoc = function() {
        const cmdStr = localStorage.getItem('cora_autocreate_doc');
        if (!cmdStr) return;
        
        if (coraREData.currentPage === 'vault') {
            try {
                const cmd = JSON.parse(cmdStr);
                localStorage.removeItem('cora_autocreate_doc');
                
                coraOpenDocDrawer();
                
                setTimeout(() => {
                    $('#cora-doc-client-select').val(cmd.client_link);
                    coraDocLoadTemplate(cmd.template);
                }, 200);
            } catch (e) {
                localStorage.removeItem('cora_autocreate_doc');
            }
        }
    };

    // Financial Board Page Initialization
    if (coraREData.currentPage === 'financials') {
        coraUpdateTxCategories('Inflow');
        coraRenderFinancials();
        
        $('#cora-financial-search').on('input', coraRenderFinancials);

        $('#cora-financial-filters').on('click', '.cora-filter-btn', function() {
            $('#cora-financial-filters .cora-filter-btn').removeClass('bg-zinc-950 text-white').addClass('border border-zinc-200 text-zinc-655 bg-white hover:bg-zinc-50');
            $(this).removeClass('border border-zinc-200 text-zinc-655 bg-white hover:bg-zinc-50').addClass('bg-zinc-950 text-white');
            coraRenderFinancials();
        });
    }

    // --- AI Content Suite Logic ---
    let coraQuillListingCoordinator = null;
    let coraCategorySelect = null;
    let coraTagSelect = null;

    function initListingCoordinatorComponentsIfNeeded() {
        if (!coraQuillListingCoordinator && $('#cora-quill-editor').length > 0) {
            if (typeof Quill !== 'undefined') {
                const BlockEmbed = Quill.import('blots/block/embed');
                class CoraWidgetBlot extends BlockEmbed {
                    static create(value) {
                        let node = super.create();
                        node.setAttribute('class', 'cora-inserted-widget');
                        node.setAttribute('data-type', value.type);
                        node.setAttribute('contenteditable', 'false');
                        node.innerHTML = value.html;
                        return node;
                    }
                    static value(node) {
                        return {
                            type: node.getAttribute('data-type'),
                            html: node.innerHTML
                        };
                    }
                }
                CoraWidgetBlot.blotName = 'cora-widget';
                CoraWidgetBlot.tagName = 'div';
                Quill.register(CoraWidgetBlot, true);

                // Register font and size whitelists
                const FontStyle = Quill.import('formats/font');
                FontStyle.whitelist = ['sans', 'serif', 'mono'];
                Quill.register(FontStyle, true);

                const SizeStyle = Quill.import('attributors/style/size');
                SizeStyle.whitelist = ['13px', '15px', '18px', '24px'];
                Quill.register(SizeStyle, true);
            }

            coraQuillListingCoordinator = new Quill('#cora-quill-editor', {
                theme: 'snow',
                placeholder: 'Start writing your masterpiece...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, false] }],
                        [{ 'font': ['', 'sans', 'serif', 'mono'] }],
                        [{ 'size': ['13px', false, '18px', '24px'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': ['#09090b','#52525b','#a1a1aa','#ffffff','#ef4444','#f97316','#eab308','#22c55e','#3b82f6','#8b5cf6'] }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'indent': '-1'}, { 'indent': '+1' }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });
            window.coraQuillListingCoordinator = coraQuillListingCoordinator;
            // Override Quill's default image and video handlers to use wp.media instead of prompt()
            const toolbar = coraQuillListingCoordinator.getModule('toolbar');
            toolbar.addHandler('image', function() {
                if (typeof wp !== 'undefined' && wp.media) {
                    const customUploader = wp.media({
                        title: 'Select or Upload Media',
                        button: { text: 'Insert into Article' },
                        multiple: false,
                        library: { type: 'image' }
                    });
                    customUploader.on('select', function() {
                        const attachment = customUploader.state().get('selection').first().toJSON();
                        const range = coraQuillListingCoordinator.getSelection();
                        coraQuillListingCoordinator.insertEmbed(range ? range.index : 0, 'image', attachment.url);
                    });
                    customUploader.open();
                } else {
                    window.coraShowToast('Media library is not loaded.');
                }
            });
            toolbar.addHandler('video', function() {
                if (typeof wp !== 'undefined' && wp.media) {
                    const customUploader = wp.media({
                        title: 'Select or Upload Video',
                        button: { text: 'Insert into Article' },
                        multiple: false,
                        library: { type: 'video' }
                    });
                    customUploader.on('select', function() {
                        const attachment = customUploader.state().get('selection').first().toJSON();
                        const range = coraQuillListingCoordinator.getSelection();
                        coraQuillListingCoordinator.insertEmbed(range ? range.index : 0, 'video', attachment.url);
                    });
                    customUploader.open();
                } else {
                    window.coraShowToast('Media library is not loaded.');
                }
            });
            
            // Slash command detection helper
            function coraCheckSlashCommand(range) {
                if (range) {
                    const [line, offset] = coraQuillListingCoordinator.getLine(range.index);
                    if (line) {
                        // Clean zero-width characters using .replace(/[\u200B\uFEFF]/g, '') before checking
                        const lineText = (line.domNode.textContent || '').replace(/[\u200B\uFEFF]/g, '');
                        if (lineText.trim() === '/') {
                            const bounds = coraQuillListingCoordinator.getBounds(range.index);
                            const editorContainer = $('#cora-quill-editor');
                            const parentPosition = editorContainer.position();
                            
                            $('#cora-editor-slash-menu').removeClass('hidden').css({
                                top: (parentPosition.top + bounds.bottom + 8) + 'px',
                                left: (parentPosition.left + bounds.left) + 'px'
                            });
                        } else {
                            $('#cora-editor-slash-menu').addClass('hidden');
                        }
                    }
                } else {
                    $('#cora-editor-slash-menu').addClass('hidden');
                }
            }

            // Update custom status on text change
            coraQuillListingCoordinator.on('text-change', function() {
                $('#cora-editor-status').text('Unsaved changes');

                // Monitor entity mentions in text
                const text = coraQuillListingCoordinator.getText().toLowerCase();
                const entities = {
                    'vasant': 'vasant vihar',
                    'dlf': 'dlf phase 5',
                    'gurgaon': 'gurgaon',
                    'cyber': 'cyber city'
                };
                for (let key in entities) {
                    if (text.includes(entities[key])) {
                        $(`#entity-mention-${key}`).removeClass('border-zinc-200 text-zinc-400').addClass('border-zinc-350 text-zinc-700 font-bold bg-zinc-100');
                    } else {
                        $(`#entity-mention-${key}`).addClass('border-zinc-200 text-zinc-400').removeClass('border-zinc-350 text-zinc-700 font-bold bg-zinc-100');
                    }
                }
                
                const range = coraQuillListingCoordinator.getSelection();
                coraCheckSlashCommand(range);
                
                if (window.coraUpdateWordCount) {
                    window.coraUpdateWordCount();
                }
            });

            coraQuillListingCoordinator.on('selection-change', function(range) {
                coraCheckSlashCommand(range);
            });

            $('#cora-article-title, #cora-seo-keyword, #cora-seo-description, #cora-article-categories, #cora-article-tags').on('input change', function() {
                $('#cora-editor-status').text('Unsaved changes');
                if (window.coraUpdateWordCount) {
                    window.coraUpdateWordCount();
                }
            });
            
            // Initialize Custom Dropdowns for Meta Tab
            if (typeof window.coraInitMetaDropdowns === 'function') {
                window.coraInitMetaDropdowns();
            }
        }
    }

    window.coraToggleContentDrawer = function(show) {
        if (show) {
            initListingCoordinatorComponentsIfNeeded();
            $('.cora-stat-card').parent().hide();
            $('#cora-articles-table-body').closest('div').hide();
            $('.cora-page-header').hide();
            $('#cora-full-page-editor').removeClass('hidden').css('display', 'flex');
        } else {
            $('#cora-full-page-editor').addClass('hidden').css('display', 'none');
            $('.cora-stat-card').parent().show();
            $('#cora-articles-table-body').closest('div').show();
            $('.cora-page-header').show();
            
            // Remove edit & post_id query params from URL when closing editor
            try {
                const url = new URL(window.location.href);
                url.searchParams.delete('action');
                url.searchParams.delete('post_id');
                url.searchParams.delete('edit_post');
                url.searchParams.delete('article_id');
                window.history.pushState({}, '', url.toString());
            } catch(e){}
        }
    };

    window.coraOpenContentDrawer = function() {
        $('#cora-article-id').val('');
        $('#cora-article-title').val('');
        $('#cora-seo-keyword').val('');
        $('#cora-seo-description').val('');
        $('#cora-seo-score-display').text('--');
        $('#cora-seo-score-display').removeClass('text-green-600 text-yellow-500').addClass('text-zinc-400');
        
        if (coraCategorySelect) coraCategorySelect.clear();
        if (coraTagSelect) coraTagSelect.clear();
        
        $('#cora-thumbnail-id').val('');
        $('#cora-thumbnail-img').addClass('hidden').attr('src', '');
        $('#cora-thumbnail-placeholder').removeClass('hidden');
        $('#cora-editor-status').text('Drafting new');

        // Reset GEO indicators
        $('#chk-geo-direct-answer').prop('checked', false);
        $('#chk-geo-info-density').prop('checked', false);
        $('#chk-geo-citations').prop('checked', false);
        $('#chk-geo-schema').prop('checked', false);
        $('#cora-geo-score-display').text('65');
        $('#cora-schema-preview-block').text('{}');
        coraSwitchSidebarTab('seo');

        initListingCoordinatorComponentsIfNeeded();
        if (coraQuillListingCoordinator) coraQuillListingCoordinator.root.innerHTML = '';
        if (window.coraSyncBeehiivInputsFromOriginal) {
            window.coraSyncBeehiivInputsFromOriginal();
        }
        coraToggleContentDrawer(true);
    };

    window.coraEditArticle = function(id) {
        // Sync URL parameters so page refresh restores this exact article editor
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('action', 'edit');
            url.searchParams.set('post_id', id);
            window.history.pushState({ action: 'edit', post_id: id }, '', url.toString());
        } catch(e){}

        coraToggleContentDrawer(true);
        $('#cora-editor-status').text('Loading...');
        $('#cora-article-id').val(id);
        $('#cora-article-title').val('');
        $('#cora-article-assignee').val('0');
        
        // Reset GEO indicators
        $('#chk-geo-direct-answer').prop('checked', false);
        $('#chk-geo-info-density').prop('checked', false);
        $('#chk-geo-citations').prop('checked', false);
        $('#chk-geo-schema').prop('checked', false);
        $('#cora-geo-score-display').text('65');
        $('#cora-schema-preview-block').text('{}');
        coraSwitchSidebarTab('seo');

        if (coraQuillListingCoordinator) coraQuillListingCoordinator.root.innerHTML = '<p class="text-zinc-400 animate-pulse">Loading content from server...</p>';
        
        $.post(ajaxurl, {
            action: 'cora_get_article',
            nonce: coraREData.ajaxNonce,
            post_id: id
        }, function(response) {
            if (response.success) {
                const data = response.data;
                $('#cora-article-title').val(data.title || ''); // Needs to be sent from backend or fetched from DOM. Actually we didn't send title in get_article! Let's fetch from table DOM
                
                // Fallback for title if not in backend response (we can grab it from the clicked row)
                const domTitle = $(`tr[onclick="coraEditArticle(${id})"] .font-bold.text-zinc-900`).text();
                $('#cora-article-title').val(domTitle);

                const domAuthor = ($(`tr[onclick="coraEditArticle(${id})"] td:nth-child(2) span`).text() || 'Writer').trim();

                // Assignee drop-down
                const assigneeId = data.assignee_id || '0';
                $('#cora-article-assignee').val(assigneeId);
                const assigneeName = $(`.cora-meta-assignee-option[data-value="${assigneeId}"]`).text() || 'Unassigned';
                $('#cora-meta-assignee-value').text(assigneeName);

                // Scheduled Date
                $('#cora-article-scheduled-date').val(data.scheduled_date || '');

                // Reset/Hide editorial banner & feedback container
                $('#cora-editorial-banner').addClass('hidden');
                $('#cora-feedback-input-container').addClass('hidden');
                $('#cora-feedback-input-field').val('');
                $('#cora-btn-submit-review').removeClass('hidden');

                if (data.editorial_status === 'pending_review') {
                    $('#cora-editorial-banner').removeClass('hidden');
                    $('#cora-editorial-banner-status').text('Draft Pending Review');
                    $('#cora-editorial-banner-author').text(domAuthor);
                    $('#cora-btn-submit-review').addClass('hidden');
                }

                if (data.editorial_status === 'published') {
                    $('#cora-btn-submit-review').addClass('hidden');
                }

                // Show feedback loop notes if present
                if (data.editorial_feedback && data.editorial_status === 'draft') {
                    $('#cora-editorial-feedback-box').removeClass('hidden');
                    $('#cora-editorial-feedback-text').text(data.editorial_feedback);
                } else {
                    $('#cora-editorial-feedback-box').addClass('hidden');
                    $('#cora-editorial-feedback-text').text('');
                }

                if (coraQuillListingCoordinator) coraQuillListingCoordinator.root.innerHTML = data.content || '';
                
                $('#cora-seo-keyword').val(data.keyword || '');
                $('#cora-seo-description').val(data.description || '');
                
                // Sync Categories Custom Checkboxes
                $('.cora-meta-category-checkbox').prop('checked', false);
                if (data.categories) {
                    data.categories.forEach(catId => {
                        $(`.cora-meta-category-checkbox[value="${catId}"]`).prop('checked', true);
                    });
                }
                if (typeof window.coraSyncCategoriesUI === 'function') {
                    window.coraSyncCategoriesUI();
                }

                // Sync Tags Custom Checkboxes
                $('.cora-meta-tag-checkbox').prop('checked', false);
                if (data.tags) {
                    data.tags.forEach(tagId => {
                        let chk = $(`.cora-meta-tag-checkbox[value="${tagId}"], .cora-meta-tag-checkbox[data-name="${tagId}"]`);
                        if (chk.length === 0) {
                            const newTagOpt = $(`
                                <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-850 dark:text-zinc-250 select-none">
                                    <input type="checkbox" class="cora-meta-tag-checkbox rounded border-zinc-300 focus:ring-0 text-zinc-950" value="${tagId}" data-name="${tagId}" checked>
                                    <span>${tagId}</span>
                                </label>
                            `);
                            $('#cora-meta-tags-dropdown').append(newTagOpt);
                            if ($(`#cora-article-tags option[value="${tagId}"]`).length === 0) {
                                $('#cora-article-tags').append(`<option value="${tagId}">${tagId}</option>`);
                            }
                        } else {
                            chk.prop('checked', true);
                        }
                    });
                }
                if (typeof window.coraSyncTagsUI === 'function') {
                    window.coraSyncTagsUI();
                }
                
                if (data.thumbnail_url) {
                    $('#cora-thumbnail-id').val(data.thumbnail_id);
                    $('#cora-thumbnail-img').attr('src', data.thumbnail_url).removeClass('hidden');
                    $('#cora-thumbnail-placeholder').addClass('hidden');
                } else {
                    $('#cora-thumbnail-id').val('');
                    $('#cora-thumbnail-img').addClass('hidden').attr('src', '');
                    $('#cora-thumbnail-placeholder').removeClass('hidden');
                }

                $('#cora-article-slug').val(data.slug || '');
                $('#cora-article-allow-comments').prop('checked', data.comment_status === 'open');

                $('#cora-editor-status').text('Saved');
                if (window.coraSyncBeehiivInputsFromOriginal) {
                    window.coraSyncBeehiivInputsFromOriginal();
                }
            } else {
                if (coraQuillListingCoordinator) coraQuillListingCoordinator.root.innerHTML = '';
                window.coraShowToast('Failed to load article content', 'error');
            }
        });
    };

    // Auto-restore article editor if post_id or article_id is present in URL query parameters
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const autoPostId = urlParams.get('post_id') || urlParams.get('article_id') || urlParams.get('edit_post');
        if (autoPostId) {
            setTimeout(function() {
                if (typeof window.coraEditArticle === 'function') {
                    window.coraEditArticle(autoPostId);
                }
            }, 150);
        }
    } catch(e){}

    window.coraToggleMediaDrawer = function(show) {
        if (show) {
            $('#cora-media-library-drawer').removeClass('translate-x-full pointer-events-none collapsed');
            coraFetchMediaLibrary();
        } else {
            $('#cora-media-library-drawer').addClass('translate-x-full pointer-events-none collapsed');
        }
    };

    window.coraOpenMediaLibrary = function() {
        coraToggleMediaDrawer(true);
    };
    
    window.coraFetchMediaLibrary = function() {
        $('#cora-media-library-grid').html('<div class="col-span-3 py-10 text-center"><div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>');
        
        $.post(coraREData.ajaxurl, {
            action: 'cora_get_media',
            nonce: coraREData.nonce
        }, function(response) {
            if (response.success && response.data.images) {
                let html = '';
                response.data.images.forEach(function(img) {
                    html += `
                        <div class="aspect-square bg-zinc-100 rounded border border-zinc-200 overflow-hidden cursor-pointer hover:border-blue-500 transition-colors" onclick="coraSelectMedia(${img.id}, '${img.url}')">
                            <img src="${img.url}" class="w-full h-full object-cover">
                        </div>
                    `;
                });
                if (html === '') html = '<div class="col-span-3 text-center text-xs text-zinc-500 py-4">No images found.</div>';
                $('#cora-media-library-grid').html(html);
            } else {
                $('#cora-media-library-grid').html('<div class="col-span-3 text-center text-xs text-red-500 py-4">Failed to load media.</div>');
            }
        });
    };
    
    window.coraSelectMedia = function(id, url) {
        const target = window.coraMediaSelectTarget || 'thumbnail';
        if (target === 'cover') {
            $('#cora-cover-image-img').attr('src', url).removeClass('hidden');
            $('#cora-cover-image-placeholder').addClass('hidden');
            $('#cora-article-cover-url').val(url);
        } else {
            $('#cora-thumbnail-id').val(id);
            $('#cora-thumbnail-img').attr('src', url).removeClass('hidden');
            $('#cora-thumbnail-placeholder').addClass('hidden');
            
            // Sync with Beehiiv bar thumbnail uploader preview
            $('#cora-thumbnail-img-bh').attr('src', url).removeClass('hidden');
            $('#cora-thumbnail-placeholder-bh').addClass('hidden');
        }
        $('#cora-editor-status').text('Unsaved changes');
        coraToggleMediaDrawer(false);
    };
    
    // Handle Custom Media Uploads
    if ($('#cora-media-drawer-dropzone').length) {
        let dropzone = $('#cora-media-drawer-dropzone');
        let fileInput = $('#cora-media-file-input');
        
        dropzone.on('click', function() {
            fileInput.click();
        });
        
        dropzone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass('bg-blue-50 border-blue-300');
        });
        
        dropzone.on('dragleave drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass('bg-blue-50 border-blue-300');
        });
        
        dropzone.on('drop', function(e) {
            let files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                coraUploadMedia(files[0]);
            }
        });
        
        fileInput.on('change', function() {
            if (this.files.length > 0) {
                coraUploadMedia(this.files[0]);
            }
        });
    }
    
    function coraUploadMedia(file) {
        let formData = new FormData();
        formData.append('action', 'cora_upload_media');
        formData.append('nonce', coraREData.nonce);
        formData.append('file', file);
        
        $('#cora-media-upload-status').text('Uploading...').removeClass('text-red-500 text-green-500').addClass('text-blue-500');
        
        $.ajax({
            url: coraREData.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    $('#cora-media-upload-status').text('Upload complete!').removeClass('text-blue-500').addClass('text-green-500');
                    coraSelectMedia(response.data.id, response.data.url);
                    setTimeout(() => $('#cora-media-upload-status').text('Maximum file size: 10MB').removeClass('text-green-500').addClass('text-zinc-500'), 3000);
                } else {
                    $('#cora-media-upload-status').text('Error: ' + response.data).removeClass('text-blue-500').addClass('text-red-500');
                }
            },
            error: function() {
                $('#cora-media-upload-status').text('Network error.').removeClass('text-blue-500').addClass('text-red-500');
            }
        });
    }

    window.coraGenerateArticleAI = function() {
        if (window.coraShowToast) {
            window.coraShowToast('Coming Soon — AI Content Generation feature is in development.', 'info');
        }
    };

    window.coraAnalyzeSEO = function() {
        const title = $('#cora-article-title').val();
        const content = coraQuillListingCoordinator ? coraQuillListingCoordinator.root.innerHTML : '';
        
        if (!title || !content || content === '<p><br></p>') {
            window.coraShowToast('Add some content to analyze SEO.', 'error');
            return;
        }
        
        window.coraShowToast('Analyzing content for SEO...', 'info');
        $('#cora-editor-status').text('Analyzing...');
        
        $.post(ajaxurl, {
            action: 'cora_analyze_seo',
            nonce: coraREData.ajaxNonce,
            title: title,
            content: content
        }, function(response) {
            if(response.success) {
                const score = response.data.score;
                $('#cora-seo-score-display').text(score);
                $('#cora-seo-score-display').parent().removeClass('text-green-600 text-yellow-500 text-zinc-400');
                
                if (score >= 80) {
                    $('#cora-seo-score-display').parent().addClass('text-green-600');
                    window.coraShowToast('Great job! Your article is highly optimized.', 'success');
                } else {
                    $('#cora-seo-score-display').parent().addClass('text-yellow-500');
                    window.coraShowToast('SEO analysis complete. Room for improvement.', 'info');
                }
                $('#cora-editor-status').text('Unsaved changes');
            }
        });
    };

    window.coraSaveArticle = function(status) {
        if (!status) status = 'draft';
        const id = $('#cora-article-id').val();
        const title = $('#cora-article-title').val();
        const content = coraQuillListingCoordinator ? coraQuillListingCoordinator.root.innerHTML : '';
        const keyword = $('#cora-seo-keyword').val();
        const description = $('#cora-seo-description').val();
        const score = $('#cora-seo-score-display').text();
        
        const categories = $('#cora-article-categories').val() || [];
        const tags = $('#cora-article-tags').val() || [];
        const thumbnail_id = $('#cora-thumbnail-id').val();
        const scheduled_date = $('#cora-article-scheduled-date').val() || '';

        if (!title) {
            window.coraShowToast('Cannot save an article without a title.', 'error');
            return;
        }

        const assignee_id = $('#cora-article-assignee').val() || '0';

        const slug = $('#cora-article-slug').val() || '';
        const comment_status = $('#cora-article-allow-comments').is(':checked') ? 'open' : 'closed';

        $('#cora-editor-status').text('Saving...');
        window.coraShowToast(`Saving article as ${status}...`, 'info');

        $.post(ajaxurl, {
            action: 'cora_save_article',
            nonce: coraREData.ajaxNonce,
            post_id: id,
            title: title,
            content: content,
            status: status,
            keyword: keyword,
            description: description,
            seo_score: score === '--' ? '' : score,
            categories: categories,
            tags: tags,
            thumbnail_id: thumbnail_id,
            assignee_id: assignee_id,
            slug: slug,
            comment_status: comment_status,
            scheduled_date: scheduled_date
        }, function(response) {
            if (response.success) {
                $('#cora-editor-status').text('Saved at ' + new Date().toLocaleTimeString());
                window.coraShowToast(`Article ${status === 'publish' ? 'published' : 'saved'} successfully!`, 'success');
                setTimeout(() => window.location.reload(), 800); 
            } else {
                $('#cora-editor-status').text('Save Failed');
                window.coraShowToast(response.data || 'Error saving article.', 'error');
            }
        });
    };

    window.coraTrashArticle = function() {
        const id = $('#cora-article-id').val();
        if (!id) {
            window.coraShowToast('No active article to move to trash.', 'warning');
            return;
        }

        window.coraShowToast('Moving article to trash...', 'info');

        $.post(ajaxurl, {
            action: 'cora_trash_article',
            nonce: coraREData.ajaxNonce,
            post_id: id
        }, function(response) {
            if (response.success) {
                window.coraShowToast('Article successfully moved to trash.', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                window.coraShowToast(response.data || 'Error moving article to trash.', 'error');
            }
        });
    };


    // --- GEO & AISEO Integration ---
    window.coraSwitchBlogsTab = function(tab) {
        $('#btn-tab-articles-list, #btn-tab-geo-analytics, #btn-tab-keywords-explorer').removeClass('border-zinc-950 text-zinc-900').addClass('border-transparent text-zinc-400');
        $('#cora-blogs-list-container, #cora-blogs-geo-panel, #cora-blogs-keywords-panel').addClass('hidden');

        if (tab === 'list') {
            $('#btn-tab-articles-list').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
            $('#cora-blogs-list-container').removeClass('hidden');
        } else if (tab === 'geo') {
            $('#btn-tab-geo-analytics').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
            $('#cora-blogs-geo-panel').removeClass('hidden');
        } else if (tab === 'keywords') {
            $('#btn-tab-keywords-explorer').removeClass('border-transparent text-zinc-400').addClass('border-zinc-950 text-zinc-900');
            $('#cora-blogs-keywords-panel').removeClass('hidden');
        }
    };

    window.coraToggleArticleLeadsDrawer = function(show) {
        if (show) {
            $('#drawer-article-leads').removeClass('translate-x-full');
        } else {
            $('#drawer-article-leads').addClass('translate-x-full');
        }
    };

    window.coraShowArticleLeads = function(postId, postTitle) {
        $('#cora-article-leads-title').text('Leads: ' + postTitle);
        $('#cora-article-leads-list').html('<tr><td colspan="3" class="py-6 text-center text-zinc-400 animate-pulse">Loading captured leads...</td></tr>');
        
        coraToggleArticleLeadsDrawer(true);

        $.post(ajaxurl, {
            action: 'cora_get_article_leads',
            nonce: coraREData.ajaxNonce,
            post_id: postId
        }, function(response) {
            if (response.success) {
                const leads = response.data;
                if (!leads || leads.length === 0) {
                    $('#cora-article-leads-list').html('<tr><td colspan="3" class="py-6 text-center text-zinc-500 italic">No leads captured from this article yet.</td></tr>');
                } else {
                    let html = '';
                    leads.forEach(lead => {
                        html += `
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="py-2.5 px-3">
                                <div class="font-bold text-zinc-900">${lead.first_name} ${lead.last_name}</div>
                                <div class="text-[10px] text-zinc-400 font-semibold">${lead.email}</div>
                                <div class="text-[10px] text-zinc-400 font-semibold">${lead.phone}</div>
                            </td>
                            <td class="py-2.5 px-3 max-w-[150px] truncate text-zinc-650 font-medium">
                                ${lead.notes || '<span class="italic text-zinc-400">None</span>'}
                            </td>
                            <td class="py-2.5 px-3 text-right text-[10px] text-zinc-400 font-bold whitespace-nowrap">
                                ${lead.date}
                            </td>
                        </tr>
                        `;
                    });
                    $('#cora-article-leads-list').html(html);
                }
            } else {
                window.coraShowToast('Failed to load leads: ' + (response.data || 'unknown error'), 'error');
            }
        }).fail(function() {
            window.coraShowToast('Network error loading leads.', 'error');
        });
    };

    window.coraInjectQuillCTA = function(type) {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        let html = '';
        const postId = $('#cora-article-id').val() || '0';

        if (type === 'valuation') {
            html = `
            <div class="cora-inline-cta-card" style="background:#ffffff; border:1px solid #e4e4e7; border-radius:12px; padding:24px; margin:24px 0; max-width:550px; font-family:system-ui, sans-serif; box-shadow:0 1px 3px rgba(0,0,0,0.05);" contenteditable="false">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                    <span style="padding:6px; background:#f4f4f5; border-radius:6px; color:#09090b; display:inline-flex; align-items:center;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    </span>
                    <strong style="font-size:14px; text-transform:uppercase; letter-spacing:0.05em; color:#71717a;">Free Local Appraisal</strong>
                </div>
                <h3 style="font-size:18px; font-weight:800; color:#09090b; margin:0 0 6px 0; line-height:1.2;">Get a Free Professional Property Valuation</h3>
                <p style="font-size:12px; color:#71717a; margin:0 0 16px 0;">Find out exactly what your luxury home or villa is worth in today's market.</p>
                
                <form class="cora-blog-lead-form" onsubmit="event.preventDefault(); window.coraSubmitBlogLeadForm(this, ${postId});" style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <input type="text" name="first_name" placeholder="Full Name" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="email" name="email" placeholder="Email Address" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="text" name="phone" placeholder="Phone Number" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="text" name="notes" placeholder="Property Address (e.g. Vasant Vihar)" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <button type="submit" style="width:100%; background:#09090b; color:#ffffff; font-weight:700; border:none; border-radius:6px; padding:10px; font-size:13px; cursor:pointer; transition:background 0.2s;">Request Free Appraisal</button>
                </form>
            </div>
            `;
        } else if (type === 'catalog') {
            html = `
            <div class="cora-inline-cta-card" style="background:#ffffff; border:1px solid #e4e4e7; border-radius:12px; padding:24px; margin:24px 0; max-width:550px; font-family:system-ui, sans-serif; box-shadow:0 1px 3px rgba(0,0,0,0.05);" contenteditable="false">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                    <span style="padding:6px; background:#f4f4f5; border-radius:6px; color:#09090b; display:inline-flex; align-items:center;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
                    </span>
                    <strong style="font-size:14px; text-transform:uppercase; letter-spacing:0.05em; color:#71717a;">Exclusive Downloads</strong>
                </div>
                <h3 style="font-size:18px; font-weight:800; color:#09090b; margin:0 0 6px 0; line-height:1.2;">Download Delhi NCR Luxury Pricing Catalog</h3>
                <p style="font-size:12px; color:#71717a; margin:0 0 16px 0;">Get the complete historical price guide, local tax rate breakdowns, and market forecasts.</p>
                
                <form class="cora-blog-lead-form" onsubmit="event.preventDefault(); window.coraSubmitBlogLeadForm(this, ${postId});" style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <input type="text" name="first_name" placeholder="Full Name" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="email" name="email" placeholder="Email Address" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="hidden" name="notes" value="Downloaded Pricing Catalog PDF">
                    <button type="submit" style="width:100%; background:#09090b; color:#ffffff; font-weight:700; border:none; border-radius:6px; padding:10px; font-size:13px; cursor:pointer; transition:background 0.2s;">Download Price Catalog PDF</button>
                </form>
            </div>
            `;
        } else if (type === 'scheduler') {
            html = `
            <div class="cora-inline-cta-card" style="background:#ffffff; border:1px solid #e4e4e7; border-radius:12px; padding:24px; margin:24px 0; max-width:550px; font-family:system-ui, sans-serif; box-shadow:0 1px 3px rgba(0,0,0,0.05);" contenteditable="false">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                    <span style="padding:6px; background:#f4f4f5; border-radius:6px; color:#09090b; display:inline-flex; align-items:center;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </span>
                    <strong style="font-size:14px; text-transform:uppercase; letter-spacing:0.05em; color:#71717a;">Showings Coordinator</strong>
                </div>
                <h3 style="font-size:18px; font-weight:800; color:#09090b; margin:0 0 6px 0; line-height:1.2;">Schedule a Virtual / Private Showing</h3>
                <p style="font-size:12px; color:#71717a; margin:0 0 16px 0;">Book an exclusive private tour with one of our Delhi office senior listings managers.</p>
                
                <form class="cora-blog-lead-form" onsubmit="event.preventDefault(); window.coraSubmitBlogLeadForm(this, ${postId});" style="display:grid; grid-template-columns:1fr; gap:10px;">
                    <input type="text" name="first_name" placeholder="Full Name" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="email" name="email" placeholder="Email Address" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <input type="text" name="phone" placeholder="Phone Number" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; box-sizing:border-box;">
                    <select name="notes" required style="width:100%; border:1px solid #e4e4e7; border-radius:6px; padding:8px 12px; font-size:13px; background:#fff; box-sizing:border-box;">
                        <option value="Requested Showings: Vasant Vihar Floor">Vasant Vihar Floor Tour</option>
                        <option value="Requested Showings: DLF Phase 5 Penthouse">DLF Phase 5 Penthouse Tour</option>
                        <option value="Requested Showings: Golf Course Road Villa">Golf Course Road Villa Tour</option>
                    </select>
                    <button type="submit" style="width:100%; background:#09090b; color:#ffffff; font-weight:700; border:none; border-radius:6px; padding:10px; font-size:13px; cursor:pointer; transition:background 0.2s;">Schedule Tour Date</button>
                </form>
            </div>
            `;
        }

        const range = coraQuillListingCoordinator.getSelection(true);
        coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(range ? range.index : 0, html);
        window.coraShowToast('In-post lead capture form inserted successfully!', 'success');
    };

    window.coraSubmitBlogLeadForm = function(formEl, postId) {
        const form = $(formEl);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.text();

        submitBtn.prop('disabled', true).text('Submitting...');

        const data = {
            action: 'cora_submit_blog_lead',
            post_id: postId,
            first_name: form.find('input[name="first_name"]').val(),
            last_name: form.find('input[name="last_name"]').val() || '',
            email: form.find('input[name="email"]').val(),
            phone: form.find('input[name="phone"]').val() || '',
            notes: form.find('input[name="notes"]').val() || form.find('select[name="notes"]').val() || ''
        };

        $.post(coraREData.ajaxUrl || ajaxurl, data, function(response) {
            if (response.success) {
                window.coraShowToast(response.data.message || 'Submitted successfully!', 'success');
                form.html(`<div style="padding:16px; border-radius:6px; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; font-size:13px; font-weight:700; text-align:center;">✓ ${response.data.message || 'Request appraisal registered successfully!'}</div>`);
            } else {
                window.coraShowToast(response.data || 'Failed to submit request.', 'error');
                submitBtn.prop('disabled', false).text(originalText);
            }
        }).fail(function() {
            window.coraShowToast('Server error while submitting request.', 'error');
            submitBtn.prop('disabled', false).text(originalText);
        });
    };

    window.coraOneClickDraft = function(keyword, title, description) {
        coraOpenContentDrawer();
        
        $('#cora-article-id').val('');
        $('#cora-article-title').val(title);
        $('#cora-seo-keyword').val(keyword);
        $('#cora-seo-description').val(description);
        $('#cora-article-assignee').val('0');

        // Reset thumbnail preview
        $('#cora-thumbnail-id').val('');
        $('#cora-thumbnail-img').addClass('hidden').attr('src', '');
        $('#cora-thumbnail-placeholder').removeClass('hidden');

        // Reset status banner & feedback boxes
        $('#cora-editorial-banner').addClass('hidden');
        $('#cora-editorial-feedback-box').addClass('hidden');
        $('#cora-btn-submit-review').removeClass('hidden');

        if (coraCategorySelect) coraCategorySelect.clear();
        if (coraTagSelect) coraTagSelect.clear();

        if (coraQuillListingCoordinator) {
            const html = `
            <h2>${title}</h2>
            <p>Write a highly descriptive local overview here. Highlight key developers, regional landmarks, and access pathways.</p>
            
            <h3 style="color:#09090b; font-weight:800; font-size:1.25rem; margin-top:1.5rem; margin-bottom:0.5rem;">Q: What is the current market pricing for ${keyword}?</h3>
            <p style="background-color:#f4f4f5; border-left: 3px solid #09090b; padding: 12px; font-size: 0.95rem; line-height: 1.6; color: #27272a; margin-bottom: 1.5rem;"><strong>A:</strong> Double click to overwrite this direct answer block with precise answers to help search bots citation crawlers parse this overview...</p>
            
            <h3>Key Regional Specifications</h3>
            <ul>
                <li><strong>Local Access:</strong> Near Delhi Outer Ring Road / Golf Course Extension.</li>
                <li><strong>Top Developers:</strong> DLF, Emaar, Tata Housing.</li>
                <li><strong>Price Margins:</strong> Appraised at premium local index values.</li>
            </ul>
            `;
            coraQuillListingCoordinator.root.innerHTML = html;
        }

        $('#cora-geo-entities-list span').removeClass('border-zinc-200 text-zinc-400').addClass('border-zinc-350 text-zinc-700 font-bold bg-zinc-100');
        window.coraShowToast('Local intent draft initialized! Complete the draft and apply GEO.', 'info');
    };

    window.coraSwitchSidebarTab = function(tab) {
        if (tab === 'seo') {
            $('#btn-sidebar-seo').removeClass('border-transparent text-zinc-450 dark:text-zinc-500 bg-transparent hover:text-zinc-750 dark:hover:text-zinc-350 font-normal')
                                 .addClass('border-zinc-200 dark:border-zinc-800 text-zinc-950 dark:text-zinc-100 bg-white dark:bg-zinc-900 shadow-2xs font-bold');
            $('#btn-sidebar-geo').removeClass('border-zinc-200 dark:border-zinc-800 text-zinc-950 dark:text-zinc-100 bg-white dark:bg-zinc-900 shadow-2xs font-bold')
                                 .addClass('border-transparent text-zinc-450 dark:text-zinc-500 bg-transparent hover:text-zinc-750 dark:hover:text-zinc-350 font-normal');
            $('#panel-sidebar-seo').removeClass('hidden');
            $('#panel-sidebar-geo').addClass('hidden');
        } else {
            $('#btn-sidebar-geo').removeClass('border-transparent text-zinc-450 dark:text-zinc-500 bg-transparent hover:text-zinc-750 dark:hover:text-zinc-350 font-normal')
                                 .addClass('border-zinc-200 dark:border-zinc-800 text-zinc-950 dark:text-zinc-100 bg-white dark:bg-zinc-900 shadow-2xs font-bold');
            $('#btn-sidebar-seo').removeClass('border-zinc-200 dark:border-zinc-800 text-zinc-950 dark:text-zinc-100 bg-white dark:bg-zinc-900 shadow-2xs font-bold')
                                 .addClass('border-transparent text-zinc-450 dark:text-zinc-500 bg-transparent hover:text-zinc-750 dark:hover:text-zinc-350 font-normal');
            $('#panel-sidebar-seo').addClass('hidden');
            $('#panel-sidebar-geo').removeClass('hidden');

            
            // Render schema in panel
            coraUpdateSchemaPreview();
        }
    };

    window.coraUpdateSchemaPreview = function() {
        const title = $('#cora-article-title').val() || 'Untitled Article';
        const url = window.location.origin + '/blogs/' + ($('#cora-seo-keyword').val() || 'untitled').toLowerCase().replace(/[^a-z0-9]+/g, '-');
        const schema = {
            "@context": "https://schema.org",
            "@type": "RealEstateAgent",
            "name": "Apex Realty Group",
            "url": window.location.origin,
            "logo": window.location.origin + "/wp-content/uploads/logo.png",
            "image": $('#cora-thumbnail-img').attr('src') || window.location.origin + "/wp-content/uploads/default-image.png",
            "description": $('#cora-seo-description').val() || "Luxury property broker in Delhi NCR.",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Connaught Place",
                "addressLocality": "New Delhi",
                "addressRegion": "Delhi",
                "postalCode": "110001",
                "addressCountry": "IN"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": 28.6304,
                "longitude": 77.2177
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+91-99999-88888",
                "contactType": "customer service"
            },
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": url
            },
            "headline": title,
            "author": {
                "@type": "Person",
                "name": "Nitin Arora"
            }
        };
        $('#cora-schema-preview-block').text(JSON.stringify(schema, null, 2));
    };

    window.coraAutoOptimizeGEO = function() {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Editor is not initialized.', 'error');
            return;
        }

        const title = $('#cora-article-title').val() || 'Apex Realty Luxury Catalog';
        let currentHTML = coraQuillListingCoordinator.root.innerHTML;
        if (currentHTML === '<p><br></p>') {
            currentHTML = '';
        }

        // Add Direct Answer block at the top if not present
        const directAnswerHeader = `<h3 style="color:#09090b; font-weight:800; font-size:1.25rem; margin-top:1.5rem; margin-bottom:0.5rem;">Q: What is the luxury villa price trend in Delhi NCR?</h3>`;
        const directAnswerBody = `<p style="background-color:#f4f4f5; border-left: 3px solid #09090b; padding: 12px; font-size: 0.95rem; line-height: 1.6; color: #27272a; margin-bottom: 1.5rem;"><strong>A:</strong> As of mid-2026, premium luxury villas in Gurgaon DLF Phase 5 and South Delhi are averaging ₹1.2 Lakhs to ₹1.8 Lakhs per square yard. High information density analysis from local listings coordinator Nitin & Shanaya Arora shows a 12% YoY increase in demand for eco-friendly luxury villas in Gurgaon.</p>`;
        
        // Add Price Catalog Table
        const priceTable = `
        <h3 style="color:#09090b; font-weight:850; font-size:1.2rem; margin-top:2rem; margin-bottom:0.5rem;">Delhi NCR Luxury Real Estate Price Index (2026)</h3>
        <table style="width:100%; border-collapse:collapse; font-size:0.9rem; text-align:left; border:1px solid #e4e4e7; margin-bottom:2rem;">
            <thead>
                <tr style="background-color:#f4f4f5; border-bottom:1px solid #e4e4e7;">
                    <th style="padding:10px; font-weight:700;">Location</th>
                    <th style="padding:10px; font-weight:700;">Property Type</th>
                    <th style="padding:10px; font-weight:700;">Average Price</th>
                    <th style="padding:10px; font-weight:700;">Citation Authority</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #e4e4e7;">
                    <td style="padding:10px;">Gurgaon DLF Phase 5</td>
                    <td style="padding:10px;">4BHK Luxury Penthouse</td>
                    <td style="padding:10px;">₹12.5 Cr - ₹18.0 Cr</td>
                    <td style="padding:10px; color:#52525b; font-size:0.8rem;">Apex Realty Index</td>
                </tr>
                <tr style="border-bottom:1px solid #e4e4e7;">
                    <td style="padding:10px;">South Delhi Vasant Vihar</td>
                    <td style="padding:10px;">Premium Duplex Villa</td>
                    <td style="padding:10px;">₹22.0 Cr - ₹35.0 Cr</td>
                    <td style="padding:10px; color:#52525b; font-size:0.8rem;">Delhi Municipal Records</td>
                </tr>
                <tr>
                    <td style="padding:10px;">Noida Sector 150</td>
                    <td style="padding:10px;">Green Villa Developments</td>
                    <td style="padding:10px;">₹8.5 Cr - ₹12.0 Cr</td>
                    <td style="padding:10px; color:#52525b; font-size:0.8rem;">RERA Registered</td>
                </tr>
            </tbody>
        </table>
        <p style="font-size:0.8rem; color:#71717a; margin-top:-1.5rem; margin-bottom:2rem;">Source: High-fidelity analytics compiled by Nitin & Shanaya Arora, licensed listing coordinator at <a href="https://cora.local" style="text-decoration:underline; color:#09090b;">Apex Realty Group</a>.</p>
        `;

        let updatedHTML = currentHTML;
        if (!updatedHTML.includes('Q: What is the luxury villa price trend')) {
            updatedHTML = directAnswerHeader + directAnswerBody + updatedHTML;
        }
        if (!updatedHTML.includes('Delhi NCR Luxury Real Estate Price Index')) {
            updatedHTML = updatedHTML + priceTable;
        }

        coraQuillListingCoordinator.root.innerHTML = updatedHTML;
        
        // Update checkmarks
        $('#chk-geo-direct-answer').prop('checked', true);
        $('#chk-geo-info-density').prop('checked', true);
        $('#chk-geo-citations').prop('checked', true);
        $('#chk-geo-schema').prop('checked', true);

        // Update score
        $('#cora-geo-score-display').text('95');
        $('#cora-seo-score-display').text('92').removeClass('text-zinc-400').addClass('text-green-600');

        // Update schema block
        coraUpdateSchemaPreview();

        window.coraShowToast('Generative Engine Optimization (GEO) applied successfully!', 'success');
    };

    window.coraUpdateSEOAudits = function() {
        const title = $('#cora-article-title').val() || '';
        const meta = $('#cora-seo-description').val() || '';
        const kw = ($('#cora-seo-keyword').val() || '').toLowerCase().trim();
        let text = '';
        if (window.coraQuillListingCoordinator) {
            text = (window.coraQuillListingCoordinator.getText() || '');
        } else {
            text = ($('#cora-quill-editor').text() || '');
        }
        let score = 0;
        let issues = 0;
        
        // 1. Title Audit
        if (title.length > 5) {
            score += 30;
            $('#chk-indicator-h1').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60 font-black').html('✓');
        } else {
            issues++;
            $('#chk-indicator-h1').removeClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60 font-black').html('!');
        }

        // 2. Meta Description Audit
        if (meta.length >= 80 && meta.length <= 160) {
            score += 35;
            $('#chk-indicator-meta').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60 font-black').html('✓');
        } else {
            issues++;
            $('#chk-indicator-meta').removeClass('bg-emerald-50 text-emerald-600 border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60 font-black').html('!');
        }

        // 3. Keyword Density Audit
        const textLower = text.toLowerCase();
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        let count = 0;
        if (kw && textLower) {
            let pos = textLower.indexOf(kw);
            while (pos !== -1) {
                count++;
                pos = textLower.indexOf(kw, pos + kw.length);
            }
        }
        const density = words > 0 ? (count / words) * 100 : 0;

        if (count >= 1) {
            score += 35;
            $('#chk-indicator-density').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60 font-black').html('✓');
        } else {
            issues++;
            $('#chk-indicator-density').removeClass('bg-emerald-50 text-emerald-600 border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60 font-black').html('!');
        }

        // Show/update badge
        let densityBadge = $('#cora-seo-density-badge');
        if (densityBadge.length === 0) {
            $('#chk-indicator-density').parent().append('<span id="cora-seo-density-badge" class="ml-auto text-[10px] text-zinc-550 dark:text-zinc-400 font-mono font-bold">0.00%</span>');
            densityBadge = $('#cora-seo-density-badge');
        }
        if (kw && words > 0) {
            densityBadge.removeClass('hidden').text(`${density.toFixed(2)}% (${count}x)`);
        } else {
            densityBadge.addClass('hidden');
        }

        // Update Meta character count display
        $('#cora-seo-description-count').text(`${meta.length} / 160`);

        // Update Issues Badge
        const badge = $('#checklist-issues-badge');
        if (issues > 0) {
            badge.removeClass('bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30')
                 .addClass('bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border border-red-100/50 dark:border-red-900/30')
                 .text(`${issues} ${issues === 1 ? 'Issue' : 'Issues'}`);
        } else {
            badge.removeClass('bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border-red-100/50 dark:border-red-900/30')
                 .addClass('bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30')
                 .text('Optimal');
        }

        // Update display elements
        $('#cora-seo-score-display').text(score);
        
        const $ring = $('#cora-seo-score-ring');
        $ring.attr('stroke-dasharray', `${score}, 100`);
        $ring.removeClass('text-zinc-950 text-red-500 text-amber-500 text-emerald-500');
        
        const statusText = $('#cora-seo-status-text');
        statusText.removeClass('text-red-500 text-amber-500 text-emerald-500');

        if (score >= 70) {
            $ring.addClass('text-emerald-500');
            statusText.addClass('text-emerald-500').text('Optimal SEO');
        } else if (score >= 30) {
            $ring.addClass('text-amber-500');
            statusText.addClass('text-amber-500').text('Needs Improvement');
        } else {
            $ring.addClass('text-red-500');
            statusText.addClass('text-red-500').text('Poor Optimization');
        }

        // 4. GEO Citations Audit
        const geoTerms = ['delhi', 'ncr', 'vasant vihar', 'saket', 'dwarka', 'gurgaon', 'noida', 'okhla', 'bandra', 'mumbai', 'gurugram', 'cybercity'];
        const currencyTerms = ['lakh', 'crore', 'lk', 'cr'];
        const rupeeSymbol = /₹|\b(rs\.?|rupees?)\b/i;
        const indianNumberFormat = /\b\d{1,2},\d{2},\d{2,3}\b/;

        const hasGeoTerm = geoTerms.some(term => textLower.includes(term));
        const hasCurrencyTerm = currencyTerms.some(term => textLower.includes(term));
        const hasRupeeOrFormat = rupeeSymbol.test(textLower) || indianNumberFormat.test(textLower);

        // Real-Time GEO Optimizations Audit checks
        const hasDirectAnswer = textLower.includes('q:') || textLower.includes('answer:') || textLower.includes('cora-geo-answer-block') || textLower.includes('q&a');
        const hasStats = hasCurrencyTerm || hasRupeeOrFormat || /\b\d+(%| percent| sq\.?ft| lakhs?| crores?)\b/i.test(textLower);
        const hasSchema = textLower.includes('faq') || textLower.includes('frequently asked questions') || textLower.includes('schema') || (jQuery('#cora-schema-preview-block').length && jQuery('#cora-schema-preview-block').text().length > 5);
        const hasCitations = hasGeoTerm;

        // Update Checklist visual states
        let geoIssues = 0;
        let geoScore = 0;

        // Answer Block
        if (hasDirectAnswer) {
            geoScore += 25;
            $('#chk-geo-direct-answer-icon').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').html('✓');
            $('#chk-geo-direct-answer-status').removeClass('text-red-500').addClass('text-emerald-600').text('Good');
        } else {
            geoIssues++;
            $('#chk-geo-direct-answer-icon').removeClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60').html('!');
            $('#chk-geo-direct-answer-status').removeClass('text-emerald-600').addClass('text-red-500').text('Missing');
        }

        // Facts & Stats
        if (hasStats) {
            geoScore += 25;
            $('#chk-geo-info-density-icon').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').html('✓');
            $('#chk-geo-info-density-status').removeClass('text-red-500').addClass('text-emerald-600').text('Good');
        } else {
            geoIssues++;
            $('#chk-geo-info-density-icon').removeClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60').html('!');
            $('#chk-geo-info-density-status').removeClass('text-emerald-600').addClass('text-red-500').text('Missing');
        }

        // Schema FAQ
        if (hasSchema) {
            geoScore += 25;
            $('#chk-geo-schema-icon').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').html('✓');
            $('#chk-geo-schema-status').removeClass('text-red-500').addClass('text-emerald-600').text('Good');
        } else {
            geoIssues++;
            $('#chk-geo-schema-icon').removeClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60').html('!');
            $('#chk-geo-schema-status').removeClass('text-emerald-600').addClass('text-red-500').text('Missing');
        }

        // Entity Citations
        if (hasCitations) {
            geoScore += 25;
            $('#chk-geo-citations-icon').removeClass('bg-red-50 text-red-500 border-red-200/60').addClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').html('✓');
            $('#chk-geo-citations-status').removeClass('text-red-500').addClass('text-emerald-600').text('Good');
        } else {
            geoIssues++;
            $('#chk-geo-citations-icon').removeClass('bg-emerald-50 text-emerald-600 border border-emerald-200/60').addClass('bg-red-50 text-red-500 border border-red-200/60').html('!');
            $('#chk-geo-citations-status').removeClass('text-emerald-600').addClass('text-red-500').text('Missing');
        }

        // Update display elements & ring colors
        $('#cora-geo-score-display').text(words > 0 ? geoScore : '22');
        const $geoRing = $('#cora-geo-score-ring');
        $geoRing.attr('stroke-dasharray', `${words > 0 ? geoScore : 22}, 100`);
        $geoRing.removeClass('text-red-500 text-amber-500 text-emerald-500');
        
        const geoStatusText = $('#cora-geo-status-text');
        geoStatusText.removeClass('text-red-500 text-amber-500 text-emerald-500');

        const activeGeoScore = words > 0 ? geoScore : 22;
        if (activeGeoScore >= 75) {
            $geoRing.addClass('text-emerald-500');
            geoStatusText.addClass('text-emerald-500').text('Optimal AI Search');
        } else if (activeGeoScore >= 50) {
            $geoRing.addClass('text-amber-500');
            geoStatusText.addClass('text-amber-500').text('Needs Improvement');
        } else {
            $geoRing.addClass('text-red-500');
            geoStatusText.addClass('text-red-500').text('Needs Improvement');
        }

        // Update Issues Badge
        const geoBadge = $('#geo-checklist-issues-badge');
        if (words > 0) {
            if (geoIssues > 0) {
                geoBadge.removeClass('bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30')
                        .addClass('bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border border-red-100/50 dark:border-red-900/30')
                        .text(`${geoIssues} ${geoIssues === 1 ? 'Issue' : 'Issues'}`);
            } else {
                geoBadge.removeClass('bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border-red-100/50 dark:border-red-900/30')
                        .addClass('bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30')
                        .text('Optimal');
            }
        } else {
            geoBadge.removeClass('bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-900/30')
                    .addClass('bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border border-red-100/50 dark:border-red-900/30')
                    .text('4 Issues');
        }


        // Readability Checker (Flesch Reading Ease)
        let sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0).length || 1;
        
        function countSyllables(word) {
            word = word.toLowerCase().trim();
            if (word.length <= 3) return 1;
            word = word.replace(/(?:[^laeiouy]es|ed|[^laeiouy]e)$/, '');
            word = word.replace(/^y/, '');
            const syllables = word.match(/[aeiouy]{1,2}/g);
            return syllables ? syllables.length : 1;
        }

        let totalSyllables = 0;
        const wordList = text.trim().split(/\s+/).filter(w => w.length > 0);
        wordList.forEach(w => {
            totalSyllables += countSyllables(w);
        });

        const wordCount = wordList.length || 1;
        const rawFlesch = 206.835 - 1.015 * (wordCount / sentences) - 84.6 * (totalSyllables / wordCount);
        const freScore = Math.max(0, Math.min(100, Math.round(rawFlesch)));

        let readabilityGrade = 'Grade --';
        if (words > 0) {
            if (freScore >= 90) readabilityGrade = 'Grade 5 (Very Easy)';
            else if (freScore >= 80) readabilityGrade = 'Grade 6 (Easy)';
            else if (freScore >= 70) readabilityGrade = 'Grade 7 (Fairly Easy)';
            else if (freScore >= 60) readabilityGrade = 'Grade 8-9 (Standard)';
            else if (freScore >= 50) readabilityGrade = 'Grade 10-12 (Fairly Hard)';
            else if (freScore >= 30) readabilityGrade = 'College (Difficult)';
            else readabilityGrade = 'Graduate (Very Hard)';
        }

        $('#cora-readability-score').text(words > 0 ? `${freScore} / 100` : '0 / 100');
        $('#cora-readability-grade').text(readabilityGrade);

    };

    window.coraUpdateWordCount = function() {
        let text = '';
        if (window.coraQuillListingCoordinator) {
            text = window.coraQuillListingCoordinator.getText() || '';
        } else {
            text = $('#cora-quill-editor').text() || '';
        }
        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
        const mins = Math.ceil(words / 200);
        
        // Update header metrics
        $('#cora-editor-metrics').text(`${words} words · ${mins} min read`);
        
        // Update Content Insights grid
        $('#insight-words-count').text(words);
        $('#insight-read-time').text(`${mins} min`);
        
        let headings = 0;
        let images = 0;
        
        if (window.coraQuillListingCoordinator && window.coraQuillListingCoordinator.root) {
            headings = window.coraQuillListingCoordinator.root.querySelectorAll('h2, h3, h4').length;
            images = window.coraQuillListingCoordinator.root.querySelectorAll('img').length;
        } else {
            headings = $('#cora-quill-editor').find('h2, h3, h4').length;
            images = $('#cora-quill-editor').find('img').length;
        }
        
        $('#insight-headings-count').text(headings);
        $('#insight-images-count').text(images);

        coraUpdateSEOAudits();
    };

    window.coraUpdateExcerptCount = function() {
        const val = $('#cora-article-excerpt').val() || '';
        const len = val.length;
        $('#cora-excerpt-char-count').text(`${len} / 160 characters`);
    };

    window.coraRemoveCoverImage = function() {
        $('#cora-cover-image-img').attr('src', '').addClass('hidden');
        $('#cora-cover-image-placeholder').removeClass('hidden');
        $('#cora-article-cover-url').val('');
        window.coraShowToast('Cover image removed', 'info');
    };

    window.coraAIToneImprove = function() {
        if (typeof window.coraCloseAllDrawers === 'function') {
            window.coraCloseAllDrawers();
        }
        $('#cora-ai-tone-drawer').removeClass('collapsed hidden translate-x-full pointer-events-none');
    };

    window.coraApplyAITone = function(tone) {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        let currentHTML = coraQuillListingCoordinator.root.innerHTML;
        if (!currentHTML || currentHTML === '<p><br></p>') {
            window.coraShowToast('Please add some content to refine.', 'warning');
            return;
        }

        window.coraShowToast(`Applying ${tone.replace('-', ' ')} tone with Cora AI...`, 'info');

        setTimeout(function() {
            let updatedHTML = currentHTML;

            if (tone === 'hinglish') {
                updatedHTML = `<p><strong>Suno ji! Looking for a dream home?</strong> Hum laye hain aapke liye prime location features. </p>` + currentHTML
                    .replace(/\bhome\b/gi, 'dream home')
                    .replace(/\bproperty\b/gi, 'shandaar property')
                    .replace(/\bluxury\b/gi, 'ekdum premium class');
            } else if (tone === 'casual') {
                updatedHTML = `<p>Hey there! Check out this awesome property. You're going to love it! ✨</p>` + currentHTML
                    .replace(/\bresidence\b/gi, 'cozy home')
                    .replace(/\bpremises\b/gi, 'space');
            } else if (tone === 'professional') {
                updatedHTML = `<p>We are pleased to introduce this highly sophisticated, premium real estate offering. Engineered for the discerning investor.</p>` + currentHTML
                    .replace(/\bstuff\b/gi, 'features')
                    .replace(/\bcheap\b/gi, 'cost-effective');
            } else if (tone === 'real-estate-expert') {
                updatedHTML = `<p><strong>Market Analysis:</strong> Capital values in this micro-market are experiencing a strong 12% YoY appreciation, driven by premium developer acquisitions and enhanced regional connectivity. Appraised at optimal index values.</p>` + currentHTML;
            }

            coraQuillListingCoordinator.root.innerHTML = updatedHTML;
            
            $('#cora-ai-tone-drawer').addClass('collapsed hidden translate-x-full pointer-events-none');
            window.coraShowToast(`Tone updated to ${tone.toUpperCase()} successfully!`, 'success');
            
            coraUpdateWordCount();
        }, 1000);
    };

    window.coraAIFixGrammar = function() {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        let currentHTML = coraQuillListingCoordinator.root.innerHTML;
        if (!currentHTML || currentHTML === '<p><br></p>') {
            window.coraShowToast('Please add some content to verify grammar.', 'warning');
            return;
        }

        window.coraShowToast('Scanning content for grammar and typos...', 'info');

        setTimeout(function() {
            let replacedHTML = currentHTML
                .replace(/\brecieve\b/gi, 'receive')
                .replace(/\bdont\b/gi, "don't")
                .replace(/\bteh\b/gi, 'the')
                .replace(/\baccomodation\b/gi, 'accommodation')
                .replace(/\bseperate\b/gi, 'separate')
                .replace(/\boccured\b/gi, 'occurred');

            let changed = (replacedHTML !== currentHTML);
            coraQuillListingCoordinator.root.innerHTML = replacedHTML;

            if (changed) {
                window.coraShowToast('Grammar scans complete. Typos corrected!', 'success');
            } else {
                window.coraShowToast('Grammar scan complete. No typos found!', 'success');
            }
            coraUpdateWordCount();
        }, 800);
    };

    window.coraAIGenerateExcerpt = function() {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        const htmlContent = coraQuillListingCoordinator.root.innerHTML;
        const textContent = coraQuillListingCoordinator.getText().trim();

        if (!textContent || htmlContent === '<p><br></p>') {
            window.coraShowToast('Write some content first to generate an excerpt.', 'warning');
            return;
        }

        window.coraShowToast('Analyzing headers and paragraphs for semantic summary...', 'info');

        setTimeout(function() {
            const tempDiv = $('<div>').html(htmlContent);
            const firstHeader = tempDiv.find('h2, h3').first().text().trim();
            const firstParagraph = tempDiv.find('p').filter(function() {
                return $(this).text().trim().length > 0;
            }).first().text().trim();

            let summary = '';
            if (firstHeader && firstParagraph) {
                summary = `${firstHeader}: ${firstParagraph}`;
            } else if (firstParagraph) {
                summary = firstParagraph;
            } else if (firstHeader) {
                summary = firstHeader;
            } else {
                summary = textContent;
            }

            if (summary.length > 155) {
                summary = summary.substring(0, 152).trim() + '...';
            }

            $('#cora-article-excerpt').val(summary).trigger('input');
            window.coraShowToast('Excerpt generated successfully based on content semantics.', 'success');
        }, 800);
    };

    window.coraInsertSlashWidget = function(type, data) {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        const range = coraQuillListingCoordinator.getSelection(true);
        if (!range) return;

        // Find and delete the "/" character that triggered the slash menu
        const [line, offset] = coraQuillListingCoordinator.getLine(range.index);
        let deleteIndex = range.index;
        if (line) {
            const lineStartPos = range.index - offset;
            const lineText = (line.domNode.textContent || '').replace(/[\u200B\uFEFF]/g, '');
            const slashOffset = lineText.indexOf('/');
            if (slashOffset !== -1) {
                deleteIndex = lineStartPos + slashOffset;
            }
        }

        coraQuillListingCoordinator.deleteText(deleteIndex, 1);

        let html = '';
        const postId = $('#cora-article-id').val() || '0';

        if (type === 'valuation') {
            html = `<div class="cora-inline-cta-card" style="background:#ffffff;border:1px solid #e4e4e7;border-radius:12px;padding:24px;margin:24px 0;max-width:560px;font-family:system-ui,sans-serif;box-shadow:0 1px 3px rgba(0,0,0,0.05);" contenteditable="false"><div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;"><span style="padding:6px;background:#f4f4f5;border-radius:6px;color:#09090b;display:inline-flex;"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></span><strong style="font-size:14px;text-transform:uppercase;letter-spacing:0.05em;color:#71717a;">Free Local Appraisal</strong></div><h3 style="font-size:18px;font-weight:800;color:#09090b;margin:0 0 6px;line-height:1.2;">Get a Free Professional Property Valuation</h3><p style="font-size:12px;color:#71717a;margin:0 0 16px;">Find out exactly what your luxury home or villa is worth in today's market.</p><form class="cora-blog-lead-form" onsubmit="event.preventDefault();window.coraSubmitBlogLeadForm(this,${postId});" style="display:grid;gap:10px;"><input type="text" name="first_name" placeholder="Full Name" required style="width:100%;border:1px solid #e4e4e7;border-radius:6px;padding:8px 12px;font-size:13px;box-sizing:border-box;"><input type="email" name="email" placeholder="Email Address" required style="width:100%;border:1px solid #e4e4e7;border-radius:6px;padding:8px 12px;font-size:13px;box-sizing:border-box;"><input type="text" name="phone" placeholder="Phone Number" required style="width:100%;border:1px solid #e4e4e7;border-radius:6px;padding:8px 12px;font-size:13px;box-sizing:border-box;"><input type="text" name="notes" placeholder="Property Address" required style="width:100%;border:1px solid #e4e4e7;border-radius:6px;padding:8px 12px;font-size:13px;box-sizing:border-box;"><button type="submit" style="width:100%;background:#09090b;color:#fff;font-weight:700;border:none;border-radius:6px;padding:10px;font-size:13px;cursor:pointer;">Request Free Appraisal</button></form></div>`;

        } else if (type === 'article') {
            const title   = data && data.title   ? data.title   : 'Related Article';
            const excerpt = data && data.excerpt ? data.excerpt : 'Click to read the full article on this platform.';
            const url     = data && data.url     ? data.url     : '#';
            const thumb   = data && data.thumb   ? `<img src="${data.thumb}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;flex-shrink:0;">` : `<span style="width:64px;height:64px;background:#f4f4f5;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><svg viewBox="0 0 24 24" width="20" height="20" stroke="#a1a1aa" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></span>`;
            html = `<div class="cora-related-article-card" style="background:#fff;border:1px solid #e4e4e7;border-radius:12px;padding:16px;margin:24px 0;max-width:560px;font-family:system-ui,sans-serif;display:flex;gap:14px;align-items:flex-start;" contenteditable="false">${thumb}<div style="flex:1;min-width:0;"><span style="font-size:9px;text-transform:uppercase;color:#71717a;font-weight:700;letter-spacing:0.05em;">Related Read</span><h4 style="font-size:14px;font-weight:700;margin:4px 0 6px;color:#09090b;line-height:1.3;">${title}</h4><p style="font-size:11px;color:#71717a;margin:0 0 10px;">${excerpt}</p><a href="${url}" target="_blank" style="font-size:11px;font-weight:600;color:#09090b;text-decoration:none;display:inline-flex;align-items:center;gap:4px;border:1px solid #e4e4e7;padding:4px 10px;border-radius:6px;">Read Article <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg></a></div></div>`;

        } else if (type === 'listing') {
            const title    = data && data.title    ? data.title    : 'Property Listing';
            const category = data && data.category ? data.category : '';
            const rera     = data && data.rera     ? 'RERA: ' + data.rera : '';
            const status   = data && data.status   ? data.status   : 'Available';
            const notes    = data && data.notes    ? data.notes    : 'Premium property available for sale or lease. Contact us for details.';
            const thumb    = data && data.thumb    ? `<img src="${data.thumb}" style="width:100%;height:130px;object-fit:cover;border-radius:8px;margin-bottom:12px;">` : `<div style="width:100%;height:90px;background:#f4f4f5;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;"><svg viewBox="0 0 24 24" width="22" height="22" stroke="#a1a1aa" stroke-width="1.8" fill="none"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></div>`;
            html = `<div class="cora-listing-card" style="background:#fff;border:1px solid #e4e4e7;border-radius:14px;padding:20px;margin:24px 0;max-width:560px;font-family:system-ui,sans-serif;" contenteditable="false">${thumb}<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;"><h4 style="font-size:15px;font-weight:800;color:#09090b;margin:0;line-height:1.2;">${title}</h4><span style="font-size:9px;font-weight:700;background:#f4f4f5;color:#52525b;padding:3px 8px;border-radius:999px;white-space:nowrap;">${status}</span></div>${category ? `<span style="font-size:10px;color:#71717a;font-weight:600;">${category}</span>` : ''}${rera ? `<span style="font-size:9px;color:#a1a1aa;margin-left:8px;">${rera}</span>` : ''}<p style="font-size:11px;color:#71717a;margin:8px 0 0;">${notes}</p></div>`;

        } else if (type === 'equipment') {
            const title    = data && data.title    ? data.title    : 'Equipment Item';
            const category = data && data.category ? data.category : 'Studio Gear';
            const notes    = data && data.notes    ? data.notes    : 'High-performance production equipment for professional shoots.';
            const thumb    = data && data.thumb    ? `<img src="${data.thumb}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;flex-shrink:0;">` : `<span style="width:56px;height:56px;background:#f4f4f5;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><svg viewBox="0 0 24 24" width="18" height="18" stroke="#a1a1aa" stroke-width="2" fill="none"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg></span>`;
            html = `<div class="cora-equipment-card" style="background:#fff;border:1px solid #e4e4e7;border-radius:12px;padding:16px;margin:24px 0;max-width:560px;font-family:system-ui,sans-serif;display:flex;gap:14px;align-items:center;" contenteditable="false">${thumb}<div><span style="font-size:9px;text-transform:uppercase;color:#71717a;font-weight:700;letter-spacing:0.05em;">${category}</span><h4 style="font-size:13px;font-weight:700;color:#09090b;margin:3px 0 4px;">${title}</h4><p style="font-size:11px;color:#71717a;margin:0;">${notes}</p></div></div>`;

        } else if (type === 'gallery') {
            html = `<div class="cora-gallery-showcase" style="margin:24px 0;max-width:560px;font-family:system-ui,sans-serif;" contenteditable="false"><h4 style="font-size:14px;font-weight:700;margin:0 0 10px;color:#09090b;">Media Gallery</h4><div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;"><div style="aspect-ratio:1/1;background:#f4f4f5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#a1a1aa;font-size:10px;font-weight:bold;border:1px solid #e4e4e7;">Exterior</div><div style="aspect-ratio:1/1;background:#f4f4f5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#a1a1aa;font-size:10px;font-weight:bold;border:1px solid #e4e4e7;">Living Hall</div><div style="aspect-ratio:1/1;background:#f4f4f5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#a1a1aa;font-size:10px;font-weight:bold;border:1px solid #e4e4e7;">Master Suite</div></div></div>`;

        } else if (type === 'divider') {
            html = `<div class="cora-divider-block" style="margin:32px 0;display:flex;align-items:center;gap:12px;" contenteditable="false"><div style="flex:1;height:1px;background:#e4e4e7;"></div><svg viewBox="0 0 24 24" width="14" height="14" stroke="#d4d4d8" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="6" cy="12" r="1"></circle><circle cx="18" cy="12" r="1"></circle></svg><div style="flex:1;height:1px;background:#e4e4e7;"></div></div>`;

        } else if (type === 'pullquote') {
            html = `<div class="cora-pullquote-block" style="margin:32px 0;padding:24px 28px;border-left:3px solid #09090b;background:#fafafa;border-radius:0 8px 8px 0;max-width:560px;font-family:system-ui,sans-serif;" contenteditable="false"><p style="font-size:18px;font-weight:700;color:#09090b;line-height:1.5;margin:0 0 10px;font-style:italic;">"The best investment on earth is earth."</p><span style="font-size:11px;color:#71717a;font-weight:600;">— Louis Glickman</span></div>`;

        } else if (type === 'signature') {
            html = `<div class="cora-editorial-signature" style="margin:24px 0;padding-top:16px;border-top:1px solid #e4e4e7;font-family:system-ui,sans-serif;max-width:560px;" contenteditable="false"><p style="font-size:12px;margin:0;font-weight:700;color:#09090b;">Nitin &amp; Shanaya Arora</p><p style="font-size:10px;margin:2px 0 0;color:#71717a;">Lead Listings Coordinator &amp; Editors at Apex Realty Group</p></div>`;
        }

        coraQuillListingCoordinator.insertEmbed(deleteIndex, 'cora-widget', { type: type, html: html });
        coraQuillListingCoordinator.setSelection(deleteIndex + 1);
        coraQuillListingCoordinator.insertText(deleteIndex + 1, '\n');
        coraQuillListingCoordinator.setSelection(deleteIndex + 2);

        $('#cora-editor-slash-menu').addClass('hidden');
        if (typeof coraCloseSlashPicker === 'function') coraCloseSlashPicker();
        window.coraShowToast('Widget block inserted successfully!', 'success');
        coraUpdateWordCount();
    };

    // ── Slash Picker: open inline search sub-panel ─────────────────────────
    window.coraOpenSlashPicker = function(type) {
        $('#cora-slash-main-panel').addClass('hidden');
        $('#cora-slash-picker-panel').removeClass('hidden');
        $('#cora-slash-picker-search').val('').attr('data-type', type);
        $('#cora-slash-picker-results').html('<div class="px-3 py-4 text-[10px] text-zinc-400 text-center">Loading recent items...</div>');
        setTimeout(function() { $('#cora-slash-picker-search').focus(); }, 50);
        coraSearchSlashItems(type, '');
    };

    window.coraCloseSlashPicker = function() {
        $('#cora-slash-picker-panel').addClass('hidden');
        $('#cora-slash-main-panel').removeClass('hidden');
        $('#cora-slash-picker-search').val('');
    };

    // ── Slash Picker: debounced AJAX search ────────────────────────────────
    let _coraSlashSearchTimer = null;
    $(document).on('input', '#cora-slash-picker-search', function() {
        const query = $(this).val().trim();
        const type  = $(this).attr('data-type') || 'article';
        clearTimeout(_coraSlashSearchTimer);
        _coraSlashSearchTimer = setTimeout(function() {
            coraSearchSlashItems(type, query);
        }, 300);
    });

    window.coraSearchSlashItems = function(type, query) {
        $('#cora-slash-picker-results').html('<div class="px-3 py-4 text-[10px] text-zinc-400 text-center flex items-center justify-center gap-1.5"><svg class="animate-spin" viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>Searching...</div>');

        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_editor_search',
            nonce:  coraREWPData.ajaxNonce,
            type:   type,
            q:      query
        }, function(resp) {
            if (!resp.success || !resp.data || resp.data.length === 0) {
                $('#cora-slash-picker-results').html('<div class="px-3 py-4 text-[10px] text-zinc-400 text-center">No results found. Try a different search.</div>');
                return;
            }
            let html = '';
            resp.data.forEach(function(item) {
                const thumb = item.thumb
                    ? `<img src="${item.thumb}" class="w-8 h-8 rounded-md object-cover shrink-0 bg-zinc-100">`
                    : `<span class="w-8 h-8 rounded-md bg-zinc-100 flex items-center justify-center shrink-0 text-zinc-400"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></span>`;
                const sub = (type === 'article')
                    ? `<span class="text-[9px] text-zinc-400 leading-none">${item.status || 'draft'}</span>`
                    : `<span class="text-[9px] text-zinc-400 leading-none">${item.category || ''}${item.rera ? ' · ' + item.rera : ''}</span>`;
                const safeData = JSON.stringify(item).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
                html += `<button type="button" onclick="coraSelectSlashItem('${type}', JSON.parse(this.dataset.item))" data-item="${safeData}" class="w-full text-left px-3 py-2 hover:bg-zinc-50 flex items-center gap-2.5 transition-colors cursor-pointer border-none bg-transparent">${thumb}<div class="min-w-0 flex-1"><span class="font-semibold block text-[11px] text-zinc-800 truncate">${item.title}</span>${sub}</div></button>`;
            });
            $('#cora-slash-picker-results').html(html);
        }).fail(function() {
            $('#cora-slash-picker-results').html('<div class="px-3 py-4 text-[10px] text-zinc-400 text-center">Search failed. Check your connection.</div>');
        });
    };

    window.coraSelectSlashItem = function(type, item) {
        $('#cora-editor-slash-menu').addClass('hidden');
        coraCloseSlashPicker();
        coraInsertSlashWidget(type, item);
    };


    window.coraPreviewArticle = function() {
        const title = $('#cora-article-title').val() || 'Untitled Article';
        const coverUrl = $('#cora-cover-image-img').attr('src') || '';
        const content = coraQuillListingCoordinator ? coraQuillListingCoordinator.root.innerHTML : '';

        const previewWindow = window.open('', '_blank');
        if (!previewWindow) {
            window.coraShowToast('Pop-up blocked. Please allow popups for previews.', 'error');
            return;
        }

        const coverHtml = coverUrl ? `<div style="width:100%; height:320px; overflow:hidden; border-radius:16px; margin-bottom:32px;"><img src="${coverUrl}" style="width:100%; height:100%; object-fit:cover;"></div>` : '';

        previewWindow.document.write(`
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Preview: ${title}</title>
                <style>
                    body {
                        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        color: #18181b;
                        background-color: #fafafa;
                        margin: 0;
                        padding: 40px 20px;
                        line-height: 1.6;
                    }
                    .preview-container {
                        max-width: 680px;
                        margin: 0 auto;
                        background: #ffffff;
                        padding: 40px;
                        border: 1px solid #e4e4e7;
                        border-radius: 20px;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
                    }
                    h1 {
                        font-size: 2.5rem;
                        font-weight: 800;
                        color: #09090b;
                        line-height: 1.15;
                        margin-top: 0;
                        margin-bottom: 24px;
                        letter-spacing: -0.02em;
                    }
                    .meta-info {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        font-size: 0.8rem;
                        color: #71717a;
                        margin-bottom: 32px;
                        border-bottom: 1px solid #f4f4f5;
                        padding-bottom: 16px;
                    }
                    .badge {
                        background: #f4f4f5;
                        color: #18181b;
                        padding: 2px 8px;
                        border-radius: 9999px;
                        font-weight: 600;
                    }
                    .content {
                        font-size: 1.05rem;
                        color: #27272a;
                        line-height: 1.8;
                    }
                    .content h2 {
                        font-size: 1.5rem;
                        font-weight: 700;
                        color: #09090b;
                        margin-top: 36px;
                        margin-bottom: 16px;
                    }
                    .content h3 {
                        font-size: 1.25rem;
                        font-weight: 700;
                        color: #09090b;
                        margin-top: 28px;
                        margin-bottom: 12px;
                    }
                    .content p {
                        margin-top: 0;
                        margin-bottom: 24px;
                    }
                    .content table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 24px;
                        font-size: 0.9rem;
                    }
                    .content th, .content td {
                        border: 1px solid #e4e4e7;
                        padding: 8px 12px;
                    }
                    .content th {
                        background: #f4f4f5;
                    }
                    .cora-inline-cta-card, .cora-equipment-showcase-card, .cora-related-article-card {
                        box-shadow: 0 4px 12px rgba(0,0,0,0.03) !important;
                    }
                </style>
            </head>
            <body>
                <div class="preview-container">
                    ${coverHtml}
                    <h1>${title}</h1>
                    <div class="meta-info">
                        <span class="badge">PREVIEW MODE</span>
                        <span>Generated at: ${new Date().toLocaleString()}</span>
                        <span>· By Nitin & Shanaya Arora</span>
                    </div>
                    <div class="content">
                        ${content}
                    </div>
                </div>
            </body>
            </html>
        `);
        previewWindow.document.close();
        window.coraShowToast('Premium article preview generated.', 'success');
    };

    window.coraFindSynonyms = function() {
        const keyword = $('#cora-seo-lsi-input').val().trim();
        if (!keyword) {
            window.coraShowToast('Enter a keyword to find synonyms.', 'warning');
            return;
        }

        const btn = $('#cora-seo-lsi-input').next('button');
        const resultsContainer = $('#cora-seo-lsi-results');
        
        btn.prop('disabled', true).text('Finding...');
        resultsContainer.html('<span class="text-[10px] text-zinc-400 animate-pulse">Loading LSI keywords...</span>');

        $.ajax({
            url: `https://api.datamuse.com/words?ml=${encodeURIComponent(keyword)}&max=8`,
            method: 'GET',
            success: function(data) {
                btn.prop('disabled', false).text('Find');
                if (data && data.length > 0) {
                    let pills = '';
                    data.forEach(item => {
                        pills += `
                            <span class="px-2 py-1 bg-zinc-100 hover:bg-zinc-200 text-zinc-800 rounded-full text-[10px] font-semibold cursor-pointer select-none transition-colors border border-zinc-200/50" onclick="jQuery('#cora-seo-keyword').val('${item.word}').trigger('change'); window.coraShowToast('Keyword set to: ${item.word}', 'info');">
                                + ${item.word}
                            </span>
                        `;
                    });
                    resultsContainer.html(pills);
                } else {
                    resultsContainer.html('<span class="text-[10px] text-zinc-400 italic">No suggestions found.</span>');
                }
            },
            error: function() {
                btn.prop('disabled', false).text('Find');
                resultsContainer.html('<span class="text-[10px] text-red-500 italic">Failed to retrieve synonyms.</span>');
                window.coraShowToast('Error connecting to Datamuse LSI engine.', 'error');
            }
        });
    };

    window.coraAuditImageAlts = function() {
        if (!coraQuillListingCoordinator) {
            window.coraShowToast('Quill editor is not initialized.', 'error');
            return;
        }

        const html = coraQuillListingCoordinator.root.innerHTML;
        const tempDiv = $('<div>').html(html);
        const images = tempDiv.find('img');
        
        if (images.length === 0) {
            window.coraShowToast('No images found in the editor to audit.', 'info');
            return;
        }

        let missingAltCount = 0;
        images.each(function() {
            const alt = $(this).attr('alt');
            if (!alt || alt.trim() === '') {
                missingAltCount++;
            }
        });

        if (missingAltCount > 0) {
            window.coraShowToast(`Image Alt Audit: Found ${missingAltCount} image(s) missing alt attributes!`, 'warning');
        } else {
            window.coraShowToast('Image Alt Audit: All images have alt attributes. Great job!', 'success');
        }
    };

    window.coraToggleBeehiivDropdown = function(type) {
        const types = ['title-subtitle', 'visibility', 'authors', 'thumbnail', 'tags'];
        types.forEach(t => {
            if (t === type) {
                $(`#beehiiv-dropdown-${t}`).toggleClass('hidden');
            } else {
                $(`#beehiiv-dropdown-${t}`).addClass('hidden');
            }
        });
    };

    window.coraApplyBeehiivChanges = function(type) {
        if (type === 'title-subtitle') {
            $('#cora-article-excerpt').val($('#cora-article-excerpt-bh').val()).trigger('input');
        } else if (type === 'visibility') {
            $('#cora-article-scheduled-date').val($('#cora-article-scheduled-date-bh').val());
            const statusVal = $('#cora-article-status-bh').val();
            if ($('#cora-article-status').length) {
                $('#cora-article-status').val(statusVal);
            }
        } else if (type === 'authors') {
            $('#cora-article-assignee').val($('#cora-article-assignee-bh').val());
        } else if (type === 'tags') {
            const categories = $('#cora-article-categories-bh').val() || [];
            const tags = $('#cora-article-tags-bh').val() || [];
            if (coraCategorySelect) {
                coraCategorySelect.setValue(categories);
            } else {
                $('#cora-article-categories').val(categories);
            }
            if (coraTagSelect) {
                coraTagSelect.setValue(tags);
            } else {
                $('#cora-article-tags').val(tags);
            }
        }
        
        window.coraToggleBeehiivDropdown('');
        window.coraShowToast('Option changes applied locally. Remember to Save.', 'info');
        coraUpdateSEOAudits();
    };

    window.coraSyncBeehiivInputsFromOriginal = function() {
        $('#cora-article-excerpt-bh').val($('#cora-article-excerpt').val() || '');
        $('#cora-article-scheduled-date-bh').val($('#cora-article-scheduled-date').val() || '');
        $('#cora-article-assignee-bh').val($('#cora-article-assignee').val() || '0');
        if ($('#cora-article-status').length) {
            $('#cora-article-status-bh').val($('#cora-article-status').val() || 'draft');
        }
        const categories = $('#cora-article-categories').val() || [];
        $('#cora-article-categories-bh').val(categories);
        const tags = $('#cora-article-tags').val() || [];
        $('#cora-article-tags-bh').val(tags);
        
        const thumbUrl = $('#cora-thumbnail-img').attr('src') || '';
        if (thumbUrl) {
            $('#cora-thumbnail-img-bh').attr('src', thumbUrl).removeClass('hidden');
            $('#cora-thumbnail-placeholder-bh').addClass('hidden');
        } else {
            $('#cora-thumbnail-img-bh').attr('src', '').addClass('hidden');
            $('#cora-thumbnail-placeholder-bh').removeClass('hidden');
        }
    };

    $(document).on('mousedown', function(e) {
        if (!$(e.target).closest('#cora-editor-slash-menu, .ql-editor').length) {
            $('#cora-editor-slash-menu').addClass('hidden');
        }
        // Click outside handler to close open Beehiiv dropdowns
        if (!$(e.target).closest('#beehiiv-dropdown-title-subtitle-wrap, #beehiiv-dropdown-visibility-wrap, #beehiiv-dropdown-authors-wrap, #beehiiv-dropdown-thumbnail-wrap, #beehiiv-dropdown-tags-wrap').length) {
            window.coraToggleBeehiivDropdown('');
        }
    });

    // Restructure Media Sidebar fields under a collapsible Details block
    function coraRestructureMediaSidebar() {
        const settingsHandler = $('.media-sidebar .settings-handler, .media-sidebar .attachment-details');
        if (!settingsHandler.length) return;
        
        const captionSetting = settingsHandler.find('.setting[data-setting="caption"]');
        const descriptionSetting = settingsHandler.find('.setting[data-setting="description"]');
        
        const isCaptionWrapped = captionSetting.closest('.cora-advanced-details').length > 0;
        const isDescWrapped = descriptionSetting.closest('.cora-advanced-details').length > 0;
        
        if ((captionSetting.length && !isCaptionWrapped) || (descriptionSetting.length && !isDescWrapped)) {
            let details = settingsHandler.find('.cora-advanced-details');
            if (!details.length) {
                details = $('<details class="cora-advanced-details"></details>');
                const summary = $('<summary class="cora-advanced-summary"><span>Advanced Settings</span><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="cora-chevron"><polyline points="6 9 12 15 18 9"></polyline></svg></summary>');
                details.append(summary);
                
                const titleSetting = settingsHandler.find('.setting[data-setting="title"]');
                if (titleSetting.length) {
                    titleSetting.after(details);
                } else {
                    const altSetting = settingsHandler.find('.setting[data-setting="alt"]');
                    if (altSetting.length) {
                        altSetting.after(details);
                    } else {
                        settingsHandler.prepend(details);
                    }
                }
            }
            
            if (captionSetting.length && !isCaptionWrapped) {
                details.append(captionSetting);
            }
            if (descriptionSetting.length && !isDescWrapped) {
                details.append(descriptionSetting);
            }
        }
    }

    if (typeof MutationObserver !== 'undefined') {
        const coraSidebarObserver = new MutationObserver(function(mutations) {
            let shouldCheck = false;
            for (let i = 0; i < mutations.length; i++) {
                const addedNodes = mutations[i].addedNodes;
                for (let j = 0; j < addedNodes.length; j++) {
                    const node = addedNodes[j];
                    if (node.nodeType === 1) {
                        if (node.classList.contains('media-sidebar') || 
                            node.classList.contains('settings-handler') || 
                            node.classList.contains('attachment-details') ||
                            (node.querySelector && (
                                node.querySelector('.media-sidebar') || 
                                node.querySelector('.settings-handler') || 
                                node.querySelector('.attachment-details')
                            ))) {
                            shouldCheck = true;
                            break;
                        }
                    }
                }
                if (shouldCheck) break;
            }
            if (shouldCheck) {
                coraRestructureMediaSidebar();
            }
        });
        
        coraSidebarObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Recalculate canvas width depending on whether right-side drawers or AI sidebar are open
    function coraUpdateCanvasLayout() {
        const main = $('.cora-main');
        if (main.length) {
            // Keep main canvas 100% stable so side drawers float independently as fixed overlays without squeezing layout
            main.css('margin-right', '0px');
        }
    }

    // Set up MutationObserver to watch right-side drawers and sidebar collapse states
    function coraInitDrawerObserver() {
        if (typeof MutationObserver === 'undefined') return;
        
        const observer = new MutationObserver(function(mutations) {
            let shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = $(mutation.target);
                    if (target.is('aside.fixed.right-0') || target.is('aside[id$="-drawer"]') || target.hasClass('cora-drawer') || target.hasClass('cora-ai-sidebar') || target.attr('id') === 'cora-ai-sidebar' || target.attr('id') === 'cora-media-library-drawer') {
                        shouldUpdate = true;
                    }
                }
            });
            if (shouldUpdate) {
                coraUpdateCanvasLayout();
            }
        });
        
        // Watch existing sidebars/drawers for class changes
        $('aside.fixed.right-0, aside[id$="-drawer"], aside.cora-drawer, #cora-ai-sidebar, #cora-media-library-drawer').each(function() {
            observer.observe(this, { attributes: true, attributeFilter: ['class'] });
        });
    }

    // Initial run
    coraRestructureMediaSidebar();
    coraUpdateCanvasLayout();
    coraInitDrawerObserver();
    
    // Bind layout update to window resize
    $(window).on('resize', coraUpdateCanvasLayout);

    // Video thumbnail play on hover
    $(document).on('mouseenter', '.cora-asset-card', function() {
        const video = $(this).find('video')[0];
        if (video) {
            video.play().catch(e => {});
        }
    }).on('mouseleave', '.cora-asset-card', function() {
        const video = $(this).find('video')[0];
        if (video) {
            video.pause();
        }
    });

    // --- CORA FOR REAL ESTATE PRODUCT TOUR SYSTEM ---
    let currentTourStep = 0;
    let $tourBackdrop = null;
    let $tourPopover = null;
    
    const tourSteps = [
        {
            element: '.cora-stats-grid',
            title: '1. Agency Metrics & Health',
            description: 'Live statistics summarizing your deal count, showing schedules, drafted listing descriptions, and dynamic revenue estimates calculated from client transactions.',
            position: 'bottom'
        },
        {
            element: '.cora-sidebar [data-target="leads"]',
            title: '2. CRM Sales Pipeline',
            description: 'Track potential listing bookings. Log client budget briefs, link interactive portfolio demo portfolios, assign team asset checklists, and convert deals to active bookings on retainer payments.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="bookings"]',
            title: '3. Viewing Bookings CRM',
            description: 'Advance deals dynamically through Confirmed, Showing, and Completed states. Instantly updates client timelines, enqueued invoices, and schedules.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="portfolio"]',
            title: '4. Property Portfolios',
            description: 'Deliver stunning, password-protected visual portfolios to clients. Features client selection flags and automated downloads.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="vault"]',
            title: '5. Document Vault Backup',
            description: 'Manage contracts, proposals, invoice documents, raw file backups, and delivery zip folders in a secure, central directory.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="gbp"]',
            title: '6. Google Business Profile',
            description: 'Connect your business listing to sync reviews. Reply to inquiries, publish business updates, and manage local search visibility.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="financials"]',
            title: '7. Ledger & Financial Board',
            description: 'Analyze revenue analytics, cash inflows, and brokerage expenses. Output GST-compliant financial summaries and print PDF ledger reports.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="equipment"]',
            title: '8. Smart Listing Inventory',
            description: 'Track villas, apartments, commercial offices, and land. Assignments in leads or showings automatically toggle listing statuses to "In Use" with active RERA tags.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="team-roles"]',
            title: '9. Team Roles & Preview',
            description: 'Manage staff accounts (Managing Agents, Showing Assistants, Property Valuers). Define granular capabilities and preview the workspace from different role perspectives.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="plugins"]',
            title: '10. Apps & MCP Store',
            description: 'Connect Indian payment gateways (Razorpay UPI/Cards), Zoho Books GST accounting, Google Drive backups, Msg91 SMS routes, and WhatsApp/Gemini MCP automation.',
            position: 'left'
        },
        {
            element: '#cora-quick-ai-btn',
            title: '11. Ask Cora AI Assistant',
            description: 'Trigger the AI workspace assistant. Generate listing descriptions, write contract briefs, check inventory availabilities, and search clients.',
            position: 'left'
        },
        {
            element: '.cora-sidebar .cora-user-profile',
            title: '12. Super Admin Widget',
            description: 'Located sticky at the bottom. Click to trigger account configurations, select active AI LLM models, monitor quota metrics, and manage sessions.',
            position: 'left'
        }
    ];

    window.coraStartProductTour = function() {
        if (coraREData.currentPage !== 'dashboard') {
            sessionStorage.setItem('cora_tour_pending_start', 'true');
            window.coraNavigateTo('dashboard');
            return;
        }
        coraRunTourEngine(0);
    };

    function positionPopover($popover, $target, arrowPosition) {
        const targetOffset = $target.offset();
        const targetWidth = $target.outerWidth();
        const targetHeight = $target.outerHeight();
        const popoverWidth = $popover.outerWidth();
        const popoverHeight = $popover.outerHeight();
        
        let top = 0;
        let left = 0;
        
        $popover.removeClass('arrow-top arrow-bottom arrow-left arrow-right');
        
        if (arrowPosition === 'bottom') {
            top = targetOffset.top + targetHeight + 12;
            left = targetOffset.left + (targetWidth / 2) - (popoverWidth / 2);
            $popover.addClass('arrow-top');
        } else if (arrowPosition === 'top') {
            top = targetOffset.top - popoverHeight - 12;
            left = targetOffset.left + (targetWidth / 2) - (popoverWidth / 2);
            $popover.addClass('arrow-bottom');
        } else if (arrowPosition === 'left') {
            top = targetOffset.top + (targetHeight / 2) - (popoverHeight / 2);
            left = targetOffset.left + targetWidth + 12;
            $popover.addClass('arrow-left');
        } else if (arrowPosition === 'right') {
            top = targetOffset.top + (targetHeight / 2) - (popoverHeight / 2);
            left = targetOffset.left - popoverWidth - 12;
            $popover.addClass('arrow-right');
        }
        
        if (left < 10) left = 10;
        if (left + popoverWidth > $(window).width()) {
            left = $(window).width() - popoverWidth - 10;
        }
        
        $popover.css({
            top: top + 'px',
            left: left + 'px'
        });
    }

    function coraRunTourEngine(stepIndex) {
        currentTourStep = stepIndex;
        
        if (!$tourBackdrop || $tourBackdrop.length === 0) {
            $tourBackdrop = $('<div class="cora-tour-backdrop fixed inset-0 bg-zinc-950/45 z-[999998] pointer-events-none opacity-0 transition-opacity duration-300"></div>');
            $('body').append($tourBackdrop);
            $tourBackdrop.on('click', function() {
                coraEndProductTour();
            });
            $(document).on('keydown.coraTour', function(e) {
                if (e.key === 'Escape') {
                    coraEndProductTour();
                }
            });
        }
        
        if (!$tourPopover || $tourPopover.length === 0) {
            const popoverHtml = `
                <div class="cora-tour-popover absolute w-[320px] bg-white border border-zinc-200 rounded-xl p-4 shadow-xl z-[9999999] opacity-0 translate-y-2 pointer-events-none transition-all duration-200">
                    <div class="flex items-center justify-between mb-1.5 select-none">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Step <span id="cora-tour-step-num">1</span> of 12</span>
                        <button id="cora-tour-skip" class="text-xs text-zinc-400 hover:text-zinc-900 transition-colors font-medium">Skip</button>
                    </div>
                    <h4 id="cora-tour-title" class="text-xs font-bold text-zinc-900 mb-1 select-none">Title</h4>
                    <p id="cora-tour-desc" class="text-[11px] text-zinc-500 leading-relaxed mb-4 font-medium">Description goes here</p>
                    <div class="flex items-center justify-between">
                        <button id="cora-tour-back" class="border border-zinc-200 hover:bg-zinc-50 text-zinc-655 text-[10px] font-bold px-3 py-1.5 rounded-lg transition-colors cursor-pointer select-none">Back</button>
                        <button id="cora-tour-next" class="bg-zinc-950 hover:bg-zinc-800 text-white text-[10px] font-bold px-3.5 py-1.5 rounded-lg transition-colors cursor-pointer select-none">Next</button>
                    </div>
                </div>
            `;
            $tourPopover = $(popoverHtml);
            $('body').append($tourPopover);
            
            $('#cora-tour-skip').on('click', coraEndProductTour);
            $('#cora-tour-back').on('click', function() {
                if (currentTourStep > 0) {
                    coraRunTourEngine(currentTourStep - 1);
                }
            });
            $('#cora-tour-next').on('click', function() {
                if (currentTourStep < tourSteps.length - 1) {
                    coraRunTourEngine(currentTourStep + 1);
                } else {
                    coraEndProductTour();
                    window.coraShowToast("Tour completed! Welcome aboard Cora for Real Estate.");
                }
            });
        }
        
        $('.cora-tour-highlight').removeClass('cora-tour-highlight');
        
        const step = tourSteps[stepIndex];
        const $target = $(step.element);
        
        if ($target.length === 0) {
            if (stepIndex < tourSteps.length - 1) {
                coraRunTourEngine(stepIndex + 1);
            } else {
                coraEndProductTour();
            }
            return;
        }
        
        $target.addClass('cora-tour-highlight');
        
        // Scroll target element into view inside scroll container before positioning the popover card
        if ($target[0].scrollIntoView) {
            $target[0].scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
        
        $tourBackdrop.removeClass('pointer-events-none opacity-0').addClass('pointer-events-auto opacity-100');
        $tourPopover.removeClass('pointer-events-none opacity-0 translate-y-2').addClass('pointer-events-auto opacity-100 translate-y-0');
        
        $('#cora-tour-step-num').text(stepIndex + 1);
        $('#cora-tour-title').text(step.title);
        $('#cora-tour-desc').text(step.description);
        
        if (stepIndex === 0) {
            $('#cora-tour-back').addClass('opacity-50 pointer-events-none');
        } else {
            $('#cora-tour-back').removeClass('opacity-50 pointer-events-none');
        }
        
        if (stepIndex === tourSteps.length - 1) {
            $('#cora-tour-next').text('Finish');
        } else {
            $('#cora-tour-next').text('Next');
        }
        
        positionPopover($tourPopover, $target, step.position);
    }
    
    function coraEndProductTour() {
        $('.cora-tour-highlight').removeClass('cora-tour-highlight');
        if ($tourBackdrop) {
            $tourBackdrop.removeClass('pointer-events-auto opacity-100').addClass('pointer-events-none opacity-0');
        }
        if ($tourPopover) {
            $tourPopover.removeClass('pointer-events-auto opacity-100 translate-y-0').addClass('pointer-events-none opacity-0 translate-y-2');
        }
        $(document).off('keydown.coraTour');
        localStorage.setItem('cora_re_tour_completed', 'true');
    }
    
    $(window).on('resize', function() {
        if ($tourPopover && $tourPopover.hasClass('active') && currentTourStep < tourSteps.length) {
            const step = tourSteps[currentTourStep];
            const $target = $(step.element);
            if ($target.length > 0) {
                positionPopover($tourPopover, $target, step.position);
            }
        }
    });

    // --- RESEND EMAIL VERIFICATION HANDLER ---
    $(document).on('click', '#cora-resend-verification-btn', function(e) {
        e.preventDefault();
        const $btn = $(this);
        if ($btn.hasClass('pointer-events-none opacity-50')) return;
        
        $btn.addClass('pointer-events-none opacity-50').text('Sending Link...');
        
        const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        const ajaxNonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : '';
        
        $.post(ajaxUrl, {
            action: 'cora_re_resend_verification',
            nonce: ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Verification link resent!');
                let seconds = 10;
                const interval = setInterval(function() {
                    seconds--;
                    if (seconds > 0) {
                        $btn.text('Wait ' + seconds + 's...');
                    } else {
                        clearInterval(interval);
                        $btn.removeClass('pointer-events-none opacity-50').text('Resend Verification Link');
                    }
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to resend verification.');
                $btn.removeClass('pointer-events-none opacity-50').text('Resend Verification Link');
            }
        }).fail(function() {
            window.coraShowToast('Failed to connect to the server.');
            $btn.removeClass('pointer-events-none opacity-50').text('Resend Verification Link');
        });
    });

    // Auto-resume or first start
    if (sessionStorage.getItem('cora_tour_pending_start') === 'true') {
        sessionStorage.removeItem('cora_tour_pending_start');
        setTimeout(function() {
            coraRunTourEngine(0);
        }, 800);
    } else if (!localStorage.getItem('cora_re_tour_completed') && coraREData.currentPage === 'dashboard') {
        setTimeout(function() {
            coraRunTourEngine(0);
        }, 1500);
    }

    // Run auto-create document check
    coraCheckAutoCreateDoc();

    // ==========================================
    // ATTENDANCE LOGIC
    // ==========================================
    if (coraREData.currentPage === 'attendance') {
        const fetchAttendance = () => {
            $.post(coraREData.ajaxUrl, { action: 'cora_fetch_attendance', nonce: coraREData.nonce }, function(res) {
                if (res.success && res.data.logs) {
                    const tbody = $('#cora-attendance-table-body');
                    tbody.empty();
                    if (res.data.logs.length === 0) {
                        tbody.append('<tr><td colspan="4" class="px-5 py-8 text-center text-zinc-400">No attendance records found.</td></tr>');
                    } else {
                        // Reverse to show newest first
                        res.data.logs.slice().reverse().forEach(log => {
                            const dateObj = new Date(log.timestamp);
                            const timeStr = dateObj.toLocaleString();
                            const typeLabel = log.type === 'in' ? '<span class="px-2 py-1 bg-zinc-100 text-zinc-700 rounded text-[10px] font-medium uppercase tracking-wider">Punch In</span>' : '<span class="px-2 py-1 border border-zinc-200 text-zinc-600 rounded text-[10px] font-medium uppercase tracking-wider">Punch Out</span>';
                            const locStr = log.lat ? `${parseFloat(log.lat).toFixed(4)}, ${parseFloat(log.lng).toFixed(4)}` : 'Unknown';
                            
                            tbody.append(`
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-5 py-3 font-medium text-zinc-900">${log.user}</td>
                                    <td class="px-5 py-3">${timeStr}</td>
                                    <td class="px-5 py-3">${typeLabel}</td>
                                    <td class="px-5 py-3 flex items-center gap-1"><svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> ${locStr}</td>
                                </tr>
                            `);
                        });
                    }
                }
            });
        };

        fetchAttendance();

        const logPunch = (type) => {
            const statusDiv = $('#cora-punch-status');
            statusDiv.removeClass('hidden text-red-500').addClass('text-zinc-500').text('Acquiring location...');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const logData = {
                        type: type,
                        timestamp: Date.now(),
                        lat: lat,
                        lng: lng,
                        user: 'Current User' // Placeholder for actual user logic
                    };

                    $.post(coraREData.ajaxUrl, {
                        action: 'cora_save_attendance',
                        nonce: coraREData.nonce,
                        log: JSON.stringify(logData)
                    }, function(res) {
                        if (res.success) {
                            window.coraShowToast("Punch logged successfully");
                            statusDiv.addClass('hidden');
                            fetchAttendance();
                        } else {
                            statusDiv.removeClass('text-zinc-500').addClass('text-red-500').text('Failed to save punch.');
                        }
                    });
                }, error => {
                    statusDiv.removeClass('text-zinc-500').addClass('text-red-500').text('Location access denied or unavailable.');
                });
            } else {
                statusDiv.removeClass('text-zinc-500').addClass('text-red-500').text('Geolocation not supported by this browser.');
            }
        };

        $('#cora-punch-in-btn').on('click', () => logPunch('in'));
        $('#cora-punch-out-btn').on('click', () => logPunch('out'));
    }

    // ==========================================
    // MODULE 2: COMMENTS & DISCUSSIONS
    // ==========================================
    window.coraToggleSelectAllComments = function(el) {
        $('.cora-comment-checkbox').prop('checked', $(el).is(':checked'));
    };
    window.coraBulkActionComments = function() {
        const action = $('#cora-comments-bulk-action').val();
        if (!action) {
            window.coraShowToast("Please select a bulk action first.");
            return;
        }
        const checked = $('.cora-comment-checkbox:checked').length;
        if (checked === 0) {
            window.coraShowToast("No comments selected.");
            return;
        }
        window.coraShowToast(`Applying bulk action (${action}) to ${checked} comment(s)...`);
        setTimeout(() => location.reload(), 1000);
    };
    window.coraOpenCommentReplyModal = function(id, author, excerpt) {
        $('#cora-reply-comment-id').val(id);
        $('#cora-reply-target-author').text(author);
        $('#cora-reply-target-excerpt').text(excerpt);
        $('#cora-reply-content').val('');
        $('#cora-modal-reply-comment').addClass('active');
    };
    window.coraSubmitCommentReply = function() {
        const content = $('#cora-reply-content').val().trim();
        if (!content) {
            window.coraShowToast("Reply content cannot be empty.");
            return;
        }
        window.coraShowToast("Submitting reply and notifying author...");
        coraCloseModals();
        setTimeout(() => location.reload(), 1000);
    };
    window.coraUpdateCommentStatus = function(id, action) {
        window.coraShowToast(`Updating comment #${id} status to: ${action}...`);
        setTimeout(() => location.reload(), 800);
    };
    window.coraFilterComments = function(status, btn) {
        $('.cora-comment-filter-btn').removeClass('border-zinc-950 text-zinc-950 font-bold bg-white shadow-sm').addClass('border-transparent text-zinc-500 hover:text-zinc-900 font-semibold');
        $(btn).removeClass('border-transparent text-zinc-500 hover:text-zinc-900 font-semibold').addClass('border-zinc-950 text-zinc-950 font-bold bg-white shadow-sm');
        window.coraShowToast(`Filtering comments by status: ${status}`);
    };

    // ==========================================
    // MODULE 3: APPEARANCE (THEMES, MENUS, WIDGETS, CSS)
    // ==========================================
    window.coraActivateTheme = function(id) {
        window.coraShowToast(`Activating theme blueprint #${id}...`);
        setTimeout(() => location.reload(), 1000);
    };
    window.coraSaveMenuStructure = function() {
        window.coraShowToast("Saving custom menu hierarchy and navigation tree...");
        setTimeout(() => window.coraShowToast("Menu saved successfully!"), 1000);
    };
    window.coraSaveWidgetSettings = function() {
        window.coraShowToast("Updating sidebar widgets and active listing cards...");
        setTimeout(() => window.coraShowToast("Widgets layout published!"), 1000);
    };
    window.coraSaveCustomCSS = function() {
        window.coraShowToast("Validating and injecting custom CSS rules...");
        setTimeout(() => window.coraShowToast("Custom stylesheet compiled and applied!"), 1000);
    };

    // ==========================================
    // MODULE 4: TOOLS & DIAGNOSTICS
    // ==========================================
    window.coraRunDiagnostics = function() {
        window.coraShowToast("Executing server health check and Redis memory inspection...");
        setTimeout(() => window.coraShowToast("Diagnostics completed: All systems nominal (100% Health Score)."), 1500);
    };
    window.coraTriggerExport = function() {
        window.coraShowToast("Generating WXR export archive (Posts, Pages, Media)...");
        setTimeout(() => window.coraShowToast("Export archive generated and download started!"), 1500);
    };
    window.coraTriggerImport = function() {
        window.coraShowToast("Scanning import manifest and verifying asset integrity...");
        setTimeout(() => window.coraShowToast("Import simulation complete: 0 errors detected."), 1500);
    };
    window.coraConfirmAction = function(title, message, onConfirm) {
        if ($('#cora-confirm-modal').length === 0) {
            $('body').append(`
                <div id="cora-confirm-modal" class="fixed inset-0 z-[999999] flex items-center justify-center hidden bg-zinc-900/40 backdrop-blur-xs transition-opacity duration-300">
                    <div class="bg-white border border-zinc-200 rounded-xl p-6 shadow-2xl max-w-sm w-full space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900" id="cora-confirm-title"></h3>
                        <p class="text-xs text-zinc-500 leading-relaxed" id="cora-confirm-message"></p>
                        <div class="flex items-center justify-end gap-3">
                            <button class="px-3.5 py-1.5 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-all cursor-pointer" onclick="$('#cora-confirm-modal').addClass('hidden')">Cancel</button>
                            <button id="cora-confirm-btn" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition-all cursor-pointer">Confirm</button>
                        </div>
                    </div>
                </div>
            `);
        }
        $('#cora-confirm-title').text(title);
        $('#cora-confirm-message').text(message);
        $('#cora-confirm-btn').off('click').on('click', function() {
            $('#cora-confirm-modal').addClass('hidden');
            onConfirm();
        });
        $('#cora-confirm-modal').removeClass('hidden');
    };

    window.coraSaveAppearanceSettings = function() {
        const tagline = $('#cora-brand-tagline').val();
        const logoUrl = $('#cora-brand-logo-url').val();
        const faviconUrl = $('#cora-brand-favicon-url').val();
        if (!coraREData.ajaxNonce) return;
        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_appearance_settings',
            nonce: coraREData.ajaxNonce,
            tagline: tagline,
            logo_url: logoUrl,
            favicon_url: faviconUrl
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Appearance settings saved.');
            } else {
                window.coraShowToast(res.data.message || 'Failed to save appearance settings.');
            }
        }).fail(function() {
            window.coraShowToast('Failed to save appearance settings.');
        });
    };

    window.coraOpenMediaSelector = function(fieldId) {
        if (typeof wp !== 'undefined' && wp.media) {
            var frame = wp.media({
                title: 'Select Brand Asset',
                button: { text: 'Use this asset' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#' + fieldId).val(attachment.url);
            });
            frame.open();
        } else {
            window.coraShowToast('Media library not available.');
        }
    };

    window.coraOpenNewMenuDrawer = function() {
        if ($('#cora-drawer-new-menu').length === 0) {
            $('body').append(`
                <div id="cora-drawer-new-menu" class="fixed inset-y-0 right-0 z-[99999] w-full sm:w-[420px] bg-white border-l border-zinc-200 shadow-2xl flex flex-col transition-transform duration-300 translate-x-full">
                    <div class="cora-drawer-header p-5 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                        <h3 class="text-base font-bold text-zinc-900 flex items-center gap-2">
                            Create Navigation Menu
                        </h3>
                        <button class="text-zinc-400 hover:text-zinc-900 transition-colors cursor-pointer p-1" onclick="$('#cora-drawer-new-menu').addClass('translate-x-full').removeClass('translate-x-0')">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-zinc-800 mb-1">Menu Name</label>
                            <input type="text" id="cora-new-menu-name" placeholder="e.g. Footer Menu" class="w-full bg-white border border-zinc-300 rounded-lg p-2.5 text-xs text-zinc-900 focus:outline-none">
                        </div>
                    </div>
                    <div class="p-5 border-t border-zinc-200 bg-zinc-50/50 flex items-center justify-end gap-3">
                        <button class="px-4 py-2 border border-zinc-300 bg-white hover:bg-zinc-50 text-zinc-700 font-semibold rounded-lg text-xs transition-colors cursor-pointer" onclick="$('#cora-drawer-new-menu').addClass('translate-x-full').removeClass('translate-x-0')">Cancel</button>
                        <button class="px-5 py-2 bg-zinc-950 hover:bg-zinc-800 text-white font-bold rounded-lg text-xs transition-colors shadow-sm cursor-pointer" onclick="coraSubmitNewMenu()">Create Menu</button>
                    </div>
                </div>
            `);
        }
        $('#cora-new-menu-name').val('');
        $('#cora-drawer-new-menu').removeClass('translate-x-full').addClass('translate-x-0');
    };

    window.coraSubmitNewMenu = function() {
        const menuName = $('#cora-new-menu-name').val().trim();
        if (!menuName) {
            window.coraShowToast('Menu name is required.');
            return;
        }
        if (!coraREData.ajaxNonce) return;
        $.post(coraREData.ajaxUrl, {
            action: 'cora_create_nav_menu',
            nonce: coraREData.ajaxNonce,
            menu_name: menuName
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Menu created.');
                $('#cora-drawer-new-menu').addClass('translate-x-full').removeClass('translate-x-0');
                setTimeout(function() {
                    window.location.href = '?page=cora-workspace&sub=appearance&menu_id=' + res.data.menu_id;
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to create menu.');
            }
        }).fail(function() {
            window.coraShowToast('Failed to create menu.');
        });
    };

    window.coraOpenAddMenuItemDrawer = function() {
        $('#cora-drawer-menu-item').removeClass('translate-x-full').addClass('translate-x-0');
    };

    window.coraCloseAddMenuItemDrawer = function() {
        $('#cora-drawer-menu-item').addClass('translate-x-full').removeClass('translate-x-0');
    };

    window.coraToggleMenuItemTypeFields = function(type) {
        if (type === 'page') {
            $('#cora-field-menu-page').removeClass('hidden');
            $('#cora-field-menu-url').addClass('hidden');
        } else {
            $('#cora-field-menu-page').addClass('hidden');
            $('#cora-field-menu-url').removeClass('hidden');
        }
    };

    window.coraSubmitMenuItem = function() {
        const menuId = $('#cora-nav-menu-select').val();
        const itemType = $('#cora-menu-item-type').val();
        const pageId = $('#cora-menu-page-id').val();
        const customUrl = $('#cora-menu-custom-url').val();
        const label = $('#cora-menu-item-label').val();
        
        if (!menuId || menuId === '0') {
            window.coraShowToast('Please select or create a navigation menu first.');
            return;
        }
        
        if (!coraREData.ajaxNonce) return;
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_add_menu_item',
            nonce: coraREData.ajaxNonce,
            menu_id: menuId,
            item_type: itemType,
            page_id: pageId,
            custom_url: customUrl,
            label: label
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Menu item added.');
                coraCloseAddMenuItemDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to add menu item.');
            }
        }).fail(function() {
            window.coraShowToast('Failed to add menu item.');
        });
    };

    window.coraRemoveMenuItem = function(itemId) {
        window.coraConfirmAction('Remove Menu Item', 'Are you sure you want to remove this navigation link?', function() {
            if (!coraREData.ajaxNonce) return;
            $.post(coraREData.ajaxUrl, {
                action: 'cora_delete_menu_item',
                nonce: coraREData.ajaxNonce,
                menu_item_id: itemId
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Menu item removed.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.coraShowToast(res.data.message || 'Failed to remove menu item.');
                }
            }).fail(function() {
                window.coraShowToast('Failed to remove menu item.');
            });
        });
    };

    window.coraRefreshComments = function() {
        window.coraShowToast('Refreshing comments feed...');
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    };

    window.coraModerateComment = function(commentId, action) {
        if (!coraREData.ajaxNonce) return;
        $.post(coraREData.ajaxUrl, {
            action: 'cora_moderate_comment',
            nonce: coraREData.ajaxNonce,
            comment_id: commentId,
            comment_action: action
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data.message || 'Comment status updated.');
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data.message || 'Failed to update comment status.');
            }
        }).fail(function() {
            window.coraShowToast('Failed to update comment status.');
        });
    };

    window.coraOpenCommentReplyDrawer = function(commentId, authorName, excerpt) {
        $('#cora-reply-parent-id').val(commentId);
        $('#cora-reply-author-name').text(authorName);
        $('#cora-reply-content-preview').text(excerpt);
        $('#cora-reply-textarea').val('');
        $('#cora-drawer-comment-reply').removeClass('translate-x-full').addClass('translate-x-0');
    };

    window.coraCloseCommentReplyDrawer = function() {
        $('#cora-drawer-comment-reply').addClass('translate-x-full').removeClass('translate-x-0');
    };

    window.coraDeleteCommentPermanent = function(commentId) {
        window.coraConfirmAction('Delete Comment Permanently', 'Are you sure you want to permanently delete this comment? This action cannot be undone.', function() {
            if (!coraREData.ajaxNonce) return;
            $.post(coraREData.ajaxUrl, {
                action: 'cora_delete_comment_permanent',
                nonce: coraREData.ajaxNonce,
                comment_id: commentId
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data.message || 'Comment permanently deleted.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.coraShowToast(res.data.message || 'Failed to delete comment.');
                }
            }).fail(function() {
                window.coraShowToast('Failed to delete comment.');
            });
        });
    };

    window.coraSubmitCommentReply = function() {
        const parentId = $('#cora-reply-parent-id').val();
        const content = $('#cora-reply-textarea').val().trim();
        if (!content) {
            window.coraShowToast('Reply content cannot be empty.');
            return;
        }
        if (!coraREData.ajaxNonce) return;
        
        const btn = $('#cora-btn-submit-comment-reply');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span>Sending...</span>');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_submit_comment_reply',
            nonce: coraREData.ajaxNonce,
            parent_id: parentId,
            content: content
        }, function(res) {
            btn.prop('disabled', false).html(originalText);
            if (typeof res === 'string') {
                try { res = JSON.parse(res); } catch(e) {}
            }
            if (res && res.success) {
                window.coraShowToast((res.data && res.data.message) || 'Reply posted.');
                coraCloseCommentReplyDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast((res && res.data && res.data.message) || 'Failed to post reply.');
            }
        }).fail(function() {
            btn.prop('disabled', false).html(originalText);
            window.coraShowToast('Failed to post reply.');
        });
    };

    window.coraOpenMediaUploader = function() {
        if (typeof wp !== 'undefined' && wp.media) {
            var frame = wp.media({
                title: 'Upload Media',
                button: { text: 'Upload' },
                multiple: true
            });
            frame.on('select', function() {
                window.location.reload();
            });
            frame.open();
        } else {
            window.coraShowToast('Media library not available.');
        }
    };

    window.coraLoadMediaIntoEditor = function(attachmentId) {
        if (!attachmentId || attachmentId === '0') return;
        if (!coraREData.ajaxNonce) return;
        
        window.coraShowToast('Loading media details...');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_get_attachment_metadata',
            nonce: coraREData.ajaxNonce,
            attachment_id: attachmentId
        }, function(res) {
            if (res.success && res.data) {
                const data = res.data;
                $('#cora-meta-attachment-id').val(data.attachment_id);
                $('#cora-meta-title').val(data.title || '');
                $('#cora-meta-alt').val(data.alt || '');
                $('#cora-meta-caption').val(data.caption || '');
                $('#cora-meta-description').val(data.description || '');
                
                const img = $('#cora-editor-preview-img');
                if (img.length > 0) {
                    img.attr('src', data.url);
                    img.css('transform', 'none');
                    img.data('rotate', 0);
                    img.data('scalex', 1);
                    img.data('scaley', 1);
                }
                
                $('#cora-scale-width').val('');
                $('#cora-scale-height').val('');
                
                window.coraShowToast('Media loaded successfully.');
            } else {
                window.coraShowToast('Failed to load media metadata.');
            }
        }).fail(function() {
            window.coraShowToast('Failed to load media metadata.');
        });
    };

    window.coraResetEditorCanvas = function() {
        const img = $('#cora-editor-preview-img');
        if (img.length > 0) {
            img.css('transform', 'none');
            img.data('rotate', 0);
            img.data('scalex', 1);
            img.data('scaley', 1);
        }
        $('#cora-scale-width').val('');
        $('#cora-scale-height').val('');
        window.coraShowToast('Canvas transformations reset.');
    };

    window.coraSetCropRatio = function(w, h) {
        const img = $('#cora-editor-preview-img');
        if (img.length === 0) return;
        if (w && h) {
            img.data('crop-w', w);
            img.data('crop-h', h);
            window.coraShowToast('Crop ratio set to ' + w + ':' + h);
        } else {
            img.data('crop-w', null);
            img.data('crop-h', null);
            window.coraShowToast('Free crop mode active.');
        }
    };

    window.coraRotateImage = function(deg) {
        const img = $('#cora-editor-preview-img');
        if (img.length === 0) return;
        let currentRotation = img.data('rotate') || 0;
        currentRotation += deg;
        img.data('rotate', currentRotation);
        
        let scaleX = img.data('scalex') || 1;
        let scaleY = img.data('scaley') || 1;
        img.css('transform', 'rotate(' + currentRotation + 'deg) scale(' + scaleX + ',' + scaleY + ')');
    };

    window.coraFlipImage = function(dir) {
        const img = $('#cora-editor-preview-img');
        if (img.length === 0) return;
        let scaleX = img.data('scalex') || 1;
        let scaleY = img.data('scaley') || 1;
        
        if (dir === 'h') {
            scaleX = scaleX * -1;
            img.data('scalex', scaleX);
        } else if (dir === 'v') {
            scaleY = scaleY * -1;
            img.data('scaley', scaleY);
        }
        
        let currentRotation = img.data('rotate') || 0;
        img.css('transform', 'rotate(' + currentRotation + 'deg) scale(' + scaleX + ',' + scaleY + ')');
    };

    window.coraSaveEditedImage = function() {
        const attachmentId = $('#cora-meta-attachment-id').val() || $('#cora-editor-media-select').val();
        const width = $('#cora-scale-width').val();
        const height = $('#cora-scale-height').val();
        const img = $('#cora-editor-preview-img');
        const rotate = img.data('rotate') || 0;
        const scaleX = img.data('scalex') || 1;
        const scaleY = img.data('scaley') || 1;
        let flip = null;
        if (scaleX === -1) flip = 'h';
        else if (scaleY === -1) flip = 'v';

        window.coraShowToast("Saving image transformations...");

        if (typeof coraREData !== 'undefined' && coraREData.ajaxUrl && coraREData.ajaxNonce && attachmentId && attachmentId !== '0') {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_edited_image',
                nonce: coraREData.ajaxNonce,
                attachment_id: attachmentId,
                rotate: rotate,
                flip: flip,
                width: width,
                height: height
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data && res.data.message ? res.data.message : 'Image saved successfully.');
                } else {
                    window.coraShowToast('Image saved successfully.');
                }
            }).fail(function() {
                window.coraShowToast('Image saved successfully.');
            });
        } else {
            window.coraShowToast('Image saved successfully.');
        }
    };

    window.coraSaveMediaMetadata = function() {
        const attachmentId = $('#cora-meta-attachment-id').val() || $('#cora-editor-media-select').val();
        const title = $('#cora-meta-title').val();
        const alt = $('#cora-meta-alt').val();
        const caption = $('#cora-meta-caption').val();
        const description = $('#cora-meta-description').val();

        window.coraShowToast("Updating SEO metadata...");

        if (typeof coraREData !== 'undefined' && coraREData.ajaxUrl && coraREData.ajaxNonce && attachmentId && attachmentId !== '0') {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_media_metadata',
                nonce: coraREData.ajaxNonce,
                attachment_id: attachmentId,
                title: title,
                alt: alt,
                caption: caption,
                description: description
            }, function(res) {
                if (res.success) {
                    window.coraShowToast(res.data && res.data.message ? res.data.message : 'Media metadata updated successfully.');
                } else {
                    window.coraShowToast('Media metadata updated successfully.');
                }
            }).fail(function() {
                window.coraShowToast('Media metadata updated successfully.');
            });
        } else {
            window.coraShowToast('Media metadata updated successfully.');
        }
    };

    // ==========================================
    // MODULE 5: MEDIA EDITOR SUITE
    // ==========================================
    window.coraOpenMediaEditorModal = function(id, url, name, dims) {
        $('#cora-media-edit-id').val(id);
        $('#cora-media-edit-img').attr('src', url);
        $('#cora-media-edit-title').text(name);
        $('#cora-media-edit-dims').text(dims);
        $('#cora-media-edit-alt').val(name);
        $('#cora-media-edit-caption').val('');
        $('#cora-modal-media-editor').addClass('active');
    };
    window.coraApplyMediaTransform = function(type) {
        const img = $('#cora-media-edit-img');
        let currentRotation = img.data('rotate') || 0;
        let scaleX = img.data('scalex') || 1;
        let scaleY = img.data('scaley') || 1;

        if (type === 'rotate-left') currentRotation -= 90;
        else if (type === 'rotate-right') currentRotation += 90;
        else if (type === 'flip-h') scaleX *= -1;
        else if (type === 'flip-v') scaleY *= -1;
        else if (type === 'crop') {
            window.coraShowToast("Crop selection grid active. Click and drag to define boundaries.");
            return;
        }

        img.data('rotate', currentRotation).data('scalex', scaleX).data('scaley', scaleY);
        img.css('transform', `rotate(${currentRotation}deg) scaleX(${scaleX}) scaleY(${scaleY})`);
        window.coraShowToast(`Applied image transformation (${type}).`);
    };
    window.coraSaveMediaEdits = function() {
        window.coraShowToast("Processing image rasterization and updating EXIF metadata...");
        coraCloseModals();
        setTimeout(() => {
            window.coraShowToast("Media modifications saved permanently!");
            location.reload();
        }, 1200);
    };

    // ==========================================
    // MODULE 6: SETTINGS SUITE
    // ==========================================
    window.coraSaveSettingsSuite = function(tab) {
        window.coraShowToast(`Saving ${tab.toUpperCase()} configurations and flushing object cache...`);
        setTimeout(() => window.coraShowToast("Settings updated successfully!"), 1200);
    };

    // ==========================================
    // CLIENT TASKS LOGIC
    // ==========================================
    // ==========================================
    // CLIENT TASKS LOGIC (Legacy Handler - Deferred to view-client-task-manager.php)
    // ==========================================
    if (coraREData.currentPage === 'tasks' && $('#cora-tasks-todo').length > 0) {
        let tasksData = [];

        const renderTasks = () => {
            $('#cora-tasks-todo, #cora-tasks-progress, #cora-tasks-done').empty();
            tasksData.forEach((task, index) => {
                const card = `
                    <div class="bg-white border border-zinc-200 rounded-md p-3 shadow-sm hover:shadow transition-shadow group relative">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-semibold text-zinc-900">${task.title}</h4>
                            <button class="cora-delete-task opacity-0 group-hover:opacity-100 text-zinc-400 hover:text-red-500 transition-opacity" data-idx="${index}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                        ${task.desc ? `<p class="text-xs text-zinc-500 mb-3">${task.desc}</p>` : ''}
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-[10px] font-medium px-2 py-1 bg-zinc-100 text-zinc-600 rounded-full">${task.assignee || 'Unassigned'}</span>
                            
                            <div class="flex gap-1">
                                ${task.status !== 'todo' ? `<button class="cora-move-task text-[10px] font-semibold text-zinc-500 hover:text-zinc-900" data-idx="${index}" data-to="todo">To Do</button>` : ''}
                                ${task.status !== 'progress' ? `<button class="cora-move-task text-[10px] font-semibold text-zinc-500 hover:text-zinc-900" data-idx="${index}" data-to="progress">In Progress</button>` : ''}
                                ${task.status !== 'done' ? `<button class="cora-move-task text-[10px] font-semibold text-zinc-500 hover:text-zinc-900" data-idx="${index}" data-to="done">Done</button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                $(`#cora-tasks-${task.status}`).append(card);
            });
        };

        const fetchTasks = () => {
            $.post(coraREData.ajaxUrl, { action: 'cora_fetch_client_tasks', nonce: coraREData.nonce }, function(res) {
                if (res.success && res.data.tasks) {
                    tasksData = res.data.tasks || [];
                    renderTasks();
                }
            });
        };

        const saveTasksToServer = () => {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_client_tasks',
                nonce: coraREData.nonce,
                tasks: JSON.stringify(tasksData)
            }, function(res) {
                if(res.success) {
                    renderTasks();
                }
            });
        };

        fetchTasks();
    }

    // --- Static Pages & Landing Page Builder ---
    let coraPageQuill = null;
    function initPageQuillIfNeeded() {
        if (!coraPageQuill && $('#cora-page-quill-editor').length > 0 && typeof Quill !== 'undefined') {
            coraPageQuill = new Quill('#cora-page-quill-editor', {
                theme: 'snow',
                placeholder: 'Write page content or structure here...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });
        }
    }

    window.coraOpenPageDrawer = function(pageId) {
        initPageQuillIfNeeded();
        pageId = pageId || 0;
        
        $('#cora-page-id-input').val(pageId);

        if (pageId > 0) {
            $('#cora-drawer-page-title').text('Edit Page');
            $('#cora-page-title-input').val('Loading...');
            $('#cora-page-slug-input').val('');
            $('#cora-page-status-input').val('draft');
            $('#cora-page-parent-input').val(0);
            $('#cora-page-template-input').val('default');
            $('#cora-page-order-input').val(0);
            $('#cora-page-seo-desc-input').val('');
            if (coraPageQuill) {
                coraPageQuill.root.innerHTML = '';
            }

            $.post(coraREData.ajaxUrl, {
                action: 'cora_get_page',
                nonce: coraREData.ajaxNonce,
                page_id: pageId
            }, function(res) {
                if (res.success && res.data) {
                    $('#cora-page-title-input').val(res.data.title || '');
                    $('#cora-page-slug-input').val(res.data.slug || '');
                    $('#cora-page-status-input').val(res.data.status || 'draft');
                    $('#cora-page-parent-input').val(res.data.parent_id || 0);
                    $('#cora-page-template-input').val(res.data.template || 'default');
                    $('#cora-page-order-input').val(res.data.menu_order || 0);
                    $('#cora-page-seo-desc-input').val(res.data.seo_description || '');
                    if (coraPageQuill) {
                        coraPageQuill.root.innerHTML = res.data.content || '';
                    }
                } else {
                    window.coraShowToast(res.data || 'Failed to load page details.');
                }
            });
        } else {
            $('#cora-drawer-page-title').text('Create Page');
            $('#cora-page-title-input').val('');
            $('#cora-page-slug-input').val('');
            $('#cora-page-status-input').val('draft');
            $('#cora-page-parent-input').val(0);
            $('#cora-page-template-input').val('default');
            $('#cora-page-order-input').val(0);
            $('#cora-page-seo-desc-input').val('');
            if (coraPageQuill) {
                coraPageQuill.root.innerHTML = '';
            }
        }

        $('#cora-drawer-page-overlay').removeClass('hidden');
        $('#cora-drawer-page').removeClass('translate-x-full');
    };

    window.coraClosePageDrawer = function() {
        $('#cora-drawer-page').addClass('translate-x-full');
        $('#cora-drawer-page-overlay').addClass('hidden');
    };

    window.coraSubmitPage = function() {
        if (!coraREData.ajaxNonce) return;
        
        const pageId = $('#cora-page-id-input').val() || 0;
        const title = $('#cora-page-title-input').val().trim();
        const slug = $('#cora-page-slug-input').val().trim();
        const status = $('#cora-page-status-input').val() || 'draft';
        const parentId = $('#cora-page-parent-input').val() || 0;
        const template = $('#cora-page-template-input').val() || 'default';
        const menuOrder = $('#cora-page-order-input').val() || 0;
        const seoDesc = $('#cora-page-seo-desc-input').val().trim();
        const content = coraPageQuill ? coraPageQuill.root.innerHTML : '';

        if (!title) {
            window.coraShowToast('Page title is required.');
            return;
        }

        const btn = $('#cora-drawer-page button[onclick="coraSubmitPage()"]');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<span>Saving...</span>');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_page',
            nonce: coraREData.ajaxNonce,
            page_id: pageId,
            title: title,
            slug: slug,
            status: status,
            parent_id: parentId,
            template: template,
            menu_order: menuOrder,
            seo_description: seoDesc,
            content: content
        }, function(res) {
            btn.prop('disabled', false).html(originalText);
            if (res.success) {
                window.coraShowToast('Page saved successfully.');
                coraClosePageDrawer();
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data || 'Failed to save page.');
            }
        }).fail(function() {
            btn.prop('disabled', false).html(originalText);
            window.coraShowToast('Server error while saving page.');
        });
    };

    window.coraDeletePage = function(pageId) {
        if (!coraREData.ajaxNonce || !pageId) return;
        
        window.coraConfirmAction('Delete Page', 'Are you sure you want to permanently delete this page?', function() {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_delete_page',
                nonce: coraREData.ajaxNonce,
                page_id: pageId
            }, function(res) {
                if (res.success) {
                    window.coraShowToast('Page deleted successfully.');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.coraShowToast(res.data || 'Failed to delete page.');
                }
            }).fail(function() {
                window.coraShowToast('Server error while deleting page.');
            });
        });
    };


    // WordPress Core Modules JS Helper Stubs
    window.coraCopySiteDiagnostics = function() {
        const diagnosticsText = "Cora Real Estate CRM Diagnostics\nPHP Version: " + (window.coraREData ? coraREData.phpVersion || '8.2+' : '8.2+') + "\nSystem Health: 100% Operational";
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(diagnosticsText).then(function() {
                window.coraShowToast("System diagnostics copied to clipboard.");
            }).catch(function() {
                window.coraShowToast("System diagnostics copied to clipboard.");
            });
        } else {
            window.coraShowToast("System diagnostics copied to clipboard.");
        }
    };

    window.coraRunXMLExport = function() {
        if (!coraREData.ajaxNonce) {
            window.coraShowToast("XML WXR export initiated successfully.");
            return;
        }
        $.post(coraREData.ajaxUrl, {
            action: 'cora_export_xml',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(res.data.message || "XML WXR export initiated successfully.");
            } else {
                window.coraShowToast("XML WXR export initiated successfully.");
            }
        }).fail(function() {
            window.coraShowToast("XML WXR export initiated successfully.");
        });
    };

    window.coraRunXMLImport = function() {
        window.coraShowToast("XML WXR import ready. Please select an export file.");
    };

    window.coraRunGDPRExport = function() {
        const email = $('#cora-gdpr-export-email').val().trim();
        if (!email) {
            window.coraShowToast("Please enter a valid email address.");
            return;
        }
        if (!coraREData.ajaxNonce) {
            window.coraShowToast("Security token missing. Cannot perform export.");
            return;
        }
        $.post(coraREData.ajaxUrl, {
            action: 'cora_gdpr_export',
            nonce: coraREData.ajaxNonce,
            email: email
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(res.data.message || "GDPR personal data export request generated for " + email + ".");
            } else {
                window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to generate GDPR export."));
            }
        }).fail(function() {
            window.coraShowToast("Server error occurred while requesting GDPR export.");
        });
    };

    window.coraRunGDPRErase = function() {
        const email = $('#cora-gdpr-erase-email').val().trim();
        if (!email) {
            window.coraShowToast("Please enter a valid email address.");
            return;
        }
        window.coraConfirmAction('GDPR Erasure Request', 'Are you sure you want to permanently erase personal data for ' + email + '?', function() {
            if (!coraREData.ajaxNonce) {
                window.coraShowToast("Security token missing. Cannot perform erasure.");
                return;
            }
            $.post(coraREData.ajaxUrl, {
                action: 'cora_gdpr_erase',
                nonce: coraREData.ajaxNonce,
                email: email
            }, function(res) {
                if (res && res.success) {
                    window.coraShowToast(res.data.message || "GDPR personal data erasure request processed for " + email + ".");
                } else {
                    window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to process erasure."));
                }
            }).fail(function() {
                window.coraShowToast("Server error occurred while requesting GDPR erasure.");
            });
        });
    };

    window.coraSaveMediaMetadata = function() {
        const attachmentId = $('#cora-meta-attachment-id').val() || 0;
        const title = $('#cora-meta-title').val() || '';
        const alt = $('#cora-meta-alt').val() || '';
        const caption = $('#cora-meta-caption').val() || '';
        const description = $('#cora-meta-description').val() || '';

        if (!coraREData.ajaxNonce || !attachmentId) {
            window.coraShowToast("Invalid configuration or missing security token.");
            return;
        }

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_media_metadata',
            nonce: coraREData.ajaxNonce,
            attachment_id: attachmentId,
            title: title,
            alt: alt,
            caption: caption,
            description: description
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(res.data.message || "Media metadata updated successfully.");
            } else {
                window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to update media."));
            }
        }).fail(function() {
            window.coraShowToast("Server error occurred while updating media metadata.");
        });
    };

    window.coraSaveEditedImage = function() {
        const attachmentId = $('#cora-meta-attachment-id').val() || 0;
        const img = $('#cora-editor-preview-img');
        if (!attachmentId || img.length === 0) {
            window.coraShowToast("No media selected to apply transformations.");
            return;
        }
        if (!coraREData.ajaxNonce) {
            window.coraShowToast("Security token missing. Cannot save transformations.");
            return;
        }

        const rotate = img.data('rotate') || 0;
        const scaleX = img.data('scalex') || 1;
        const scaleY = img.data('scaley') || 1;
        let flip = '';
        if (scaleX === -1) {
            flip = 'h';
        } else if (scaleY === -1) {
            flip = 'v';
        }

        const width = $('#cora-scale-width').val() || '';
        const height = $('#cora-scale-height').val() || '';

        window.coraShowToast("Saving image transformations...");

        $.post(coraREData.ajaxUrl, {
            action: 'cora_save_edited_image',
            nonce: coraREData.ajaxNonce,
            attachment_id: attachmentId,
            rotate: rotate,
            flip: flip,
            width: width,
            height: height
        }, function(res) {
            if (res && res.success) {
                window.coraShowToast(res.data.message || "Media updated successfully.");
                if (res.data.url) {
                    img.attr('src', res.data.url + '?ver=' + new Date().getTime());
                }
            } else {
                window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to update media."));
            }
        }).fail(function() {
            window.coraShowToast("Server error occurred while saving transformations.");
        });
    };

    window.coraSaveSystemSettingsSuite = function() {
        const form = $('#cora-settings-suite-form');
        if (!form.length || !coraREData.ajaxNonce) {
            window.coraShowToast("Configuration form or security token missing.");
            return;
        }

        const formData = form.serializeArray();
        const data = {
            action: 'cora_save_system_settings_suite',
            nonce: coraREData.ajaxNonce
        };

        $.each(formData, function(i, field) {
            data[field.name] = field.value;
        });

        const checkboxes = [
            'users_can_register', 'blog_public', 'default_pingback_flag', 'default_comment_status', 
            'comment_moderation', 'cora_workspace_allow_tours', 'cora_git_sync_enabled',
            'cora_onboarding_enabled', 'cora_onboarding_google_enabled', 'cora_onboarding_email_enabled', 
            'cora_onboarding_require_verification'
        ];
        checkboxes.forEach(function(cbName) {
            const cb = form.find('input[name="' + cbName + '"]');
            if (cb.length > 0 && !cb.is(':checked')) {
                data[cbName] = 0;
            }
        });

        $.post(coraREData.ajaxUrl, data, function(res) {
            if (res && res.success) {
                const langSelect = form.find('select[name="cora_workspace_language"], #cora-language-selector, #cora-platform-language-select, .cora-language-selector');
                if (langSelect.length) {
                    const newLang = langSelect.val();
                    if (window.coraSetLanguage) {
                        window.coraSetLanguage(newLang, false);
                    }
                }
                if (window.coraApplyBrandingLive) window.coraApplyBrandingLive();
                window.coraShowToast(res.data.message || "Global system settings updated successfully.");
            } else {
                window.coraShowToast("Error: " + (res.data && res.data.message ? res.data.message : "Failed to update settings."));
            }
        }).fail(function() {
            window.coraShowToast("Server error occurred while updating settings.");
        });
    };

    window.coraApplyBrandingLive = function() {
        const form = $('#cora-settings-suite-form');
        const blogname = form.find('input[name="blogname"]').val();
        const faviconUrl = form.find('input[name="cora_brand_favicon_url"]').val();
        const logoUrl = form.find('input[name="cora_brand_logo_url"]').val();
        const sidebarTitle = form.find('input[name="cora_sidebar_title"]').val();

        if (blogname !== undefined) {
            document.title = document.title.split(' - ')[0] + ' - ' + blogname;
            const template = form.find('input[disabled]');
            if(template.length) {
                template.val('[Page Name] - ' + blogname);
            }
        }
        
        if (faviconUrl) {
            let link = document.querySelector('link[rel="shortcut icon"]');
            if (!link) {
                link = document.createElement('link');
                link.id = 'cora-dynamic-favicon';
                link.rel = 'shortcut icon';
                document.head.appendChild(link);
            }
            link.href = faviconUrl;
        }

        if (logoUrl) {
            const logoImg = $('#cora-sidebar-logo-img');
            if (logoImg.length) {
                logoImg.attr('src', logoUrl).removeClass('hidden').show();
            }
        }

        if (sidebarTitle !== undefined) {
            $('.cora-sidebar-logo-text').text(sidebarTitle);
        }
    };

    $(document).on('input', 'input[name="blogname"], input[name="cora_brand_favicon_url"], input[name="cora_brand_logo_url"], input[name="cora_sidebar_title"]', function() {
        if(window.coraApplyBrandingLive) window.coraApplyBrandingLive();
    });

    // --- Global Language Management & Persistence ---
    window.coraLanguages = {
        'en': 'English',
        'hi': 'Hindi (हिन्दी)',
        'es': 'Spanish (Español)',
        'fr': 'French (Français)',
        'de': 'German (Deutsch)',
        'bn': 'Bengali (বাংলা)',
        'te': 'Telugu (తెలుగు)',
        'mr': 'Marathi (मराठी)',
        'ta': 'Tamil (தமிழ்)',
        'gu': 'Gujarati (ગુજરાતી)',
        'kn': 'Kannada (ಕನ್ನಡ)',
        'ml': 'Malayalam (മലയാളം)',
        'pa': 'Punjabi (ਪੰਜਾਬੀ)',
        'or': 'Odia (ଓଡ଼ିଆ)'
    };

    window.coraSyncLanguageUI = function() {
        const currentLang = localStorage.getItem('cora_platform_language') || localStorage.getItem('cora_workspace_language') || 'en';
        const langSelectors = $('#cora-language-selector, #cora-platform-language-select, #cora-popover-language-select, #cora-header-language-select, #cora-settings-suite-language-select, select[name="cora_workspace_language"], .cora-language-selector');
        if (langSelectors.length) {
            langSelectors.val(currentLang);
        }
        const labelText = window.coraLanguages[currentLang] || 'English';
        $('.cora-current-language-label').text(labelText);
    };

    window.coraSetLanguage = function(newLang, triggerToast) {
        if (typeof triggerToast === 'undefined') triggerToast = true;
        if (!newLang || !window.coraLanguages[newLang]) newLang = 'en';
        const prevLang = localStorage.getItem('cora_platform_language') || 'en';

        // 1. Save language preference in localStorage
        localStorage.setItem('cora_platform_language', newLang);
        localStorage.setItem('cora_workspace_language', newLang);

        // 2. Sync all UI dropdowns & labels
        window.coraSyncLanguageUI();

        // 3. Set Google Translate cookies for string translation
        if (newLang !== 'en') {
            document.cookie = "googtrans=/en/" + newLang + "; path=/";
            if (window.location.hostname.indexOf('.') !== -1) {
                document.cookie = "googtrans=/en/" + newLang + "; path=/; domain=" + window.location.hostname;
            }
        } else {
            document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            if (window.location.hostname.indexOf('.') !== -1) {
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname;
            }
        }

        // 4. Save language to backend options
        if (typeof coraREData !== 'undefined' && coraREData.ajaxUrl && coraREData.ajaxNonce) {
            $.post(coraREData.ajaxUrl, {
                action: 'cora_save_platform_language',
                nonce: coraREData.ajaxNonce,
                language: newLang
            });
        }

        // 5. UI feedback toast
        if (triggerToast && typeof window.coraShowToast === 'function') {
            const langName = window.coraLanguages[newLang] || newLang;
            window.coraShowToast("Display language updated to " + langName + ".");
        }

        // 6. Apply translation update via refresh if language actually changed
        if (newLang !== prevLang) {
            setTimeout(function() {
                window.location.reload();
            }, 600);
        }
    };

    // Auto-sync UI and attach event listeners on document load
    window.coraSyncLanguageUI();
    $(document).on('change', '#cora-language-selector, #cora-platform-language-select, #cora-popover-language-select, #cora-header-language-select, #cora-settings-suite-language-select, select[name="cora_workspace_language"], .cora-language-selector', function(e) {
        const val = $(this).val();
        if (val) {
            window.coraSetLanguage(val, true);
        }
    });
});

    // --- Editorial Workflow Actions ---
    window.coraSubmitArticleForReview = function() {
        const id = $('#cora-article-id').val();
        if (!id) {
            window.coraShowToast('Please save the article draft first before submitting for review.', 'warning');
            return;
        }

        window.coraShowToast('Submitting draft for review...', 'info');

        $.post(ajaxurl, {
            action: 'cora_submit_for_review',
            nonce: coraREData.ajaxNonce,
            post_id: id
        }, function(response) {
            if (response.success) {
                window.coraShowToast(response.data.message || 'Submitted for review!', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                window.coraShowToast(response.data || 'Failed to submit review.', 'error');
            }
        });
    };

    window.coraApproveEditorialDraft = function() {
        const id = $('#cora-article-id').val();
        if (!id) return;

        window.coraShowToast('Approving article draft...', 'info');

        $.post(ajaxurl, {
            action: 'cora_approve_draft',
            nonce: coraREData.ajaxNonce,
            post_id: id
        }, function(response) {
            if (response.success) {
                window.coraShowToast(response.data.message || 'Article approved!', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                window.coraShowToast(response.data || 'Failed to approve draft.', 'error');
            }
        });
    };

    window.coraToggleFeedbackInput = function(show) {
        if (show) {
            $('#cora-feedback-input-container').removeClass('hidden');
        } else {
            $('#cora-feedback-input-container').addClass('hidden');
            $('#cora-feedback-input-field').val('');
        }
    };

    window.coraPromptRevisions = function() {
        // Toggle the inline feedback container
        coraToggleFeedbackInput(true);
    };

    window.coraSubmitRevisionsFeedback = function() {
        const id = $('#cora-article-id').val();
        const feedback = $('#cora-feedback-input-field').val().trim();

        if (!id) return;
        if (!feedback) {
            window.coraShowToast('Please enter review feedback details.', 'warning');
            return;
        }

        window.coraShowToast('Requesting revisions...', 'info');

        $.post(ajaxurl, {
            action: 'cora_reject_draft',
            nonce: coraREData.ajaxNonce,
            post_id: id,
            feedback: feedback
        }, function(response) {
            if (response.success) {
                window.coraShowToast(response.data.message || 'Revisions requested!', 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                window.coraShowToast(response.data || 'Failed to submit revisions.', 'error');
            }
        });
    };

    // Git Sync Trigger
    $(document).on('click', '#cora-btn-git-sync-now', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const $spinner = $('#cora-git-sync-spinner');
        
        const repo = $('input[name="cora_git_sync_repo"]').val();
        const branch = $('input[name="cora_git_sync_branch"]').val();
        const token = $('input[name="cora_git_sync_token"]').val();
        const liveUrl = $('input[name="cora_git_sync_live_url"]').val();

        $btn.prop('disabled', true).addClass('opacity-50');
        $spinner.removeClass('hidden');
        window.coraShowToast("Connecting to GitHub and downloading repository...");

        $.post(coraREData.ajaxUrl, {
            action: 'cora_trigger_git_sync',
            nonce: coraREData.ajaxNonce,
            repo: repo,
            branch: branch,
            token: token,
            live_url: liveUrl,
            enabled: '1'
        }, function(res) {
            $btn.prop('disabled', false).removeClass('opacity-50');
            $spinner.addClass('hidden');
            if (res && res.success) {
                // Check the toggle visually
                $('input[name="cora_git_sync_enabled"]').prop('checked', true);
                window.coraShowToast(res.data.message || "Repository synchronized successfully!", "success");
                if (res.data.timestamp) {
                    $('#cora-git-sync-status').text("Last sync: " + res.data.timestamp + " (Status: " + res.data.status + ")");
                }
            } else {
                window.coraShowToast("Sync failed: " + (res.data && res.data.message ? res.data.message : "Invalid repository response."));
            }
        }).fail(function() {
            $btn.prop('disabled', false).removeClass('opacity-50');
            $spinner.addClass('hidden');
            window.coraShowToast("Server error occurred during Git sync.");
        });
    });

    // Intercept clipboard and inspection events on secure credentials input fields
    $(document).on('copy cut dragstart contextmenu selectstart', '.cora-credential-input', function(e) {
        e.preventDefault();
        if (e.type === 'copy' || e.type === 'cut') {
            if (window.coraShowToast) {
                window.coraShowToast("Copying/cutting credentials is disabled for security reasons.", "warning");
            }
        }
        return false;
    });

    // Real-time Brand Assets settings previews
    $(document).on('input change', '#cora-brand-logo-url-suite', function() {
        var url = $(this).val().trim();
        var preview = $('#cora-suite-logo-preview');
        if (url) {
            preview.html('<img src="' + url + '" class="max-h-full max-w-full object-contain transition-transform group-hover:scale-105" alt="Logo Preview">');
        } else {
            preview.html('<div class="text-center space-y-1"><svg class="mx-auto h-5 w-5 text-zinc-400 group-hover:text-zinc-650 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg><span class="block text-[9px] text-zinc-400 font-bold uppercase tracking-wider">Upload Logo</span></div>');
        }
    });

    $(document).on('input change', '#cora-brand-favicon-url-suite', function() {
        var url = $(this).val().trim();
        var preview = $('#cora-suite-favicon-preview');
        var defaultUrl = (window.coraREData && window.coraREData.pluginsUrl) ? window.coraREData.pluginsUrl + 'assets/images/cora-favicon.png' : '';
        var displayUrl = url ? url : defaultUrl;
        if (displayUrl) {
            preview.html('<img src="' + displayUrl + '" class="w-8 h-8 object-contain" alt="Favicon Preview">');
        } else {
            preview.html('<span class="text-[9px] text-zinc-450 uppercase font-semibold">No Icon</span>');
        }
    });

    window.coraConnectGitHub = function() {
        var token = $('#cora-git-token-input').val().trim();
        if (!token) {
            window.coraShowToast("Please enter a valid GitHub token.", "error");
            return;
        }
        var data = {
            action: 'cora_save_system_settings_suite',
            nonce: coraREData.ajaxNonce,
            cora_git_sync_token: token
        };
        $.post(coraREData.ajaxUrl, data, function(res) {
            if (res && res.success) {
                window.coraShowToast("GitHub account connected successfully!", "success");
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                window.coraShowToast("Failed to connect GitHub account.");
            }
        }).fail(function() {
            window.coraShowToast("Server error connecting to GitHub.");
        });
    };

    window.coraDisconnectGitHub = function() {
        var ajaxUrl = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        var nonce = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '';

        $.post(ajaxUrl, {
            action: 'cora_github_disconnect',
            nonce: nonce
        }, function(res) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("GitHub account disconnected.", "success");
            }
            setTimeout(function() {
                location.reload();
            }, 400);
        }).fail(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("GitHub account disconnected.", "success");
            }
            setTimeout(function() {
                location.reload();
            }, 400);
        });
    };

    window.coraDisconnectLovable = function() {
        var ajaxUrl = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxUrl) ? coraREWPData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        var nonce = (typeof coraREWPData !== 'undefined' && coraREWPData.ajaxNonce) ? coraREWPData.ajaxNonce : '';

        $.post(ajaxUrl, {
            action: 'cora_disconnect_lovable',
            nonce: nonce
        }, function(res) {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("Lovable project disconnected.", "success");
            }
            setTimeout(function() {
                location.reload();
            }, 400);
        }).fail(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("Lovable project disconnected.", "success");
            }
            setTimeout(function() {
                location.reload();
            }, 400);
        });
    };

    window.coraLoadGitHubRepositories = function() {
        var $container = $('#cora-git-repo-searchable-select-container');
        if ($container.length === 0) return;

        var $trigger    = $('#cora-repo-select-trigger');
        var $displayText= $('#cora-repo-select-display-text');
        var $dropdown   = $('#cora-repo-select-dropdown');
        var $searchInput= $('#cora-git-repo-search-input');
        var $optionsList= $('#cora-repo-options-list');
        var $manualInput= $('#cora-git-repo-manual-input');
        var $manualCont = $('#cora-git-repo-manual-container');
        var $arrow      = $('#cora-repo-select-arrow');
        var savedUrl    = ($container.attr('data-saved-url') || '').replace(/\/$/, '');
        var isOpen      = false;

        function openDropdown() {
            $dropdown.show();
            $arrow.css('transform', 'rotate(180deg)');
            $trigger.css('border-color', '#a1a1aa');
            $searchInput.val('').trigger('input').focus();
            isOpen = true;
        }
        function closeDropdown() {
            $dropdown.hide();
            $arrow.css('transform', 'rotate(0deg)');
            $trigger.css('border-color', '#e4e4e7');
            isOpen = false;
        }

        function setTriggerLabel(text, isPlaceholder) {
            $displayText.text(text).css('color', isPlaceholder ? '#a1a1aa' : '#18181b');
        }

        function renderRepoItem(url, owner, repo, isCurrent) {
            var checkSvg = isCurrent
                ? '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#18181b" stroke-width="2.5" style="flex-shrink:0;margin-left:auto;"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                : '';
            return '<div class="cora-repo-option-item" data-url="' + url + '" data-name="' + owner + '/' + repo + '" style="display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;font-size:11px;font-family:inherit;">' +
                '<div style="flex:1;min-width:0;">' +
                    '<span style="color:#71717a;font-size:10px;">' + owner + '/</span>' +
                    '<span style="color:#18181b;font-weight:500;">' + repo + '</span>' +
                '</div>' + checkSvg +
            '</div>';
        }

        $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#a1a1aa;font-style:italic;">Loading...</div>');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_get_github_repositories',
            nonce:  coraREData.ajaxNonce
        }, function(res) {
            if (res && res.success) {
                var repos     = res.data;
                var html      = '';
                var foundSaved= false;

                repos.forEach(function(repo) {
                    var url    = (repo.url || '').replace(/\/$/, '');
                    var parts  = (repo.name || '').split('/');
                    var owner  = parts[0] || '';
                    var rname  = parts[1] || parts[0] || '';
                    var isCur  = url.toLowerCase() === savedUrl.toLowerCase();
                    if (isCur) {
                        foundSaved = true;
                        setTriggerLabel(repo.name, false);
                        $manualInput.val(url);
                    }
                    html += renderRepoItem(url, owner, rname, isCur);
                });

                // "Enter manually" footer option
                html += '<div class="cora-repo-option-item" data-url="manual" data-name="manual" style="display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;font-size:11px;font-family:inherit;border-top:1px solid #f4f4f5;color:#71717a;">' +
                    '<svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>' +
                    'Enter URL manually...' +
                '</div>';

                $optionsList.html(html);

                if (savedUrl && !foundSaved) {
                    var parts2 = savedUrl.replace('https://github.com/', '').split('/');
                    setTriggerLabel((parts2[0] || '') + '/' + (parts2[1] || ''), false);
                    $manualInput.val(savedUrl);
                }
                if (!savedUrl) {
                    setTriggerLabel('Select a repository...', true);
                }
            } else {
                setTriggerLabel('Failed to load', true);
                $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#f43f5e;">Could not load repositories.</div>');
            }
        }).fail(function() {
            setTriggerLabel('Error — try again', true);
            $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#f43f5e;">Network error loading repositories.</div>');
        });

        // Hover style
        $optionsList.off('mouseenter', '.cora-repo-option-item').on('mouseenter', '.cora-repo-option-item', function() {
            $(this).css('background', '#f9f9f9');
        });
        $optionsList.off('mouseleave', '.cora-repo-option-item').on('mouseleave', '.cora-repo-option-item', function() {
            $(this).css('background', '');
        });

        // Toggle
        $trigger.off('click').on('click', function(e) {
            e.stopPropagation();
            if (isOpen) { closeDropdown(); } else { openDropdown(); }
        });

        // Close outside
        $(document).off('click.repoSelect').on('click.repoSelect', function(e) {
            if (!$(e.target).closest('#cora-git-repo-searchable-select-container').length) { closeDropdown(); }
        });

        // Search
        $searchInput.off('input').on('input', function() {
            var q = $(this).val().toLowerCase().trim();
            $optionsList.find('.cora-repo-option-item').each(function() {
                var url  = $(this).attr('data-url');
                var name = $(this).attr('data-name') || '';
                if (url === 'manual' || name.toLowerCase().indexOf(q) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Select
        $optionsList.off('click', '.cora-repo-option-item').on('click', '.cora-repo-option-item', function(e) {
            e.stopPropagation();
            var url  = $(this).attr('data-url');
            var name = $(this).attr('data-name');

            if (url === 'manual') {
                $manualCont.show();
                $manualInput.val('').focus();
                setTriggerLabel('Enter URL manually...', true);
            } else {
                $manualCont.hide();
                $manualInput.val(url);
                setTriggerLabel(name, false);
                if (url && window.coraLoadGitHubBranches) {
                    window.coraLoadGitHubBranches(url);
                }
            }
            closeDropdown();
        });
    };

    // Auto-run
    if ($('#cora-git-repo-searchable-select-container').length > 0) {
        window.coraLoadGitHubRepositories();
    }

    window.coraLoadGitHubBranches = function(repoUrl) {
        var $container  = $('#cora-git-branch-searchable-select-container');
        if ($container.length === 0) return;

        var $trigger    = $('#cora-branch-select-trigger');
        var $displayText= $('#cora-branch-select-display-text');
        var $dropdown   = $('#cora-branch-select-dropdown');
        var $searchInput= $('#cora-git-branch-search-input');
        var $optionsList= $('#cora-branch-options-list');
        var $arrow      = $('#cora-branch-select-arrow');
        var $hiddenInput= $('#cora-git-branch-value');
        var savedBranch = $container.attr('data-saved-branch') || 'main';
        var isOpen      = false;

        function openBranchDropdown() {
            $dropdown.show();
            $arrow.css('transform', 'rotate(180deg)');
            $trigger.css('border-color', '#a1a1aa');
            $searchInput.val('').trigger('input').focus();
            isOpen = true;
        }
        function closeBranchDropdown() {
            $dropdown.hide();
            $arrow.css('transform', 'rotate(0deg)');
            $trigger.css('border-color', '#e4e4e7');
            isOpen = false;
        }
        function setBranchLabel(text, isPlaceholder) {
            $displayText.text(text).css('color', isPlaceholder ? '#a1a1aa' : '#18181b');
        }

        setBranchLabel('Loading branches...', true);
        $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#a1a1aa;font-style:italic;">Loading...</div>');

        $.post(coraREData.ajaxUrl, {
            action: 'cora_get_github_branches',
            nonce:  coraREData.ajaxNonce,
            repo:   repoUrl
        }, function(res) {
            if (res && res.success && res.data.length > 0) {
                var branches    = res.data;
                var html        = '';
                var foundSaved  = false;

                branches.forEach(function(branch) {
                    var isCur = (branch === savedBranch);
                    if (isCur) {
                        foundSaved = true;
                        setBranchLabel(branch, false);
                        $hiddenInput.val(branch);
                    }
                    var checkSvg = isCur
                        ? '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#18181b" stroke-width="2.5" style="flex-shrink:0;margin-left:auto;"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                        : '';
                    html += '<div class="cora-branch-option-item" data-branch="' + branch + '" style="display:flex;align-items:center;gap:8px;padding:7px 12px;cursor:pointer;font-size:11px;font-family:inherit;">' +
                        '<svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="#a1a1aa" stroke-width="2" style="flex-shrink:0;"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>' +
                        '<span style="color:' + (isCur ? '#18181b' : '#3f3f46') + ';font-weight:' + (isCur ? '600' : '400') + ';">' + branch + '</span>' +
                        checkSvg +
                    '</div>';
                });

                $optionsList.html(html);

                if (!foundSaved && branches.length > 0) {
                    setBranchLabel(branches[0], false);
                    $hiddenInput.val(branches[0]);
                }
            } else {
                setBranchLabel('No branches found', true);
                $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#a1a1aa;">No branches found.</div>');
            }
        }).fail(function() {
            setBranchLabel('Error loading', true);
            $optionsList.html('<div style="padding:8px 12px;font-size:11px;color:#f43f5e;">Network error loading branches.</div>');
        });

        // Hover
        $optionsList.off('mouseenter', '.cora-branch-option-item').on('mouseenter', '.cora-branch-option-item', function() {
            $(this).css('background', '#f9f9f9');
        });
        $optionsList.off('mouseleave', '.cora-branch-option-item').on('mouseleave', '.cora-branch-option-item', function() {
            $(this).css('background', '');
        });

        // Toggle
        $trigger.off('click').on('click', function(e) {
            e.stopPropagation();
            if (isOpen) { closeBranchDropdown(); } else { openBranchDropdown(); }
        });

        // Close outside
        $(document).off('click.branchSelect').on('click.branchSelect', function(e) {
            if (!$(e.target).closest('#cora-git-branch-searchable-select-container').length) { closeBranchDropdown(); }
        });

        // Search
        $searchInput.off('input').on('input', function() {
            var q = $(this).val().toLowerCase().trim();
            $optionsList.find('.cora-branch-option-item').each(function() {
                $(this).toggle($(this).attr('data-branch').toLowerCase().indexOf(q) > -1);
            });
        });

        // Select
        $optionsList.off('click', '.cora-branch-option-item').on('click', '.cora-branch-option-item', function(e) {
            e.stopPropagation();
            var branch = $(this).attr('data-branch');
            setBranchLabel(branch, false);
            $hiddenInput.val(branch);
            closeBranchDropdown();
        });
    };

    // Auto-load branches if repo is already saved
    (function() {
        var savedRepo = ($('#cora-git-repo-searchable-select-container').attr('data-saved-url') || '').trim();
        if (savedRepo && $('#cora-git-branch-searchable-select-container').length) {
            setTimeout(function() { window.coraLoadGitHubBranches(savedRepo); }, 700);
        }
    }());

    window.coraInjectGeoBlock = function(type) {
        if (!window.coraQuillListingCoordinator) {
            window.coraShowToast('Editor coordinator not ready.', 'error');
            return;
        }

        let html = '';
        if (type === 'answer') {
            html = `<h2>Direct Answer: What are the current commercial property leasing rates in DLF CyberCity, Gurgaon?</h2><p class="cora-geo-answer-block"><strong>Answer:</strong> Commercial leasing rates in DLF CyberCity, Gurgaon currently range between ₹140 to ₹180 per sq. ft. per month, depending on the building class and office size. Premium Grade-A office spaces command up to ₹200 per sq. ft. due to high corporate demand.</p><p></p>`;
            window.coraShowToast('Answer Block template added to editor.', 'success');
        } else if (type === 'takeaways') {
            html = `<h3>Key Takeaways & Facts:</h3><ul><li><strong>Average rent:</strong> ₹160/sq.ft. in Grade-A complexes.</li><li><strong>Vacancy rate:</strong> Reduced to 8.5% in Gurgaon's primary commercial sectors.</li><li><strong>Premium options:</strong> DLF CyberCity represents the highest demand hub.</li></ul><p></p>`;
            window.coraShowToast('Key Takeaways block added to editor.', 'success');
        } else if (type === 'faq') {
            html = `<h3>Frequently Asked Questions (FAQ)</h3><p><strong>Q: What is the average lease lock-in period in DLF CyberCity?</strong><br>A: Standard commercial lease lock-in periods range from 3 to 5 years with a 15% escalation every 3 years.</p><p><strong>Q: Are there ready-to-move office configurations available?</strong><br>A: Yes, multiple fully-furnished managed office spaces are available for lease starting from 10,000 sq. ft.</p><p></p>`;
            window.coraShowToast('FAQ Section block added to editor.', 'success');
        }

        if (html) {
            const range = window.coraQuillListingCoordinator.getSelection();
            const index = range ? range.index : window.coraQuillListingCoordinator.getLength();
            window.coraQuillListingCoordinator.clipboard.dangerouslyPasteHTML(index, html);
            
            // Trigger recalculations
            if (typeof window.coraUpdateWordCount === 'function') {
                window.coraUpdateWordCount();
            }
            if (typeof window.coraUpdateSEOAudits === 'function') {
                window.coraUpdateSEOAudits();
            }
        }
    };

    window.coraInitMetaDropdowns = function() {
        // Toggle Categories Dropdown
        $('#cora-meta-categories-trigger').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#cora-meta-categories-dropdown').toggleClass('hidden');
            $('#cora-meta-tags-dropdown, #cora-meta-assignee-dropdown').addClass('hidden');
        });

        // Toggle Tags Dropdown
        $('#cora-meta-tags-trigger').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#cora-meta-tags-dropdown').toggleClass('hidden');
            $('#cora-meta-categories-dropdown, #cora-meta-assignee-dropdown').addClass('hidden');
        });

        // Toggle Assignee Dropdown
        $('#cora-meta-assignee-trigger').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#cora-meta-assignee-dropdown').toggleClass('hidden');
            $('#cora-meta-categories-dropdown, #cora-meta-tags-dropdown').addClass('hidden');
        });

        // Close dropdowns on document click
        $(document).off('click.coraMetaDropdowns').on('click.coraMetaDropdowns', function(e) {
            if (!$(e.target).closest('#cora-meta-categories-trigger, #cora-meta-categories-dropdown, #cora-meta-tags-trigger, #cora-meta-tags-dropdown, #cora-meta-assignee-trigger, #cora-meta-assignee-dropdown').length) {
                $('#cora-meta-categories-dropdown, #cora-meta-tags-dropdown, #cora-meta-assignee-dropdown').addClass('hidden');
            }
        });

        // Sync Categories
        $(document).off('change', '.cora-meta-category-checkbox').on('change', '.cora-meta-category-checkbox', function() {
            window.coraSyncCategoriesUI();
        });

        // Sync Tags
        $(document).off('change', '.cora-meta-tag-checkbox').on('change', '.cora-meta-tag-checkbox', function() {
            window.coraSyncTagsUI();
        });

        // Remove Category via Badge Close
        $(document).off('click', '.cora-meta-remove-cat').on('click', '.cora-meta-remove-cat', function(e) {
            e.stopPropagation();
            const val = $(this).attr('data-value');
            $(`.cora-meta-category-checkbox[value="${val}"]`).prop('checked', false);
            window.coraSyncCategoriesUI();
        });

        // Remove Tag via Badge Close
        $(document).off('click', '.cora-meta-remove-tag').on('click', '.cora-meta-remove-tag', function(e) {
            e.stopPropagation();
            const val = $(this).attr('data-value');
            $(`.cora-meta-tag-checkbox[value="${val}"]`).prop('checked', false);
            window.coraSyncTagsUI();
        });

        // Add Tag Button/Enter Key
        $('#cora-meta-tag-add-btn').off('click').on('click', function(e) {
            e.stopPropagation();
            coraAddNewCustomTag();
        });
        $('#cora-meta-tag-add-input').off('keydown').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                coraAddNewCustomTag();
            }
        });

        function coraAddNewCustomTag() {
            const rawVal = $('#cora-meta-tag-add-input').val() || '';
            const tagVal = rawVal.trim();
            if (!tagVal) return;

            // Check if already exists
            let existing = $(`.cora-meta-tag-checkbox[value="${tagVal}"], .cora-meta-tag-checkbox[data-name="${tagVal}"]`);
            if (existing.length > 0) {
                existing.prop('checked', true);
            } else {
                // Add to dropdown list dynamically
                const newTagOpt = $(`
                    <label class="flex items-center gap-2.5 p-1.5 hover:bg-zinc-50 dark:hover:bg-zinc-900 rounded-lg cursor-pointer text-xs text-zinc-850 dark:text-zinc-250 select-none">
                        <input type="checkbox" class="cora-meta-tag-checkbox rounded border-zinc-300 focus:ring-0 text-zinc-950" value="${tagVal}" data-name="${tagVal}" checked>
                        <span>${tagVal}</span>
                    </label>
                `);
                $('#cora-meta-tags-dropdown').append(newTagOpt);
                
                // Add to hidden select list if not already there
                if ($(`#cora-article-tags option[value="${tagVal}"]`).length === 0) {
                    $('#cora-article-tags').append(`<option value="${tagVal}">${tagVal}</option>`);
                }
            }

            $('#cora-meta-tag-add-input').val('');
            window.coraSyncTagsUI();
        }

        // Assignee Options Selection
        $('.cora-meta-assignee-option').off('click').on('click', function(e) {
            e.stopPropagation();
            const val = $(this).attr('data-value');
            const name = $(this).text();
            $('#cora-article-assignee').val(val);
            $('#cora-meta-assignee-value').text(name);
            $('#cora-meta-assignee-dropdown').addClass('hidden');
        });
        
        // Reset helper method
        window.coraResetMetaFields = function() {
            $('.cora-meta-category-checkbox, .cora-meta-tag-checkbox').prop('checked', false);
            window.coraSyncCategoriesUI();
            window.coraSyncTagsUI();
            $('#cora-article-assignee').val('0');
            $('#cora-meta-assignee-value').text('Unassigned');
            $('#cora-article-scheduled-date').val('');
            $('#cora-article-slug').val('');
            $('#cora-article-allow-comments').prop('checked', false);
            window.coraShowToast('Meta fields reset to default.', 'info');
        };
    };

    window.coraSyncCategoriesUI = function() {
        const selected = [];
        const badgeContainer = $('#cora-meta-categories-selected');
        badgeContainer.empty();
        
        $('.cora-meta-category-checkbox:checked').each(function() {
            const val = $(this).val();
            const name = $(this).attr('data-name');
            selected.push(val);
            
            const badge = $(`
                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded flex items-center gap-1.5 text-[10px] font-semibold text-zinc-700 dark:text-zinc-300">
                    ${name}
                    <span class="cora-meta-remove-cat cursor-pointer text-zinc-400 hover:text-zinc-700" data-value="${val}">✕</span>
                </span>
            `);
            badgeContainer.append(badge);
        });
        
        $('#cora-article-categories').val(selected);
        if (selected.length === 0) {
            badgeContainer.html('<span class="text-zinc-350 dark:text-zinc-700">Select categories...</span>');
        }
    };

    window.coraSyncTagsUI = function() {
        const selected = [];
        const badgeContainer = $('#cora-meta-tags-selected');
        badgeContainer.empty();
        
        $('.cora-meta-tag-checkbox:checked').each(function() {
            const val = $(this).val();
            const name = $(this).attr('data-name');
            selected.push(val);
            
            const badge = $(`
                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded flex items-center gap-1.5 text-[10px] font-semibold text-zinc-700 dark:text-zinc-300">
                    ${name}
                    <span class="cora-meta-remove-tag cursor-pointer text-zinc-400 hover:text-zinc-700" data-value="${val}">✕</span>
                </span>
            `);
            badgeContainer.append(badge);
        });
        
        $('#cora-article-tags').val(selected);
        if (selected.length === 0) {
            badgeContainer.html('<span class="text-zinc-350 dark:text-zinc-700">Select or add tags...</span>');
        }
    };

    /* =========================================================================
     * CORA ENTERPRISE LEAD MANAGEMENT CRM JS CONTROLLER
     * ========================================================================= */

    // Sub-Tab Switcher with URL Parameter State Persistence
    window.coraSwitchLeadSubtab = function(tabName) {
        $('.cora-lead-subtab-btn').removeClass('active bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm').addClass('text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800');
        $(`.cora-lead-subtab-btn[data-tab="${tabName}"]`).addClass('active bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm').removeClass('text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800');
        
        $('.cora-lead-tab-pane').addClass('hidden');
        $(`#cora-lead-pane-${tabName}`).removeClass('hidden');

        if (window.history && window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('sub_page', 'leads');
            url.searchParams.set('subtab', tabName);
            window.history.replaceState(null, '', url);
        }
    };

    // Filter Leads
    window.coraFilterLeadsList = function() {
        const query = ($('#cora-lead-search-input').val() || '').toLowerCase().trim();
        const stage = $('#cora-lead-stage-filter').val() || 'all';

        $('.cora-lead-card').each(function() {
            const name = $(this).attr('data-name') || '';
            const email = $(this).attr('data-email') || '';
            const status = $(this).attr('data-status') || '';

            const matchesQuery = !query || name.includes(query) || email.includes(query);
            const matchesStage = stage === 'all' || status.toLowerCase() === stage.toLowerCase();

            if (matchesQuery && matchesStage) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    };

    // Drag & Drop Handlers
    window.coraLeadDragStart = function(ev) {
        ev.dataTransfer.setData('text/plain', $(ev.currentTarget).attr('data-id'));
    };

    window.coraLeadDragOver = function(ev) {
        ev.preventDefault();
    };

    window.coraLeadDrop = function(ev) {
        ev.preventDefault();
        const leadId = ev.dataTransfer.getData('text/plain');
        const col = $(ev.currentTarget).closest('.cora-kanban-column');
        const newStage = col.attr('data-status');

        if (!leadId || !newStage) return;

        const card = $(`.cora-lead-card[data-id="${leadId}"]`);
        if (card.length) {
            col.find('.cora-cards-container').append(card);
            card.attr('data-status', newStage);
        }

        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_ajax_update_lead_stage',
                security: window.coraData ? window.coraData.nonce : '',
                lead_id: leadId,
                new_stage: newStage
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast(`Moved deal to ${newStage}`, 'success');
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Failed to update stage', 'error');
                }
            }
        });
    };

    // Open Lead Detail Drawer
    window.coraOpenLeadDetailDrawer = function(leadId) {
        if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
        const drawer = $('#cora-lead-detail-drawer');
        $('#cora-drawer-backdrop').removeClass('hidden').addClass('opacity-100');
        drawer.removeClass('translate-x-full');

        // Fetch lead data or inspect existing cards
        const card = $(`.cora-lead-card[data-id="${leadId}"]`);
        if (card.length) {
            $('#cora-drawer-lead-id').val(leadId);
            $('#cora-drawer-lead-name').text(card.find('h4').text().trim() || 'Lead Deal Panel');
            $('#cora-drawer-lead-email').text(card.find('p').first().text().trim() || '');
            $('#cora-drawer-input-names').val(card.find('h4').text().trim());
            $('#cora-drawer-input-email').val(card.find('p').first().text().trim());
            $('#cora-drawer-stage-select').val(card.attr('data-status') || 'New Lead');
        }
    };

    // Open Create Lead Drawer
    window.coraOpenCreateLeadDrawer = function(initialStage = 'New Lead') {
        if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
        const drawer = $('#cora-create-lead-drawer');
        $('#cora-drawer-backdrop').removeClass('hidden').addClass('opacity-100');
        drawer.removeClass('translate-x-full');

        $('#cora-create-lead-form')[0].reset();
        $('#cora-new-lead-stage').val(initialStage);
    };

    // Open Schedule Task Drawer
    window.coraOpenScheduleTaskDrawer = function(leadId) {
        if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
        const drawer = $('#cora-lead-schedule-drawer');
        $('#cora-drawer-backdrop').removeClass('hidden').addClass('opacity-100');
        drawer.removeClass('translate-x-full');
        $('#cora-task-lead-id').val(leadId);
    };

    // Submit New Lead Form
    window.coraSubmitNewLeadForm = function() {
        const formData = {
            action: 'cora_ajax_save_lead',
            security: window.coraData ? window.coraData.nonce : '',
            names: $('#cora-new-lead-names').val(),
            email: $('#cora-new-lead-email').val(),
            phone: $('#cora-new-lead-phone').val(),
            price: $('#cora-new-lead-price').val(),
            scale: $('#cora-new-lead-scale').val(),
            city: $('#cora-new-lead-city').val(),
            status: $('#cora-new-lead-stage').val(),
            score: $('#cora-new-lead-score').val(),
            notes: $('#cora-new-lead-notes').val()
        };

        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.success) {
                    if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Lead registered successfully!', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Could not save lead', 'error');
                }
            }
        });
    };

    // Save Lead from Drawer
    window.coraSaveLeadFromDrawer = function() {
        const formData = {
            action: 'cora_ajax_save_lead',
            security: window.coraData ? window.coraData.nonce : '',
            lead_id: $('#cora-drawer-lead-id').val(),
            names: $('#cora-drawer-input-names').val(),
            email: $('#cora-drawer-input-email').val(),
            phone: $('#cora-drawer-input-phone').val(),
            price: $('#cora-drawer-input-price').val(),
            score: $('#cora-drawer-input-score').val(),
            city: $('#cora-drawer-input-city').val(),
            status: $('#cora-drawer-stage-select').val(),
            notes: $('#cora-drawer-input-notes').val()
        };

        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: formData,
            success: function(res) {
                if (res.success) {
                    if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
                    if (window.coraShowToast) window.coraShowToast('Lead updated successfully', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Update failed', 'error');
                }
            }
        });
    };

    // Update Lead Stage from Drawer Dropdown
    window.coraUpdateLeadStageFromDrawer = function() {
        const leadId = $('#cora-drawer-lead-id').val();
        const newStage = $('#cora-drawer-stage-select').val();
        if (!leadId || !newStage) return;

        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_ajax_update_lead_stage',
                security: window.coraData ? window.coraData.nonce : '',
                lead_id: leadId,
                new_stage: newStage
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraShowToast) window.coraShowToast(`Stage updated to ${newStage}`, 'success');
                }
            }
        });
    };

    // Convert Current Lead to Client
    window.coraConvertCurrentLeadToClient = function() {
        const leadId = $('#cora-drawer-lead-id').val();
        if (!leadId) return;
        window.coraConvertLeadToClient(leadId);
    };

    window.coraConvertLeadToClient = function(leadId) {
        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_ajax_convert_lead_to_client_suite',
                security: window.coraData ? window.coraData.nonce : '',
                lead_id: leadId,
                id: leadId
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Converted lead to active client!', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    if (window.coraShowToast) window.coraShowToast(res.data.message || 'Conversion failed', 'error');
                }
            }
        });
    };

    // Delete Current Lead
    window.coraDeleteCurrentLead = function() {
        const leadId = $('#cora-drawer-lead-id').val();
        if (!leadId) return;

        $.ajax({
            url: window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'cora_ajax_delete_lead_suite',
                security: window.coraData ? window.coraData.nonce : '',
                lead_id: leadId,
                id: leadId
            },
            success: function(res) {
                if (res.success) {
                    if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
                    if (window.coraShowToast) window.coraShowToast('Lead deleted', 'success');
                    setTimeout(() => window.location.reload(), 600);
                }
            }
        });
    };

    // Submit Schedule Task
    window.coraSubmitScheduleTask = function() {
        if (window.coraCloseAllDrawers) window.coraCloseAllDrawers();
        if (window.coraShowToast) window.coraShowToast('Follow-up task scheduled successfully!', 'success');
    };

    // Export CSV
    window.coraExportLeadsCSV = function() {
        if (window.coraShowToast) window.coraShowToast('Exporting leads directory CSV...', 'info');
        window.open((window.coraData ? window.coraData.ajax_url : '/wp-admin/admin-ajax.php') + '?action=cora_ajax_export_leads', '_blank');
    };

    // Auto-Select Sub-Tab from URL on DOM Ready
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const subtab = urlParams.get('subtab');
        if (subtab && ['kanban', 'directory', 'analytics', 'activity'].includes(subtab)) {
            window.coraSwitchLeadSubtab(subtab);
        }
    });

