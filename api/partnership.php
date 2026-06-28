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

$subject = SITE_NAME . ' — Partnership Application from ' . $orgName;

$body  = "New partnership application\n\n";
$body .= "── Organization Details ──\n";
$body .= "Organization:      {$orgName}\n";
$body .= "Type:              {$orgType}\n";
$body .= "Business Sector:   {$businessSector}\n";
$body .= "Country:           {$country}\n";
$body .= "Website:           " . ($website !== '' ? $website : 'Not provided') . "\n\n";
$body .= "── Primary Contact ──\n";
$body .= "Name:              {$contactName}\n";
$body .= "Job Title:         {$jobTitle}\n";
$body .= "Email:             {$contactEmail}\n";
$body .= "Phone:             " . ($contactPhone !== '' ? $contactPhone : 'Not provided') . "\n\n";
$body .= "── Partnership Details ──\n";
$body .= "Interest:\n{$partnershipInterest}\n\n";
$body .= "Services of Interest: {$servicesList}\n";

$headers  = "From: " . FROM_EMAIL . "\r\n";
$headers .= "Reply-To: {$contactEmail}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail(RECIPIENT_EMAIL, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send application. Please contact us directly.']);
}
