<?php
/**
 * Cora Workspace Header Renderer
 *
 * Standard reusable widget for workspace view page headers.
 * Implements title, description, mobile icon, CTA button, AI stack, and tabs sub-navigation.
 * Monochromatic Zinc palette (zinc-50 to zinc-950), per platform rules.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'cora_get_mcp_button_html' ) ) {
    /**
     * Standard Workspace Model Context Protocol (MCP) Living Button
     * 
     * Luminous frosted peach-to-lavender glassmorphism with warm orange typography
     * and plug vector icon, matching platform design system benchmark.
     */
    function cora_get_mcp_button_html( $extra_class = '', $id = 'cora-btn-ai-mcp-gateway' ) {
        $btn_id = ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
        $classes = 'cora-ai-alive-btn select-none' . ( ! empty( $extra_class ) ? ' ' . esc_attr( $extra_class ) : '' );
        
        return '<button type="button"' . $btn_id . ' class="' . $classes . '" onclick="if(typeof coraOpenWorkspaceDeveloperGateway === \'function\') coraOpenWorkspaceDeveloperGateway();" title="Workspace Model Context Protocol (MCP) &amp; Developer Gateway">
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cora-ai-plug-icon shrink-0">
                <path d="M12 22v-5"></path>
                <path d="M9 8V2"></path>
                <path d="M15 8V2"></path>
                <path d="M18 8v5a6 6 0 0 1-12 0V8z"></path>
            </svg>
            <span class="whitespace-nowrap font-semibold">MCP</span>
        </button>';
    }
}

