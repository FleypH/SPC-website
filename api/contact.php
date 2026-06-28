<?php
/**
 * Shield Point Capital — Contact Form Handler
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

define('RECIPIENT_EMAIL', 'business@shieldpointcapital.co.zw');
define('FROM_EMAIL', 'noreply@shieldpointcapital.com');
define('SITE_NAME', 'Shield Point Capital');
define('SITE_URL', 'https://shieldpointcapital.co.zw');

require_once __DIR__ . '/mail-helper.php';

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

$fullName = trim($firstName . ($lastName !== '' ? ' ' . $lastName : ''));
$submittedAt = gmdate('F j, Y \a\t g:i A') . ' UTC';

$fields = [
    ['label' => 'Full Name', 'value' => $fullName],
    ['label' => 'Email Address', 'value' => $email],
    ['label' => 'Phone Number', 'value' => $phone !== '' ? $phone : 'Not provided'],
    ['label' => 'Selected Service', 'value' => $source],
    ['label' => 'Message / Inquiry', 'value' => $message],
];

$summaryFields = [
    ['label' => 'Submitted Name', 'value' => $fullName],
    ['label' => 'Email', 'value' => $email],
    ['label' => 'Selected Service', 'value' => $source],
    ['label' => 'Date Submitted', 'value' => $submittedAt],
];

$sent = spc_send_form_emails([
    'admin_to' => RECIPIENT_EMAIL,
    'admin_subject' => SITE_NAME . ' — New Contact Form Message from ' . $fullName,
    'form_name' => 'Contact Form — ' . $source,
    'fields' => $fields,
    'client_email' => $email,
    'client_first_name' => spc_first_name_from($fullName),
    'client_summary_fields' => $summaryFields,
    'reply_to' => $email,
    'contact_email' => RECIPIENT_EMAIL,
    'submitted_at' => $submittedAt,
]);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us directly.']);
}
