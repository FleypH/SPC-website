<?php
/**
 * Shield Point Capital — Contact Form Handler
 *
 * Configure the recipient email below before deploying.
 * Requires PHP with mail() enabled, or swap for SMTP (see comments).
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Configuration ──────────────────────────────────────────────
define('RECIPIENT_EMAIL', 'business@shieldpointcapital.co.zw');
define('FROM_EMAIL', 'noreply@shieldpointcapital.com');
define('SITE_NAME', 'Shield Point Capital');
// ───────────────────────────────────────────────────────────────

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$firstName = trim($data['firstName'] ?? '');
$lastName  = trim($data['lastName'] ?? '');
$email     = trim($data['email'] ?? '');
$phone     = trim($data['phone'] ?? '');
$message   = trim($data['message'] ?? '');
$source    = trim($data['source'] ?? 'Contact Page');

if (!empty($data['fullName'])) {
    $firstName = trim($data['fullName']);
    $lastName  = '';
} elseif ($firstName === '' || $lastName === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if ($firstName === '' || $email === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$subject = SITE_NAME . ' — New Contact Form Message from ' . $firstName . ($lastName !== '' ? ' ' . $lastName : '');

$body  = "New contact form submission\n";
$body .= "Source:  {$source}\n\n";
$body .= "Name:    {$firstName}" . ($lastName !== '' ? " {$lastName}" : '') . "\n";
$body .= "Email:   {$email}\n";
$body .= "Phone:   " . ($phone !== '' ? $phone : 'Not provided') . "\n\n";
$body .= "Message:\n{$message}\n";

$headers  = "From: " . FROM_EMAIL . "\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail(RECIPIENT_EMAIL, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us directly.']);
}
