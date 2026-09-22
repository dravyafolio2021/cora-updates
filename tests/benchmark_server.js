const { execSync } = require('child_process');
const path = require('path');

const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';

const phpScript = `
define('SAVEQUERIES', true);
require_once 'app/public/wp-load.php';

$user = get_user_by('login', 'studio_owner');
wp_set_current_user($user->ID);

$start = microtime(true);
global $wpdb;
$initial_queries = count($wpdb->queries ?? []);

// Capture output of rendering dashboard
ob_start();
$_GET['industry'] = 'photography_studio';
// Simulate template redirect to dashboard
include 'app/public/wp-content/plugins/cora-workspace/admin-dashboard.php';
$html = ob_get_clean();

$end = microtime(true);
$duration = round(($end - $start) * 1000, 2);
$mem = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
$final_queries = count($wpdb->queries ?? []);
$total_queries = $final_queries - $initial_queries;

// Find slowest queries
$queries = $wpdb->queries ?? [];
usort($queries, function($a, $b) {
    return $b[1] <=> $a[1];
});
$slowest = array_slice($queries, 0, 5);

echo json_encode([
    'phpDurationMs' => $duration,
    'peakMemoryMb' => $mem,
    'htmlSizeBytes' => strlen($html),
    'queryCount' => $total_queries,
    'slowestQueries' => array_map(function($q) {
        return ['timeMs' => round($q[1] * 1000, 2), 'sql' => substr($q[0], 0, 100)];
    }, $slowest)
], JSON_PRETTY_PRINT);
`;

const res = execSync(`${phpBin} -r "${phpScript.replace(/"/g, '\\"').replace(/\$/g, '\\$')}"`, { cwd: path.join(__dirname, '..') }).toString();
console.log(res);
