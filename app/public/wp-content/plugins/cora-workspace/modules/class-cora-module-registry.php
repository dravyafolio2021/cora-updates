<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Cora_Module_Registry' ) ) {
class Cora_Module_Registry {
    private static $modules = array();

    /**
     * Register a module instance.
     */
    public static function register_module(Cora_Module_Interface $module) {
        self::$modules[$module->get_module_id()] = $module;
        if ( did_action( 'init' ) || doing_action( 'init' ) ) {
            do_action( 'cora_module_registered', $module->get_module_id(), $module->get_display_name(), '1.0.0' );
        } else {
            // If initialized early, queue it on init hook
            add_action( 'init', function() use ($module) {
                do_action( 'cora_module_registered', $module->get_module_id(), $module->get_display_name(), '1.0.0' );
            }, 20 );
        }
    }

    /**
     * Get a module instance by ID.
     */
    public static function get_module(string $module_id) : ?Cora_Module_Interface {
        $clean_id = strtolower( trim( $module_id ) );
        if ( in_array( $clean_id, array( 'photography', 'photography_studio', 'studio', 'photo' ), true ) ) {
            $clean_id = 'photography_studio';
        } elseif ( in_array( $clean_id, array( 'real_estate', 'real-estate', 're', 'realestate' ), true ) ) {
            $clean_id = 'real_estate';
        } elseif ( in_array( $clean_id, array( 'marketing', 'marketing_agency', 'digital_agency', 'marketing_seo', 'agency' ), true ) ) {
            $clean_id = 'marketing_agency';
        } elseif ( in_array( $clean_id, array( 'professional_services', 'professional_services_agency', 'consulting', 'legal_advisory', 'advisory', 'accounting', 'tax_ca_firms', 'it_tech_services' ), true ) ) {
            $clean_id = 'professional_services';
        } elseif ( in_array( $clean_id, array( 'manufacturing', 'manufacturing_plant', 'stationery', 'stationery_inventory', 'plant_inventory', 'manufacturing_inventory', 'plant' ), true ) ) {
            $clean_id = 'stationery_inventory';
        } elseif ( in_array( $clean_id, array( 'custom', 'custom_workspace' ), true ) ) {
            $clean_id = 'custom';
        }
        
        if ( isset( self::$modules[$clean_id] ) ) {
            return self::$modules[$clean_id];
        }
        return self::$modules['photography_studio'] ?? self::$modules['real_estate'] ?? ( ! empty( self::$modules ) ? reset( self::$modules ) : null );
    }

    /**
     * Get all registered modules.
     */
    public static function get_all_modules() : array {
        return self::$modules;
    }

    /**
     * Load core modules and register them.
     */
    public static function initialize() {
        // Include default modules
        require_once CORA_WORKSPACE_PATH . 'modules/real-estate/class-re-module.php';
        require_once CORA_WORKSPACE_PATH . 'modules/photography-studio/class-studio-module.php';
        require_once CORA_WORKSPACE_PATH . 'modules/marketing-agency/class-marketing-agency-module.php';
        require_once CORA_WORKSPACE_PATH . 'modules/professional-services/class-professional-services-module.php';
        require_once CORA_WORKSPACE_PATH . 'modules/custom-workspace/class-custom-module.php';
        require_once CORA_WORKSPACE_PATH . 'modules/manufacturing-inventory/class-manufacturing-inventory-module.php';

        // Register core industry modules
        self::register_module(new Cora_Real_Estate_Module());
        self::register_module(new Cora_Photography_Studio_Module());
        self::register_module(new Cora_Marketing_Agency_Module());
        self::register_module(new Cora_Professional_Services_Module());
        self::register_module(new Cora_Custom_Workspace_Module());
        self::register_module(new Cora_Manufacturing_Inventory_Module());
    }
}
}
