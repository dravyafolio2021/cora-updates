<?php
/**
 * Cora Production API - Resilient Agency Partner Application Engine
 * Architecture: Database Capture First -> Non-Blocking Beehiiv Sync -> Non-Blocking Notification
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// 1. Try loading WordPress environment if available
$wpLoadPaths = [
    '/home/u484406462/domains/heycora.in/public_html/wp-load.php',
    dirname(__DIR__, 2) . '/wp-load.php',
    dirname(__DIR__, 3) . '/wp-load.php',
];
$hasWp = false;
foreach ($wpLoadPaths as $wlp) {
    if (file_exists($wlp)) {
        require_once $wlp;
        $hasWp = true;
        break;
    }
}

// 2. Load Server Environment Variables from protected paths
$possibleEnvPaths = [
    '/home/u484406462/domains/heycora.in/.env.production',
    '/home/u484406462/.env.production',
    dirname(__DIR__, 2) . '/.env.production',
    dirname(__DIR__, 2) . '/.env.local',
    dirname(__DIR__, 3) . '/.env.production',
];

$env = [];
foreach ($possibleEnvPaths as $path) {
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $env[trim($key)] = trim(trim($val), "\"'");
            }
        }
        break;
    }
}

// 3. Parse & Validate Payload
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];

$honeypot = trim($data['companyWebsite'] ?? '');
if ($honeypot) {
    echo json_encode(['success' => true, 'applicationCaptured' => true]);
    exit;
}

$name = htmlspecialchars(trim(substr($data['name'] ?? '', 0, 100)), ENT_QUOTES, 'UTF-8');
$email = strtolower(trim(substr($data['email'] ?? '', 0, 150)));
$phone = htmlspecialchars(trim(substr($data['phone'] ?? '', 0, 30)), ENT_QUOTES, 'UTF-8');
$companyName = htmlspecialchars(trim(substr($data['companyName'] ?? '', 0, 150)), ENT_QUOTES, 'UTF-8');
$agencyType = htmlspecialchars(trim(substr($data['agencyType'] ?? 'Performance Marketing Agency', 0, 100)), ENT_QUOTES, 'UTF-8');
$clientCount = htmlspecialchars(trim(substr($data['clientCount'] ?? '1–5 active clients', 0, 80)), ENT_QUOTES, 'UTF-8');
$website = htmlspecialchars(trim(substr($data['website'] ?? '', 0, 300)), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim(substr($data['message'] ?? '', 0, 2000)), ENT_QUOTES, 'UTF-8');
$consentNewsletter = !empty($data['consentNewsletter']);

$source = htmlspecialchars(trim(substr($data['utm_source'] ?? ($data['source'] ?? 'partner_page'), 0, 100)), ENT_QUOTES, 'UTF-8');
$utmMedium = htmlspecialchars(trim(substr($data['utm_medium'] ?? '', 0, 100)), ENT_QUOTES, 'UTF-8');
$utmCampaign = htmlspecialchars(trim(substr($data['utm_campaign'] ?? 'agency_partner_program', 0, 100)), ENT_QUOTES, 'UTF-8');
$utmTerm = htmlspecialchars(trim(substr($data['utm_term'] ?? '', 0, 100)), ENT_QUOTES, 'UTF-8');
$utmContent = htmlspecialchars(trim(substr($data['utm_content'] ?? '', 0, 200)), ENT_QUOTES, 'UTF-8');
$referrer = htmlspecialchars(trim(substr($data['referrer'] ?? '', 0, 300)), ENT_QUOTES, 'UTF-8');
$landingPage = htmlspecialchars(trim(substr($data['landing_page'] ?? '/partners/agencies/', 0, 200)), ENT_QUOTES, 'UTF-8');

// Validation rules
if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$phone || !$companyName) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Please check the highlighted fields and complete all required inputs.'
    ]);
    exit;
}

$phoneDigits = preg_replace('/[^0-9]/', '', $phone);
if (strlen($phoneDigits) < 7) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Please provide a valid phone or WhatsApp number.'
    ]);
    exit;
}

// 4. Calculate Partner Score (1 to 7)
$partnerScore = 1;
if (strpos($clientCount, '6–10') !== false) $partnerScore = 2;
elseif (strpos($clientCount, '11–25') !== false) $partnerScore = 3;
elseif (strpos($clientCount, '26–50') !== false) $partnerScore = 4;
elseif (strpos($clientCount, '50+') !== false) $partnerScore = 5;

if (stripos($agencyType, 'Performance') !== false || stripos($agencyType, 'Shopify') !== false || stripos($agencyType, 'Automation') !== false) {
    $partnerScore += 2;
} elseif (stripos($agencyType, 'SEO') !== false || stripos($agencyType, 'Branding') !== false) {
    $partnerScore += 1;
}

// 5. Primary Storage: Database
$applicationId = 'app_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
$dbSaved = false;

if ($hasWp && isset($GLOBALS['wpdb'])) {
    /** @var wpdb $wpdb */
    $wpdb = $GLOBALS['wpdb'];
    $table_name = $wpdb->prefix . 'cora_agency_partner_applications';

    $tableSql = "CREATE TABLE IF NOT EXISTS {$table_name} (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        app_ref VARCHAR(64) UNIQUE,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(191) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        company_name VARCHAR(191) NOT NULL,
        agency_type VARCHAR(100) NOT NULL,
        client_count VARCHAR(50) NOT NULL,
        website VARCHAR(300) NULL,
        message TEXT NULL,
        partner_score INT DEFAULT 1,
        consent_newsletter TINYINT(1) DEFAULT 1,
        status VARCHAR(50) DEFAULT 'new',
        source VARCHAR(100) DEFAULT 'partner_page',
        utm_source VARCHAR(100) NULL,
        utm_medium VARCHAR(100) NULL,
        utm_campaign VARCHAR(100) NULL,
        utm_content VARCHAR(200) NULL,
        utm_term VARCHAR(100) NULL,
        referrer VARCHAR(300) NULL,
        landing_page VARCHAR(200) NULL,
        beehiiv_subscription_id VARCHAR(100) NULL,
        beehiiv_synced TINYINT(1) DEFAULT 0,
        notification_sent TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $wpdb->query($tableSql);

    // Check duplicate
    $existing = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$table_name} WHERE email = %s AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) LIMIT 1", $email));

    if ($existing) {
        $wpdb->update(
            $table_name,
            [
                'name' => $name,
                'phone' => $phone,
                'company_name' => $companyName,
                'agency_type' => $agencyType,
                'client_count' => $clientCount,
                'website' => $website,
                'message' => $message,
                'partner_score' => $partnerScore,
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $existing->id]
        );
        $applicationId = $existing->id;
    } else {
        $wpdb->insert(
            $table_name,
            [
                'app_ref' => $applicationId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company_name' => $companyName,
                'agency_type' => $agencyType,
                'client_count' => $clientCount,
                'website' => $website,
                'message' => $message,
                'partner_score' => $partnerScore,
                'consent_newsletter' => $consentNewsletter ? 1 : 0,
                'status' => 'new',
                'source' => $source,
                'utm_source' => $source,
                'utm_medium' => $utmMedium,
                'utm_campaign' => $utmCampaign,
                'utm_content' => $utmContent,
                'utm_term' => $utmTerm,
                'referrer' => $referrer,
                'landing_page' => $landingPage,
                'created_at' => current_time('mysql'),
            ]
        );
        if ($wpdb->insert_id) {
            $applicationId = (int)$wpdb->insert_id;
        }
    }
    $dbSaved = true;
}

