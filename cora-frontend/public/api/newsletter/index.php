<?php
/**
 * Cora Production API - Newsletter Subscription Bridge (Beehiiv)
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

// Load env variables from .env.production or .env.local if not present in getenv
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

$email = strtolower(trim($data['email'] ?? ''));
$source = trim(substr($data['source'] ?? 'website', 0, 80));
$path = trim(substr($data['path'] ?? '', 0, 200));
$referrer = trim(substr($data['referrer'] ?? '', 0, 300));
$honeypot = trim($data['companyWebsite'] ?? '');

if ($honeypot) {
    echo json_encode(['success' => true]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Enter a valid email address.']);
    exit;
}

if (!$apiKey || !$pubId) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Newsletter service is currently being configured.']);
    exit;
}

$payload = [
    'email' => $email,
    'reactivate_existing' => true,
    'send_welcome_email' => true,
    'double_opt_override' => 'not_set',
    'utm_source' => $source ?: 'website',
    'utm_medium' => 'website',
    'utm_campaign' => 'cora_operator_brief',
    'referring_site' => $referrer ?: 'https://heycora.in',
];

if ($path) {
    $payload['utm_content'] = $path;
}

$ch = curl_init("https://api.beehiiv.com/v2/publications/" . urlencode($pubId) . "/subscriptions");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$apiKey}",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    $resData = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'subscriberId' => $resData['data']['id'] ?? null,
        'status' => $resData['data']['status'] ?? null,
    ]);
} else {
    http_response_code($httpCode ?: 502);
    echo json_encode([
        'success' => false,
        'error' => 'Could not subscribe right now. Please try again.',
        'details' => $curlErr ?: $response
    ]);
}
