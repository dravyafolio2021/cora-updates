<?php
/**
 * Cora Production API - Contact Form Bridge
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

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];

$name = htmlspecialchars(trim(substr($data['name'] ?? '', 0, 100)), ENT_QUOTES, 'UTF-8');
$email = strtolower(trim(substr($data['email'] ?? '', 0, 140)));
$phone = htmlspecialchars(trim(substr($data['phone'] ?? '', 0, 40)), ENT_QUOTES, 'UTF-8');
$companyName = htmlspecialchars(trim(substr($data['companyName'] ?? '', 0, 120)), ENT_QUOTES, 'UTF-8');
$industry = htmlspecialchars(trim(substr($data['industry'] ?? '', 0, 100)), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim(substr($data['message'] ?? '', 0, 2000)), ENT_QUOTES, 'UTF-8');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Name and valid email are required.']);
    exit;
}

// Send notification via mail() or log
$targetEmail = getenv('NOTIFICATION_FORWARD_EMAIL') ?: ($env['NOTIFICATION_FORWARD_EMAIL'] ?? 'dravya.bansal@heycora.in');
$subject = "⚡ New Inbound Inquiry from {$name} ({$companyName})";
$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nCompany: {$companyName}\nIndustry: {$industry}\nMessage:\n{$message}\n";
$headers = "From: Cora Inbound <noreply@heycora.in>\r\nReply-To: {$email}\r\nX-Mailer: PHP/" . phpversion();

@mail($targetEmail, $subject, $body, $headers);

echo json_encode([
    'success' => true,
    'message' => 'Thank you! Your message has been received.'
]);