if ( ! function_exists( 'cora_get_ai_platform_official_logo' ) ) {
    /**
     * Return authentic, official brand vector SVG logo for top AI platforms.
     *
     * @param string $platform Platform slug: 'openai'|'chatgpt'|'claude'|'gemini'|'perplexity'|'youtube'
     * @param string $class Extra CSS classes
     * @param int $size Width and height in px
     * @param bool $monochrome If true, render in currentColor instead of official brand color
     * @return string Valid SVG markup
     */
    function cora_get_ai_platform_official_logo( $platform, $class = '', $size = 15, $monochrome = false ) {
        $platform = strtolower( trim( $platform ) );
        $w = intval( $size );
        $h = intval( $size );
        $cls = ! empty( $class ) ? ' ' . esc_attr( $class ) : '';

        switch ( $platform ) {
            case 'openai':
            case 'chatgpt':
                $color = $monochrome ? 'currentColor' : '#10A37F';
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="none" class="shrink-0' . $cls . '">
                    <path d="M22.2819 9.8211a5.9847 5.9847 0 0 0-.5157-4.9108 6.0462 6.0462 0 0 0-6.5098-2.9A6.0651 6.0651 0 0 0 4.9807 4.1818a5.9847 5.9847 0 0 0-3.9977 2.9 6.0462 6.0462 0 0 0 .7427 7.0966 5.98 5.98 0 0 0 .511 4.9107 6.051 6.051 0 0 0 6.5146 2.9001A5.9847 5.9847 0 0 0 13.2599 24a6.0557 6.0557 0 0 0 5.7718-4.2058 5.9894 5.9894 0 0 0 3.9977-2.9001 6.0557 6.0557 0 0 0-.7475-7.0729zm-9.022 12.6081a4.4755 4.4755 0 0 1-2.8764-1.0408l.1419-.0804 4.7783-2.7582a.7948.7948 0 0 0 .3927-.6813v-6.7369l2.02 1.1683a.071.071 0 0 1 .038.052v5.5826a4.504 4.504 0 0 1-4.4945 4.4947zm-9.6607-4.1254a4.4708 4.4708 0 0 1-.5346-3.0137l.142.0852 4.783 2.7582a.7712.7712 0 0 0 .7806 0l5.8428-3.3685v2.3324a.0804.0804 0 0 1-.0332.0615L9.74 19.9502a4.4992 4.4992 0 0 1-6.1408-1.6464zM2.3408 7.8956a4.485 4.485 0 0 1 2.3655-1.9728V11.6a.7664.7664 0 0 0 .3879.6765l5.8144 3.3543-2.0201 1.1683a.0757.0757 0 0 1-.071 0l-4.8303-2.7866A4.4992 4.4992 0 0 1 2.3408 7.872zm16.5963 3.8558L13.1038 8.364 15.1239 7.2a.0757.0757 0 0 1 .071 0l4.8303 2.7913a4.4944 4.4944 0 0 1-.6765 8.1042v-5.6772a.79.79 0 0 0-.4076-.6667zm2.0107-3.0231l-.142-.0852-4.7735-2.7818a.7759.7759 0 0 0-.7854 0L9.409 9.2297V6.8974a.0662.0662 0 0 1 .0284-.0615l4.8303-2.7866a4.4992 4.4992 0 0 1 6.6802 4.6608zm-12.6401 4.135l2.4633-1.4208 2.4633 1.4208v2.8417l-2.4633 1.4208-2.4633-1.4208zM7.65 1.761a4.4992 4.4992 0 0 1 5.371 1.055l-.142.0804-4.783 2.763a.79.79 0 0 0-.3927.6812v6.7417L5.683 11.914a.0757.0757 0 0 1-.038-.052V6.2794a4.4944 4.4944 0 0 1 2.005-4.5184z" fill="' . esc_attr( $color ) . '"/>
                </svg>';

            case 'claude':
            case 'anthropic':
                $color = $monochrome ? 'currentColor' : '#D97706';
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="none" class="shrink-0' . $cls . '">
                    <path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z" fill="' . esc_attr( $color ) . '"/>
                </svg>';

            case 'gemini':
            case 'google':
                static $grad_idx = 0;
                $grad_idx++;
                $gid = 'cora-gemini-hdr-grad-' . $grad_idx;
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="none" class="shrink-0' . $cls . '">
                    <defs>
                        <linearGradient id="' . $gid . '" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#4E82EE"/>
                            <stop offset="50%" stop-color="#9B72CB"/>
                            <stop offset="100%" stop-color="#D96570"/>
                        </linearGradient>
                    </defs>
                    <path d="M12 0C12 6.627 6.627 12 0 12c6.627 0 12 5.373 12 12 0-6.627 5.373-12 12-12-6.627 0-12-5.373-12-12z" fill="' . ( $monochrome ? 'currentColor' : 'url(#' . $gid . ')' ) . '"/>
                </svg>';

            case 'perplexity':
                $color = $monochrome ? 'currentColor' : '#20808D';
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="none" class="shrink-0' . $cls . '">
                    <path d="M22.3977 7.0896h-2.3106V.0676l-7.5094 6.3542V.1577h-1.1554v6.1966L4.4904 0v7.0896H1.6023v10.3976h2.8882V24l6.932-6.3591v6.2005h1.1554v-6.0469l6.9318 6.1807v-6.4879h2.8882V7.0896zm-3.4657-4.531v4.531h-5.355l5.355-4.531zm-13.2862.0676 4.8691 4.4634H5.6458V2.6262zM2.7576 16.332V8.245h7.8476l-6.1149 6.1147v1.9723H2.7576zm2.8882 5.0404v-3.8852h.0001v-2.6488l5.7763-5.7764v7.0111l-5.7764 5.2993zm12.7086.0248-5.7766-5.1509V9.0618l5.7766 5.7766v6.5588zm2.8882-5.0652h-1.733v-1.9723L13.3948 8.245h7.8478v8.087z" fill="' . esc_attr( $color ) . '"/>
                </svg>';

            case 'youtube':
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="none" class="shrink-0' . $cls . '">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z" fill="' . ( $monochrome ? 'currentColor' : '#FF0000' ) . '"/>
                    <path d="M9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="' . ( $monochrome ? '#18181B' : '#FFFFFF' ) . '"/>
                </svg>';

            default:
                return '<svg viewBox="0 0 24 24" width="' . $w . '" height="' . $h . '" fill="currentColor" class="shrink-0' . $cls . '"><circle cx="12" cy="12" r="10"/></svg>';
        }
    }
}

