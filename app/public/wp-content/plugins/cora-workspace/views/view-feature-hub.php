<?php
/**
 * View: App Modules & Feature Hub
 * Allows workspace owners to dynamically enable or disable modules at the workspace tenant level.
 * Features instant AJAX switch toggles that sync with sidebar, AI Agent, roles matrix, and UI telemetry.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$cora_industry = function_exists( 'cora_get_active_industry' ) ? cora_get_active_industry() : 'real_estate';
$is_studio = ( strpos( strtolower( $cora_industry ), 'photo' ) !== false || strpos( strtolower( $cora_industry ), 'studio' ) !== false );
$enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();

$features_list = array(
    'Workspace & Core' => array(
        'blogs' => array(
            'title' => 'Content Suite',
            'desc'  => 'Create and publish custom blog articles, posts, and marketing materials.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
        ),
        'financials' => array(
            'title' => 'Financial Overview',
            'desc'  => 'Track agency transactions, GST invoicing, cash projections, and billing records.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        ),
        'team-roles' => array(
            'title' => 'User & Roles',
            'desc'  => 'Configure team members, custom roles, permission matrix, and attendance tracking.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
        ),
        'media' => array(
            'title' => $is_studio ? 'Media Proofing Manager' : 'Media Manager',
            'desc'  => $is_studio ? 'Manage photo shoots, proofs, delivery galleries, and client approvals.' : 'Manage images, videos, and galleries with advanced metadata and filtering.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>'
        ),
        'vault' => array(
            'title' => 'File & Document Vault',
            'desc'  => 'Secure encrypted file storage for client contracts, RAW media, and NDA records.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
        ),
        'calendar' => array(
            'title' => 'Calendar',
            'desc'  => 'Consolidated monthly calendar for shoots, showings, and team scheduling.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'activity-timeline' => array(
            'title' => 'Activity Timeline',
            'desc'  => 'Multi-day timeline planner for operations, logs, and activity events.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
        ),
        'automations' => array(
            'title' => 'Automations & Workflows',
            'desc'  => 'Set up triggers, action routines, email updates, and background automation loops.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>'
        ),
        'inbox' => array(
            'title' => 'Unified Inbox',
            'desc'  => 'Integrated customer support messaging inbox mapping WhatsApp and Email.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>'
        ),
        'analytics' => array(
            'title' => 'Analytics',
            'desc'  => 'Business intelligence monitoring, conversion charts, and visual performance graphs.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
        ),
        'social-meta' => array(
            'title' => 'Facebook & Instagram',
            'desc'  => 'Social media integration suite to preview scheduled campaigns and grids.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>'
        )
    ),
    'Operations' => array(
        'leads' => array(
            'title' => $is_studio ? 'Client Leads (CRM)' : 'Buyer Leads (CRM)',
            'desc'  => 'Kanban CRM funnel stages to track, nurture, and convert inquiries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
        ),
        'crew_scheduler' => array(
            'title' => $is_studio ? 'Team Scheduler' : 'Agent Scheduler',
            'desc'  => 'Manage team shifts, availability grids, and dispatch assignees to bookings.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
        ),
        'equipment' => array(
            'title' => $is_studio ? 'Camera Equipment' : 'Property Listings & Inventory',
            'desc'  => $is_studio ? 'Track camera bodies, lenses, lighting gear, custody logs, and status audits.' : 'List buildings, office locations, plot configurations, and geocoded coordinates.',
            'icon'  => $is_studio ? '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>' : '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
        ),
        'tasks' => array(
            'title' => 'Client Task Manager',
            'desc'  => 'Collaborative task checklists shared with clients, with file attachments.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>'
        ),
        'attendance' => array(
            'title' => 'Attendance Logs',
            'desc'  => 'Geofenced GPS clock-in logs, IP restriction tracking, and daily activity heatmaps.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>'
        )
    ),
    'Sales Channel' => array(
        'canvas' => array(
            'title' => 'Canvas Website Builder',
            'desc'  => 'Premium website and landing page design builder with live draft previewing.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
        ),
        'forms' => array(
            'title' => 'Forms Manager',
            'desc'  => 'Build customer intake forms, payment links, and embedded contract signatures.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><path d="M9 15l2 2 4-4"></path></svg>'
        ),
        'emails' => array(
            'title' => 'Emails Studio',
            'desc'  => 'Create and schedule SMTP email broadcasts and tenant notifications.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>'
        ),
        'review_acquisition' => array(
            'title' => 'Reviews & Feedback',
            'desc'  => 'Automate Google Places review collection campaigns and feedback response alerts.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="M9 12l2 2 4-4"></path></svg>'
        )
    ),
    'AI Marketing & Tools' => array(
        'gbp' => array(
            'title' => 'Google Profile',
            'desc'  => 'Sync Google Business Profile listings and automate local citation reviews.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" class="shrink-0" style="stroke: none !important; fill: none !important;"><circle cx="12" cy="12" r="11" fill="#ffffff" style="fill: #ffffff !important; stroke: #e4e4e7 !important; stroke-width: 0.8px !important;"></circle><g transform="matrix(0.55 0 0 0.55 5.4 5.4)"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" style="fill: #4285F4 !important; stroke: none !important;"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" style="fill: #34A853 !important; stroke: none !important;"></path><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" style="fill: #FBBC05 !important; stroke: none !important;"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" style="fill: #EA4335 !important; stroke: none !important;"></path></g></svg>'
        ),
        'mcp' => array(
            'title' => 'AI Tools MCP',
            'desc'  => 'Manage user-specific Model Context Protocol gateways and AI developer tools.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'
        ),
        'knowledge-base' => array(
            'title' => 'RAG Knowledge Base',
            'desc'  => 'Upload PDFs and documents to vectorize search context for AI client queries.',
            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>'
        )
    )
);
?>
<div class="cora-fh-container" style="user-select: none;">
    <?php
    $modules_header_args = array(
        'title'            => 'App Modules & Feature Customizer',
        'description'      => 'Toggle modules ON or OFF to customize your workspace. Changes instantly adapt the sidebar layout, AI Agent context, and user permissions.',
        'icon'             => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>',
        'ai_stack'         => true,
        'tutorial_onclick' => "window.open('https://www.youtube.com/@heycora', '_blank')",
    );

    if ( function_exists( 'cora_render_workspace_header' ) ) {
        cora_render_workspace_header( $modules_header_args );
    }
    ?>

    <div style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 20px; padding: 28px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04); box-sizing: border-box; width: 100%;">
        <form id="cora-custom-features-form" onsubmit="event.preventDefault();" style="display: flex; flex-direction: column; gap: 28px;">
            <?php foreach ( $features_list as $category => $items ) : ?>
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <h3 style="font-size: 12px; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 0.06em; margin: 0; padding-bottom: 6px; border-bottom: 1px solid #f4f4f5; display: flex; align-items: center; justify-content: space-between;">
                        <span><?php echo esc_html( $category ); ?></span>
                        <span style="font-size: 11px; font-weight: 500; text-transform: none; color: #a1a1aa;"><?php echo count( $items ); ?> modules available</span>
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 14px;">
                        <?php foreach ( $items as $slug => $data ) :
                            $is_active = in_array( $slug, $enabled, true );
                        ?>
                            <div style="background: #ffffff; border: 1px solid #e4e4e7; border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px; transition: all 0.2s ease-in-out; position: relative;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: #f4f4f5; display: flex; align-items: center; justify-content: center; color: #18181b; shrink-to-fit: 0; flex-shrink: 0;">
                                    <?php echo $data['icon']; ?>
                                </div>
                                <div style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column; gap: 3px;">
                                    <div style="font-size: 13px; font-weight: 700; color: #09090b; display: flex; align-items: center; gap: 6px;">
                                        <span><?php echo esc_html( $data['title'] ); ?></span>
                                        <?php if ( $is_active ) : ?>
                                            <span style="font-size: 9px; font-weight: 700; background: #f4f4f5; color: #27272a; padding: 1px 6px; border-radius: 4px;">Active</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 11px; color: #71717a; line-height: 1.35;"><?php echo esc_html( $data['desc'] ); ?></div>
                                </div>
                                <div style="flex-shrink: 0; align-self: center;">
                                    <label class="cora-switch-label" style="position: relative; display: inline-block; width: 38px; height: 22px;">
                                        <input type="checkbox" name="features[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $is_active ); ?> onchange="window.coraToggleCustomFeature(this);" style="opacity: 0; width: 0; height: 0;">
                                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e4e4e7; transition: .25s ease-in-out; border-radius: 9999px;"></span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
</div>

<style>
/* Monochromatic Switch Animations */
.cora-switch-label input:checked + span {
    background-color: #09090b !important;
}
.cora-switch-label span:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .25s ease-in-out;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
}
.cora-switch-label input:checked + span:before {
    transform: translateX(16px);
}
</style>

