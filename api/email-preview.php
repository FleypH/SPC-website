<?php
/**
 * Preview email templates in the browser during development.
 * Remove or protect this file before production deployment.
 */

require_once __DIR__ . '/email-templates.php';

define('SITE_NAME', 'Shield Point Capital');
define('SITE_URL', 'https://shieldpointcapital.co.zw');

$type = $_GET['type'] ?? 'admin';
$submittedAt = 'June 27, 2026 at 2:30 PM UTC';

if ($type === 'client') {
    header('Content-Type: text/html; charset=UTF-8');
    echo spc_render_client_email([
        'first_name' => 'Tendai',
        'client_email' => 'tendai@example.com',
        'form_name' => 'Contact Form — General Inquiry',
        'summary_fields' => [
            ['label' => 'Submitted Name', 'value' => 'Tendai Moyo'],
            ['label' => 'Email', 'value' => 'tendai@example.com'],
            ['label' => 'Selected Service', 'value' => 'Core Services'],
            ['label' => 'Date Submitted', 'value' => $submittedAt],
        ],
        'submitted_at' => $submittedAt,
        'contact_email' => 'business@shieldpointcapital.co.zw',
    ]);
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
echo spc_render_admin_email([
    'form_name' => 'Contact Form — General Inquiry',
    'submitted_at' => $submittedAt,
    'fields' => [
        ['label' => 'Full Name', 'value' => 'Tendai Moyo'],
        ['label' => 'Email Address', 'value' => 'tendai@example.com'],
        ['label' => 'Phone Number', 'value' => '+263 776 492 182'],
        ['label' => 'Company Name', 'value' => 'Moyo Capital Partners'],
        ['label' => 'Selected Service', 'value' => 'Core Services'],
        ['label' => 'Message / Inquiry', 'value' => "I would like to learn more about your loan facilitation services and how we can collaborate across Zimbabwe."],
    ],
]);
