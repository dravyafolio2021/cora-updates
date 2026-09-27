<?php
/**
 * Cora Growth Workspace - Core Loader
 *
 * Bootstraps DB migrations, registries, validators, preview services, and REST routes.
 *
 * @package CoraWorkspace
 * @subpackage Growth
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/class-cora-growth-db.php';
require_once __DIR__ . '/class-cora-content-type-registry.php';
require_once __DIR__ . '/class-cora-block-registry.php';
require_once __DIR__ . '/class-cora-content-validator.php';
require_once __DIR__ . '/class-cora-growth-preview-service.php';
require_once __DIR__ . '/class-cora-growth-analytics.php';
require_once __DIR__ . '/class-cora-growth-api.php';

class Cora_Growth_Loader {

    public static function init() {
        // Initialize DB
        Cora_Growth_DB::init();

        // Initialize Registries
        Cora_Content_Type_Registry::init();
        Cora_Block_Registry::init();

        // Register REST API routes
        add_action( 'rest_api_init', array( 'Cora_Growth_API', 'register_routes' ) );
    }
}

// Auto-initialize when loaded
Cora_Growth_Loader::init();
