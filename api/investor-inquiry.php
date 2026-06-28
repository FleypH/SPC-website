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

define('RECIPIENT_EMAIL', 'investors@shieldpointcapital.co.zw');
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

$submittedAt = gmdate('F j, Y \a\t g:i A') . ' UTC';

$fields = [
    ['label' => 'Full Name', 'value' => $fullName],
    ['label' => 'Email Address', 'value' => $workEmail],
    ['label' => 'Company Name', 'value' => $institution],
    ['label' => 'Selected Service', 'value' => 'Investor Inquiry'],
    ['label' => 'Investment Range', 'value' => $investmentRange],
    ['label' => 'Message / Inquiry', 'value' => $message !== '' ? $message : 'Not provided'],
];

$summaryFields = [
    ['label' => 'Submitted Name', 'value' => $fullName],
    ['label' => 'Email', 'value' => $workEmail],
    ['label' => 'Selected Service', 'value' => 'Investor Inquiry'],
    ['label' => 'Date Submitted', 'value' => $submittedAt],
];

$sent = spc_send_form_emails([
    'admin_to' => RECIPIENT_EMAIL,
    'admin_subject' => SITE_NAME . ' — Investor Inquiry from ' . $fullName,
    'form_name' => 'Investor Information Request',
    'fields' => $fields,
    'client_email' => $workEmail,
    'client_first_name' => spc_first_name_from($fullName),
    'client_summary_fields' => $summaryFields,
    'reply_to' => $workEmail,
    'contact_email' => RECIPIENT_EMAIL,
    'submitted_at' => $submittedAt,
]);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Inquiry sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please contact us directly.']);
}
