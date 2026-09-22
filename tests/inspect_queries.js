const { execSync } = require('child_process');
const path = require('path');

const phpBin = '/Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php';

const phpScript = `
define('SAVEQUERIES', true);
require_once 'app/public/wp-load.php';

$user = get_user_by('login', 'studio_owner');
wp_set_current_user($user->ID);

global $wpdb;
$wpdb->queries = [];

ob_start();
$_GET['industry'] = 'photography_studio';
include 'app/public/wp-content/plugins/cora-workspace/admin-dashboard.php';
$html = ob_get_clean();

$groups = [];
foreach ($wpdb->queries as $q) {
    $sql = trim(preg_replace('/\\s+/', ' ', $q[0]));
    // Generalize numbers/IDs for grouping
    $pattern = preg_replace('/\\b\\d+\\b/', '?', $sql);
    $pattern = preg_replace('/\\b[a-f0-9]{32}\\b/', '?', $pattern);
    if (!isset($groups[$pattern])) {
        $groups[$pattern] = [
            'count' => 0,
            'totalTime' => 0,
            'example' => substr($sql, 0, 120),
            'caller' => substr($q[2] ?? '', 0, 100)
        ];
    }
    $groups[$pattern]['count']++;
    $groups[$pattern]['totalTime'] += $q[1];
}

uasort($groups, function($a, $b) {
    return $b['count'] <=> $a['count'];
});

$top = array_slice($groups, 0, 20);
echo json_encode([
    'totalQueries' => count($wpdb->queries),
    'topQueryPatterns' => $top
], JSON_PRETTY_PRINT);
`;

const res = execSync(`${phpBin} -r "${phpScript.replace(/"/g, '\\"').replace(/\$/g, '\\$')}"`, { cwd: path.join(__dirname, '..') }).toString();
console.log(res);
