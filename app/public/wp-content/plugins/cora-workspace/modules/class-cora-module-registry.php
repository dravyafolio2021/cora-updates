<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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
        if ( $module_id === 'photography' ) {
            $module_id = 'photography_studio';
        }
        return self::$modules[$module_id] ?? null;
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

        // Register core industry modules
        self::register_module(new Cora_Real_Estate_Module());
        self::register_module(new Cora_Photography_Studio_Module());
    }
}
