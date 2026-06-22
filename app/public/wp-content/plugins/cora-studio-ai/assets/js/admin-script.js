/**
 * Cora Studio AI - Admin Dashboard JavaScript Interactions
 */

jQuery(document).ready(function($) {
    // Custom Toast Notification System
    window.coraShowToast = function(message) {
        let toastContainer = $('#cora-toast-container');
        if (toastContainer.length === 0) {
            $('body').append('<div id="cora-toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>');
            toastContainer = $('#cora-toast-container');
        }
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="bg-zinc-950 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow-xl border border-zinc-800 flex items-center gap-2 pointer-events-auto transition-all duration-300 transform translate-y-2 opacity-0 select-none">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400 shrink-0"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <span>${message}</span>
            </div>
        `;
        toastContainer.append(toastHtml);
        
        const toast = $(`#${toastId}`);
        // Fade & slide in
        setTimeout(() => {
            toast.removeClass('translate-y-2 opacity-0');
        }, 50);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.addClass('translate-y-2 opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    };

    // 1. Navigation & Tab Switching
    window.coraNavigateTo = function(targetPageId) {
        const activeRole = $('#cora-role-preview-select').val() || coraData.currentRole;
        let allowed = coraData.userPermissions[activeRole] || [];
        if (activeRole === 'administrator') {
            allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'vault', 'settings'];
        }

        if (targetPageId !== 'feature-hub' && !allowed.includes(targetPageId)) {
            window.coraShowToast("Access denied: your role does not have permission for this section.");
            return;
        }

        // If the target page is different from the current page, redirect
        if (targetPageId !== coraData.currentPage) {
            let siteUrl = coraData.siteUrl || '';
            if (siteUrl.endsWith('/')) {
                siteUrl = siteUrl.slice(0, -1);
            }
            window.location.href = siteUrl + '/workspace/' + targetPageId;
        }
    };

    $('.cora-nav-item, .cora-bottom-nav-item').on('click', function(e) {
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

        const newRow = `
            <tr data-status="confirmed" class="hover:bg-zinc-50/30 transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="cora-client-meta flex flex-col">
                        <span class="cora-client-name font-semibold text-sm text-zinc-900">${clientName}</span>
                        <span class="cora-client-email text-[11px] text-zinc-400">${clientName.toLowerCase().replace(/\s+/g, '')}@gmail.com</span>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-blue">${shootType}</span></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">${location}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-zinc-500">${date}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-zinc-900">${price}</td>
                <td class="px-4 py-3 whitespace-nowrap"><span class="cora-badge cora-badge-blue">Confirmed</span></td>
                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                    <div class="flex items-center justify-end gap-1.5">
                        <button class="cora-btn-icon-only inline-flex items-center justify-center p-1.5 rounded border border-zinc-200 text-zinc-500 hover:text-zinc-955 hover:bg-zinc-100 transition-all cursor-pointer" onclick="coraTriggerAction('whatsapp', '${clientName}')">
                            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </button>
                        <button class="cora-btn-action px-2 py-1 text-xs font-semibold border border-zinc-300 rounded text-zinc-700 hover:bg-zinc-50 active:scale-95 transition-all cursor-pointer" onclick="coraUpdateBookingStatus(this, 'editing')">Advance to Editing</button>
                    </div>
                </td>
            </tr>
        `;
        $('#cora-bookings-table tbody').prepend(newRow);
        coraShowToast("Booking created successfully!");
        
        // Update sidebar badge
        const badge = $('.cora-badge-sidebar');
        const currentVal = parseInt(badge.text()) || 0;
        badge.text(currentVal + 1);

        coraToggleAddShootDrawer(false);
    });

    // 3. Status Filters for CRM Table
    $('.cora-filter-tab').on('click', function() {
        $('.cora-filter-tab').removeClass('active');
        $(this).addClass('active');

        const filterVal = $(this).data('filter');
        const rows = $('#cora-bookings-table tbody tr');

        if (filterVal === 'all') {
            rows.show();
        } else {
            rows.hide();
            rows.filter(`[data-status="${filterVal}"]`).show();
        }
    });

    // 4. Update Booking Status (Action callback)
    window.coraUpdateBookingStatus = function(button, nextStatus) {
        const row = $(button).closest('tr');
        row.attr('data-status', nextStatus);

        const statusCell = row.find('td:nth-child(6)');
        const actionCell = row.find('td:nth-child(7)');
        const clientName = row.find('.cora-client-name').text();

        if (nextStatus === 'editing') {
            statusCell.html('<span class="cora-badge cora-badge-yellow">Editing</span>');
            actionCell.html(`
                <button class="cora-btn-icon-only" onclick="coraTriggerAction('caption-quick', '${clientName}')">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                </button>
                <button class="cora-btn-action" onclick="coraUpdateBookingStatus(this, 'completed')">Mark Completed</button>
            `);
        } else if (nextStatus === 'completed') {
            statusCell.html('<span class="cora-badge cora-badge-green">Completed</span>');
            actionCell.html(`
                <span class="cora-delivered-text">✓ Previews Sent</span>
                <button class="cora-btn-icon-only" onclick="coraTriggerAction('invoice', '${clientName}')">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </button>
            `);
        }
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
    $('#cora-profile-popover').on('click', function(e) {
        e.stopPropagation();
    });

    $(document).on('click', function(e) {
        // Close popover if clicked outside profile card area
        if (!$(e.target).closest('#cora-profile-popover').length && !$(e.target).closest('.cora-user-settings-btn').length) {
            $('#cora-profile-popover').addClass('hidden');
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

        // Hotkeys active only when NOT typing in inputs/textareas
        if (!$(e.target).is('input, textarea, select')) {
            const key = e.key.toLowerCase();
            
            // Switch tabs: 1-8
            if (key === '1') {
                coraNavigateTo('dashboard');
            } else if (key === '2') {
                coraNavigateTo('bookings');
            } else if (key === '3') {
                $('.cora-nav-item[data-target="ai-assistants"]').trigger('click');
            } else if (key === '4') {
                $('.cora-nav-item[data-target="gallery-seo"]').trigger('click');
            } else if (key === '5') {
                coraNavigateTo('settings');
            } else if (key === '6') {
                coraNavigateTo('feature-hub');
            } else if (key === '7') {
                coraNavigateTo('team-roles');
            } else if (key === '8') {
                coraNavigateTo('equipment');
            }
            
            // Open Create Shoot drawer: n or c
            if (key === 'n' || key === 'c') {
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

        permissions['administrator'] = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'settings'];

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
            headerPreview.html(`<img src="${logoUrl}" style="max-height: 50px; max-width: 180px; object-fit: contain;" alt="Branding Logo" />`).removeClass('hidden');
        } else {
            headerPreview.html('').addClass('hidden');
        }

        // Update footer text
        const footerPreview = $('#cora-paper-footer-preview');
        if (footerText) {
            footerPreview.text(footerText).removeClass('hidden');
        } else {
            footerPreview.text('').addClass('hidden');
        }
    };

    window.coraOpenDocDrawer = function() {
        // Reset inputs
        $('#cora-doc-id-hidden').val('');
        $('#cora-doc-title-input').val('Untitled Document');
        $('#cora-doc-type-select').val('Proposal');
        $('#cora-doc-amount-input').val('');
        $('#cora-doc-logo-url').val('');
        $('#cora-doc-footer-text').val('');
        $('#cora-doc-paper').html('<p>Start typing your document content here...</p>');

        coraEditorUpdateBranding();

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');
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
        $('#cora-doc-type-select').val(doc.type);
        $('#cora-doc-amount-input').val(doc.amount || '');
        $('#cora-doc-logo-url').val(doc.logo_url || '');
        $('#cora-doc-footer-text').val(doc.footer_text || '');
        $('#cora-doc-paper').html(doc.content || '<p></p>');

        coraEditorUpdateBranding();

        // Update headings selector state if heading is at start
        $('#cora-editor-heading').val('p');

        // Switch views
        $('#cora-vault-list-view').addClass('hidden');
        $('#cora-vault-editor-view').removeClass('hidden');
    };

    window.coraCloseEditor = function() {
        $('#cora-vault-editor-view').addClass('hidden');
        $('#cora-vault-list-view').removeClass('hidden');
    };

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
            content: content
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
            allowed = ['dashboard', 'bookings', 'feature-hub', 'team-roles', 'equipment', 'financials', 'vault', 'settings'];
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
        }
    }
    coraEnforcePermissions(initialRole);

    // Bind Preview Change Dropdown
    $('#cora-role-preview-select').on('change', function() {
        const selectedRole = $(this).val();
        try {
            sessionStorage.setItem('cora_preview_role', selectedRole);
        } catch(e) {}
        coraEnforcePermissions(selectedRole);
        window.coraShowToast(`Previewing dashboard as ${$('#cora-role-preview-select option:selected').text()}`);
    });
});
