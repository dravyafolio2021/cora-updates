<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface Cora_Module_Interface {
    /**
     * Get the unique string identifier for this module.
     */
    public function get_module_id() : string;

    /**
     * Get the user-friendly display name for this module.
     */
    public function get_display_name() : string;

    /**
     * Get the navigation sidebar groups defined by this module.
     */
    public function get_navigation_groups(string $active_role) : array;

    /**
     * Get specific staff/user roles registered by this module.
     */
    public function get_industry_roles() : array;

    /**
     * Get CRM stages specific to this industry module.
     */
    public function get_crm_stages() : array;

    /**
     * Trigger module-specific database setup / schema initialization.
     */
    public function setup_database_tables();
}
