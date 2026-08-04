<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define( 'WP_ADMIN', true );
ini_set( 'mysqli.default_socket', '/Users/shrutian/Library/Application Support/Local/run/efD3wPMAY/mysql/mysqld.sock' );

try {
    require_once dirname( __FILE__ ) . '/../app/public/wp-load.php';
    echo "\n=== Cora Documentation System Verification ===\n";

    global $wpdb;
    $t_pages = $wpdb->prefix . 'cora_docs_pages';
    $t_versions = $wpdb->prefix . 'cora_docs_versions';
    $t_changelog = $wpdb->prefix . 'cora_docs_changelog';
    $t_api = $wpdb->prefix . 'cora_docs_api_endpoints';

    // 1. Check Tables Existence
    $tables = array( $t_pages, $t_versions, $t_changelog, $t_api );
    foreach ( $tables as $table ) {
        $check = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );
        if ( $check === $table ) {
            echo "PASS: Table '{$table}' exists in database.\n";
        } else {
            echo "FAIL: Table '{$table}' NOT found!\n";
            exit(1);
        }
    }

    // 2. Check Seeded Data
    $page_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_pages}" );
    if ( $page_count >= 4 ) {
        echo "PASS: Seeded documentation pages verified. Count: {$page_count}\n";
    } else {
        echo "FAIL: Core documentation pages missing or incomplete. Count: {$page_count}\n";
        exit(1);
    }

    $api_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_api}" );
    if ( $api_count >= 2 ) {
        echo "PASS: Seeded API endpoints verified. Count: {$api_count}\n";
    } else {
        echo "FAIL: Seeded API endpoints missing. Count: {$api_count}\n";
        exit(1);
    }

    // 3. Test Auto-Update Trigger Hooks
    echo "\nSimulating Auto-Update Event Triggers...\n";
    
    // Check initial count of changelogs
    $init_changelogs = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_changelog}" );
    
    // Simulate feature completion
    do_action( 'cora_module_feature_completed', 'user-management', 'Two-Factor Authenticator Gateway', 'v1.1.0' );
    
    // Verify changelog entry created
    $post_changelogs = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$t_changelog}" );
    if ( $post_changelogs === $init_changelogs + 1 ) {
        echo "PASS: Hook 'cora_module_feature_completed' successfully created a new changelog entry.\n";
    } else {
        echo "FAIL: Hook 'cora_module_feature_completed' failed to create a changelog entry.\n";
        exit(1);
    }

    // Simulate new module registration
    $module_check_slug = 'billing-engine';
    do_action( 'cora_module_registered', $module_check_slug, 'Billing and Subscriptions', 'v1.0.0' );
    
    $engine_page = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$t_pages} WHERE module_key = %s", $module_check_slug ) );
    if ( $engine_page ) {
        echo "PASS: Hook 'cora_module_registered' successfully auto-scaffolded a documentation page for '{$module_check_slug}'.\n";
    } else {
        echo "FAIL: Hook 'cora_module_registered' did not auto-scaffold documentation page.\n";
        exit(1);
    }

    echo "\n=== ALL VERIFICATION CHECKS PASSED SUCCESSFULLY ===\n\n";

} catch (Throwable $e) {
    echo "Verification Exception caught: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    exit(1);
}
