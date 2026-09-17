<?php
/**
 * Cora Stationery Manufacturing & Field Sales Inventory Module
 * 
 * Provides single point of control for stationery manufacturing plants,
 * dynamic mobile van sales consignment allocations (₹1k to ₹10L+), live GPS route tracking,
 * multimodal AI invoice OCR parsing, and automated 24-hour daily audit reports.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cora_Manufacturing_Inventory_Module implements Cora_Module_Interface {
    
    public function get_module_id() : string {
        return 'stationery_inventory';
    }

    public function get_display_name() : string {
        return 'Stationery Manufacturing & Van Sales';
    }

    public function get_navigation_groups(string $active_role) : array {
        if ( $active_role === 'cora_field_vendor' ) {
            return array(
                array(
                    'label' => 'Field Operations',
                    'items' => array(
                        'plant_inventory' => array(
                            'title' => 'Van Sales & Route',
                            'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>'
                        )
                    )
                )
            );
        }

        $enabled = function_exists( 'cora_get_custom_enabled_features' ) ? cora_get_custom_enabled_features() : array();
        
        $is_enabled = function( $slug ) use ( $enabled ) {
            return in_array( $slug, $enabled, true );
        };

        $groups = array();

        // 1. Core Workspace Group
        $workspace_items = array(
            'dashboard' => array(
                'title' => 'Dashboard',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect></svg>'
            )
        );

        if ( $is_enabled( 'plant_inventory' ) || $is_enabled( 'stationery_inventory' ) || $is_enabled( 'inventory_management' ) ) {
            $workspace_items['plant_inventory'] = array(
                'title' => 'Inventory',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
            );
        }

        if ( $is_enabled( 'financials' ) ) {
            $workspace_items['financials'] = array(
                'title' => 'Financial Overview',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'
            );
        }

        if ( $is_enabled( 'team-roles' ) ) {
            $workspace_items['team-roles'] = array(
                'title' => 'User & Roles',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'
            );
        }

        if ( $is_enabled( 'vault' ) ) {
            $workspace_items['vault'] = array(
                'title' => 'Document Vault & GST',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>'
            );
        }

        $groups[] = array(
            'label' => 'Plant Workspace',
            'items' => $workspace_items
        );

        // 2. CRM Group (Independent)
        $crm_items = array();
        if ( $is_enabled( 'leads' ) ) {
            $crm_items['leads'] = array(
                'title' => 'Dealer Leads (CRM)',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>'
            );
        }
        if ( ! empty( $crm_items ) ) {
            $groups[] = array(
                'label' => 'CRM',
                'items' => $crm_items
            );
        }

        // 3. Operations Group
        $ops_items = array();
        if ( $is_enabled( 'forms' ) ) {
            $ops_items['forms'] = array(
                'title' => 'Forms & Reviews 2.0',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'
            );
        }
        if ( $is_enabled( 'feature_hub' ) ) {
            $ops_items['feature_hub'] = array(
                'title' => 'App Modules',
                'icon'  => '<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>'
            );
        }

        if ( ! empty( $ops_items ) ) {
            $groups[] = array(
                'label' => 'Operations',
                'items' => $ops_items
            );
        }

        return $groups;
    }

    public function get_industry_roles() : array {
        return array(
            'cora_plant_manager' => 'Plant / Operations Director',
            'cora_field_vendor'  => 'Field Sales / Mobile Vendor'
        );
    }

    public function get_crm_stages() : array {
        return array(
            'inquiry'           => 'Dealer Inquiry',
            'sample_kit'        => 'Sample Kit Dispatched',
            'bulk_quotation'    => 'Wholesale Quotation',
            'order_contract'    => 'Contract & Advance',
            'active_dealer'     => 'Active Recurring Dealer'
        );
    }

    public function setup_database_tables() {
        if ( function_exists( 'cora_create_custom_tables' ) ) {
            cora_create_custom_tables();
        }
    }
}
