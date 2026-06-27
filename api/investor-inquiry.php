<?php
/**
 * Shield Point Capital — Investor Inquiry Form Handler
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

define('RECIPIENT_EMAIL', 'investors@shieldpointcapital.com');
define('FROM_EMAIL', 'noreply@shieldpointcapital.com');
define('SITE_NAME', 'Shield Point Capital');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$fullName         = trim($data['fullName'] ?? '');
$institution      = trim($data['institution'] ?? '');
$workEmail        = trim($data['workEmail'] ?? '');
$investmentRange  = trim($data['investmentRange'] ?? '');
$message          = trim($data['message'] ?? '');

if ($fullName === '' || $institution === '' || $workEmail === '' || $investmentRange === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($workEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$subject = SITE_NAME . ' — Investor Inquiry from ' . $fullName;

$body  = "New investor inquiry\n\n";
$body .= "Name:              {$fullName}\n";
$body .= "Institution:       {$institution}\n";
$body .= "Work Email:        {$workEmail}\n";
$body .= "Investment Range:  {$investmentRange}\n\n";
$body .= "Message:\n" . ($message !== '' ? $message : 'Not provided') . "\n";

$headers  = "From: " . FROM_EMAIL . "\r\n";
$headers .= "Reply-To: {$workEmail}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail(RECIPIENT_EMAIL, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Inquiry sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us directly.']);
}
