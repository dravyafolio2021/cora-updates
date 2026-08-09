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
    
    ?>
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
            if (subPage === 'leads') {
                return "How do I configure the CRM lead funnel pipeline, drag-and-drop Kanban cards, and automate deal statuses in Cora?";
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

        window.coraAskExternalPlatform = function(platform) {
            var query = window.coraGetReferQuery();

            // Copy to clipboard dynamically
            if (navigator.clipboard && navigator.clipboard.writeText) {
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
                url = 'https://chatgpt.com/?q=' + encodeURIComponent(query);
            } else if (platform === 'gemini') {
                platformName = 'Gemini';
                url = 'https://gemini.google.com/app?q=' + encodeURIComponent(query);
            } else if (platform === 'claude') {
                platformName = 'Claude';
                url = 'https://claude.ai/';
            } else if (platform === 'perplexity') {
                platformName = 'Perplexity';
                url = 'https://www.perplexity.ai/?q=' + encodeURIComponent(query);
            }

            if (url) {
                if (window.coraShowToast) {
                    window.coraShowToast('Opening ' + platformName + '... Query copied to clipboard!', 'info');
                } else {
                    console.log('Opening ' + platformName + '... Query copied to clipboard!');
                }
                window.open(url, '_blank');
            }
        };
    })();
    </script>
    <div class="cora-workspace-header select-none w-full max-w-full min-w-0 overflow-visible <?php echo esc_attr( $args['container_class'] ); ?>">
        <!-- Desktop Header -->

        <div class="hidden md:flex items-center justify-between border-b border-zinc-200/50 dark:border-zinc-800/40 pb-5 mb-5 select-none">
            <div class="cora-page-header flex items-center gap-4.5">
                <div class="flex flex-col min-w-0">
                    <h1 class="cora-page-title text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 leading-tight"><?php echo esc_html( $args['title'] ); ?></h1>
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
                    <button onclick="coraAskExternalPlatform('openai')" class="group relative w-10 h-10 rounded-full border-0 bg-emerald-50/70 hover:bg-emerald-100/50 dark:bg-emerald-950/20 dark:hover:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none" style="z-index: 5 !important; margin-left: 0px !important;">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" class="w-5 h-5"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432 4.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523z"/></svg>
                        <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                            Ask ChatGPT
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950"></span>
                        </span>
                    </button>
                    
                    <!-- Claude Button -->
                    <button onclick="coraAskExternalPlatform('claude')" class="group relative w-10 h-10 rounded-full border-0 bg-amber-50/70 hover:bg-amber-100/50 dark:bg-amber-950/20 dark:hover:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-500 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none" style="z-index: 4 !important; margin-left: -14px !important;">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" class="w-5 h-5"><path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"/></svg>
                         <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                             Ask Claude
                             <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950"></span>
                         </span>
                     </button>
                     
                     <!-- Gemini Button -->
                     <button onclick="coraAskExternalPlatform('gemini')" class="group relative w-10 h-10 rounded-full border-0 bg-blue-5/70 hover:bg-blue-100/50 dark:bg-blue-950/20 dark:hover:bg-blue-950/40 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none" style="z-index: 3 !important; margin-left: -14px !important;">
                         <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" class="w-5 h-5"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                         <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                             Ask Gemini
                             <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950"></span>
                         </span>
                     </button>
                     
                     <!-- Perplexity Button -->
                     <button onclick="coraAskExternalPlatform('perplexity')" class="group relative w-10 h-10 rounded-full border-0 bg-zinc-50/50 hover:bg-zinc-100/80 dark:bg-zinc-800/20 dark:hover:bg-zinc-800/40 flex items-center justify-center text-zinc-650 dark:text-zinc-400 transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none" style="z-index: 2 !important; margin-left: -14px !important;">
                         <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"></line></svg>
                         <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                             Ask Perplexity
                             <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950"></span>
                         </span>
                     </button>
                     
                     <!-- YouTube Button -->
                     <?php if ( ! empty( $args['tutorial_onclick'] ) ) : ?>
                     <button onclick="<?php echo esc_attr( $args['tutorial_onclick'] ); ?>" class="group relative w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 bg-white hover:bg-zinc-50 dark:bg-zinc-950 dark:hover:bg-zinc-900 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 hover:scale-110 hover:z-50 shadow-2xs cursor-pointer focus:outline-none" style="z-index: 1 !important; margin-left: -14px !important;">
                         <svg viewBox="0 0 24 24" width="20" height="20" fill="none" class="w-5 h-5"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" fill="#FF0000"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#FFFFFF"/></svg>
                         <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 bg-zinc-950 text-white text-[10px] font-semibold py-1.5 px-2.5 rounded-lg shadow-md whitespace-nowrap pointer-events-none z-50">
                             Tutorial Walkthrough
                             <span class="absolute bottom-full left-1/2 -translate-x-1/2 border-[4px] border-transparent border-b-zinc-950"></span>
                         </span>
                     </button>
                     <?php endif; ?>
                 </div>

                <?php endif; ?>
                
                <?php if ( ! empty( $cta['text'] ) && $cta['visible'] ) : ?>
                    <button onclick="<?php echo esc_attr( $cta['onclick'] ); ?>" class="bg-zinc-950 hover:bg-zinc-900 dark:bg-zinc-50 dark:hover:bg-zinc-100 text-white dark:text-zinc-950 font-bold text-xs px-4 py-2.5 rounded-xl transition-all cursor-pointer active:scale-98 shadow-sm flex items-center gap-1.5 border border-zinc-950 dark:border-zinc-50 shrink-0 <?php echo esc_attr( $cta['class'] ); ?>">
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
        <div class="flex md:hidden items-center justify-between gap-3 mb-2 px-0 py-3 border-b border-zinc-200/60 dark:border-zinc-800 bg-white dark:bg-zinc-900 select-none">
            <div class="flex items-center gap-2 min-w-0">
               
                <div class="min-w-0">
                    <h1 class="text-sm font-bold tracking-tight text-zinc-900 dark:text-zinc-100 truncate"><?php echo esc_html( ! empty( $args['mobile_title'] ) ? $args['mobile_title'] : $args['title'] ); ?></h1>
                    <?php if ( ! empty( $args['mobile_description'] ) || ! empty( $args['description'] ) ) : ?>
                        <p class="text-[10px] text-zinc-400 truncate"><?php echo esc_html( ! empty( $args['mobile_description'] ) ? $args['mobile_description'] : $args['description'] ); ?></p>
                    <?php endif; ?>
                </div>

            </div>
            
            <div class="flex items-center gap-2.5 shrink-0">
                <!-- LLM Platforms Stacked Overlapping Shortcuts (Mobile) -->
                <?php if ( $args['ai_stack'] ) : ?>
                <div class="cora-platform-stack flex items-center select-none mr-0.5" style="display: flex; align-items: center;">
                    <!-- ChatGPT Button -->
                    <button onclick="coraAskExternalPlatform('openai')" class="group relative rounded-full border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/70 dark:bg-emerald-950/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs cursor-pointer focus:outline-none" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; z-index: 5 !important; margin-left: 0px !important; padding: 0 !important;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M9.205 8.658v-2.26c0-.19.072-.333.238-.428l4.543-2.616c.619-.357 1.356-.523 2.117-.523 2.854 0 4.662 2.212 4.662 4.566 0 .167 0 .357-.024.547l-4.71-2.759a.797.797 0 00-.856 0l-5.97 3.473zm10.609 8.8V12.06c0-.333-.143-.57-.429-.737l-5.97-3.473 1.95-1.118a.433.433 0 01.476 0l4.543 2.617c1.309.76 2.189 2.378 2.189 3.948 0 1.808-1.07 3.473-2.76 4.163zM7.802 12.703l-1.95-1.142c-.167-.095-.239-.238-.239-.428V5.899c0-2.545 1.95-4.472 4.591-4.472 1 0 1.927.333 2.712.928L8.23 5.067c-.285.166-.428.404-.428.737v6.898zM12 15.128l-2.795-1.57v-3.33L12 8.658l2.795 1.57v3.33L12 15.128zm1.796 7.23c-1 0-1.927-.332-2.712-.927l4.686-2.712c.285-.166.428-.404.428-.737v-6.898l1.974 1.142c.167.095.238.238.238.428v5.233c0 2.545-1.974 4.472-4.614 4.472zm-5.637-5.303l-4.544-2.617c-1.308-.761-2.188-2.378-2.188-3.948A4.482 4.482 0 014.21 6.327v5.423c0 .333.143.571.428.738l5.947 3.449-1.95 1.118a.432 4.432 0 01-.476 0zm-.262 3.9c-2.688 0-4.662-2.021-4.662-4.519 0-.19.024-.38.047-.57l4.686 2.71c.286.167.571.167.856 0l5.97-3.448v2.26c0 .19-.07.333-.237.428l-4.543 2.616c-.619.357-1.356.523-2.117.523z"/></svg>
                    </button>
                    <!-- Claude Button -->
                    <button onclick="coraAskExternalPlatform('claude')" class="group relative rounded-full border border-amber-100 dark:border-amber-900/30 bg-amber-50/70 dark:bg-amber-950/20 flex items-center justify-center text-amber-600 dark:text-amber-500 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs cursor-pointer focus:outline-none" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; z-index: 4 !important; margin-left: -8px !important; padding: 0 !important;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="m4.7144 15.9555 4.7174-2.6471.079-.2307-.079-.1275h-.2307l-.7893-.0486-2.6956-.0729-2.3375-.0971-2.2646-.1214-.5707-.1215-.5343-.7042.0546-.3522.4797-.3218.686.0608 1.5179.1032 2.2767.1578 1.6514.0972 2.4468.255h.3886l.0546-.1579-.1336-.0971-.1032-.0972L6.973 9.8356l-2.55-1.6879-1.3356-.9714-.7225-.4918-.3643-.4614-.1578-1.0078.6557-.7225.8803.0607.2246.0607.8925.686 1.9064 1.4754 2.4893 1.8336.3643.3035.1457-.1032.0182-.0728-.164-.2733-1.3539-2.4467-1.445-2.4893-.6435-1.032-.17-.6194c-.0607-.255-.1032-.4674-.1032-.7285L6.287.1335 6.6997 0l.9957.1336.419.3642.6192 1.4147 1.0018 2.2282 1.5543 3.0296.4553.8985.2429.8318.091.255h.1579v-.1457l.1275-1.706.2368-2.0947.2307-2.6957.0789-.7589.3764-.9107.7468-.4918.5828.2793.4797.686-.0668.4433-.2853 1.8517-.5586 2.9021-.3643 1.9429h.2125l.2429-.2429.9835-1.3053 1.6514-2.0643.7286-.8196.85-.9046.5464-.4311h1.0321l.759 1.1293-.34 1.1657-1.0625 1.3478-.8804 1.1414-1.2628 1.7-.7893 1.36.0729.1093.1882-.0183 2.8535-.607 1.5421-.2794 1.8396-.3157.8318.3886.091.3946-.3278.8075-1.967.4857-2.3072.4614-3.4364.8136-.0425.0304.0486.0607 1.5482.1457.6618.0364h1.621l3.0175.2247.7892.522.4736.6376-.079.4857-1.2142.6193-1.6393-.3886-3.825-.9107-1.3113-.3279h-.1822v.1093l1.0929 1.0686 2.0035 1.8092 2.5075 2.3314.1275.5768-.3218.4554-.34-.0486-2.2039-1.6575-.85-.7468-1.9246-1.621h-.1275v.17l.4432.6496 2.3436 3.5214.1214 1.0807-.17.3521-.6071.2125-.6679-.1214-1.3721-1.9246L14.38 17.959l-1.1414-1.9428-.1397.079-.674 7.2552-.3156.3703-.7286.2793-.6071-.4614-.3218-.7468.3218-1.4753.3886-1.9246.3157-1.53.2853-1.9004.17-.6314-.0121-.0425-.1397.0182-1.4328 1.9672-2.1796 2.9446-1.7243 1.8456-.4128.164-.7164-.3704.0667-.6618.4008-.5889 2.386-3.0357 1.4389-1.882.929-1.0868-.0062-.1579h-.0546l-6.3385 4.1164-1.1293.1457-.4857-.4554.0608-.7467.2307-.2429 1.9064-1.3114Z"/></svg>
                    </button>
                    <!-- Gemini Button -->
                    <button onclick="coraAskExternalPlatform('gemini')" class="group relative rounded-full border border-blue-100 dark:border-blue-900/30 bg-blue-50/70 dark:bg-blue-950/20 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs cursor-pointer focus:outline-none" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; z-index: 3 !important; margin-left: -8px !important; padding: 0 !important;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M11.04 19.32Q12 21.51 12 24q0-2.49.93-4.68.96-2.19 2.58-3.81t3.81-2.55Q21.51 12 24 12q-2.49 0-4.68-.93a12.3 12.3 0 0 1-3.81-2.58 12.3 12.3 0 0 1-2.58-3.81Q12 2.49 12 0q0 2.49-.96 4.68-.93 2.19-2.55 3.81a12.3 12.3 0 0 1-3.81 2.58Q2.49 12 0 12q2.49 0 4.68.96 2.19.93 3.81 2.55t2.55 3.81"/></svg>
                    </button>
                    <!-- Perplexity Button -->
                    <button onclick="coraAskExternalPlatform('perplexity')" class="group relative rounded-full border border-zinc-200 dark:border-zinc-800 bg-zinc-50/80 dark:bg-zinc-900 flex items-center justify-center text-zinc-900 dark:text-zinc-100 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs cursor-pointer focus:outline-none" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; z-index: 2 !important; margin-left: -8px !important; padding: 0 !important;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line><line x1="4.93" y1="19.07" x2="19.07" y2="4.93"></line></svg>
                    </button>
                    <!-- YouTube Button -->
                    <?php if ( ! empty( $args['tutorial_onclick'] ) ) : ?>
                    <button onclick="<?php echo esc_attr( $args['tutorial_onclick'] ); ?>" class="group relative rounded-full border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 flex items-center justify-center transition-all duration-200 hover:-translate-y-0.5 shadow-2xs cursor-pointer focus:outline-none" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; z-index: 1 !important; margin-left: -8px !important; padding: 0 !important;">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" fill="#FF0000"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#FFFFFF"/></svg>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                
                <?php if ( ! empty( $cta['text'] ) && $cta['visible'] ) : ?>
                    <button onclick="<?php echo esc_attr( $cta['onclick'] ); ?>" class="bg-zinc-950 hover:bg-zinc-800 dark:bg-zinc-100 dark:hover:bg-zinc-200 text-white dark:text-zinc-950 font-bold text-[10px] px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer active:scale-95 shadow-sm flex items-center gap-1 shrink-0 <?php echo esc_attr( $cta['class'] ); ?>">
                        <?php if ( ! empty( $cta['icon'] ) ) : ?>
                            <?php echo str_replace( array('width="12"', 'height="12"', 'width="14"', 'height="14"'), 'width="10" height="10"', $cta['icon'] ); ?>
                        <?php else : ?>
                            <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <?php endif; ?>
                        <span><?php echo esc_html( $cta['mobile_text'] ); ?></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sub Navigation Tabs -->
        <?php if ( ! empty( $visible_tabs ) ) : ?>
            <!-- Desktop Sub Navigation Tabs -->
            <div class="cora-sub-tabs-container hidden md:flex border-b border-zinc-200 dark:border-zinc-800 items-center gap-1.5 overflow-x-auto pb-px shrink-0 select-none no-scrollbar mb-4 w-full max-w-full min-w-0">
                <?php foreach ( $visible_tabs as $tab ) : 
                    $active_class = ! empty( $tab['active'] ) ? 'active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold' : 'border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 font-medium';
                    $onclick_attr = ! empty( $tab['onclick'] ) ? 'onclick="' . esc_attr( $tab['onclick'] ) . '"' : '';
                ?>
                    <button <?php if ( ! empty( $tab['dom_id'] ) ) : ?>id="<?php echo esc_attr( $tab['dom_id'] ); ?>"<?php endif; ?> class="cora-sub-tab flex items-center gap-2 px-3 pb-2.5 pt-1 text-xs border-b-2 transition-all cursor-pointer whitespace-nowrap <?php echo $active_class; ?>" data-target="<?php echo esc_attr( $tab['id'] ); ?>" <?php echo $onclick_attr; ?>>
                        <?php if ( ! empty( $tab['icon'] ) ) : ?>
                            <?php echo $tab['icon']; ?>
                        <?php endif; ?>
                        <?php echo esc_html( $tab['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Mobile Sub Navigation Tabs -->
            <div class="cora-sub-tabs-container flex md:hidden items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-px mb-4 px-0 bg-white dark:bg-zinc-900 relative select-none">
                <div class="flex items-center gap-1.5">
                    <?php 
                    $direct_tabs = array_slice( $visible_tabs, 0, 2 );
                    $dropdown_tabs = array_slice( $visible_tabs, 2 );
                    
                    foreach ( $direct_tabs as $tab ) :
                        $active_class = ! empty( $tab['active'] ) ? 'active border-zinc-950 dark:border-zinc-100 text-zinc-950 dark:text-zinc-100 font-semibold' : 'border-transparent text-zinc-550 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 font-medium';
                        $onclick_attr = ! empty( $tab['onclick'] ) ? 'onclick="' . esc_attr( $tab['onclick'] ) . '"' : '';
                    ?>
                        <button <?php if ( ! empty( $tab['dom_id'] ) ) : ?>id="<?php echo esc_attr( $tab['dom_id'] ); ?>"<?php endif; ?> class="cora-sub-tab flex items-center gap-1.5 px-2.5 pb-2 pt-1 text-[11px] border-b-[1.5px] transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none <?php echo $active_class; ?>" data-target="<?php echo esc_attr( $tab['id'] ); ?>" <?php echo $onclick_attr; ?>>
                            <?php if ( ! empty( $tab['icon'] ) ) : ?>
                                <?php echo str_replace( array('width="13"', 'height="13"', 'width="14"', 'height="14"'), 'width="11" height="11"', $tab['icon'] ); ?>
                            <?php endif; ?>
                            <?php echo esc_html( $tab['mobile_label'] ?? $tab['label'] ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php if ( ! empty( $dropdown_tabs ) ) : ?>
                <!-- More Button and Floating Dropdown Panel -->
                <div class="relative">
                    <button id="mobile-tabs-more-btn" class="flex items-center gap-1.5 px-2.5 py-1.5 text-[11px] font-medium text-zinc-650 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-all cursor-pointer focus:outline-none focus:ring-0 outline-none shadow-none border-0 bg-transparent">
                        <span>More</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="transition-transform" id="more-chevron-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>

                    <!-- Floating Right-Aligned Dropdown Menu Card -->
                    <div id="mobile-tabs-more-dropdown" class="hidden absolute right-0 top-full mt-1.5 z-30 w-48 bg-white dark:bg-zinc-900 border border-zinc-200/60 dark:border-zinc-800 rounded-lg shadow-md py-1 animate-in fade-in duration-100">
                        <?php foreach ( $dropdown_tabs as $tab ) : 
                            $active_class = ! empty( $tab['active'] ) ? 'active bg-zinc-50 dark:bg-zinc-850 text-zinc-950 dark:text-zinc-100 font-semibold' : 'text-zinc-650 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800 font-medium';
                            $onclick_attr = ! empty( $tab['onclick'] ) ? 'onclick="' . esc_attr( $tab['onclick'] ) . '"' : '';
                        ?>
                            <button <?php if ( ! empty( $tab['dom_id'] ) ) : ?>id="<?php echo esc_attr( $tab['dom_id'] ); ?>"<?php endif; ?> class="cora-sub-tab flex items-center gap-2 w-full px-3 py-2 text-left text-[11px] transition-all cursor-pointer whitespace-nowrap focus:outline-none focus:ring-0 outline-none shadow-none <?php echo $active_class; ?>" data-target="<?php echo esc_attr( $tab['id'] ); ?>" <?php echo $onclick_attr; ?>>
                                <?php if ( ! empty( $tab['icon'] ) ) : ?>
                                    <?php echo str_replace( array('width="13"', 'height="13"', 'width="14"', 'height="14"'), 'width="11" height="11"', $tab['icon'] ); ?>
                                <?php endif; ?>
                                <?php echo esc_html( $tab['label'] ); ?>
                            </button>

                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
}
