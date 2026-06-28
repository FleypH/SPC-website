<?php
/**
 * Shield Point Capital — Partnership Application Form Handler
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

define('RECIPIENT_EMAIL', 'partners@shieldpointcapital.co.zw');
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

$orgName             = trim($data['orgName'] ?? '');
$orgType             = trim($data['orgType'] ?? '');
$businessSector      = trim($data['businessSector'] ?? '');
$country             = trim($data['country'] ?? '');
$website             = trim($data['website'] ?? '');
$contactName         = trim($data['contactName'] ?? '');
$jobTitle            = trim($data['jobTitle'] ?? '');
$contactEmail        = trim($data['contactEmail'] ?? '');
$contactPhone        = trim($data['contactPhone'] ?? '');
$partnershipInterest = trim($data['partnershipInterest'] ?? '');
$services            = $data['services'] ?? [];

if (
    $orgName === '' ||
    $orgType === '' ||
    $businessSector === '' ||
    $country === '' ||
    $contactName === '' ||
    $jobTitle === '' ||
    $contactEmail === '' ||
    $partnershipInterest === ''
) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$servicesList = is_array($services) && count($services) > 0
    ? implode(', ', $services)
    : 'None selected';

$submittedAt = gmdate('F j, Y \a\t g:i A') . ' UTC';

$fields = [
    ['label' => 'Full Name', 'value' => $contactName],
    ['label' => 'Email Address', 'value' => $contactEmail],
    ['label' => 'Phone Number', 'value' => $contactPhone !== '' ? $contactPhone : 'Not provided'],
    ['label' => 'Company Name', 'value' => $orgName],
    ['label' => 'Organization Type', 'value' => $orgType],
    ['label' => 'Business Sector', 'value' => $businessSector],
    ['label' => 'Country', 'value' => $country],
    ['label' => 'Website', 'value' => $website !== '' ? $website : 'Not provided'],
    ['label' => 'Job Title', 'value' => $jobTitle],
    ['label' => 'Selected Service', 'value' => $servicesList],
    ['label' => 'Partnership Interest', 'value' => $partnershipInterest],
];

$summaryFields = [
    ['label' => 'Submitted Name', 'value' => $contactName],
    ['label' => 'Email', 'value' => $contactEmail],
    ['label' => 'Selected Service', 'value' => 'Partnership Application'],
    ['label' => 'Date Submitted', 'value' => $submittedAt],
];

$sent = spc_send_form_emails([
    'admin_to' => RECIPIENT_EMAIL,
    'admin_subject' => SITE_NAME . ' — Partnership Application from ' . $orgName,
    'form_name' => 'Partnership Application',
    'fields' => $fields,
    'client_email' => $contactEmail,
    'client_first_name' => spc_first_name_from($contactName),
    'client_summary_fields' => $summaryFields,
    'reply_to' => $contactEmail,
    'contact_email' => RECIPIENT_EMAIL,
    'submitted_at' => $submittedAt,
]);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send application. Please contact us directly.']);
}
