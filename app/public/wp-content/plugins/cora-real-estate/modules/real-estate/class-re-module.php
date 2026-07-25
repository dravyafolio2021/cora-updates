<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Real_Estate_Module implements Cora_Module_Interface {
    public function get_module_id() : string {
        return 'real_estate';
    }

    public function get_display_name() : string {
        return 'Real Estate Agency';
    }

    public function get_navigation_groups(string $active_role) : array {
        return array(
            array(
                'label' => 'Workspace',
                'items' => array(
                    'dashboard' => array(
                        'title' => 'Dashboard',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>'
                    ),
                    'blogs' => array(
                        'title' => 'Content Suite',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
                    ),
                    'leads' => array(
                        'title' => 'Buyer Leads (CRM)',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
                    ),
                    'bookings' => array(
                        'title' => 'Site Showings',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'
                    ),
                    'financials' => array(
                        'title' => 'Financial Overview',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
                    ),
                    'team-roles' => array(
                        'title' => 'User & Roles',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
                    )
                )
            ),
            array(
                'label' => 'Property Portfolio',
                'items' => array(
                    'equipment' => array(
                        'title' => 'Property Listings',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>'
                    ),
                    'vault' => array(
                        'title' => 'Secure Vault',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
                    )
                )
            ),
            array(
                'label' => 'Sales Channel',
                'items' => array(
                    'canvas' => array(
                        'title' => 'Canvas',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>'
                    )
                )
            ),
            array(
                'label' => 'AI Marketing',
                'items' => array(
                    'gbp' => array(
                        'title' => 'Google Profile',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>'
                    ),
                    'mcp' => array(
                        'title' => 'AI Tools MCP',
                        'soon'  => true,
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="15" x2="23" y2="15"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="15" x2="4" y2="15"></line></svg>'
                    )
                )
            ),
            array(
                'label' => 'Settings',
                'items' => array(
                    'feature-hub' => array(
                        'title' => 'App Modules',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
                    ),
                    'settings-suite' => array(
                        'title' => 'Settings',
                        'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>'
                    )
                )
            )
        );
    }

    public function get_industry_roles() : array {
        return array(
            'administrator' => 'Super Admin',
            'cora_manager' => 'Broker Owner',
            'cora_photographer' => 'Managing Agent',
            'cora_videographer' => 'Showing Assistant',
            'cora_drone_pilot' => 'Property Valuer',
            'cora_editor' => 'Listing Coordinator'
        );
    }

    public function get_crm_stages() : array {
        return array(
            'New Lead' => array( 'label' => 'New Leads', 'badge' => 'bg-blue-100 border border-blue-200/60', 'desc' => 'New inquiries to review' ),
            'Nurturing' => array( 'label' => 'Nurturing', 'badge' => 'bg-amber-100 border border-amber-200/60', 'desc' => 'Active communication' ),
            'Showing Scheduled' => array( 'label' => 'Showing Scheduled', 'badge' => 'bg-orange-100 border border-orange-200/60', 'desc' => 'Property visits scheduled' ),
            'Converted' => array( 'label' => 'Converted', 'badge' => 'bg-emerald-100 border border-emerald-200/60', 'desc' => 'Successfully closed' )
        );
    }

    public function setup_database_tables() {
        // Handled centrally in core
    }
}
