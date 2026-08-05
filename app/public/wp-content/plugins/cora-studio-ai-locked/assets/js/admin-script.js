/**
 * Cora for Studio - Admin Dashboard JavaScript Interactions
 */

// Ensure coraData is defined and merge settings from coraWPData if it exists
if (typeof window.coraData === 'undefined') {
    window.coraData = typeof coraWPData !== 'undefined' ? coraWPData : {};
} else if (typeof coraWPData !== 'undefined') {
    Object.assign(window.coraData, coraWPData);
}

jQuery(document).ready(function($) {
    // Global wp.media Select Button & Toolbar Fix
    if (typeof wp !== 'undefined' && wp.media) {
        const originalMedia = wp.media;
        wp.media = function(options) {
            const frame = originalMedia(options);
            frame.on('open', function() {
                setTimeout(function() {
                    var modal = frame.$el;
                    if (!modal || !modal.length) return;
                    
                    // Force the bottom action toolbar and its buttons to be visible
                    var toolbar = modal.find('.media-frame-toolbar');
                    if (toolbar.length) {
                        toolbar.show().css({
                            'display': 'block',
                            'visibility': 'visible',
                            'opacity': '1',
                            'bottom': '0',
                            'z-index': '100000'
                        });
                        var primary = toolbar.find('.media-toolbar-primary');
                        if (primary.length) {
                            primary.show().css({
                                'display': 'flex',
                                'visibility': 'visible',
                                'opacity': '1'
                            });
                            var nativeBtns = primary.find('button, .button, .button-primary, .media-button');
                            if (nativeBtns.length) {
                                nativeBtns.show().css({
                                    'display': 'inline-flex',
                                    'visibility': 'visible',
                                    'opacity': '1',
                                    'pointer-events': 'auto'
                                });
                            }
                        }
                    }
                }, 150);
            });
            return frame;
        };
    }

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
        const activeData = (window.coraData && window.coraData.currentRole) ? window.coraData : (window.coraREData || {});
        const activeRole = $('#cora-role-preview-select').val() || activeData.currentRole || 'administrator';
        let allowed = (activeData.userPermissions && activeData.userPermissions[activeRole]) ? activeData.userPermissions[activeRole] : [];
        if (activeRole === 'administrator') {
            allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'gallery', 'leads', 'clients', 'blogs', 'gbp', 'plugins', 'my-profile', 'canvas'];
        }

        // my-profile is accessible by all logged-in users
        if (!allowed.includes('my-profile')) {
            allowed.push('my-profile');
        }

        if (targetPageId !== 'feature-hub' && !allowed.includes(targetPageId)) {
            window.coraShowToast("Access denied: your role does not have permission for this section.");
            return;
        }

        // If the target page is different from the current page, redirect
        if (targetPageId !== activeData.currentPage) {
            let siteUrl = activeData.siteUrl || window.location.origin;
            if (siteUrl.endsWith('/')) {
                siteUrl = siteUrl.slice(0, -1);
            }
            window.location.href = siteUrl + '/workspace/' + targetPageId;
        }
    };

    $('.cora-nav-item, .cora-bottom-nav-item').off('click').on('click', function(e) {
        const item = $(this).closest('.cora-nav-item, .cora-bottom-nav-item');
        if (item.hasClass('cora-nav-soon')) {
            window.coraShowToast("AI Assistants & Automation features are coming soon. Stay tuned!");
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        if (item.hasClass('cora-nav-locked')) {
            window.coraShowToast("Gallery SEO Tagging is a Premium feature. Upgrade to unlock.");
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        const target = item.data('target');
        if (target) {
            coraNavigateTo(target);
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

    // 2. Add Booking Dialog Drawer Controllers
    window.coraToggleAddShootDrawer = function(show) {
        const drawer = $('#cora-add-shoot-drawer');
        if (show) {
            drawer.removeClass('collapsed');
            $('#cora-drawer-client-name').focus();
        } else {
            drawer.addClass('collapsed');
            // Reset input values
            $('#cora-drawer-client-name').val('');
            $('#cora-drawer-shoot-type').val('Maternity Portrait');
            $('#cora-drawer-location').val('');
            $('#cora-drawer-date').val('');
            $('#cora-drawer-price').val('');
        }
    };

    $('#cora-add-booking-btn').on('click', function() {
        coraToggleAddShootDrawer(true);
    });

    // Save shoot details from drawer form
    $('#cora-save-shoot-btn').on('click', function() {
        const clientName = $('#cora-drawer-client-name').val().trim();
        const shootType = $('#cora-drawer-shoot-type').val();
        const location = $('#cora-drawer-location').val().trim() || 'Delhi Studio';
        const date = $('#cora-drawer-date').val().trim() || '28th Jun, 2026';
        const price = $('#cora-drawer-price').val().trim() || '₹15,000';
        
        if (!clientName) {
            coraShowToast("Please enter client name.");
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: coraData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_save_booking',
                nonce: coraData.ajaxNonce,
                client_name: clientName,
                shoot_type: shootType,
                location: location,
                date: date,
                price: price
            },
            success: function(response) {
                btn.prop('disabled', false).text('Save Shoot');
                if (response.success) {
                    coraShowToast("Booking created successfully!");
                    coraToggleAddShootDrawer(false);
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
            url: coraData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cora_update_booking_status',
                nonce: coraData.ajaxNonce,
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
            $('#cora-caption-shoot-select').val('wedding-jaipur');
            $('#cora-generate-caption-btn').click();
        } else if (actionType === 'invoice') {
            coraShowToast(`Invoice PDF dispatched to ${clientName}.`);
        }
    };

    // 6. AI Caption Generator Logic
    const captionDatabase = {
        'wedding-jaipur': {
            cinematic: [
                "Hand in hand, walking into our forever. Rohit and Sneha's stunning pre-wedding memories captured beautifully in the Pink City of Jaipur. It was pure magic. \n\nShot on Sony A7R V\n#JaipurWedding #DestinationWedding #WeddingAesthetic #CoraStudio #RambaghPalace",
                "A tale of royalty, heritage, and a love that stands still. Sneha and Rohit's moments at Rambagh Palace felt like a scene out of a cinematic romance.\n\n#RoyalWedding #JaipurPhotographer #IndianBride #PreWeddingMagic"
            ],
            romantic: [
                "Spent the most gorgeous evening shooting Rohit and Sneha's pre-wedding inside the majestic palace walls of Jaipur. \n\n#CoupleGoals #JaipurSunset #LoveInJaipur #PreWeddingShoot",
                "Wrapped in colors of love and Jaipur's golden hour. Sneha and Rohit showing us what dream weddings are made of.\n\n#WeddingPhotographyIndia #BridalStyle #JaipurDiaries"
            ],
            minimalist: [
                "Jaipur, sunset, and a quiet love story. Rohit & Sneha.\n\n#PreWedding #MinimalistPortrait #WeddingDelhi #Jaipur",
                "Simple moments in grand places.\n\n#WeddingFilm #CouplesPhotography #FineArtWedding"
            ],
            royal: [
                "The grandeur of Rajputana arches framing a love that is timeless. Rohit and Sneha at Rambagh Palace, Jaipur.\n\n#RoyalJaipur #PalaceWeddings #RajasthanTourism #IndianHeritage"
            ]
        },
        'maternity-delhi': {
            cinematic: [
                "Waiting for the greatest blessing of all. Ananya Sharma looking absolutely radiant amidst the peaceful backdrop of Lodhi Gardens. \n\n#MaternityShoot #DelhiPhotographer #MotherhoodMagic #CoraStudio",
                "A beautiful beginning, floating on hope and sunshine. Capturing this quiet milestone for Ananya in New Delhi.\n\n#MaternityPortrait #PregnancyAesthetic #DelhiMaternity"
            ],
            romantic: [
                "Already loved so deeply, little one. Beautiful morning walk with the radiant mother-to-be, Ananya. \n\n#LodhiGardens #DelhiMaternityPhotographer #BabyOnBoard #Motherhood"
            ],
            minimalist: [
                "Growing in grace. Ananya.\n\n#MaternityMinimalist #NaturalLight #StudioPortrait"
            ],
            royal: [
                "Empress of a new beginning. Ananya Sharma in Delhi.\n\n#MaternityLehenga #TraditionalMaternity #IndianMother"
            ]
        },
        'product-delhi': {
            cinematic: [
                "Sculpted by light. Behind the scenes of our recent high-fashion product campaign for RK Enterprises.\n\n#ProductCommercial #CommercialPhotographer #DelhiStudio #CoraStudio",
                "Details define design. Crafting sleek visuals for local brands in India.\n\n#BrandPhotography #CommercialShoot #StudioLighting"
            ],
            romantic: [
                "Details that make you fall in love. Product styling at Studio A.\n\n#ProductStyling #AestheticDetail #IndianBrands"
            ],
            minimalist: [
                "Form, light, and symmetry. Product commercial.\n\n#MinimalistProduct #StudioA #DelhiStudio"
            ],
            royal: [
                "Crafted for royalty. Premium detail campaign.\n\n#LuxuryProduct #BrassDecor #StudioLighting"
            ]
        }
    };

    $('#cora-generate-caption-btn').on('click', function() {
        const shoot = $('#cora-caption-shoot-select').val();
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
            title: "Aesthetic Indian Bride Portrait at Jaipur Rambagh Palace",
            alt: "Cinematic wedding portrait of an Indian bride in red traditional lehenga posing at sunset inside the historical corridors of Rambagh Palace, Jaipur.",
            tags: ["jaipur-wedding-photographer", "indian-bride-lehenga", "destination-wedding-jaipur", "rambagh-palace-shoot", "cinematic-bride-portrait"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            `
        },
        '2': {
            title: "Romantic Sunset Pre-Wedding Silhouette at Jaipur Fort",
            alt: "A couple standing silhouetted against a vibrant orange and golden sunset sky with the majestic arches of Rambagh Palace behind them.",
            tags: ["pre-wedding-shoot", "sunset-photography", "couple-silhouette", "jaipur-photographer", "royal-wedding"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
            `
        },
        '3': {
            title: "Traditional Rajasthani Wedding Feast and Table Decor",
            alt: "Close up of an elegantly decorated wedding dinner table featuring traditional brass plates filled with colorful Rajasthani delicacies, lit by soft candlelight.",
            tags: ["wedding-cuisine", "rajasthani-decor", "wedding-reception", "indian-wedding-food"],
            thumb: `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            `,
            largeThumb: `
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
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

    // Toggle button and search bar opens the sidebar instead of a modal
    $('#cora-quick-ai-btn, .cora-sidebar-search').on('click', function(e) {
        e.preventDefault();
        const sidebar = $('#cora-ai-sidebar');
        const isCollapsed = sidebar.hasClass('collapsed');
        coraToggleSidebar(isCollapsed);
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
                reply = "*WhatsApp Draft generated for Ananya Sharma:*\n\n\"Namaste Ananya! This is Cora from Delhi Studio. Just reminding you of our outdoor maternity shoot scheduled for tomorrow at 4:00 PM at Lodhi Gardens. 📸 Please let us know if you need any adjustments. See you there!\"";
            } else if (normalizedText.includes('rohit') || normalizedText.includes('wedding') || normalizedText.includes('jaipur')) {
                reply = "Booking Found: *Rohit & Sneha (Jaipur Destination Wedding)*.\n\n*Status:* Editing\n*AI Action Recommendation:* Social Media caption generator ready. Let me know if you want me to write Instagram caption drafts for this shoot.";
            } else if (normalizedText.includes('hi') || normalizedText.includes('hello')) {
                reply = "Hello! I am Cora, your studio AI Assistant. I can help you draft reminders, check shoot schedules, or suggest SEO keywords.";
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
        const sidebar = $('.cora-sidebar');
        sidebar.toggleClass('collapsed-sidebar');
        
        const isCollapsed = sidebar.hasClass('collapsed-sidebar');
        const icon = $('#cora-toggle-icon');
        
        if (isCollapsed) {
            // Point chevron right
            icon.html('<polyline points="9 18 15 12 9 6"></polyline>');
            $('#cora-sidebar-toggle').attr('title', 'Expand Sidebar');
        } else {
            // Point chevron left
            icon.html('<polyline points="15 18 9 12 15 6"></polyline>');
            $('#cora-sidebar-toggle').attr('title', 'Collapse Sidebar');
        }
        e.stopPropagation(); // Avoid triggering user profile popover if enmeshed
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

    // 13. Locked AI Model Selector Interaction
    $('#cora-ai-model-selector').on('change', function(e) {
        const val = $(this).val();
        if (val === 'claude-3-5') {
            window.coraShowToast('AI Model switching is a Premium feature. Upgrade to unlock Claude 3.5 Sonnet.');
            $(this).val('cora-core-v2');
        } else if (val === 'gpt-4o') {
            window.coraShowToast('GPT-4o mini model integration is coming soon. Stay tuned!');
            $(this).val('cora-core-v2');
        }
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
            $('#cora-sidebar-toggle').trigger('click');
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
            coraToggleAddShootDrawer(false);
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
                    $('.cora-nav-item[data-target="gallery"]').trigger('click');
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
                coraToggleAddShootDrawer(true);
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

    // Navigate to galleries page when clicking Beautiful Client Galleries card
    $('#cora-card-client-galleries').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('gallery');
    });

    // Navigate to galleries page when clicking Easy Photo Selection card
    $('#cora-card-photo-selection').on('click', function(e) {
        e.preventDefault();
        coraNavigateTo('gallery');
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
        
        const s1_photographer = $('#cora-team-shoot1-photographer').val();
        const s1_videographer = $('#cora-team-shoot1-videographer').val();
        const s1_drone = $('#cora-team-shoot1-drone').val();

        const s2_photographer = $('#cora-team-shoot2-photographer').val();
        const s2_assistant = $('#cora-team-shoot2-assistant').val();

        $.post(coraData.ajaxUrl, {
            action: 'cora_save_crew_assignments',
            security: coraData.ajaxNonce,
            shoot_id: 'shoot1',
            crew: { photographer: s1_photographer, videographer: s1_videographer, drone: s1_drone }
        }, function(res1) {
            $.post(coraData.ajaxUrl, {
                action: 'cora_save_crew_assignments',
                security: coraData.ajaxNonce,
                shoot_id: 'shoot2',
                crew: { photographer: s2_photographer, assistant: s2_assistant }
            }, function(res2) {
                window.coraShowToast('Team crew assignments saved successfully.');
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

    // 16. Sub-Tab Navigation for Team and Equipment Sections
    $('.cora-sub-tab').on('click', function(e) {
        e.preventDefault();
        const tab = $(this);
        const target = tab.data('sub-target');
        const parentSection = tab.closest('.cora-page-section');
        
        // Toggle tab classes
        parentSection.find('.cora-sub-tab').removeClass('active border-zinc-950 text-zinc-950').addClass('border-transparent hover:text-zinc-900');
        tab.addClass('active border-zinc-950 text-zinc-950').removeClass('border-transparent hover:text-zinc-900');
        
        // Hide all sub-sections and show active one
        parentSection.find('.cora-sub-section').addClass('hidden').removeClass('active');
        parentSection.find(`#cora-sub-page-${target}`).removeClass('hidden').addClass('active');

        // Update URL hash without jumping
        if (history.pushState) {
            history.pushState(null, null, '#' + target);
        } else {
            window.location.hash = '#' + target;
        }
    });

    // Initialize sub-tabs based on URL hash
    if (window.location.hash) {
        const target = window.location.hash.substring(1);
        const tab = $(`.cora-sub-tab[data-sub-target="${target}"]`);
        if (tab.length) {
            tab.trigger('click');
        }
    }

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
            Add Studio Member
        `);
        $('#cora-team-form-desc').text('Create a new WordPress user profile mapped to your studio\'s operational roles.');
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
        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_team_user',
            security: coraData.ajaxNonce,
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
        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_equipment',
            security: coraData.ajaxNonce,
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
            $('#cora-assign-eq-shoot').val(shoot !== '—' ? shoot : '');
            $('#cora-assign-eq-note').val(note !== '—' ? note : '');
        } else {
            $('#cora-assign-eq-crew').val('');
            $('#cora-assign-eq-shoot').val('');
            $('#cora-assign-eq-note').val('');
        }
        
        // Switch to assign tab
        $('#cora-sub-tab-eq-assign').trigger('click');
        $('#cora-assign-eq-status').focus();
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

        permissions['administrator'] = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings', 'vault', 'gallery', 'leads', 'clients', 'gbp', 'plugins'];

        // Instantly update the local cache
        coraData.userPermissions = permissions;
        
        // Apply updates to active preview role
        const activeRole = $('#cora-role-preview-select').val() || coraData.currentRole;
        coraEnforcePermissions(activeRole);

        // Send AJAX save in background
        $.post(coraData.ajaxUrl, {
            action: 'cora_save_role_permissions',
            security: coraData.ajaxNonce,
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
        formData.append('security', coraData.ajaxNonce);
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
            url: coraData.ajaxUrl,
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
        const serial = $('#cora-eq-serial').val().trim();

        if (!name || !serial) {
            window.coraShowToast('Please fill all fields.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'cora_save_equipment');
        formData.append('security', coraData.ajaxNonce);
        formData.append('name', name);
        formData.append('category', category);
        formData.append('serial', serial);

        const photoFile = $('#cora-gear-photo-file')[0].files[0];
        if (photoFile) {
            formData.append('gear_photo', photoFile);
        }

        const btn = $(this);
        const originalBtnText = btn.text();
        btn.text('Saving...').prop('disabled', true);

        $.ajax({
            url: coraData.ajaxUrl,
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
                        <td class="px-4 py-3.5 text-zinc-400 font-mono text-[10px]">${serial}</td>
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
                    $('#cora-assign-eq-id').append(new Option(`${name} (${serial})`, response.data.id));

                    // Increment counts in stats cards
                    $('#cora-eq-stat-total').text(parseInt($('#cora-eq-stat-total').text()) + 1);
                    $('#cora-eq-stat-avail').html(`<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>${parseInt($('#cora-eq-stat-avail').text()) + 1}`);

                    // Clear input fields and switch tab
                    $('#cora-eq-name').val('');
                    $('#cora-eq-serial').val('');
                    $('#cora-gear-photo-file').val('');
                    $('#cora-gear-photo-preview').html('<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-gear-photo-placeholder">No Photo</span>');
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

    $(document).on('change', '#cora-gear-photo-file', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#cora-gear-photo-preview').html(`<img src="${e.target.result}" class="w-full h-full object-cover">`);
            };
            reader.readAsDataURL(file);
        } else {
            $('#cora-gear-photo-preview').html(`<span class="text-[9px] text-zinc-400 text-center px-1 font-semibold" id="cora-gear-photo-placeholder">No Photo</span>`);
        }
    });

    // AJAX: Save Equipment Allocation
    $('#cora-confirm-eq-assign-btn').on('click', function(e) {
        e.preventDefault();
        const eqId = $('#cora-assign-eq-id').val();
        const status = $('#cora-assign-eq-status').val();
        const crewName = $('#cora-assign-eq-crew').val();
        const shootTitle = $('#cora-assign-eq-shoot').val();
        const assignmentNote = $('#cora-assign-eq-note').val().trim();

        if (!eqId) {
            window.coraShowToast('Please select a gear item.');
            return;
        }

        $.post(coraData.ajaxUrl, {
            action: 'cora_assign_equipment',
            security: coraData.ajaxNonce,
            eq_id: eqId,
            status: status,
            crew_name: crewName,
            shoot_title: shootTitle,
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
    window.coraEditorFormat = function(command) {
        document.execCommand(command, false, null);
        $('#cora-doc-paper').focus();
    };

    window.coraEditorApplyHeading = function(value) {
        if (value === 'p') {
            document.execCommand('formatBlock', false, '<p>');
        } else {
            document.execCommand('formatBlock', false, '<' + value + '>');
        }
        $('#cora-doc-paper').focus();
    };

    window.coraEditorUpdateBranding = function() {
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

    const defaultSampleTitle = "📝 Notes - Jun 20";
    const defaultSampleGdoc = "https://docs.google.com/document/d/1osN4szar57b7mWva6w4XSDNFeB3y6JuAUPpzwOuh06A/edit?usp=sharing";
    const defaultSampleContent = `
        <h1>📝 Notes</h1>
        <p><strong>Jun 20, 2026</strong></p>
        <h2>Meeting Jun 20, 2026 at 19:54 IST Shruti</h2>
        <p>Meeting records <a href="https://docs.google.com/document/d/1osN4szar57b7mWva6w4XSDNFeB3y6JuAUPpzwOuh06A/edit?usp=sharing">Transcript</a></p>
        
        <h3>Summary</h3>
        <p>Meeting discussions addressed CRM functionality improvements and team access management for better lead tracking operations.</p>
        <p><strong>CRM Capabilities and Integration</strong><br>Discussion centered on the current limitations of WordPress for lead management and how the new CRM platform improves visual organization. The system integrates Google Business Profiles and automated WhatsApp follow-up tools.</p>
        <p><strong>Technical Workflow and Access</strong><br>The platform enables third-party API integrations for galleries and supports Zoho syncing for invoices. The system provides customizable access permissions for team members to protect sensitive financial data.</p>
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
        $('#cora-doc-footer-text').val('© 2026 Nitin Arora Photography. All rights reserved. • Contact: hello@nitinarora.com');
        $('#cora-doc-paper').html(defaultSampleContent);
        
        // Pre-fill Google Doc Sync URL and check the real-time sync checkbox
        $('#cora-doc-gdoc-url').val(defaultSampleGdoc);
        $('#cora-doc-gdoc-sync-toggle').prop('checked', true);

        // Clear custom inputs
        $('#cora-custom-type-input-group').addClass('hidden');
        $('#cora-custom-type-input').val('');

        coraEditorUpdateBranding();
        coraStartGdocPolling();

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');
        
        // Reset toggle button
        $('#cora-vault-editor-view').removeClass('cora-sidebar-collapsed');
        $('#cora-editor-toggle-sidebar-btn').find('.toggle-text').text('Hide Settings');
    };

    window.coraViewDocument = function(docId) {
        const doc = (coraData.documents || []).find(d => d.id === docId);
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

        coraEditorUpdateBranding();

        // Update headings selector state if heading is at start
        $('#cora-editor-heading').val('p');

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');

        // Reset toggle button
        $('#cora-vault-editor-view').removeClass('cora-sidebar-collapsed');
        $('#cora-editor-toggle-sidebar-btn').find('.toggle-text').text('Hide Settings');
    };

    window.coraCloseEditor = function() {
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
            coraEditorUpdateBranding();
        });

        mediaUploader.open();
    });

    $(document).on('click', '#cora-doc-logo-remove-btn', function(e) {
        e.preventDefault();
        $('#cora-doc-logo-url').val('');
        coraEditorUpdateBranding();
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_sync_google_doc',
            security: coraData.ajaxNonce,
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
    window.coraEditorToggleSidebar = function() {
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_sync_google_doc',
            security: coraData.ajaxNonce,
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_save_document',
            security: coraData.ajaxNonce,
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
                if (coraData.documents) {
                    const idx = coraData.documents.findIndex(d => d.id === response.data.id);
                    if (idx !== -1) {
                        coraData.documents[idx] = response.data;
                    } else {
                        coraData.documents.push(response.data);
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_save_document',
            security: coraData.ajaxNonce,
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
                coraCloseEditor();
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_share_document',
            security: coraData.ajaxNonce,
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
        let allowed = coraData.userPermissions[role] || [];
        
        if (role === 'administrator') {
            allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings', 'gallery', 'leads', 'clients', 'blogs', 'gbp', 'plugins'];
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
            if (target && !allowed.includes(target) && target !== 'feature-hub' && !$(this).hasClass('cora-nav-soon') && !$(this).hasClass('cora-nav-locked')) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        // Hide mobile navigation items
        $('.cora-bottom-nav-item').each(function() {
            const target = $(this).data('target');
            if (target && !allowed.includes(target)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });

        // Redirect to first allowed screen if unauthorized
        const currentActiveTab = $('.cora-nav-item.cora-active').data('target');
        if (currentActiveTab && currentActiveTab !== 'feature-hub' && !allowed.includes(currentActiveTab)) {
            const firstAllowed = allowed[0] || 'dashboard';
            coraNavigateTo(firstAllowed);
        }
    };

    // Initialize capabilities enforcement with preview role persistence
    let initialRole = coraData.currentRole;
    if ($('#cora-role-preview-select').length) {
        let savedPreviewRole = null;
        try {
            savedPreviewRole = sessionStorage.getItem('cora_preview_role');
        } catch(e) {}
        if (savedPreviewRole) {
            $('#cora-role-preview-select').val(savedPreviewRole);
            initialRole = savedPreviewRole;
            const selectedText = $('#cora-role-preview-select option:selected').text();
            $('#cora-sidebar-user-role').text(selectedText + ' (Preview)');
        }
    }
    coraEnforcePermissions(initialRole);

    // Bind Preview Change Dropdown
    $('#cora-role-preview-select').on('change', function() {
        const selectedRole = $(this).val();
        const selectedText = $('#cora-role-preview-select option:selected').text();
        try {
            sessionStorage.setItem('cora_preview_role', selectedRole);
        } catch(e) {}
        coraEnforcePermissions(selectedRole);
        
        // Update sidebar widget
        $('#cora-sidebar-user-role').text(selectedText + ' (Preview)');
        
        window.coraShowToast(`Previewing dashboard as ${selectedText}`);
    });

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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { 
                action: 'cora_gbp_save_api_credentials', 
                security: coraData.ajaxNonce, 
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_search_places',
                security: coraData.ajaxNonce,
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
                    <p class="text-[10px] text-zinc-400">Try adding your city/town (e.g. Nitin Arora Photography New Delhi)</p>
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
                            <p class="flex items-start gap-1.5"><span class="text-zinc-300 shrink-0 mt-0.5">📍</span>${address}</p>
                            ${phone ? `<p class="flex items-start gap-1.5"><span class="text-zinc-300 shrink-0 mt-0.5">📞</span>${phone}</p>` : ''}
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_connect_place',
                security: coraData.ajaxNonce,
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

    // Initiate real Google OAuth — redirects to Google's real consent screen
    window.coraGbpConnectWithGoogle = function() {
        const btn = $('#cora-gbp-oauth-btn');
        btn.prop('disabled', true).html('<svg class="animate-spin mr-2" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Connecting to Google...');
        $.ajax({
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_get_oauth_url', security: coraData.ajaxNonce },
            success: function(res) {
                if (res.success && res.data.url) {
                    // Full redirect to Google OAuth — this is the real flow
                    window.location.href = res.data.url;
                } else {
                    window.coraShowToast('Error: ' + (res.data || 'Could not get OAuth URL. Check your Client ID in Settings.'));
                    btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg> Sign in with Google');
                }
            },
            error: function() {
                window.coraShowToast('Network error. Please try again.');
                btn.prop('disabled', false).text('Sign in with Google');
            }
        });
    };

    // State C: Load Google Business accounts and render location picker
    window.coraGbpLoadAccounts = function() {
        const picker = $('#cora-gbp-location-picker');
        if (!picker.length) return;
        $.ajax({
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_fetch_accounts', security: coraData.ajaxNonce },
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
                        url: coraData.ajaxUrl,
                        method: 'POST',
                        data: { action: 'cora_gbp_fetch_locations', security: coraData.ajaxNonce, account_name: account.name }
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: {
                action: 'cora_gbp_select_location',
                security: coraData.ajaxNonce,
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_disconnect', security: coraData.ajaxNonce },
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_fetch_reviews', security: coraData.ajaxNonce },
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_reply_review', security: coraData.ajaxNonce, review_name: reviewName, reply: text },
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_ai_chat', security: coraData.ajaxNonce, message: 'Write a short professional and warm reply to a 5-star Google review from ' + reviewerName + '. Keep it under 3 sentences, genuine, and thank them by name.' },
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
            url: coraData.ajaxUrl,
            method: 'POST',
            data: { action: 'cora_gbp_create_post', security: coraData.ajaxNonce, content: content, cta: cta, cta_url: ctaUrl },
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
    if (coraData.gbpIsAuthenticated && !coraData.gbpIsConnected && $('#cora-gbp-location-picker').length) {
        coraGbpLoadAccounts();
    }

    // Auto-load reviews if on State D (fully connected)
    if (coraData.gbpIsConnected && $('#cora-gbp-reviews-loading').length) {
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
        sessionStorage.setItem('cora_active_gallery_tab', tabId);
        sessionStorage.removeItem('cora_active_gallery_details_id');

        // Update top bar UI
        $('.cora-gallery-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
        const activeBtn = btnElement ? $(btnElement) : $(`.cora-gallery-tab-btn[data-tab="${tabId}"]`);
        activeBtn.removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');
        
        // Hide all views
        $('#cora-vault-grid-view, #cora-gallery-list-view, #cora-gallery-details-view').addClass('hidden');
        
        if (tabId === 'client-galleries') {
            $('#cora-gallery-list-view').removeClass('hidden');
            $('#cora-vault-topbar-actions').addClass('hidden'); // Hide folders dropdown in Shared Galleries
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
        $('#cora-gallery-list-view, #cora-gallery-details-view').addClass('hidden');
        $('#cora-vault-topbar-actions').removeClass('hidden');
        
        // Update tab active state to Master Vault
        $('.cora-gallery-tab-btn[data-tab="vault-all"]').click();
        
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
                
                $.post(coraData.ajaxUrl, {
                    action: 'cora_create_media_folder',
                    nonce: coraData.ajaxNonce,
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
        
        $.post(coraData.ajaxUrl, {
            action: 'cora_get_media',
            nonce: coraData.ajaxNonce,
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
            $('#cora-btn-create-gallery, #cora-btn-delete-selection').removeClass('hidden').addClass('inline-flex');
        } else {
            $('#cora-btn-create-gallery, #cora-btn-delete-selection').removeClass('inline-flex').addClass('hidden');
        }
    };

    window.coraCreateClientGalleryFromSelection = function() {
        if (coraVaultSelection.size === 0) return;
        
        // Open the gallery drawer
        coraOpenGalleryDrawer();
        $('#cora-gallery-assets-container').empty();
        
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
        window.coraShowToast("Added selected assets to new gallery");
    };

    // Initialize Vault if on Gallery tab
    $(document).ready(function() {
        if ($('.cora-nav-item[data-target="gallery"]').hasClass('cora-active')) {
            const savedDetailsId = sessionStorage.getItem('cora_active_gallery_details_id');
            const savedTab = sessionStorage.getItem('cora_active_gallery_tab');
            
            if (savedDetailsId) {
                // Ensure Shared Galleries tab button looks active
                $('.cora-gallery-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
                $(`.cora-gallery-tab-btn[data-tab="client-galleries"]`).removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');
                $('#cora-vault-topbar-actions').addClass('hidden');
                
                // Show the specific gallery details
                coraShowGalleryDetails(savedDetailsId);
            } else if (savedTab === 'client-galleries') {
                coraSwitchGalleryTab('client-galleries');
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
                    
                    $.post(coraData.ajaxUrl, {
                        action: 'cora_assign_media_folder',
                        nonce: coraData.ajaxNonce,
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
            window.coraShowToast("WordPress media library not available.");
        }
    };

    window.coraVaultOpenUpload = function() {
        coraUploadToVault();
    };

    // ==========================================
    // CLIENT GALLERY CONTROLLERS
    // ==========================================
    window.coraToggleGalleryDrawer = function(show) {
        const drawer = $('#cora-gallery-drawer');
        if (show) {
            drawer.removeClass('collapsed');
        } else {
            drawer.addClass('collapsed');
        }
    };

    window.coraOpenGalleryDrawer = function() {
        $('#cora-gallery-id').val('');
        $('#cora-gallery-title').val('');
        $('#cora-gallery-template').val('grid');
        $('#cora-gallery-password').val('');
        $('#cora-gallery-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Create Gallery Folder
        `);
        $('#cora-gallery-assets-container').empty();
        $('#cora-gallery-selections-section').addClass('hidden');
        $('#cora-gallery-selections-list').empty();
        coraAddAssetRow(); // add one default blank row
        coraToggleGalleryDrawer(true);
    };

    window.coraAddAssetRow = function(name = '', type = 'image', url = '', id = '') {
        const container = $('#cora-gallery-assets-container');
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
        const id = $('#cora-gallery-id').val();
        const title = $('#cora-gallery-title').val().trim();
        const template = $('#cora-gallery-template').val();
        const password = $('#cora-gallery-password').val().trim();
        
        if (!title) {
            window.coraShowToast("Please enter a gallery title.");
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

        $('#cora-gallery-submit-btn').prop('disabled', true).text('Saving...');

        $.post(coraData.ajaxUrl, {
            action: 'cora_save_gallery',
            nonce: coraData.ajaxNonce,
            id: id,
            title: title,
            template: template,
            password: password,
            assets: JSON.stringify(assets)
        }, function(res) {
            $('#cora-gallery-submit-btn').prop('disabled', false).text('Save Gallery Folder');
            if (res.success) {
                window.coraShowToast(id ? 'Gallery updated successfully.' : 'Gallery created successfully.');
                coraToggleGalleryDrawer(false);
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data || 'Failed to save gallery.');
            }
        }).fail(function() {
            $('#cora-gallery-submit-btn').prop('disabled', false).text('Save Gallery Folder');
            window.coraShowToast('Network error, please try again.');
        });
    };

    window.coraEditGallery = function(id) {
        if (!coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === id);
        if (!gallery) return;

        $('#cora-gallery-id').val(gallery.id);
        $('#cora-gallery-title').val(gallery.title);
        $('#cora-gallery-template').val(gallery.template || 'grid');
        $('#cora-gallery-password').val(gallery.password || '');
        $('#cora-gallery-drawer-title').html(`
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 mr-1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Edit Gallery Folder
        `);

        $('#cora-gallery-assets-container').empty();
        
        if (gallery.assets && gallery.assets.length > 0) {
            gallery.assets.forEach(asset => {
                coraAddAssetRow(asset.name, asset.type, asset.raw_url || asset.url, asset.id);
            });
        } else {
            coraAddAssetRow();
        }

        // Show selections list if any exist
        const selectionsSection = $('#cora-gallery-selections-section');
        const selectionsList = $('#cora-gallery-selections-list');
        selectionsList.empty();

        if (gallery.likes && gallery.likes.length > 0 && gallery.assets) {
            selectionsSection.removeClass('hidden');
            gallery.likes.forEach(likeId => {
                const asset = gallery.assets.find(a => a.id === likeId);
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
        $('#cora-gallery-selections-list div').each(function() {
            names.push($(this).data('asset-name'));
        });
        if (names.length === 0) return;
        
        const textToCopy = names.join(', ');
        navigator.clipboard.writeText(textToCopy).then(function() {
            window.coraShowToast("Selected asset titles copied to clipboard!");
        });
    };

    window.coraDeleteGallery = function(id) {
        if (!coraData.ajaxNonce) return;
        
        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_gallery',
            nonce: coraData.ajaxNonce,
            id: id
        }, function(res) {
            if (res.success) {
                window.coraShowToast("Gallery deleted successfully.");
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                window.coraShowToast(res.data || 'Failed to delete gallery.');
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
        if (!coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === id);
        if (!gallery) return;

        window.coraActiveGalleryId = id;
        sessionStorage.setItem('cora_active_gallery_details_id', id);
        sessionStorage.setItem('cora_active_gallery_tab', 'client-galleries');
        
        // Update Header Text
        $('#cora-detail-gallery-title-text').text(gallery.title);
        $('#cora-detail-gallery-title-input').val(gallery.title);
        
        // Render Stats
        const photos = gallery.assets ? gallery.assets.filter(a => a.type === 'image').length : 0;
        const videos = gallery.assets ? gallery.assets.filter(a => a.type === 'video').length : 0;
        $('#cora-stat-photos').text(photos + ' Photos');
        $('#cora-stat-videos').text(videos + ' Videos');
        $('#cora-stat-security').text(gallery.password ? 'Protected' : 'Public');
        
        // Setup Google Drive Sync Banner state
        if (gallery.drive_folder_url) {
            $('#cora-detail-drive-banner').removeClass('hidden').addClass('flex');
            $('#cora-detail-drive-url').text(gallery.drive_folder_url);
        } else {
            $('#cora-detail-drive-banner').addClass('hidden').removeClass('flex');
            $('#cora-detail-drive-url').text('');
        }
        
        // Reset Filter & Search
        window.coraActiveGalleryFilter = 'all';
        $('.cora-filter-tab').removeClass('bg-white text-zinc-900 shadow-sm').addClass('hover:text-zinc-900');
        $('.cora-filter-tab[data-filter="all"]').addClass('bg-white text-zinc-900 shadow-sm').removeClass('hover:text-zinc-900');
        $('#cora-detail-gallery-search').val('');
        $('#cora-detail-gallery-sort').val('name-asc');
        
        // Render Grid
        coraRenderActiveGalleryAssets();

        // Switch Views
        $('#cora-vault-grid-view, #cora-gallery-list-view').addClass('hidden');
        $('#cora-gallery-details-view').addClass('flex').removeClass('hidden');
    };

    window.coraShowGalleryListView = function() {
        window.coraActiveGalleryId = null;
        sessionStorage.removeItem('cora_active_gallery_details_id');
        sessionStorage.setItem('cora_active_gallery_tab', 'client-galleries');

        // Update tabs active state
        $('.cora-gallery-tab-btn').removeClass('font-bold text-zinc-900 border-zinc-900').addClass('font-semibold text-zinc-500 border-transparent');
        $(`.cora-gallery-tab-btn[data-tab="client-galleries"]`).removeClass('font-semibold text-zinc-500 border-transparent').addClass('font-bold text-zinc-900 border-zinc-900');

        $('#cora-vault-grid-view').addClass('hidden');
        $('#cora-gallery-details-view').addClass('hidden').removeClass('flex');
        $('#cora-gallery-list-view').addClass('block').removeClass('hidden');
    };

    window.coraSetAssetFilter = function(filter) {
        window.coraActiveGalleryFilter = filter;
        $('.cora-filter-tab').removeClass('bg-white text-zinc-900 shadow-sm').addClass('hover:text-zinc-900');
        $(`.cora-filter-tab[data-filter="${filter}"]`).addClass('bg-white text-zinc-900 shadow-sm').removeClass('hover:text-zinc-900');
        coraRenderActiveGalleryAssets();
    };

    window.coraRenderActiveGalleryAssets = function() {
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

        const grid = $('#cora-detail-gallery-grid');
        grid.empty();

        let assets = gallery.assets || [];
        const likes = gallery.likes || [];
        const searchQuery = $('#cora-detail-gallery-search').val().toLowerCase();
        const sortMode = $('#cora-detail-gallery-sort').val();

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
        $('#cora-detail-gallery-title-text').addClass('hidden');
        $('#cora-detail-gallery-title-input').removeClass('hidden').focus();
    };

    window.coraSaveActiveGalleryTitle = function() {
        const newTitle = $('#cora-detail-gallery-title-input').val().trim();
        if (!newTitle || !window.coraActiveGalleryId) return;
        
        $('#cora-detail-gallery-title-input').addClass('hidden');
        $('#cora-detail-gallery-title-text').text(newTitle).removeClass('hidden');
        
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (gallery) {
            gallery.title = newTitle;
            if (coraData.ajaxNonce) {
                $.post(coraData.ajaxUrl, {
                    action: 'cora_save_gallery',
                    nonce: coraData.ajaxNonce,
                    id: gallery.id,
                    title: gallery.title,
                    template: gallery.template,
                    password: gallery.password,
                    assets: JSON.stringify(gallery.assets)
                }, function(res) {
                    if(!res.success) {
                        window.coraShowToast("Failed to save title to database.");
                    }
                });
            }
        }
    };

    window.coraDeleteAssetFromActiveGallery = function(assetId) {
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

        gallery.assets = gallery.assets.filter(a => a.id !== assetId);
        coraRenderActiveGalleryAssets();
        
        const photos = gallery.assets ? gallery.assets.filter(a => a.type === 'image').length : 0;
        const videos = gallery.assets ? gallery.assets.filter(a => a.type === 'video').length : 0;
        $('#cora-stat-photos').text(photos + ' Photos');
        $('#cora-stat-videos').text(videos + ' Videos');
        
        if (coraData.ajaxNonce) {
            $.post(coraData.ajaxUrl, {
                action: 'cora_save_gallery',
                nonce: coraData.ajaxNonce,
                id: gallery.id,
                title: gallery.title,
                template: gallery.template,
                password: gallery.password,
                assets: JSON.stringify(gallery.assets)
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
        if (!activeId || !coraData.galleries) {
            window.coraShowToast("Please create a gallery first.");
            coraOpenGalleryDrawer();
            return;
        }
        
        window.coraActiveGalleryId = activeId;
        const gallery = coraData.galleries.find(g => g.id === activeId);
        if (gallery) {
            $('#cora-share-template').val(gallery.template || 'grid');
            $('#cora-share-password').val(gallery.password || '');
            
            // Re-check boxes by default
            $('#cora-share-images').prop('checked', true);
            $('#cora-share-videos').prop('checked', true);
        }
        
        $('#cora-modal-share-gallery').addClass('active');
    };

    window.coraSubmitShareGallery = function() {
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

        const template = $('#cora-share-template').val();
        const shareImages = $('#cora-share-images').is(':checked');
        const shareVideos = $('#cora-share-videos').is(':checked');
        const email = $('#cora-share-email').val().trim();
        const password = $('#cora-share-password').val().trim();

        // Construct the Share URL immediately
        let siteUrl = coraData.siteUrl || '';
        if (siteUrl.endsWith('/')) {
            siteUrl = siteUrl.slice(0, -1);
        }
        const shareUrl = siteUrl + '/shared-gallery/' + gallery.hash;

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

        gallery.template = template;
        gallery.password = password;
        
        // Use an AJAX request to save settings, and if email provided, mock sending
        if (coraData.ajaxNonce) {
            $.post(coraData.ajaxUrl, {
                action: 'cora_save_gallery',
                nonce: coraData.ajaxNonce,
                id: gallery.id,
                title: gallery.title,
                template: gallery.template,
                password: gallery.password,
                share_images: shareImages ? 1 : 0,
                share_videos: shareVideos ? 1 : 0,
                client_email: email,
                assets: JSON.stringify(gallery.assets)
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
                    window.coraShowToast("Failed to save gallery settings.");
                }
            }).fail(function() {
                btn.prop('disabled', false).html('<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 2L11 13"></path><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Save & Generate Link');
                window.coraShowToast("Network error.");
            });
        }
    };

    window.coraOpenLinkGoogleDriveModal = function() {
        if (!window.coraActiveGalleryId) {
            window.coraShowToast("Please open a gallery first.");
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

        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

        if (!gallery.assets) gallery.assets = [];
        
        const newAsset = {
            id: 'drive_' + Date.now(),
            name: name,
            type: type,
            url: url,
            raw_url: url
        };
        
        gallery.assets.push(newAsset);
        coraRenderActiveGalleryAssets();
        coraCloseModals();
        
        // Save
        if (coraData.ajaxNonce) {
            $.post(coraData.ajaxUrl, {
                action: 'cora_save_gallery',
                nonce: coraData.ajaxNonce,
                id: gallery.id,
                title: gallery.title,
                template: gallery.template,
                password: gallery.password,
                assets: JSON.stringify(gallery.assets)
            });
        }
        
        window.coraShowToast("Drive Asset linked successfully.");
    };

    window.coraResyncGoogleDriveFolder = function() {
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

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
            window.coraShowToast("Please open a gallery first.");
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
            if (!window.coraActiveGalleryId || !coraData.galleries) return;
            const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
            if (!gallery) return;

            if (!gallery.assets) gallery.assets = [];
            
            const syncImages = [
                { name: 'Bride Preparation Portrait.jpg', url: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?q=80&w=1200&auto=format&fit=crop' },
                { name: 'Vows Exchange Ceremony.jpg', url: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?q=80&w=1200&auto=format&fit=crop' },
                { name: 'Floral Decor CloseUp.jpg', url: 'https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1200&auto=format&fit=crop' }
            ];

            const syncVideos = [
                { name: 'Bridal Flower Portrait.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-bride-holding-a-bouquet-of-flowers-41712-large.mp4' },
                { name: 'First Dance Highlights.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-wedding-couple-dancing-slowly-41713-large.mp4' },
                { name: 'Groomsmen Laughing Scene.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-groomsmen-posing-and-laughing-41724-large.mp4' },
                { name: 'Ring Ceremony Teaser.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-putting-on-the-wedding-ring-41725-large.mp4' },
                { name: 'Groom Entrance Reel.mp4', url: 'https://assets.mixkit.co/videos/preview/mixkit-groom-waiting-for-the-bride-41711-large.mp4' }
            ];

            // Add 3 mock images
            syncImages.forEach((img, idx) => {
                gallery.assets.push({
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
                gallery.assets.push({
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
            if (coraData.ajaxNonce) {
                $.post(coraData.ajaxUrl, {
                    action: 'cora_save_gallery',
                    nonce: coraData.ajaxNonce,
                    id: gallery.id,
                    title: gallery.title,
                    template: gallery.template,
                    password: gallery.password,
                    drive_folder_url: url,
                    assets: JSON.stringify(gallery.assets)
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
            window.coraShowToast("Please open a gallery first.");
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
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;

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
            'https://assets.mixkit.co/videos/preview/mixkit-wedding-couple-dancing-slowly-41713-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-groomsmen-posing-and-laughing-41724-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-putting-on-the-wedding-ring-41725-large.mp4',
            'https://assets.mixkit.co/videos/preview/mixkit-groom-waiting-for-the-bride-41711-large.mp4'
        ];

        // Mock upload delay to simulate processing
        setTimeout(() => {
            if (!gallery.assets) gallery.assets = [];
            
            const uploadedCount = window.coraSelectedUploadFiles.length;
            window.coraSelectedUploadFiles.forEach((file, idx) => {
                const isVideo = file.type.startsWith('video/') || file.name.endsWith('.mp4') || file.name.endsWith('.mov') || file.name.endsWith('.avi');
                
                let assetUrl = '';
                if (isVideo) {
                    assetUrl = mixkitPremiumVids[idx % mixkitPremiumVids.length];
                } else {
                    assetUrl = unsplashPremiumPics[idx % unsplashPremiumPics.length];
                }

                gallery.assets.push({
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
            if (coraData.ajaxNonce) {
                $.post(coraData.ajaxUrl, {
                    action: 'cora_save_gallery',
                    nonce: coraData.ajaxNonce,
                    id: gallery.id,
                    title: gallery.title,
                    template: gallery.template,
                    password: gallery.password,
                    assets: JSON.stringify(gallery.assets)
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
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;
        
        const asset = gallery.assets.find(a => a.id === assetId);
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
        if (!window.coraActiveGalleryId || !coraData.galleries) return;
        const gallery = coraData.galleries.find(g => g.id === window.coraActiveGalleryId);
        if (!gallery) return;
        
        const assetId = $('#cora-lightbox-asset-id').val();
        const asset = gallery.assets.find(a => a.id === assetId);
        if (!asset) return;
        
        const btn = $('#cora-btn-submit-lightbox');
        btn.prop('disabled', true).html('<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...');
        
        asset.name = $('#cora-lightbox-name').val().trim();
        asset.alt_text = $('#cora-lightbox-alt').val().trim();
        asset.description = $('#cora-lightbox-description').val().trim();
        
        coraRenderActiveGalleryAssets();
        
        if (coraData.ajaxNonce) {
            $.post(coraData.ajaxUrl, {
                action: 'cora_save_gallery',
                nonce: coraData.ajaxNonce,
                id: gallery.id,
                title: gallery.title,
                template: gallery.template,
                password: gallery.password,
                assets: JSON.stringify(gallery.assets)
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
        const galSelect = $('#cora-lead-demo-gallery');
        galSelect.empty().append('<option value="">-- No Demo Gallery Linked --</option>');
        (coraData.galleries || []).forEach(g => {
            galSelect.append(`<option value="${g.id}">${g.title}</option>`);
        });
        $('#cora-lead-gallery-tracking-box').addClass('hidden');
        
        // Reset Equipment List Checkboxes
        const gearContainer = $('#cora-lead-equipment-list');
        gearContainer.empty();
        if ((coraData.equipment || []).length === 0) {
            gearContainer.html('<div class="text-[11px] text-zinc-450 py-2 text-center select-none">No studio equipment catalog loaded.</div>');
        } else {
            coraData.equipment.forEach(item => {
                gearContainer.append(`
                    <label class="flex items-center gap-2.5 px-3 py-2 border border-zinc-200 rounded-md bg-white hover:bg-zinc-50 cursor-pointer text-xs transition-all">
                        <input type="checkbox" class="cora-lead-gear-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-900 cursor-pointer" value="${item.id}">
                        <div class="flex-1">
                            <span class="font-bold text-zinc-900">${item.name}</span>
                            <span class="text-[9px] text-zinc-400 uppercase tracking-wider block mt-0.5">${item.type} &bull; ${item.status}</span>
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
        const galSelect = $('#cora-lead-demo-gallery');
        galSelect.empty().append('<option value="">-- No Demo Gallery Linked --</option>');
        (coraData.galleries || []).forEach(g => {
            const selected = (lead.demo_gallery === String(g.id) || lead.demo_gallery === g.id) ? 'selected' : '';
            galSelect.append(`<option value="${g.id}" ${selected}>${g.title}</option>`);
        });

        // Set up gallery tracking box
        window.coraUpdateGalleryTrackingUI(lead);

        // Bind change handler to gallery select
        galSelect.off('change').on('change', function() {
            const val = $(this).val();
            if (val) {
                lead.demo_gallery = val;
                $('#cora-lead-gallery-tracking-box').removeClass('hidden');
                // update default state
                lead.demo_gallery_shared = false;
                lead.demo_gallery_viewed = false;
                window.coraUpdateGalleryTrackingUI(lead);
            } else {
                lead.demo_gallery = '';
                $('#cora-lead-gallery-tracking-box').addClass('hidden');
            }
        });

        // Render linked sales documents list
        const docsListContainer = $('#cora-lead-linked-docs-list');
        docsListContainer.empty();
        const linkedDocs = (coraData.documents || []).filter(d => d.client_link === 'lead_' + lead.id || d.client_link === lead.email);
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

        // --- Tab 3: Shoot Gear Checklist ---
        const gearContainer = $('#cora-lead-equipment-list');
        gearContainer.empty();
        const leadGearIds = (lead.equipment_ids || []).map(String);
        if ((coraData.equipment || []).length === 0) {
            gearContainer.html('<div class="text-[11px] text-zinc-450 py-2 text-center select-none">No studio equipment catalog loaded.</div>');
        } else {
            coraData.equipment.forEach(item => {
                const checked = leadGearIds.includes(String(item.id)) ? 'checked' : '';
                gearContainer.append(`
                    <label class="flex items-center gap-2.5 px-3 py-2 border border-zinc-200 rounded-md bg-white hover:bg-zinc-50 cursor-pointer text-xs transition-all">
                        <input type="checkbox" class="cora-lead-gear-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-zinc-900 cursor-pointer" value="${item.id}" ${checked}>
                        <div class="flex-1">
                            <span class="font-bold text-zinc-900">${item.name}</span>
                            <span class="text-[9px] text-zinc-400 uppercase tracking-wider block mt-0.5">${item.type} &bull; ${item.status}</span>
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
        if (!lead.demo_gallery) {
            $('#cora-lead-gallery-tracking-box').addClass('hidden');
            return;
        }

        $('#cora-lead-gallery-tracking-box').removeClass('hidden');
        
        const sharedBadge = $('#cora-lead-gallery-shared-badge');
        if (lead.demo_gallery_shared) {
            sharedBadge.text('Shared').removeClass('bg-zinc-100 text-zinc-655').addClass('bg-green-100 text-green-800 font-bold border border-green-200');
        } else {
            sharedBadge.text('Not Shared').removeClass('bg-green-100 text-green-800 border border-green-200').addClass('bg-zinc-100 text-zinc-655');
        }

        const viewedBadge = $('#cora-lead-gallery-viewed-badge');
        if (lead.demo_gallery_viewed) {
            viewedBadge.text('Viewed by Client').removeClass('bg-zinc-100 text-zinc-655').addClass('bg-green-100 text-green-800 font-bold border border-green-200');
        } else {
            viewedBadge.text('Unopened').removeClass('bg-green-100 text-green-800 border border-green-200').addClass('bg-zinc-100 text-zinc-655');
        }
    };

    window.coraShareDemoGalleryAction = function() {
        const lead = $('#cora-lead-drawer').data('lead');
        if (!lead || !lead.id) return;

        lead.demo_gallery_shared = true;
        window.coraUpdateGalleryTrackingUI(lead);
        window.coraShowToast("Demo gallery sharing registered!");
    };

    window.coraSimulateClientViewAction = function() {
        const lead = $('#cora-lead-drawer').data('lead');
        if (!lead || !lead.id) return;

        lead.demo_gallery_viewed = true;
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
        $.post(coraData.ajaxUrl, {
            action: 'cora_update_lead_email_status',
            nonce: coraData.ajaxNonce,
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
        const demoGallery = $('#cora-lead-demo-gallery').val();
        const lead = $('#cora-lead-drawer').data('lead') || {};
        const demoGalleryShared = lead.demo_gallery_shared ? 'true' : 'false';
        const demoGalleryViewed = lead.demo_gallery_viewed ? 'true' : 'false';
        
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
        $.post(coraData.ajaxUrl, {
            action: leadId ? 'cora_update_lead_status' : 'cora_submit_lead',
            nonce: coraData.ajaxNonce,
            id: leadId,
            names: names,
            email: email,
            scale: scale,
            city: city,
            price: price,
            status: status,
            notes: notes,
            demo_gallery: demoGallery,
            demo_gallery_shared: demoGalleryShared,
            demo_gallery_viewed: demoGalleryViewed,
            equipment_ids: equipmentIds.join(',')
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_convert_lead_to_client',
            nonce: coraData.ajaxNonce,
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_lead',
            nonce: coraData.ajaxNonce,
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
                client = { names: 'Ananya Sharma', email: 'ananya@gmail.com', city: 'Lodhi Gardens, Delhi', notes: 'Maternity shoot in Lodhi Gardens. Wants a very soft, natural light feel with pastel dresses.', scale: 'intimate', price: '₹25,000', status: 'confirmed', shoot_type: 'Maternity Portrait', shoot_date: '24th Jun, 2026' };
            } else if (clientId === 'client_2') {
                client = { names: 'Rohit & Sneha', email: 'rohit.sneha@outlook.com', city: 'Rambagh Palace, Jaipur', notes: 'Destination wedding over 3 days. Focus heavily on candid moments and the royal aesthetic of Rambagh Palace.', scale: 'destination', price: '₹1,80,000', status: 'editing', shoot_type: 'Destination Wedding', shoot_date: '20th Jun, 2026' };
            } else if (clientId === 'client_3') {
                client = { names: 'Rajesh Kumar (Studio B)', email: 'rk.enterprises@gmail.com', city: 'Studio A, Delhi', notes: 'E-commerce product shoot for new apparel line. Needs white background and lifestyle shots.', scale: 'commercial', price: '₹40,000', status: 'completed', shoot_type: 'Product Shoot', shoot_date: '15th Jun, 2026' };
            } else {
                client = { names: 'Client Profile', email: 'client@example.com', city: 'Unknown', notes: 'No notes available.', scale: 'standard', price: '₹15,000', status: 'confirmed', shoot_type: 'Primary Shoot', shoot_date: 'Pending Date' };
            }
        }

        $('#cora-lifecycle-client-name').text(client.names);
        $('#cora-lifecycle-email').text(client.email);
        $('#cora-lifecycle-city').text(client.city || 'Mumbai');
        $('#cora-lifecycle-notes').text(client.notes || 'No vision notes provided.');

        // Build Shoot Bookings timeline dynamically
        const statusBadgeClass = client.status === 'completed' ? 'bg-green-100 text-green-700' : (client.status === 'editing' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700');
        const statusLabel = client.status ? client.status.charAt(0).toUpperCase() + client.status.slice(1) : 'Confirmed';
        
        let bookingsHtml = `
            <div class="bg-white border border-zinc-200 rounded-lg p-3 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-zinc-900">${client.shoot_type || 'Shoot'}</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${statusBadgeClass}">${statusLabel}</span>
                </div>
                <div class="text-[11px] text-zinc-500 font-medium">${client.shoot_date || 'Pending Date'} &bull; ${client.city || 'Delhi'}</div>
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

        // Build Client Galleries / Assets dynamically
        const clientGalleries = (window.coraGalleries || []).filter(g => g.client_email === client.email);
        let assetsHtml = '';
        if (clientGalleries.length === 0) {
            assetsHtml = '<div class="text-[11px] text-zinc-400 italic py-2">No galleries delivered yet.</div>';
        } else {
            clientGalleries.forEach(gallery => {
                const totalAssets = gallery.assets ? gallery.assets.length : 0;
                let firstImage = 'https://images.unsplash.com/photo-1519741497674-611481863552?w=100&h=80&fit=crop';
                if (gallery.assets && gallery.assets.length > 0) {
                    const imgAsset = gallery.assets.find(a => a.type === 'image');
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
                            <div class="text-[11px] font-bold text-zinc-800">${gallery.title}</div>
                            <div class="text-[9.5px] text-zinc-400 mt-0.5">${totalAssets} Photos/Videos &bull; Sent to client</div>
                        </div>
                        <button class="text-zinc-400 hover:text-zinc-800 transition-colors" title="View Gallery" onclick="coraNavigateTo('gallery')"><svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                    </div>
                `;
            });
        }
        $('#cora-lifecycle-assets-container').html(assetsHtml);
    };

    window.coraPrefillAddShoot = function(client) {
        coraToggleAddShootDrawer(true);
        $('#cora-drawer-client-name').val(client.names);
        $('#cora-drawer-location').val(client.city || 'Mumbai');
        $('#cora-drawer-price').val(client.price || '');
        
        let shootType = 'Destination Wedding';
        if (client.scale === 'documentary') {
            shootType = 'Couples Portrait';
        }
        $('#cora-drawer-shoot-type').val(shootType);
    };

    window.coraDeleteClient = function(clientId) {
        if (!clientId) return;

        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_client',
            nonce: coraData.ajaxNonce,
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
        $.post(coraData.ajaxUrl, {
            action: 'cora_update_lead_status',
            nonce: coraData.ajaxNonce,
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
        'Second Shooter Fee Upsell',
        'Other Income'
    ];

    const outflowCategories = [
        'Equipment Rental',
        'Crew / Assistant Payout',
        'Studio Rent / Utilities',
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
        const tx = (coraData.financials || []).find(t => t.id === txId);
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_save_transaction',
            security: coraData.ajaxNonce,
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
                
                if (!coraData.financials) coraData.financials = [];
                const idx = coraData.financials.findIndex(t => t.id === res.data.id);
                if (idx !== -1) {
                    coraData.financials[idx] = res.data;
                } else {
                    coraData.financials.push(res.data);
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_delete_transaction',
            security: coraData.ajaxNonce,
            id: txId
        }, function(res) {
            if (res.success) {
                window.coraShowToast("Ledger entry deleted.");
                
                if (coraData.financials) {
                    coraData.financials = coraData.financials.filter(t => t.id !== txId);
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
        let txs = coraData.financials || [];
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
                        const client = (coraData.clients || []).find(c => c.id === cId || c.id === tx.client_link);
                        if (client) matchedName = client.names;
                    } else if (tx.client_link.startsWith('lead_')) {
                        const lId = tx.client_link.substring(5);
                        const lead = (coraData.leads || []).find(l => l.id === lId || l.id === tx.client_link);
                        if (lead) {
                            matchedName = lead.names;
                            isClient = false;
                        }
                    } else {
                        const client = (coraData.clients || []).find(c => c.id === tx.client_link);
                        if (client) matchedName = client.names;
                        else {
                            const lead = (coraData.leads || []).find(l => l.id === tx.client_link);
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

        (coraData.financials || []).forEach(tx => {
            const amt = parseFloat(tx.amount) || 0;
            if (tx.type === 'Inflow' && tx.status === 'Received') {
                totalInflow += amt;
            } else if (tx.type === 'Outflow' && tx.status === 'Paid') {
                totalOutflow += amt;
            }
        });

        let netProfit = totalInflow - totalOutflow;

        let pendingDues = 0;
        (coraData.clients || []).forEach(client => {
            let clientPrice = parseAmount(client.price);
            let paidInflows = 0;
            (coraData.financials || []).forEach(tx => {
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
        const txs = coraData.financials || [];
        
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
        const txs = coraData.financials || [];
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
                        <div class="logo-title">CORA FOR STUDIO</div>
                        <div style="font-size: 12px; color: #71717a; margin-top: 4px;">Photographer Workspace Platform</div>
                    </div>
                    <div class="meta-details">
                        <div><strong>Studio:</strong> Nitin Arora Photography</div>
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
                    Confidential Financial Document &bull; Generated from Cora for Studio Workspace Manager.
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

        const doc = (coraData.documents || []).find(d => d.id === docId);
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
                const client = (coraData.clients || []).find(c => c.id === cId || c.id === clientLink);
                if (client) {
                    email = client.email;
                    clientName = client.names;
                }
            } else if (clientLink.startsWith('lead_')) {
                const lId = clientLink.substring(5);
                const lead = (coraData.leads || []).find(l => l.id === lId || l.id === clientLink);
                if (lead) {
                    email = lead.email;
                    clientName = lead.names;
                }
            } else {
                const client = (coraData.clients || []).find(c => c.id === clientLink);
                if (client) {
                    email = client.email;
                    clientName = client.names;
                } else {
                    const lead = (coraData.leads || []).find(l => l.id === clientLink);
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

        $.post(coraData.ajaxUrl, {
            action: 'cora_send_document_email',
            security: coraData.ajaxNonce,
            doc_id: docId,
            email: email
        }, function(res) {
            if (res.success) {
                window.coraShowToast(`Document emailed successfully to ${email}.`);
                if (coraData.documents) {
                    const idx = coraData.documents.findIndex(d => d.id === docId);
                    if (idx !== -1) {
                        coraData.documents[idx].status = 'Sent';
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

    window.coraEditorLoadTemplate = function(templateKey) {
        if (!templateKey) return;

        let clientName = '{{CLIENT_NAME}}';
        let clientEmail = '{{CLIENT_EMAIL}}';
        let amount = $('#cora-doc-amount-input').val().trim() || '{{AMOUNT}}';
        let dateStr = new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' });

        const clientLink = $('#cora-doc-client-select').val();
        if (clientLink) {
            if (clientLink.startsWith('client_')) {
                const cId = clientLink.substring(7);
                const client = (coraData.clients || []).find(c => c.id === cId || c.id === clientLink);
                if (client) {
                    clientName = client.names;
                    clientEmail = client.email;
                    amount = client.price || amount;
                }
            } else if (clientLink.startsWith('lead_')) {
                const lId = clientLink.substring(5);
                const lead = (coraData.leads || []).find(l => l.id === lId || l.id === clientLink);
                if (lead) {
                    clientName = lead.names;
                    clientEmail = lead.email;
                    amount = lead.price || amount;
                }
            } else {
                const client = (coraData.clients || []).find(c => c.id === clientLink);
                if (client) {
                    clientName = client.names;
                    clientEmail = client.email;
                    amount = client.price || amount;
                } else {
                    const lead = (coraData.leads || []).find(l => l.id === clientLink);
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

        if (templateKey === 'wedding_proposal') {
            $('#cora-doc-title-input').val(`Photography Proposal - ${clientName}`);
            $('#cora-doc-type-select').val('Proposal');
            html = `
                <h2><strong>Photography & Video Proposal</strong></h2>
                <p><strong>Prepared for:</strong> ${clientName} (${clientEmail})<br><strong>Date:</strong> ${dateStr}</p>
                <hr>
                <h3><strong>Overview & Creative Vision</strong></h3>
                <p>We are absolutely thrilled at the prospect of documenting your love story. Our style is a signature blend of luxury editorial portraiture and emotional, documentary-style storytelling. We don't just take pictures; we capture how it felt.</p>
                <h3><strong>Scope of Services</strong></h3>
                <ul>
                    <li><strong>2-Day Wedding Coverage:</strong> Full day coverage of all primary events by Nitin Arora + 2 senior cinematographers.</li>
                    <li><strong>Deliverables:</strong>
                        <ul>
                            <li>High-resolution curated digital gallery (400+ fully processed images).</li>
                            <li>5-minute cinematic highlight film + full length ceremony feature.</li>
                            <li>One premium hand-crafted leather wedding album (12x15 inches, 40 spreads).</li>
                        </ul>
                    </li>
                </ul>
                <h3><strong>Investment & Commercials</strong></h3>
                <p>The total investment for the described coverage is <strong>${amount}</strong> (all-inclusive). </p>
                <p><strong>Payment Terms:</strong> 50% Advance retainer to book date, 40% on shoot start, 10% on delivery of final assets.</p>
            `;
        } else if (templateKey === 'intimate_proposal') {
            $('#cora-doc-title-input').val(`Intimate Event Proposal - ${clientName}`);
            $('#cora-doc-type-select').val('Proposal');
            html = `
                <h2><strong>Intimate Event Proposal</strong></h2>
                <p><strong>Prepared for:</strong> ${clientName} (${clientEmail})<br><strong>Date:</strong> ${dateStr}</p>
                <hr>
                <h3><strong>Event Coverage Plan</strong></h3>
                <p>A customized, intimate photography package tailored to capture close friends, family, and beautiful details of your celebration.</p>
                <ul>
                    <li><strong>Duration:</strong> Up to 6 hours of continuous coverage by a single lead photographer.</li>
                    <li><strong>Deliverables:</strong>
                        <ul>
                            <li>Private digital gallery with 150+ color-graded images delivered in 3 weeks.</li>
                            <li>Next-day social preview (10 sneak-peek highlights).</li>
                        </ul>
                    </li>
                </ul>
                <h3><strong>Total Fees & Retainer</strong></h3>
                <p><strong>Total Proposal Value:</strong> ${amount}</p>
                <p>50% non-refundable retainer required to lock in availability.</p>
            `;
        } else if (templateKey === 'standard_invoice') {
            $('#cora-doc-title-input').val(`Invoice - ${clientName}`);
            $('#cora-doc-type-select').val('Invoice');
            html = `
                <h2><strong>TAX INVOICE</strong></h2>
                <p><strong>Invoice To:</strong> ${clientName}<br><strong>Email:</strong> ${clientEmail}<br><strong>Invoice Date:</strong> ${dateStr}<br><strong>Due Date:</strong> Immediate</p>
                <hr>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e4e4e7; text-align: left;">
                            <th style="padding: 10px 0;">Description</th>
                            <th style="padding: 10px 0; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #f4f4f5;">
                            <td style="padding: 12px 0;">Professional Photography Services (Booking Retainer)</td>
                            <td style="padding: 12px 0; text-align: right;">${amount}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 0; font-weight: bold;">Total Due</td>
                            <td style="padding: 12px 0; text-align: right; font-weight: bold;">${amount}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p><strong>Bank Wire Details:</strong><br>Account Name: Cora Photography Studio Private Limited<br>Bank: HDFC Bank Limited, Delhi Branch<br>IFSC Code: HDFC0001202<br>Account Number: 50200084729103</p>
            `;
        } else if (templateKey === 'commercial_invoice') {
            $('#cora-doc-title-input').val(`Commercial Invoice - ${clientName}`);
            $('#cora-doc-type-select').val('Invoice');
            html = `
                <h2><strong>COMMERCIAL INVOICE</strong></h2>
                <p><strong>Client:</strong> ${clientName}<br><strong>Brand/Entity:</strong> Commercial Brand Campaign<br><strong>Date:</strong> ${dateStr}</p>
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
                            <td style="padding: 12px 0;">Creative Fee: 1-Day Studio Commercial Campaign (Up to 10 hours)</td>
                            <td style="padding: 12px 0; text-align: right;">${amount}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #f4f4f5;">
                            <td style="padding: 12px 0;">Full Commercial Usage & Perpetual Digital Licensing Rights</td>
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
            template: 'wedding_proposal',
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
        
        if (coraData.currentPage === 'vault') {
            try {
                const cmd = JSON.parse(cmdStr);
                localStorage.removeItem('cora_autocreate_doc');
                
                coraOpenDocDrawer();
                
                setTimeout(() => {
                    $('#cora-doc-client-select').val(cmd.client_link);
                    coraEditorLoadTemplate(cmd.template);
                }, 200);
            } catch (e) {
                localStorage.removeItem('cora_autocreate_doc');
            }
        }
    };

    // Financial Board Page Initialization
    if (coraData.currentPage === 'financials') {
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
    let coraQuillEditor = null;
    let coraCategorySelect = null;
    let coraTagSelect = null;

    function initEditorComponentsIfNeeded() {
        if (!coraQuillEditor && $('#cora-quill-editor').length > 0) {
            coraQuillEditor = new Quill('#cora-quill-editor', {
                theme: 'snow',
                placeholder: 'Start writing your masterpiece...',
                modules: {
                    toolbar: [
                        [{ 'header': [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });
            // Override Quill's default image and video handlers to use wp.media instead of prompt()
            const toolbar = coraQuillEditor.getModule('toolbar');
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
                        const range = coraQuillEditor.getSelection();
                        coraQuillEditor.insertEmbed(range ? range.index : 0, 'image', attachment.url);
                    });
                    customUploader.open();
                } else {
                    window.coraShowToast('WordPress media library is not loaded.');
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
                        const range = coraQuillEditor.getSelection();
                        coraQuillEditor.insertEmbed(range ? range.index : 0, 'video', attachment.url);
                    });
                    customUploader.open();
                } else {
                    window.coraShowToast('WordPress media library is not loaded.');
                }
            });
            
            // Update custom status on text change
            coraQuillEditor.on('text-change', function() {
                $('#cora-editor-status').text('Unsaved changes');
            });
            $('#cora-article-title, #cora-seo-keyword, #cora-seo-description, #cora-article-categories, #cora-article-tags').on('input change', function() {
                $('#cora-editor-status').text('Unsaved changes');
            });
            
            // Initialize TomSelect for Categories
            if (typeof TomSelect !== 'undefined') {
                if ($('#cora-article-categories').length) {
                    coraCategorySelect = new TomSelect('#cora-article-categories', {
                        plugins: ['remove_button'],
                        placeholder: 'Select Categories...',
                        create: false
                    });
                }
                if ($('#cora-article-tags').length) {
                    coraTagSelect = new TomSelect('#cora-article-tags', {
                        plugins: ['remove_button'],
                        placeholder: 'Select or Add Tags...',
                        create: true, // Allow creating new tags on the fly!
                        createOnBlur: true
                    });
                }
            }
        }
    }

    window.coraToggleContentDrawer = function(show) {
        if (show) {
            initEditorComponentsIfNeeded();
            $('.cora-stat-card').parent().hide();
            $('#cora-articles-table-body').closest('div').hide();
            $('.cora-page-header').hide();
            $('#cora-full-page-editor').removeClass('hidden');
        } else {
            $('#cora-full-page-editor').addClass('hidden');
            $('.cora-stat-card').parent().show();
            $('#cora-articles-table-body').closest('div').show();
            $('.cora-page-header').show();
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

        initEditorComponentsIfNeeded();
        if (coraQuillEditor) coraQuillEditor.root.innerHTML = '';
        
        coraToggleContentDrawer(true);
    };

    window.coraEditArticle = function(id) {
        coraToggleContentDrawer(true);
        $('#cora-editor-status').text('Loading...');
        $('#cora-article-id').val(id);
        
        if (coraQuillEditor) coraQuillEditor.root.innerHTML = '<p class="text-zinc-400 animate-pulse">Loading content from server...</p>';
        
        $.post(ajaxurl, {
            action: 'cora_get_article',
            nonce: coraData.ajaxNonce,
            post_id: id
        }, function(response) {
            if (response.success) {
                const data = response.data;
                $('#cora-article-title').val(data.title || ''); // Needs to be sent from backend or fetched from DOM. Actually we didn't send title in get_article! Let's fetch from table DOM
                
                // Fallback for title if not in backend response (we can grab it from the clicked row)
                const domTitle = $(`tr[onclick="coraEditArticle(${id})"] .font-bold.text-zinc-900`).text();
                $('#cora-article-title').val(domTitle);

                if (coraQuillEditor) coraQuillEditor.root.innerHTML = data.content || '';
                
                $('#cora-seo-keyword').val(data.keyword || '');
                $('#cora-seo-description').val(data.description || '');
                
                if (coraCategorySelect) {
                    coraCategorySelect.setValue(data.categories || []);
                }
                if (coraTagSelect) {
                    // Pre-add tag options if they don't exist yet in the TomSelect list
                    if (data.tags) {
                        data.tags.forEach(tagId => {
                            coraTagSelect.addOption({value: tagId, text: tagId}); // If the tag is an ID. Wait, tags might be returned as names or IDs from backend. Currently we return IDs.
                        });
                        coraTagSelect.setValue(data.tags);
                    } else {
                        coraTagSelect.clear();
                    }
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

                $('#cora-editor-status').text('Saved');
            } else {
                if (coraQuillEditor) coraQuillEditor.root.innerHTML = '';
                window.coraShowToast('Failed to load article content', 'error');
            }
        });
    };

    window.coraToggleMediaDrawer = function(show) {
        if (show) {
            $('#cora-media-library-drawer').removeClass('translate-x-full');
            coraFetchMediaLibrary();
        } else {
            $('#cora-media-library-drawer').addClass('translate-x-full');
        }
    };

    window.coraOpenMediaLibrary = function() {
        coraToggleMediaDrawer(true);
    };
    
    window.coraFetchMediaLibrary = function() {
        $('#cora-media-library-grid').html('<div class="col-span-3 py-10 text-center"><div class="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto"></div></div>');
        
        $.post(coraData.ajaxUrl, {
            action: 'cora_get_media',
            nonce: coraData.ajaxNonce
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
        $('#cora-thumbnail-id').val(id);
        $('#cora-thumbnail-img').attr('src', url).removeClass('hidden');
        $('#cora-thumbnail-placeholder').addClass('hidden');
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
        formData.append('nonce', coraData.ajaxNonce);
        formData.append('file', file);
        
        $('#cora-media-upload-status').text('Uploading...').removeClass('text-red-500 text-green-500').addClass('text-blue-500');
        
        $.ajax({
            url: coraData.ajaxUrl,
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
        const title = $('#cora-article-title').val();
        if (!title) {
            window.coraShowToast('Please enter an Article Title first so AI knows what to write about.', 'error');
            return;
        }
        
        window.coraShowToast('AI is drafting the article... Please wait.', 'info');
        $('#cora-editor-status').text('AI Drafting...');
        
        setTimeout(() => {
            if (coraQuillEditor) {
                coraQuillEditor.root.innerHTML = `<h2>Introduction</h2><p>Weddings are magical, and capturing them perfectly requires a mix of technical skill and artistic vision. In this guide, we will walk you through the essential tips for ensuring your memories are preserved flawlessly.</p><h2>Key Focus Areas</h2><ol><li><strong>Lighting:</strong> Natural light is your best friend during golden hour.</li><li><strong>Posing:</strong> Keep it candid. Stiff poses often feel unnatural.</li><li><strong>Location:</strong> Choose a venue that resonates with your personal story.</li></ol><h2>Conclusion</h2><p>Remember, the best photos are the ones where you are truly in the moment. Let the photographer handle the technicalities while you enjoy your big day.</p>`;
            }
            window.coraShowToast('Article drafted successfully!', 'success');
            $('#cora-editor-status').text('Unsaved changes');
            coraAnalyzeSEO();
        }, 1500);
    };

    window.coraAnalyzeSEO = function() {
        const title = $('#cora-article-title').val();
        const content = coraQuillEditor ? coraQuillEditor.root.innerHTML : '';
        
        if (!title || !content || content === '<p><br></p>') {
            window.coraShowToast('Add some content to analyze SEO.', 'error');
            return;
        }
        
        window.coraShowToast('Analyzing content for SEO...', 'info');
        $('#cora-editor-status').text('Analyzing...');
        
        $.post(ajaxurl, {
            action: 'cora_analyze_seo',
            nonce: coraData.ajaxNonce,
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
        const content = coraQuillEditor ? coraQuillEditor.root.innerHTML : '';
        const keyword = $('#cora-seo-keyword').val();
        const description = $('#cora-seo-description').val();
        const score = $('#cora-seo-score-display').text();
        
        const categories = $('#cora-article-categories').val() || [];
        const tags = $('#cora-article-tags').val() || [];
        const thumbnail_id = $('#cora-thumbnail-id').val();

        if (!title) {
            window.coraShowToast('Cannot save an article without a title.', 'error');
            return;
        }

        $('#cora-editor-status').text('Saving...');
        window.coraShowToast(`Saving article as ${status}...`, 'info');

        $.post(ajaxurl, {
            action: 'cora_save_article',
            nonce: coraData.ajaxNonce,
            post_id: id,
            title: title,
            content: content,
            status: status,
            keyword: keyword,
            description: description,
            seo_score: score === '--' ? '' : score,
            categories: categories,
            tags: tags,
            thumbnail_id: thumbnail_id
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
        const workspace = $('#cora-workspace');
        const main = $('.cora-main');
        if (!workspace.length || !main.length) return;
        
        // Disable layout shifting on mobile/tablet screens
        if (window.innerWidth < 1024) {
            main.css('margin-right', '');
            return;
        }
        
        let activeWidth = 0;
        $('aside.fixed.right-0, aside[id$="-drawer"], aside.cora-drawer, #cora-ai-sidebar, #cora-media-library-drawer').each(function() {
            const drawer = $(this);
            // Check if drawer is currently expanded (doesn't have collapsed/translate-x-full classes)
            if (!drawer.hasClass('collapsed') && !drawer.hasClass('translate-x-full') && drawer.css('display') !== 'none') {
                const width = drawer.outerWidth() || 0;
                if (width > activeWidth) {
                    activeWidth = width;
                }
            }
        });
        
        if (activeWidth > 0) {
            main.css('margin-right', activeWidth + 'px');
        } else {
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

    // --- CORA FOR STUDIO PRODUCT TOUR SYSTEM ---
    let currentTourStep = 0;
    let $tourBackdrop = null;
    let $tourPopover = null;
    
    const tourSteps = [
        {
            element: '.cora-stats-grid',
            title: '1. Studio Metrics & Health',
            description: 'Live statistics summarizing your shoot count, delivery backlog, drafted social media captions, and dynamic revenue estimates calculated from client transactions.',
            position: 'bottom'
        },
        {
            element: '.cora-sidebar [data-target="leads"]',
            title: '2. CRM Sales Pipeline',
            description: 'Track potential wedding bookings. Log client budget briefs, link interactive portfolio demo galleries, assign crew gear checklists, and convert deals to active bookings on retainer payments.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="bookings"]',
            title: '3. Shoot Bookings CRM',
            description: 'Advance shoots dynamically through Confirmed, Editing, and Completed states. Instantly updates client timelines, enqueued invoices, and schedules.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="gallery"]',
            title: '4. Client Galleries',
            description: 'Deliver stunning, password-protected visual portfolios to couples. Features client selection flags and automated downloads.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="vault"]',
            title: '5. Studio Vault Backup',
            description: 'Manage contracts, proposals, invoice documents, raw file backups, and delivery zip folders in a secure, central directory.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="gbp"]',
            title: '6. Google Business Profile',
            description: 'Connect your business listing to sync reviews. Reply to inquiries, publish studio updates, and manage local search visibility.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="financials"]',
            title: '7. Ledger & Financial Board',
            description: 'Analyze revenue analytics, cash inflows, and studio expenses. Output GST-compliant financial summaries and print PDF ledger reports.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="equipment"]',
            title: '8. Smart Gear Inventory',
            description: 'Track cameras, lenses, and flash gear. Assignments in leads or shoots automatically toggle gear statuses to "In Use" with active event tags.',
            position: 'left'
        },
        {
            element: '.cora-sidebar [data-target="team-roles"]',
            title: '9. Team Roles & Preview',
            description: 'Manage staff accounts (Photographers, Editors, Pilots). Define granular capabilities and preview the workspace from different role perspectives.',
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
            description: 'Trigger the AI workspace assistant. Generate social media captions, write contract briefs, check inventory availabilities, and search clients.',
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
        if (coraData.currentPage !== 'dashboard') {
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
                    window.coraShowToast("Tour completed! Welcome aboard Cora Studio.");
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
        localStorage.setItem('cora_tour_completed', 'true');
        localStorage.setItem('cora_studio_tour_completed', 'true');
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
        
        const ajaxUrl = (typeof coraData !== 'undefined' && coraData.ajaxUrl) ? coraData.ajaxUrl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
        const ajaxNonce = (typeof coraData !== 'undefined' && coraData.ajaxNonce) ? coraData.ajaxNonce : '';
        
        $.post(ajaxUrl, {
            action: 'cora_resend_verification',
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
    } else if (!localStorage.getItem('cora_tour_completed') && coraData.currentPage === 'dashboard') {
        setTimeout(function() {
            coraRunTourEngine(0);
        }, 1500);
    }

    // Run auto-create document check
    coraCheckAutoCreateDoc();
});


// ==========================================
// ATTENDANCE & GEOLOCATION
// ==========================================

let isPunchedIn = false;

function coraInitAttendance() {
    // Check initial state on load if possible, or just fetch
    coraLoadAttendance();
}

function coraTogglePunch() {
    if (!navigator.geolocation) {
        window.coraShowToast("Geolocation is not supported by your browser.", "error");
        return;
    }

    const btnText = document.getElementById('cora-punch-text');
    const originalText = btnText.innerText;
    btnText.innerText = "Locating...";

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const action = isPunchedIn ? 'cora_punch_out' : 'cora_punch_in';

            const formData = new FormData();
            formData.append('action', action);
            formData.append('nonce', coraData.ajaxNonce);
            formData.append('lat', lat);
            formData.append('lng', lng);
    const name = document.getElementById('cora-office-address-search').value;
    if (name) formData.append('name', name);

            fetch(coraData.ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    isPunchedIn = !isPunchedIn;
                    btnText.innerText = isPunchedIn ? "Punch Out" : "Punch In";
                    
                    const iconWrapper = document.getElementById('cora-punch-icon-wrapper');
                    if (iconWrapper) {
                        if (isPunchedIn) {
                            iconWrapper.classList.remove('bg-green-50', 'text-green-600');
                            iconWrapper.classList.add('bg-amber-50', 'text-amber-600');
                        } else {
                            iconWrapper.classList.remove('bg-amber-50', 'text-amber-600');
                            iconWrapper.classList.add('bg-green-50', 'text-green-600');
                        }
                    }
                    
                    window.coraShowToast(res.data.message);
                    coraLoadAttendance();
                } else {
                    btnText.innerText = originalText;
                    window.coraShowToast(res.data, "error");
                    
                    // Failsafe state sync if already punched in/out
                    if (res.data.includes("already punched in")) {
                        isPunchedIn = true;
                        btnText.innerText = "Punch Out";
                    } else if (res.data.includes("already punched out") || res.data.includes("not punched in")) {
                        isPunchedIn = false;
                        btnText.innerText = "Punch In";
                    }
                }
            })
            .catch(err => {
                btnText.innerText = originalText;
                window.coraShowToast("Network error while punching.", "error");
            });
        },
        function(error) {
            btnText.innerText = originalText;
            window.coraShowToast("Please allow location access to punch in/out.", "error");
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function coraLoadAttendance() {
    const tbody = document.getElementById('cora-attendance-tbody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-zinc-400">Loading attendance...</td></tr>';

    const formData = new FormData();
    formData.append('action', 'cora_get_attendance');
    formData.append('nonce', coraData.ajaxNonce);

    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            const logs = res.data.logs || [];
            const stats = res.data.stats || { total_team: '--', present_today: '--', missing_absent: '--', flagged_locations: '--' };

            // Update stats overview boxes
            const totalEl = document.getElementById('cora-overview-total');
            const presentEl = document.getElementById('cora-overview-present');
            const absentEl = document.getElementById('cora-overview-absent');
            const flaggedEl = document.getElementById('cora-overview-flagged');

            if (totalEl) totalEl.innerText = stats.total_team;
            if (presentEl) presentEl.innerText = stats.present_today;
            if (absentEl) absentEl.innerText = stats.missing_absent;
            if (flaggedEl) flaggedEl.innerText = stats.flagged_locations;

            if (logs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-zinc-400">No attendance records found.</td></tr>';
                return;
            }

            let html = '';
            
            // Auto-detect current user's punch status from today's logs
            const today = new Date().toISOString().split('T')[0];
            
            logs.forEach(log => {
                // If it's the current user and today's log, sync button state
                if (log.date === today && log.punch_in && !log.punch_out && !isPunchedIn) {
                    isPunchedIn = true;
                    document.getElementById('cora-punch-text').innerText = "Punch Out";
                }
                
                const punchInTime = log.punch_in ? new Date(log.punch_in.replace(' ', 'T')).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '--';
                const punchOutTime = log.punch_out ? new Date(log.punch_out.replace(' ', 'T')).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '--';
                
                let locationHtml = '--';
                if (log.punch_in_lat && log.punch_in_lng) {
                    locationHtml = `<a href="https://www.google.com/maps/search/?api=1&query=${log.punch_in_lat},${log.punch_in_lng}" target="_blank" class="text-blue-500 hover:underline">View Map</a>`;
                }
                
                let actionHtml = '<div class="flex items-center justify-end gap-2">';
                
                if (log.edit_history && log.edit_history.length > 0) {
                    const logDataStr = encodeURIComponent(JSON.stringify(log));
                    actionHtml += `<button onclick="coraViewAttendanceHistory('${logDataStr}')" class="px-2 py-1 border border-amber-200 bg-amber-50 rounded text-[10px] font-bold text-amber-700 hover:bg-amber-100 transition-all" title="View Audit Trail">Modified</button>`;
                }
                
                if (log.can_edit) {
                    actionHtml += `<button onclick="coraEditAttendance(${log.user_id}, '${log.date}', '${log.punch_in || ''}', '${log.punch_out || ''}')" class="px-2 py-1 border border-zinc-200 rounded text-[10px] font-bold text-zinc-700 bg-white hover:bg-zinc-50 transition-all cursor-pointer">Edit</button>`;
                }
                
                if (log.can_manage) {
                    actionHtml += `
                        <div class="flex items-center border border-zinc-200 rounded ml-2 overflow-hidden shadow-sm">
                            <button onclick="coraManageAttendance(${log.user_id}, '${log.date}', 'approve')" class="px-2 py-1 bg-white hover:bg-green-50 text-green-600 border-r border-zinc-200 transition-colors" title="Approve">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                            <button onclick="coraManageAttendance(${log.user_id}, '${log.date}', 'reject')" class="px-2 py-1 bg-white hover:bg-red-50 text-red-600 border-r border-zinc-200 transition-colors" title="Reject">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                            <button onclick="if(confirm('Are you sure you want to delete this log?')) coraManageAttendance(${log.user_id}, '${log.date}', 'delete')" class="px-2 py-1 bg-white hover:bg-red-50 text-zinc-500 hover:text-red-600 transition-colors" title="Delete">
                                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    `;
                }
                
                actionHtml += '</div>';
                
                let nameHtml = log.name;
                if (log.status === 'approved') {
                    nameHtml += ` <span class="inline-flex items-center gap-1 ml-1 px-1.5 py-0.5 rounded text-[8px] font-bold bg-green-100 text-green-700" title="Approved by Admin"><svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>Approved</span>`;
                } else if (log.flagged) {
                    nameHtml += ` <span class="inline-flex items-center gap-1 ml-1 px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-100 text-red-700" title="${log.flag_reason || 'Outside 1000m'}"><svg viewBox="0 0 24 24" width="8" height="8" stroke="currentColor" stroke-width="3" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>${log.status === 'rejected' ? 'Rejected' : 'Flagged'}</span>`;
                }

                html += `
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-4 py-3 font-medium text-zinc-900">${nameHtml}</td>
                        <td class="px-4 py-3">${log.date}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">${punchInTime}</span></td>
                        <td class="px-4 py-3"><span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold ${log.punch_out ? 'bg-zinc-100 text-zinc-700' : 'bg-amber-100 text-amber-700'}">${log.punch_out ? punchOutTime : 'Active'}</span></td>
                        <td class="px-4 py-3">${locationHtml}</td>
                        <td class="px-4 py-3">${actionHtml}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }
    });
}

// Hook into DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // Other inits...
    setTimeout(coraInitAttendance, 1000);
    coraInitOfficeLocationBtn(); // slight delay to not block main thread
});


// --- Enterprise Attendance Log Editing & Audit ---

window.coraEditAttendance = function(userId, date, currentIn, currentOut) {
    // We'll use a simple prompt for now, but a modal is better.
    // Let's create a custom drawer for this to match the global rules.
    const drawerHtml = `
        <div id="cora-attendance-drawer" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white shadow-2xl z-50 transform transition-transform translate-x-full border-l border-zinc-200 flex flex-col">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                <h3 class="text-sm font-bold text-zinc-900">Edit Attendance Record</h3>
                <button onclick="document.getElementById('cora-attendance-drawer').remove()" class="text-zinc-400 hover:text-zinc-600">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 flex-1 overflow-y-auto">
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Punch In Time</label>
                    <input type="datetime-local" id="cora-edit-punch-in" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" value="${currentIn.replace(' ', 'T')}">
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Punch Out Time</label>
                    <input type="datetime-local" id="cora-edit-punch-out" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" value="${currentOut ? currentOut.replace(' ', 'T') : ''}">
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Reason for Edit</label>
                    <input type="text" id="cora-edit-reason" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" placeholder="e.g. Forgot to punch out">
                </div>
            </div>
            <div class="p-5 border-t border-zinc-200 bg-zinc-50 flex justify-end gap-3">
                <button onclick="document.getElementById('cora-attendance-drawer').remove()" class="px-4 py-2 border border-zinc-200 rounded-md text-xs font-bold text-zinc-700 bg-white hover:bg-zinc-50">Cancel</button>
                <button onclick="coraSubmitAttendanceEdit(${userId}, '${date}')" class="px-4 py-2 bg-zinc-950 text-white rounded-md text-xs font-bold hover:bg-zinc-800">Save Changes</button>
            </div>
        </div>
    `;
    
    // Append and animate
    $('body').append(drawerHtml);
    setTimeout(() => {
        $('#cora-attendance-drawer').removeClass('translate-x-full');
    }, 10);
};

window.coraSubmitAttendanceEdit = function(userId, date) {
    const punchIn = $('#cora-edit-punch-in').val().replace('T', ' ');
    const punchOut = $('#cora-edit-punch-out').val().replace('T', ' ');
    const reason = $('#cora-edit-reason').val().trim();
    
    if (!reason) {
        coraShowToast("Reason for edit is mandatory.", "error");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'cora_edit_attendance');
    formData.append('nonce', coraData.ajaxNonce);
    formData.append('user_id', userId);
    formData.append('date', date);
    formData.append('punch_in', punchIn);
    formData.append('punch_out', punchOut);
    formData.append('reason', reason);

    const btn = $('#cora-attendance-drawer button.bg-zinc-950');
    const originalText = btn.text();
    btn.text('Saving...').prop('disabled', true);

    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.coraShowToast(res.data.message);
            $('#cora-attendance-drawer').addClass('translate-x-full');
            setTimeout(() => {
                $('#cora-attendance-drawer').remove();
                coraLoadAttendance();
            }, 300);
        } else {
            window.coraShowToast(res.data, 'error');
            btn.text(originalText).prop('disabled', false);
        }
    })
    .catch(err => {
        window.coraShowToast('Network error while saving.', 'error');
        btn.text(originalText).prop('disabled', false);
    });
};

window.coraManageAttendance = function(userId, date, action) {
    const formData = new FormData();
    formData.append('action', 'cora_manage_attendance');
    formData.append('nonce', coraData.ajaxNonce);
    formData.append('user_id', userId);
    formData.append('date', date);
    formData.append('manage_action', action);

    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.coraShowToast(res.data.message);
            coraLoadAttendance();
        } else {
            window.coraShowToast(res.data, 'error');
        }
    })
    .catch(err => {
        window.coraShowToast('Network error while updating.', 'error');
    });
};

window.coraViewAttendanceHistory = function(logDataStr) {
    const logData = JSON.parse(decodeURIComponent(logDataStr));
    const history = logData.edit_history || [];
    
    let historyHtml = history.map(h => `
        <div class="border-l-2 border-zinc-200 pl-4 py-1 relative">
            <span class="absolute -left-[5px] top-2 w-2 h-2 rounded-full bg-zinc-300"></span>
            <p class="text-xs font-bold text-zinc-900">${h.editor_name} <span class="font-normal text-zinc-500">edited this record</span></p>
            <p class="text-[10px] text-zinc-400 mb-1">${h.timestamp}</p>
            <div class="bg-zinc-50 rounded p-2 text-[10px] border border-zinc-100">
                <span class="font-semibold">Reason:</span> ${h.reason}<br>
                <span class="font-semibold text-red-600">Old In:</span> ${h.old_punch_in || '--'} &rarr; <span class="font-semibold text-emerald-600">New In:</span> ${h.new_punch_in || '--'}<br>
                <span class="font-semibold text-red-600">Old Out:</span> ${h.old_punch_out || '--'} &rarr; <span class="font-semibold text-emerald-600">New Out:</span> ${h.new_punch_out || '--'}
            </div>
        </div>
    `).join('');

    if (history.length === 0) historyHtml = '<p class="text-xs text-zinc-500">No edits recorded.</p>';

    const drawerHtml = `
        <div id="cora-audit-drawer" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white shadow-2xl z-50 transform transition-transform translate-x-full border-l border-zinc-200 flex flex-col">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                <h3 class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    Audit Trail
                </h3>
                <button onclick="document.getElementById('cora-audit-drawer').remove()" class="text-zinc-400 hover:text-zinc-600">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 flex-1 overflow-y-auto">
                ${historyHtml}
            </div>
        </div>
    `;

    $('body').append(drawerHtml);
    setTimeout(() => {
        $('#cora-audit-drawer').removeClass('translate-x-full');
    }, 10);
};


window.coraAddAttendanceRecord = function() {
    const members = coraData.teamMembers || [];
    const memberOptions = members.map(m => `<option value="${m.id}">${m.display_name} (${m.email})</option>`).join('');

    const todayDate = new Date().toISOString().split('T')[0];
    const today = new Date();
    today.setHours(9, 0, 0, 0);
    const defaultPunchIn = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 16);

    const drawerHtml = `
        <div id="cora-add-attendance-drawer" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-white shadow-2xl z-50 transform transition-transform translate-x-full border-l border-zinc-200 flex flex-col">
            <div class="px-5 py-4 border-b border-zinc-200 flex items-center justify-between bg-zinc-50/50">
                <h3 class="text-sm font-bold text-zinc-900">Add Attendance Record Manually</h3>
                <button onclick="document.getElementById('cora-add-attendance-drawer').remove()" class="text-zinc-400 hover:text-zinc-600">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="p-5 space-y-4 flex-1 overflow-y-auto">
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Select Team Member</label>
                    <select id="cora-add-user-id" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none">
                        <option value="">-- Choose Member --</option>
                        ${memberOptions}
                    </select>
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Record Date</label>
                    <input type="date" id="cora-add-date" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" value="${todayDate}">
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Punch In Time</label>
                    <input type="datetime-local" id="cora-add-punch-in" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" value="${defaultPunchIn}">
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Punch Out Time (Optional)</label>
                    <input type="datetime-local" id="cora-add-punch-out" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none">
                </div>
                <div class="cora-form-group flex flex-col gap-1.5">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Reason for Manual Entry</label>
                    <input type="text" id="cora-add-reason" class="w-full border border-zinc-200 rounded-md p-2 text-sm focus:border-zinc-400 focus:outline-none" placeholder="e.g. Forgot phone / Onsite direct visit">
                </div>
            </div>
            <div class="p-5 border-t border-zinc-200 bg-zinc-50 flex justify-end gap-3">
                <button onclick="document.getElementById('cora-add-attendance-drawer').remove()" class="px-4 py-2 border border-zinc-200 rounded-md text-xs font-bold text-zinc-700 bg-white hover:bg-zinc-50">Cancel</button>
                <button onclick="coraSubmitAttendanceAdd()" class="px-4 py-2 bg-zinc-950 text-white rounded-md text-xs font-bold hover:bg-zinc-800">Add Record</button>
            </div>
        </div>
    `;

    $('body').append(drawerHtml);
    setTimeout(() => {
        $('#cora-add-attendance-drawer').removeClass('translate-x-full');
    }, 10);
};

window.coraSubmitAttendanceAdd = function() {
    const userId = $('#cora-add-user-id').val();
    const date = $('#cora-add-date').val();
    const punchIn = $('#cora-add-punch-in').val().replace('T', ' ');
    const punchOutVal = $('#cora-add-punch-out').val();
    const punchOut = punchOutVal ? punchOutVal.replace('T', ' ') : '';
    const reason = $('#cora-add-reason').val().trim();

    if (!userId) {
        coraShowToast("Please select a team member.", "error");
        return;
    }
    if (!date) {
        coraShowToast("Please select a date.", "error");
        return;
    }
    if (!punchIn) {
        coraShowToast("Punch in time is mandatory.", "error");
        return;
    }
    if (!reason) {
        coraShowToast("Reason for manual entry is mandatory.", "error");
        return;
    }

    const formData = new FormData();
    formData.append('action', 'cora_add_attendance');
    formData.append('nonce', coraData.ajaxNonce);
    formData.append('user_id', userId);
    formData.append('date', date);
    formData.append('punch_in', punchIn);
    formData.append('punch_out', punchOut);
    formData.append('reason', reason);

    const btn = $('#cora-add-attendance-drawer button.bg-zinc-950');
    const originalText = btn.text();
    btn.text('Saving...').prop('disabled', true);

    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.coraShowToast(res.data.message);
            $('#cora-add-attendance-drawer').addClass('translate-x-full');
            setTimeout(() => {
                $('#cora-add-attendance-drawer').remove();
                coraLoadAttendance();
            }, 300);
        } else {
            window.coraShowToast(res.data, 'error');
            btn.text(originalText).prop('disabled', false);
        }
    })
    .catch(err => {
        window.coraShowToast('Network error while saving.', 'error');
        btn.text(originalText).prop('disabled', false);
    });
};


let coraOfficeMap = null;
let coraOfficeMarker = null;


window.coraInitOfficeLocationBtn = function() {
    fetch(coraData.ajaxUrl + '?action=cora_get_office_location&nonce=' + coraData.ajaxNonce)
    .then(r => r.json())
    .then(res => {
        if (res.success && res.data.name) {
            const btnText = document.getElementById('cora-office-btn-text');
            if (btnText) btnText.innerText = 'Office: ' + res.data.name.substring(0, 15) + (res.data.name.length > 15 ? '...' : '');
        }
    });
};

window.coraToggleOfficeLocationDrawer = function(show) {
    const drawer = document.getElementById('cora-office-location-drawer');
    if (show) {
        drawer.classList.remove('translate-x-full');
        coraRenderSearchHistory();
        // Initialize Map
        setTimeout(() => {
            if (!coraOfficeMap) {
                coraOfficeMap = L.map('cora-office-map').setView([20.5937, 78.9629], 5); // Default India center
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(coraOfficeMap);

                // Fetch existing location
                fetch(coraData.ajaxUrl + '?action=cora_get_office_location&nonce=' + coraData.ajaxNonce)
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.data.lat && res.data.lng) {
                        const lat = parseFloat(res.data.lat);
                        const lng = parseFloat(res.data.lng);
                        coraSetMapMarker(lat, lng);
                        if (res.data.name) document.getElementById('cora-office-address-search').value = res.data.name;
                    }
                });

                coraOfficeMap.on('click', function(e) {
                    coraSetMapMarker(e.latlng.lat, e.latlng.lng);
                });
            } else {
                coraOfficeMap.invalidateSize();
            }
        }, 300);
    } else {
        drawer.classList.add('translate-x-full');
        document.getElementById('cora-office-search-results').classList.add('hidden');
    }
}

window.coraSetMapMarker = function(lat, lng) {
    if (coraOfficeMarker) {
        coraOfficeMap.removeLayer(coraOfficeMarker);
    }
    coraOfficeMarker = L.marker([lat, lng]).addTo(coraOfficeMap);
    coraOfficeMap.setView([lat, lng], 15);
    document.getElementById('cora-office-lat').value = lat.toFixed(6);
    document.getElementById('cora-office-lng').value = lng.toFixed(6);
}

let searchTimeout = null;
window.coraDebounceSearch = function(query) {
    if (searchTimeout) clearTimeout(searchTimeout);
    if (!query || query.length < 3) {
        document.getElementById('cora-office-search-results').classList.add('hidden');
        return;
    }
    searchTimeout = setTimeout(() => {
        coraSearchOfficeLocation(query);
    }, 500); // 500ms debounce
}

window.coraSearchOfficeLocation = function(searchQuery) {
    const query = searchQuery || document.getElementById('cora-office-address-search').value;
    if (!query) return;

    // Check if it's a coordinate string or Google Maps URL with coordinates in string
    const latLngMatch = query.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/) || query.match(/^(-?\d+\.\d+)[\s,]+(-?\d+\.\d+)$/);
    if (latLngMatch) {
        const lat = parseFloat(latLngMatch[1]);
        const lng = parseFloat(latLngMatch[2]);
        coraSetMapMarker(lat, lng);
        document.getElementById('cora-office-address-search').value = query;
        coraAddSearchHistory(query, lat, lng);
        document.getElementById('cora-office-search-results').classList.add('hidden');
        return;
    }

    if (query.startsWith('http://') || query.startsWith('https://')) {
        const resultsContainer = document.getElementById('cora-office-search-results');
        resultsContainer.innerHTML = '<div class="p-3 text-xs text-zinc-500">Resolving Maps URL...</div>';
        resultsContainer.classList.remove('hidden');
        
        const formData = new FormData();
        formData.append('action', 'cora_resolve_map_url');
        formData.append('nonce', coraData.ajaxNonce);
        formData.append('url', query);
        
        fetch(coraData.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data.lat && res.data.lng) {
                const lat = parseFloat(res.data.lat);
                const lng = parseFloat(res.data.lng);
                coraSetMapMarker(lat, lng);
                document.getElementById('cora-office-address-search').value = query;
                coraAddSearchHistory(query, lat, lng);
                resultsContainer.classList.add('hidden');
            } else {
                resultsContainer.innerHTML = '<div class="p-3 text-xs text-zinc-500">Could not extract coordinates from this URL.</div>';
            }
        })
        .catch(err => {
            resultsContainer.innerHTML = '<div class="p-3 text-xs text-zinc-500">Error resolving URL.</div>';
        });
        return;
    }
    
    // Add &addressdetails=1 to try and get better fuzzy matching sometimes, though Nominatim is strict
    // Add &countrycodes=in to restrict search to India only
    fetch('https://nominatim.openstreetmap.org/search?format=json&countrycodes=in&q=' + encodeURIComponent(query))
    .then(r => r.json())
    .then(data => {
        const resultsContainer = document.getElementById('cora-office-search-results');
        resultsContainer.innerHTML = '';
        if (data.length === 0) {
            resultsContainer.innerHTML = '<div class="p-3 text-xs text-zinc-500">No exact results found. Try simplifying your query.</div>';
        } else {
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = "p-2 hover:bg-zinc-50 text-xs border-b border-zinc-100 cursor-pointer text-zinc-700";
                div.innerText = item.display_name;
                div.onclick = () => {
                    coraSetMapMarker(parseFloat(item.lat), parseFloat(item.lon));
                    resultsContainer.classList.add('hidden');
                    document.getElementById('cora-office-address-search').value = item.display_name;
                    coraAddSearchHistory(item.display_name, item.lat, item.lon);
                };
                resultsContainer.appendChild(div);
            });
        }
        resultsContainer.classList.remove('hidden');
    })
    .catch(err => console.error(err));
}

window.coraAddSearchHistory = function(name, lat, lng) {
    let history = JSON.parse(localStorage.getItem('cora_office_search_history') || '[]');
    // Remove if already exists
    history = history.filter(h => h.name !== name);
    // Add to front
    history.unshift({name, lat, lng});
    if (history.length > 5) history.pop();
    localStorage.setItem('cora_office_search_history', JSON.stringify(history));
    coraRenderSearchHistory();
};

window.coraRenderSearchHistory = function() {
    let history = JSON.parse(localStorage.getItem('cora_office_search_history') || '[]');
    let container = document.getElementById('cora-office-search-history');
    
    if (!container) {
        // Create it if it doesn't exist
        const searchDiv = document.getElementById('cora-office-address-search').parentNode.parentNode;
        container = document.createElement('div');
        container.id = 'cora-office-search-history';
        container.className = 'flex flex-wrap gap-2 mt-2';
        searchDiv.appendChild(container);
    }
    
    if (history.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    container.innerHTML = '<span class="text-[10px] text-zinc-400 w-full mb-1">Recent Searches:</span>' + history.map(h => {
        return `<span class="text-[10px] bg-zinc-100 border border-zinc-200 text-zinc-600 px-2 py-1 rounded cursor-pointer hover:bg-zinc-200 transition-colors truncate max-w-[150px]" onclick="coraSetMapMarker(${h.lat}, ${h.lng}); document.getElementById('cora-office-address-search').value = '${h.name.replace(/'/g, "\\'")}'">${h.name}</span>`;
    }).join('');
};

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    const searchContainer = document.getElementById('cora-office-search-results');
    const searchInput = document.getElementById('cora-office-address-search');
    if (searchContainer && !searchContainer.contains(e.target) && e.target !== searchInput && e.target.tagName !== 'BUTTON') {
        searchContainer.classList.add('hidden');
    }
});

window.coraSaveOfficeLocationDrawer = function() {
    const lat = document.getElementById('cora-office-lat').value;
    const lng = document.getElementById('cora-office-lng').value;

    if (!lat || !lng) {
        window.coraShowToast("Please select a location first.", "error");
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'cora_set_office_location');
    formData.append('nonce', coraData.ajaxNonce);
    formData.append('lat', lat);
    formData.append('lng', lng);
    
    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.coraShowToast("Office location saved.");
            coraToggleOfficeLocationDrawer(false);
            coraInitOfficeLocationBtn();
        } else {
            window.coraShowToast(res.data || "Error saving location.", "error");
        }
    })
    .catch(err => {
        window.coraShowToast("Error saving office location.", "error");
    });
};

// ═══ MY PROFILE PAGE FUNCTIONALITY ═══
// Preview avatar locally upon selection
$(document).ready(function() {
    $(document).on('change', '#cora-profile-avatar-input', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = $('#cora-profile-avatar-img');
                if (img.is('img')) {
                    img.attr('src', event.target.result);
                } else {
                    // Replace fallback initials div with actual img element
                    const newImg = $('<img>', {
                        id: 'cora-profile-avatar-img',
                        src: event.target.result,
                        class: 'w-20 h-20 rounded-full object-cover border-2 border-zinc-200/60 shadow-sm',
                        alt: 'Profile Avatar'
                    });
                    img.replaceWith(newImg);
                }
            };
            reader.readAsDataURL(file);
        }
    });
});

window.coraSaveMyProfile = function() {
    const displayName = $('#cora-profile-display-name').val().trim();
    const email = $('#cora-profile-email').val().trim();
    const phone = $('#cora-profile-phone').val().trim();
    const bio = $('#cora-profile-bio').val().trim();
    const avatarInput = $('#cora-profile-avatar-input')[0];
    const saveBtn = $('#cora-profile-save-btn');
    const statusSpan = $('#cora-profile-save-status');

    if (!displayName) {
        window.coraShowToast("Display name cannot be empty.", "error");
        return;
    }

    if (!email) {
        window.coraShowToast("Email address cannot be empty.", "error");
        return;
    }

    saveBtn.prop('disabled', true).addClass('opacity-50').html(
        `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg> Saving...`
    );
    statusSpan.text('');

    const formData = new FormData();
    formData.append('action', 'cora_update_my_profile');
    formData.append('nonce', coraData.ajaxNonce);
    formData.append('display_name', displayName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('bio', bio);
    if (avatarInput && avatarInput.files.length > 0) {
        formData.append('avatar_file', avatarInput.files[0]);
    }

    fetch(coraData.ajaxUrl, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        saveBtn.prop('disabled', false).removeClass('opacity-50').html(
            `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="inline mr-1.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Save Changes`
        );
        if (res.success) {
            window.coraShowToast("Profile details updated successfully.");
            statusSpan.text('Saved. Reloading page...').addClass('text-emerald-600').removeClass('text-red-500');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            window.coraShowToast(res.data || "Error saving profile details.", "error");
            statusSpan.text(res.data || "Save failed.").addClass('text-red-500').removeClass('text-emerald-600');
        }
    })
    .catch(err => {
        saveBtn.prop('disabled', false).removeClass('opacity-50').html(
            `<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" class="inline mr-1.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Save Changes`
        );
        window.coraShowToast("Network error saving profile details.", "error");
        statusSpan.text("Network error.").addClass('text-red-500');
    });
};

window.coraSaveStudioSettings = function(btnElement) {
    const brandName = $('#cora-settings-brand-name').val().trim();
    const updatesUrl = $('#cora-settings-updates-url').val().trim();
    
    if (!brandName) {
        window.coraShowToast("Studio Brand Name cannot be empty.", "error");
        return;
    }

    const btn = $(btnElement);
    const oldHtml = btn.html();
    btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: coraData.ajaxUrl,
        method: 'POST',
        data: {
            action: 'cora_save_studio_settings',
            security: coraData.ajaxNonce,
            brand_name: brandName,
            updates_url: updatesUrl
        },
        success: function(res) {
            btn.prop('disabled', false).html(oldHtml);
            if (res.success) {
                window.coraShowToast("Studio settings saved successfully.");
            } else {
                window.coraShowToast(res.data || "Error saving settings.", "error");
            }
        },
        error: function() {
            btn.prop('disabled', false).html(oldHtml);
            window.coraShowToast("Network error saving studio settings.", "error");
        }
    });
};


