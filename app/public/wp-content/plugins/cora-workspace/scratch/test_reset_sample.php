<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );
require_once dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/wp-load.php';

// Set current user to user 9 (cora_admin)
wp_set_current_user( 9 );

// 1. Check current status of lead_sample_1
$leads = cora_db_get_leads();
$sample_1 = null;
foreach ( $leads as $l ) {
    if ( (string)$l['id'] === 'lead_sample_1' ) {
        $sample_1 = $l;
        break;
    }
}
echo "Status before update: " . ($sample_1 ? $sample_1['status'] : 'Not found') . "\n";

// 2. Update status of lead_sample_1 to 'Contacted' in the option
$existing_leads = get_option( 'cora_workspace_leads', array() );
foreach ( $existing_leads as &$el ) {
    if ( (string)$el['id'] === 'lead_sample_1' ) {
        $el['status'] = 'Contacted';
        break;
    }
}
update_option( 'cora_workspace_leads', $existing_leads );
echo "Status updated in option.\n";

// 3. Verify it is updated
$leads_after = cora_db_get_leads();
$sample_1_after = null;
foreach ( $leads_after as $l ) {
    if ( (string)$l['id'] === 'lead_sample_1' ) {
        $sample_1_after = $l;
        break;
    }
}
echo "Status immediately after update: " . ($sample_1_after ? $sample_1_after['status'] : 'Not found') . "\n";

// 4. Simulate a new page load by calling the init action hook again!
// During a new page load, cora_ensure_default_agency_setup runs.
// Let's run it directly:
cora_ensure_default_agency_setup();

// 5. Check status after page load simulation
$leads_final = cora_db_get_leads();
$sample_1_final = null;
foreach ( $leads_final as $l ) {
    if ( (string)$l['id'] === 'lead_sample_1' ) {
        $sample_1_final = $l;
        break;
    }
}
echo "Status after page load simulation: " . ($sample_1_final ? $sample_1_final['status'] : 'Not found') . "\n";
