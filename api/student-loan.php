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
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

$firstName   = trim($_POST['firstName'] ?? '');
$lastName    = trim($_POST['lastName'] ?? '');
$idNumber    = trim($_POST['idNumber'] ?? '');
$loanAmount  = trim($_POST['loanAmount'] ?? '');

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

$subject = SITE_NAME . ' — Student Loan Application from ' . $firstName . ' ' . $lastName;

$body  = "New student loan application\n\n";
$body .= "First Name:   {$firstName}\n";
$body .= "Last Name:    {$lastName}\n";
$body .= "ID/Passport:  {$idNumber}\n";
$body .= "Loan Amount:  \${$loanAmount}\n";

$boundary = md5((string) time());
$headers  = "From: " . FROM_EMAIL . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

$message  = "--{$boundary}\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$message .= $body . "\r\n";

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

    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: {$mime}; name=\"{$attachmentName}\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"{$attachmentName}\"\r\n\r\n";
    $message .= chunk_split(base64_encode($fileContents)) . "\r\n";
}

$message .= "--{$boundary}--";

$sent = mail(RECIPIENT_EMAIL, $subject, $message, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Application sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send email. Please try WhatsApp instead.']);
}
