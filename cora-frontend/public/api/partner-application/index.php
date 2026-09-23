<?php
/**
 * Cora Production API - Agency Partner Application Bridge (Beehiiv + Email)
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

// Load env variables
$envFile = dirname(__DIR__, 2) . '/.env.production';
if (!file_exists($envFile)) {
    $envFile = dirname(__DIR__, 2) . '/.env.local';
}
if (!file_exists($envFile)) {
    $envFile = dirname(__DIR__, 3) . '/.env.production';
}

$env = [];
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && $line[0] !== '#' && strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $env[trim($key)] = trim(trim($val), "\"'");
        }
    }
}

$apiKey = getenv('BEEHIIV_API_KEY') ?: ($env['BEEHIIV_API_KEY'] ?? '');
$pubId = getenv('BEEHIIV_PUBLICATION_ID') ?: ($env['BEEHIIV_PUBLICATION_ID'] ?? '');

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];

$name = trim(substr($data['name'] ?? '', 0, 100));
$email = strtolower(trim(substr($data['email'] ?? '', 0, 140)));
$phone = trim(substr($data['phone'] ?? '', 0, 40));
$companyName = trim(substr($data['companyName'] ?? '', 0, 120));
$agencyType = trim(substr($data['agencyType'] ?? '', 0, 100));
$clientCount = trim(substr($data['clientCount'] ?? '', 0, 80));
$website = trim(substr($data['website'] ?? '', 0, 220));
$message = trim(substr($data['message'] ?? '', 0, 1200));

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$phone || !$companyName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please complete your name, work email, phone and agency name.']);
    exit;
}

if (!$apiKey || !$pubId) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Partner applications are being configured. Please try again shortly.']);
    exit;
}

$beehiivPayload = [
    'email' => $email,
    'reactivate_existing' => true,
    'send_welcome_email' => false,
    'double_opt_override' => 'not_set',
    'utm_source' => 'partner_application',
    'utm_medium' => 'website',
    'utm_campaign' => 'cora_agency_partner_program',
    'utm_term' => $agencyType,
    'utm_content' => substr("{$companyName} | {$clientCount}", 0, 200),
    'referring_site' => $website ?: 'https://heycora.in/partners/agencies/',
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
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        'success' => true,
        'message' => 'Partner application submitted successfully.'
    ]);
} else {
    http_response_code($httpCode ?: 502);
    echo json_encode([
        'success' => false,
        'error' => 'Could not submit the application right now. Please try again.'
    ]);
}
