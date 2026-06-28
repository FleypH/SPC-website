<?php
/**
 * Shield Point Capital — Student Loan Application Handler
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
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

require_once __DIR__ . '/email-templates.php';

$firstName   = trim($_POST['firstName'] ?? '');
$lastName    = trim($_POST['lastName'] ?? '');
$idNumber    = trim($_POST['idNumber'] ?? '');
$loanAmount  = trim($_POST['loanAmount'] ?? '');
$email       = trim($_POST['email'] ?? '');

if ($firstName === '' || $lastName === '' || $idNumber === '' || $loanAmount === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!is_numeric($loanAmount) || (float) $loanAmount <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid loan amount.']);
    exit;
}

$files = [
    'idPhoto'   => $_FILES['idPhoto'] ?? null,
    'facePhoto' => $_FILES['facePhoto'] ?? null,
];

$allowedMime = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'application/pdf',
];

foreach ($files as $key => $file) {
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Please upload both required photos.']);
        exit;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Each file must be 5MB or smaller.']);
        exit;
    }

    $mime = mime_content_type($file['tmp_name']);
    if ($key === 'facePhoto') {
        $faceAllowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $faceAllowed, true)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Face photo must be JPG, PNG, or WEBP.']);
            exit;
        }
    } elseif (!in_array($mime, $allowedMime, true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'ID photo must be JPG, PNG, WEBP, or PDF.']);
        exit;
    }
}

$fullName = trim($firstName . ' ' . $lastName);
$submittedAt = gmdate('F j, Y \a\t g:i A') . ' UTC';
$subject = SITE_NAME . ' — Student Loan Application from ' . $fullName;

$fields = [
    ['label' => 'Full Name', 'value' => $fullName],
    ['label' => 'Email Address', 'value' => $email !== '' ? $email : 'Not provided'],
    ['label' => 'ID / Passport Number', 'value' => $idNumber],
    ['label' => 'Selected Service', 'value' => 'Student Loan Application'],
    ['label' => 'Loan Amount', 'value' => '$' . number_format((float) $loanAmount, 2)],
    ['label' => 'Attachments', 'value' => 'ID/Passport photo and face photo included'],
];

$adminData = [
    'form_name' => 'Student Loan Application',
    'fields' => $fields,
    'submitted_at' => $submittedAt,
];

$htmlBody = spc_render_admin_email($adminData);
$plainBody = spc_render_admin_plain($adminData);

$mixedBoundary = 'spc_mix_' . md5((string) microtime(true));
$altBoundary = 'spc_alt_' . md5((string) microtime(true) . 'alt');

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'From: ' . FROM_EMAIL;
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = "Content-Type: multipart/mixed; boundary=\"{$mixedBoundary}\"";

$message = "--{$mixedBoundary}\r\n";
$message .= "Content-Type: multipart/alternative; boundary=\"{$altBoundary}\"\r\n\r\n";
$message .= "--{$altBoundary}\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$message .= $plainBody . "\r\n\r\n";
$message .= "--{$altBoundary}\r\n";
$message .= "Content-Type: text/html; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$message .= $htmlBody . "\r\n\r\n";
$message .= "--{$altBoundary}--\r\n";

foreach ($files as $fieldName => $file) {
    $fileContents = file_get_contents($file['tmp_name']);
    if ($fileContents === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to process uploaded files.']);
        exit;
    }

    $mime = mime_content_type($file['tmp_name']);
    $filename = basename($file['name']);
    $label = $fieldName === 'idPhoto' ? 'id-passport' : 'face-photo';
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($filename, PATHINFO_FILENAME));
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $attachmentName = $label . ($extension ? '.' . $extension : '');

    $message .= "--{$mixedBoundary}\r\n";
    $message .= "Content-Type: {$mime}; name=\"{$attachmentName}\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"{$attachmentName}\"\r\n\r\n";
    $message .= chunk_split(base64_encode($fileContents)) . "\r\n";
}

$message .= "--{$mixedBoundary}--";

$sent = mail(RECIPIENT_EMAIL, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, implode("\r\n", $headers));

if ($sent && $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    require_once __DIR__ . '/mail-helper.php';

    spc_send_mail(
        $email,
        SITE_NAME . ' — Thank You For Contacting Us',
        spc_render_client_email([
            'first_name' => spc_first_name_from($fullName),
            'client_email' => $email,
            'form_name' => 'Student Loan Application',
            'summary_fields' => [
                ['label' => 'Submitted Name', 'value' => $fullName],
                ['label' => 'Email', 'value' => $email],
                ['label' => 'Selected Service', 'value' => 'Student Loan Application'],
                ['label' => 'Date Submitted', 'value' => $submittedAt],
            ],
            'submitted_at' => $submittedAt,
            'contact_email' => RECIPIENT_EMAIL,
        ]),
        spc_render_client_plain([
            'first_name' => spc_first_name_from($fullName),
            'form_name' => 'Student Loan Application',
            'summary_fields' => [
                ['label' => 'Submitted Name', 'value' => $fullName],
                ['label' => 'Email', 'value' => $email],
                ['label' => 'Selected Service', 'value' => 'Student Loan Application'],
                ['label' => 'Date Submitted', 'value' => $submittedAt],
            ],
            'contact_email' => RECIPIENT_EMAIL,
        ]),
        RECIPIENT_EMAIL
    );
}

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Application sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try WhatsApp instead.']);
}
