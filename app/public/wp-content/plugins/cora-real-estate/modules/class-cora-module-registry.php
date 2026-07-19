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
        require_once CORA_REAL_ESTATE_AI_PATH . 'modules/real-estate/class-re-module.php';
        require_once CORA_REAL_ESTATE_AI_PATH . 'modules/photography-studio/class-studio-module.php';

        // Register core industry modules
        self::register_module(new Cora_Real_Estate_Module());
        self::register_module(new Cora_Photography_Studio_Module());
    }
}
