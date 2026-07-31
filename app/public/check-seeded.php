<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');

header('Content-Type: text/plain');

$seeded = get_option('cora_seeded_3_leads_per_column_v2');
echo "cora_seeded_3_leads_per_column_v2: " . ($seeded ? 'Yes' : 'No') . "\n";
echo "Value: " . var_export($seeded, true) . "\n";