// Durable JSONL Log Backup
$logDirs = [
    '/home/u484406462/domains/heycora.in',
    dirname(__DIR__, 3),
    __DIR__
];
foreach ($logDirs as $ld) {
    if (is_dir($ld) && is_writable($ld)) {
        $logFile = $ld . '/partner_applications_archive.jsonl';
        $logEntry = json_encode([
            'id' => $applicationId,
            'timestamp' => date('c'),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company_name' => $companyName,
            'agency_type' => $agencyType,
            'client_count' => $clientCount,
            'website' => $website,
            'message' => $message,
            'partner_score' => $partnerScore,
            'source' => $source,
            'utms' => ['source' => $source, 'medium' => $utmMedium, 'campaign' => $utmCampaign, 'term' => $utmTerm, 'content' => $utmContent],
            'referrer' => $referrer,
        ]) . "\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        break;
    }
}

// 6. Non-Blocking Beehiiv Sync
$apiKey = getenv('BEEHIIV_API_KEY') ?: ($env['BEEHIIV_API_KEY'] ?? '');
$pubId = getenv('BEEHIIV_PUBLICATION_ID') ?: ($env['BEEHIIV_PUBLICATION_ID'] ?? 'pub_838eba99-e4d5-413b-b744-50d94de64c60');

$beehiivSynced = false;
$beehiivSubId = null;

