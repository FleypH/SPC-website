<?php
/**
 * Shield Point Capital — HTML Mail Helper
 */

require_once __DIR__ . '/email-templates.php';

/**
 * Send multipart HTML + plain-text email.
 */
function spc_send_mail(
    string $to,
    string $subject,
    string $htmlBody,
    string $plainBody,
    ?string $replyTo = null,
    ?string $fromEmail = null,
    ?string $fromName = null
): bool {
    $fromEmail = $fromEmail ?? (defined('FROM_EMAIL') ? FROM_EMAIL : 'noreply@shieldpointcapital.com');
    $fromName = $fromName ?? (defined('SITE_NAME') ? SITE_NAME : 'Shield Point Capital');
    $boundary = 'spc_' . md5((string) microtime(true));

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'From: ' . spc_mail_encode_name($fromName) . " <{$fromEmail}>";
    if ($replyTo) {
        $headers[] = "Reply-To: {$replyTo}";
    }
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $plainBody . "\r\n\r\n";
    $message .= "--{$boundary}\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message .= $htmlBody . "\r\n\r\n";
    $message .= "--{$boundary}--";

    return mail($to, spc_mail_encode_subject($subject), $message, implode("\r\n", $headers));
}

function spc_mail_encode_name(string $name): string
{
    return preg_match('/[^\x20-\x7E]/', $name)
        ? '=?UTF-8?B?' . base64_encode($name) . '?='
        : $name;
}

function spc_mail_encode_subject(string $subject): string
{
    return '=?UTF-8?B?' . base64_encode($subject) . '?=';
}

/**
 * Send admin notification and optional client confirmation.
 *
 * @param array{
 *   admin_to: string,
 *   admin_subject: string,
 *   form_name: string,
 *   fields: array<int, array{label: string, value: string}>,
 *   client_email?: string,
 *   client_first_name?: string,
 *   client_summary_fields?: array<int, array{label: string, value: string}>,
 *   reply_to?: string,
 *   contact_email?: string,
 *   submitted_at?: string
 * } $options
 */
function spc_send_form_emails(array $options): bool
{
    $submittedAt = $options['submitted_at'] ?? gmdate('F j, Y \a\t g:i A') . ' UTC';
    $contactEmail = $options['contact_email'] ?? $options['admin_to'];

    $adminData = [
        'form_name' => $options['form_name'],
        'fields' => $options['fields'],
        'submitted_at' => $submittedAt,
    ];

    $adminSent = spc_send_mail(
        $options['admin_to'],
        $options['admin_subject'],
        spc_render_admin_email($adminData),
        spc_render_admin_plain($adminData),
        $options['reply_to'] ?? null
    );

    $clientEmail = trim($options['client_email'] ?? '');
    if ($adminSent && $clientEmail !== '' && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $clientData = [
            'first_name' => $options['client_first_name'] ?? 'there',
            'client_email' => $clientEmail,
            'form_name' => $options['form_name'],
            'summary_fields' => $options['client_summary_fields'] ?? [],
            'submitted_at' => $submittedAt,
            'contact_email' => $contactEmail,
        ];

        spc_send_mail(
            $clientEmail,
            (defined('SITE_NAME') ? SITE_NAME : 'Shield Point Capital') . ' — Thank You For Contacting Us',
            spc_render_client_email($clientData),
            spc_render_client_plain($clientData),
            $contactEmail
        );
    }

    return $adminSent;
}

/**
 * Extract a friendly first name from a full name string.
 */
function spc_first_name_from(string $fullName): string
{
    $fullName = trim($fullName);
    if ($fullName === '') {
        return 'there';
    }

    $parts = preg_split('/\s+/', $fullName);

    return $parts[0] ?? 'there';
}