<script>
(function($) {
    window.coraToggleCustomFeature = function(checkbox) {
        const form = $('#cora-custom-features-form');
        const enabledFeatures = [];
        form.find('input[name="features[]"]:checked').each(function() {
            enabledFeatures.push($(this).val());
        });

        if (typeof window.coraShowToast === 'function') {
            window.coraShowToast("Updating workspace modules...");
        }

        const ajaxUrl = (typeof coraREData !== 'undefined' && coraREData.ajaxUrl) ? coraREData.ajaxUrl : ((typeof coraData !== 'undefined' && coraData.ajaxUrl) ? coraData.ajaxUrl : '/wp-admin/admin-ajax.php');
        const ajaxNonce = (typeof coraREData !== 'undefined' && coraREData.ajaxNonce) ? coraREData.ajaxNonce : ((typeof coraData !== 'undefined' && coraData.ajaxNonce) ? coraData.ajaxNonce : '');

        $.post(ajaxUrl, {
            action: 'cora_save_custom_features',
            security: ajaxNonce,
            nonce: ajaxNonce,
            features: enabledFeatures
        }, function(response) {
            if (response && response.success) {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast("Workspace modules updated! Refreshing layout...");
                }
                setTimeout(function() {
                    window.location.reload();
                }, 600);
            } else {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(response.data?.message || "Failed to update modules.");
                }
            }
        }).fail(function() {
            if (typeof window.coraShowToast === 'function') {
                window.coraShowToast("Connection error. Failed to update modules.");
            }
        });
    };
})(jQuery);
</script>