if ( ! function_exists( 'cora_render_workspace_header' ) ) {
function cora_render_workspace_header( $args = array() ) {
    $defaults = array(
        'title'              => 'Workspace',
        'mobile_title'       => '',
        'description'        => '',
        'mobile_description' => '',
        'icon'               => '', // Left SVG icon for mobile view

        'ai_stack'           => true,
        'tutorial_onclick'   => 'openPermissionsVideoDrawer()',
        'cta'                => array(),
        'tabs'               => array(),
        'show_mcp_button'    => true,
        'container_class'    => '',
    );
    
    $args = array_replace_recursive( $defaults, $args );
    
    // Process CTA defaults
    $cta_defaults = array(
        'text'        => '',
        'mobile_text' => '',
        'onclick'     => '',
        'class'       => '',
        'icon'        => '',
        'visible'     => true,
    );
    $cta = array_merge( $cta_defaults, $args['cta'] );
    if ( empty( $cta['mobile_text'] ) ) {
        $cta['mobile_text'] = $cta['text'];
    }
    
    // Process visible tabs
    $visible_tabs = array_values( array_filter( $args['tabs'], function( $tab ) {
        return ! isset( $tab['visible'] ) || $tab['visible'];
    } ) );
    
    static $cora_header_script_rendered = false;
    if ( ! $cora_header_script_rendered ) :
        $cora_header_script_rendered = true;
    ?>
    <style>
    @media (min-width: 1024px) {
        .cora-sub-tabs-container,
        .cora-sticky-sub-tabs {
            top: 0px !important;
        }
    }
    @media (max-width: 1023px) {
        .cora-sub-tabs-container,
        .cora-sticky-sub-tabs {
            top: var(--cora-sticky-tabs-top, var(--cora-topbar-height, 48px)) !important;
        }
    }
    /* Reset extra action buttons inside sticky sub-tabs from global tab button overrides */
    .cora-sub-tabs-container .cora-sub-tabs-extra button,
    .cora-sticky-sub-tabs .cora-sub-tabs-extra button {
        margin-bottom: 0 !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
    }
    .cora-sub-tabs-container .cora-sub-tabs-extra button.cora-subtabs-switcher-btn,
    .cora-sticky-sub-tabs .cora-sub-tabs-extra button.cora-subtabs-switcher-btn {
        height: 22px !important;
        min-height: 22px !important;
        max-height: 22px !important;
        width: 26px !important;
        min-width: 26px !important;
        max-width: 26px !important;
        padding: 0 !important;
    }
    .cora-sub-tabs-container .cora-sub-tabs-extra button.cora-subtabs-search-trigger,
    .cora-sticky-sub-tabs .cora-sub-tabs-extra button.cora-subtabs-search-trigger {
        height: 28px !important;
        min-height: 28px !important;
        max-height: 28px !important;
        width: 28px !important;
        min-width: 28px !important;
        max-width: 28px !important;
        padding: 0 !important;
    }
    @media (min-width: 640px) {
        .cora-sub-tabs-container .cora-subtabs-search-trigger,
        .cora-sticky-sub-tabs .cora-subtabs-search-trigger,
        .cora-sub-tabs-extra .cora-subtabs-search-trigger,
        .cora-subtabs-search-trigger,
        .cora-subtabs-search-close {
            display: none !important;
        }
    }
    .cora-sub-tabs-container .cora-sub-tabs-extra button > * {
        pointer-events: none !important;
    }
    /* When extra actions exist, keep them pinned on the right while tabs scroll independently */
    .cora-sub-tabs-container.has-extra-actions {
        overflow-x: hidden !important;
    }
    .cora-sub-tabs-container.has-extra-actions .cora-sub-tabs-nav {
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: none !important;
    }
    .cora-sub-tabs-container.has-extra-actions .cora-sub-tabs-nav::-webkit-scrollbar {
        display: none !important;
    }
    </style>
    <script>
    (function() {
        if (window.coraAskExternalPlatform) return; // Prevent duplicate declarations

        window.coraGetReferQuery = function() {
            var urlParams = new URLSearchParams(window.location.search);
            var subPage = urlParams.get('sub_page') || 'dashboard';

            // 1. Content Suite
            if (subPage === 'blogs' || document.getElementById('cora-view-content-suite')) {
                var activePanelEl = document.querySelector('.cora-ct-panel:not(.hidden)');
                var activeContentTab = activePanelEl ? activePanelEl.id : '';
                if (activeContentTab.indexOf('ct-opportunities') !== -1) {
                    return "How do I generate SEO content opportunities, filter by intent/impact, and create brief drafts in Cora Workspace?";
                } else if (activeContentTab.indexOf('ct-calendar') !== -1) {
                    return "How do I schedule articles on the Content Editorial Calendar and edit call-time slots in Cora?";
                } else if (activeContentTab.indexOf('ct-library') !== -1) {
                    return "How do I organize the article document library, filter by status, and edit posts in Cora?";
                } else if (activeContentTab.indexOf('ct-seo') !== -1) {
                    return "How do I run the 11-point SEO audit check, score keywords, and inject FAQ schemas in Cora?";
                } else if (activeContentTab.indexOf('ct-performance') !== -1) {
                    return "How do I trace lead attributions and revenue back to articles in the Content Performance Ledger in Cora?";
                } else if (activeContentTab.indexOf('ct-automations') !== -1) {
                    return "How do I configure content auto-posting policies, syndication, and autonomy level limits in Cora?";
                } else if (activeContentTab.indexOf('ct-brain') !== -1) {
                    return "How do I build the Business Brain RAG knowledge base repository in Cora Workspace?";
                } else {
                    return "How do I use the Content Suite overview dashboard and KPI metrics in Cora Workspace?";
                }
            }

            // 2. Users (Team Management)
            if (subPage === 'users' || document.getElementById('cora-page-users')) {
                var activeTabEl = document.querySelector('.cora-tab-content:not(.hidden)');
                var activeTab = activeTabEl ? activeTabEl.id : 'tab-active-members';
                if (activeTab === 'tab-permissions-matrix') {
                    return "How do I configure the granular Permissions Matrix (View, Edit, No Access levels) in the Cora Workspace Platform?";
                } else if (activeTab === 'tab-custom-roles') {
                    return "How to create custom team roles and manage capabilities inside Cora Workspace?";
                } else if (activeTab === 'tab-attendance-logs') {
                    return "How to configure geofenced office location attendance logs and GPS check-ins in Cora Workspace?";
                } else if (activeTab === 'tab-owner-automations') {
                    return "How to setup automated owner digests, security alerts, and SMTP templates in Cora Workspace?";
                } else if (activeTab === 'tab-pending-invites') {
                    return "How to invite new users, manage pending invitations, and resend links in Cora Workspace?";
                } else {
                    return "How to manage team members, active accounts, and edit profile details in Cora Workspace?";
                }
            }

            // 3. Page Builder / Canvas
            if (subPage === 'canvas' || document.getElementById('cora-page-canvas')) {
                return "How do I use the GrapesJS visual page builder and prompt-to-layout AI engine in Cora?";
            }

            // 4. Financials
            if (subPage === 'financials') {
                return "How do I generate sales invoices, track expense statements, and review GST tax breakdowns in Cora Workspace?";
            }

            // 5. Media
            if (subPage === 'media') {
                return "How do I upload images, organize folders, crop assets, and edit SEO metadata in the Cora Media Library?";
            }

            // 6. Document Vault
            if (subPage === 'vault') {
                return "How do I use the Document Vault, send e-sign contracts, and run the 5-step guided document studio in Cora?";
            }

            // 7. Leads CRM
            if (subPage === 'leads' || document.getElementById('cora-leads-module-container')) {
                var activeTabBtn = document.querySelector('.cora-lead-subtab-btn.active');
                var activeLeadTab = activeTabBtn ? (activeTabBtn.getAttribute('data-tab') || '') : '';
                if (activeLeadTab === 'directory') {
                    return "How do I manage the CRM Leads Directory, filter by stages and assignees, and export CSV in Cora Workspace?";
                } else if (activeLeadTab === 'analytics') {
                    return "How do I analyze CRM funnel conversion rates, stage velocity, and revenue forecasts in Cora Workspace?";
                } else if (activeLeadTab === 'activity') {
                    return "How do I review CRM lead activity history, communication logs, and sales note timelines in Cora Workspace?";
                } else {
                    return "How do I configure the CRM lead funnel pipeline, drag-and-drop Kanban cards, and automate deal statuses in Cora?";
                }
            }

            // 8. Forms & Reviews
            if (subPage === 'forms') {
                return "How do I create client feedback forms, acquire WhatsApp-first reviews, and map custom Hinglish automation rules in Cora?";
            }

            // 9. Email Center
            if (subPage === 'emails') {
                return "How do I set up Hostinger SMTP, construct drip templates, and check outbox delivery logs in the Cora Email Center?";
            }

            // 10. Settings Suite
            if (subPage === 'settings-suite') {
                return "How do I modify my workspace details, update custom domains, and configure security backup policies in Cora Workspace?";
            }

            return "How do I configure and manage my workspaces in the Cora Workspace Platform?";
        };

        var _coraPlatformLastOpen = 0;
        window.coraAskExternalPlatform = function(platform, e) {
            if (e) {
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
                if (typeof e.preventDefault === 'function') e.preventDefault();
                if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
            }
            var now = Date.now();
            if (now - _coraPlatformLastOpen < 1200) {
                return false;
            }
            _coraPlatformLastOpen = now;

            var query = (typeof window.coraGetReferQuery === 'function') ? window.coraGetReferQuery() : '';

            // Copy to clipboard dynamically
            if (query && navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(query).then(function() {
                    // Clipboard copy successful
                }).catch(function() {
                    // Ignore fallback
                });
            }

            var url = '';
            var platformName = '';
            if (platform === 'openai') {
                platformName = 'ChatGPT';
                url = query ? ('https://chatgpt.com/?q=' + encodeURIComponent(query)) : 'https://chatgpt.com/';
            } else if (platform === 'gemini') {
                platformName = 'Gemini';
                url = query ? ('https://gemini.google.com/app?q=' + encodeURIComponent(query)) : 'https://gemini.google.com/app';
            } else if (platform === 'claude') {
                platformName = 'Claude';
                url = 'https://claude.ai/';
            } else if (platform === 'perplexity') {
                platformName = 'Perplexity';
                url = query ? ('https://www.perplexity.ai/?q=' + encodeURIComponent(query)) : 'https://www.perplexity.ai/';
            } else if (platform === 'youtube') {
                platformName = 'YouTube';
                url = query ? ('https://www.youtube.com/results?search_query=' + encodeURIComponent(query)) : 'https://www.youtube.com/';
            }

            if (url) {
                if (window.coraShowToast) {
                    window.coraShowToast('Opening ' + platformName + '...' + (query ? ' Query copied to clipboard!' : ''), 'info');
                }
                window.open(url, '_blank');
            }
            return false;
        };

        window.coraInitSubTabsDragScroll = function() {
            var tabContainers = document.querySelectorAll('.cora-sub-tabs-container, .cora-sticky-sub-tabs, #cora-forms-tabs, #cora-content-tabs, #cora-settings-tabs, #cora-emails-subtabs, #cora-canvas-subtabs');
            tabContainers.forEach(function(container) {
                if (!container || container.dataset.coraDragInit === 'true') return;
                container.dataset.coraDragInit = 'true';

                var isDown = false;
                var startX = 0;
                var scrollLeft = 0;
                var scrollTarget = (container.classList.contains('has-extra-actions') && container.querySelector('.cora-sub-tabs-nav')) ? container.querySelector('.cora-sub-tabs-nav') : container;

                container.addEventListener('mousedown', function(e) {
                    if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.closest('button, a, .cora-sub-tab, .cora-tab-btn, .cora-sub-tabs-extra'))) return;
                    isDown = true;
                    startX = e.pageX;
                    scrollLeft = scrollTarget.scrollLeft;
                });

                window.addEventListener('mouseup', function() {
                    if (isDown) {
                        isDown = false;
                        container.style.cursor = '';
                    }
                });

                container.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    var dx = e.pageX - startX;
                    if (Math.abs(dx) > 8) {
                        container.style.cursor = 'grabbing';
                        e.preventDefault();
                        scrollTarget.scrollLeft = scrollLeft - dx;
                    }
                });

                // Stuck state handler for subtle elevation shadow and white frosted surface
                var checkStuck = function() {
                    var rect = container.getBoundingClientRect();
                    var topbarEl = document.getElementById('cora-global-topbar');
                    var topbarH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--cora-topbar-height')) || 48;
                    var topbarBottom = topbarEl ? topbarEl.getBoundingClientRect().bottom : topbarH;
                    if (rect.top <= topbarBottom + 3) {
                        container.classList.add('cora-tabs-stuck');
                    } else {
                        container.classList.remove('cora-tabs-stuck');
                    }
                };
                window.addEventListener('scroll', checkStuck, { passive: true });
                var mainEl = document.querySelector('main.cora-main');
                if (mainEl) mainEl.addEventListener('scroll', checkStuck, { passive: true });
                checkStuck();

                // Auto-center active tab on load or switch
                var activeTab = container.querySelector('.cora-sub-tab.active, .cora-tab-btn.active');
                if (activeTab) {
                    setTimeout(function() {
                        var cRect = container.getBoundingClientRect();
                        var tRect = activeTab.getBoundingClientRect();
                        if (tRect.left < cRect.left || tRect.right > cRect.right) {
                            activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                        }
                    }, 120);
                }
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.coraInitSubTabsDragScroll);
        } else {
            window.coraInitSubTabsDragScroll();
        }
    })();
    </script>
    <?php endif; ?>
    <div class="cora-workspace-header select-none w-full max-w-full min-w-0 overflow-visible relative z-[45] <?php echo esc_attr( $args['container_class'] ?? '' ); ?>">
        <!-- Desktop Header -->
        <?php 
        $desktop_header_classes = ! empty( $visible_tabs ) 
            ? 'hidden md:flex items-center justify-between pb-2 mb-0 select-none' 
            : 'hidden md:flex items-center justify-between border-b border-zinc-200/50 pb-3 mb-2 select-none';
        ?>
        <div class="<?php echo esc_attr( $desktop_header_classes ); ?>">
            <div class="cora-page-header flex items-center gap-4.5">
                <div class="flex flex-col min-w-0">
                    <h1 class="cora-page-title text-xl font-bold tracking-tight text-zinc-900 leading-tight"><?php echo esc_html( $args['title'] ); ?></h1>
                    <?php if ( ! empty( $args['description'] ) ) : ?>
                        <p class="cora-section-desc text-xs text-zinc-400 mt-1 leading-normal"><?php echo esc_html( $args['description'] ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex items-center gap-3 shrink-0">
                <!-- LLM Platforms Stacked Overlapping Shortcuts (Desktop) -->
                <?php if ( $args['ai_stack'] ) : ?>
                <div class="cora-platform-stack flex items-center select-none mr-1" style="display: flex; align-items: center;">
                    <!-- ChatGPT Button -->
                    <button type="button" onclick="coraAskExternalPlatform('openai', event); event.stopPropagation(); return false;" class="group relative w-8 h-8 rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-750 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 shadow-3xs cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 dark:focus-visible:ring-white">
                        <?php echo cora_get_ai_platform_official_logo( 'openai', '', 16 ); ?>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-visible:opacity-100 group-focus-visible:visible transition-all duration-150 bg-zinc-950 dark:bg-zinc-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Ask ChatGPT
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950 dark:border-b-zinc-900"></span>
                        </span>
                    </button>
                    
                    <!-- Claude Button -->
                    <button type="button" onclick="coraAskExternalPlatform('claude', event); event.stopPropagation(); return false;" class="group relative w-8 h-8 rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-750 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 shadow-3xs cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 dark:focus-visible:ring-white">
                        <?php echo cora_get_ai_platform_official_logo( 'claude', '', 16 ); ?>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-visible:opacity-100 group-focus-visible:visible transition-all duration-150 bg-zinc-950 dark:bg-zinc-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Ask Claude
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950 dark:border-b-zinc-900"></span>
                        </span>
                    </button>
                    
                    <!-- Gemini Button -->
                    <button type="button" onclick="coraAskExternalPlatform('gemini', event); event.stopPropagation(); return false;" class="group relative w-8 h-8 rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-750 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 shadow-3xs cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 dark:focus-visible:ring-white">
                        <?php echo cora_get_ai_platform_official_logo( 'gemini', '', 16 ); ?>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-visible:opacity-100 group-focus-visible:visible transition-all duration-150 bg-zinc-950 dark:bg-zinc-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Ask Gemini
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950 dark:border-b-zinc-900"></span>
                        </span>
                    </button>

                    <!-- Perplexity Button -->
                    <button type="button" onclick="coraAskExternalPlatform('perplexity', event); event.stopPropagation(); return false;" class="group relative w-8 h-8 rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-750 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 shadow-3xs cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 dark:focus-visible:ring-white">
                        <?php echo cora_get_ai_platform_official_logo( 'perplexity', '', 16 ); ?>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-visible:opacity-100 group-focus-visible:visible transition-all duration-150 bg-zinc-950 dark:bg-zinc-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Ask Perplexity
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950 dark:border-b-zinc-900"></span>
                        </span>
                    </button>

                    <!-- YouTube Button -->
                    <button type="button" onclick="coraAskExternalPlatform('youtube', event); event.stopPropagation(); return false;" class="group relative w-8 h-8 rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-750 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 shadow-3xs cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 dark:focus-visible:ring-white">
                        <?php echo cora_get_ai_platform_official_logo( 'youtube', '', 16 ); ?>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-visible:opacity-100 group-focus-visible:visible transition-all duration-150 bg-zinc-950 dark:bg-zinc-900 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Search YouTube
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950 dark:border-b-zinc-900"></span>
                        </span>
                    </button>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $args['extra_actions_html'] ) ) : ?>
                    <?php echo $args['extra_actions_html']; ?>
                <?php endif; ?>
                
                <?php if ( ! empty( $cta['text'] ) && $cta['visible'] ) : ?>
                    <button <?php if ( ! empty( $cta['id'] ) ) : ?>id="<?php echo esc_attr( $cta['id'] ); ?>"<?php endif; ?> onclick="<?php echo esc_attr( $cta['onclick'] ); ?>" class="bg-zinc-950 hover:bg-zinc-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all cursor-pointer active:scale-98 shadow-sm flex items-center gap-1.5 border border-zinc-950 shrink-0 <?php echo esc_attr( $cta['class'] ); ?>">
                        <?php if ( ! empty( $cta['icon'] ) ) : ?>
                            <?php echo $cta['icon']; ?>
                        <?php else : ?>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <?php endif; ?>
                        <span><?php echo esc_html( $cta['text'] ); ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Header (Visible only on mobile) -->
        <div class="cora-workspace-header-mobile flex md:hidden items-center justify-between gap-3 mb-0 px-0 py-1 select-none h-[44px] min-h-[44px]">
            <div class="flex items-center gap-2 min-w-0 flex-1">
               
                <div class="min-w-0">
                    <h1 class="text-sm font-bold tracking-tight text-zinc-900 truncate"><?php echo esc_html( ! empty( $args['mobile_title'] ) ? $args['mobile_title'] : $args['title'] ); ?></h1>
                    <?php if ( ! empty( $args['mobile_description'] ) || ! empty( $args['description'] ) ) : ?>
                        <p class="text-[10px] text-zinc-400 truncate"><?php echo esc_html( ! empty( $args['mobile_description'] ) ? $args['mobile_description'] : $args['description'] ); ?></p>
                    <?php endif; ?>
                </div>

            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <!-- LLM Platforms Stacked Overlapping Shortcuts (Mobile) -->
                <?php if ( $args['ai_stack'] ) : ?>
                <div class="cora-platform-stack flex items-center select-none mr-0.5" style="display: flex; align-items: center;">
                    <!-- ChatGPT Button -->
                    <button type="button" onclick="coraAskExternalPlatform('openai', event); event.stopPropagation(); return false;" class="group relative rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-3xs cursor-pointer focus:outline-none" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0 !important;">
                        <?php echo cora_get_ai_platform_official_logo( 'openai', '', 13 ); ?>
                    </button>
                    <!-- Claude Button -->
                    <button type="button" onclick="coraAskExternalPlatform('claude', event); event.stopPropagation(); return false;" class="group relative rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-3xs cursor-pointer focus:outline-none" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0 !important;">
                        <?php echo cora_get_ai_platform_official_logo( 'claude', '', 13 ); ?>
                    </button>
                    <!-- Gemini Button -->
                    <button type="button" onclick="coraAskExternalPlatform('gemini', event); event.stopPropagation(); return false;" class="group relative rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-3xs cursor-pointer focus:outline-none" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0 !important;">
                        <?php echo cora_get_ai_platform_official_logo( 'gemini', '', 13 ); ?>
                    </button>
                    <!-- Perplexity Button -->
                    <button type="button" onclick="coraAskExternalPlatform('perplexity', event); event.stopPropagation(); return false;" class="group relative rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-3xs cursor-pointer focus:outline-none" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0 !important;">
                        <?php echo cora_get_ai_platform_official_logo( 'perplexity', '', 13 ); ?>
                    </button>
                    <!-- YouTube Button -->
                    <button type="button" onclick="coraAskExternalPlatform('youtube', event); event.stopPropagation(); return false;" class="group relative rounded-full border border-zinc-200/90 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-3xs cursor-pointer focus:outline-none" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; padding: 0 !important;">
                        <?php echo cora_get_ai_platform_official_logo( 'youtube', '', 13 ); ?>
                    </button>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $args['mobile_extra_actions_html'] ) ) : ?>
                    <?php echo $args['mobile_extra_actions_html']; ?>
                <?php elseif ( ! empty( $args['extra_actions_html'] ) && ! empty( $args['show_extra_actions_on_mobile'] ) ) : ?>
                    <?php echo $args['extra_actions_html']; ?>
                <?php endif; ?>
                
                <?php if ( ! empty( $cta['text'] ) && $cta['visible'] ) : 
                    $is_mobile_locked = ! empty( $cta['mobile_locked'] );
                    $mobile_btn_class = 'bg-zinc-950 hover:bg-zinc-800 text-white font-bold text-[10px] px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-1 shrink-0 ' . esc_attr( $cta['class'] );
                    if ( $is_mobile_locked ) {
                        $mobile_btn_class .= ' opacity-60 hover:opacity-75 ' . ( ! empty( $cta['mobile_class'] ) ? esc_attr( $cta['mobile_class'] ) : '' );
                    }
                ?>
                    <button <?php if ( ! empty( $cta['id'] ) ) : ?>id="<?php echo esc_attr( $cta['id'] ); ?>"<?php endif; ?> onclick="<?php echo esc_attr( $cta['onclick'] ); ?>" class="<?php echo trim( $mobile_btn_class ); ?>">
                        <?php if ( $is_mobile_locked ) : ?>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-300"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <?php elseif ( ! empty( $cta['icon'] ) ) : ?>
                            <?php echo str_replace( array('width="12"', 'height="12"', 'width="14"', 'height="14"'), 'width="10" height="10"', $cta['icon'] ); ?>
                        <?php else : ?>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <?php endif; ?>
                        <span><?php echo esc_html( $cta['mobile_text'] ); ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sub Navigation Tabs (Sticky Bar) -->
    <?php if ( ! empty( $visible_tabs ) ) : 
        $show_mcp = ! isset( $args['show_mcp_button'] ) || ! empty( $args['show_mcp_button'] );
        $has_extra = ! empty( $args['tabs_extra_html'] ) || $show_mcp;
        $container_extra_class = $has_extra ? ' has-extra-actions' : '';
        $container_overflow = $has_extra ? 'overflow: hidden;' : 'overflow-x: auto; overflow-y: hidden;';
    ?>
        <div class="cora-sub-tabs-container cora-sticky-sub-tabs<?php echo $container_extra_class; ?> relative flex items-center justify-between border-b border-zinc-200/80 dark:border-zinc-800 bg-transparent -mx-3 sm:-mx-4 md:-mx-5 px-3 sm:px-4 md:px-5 w-[calc(100%+1.5rem)] sm:w-[calc(100%+2rem)] md:w-[calc(100%+2.5rem)] max-w-[calc(100%+1.5rem)] sm:max-w-[calc(100%+2rem)] md:max-w-[calc(100%+2.5rem)]" id="<?php echo esc_attr( $args['tabs_dom_id'] ?? 'cora-sub-navigation-tabs' ); ?>" style="position: -webkit-sticky; position: sticky; top: var(--cora-sticky-tabs-top, 0px); left: 0; right: 0; z-index: 45; display: flex; flex-wrap: nowrap; background-color: transparent; <?php echo $container_overflow; ?> -webkit-overflow-scrolling: touch; touch-action: pan-x pan-y; overscroll-behavior-x: contain; scrollbar-width: none; min-height: 44px; height: 44px; padding-top: 0; padding-bottom: 0;">
            <div class="cora-sub-tabs-nav flex items-center gap-1 <?php echo $has_extra ? 'min-w-0 flex-1' : 'shrink-0'; ?> overflow-x-auto scrollbar-hide" style="touch-action: pan-x pan-y; -webkit-overflow-scrolling: touch; overscroll-behavior-x: contain; scrollbar-width: none;">
            <?php foreach ( $visible_tabs as $tab ) : 
                $active_class = ! empty( $tab['active'] ) ? 'active border-zinc-950 text-zinc-900 dark:border-white dark:text-white font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 font-semibold';
                $onclick_attr = ! empty( $tab['onclick'] ) ? 'onclick="' . esc_attr( $tab['onclick'] ) . '"' : '';
            ?>
                <button <?php if ( ! empty( $tab['dom_id'] ) ) : ?>id="<?php echo esc_attr( $tab['dom_id'] ); ?>"<?php endif; ?> class="cora-sub-tab cora-tab-btn px-2.5 sm:px-3.5 h-[40px] py-0 -mb-px border-b-2 text-xs cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap shrink-0 outline-none focus:outline-none focus-visible:outline-none select-none <?php echo $active_class; ?>" data-target="<?php echo esc_attr( $tab['id'] ); ?>" <?php echo $onclick_attr; ?> style="touch-action: pan-x pan-y; -webkit-tap-highlight-color: transparent;">
                    <?php if ( ! empty( $tab['icon'] ) ) : ?>
                        <span class="pointer-events-none flex items-center shrink-0"><?php echo $tab['icon']; ?></span>
                    <?php endif; ?>
                    <span class="pointer-events-none <?php echo ! empty( $tab['mobile_label'] ) ? 'hidden sm:inline' : ''; ?>"><?php echo esc_html( $tab['label'] ); ?></span>
                    <?php if ( ! empty( $tab['mobile_label'] ) ) : ?>
                        <span class="pointer-events-none sm:hidden"><?php echo esc_html( $tab['mobile_label'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( ! empty( $tab['badge'] ) ) : ?>
                        <span class="pointer-events-none flex items-center"><?php echo $tab['badge']; ?></span>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
            </div>
            <?php if ( $has_extra ) : ?>
                <div class="cora-sub-tabs-extra h-full flex items-center gap-1.5 sm:gap-2 pl-2 sm:pl-3 ml-auto shrink-0 select-none">
                    <?php 
                    if ( ! empty( $args['tabs_extra_html'] ) ) {
                        echo $args['tabs_extra_html'];
                    }
                    if ( $show_mcp && ( empty( $args['tabs_extra_html'] ) || strpos( $args['tabs_extra_html'], 'coraOpenWorkspaceDeveloperGateway' ) === false ) ) {
                        echo cora_get_mcp_button_html();
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php
}
}