if ($apiKey && $pubId) {
    $beehiivPayload = [
        'email' => $email,
        'reactivate_existing' => true,
        'send_welcome_email' => false,
        'double_opt_override' => 'not_set',
        'utm_source' => $source ?: 'agency_partner_application',
        'utm_medium' => $utmMedium ?: 'website',
        'utm_campaign' => $utmCampaign ?: 'agency_partner_program',
        'utm_term' => $agencyType,
        'utm_content' => substr("{$companyName} | {$clientCount}", 0, 200),
        'referring_site' => $website ?: ($referrer ?: 'https://heycora.in/partners/agencies/'),
        'custom_fields' => [
            ['name' => 'First Name', 'value' => $name],
            ['name' => 'Phone', 'value' => $phone],
            ['name' => 'Agency Name', 'value' => $companyName],
            ['name' => 'Agency Type', 'value' => $agencyType],
            ['name' => 'Active Clients', 'value' => $clientCount],
            ['name' => 'Agency Website', 'value' => $website],
            ['name' => 'Partner Note', 'value' => $message],
        ],
    ];

    $ch = curl_init("https://api.beehiiv.com/v2/publications/" . urlencode($pubId) . "/subscriptions");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$apiKey}",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($beehiivPayload),
        CURLOPT_TIMEOUT => 7,
    ]);

    $bResponse = curl_exec($ch);
    $bCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($bCode >= 200 && $bCode < 300) {
        $bData = json_decode($bResponse, true);
        $beehiivSubId = $bData['data']['id'] ?? null;
        $beehiivSynced = true;
    }
}

// 7. Non-Blocking Internal Email Notification
$notificationSent = false;
$targetEmail = getenv('NOTIFICATION_FORWARD_EMAIL') ?: ($env['NOTIFICATION_FORWARD_EMAIL'] ?? 'dravya.bansal@heycora.in');

$waClean = $phoneDigits;
$waUrl = '';
if (strlen($waClean) >= 10) {
    if (substr($waClean, 0, 2) === '91' || strlen($waClean) > 10) {
        $waUrl = "https://wa.me/{$waClean}";
    } else {
        $waUrl = "https://wa.me/91{$waClean}";
    }
}

$subject = "⚡ New Cora Agency Partner Application — {$companyName}";

$htmlEmail = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #09090b; margin: 0; padding: 24px; color: #ffffff; }
    .card { max-width: 600px; margin: 0 auto; background: #18181b; border: 1px solid #27272a; border-radius: 20px; overflow: hidden; }
    .header { padding: 24px 28px; background: #000000; border-bottom: 1px solid #27272a; }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; font-size: 11px; font-weight: 700; font-family: monospace; letter-spacing: 0.1em; }
    .title { font-size: 20px; font-weight: 700; color: #ffffff; margin: 12px 0 0 0; }
    .body { padding: 28px; }
    .table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .table td { padding: 10px 0; border-bottom: 1px solid #27272a; font-size: 13px; }
    .table td.label { color: #a1a1aa; width: 35%; }
    .table td.val { color: #ffffff; font-weight: 600; width: 65%; }
    .note { background: #09090b; border: 1px solid #27272a; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 13px; line-height: 1.6; color: #e4e4e7; }
    .btn-group { margin-top: 20px; }
    .btn { display: inline-block; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; margin-right: 8px; margin-bottom: 8px; }
    .btn-primary { background: #ffffff; color: #09090b !important; }
    .btn-wa { background: #16a34a; color: #ffffff !important; }
    .footer { padding: 18px 28px; background: #09090b; border-top: 1px solid #27272a; font-size: 11px; color: #71717a; font-family: monospace; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <span class="badge">PARTNER APPLICATION // PRIORITY SCORE {$partnerScore}/7</span>
      <h1 class="title">{$companyName}</h1>
    </div>
    <div class="body">
      <table class="table">
        <tr><td class="label">Applicant Name</td><td class="val">{$name}</td></tr>
        <tr><td class="label">Work Email</td><td class="val"><a href="mailto:{$email}" style="color: #60a5fa;">{$email}</a></td></tr>
        <tr><td class="label">Phone / WhatsApp</td><td class="val">{$phone}</td></tr>
        <tr><td class="label">Agency Type</td><td class="val">{$agencyType}</td></tr>
        <tr><td class="label">Active Clients</td><td class="val">{$clientCount}</td></tr>
        <tr><td class="label">Website / Portfolio</td><td class="val">{$website}</td></tr>
        <tr><td class="label">Attribution / Source</td><td class="val">{$source} ({$utmCampaign})</td></tr>
      </table>

      <div style="font-size: 11px; font-family: monospace; text-transform: uppercase; color: #a1a1aa; margin-bottom: 8px;">Partner Note / Client Stack</div>
      <div class="note">{$message}</div>

      <div class="btn-group">
        <a href="mailto:{$email}?subject=Cora%20Agency%20Partner%20Program%20%E2%80%94%20{$companyName}" class="btn btn-primary">Reply via Email &rarr;</a>
        <a href="{$waUrl}" class="btn btn-wa">Open WhatsApp &rarr;</a>
        <a href="{$website}" class="btn" style="background: #27272a; color: #ffffff !important;">Visit Website &rarr;</a>
      </div>
    </div>
    <div class="footer">
      Application Ref: {$applicationId} &bull; Stored: DB &bull; Beehiiv: {$beehiivSynced}
    </div>
  </div>
</body>
</html>
HTML;

$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=utf-8',
    'From: Cora Partner Gateway <notifications@heycora.in>',
    "Reply-To: {$name} <{$email}>",
    'X-Mailer: PHP/' . phpversion()
];

if (function_exists('wp_mail')) {
    $notificationSent = wp_mail($targetEmail, $subject, $htmlEmail, $headers);
} else {
    $notificationSent = @mail($targetEmail, $subject, $htmlEmail, implode("\r\n", $headers));
}

// 8. Update DB with sync statuses
if ($dbSaved && $hasWp && isset($GLOBALS['wpdb']) && is_numeric($applicationId)) {
    /** @var wpdb $wpdb */
    $wpdb = $GLOBALS['wpdb'];
    $table_name = $wpdb->prefix . 'cora_agency_partner_applications';
    $wpdb->update(
        $table_name,
        [
            'beehiiv_subscription_id' => $beehiivSubId,
            'beehiiv_synced' => $beehiivSynced ? 1 : 0,
            'notification_sent' => $notificationSent ? 1 : 0,
        ],
        ['id' => $applicationId]
    );
}

// 9. Return Success
echo json_encode([
    'success' => true,
    'applicationCaptured' => true,
    'applicationId' => $applicationId,
    'beehiivSynced' => $beehiivSynced,
    'notificationSent' => $notificationSent,
    'partnerScore' => $partnerScore,
]);
