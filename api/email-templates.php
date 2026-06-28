<?php
/**
 * Shield Point Capital — HTML Email Templates
 *
 * Table-based, inline-CSS templates for Gmail, Outlook, and Apple Mail.
 */

define('SPC_EMAIL_BRAND_GREEN', '#0B6B4A');
define('SPC_EMAIL_BG_CREAM', '#FAF9F6');
define('SPC_EMAIL_TEXT_DARK', '#1A1A1A');
define('SPC_EMAIL_TEXT_MUTED', '#5C5C5C');
define('SPC_EMAIL_CARD_BORDER', '#E8E6E1');
define('SPC_EMAIL_CARD_SHADOW', '0 4px 24px rgba(11, 107, 74, 0.08)');

/**
 * @return string Public logo URL for email clients.
 */
function spc_email_logo_url(): string
{
    $base = defined('SITE_URL') ? SITE_URL : 'https://shieldpointcapital.co.zw';

    return rtrim($base, '/') . '/assets/main/spclogo.png';
}

/**
 * @return string Public website URL.
 */
function spc_email_site_url(): string
{
    return defined('SITE_URL') ? rtrim(SITE_URL, '/') : 'https://shieldpointcapital.co.zw';
}

/**
 * Escape HTML for email body content.
 */
function spc_email_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Build a labeled field row for admin / summary cards.
 *
 * @param array<int, array{label: string, value: string}> $fields
 */
function spc_email_fields_rows(array $fields): string
{
    $rows = '';

    foreach ($fields as $index => $field) {
        $value = trim($field['value'] ?? '');
        if ($value === '') {
            continue;
        }

        $borderTop = $index > 0
            ? 'border-top:1px solid ' . SPC_EMAIL_CARD_BORDER . ';'
            : '';

        $rows .= '
          <tr>
            <td style="padding:14px 20px;' . $borderTop . '">
              <p style="margin:0 0 4px;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:' . SPC_EMAIL_BRAND_GREEN . ';">'
            . spc_email_escape($field['label']) . '</p>
              <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.55;color:' . SPC_EMAIL_TEXT_DARK . ';">'
            . nl2br(spc_email_escape($value)) . '</p>
            </td>
          </tr>';
    }

    return $rows;
}

/**
 * Plain-text fallback from labeled fields.
 *
 * @param array<int, array{label: string, value: string}> $fields
 */
function spc_email_fields_plain(array $fields): string
{
    $lines = [];

    foreach ($fields as $field) {
        $value = trim($field['value'] ?? '');
        if ($value === '') {
            continue;
        }
        $lines[] = $field['label'] . ': ' . $value;
    }

    return implode("\n", $lines);
}

/**
 * Admin notification — new form submission.
 *
 * @param array{
 *   form_name?: string,
 *   fields: array<int, array{label: string, value: string}>,
 *   submitted_at?: string
 * } $data
 */
function spc_render_admin_email(array $data): string
{
    $formName = spc_email_escape($data['form_name'] ?? 'Website Form');
    $submittedAt = spc_email_escape(
        $data['submitted_at'] ?? gmdate('F j, Y \a\t g:i A') . ' UTC'
    );
    $logoUrl = spc_email_escape(spc_email_logo_url());
    $siteName = spc_email_escape(defined('SITE_NAME') ? SITE_NAME : 'Shield Point Capital');
    $fieldsHtml = spc_email_fields_rows($data['fields']);

    return '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>New Form Submission Received</title>
  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:' . SPC_EMAIL_BG_CREAM . ';width:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">New form submission received from your website.</div>
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:' . SPC_EMAIL_BG_CREAM . ';margin:0;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;width:100%;">
          <tr>
            <td style="background-color:' . SPC_EMAIL_BRAND_GREEN . ';border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
              <img src="' . $logoUrl . '" alt="' . $siteName . '" width="140" style="display:block;margin:0 auto 16px;border:0;outline:none;text-decoration:none;height:auto;max-width:140px;" />
              <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:rgba(255,255,255,0.82);">Admin Notification</p>
              <h1 style="margin:10px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:1.3;font-weight:700;color:#FFFFFF;">New Form Submission Received</h1>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:24px 28px 8px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr>
                  <td style="padding-bottom:18px;">
                    <p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:' . SPC_EMAIL_BRAND_GREEN . ';">Form Type</p>
                    <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:700;color:' . SPC_EMAIL_TEXT_DARK . ';">' . $formName . '</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:0 28px 28px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-radius:10px;overflow:hidden;box-shadow:' . SPC_EMAIL_CARD_SHADOW . ';">
                ' . $fieldsHtml . '
                <tr>
                  <td style="padding:14px 20px;border-top:1px solid ' . SPC_EMAIL_CARD_BORDER . ';background-color:#F7FAF8;">
                    <p style="margin:0 0 4px;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:' . SPC_EMAIL_BRAND_GREEN . ';">Submission Date &amp; Time</p>
                    <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.55;color:' . SPC_EMAIL_TEXT_DARK . ';">' . $submittedAt . '</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-top:0;border-radius:0 0 12px 12px;padding:20px 28px 28px;text-align:center;">
              <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6;color:' . SPC_EMAIL_TEXT_MUTED . ';">This message was generated automatically from your website.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}

/**
 * Client confirmation — thank you email.
 *
 * @param array{
 *   first_name: string,
 *   client_email?: string,
 *   form_name?: string,
 *   summary_fields?: array<int, array{label: string, value: string}>,
 *   submitted_at?: string,
 *   contact_email?: string,
 *   website_url?: string
 * } $data
 */
function spc_render_client_email(array $data): string
{
    $firstName = spc_email_escape($data['first_name'] ?? 'there');
    $formName = spc_email_escape($data['form_name'] ?? 'your inquiry');
    $submittedAt = spc_email_escape(
        $data['submitted_at'] ?? gmdate('F j, Y \a\t g:i A') . ' UTC'
    );
    $contactEmail = spc_email_escape($data['contact_email'] ?? 'business@shieldpointcapital.co.zw');
    $websiteUrl = spc_email_escape($data['website_url'] ?? spc_email_site_url());
    $logoUrl = spc_email_escape(spc_email_logo_url());
    $siteName = spc_email_escape(defined('SITE_NAME') ? SITE_NAME : 'Shield Point Capital');

    $summaryFields = $data['summary_fields'] ?? [];
    if (count($summaryFields) === 0) {
        $summaryFields = [
            ['label' => 'Submitted Name', 'value' => $data['first_name'] ?? ''],
            ['label' => 'Email', 'value' => $data['client_email'] ?? ''],
            ['label' => 'Selected Service', 'value' => $data['form_name'] ?? 'General Inquiry'],
            ['label' => 'Date Submitted', 'value' => $data['submitted_at'] ?? gmdate('F j, Y')],
        ];
    }

    $summaryHtml = spc_email_fields_rows($summaryFields);

    return '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Thank You For Contacting Us</title>
</head>
<body style="margin:0;padding:0;background-color:' . SPC_EMAIL_BG_CREAM . ';width:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">Thank you for contacting Shield Point Capital. We received your submission.</div>
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:' . SPC_EMAIL_BG_CREAM . ';margin:0;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;width:100%;">
          <tr>
            <td style="background-color:' . SPC_EMAIL_BRAND_GREEN . ';border-radius:12px 12px 0 0;padding:28px 32px;text-align:center;">
              <img src="' . $logoUrl . '" alt="' . $siteName . '" width="140" style="display:block;margin:0 auto 16px;border:0;outline:none;text-decoration:none;height:auto;max-width:140px;" />
              <h1 style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:24px;line-height:1.3;font-weight:700;color:#FFFFFF;">Thank You For Contacting Us</h1>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:28px 28px 8px;">
              <p style="margin:0 0 16px;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.65;color:' . SPC_EMAIL_TEXT_DARK . ';">Hi ' . $firstName . ',</p>
              <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.65;color:' . SPC_EMAIL_TEXT_MUTED . ';">Thank you for reaching out. We have successfully received your submission regarding <strong style="color:' . SPC_EMAIL_TEXT_DARK . ';">' . $formName . '</strong> and our team will review your request shortly.</p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:8px 28px 24px;">
              <p style="margin:0 0 12px;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:' . SPC_EMAIL_BRAND_GREEN . ';">Submission Summary</p>
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-radius:10px;overflow:hidden;box-shadow:' . SPC_EMAIL_CARD_SHADOW . ';">
                ' . $summaryHtml . '
              </table>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:0 28px 28px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#F7FAF8;border:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-radius:10px;">
                <tr>
                  <td style="padding:20px 22px;">
                    <p style="margin:0 0 12px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:' . SPC_EMAIL_TEXT_DARK . ';">What happens next?</p>
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                      <tr>
                        <td valign="top" width="18" style="padding:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:' . SPC_EMAIL_BRAND_GREEN . ';">&#10003;</td>
                        <td style="padding:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.55;color:' . SPC_EMAIL_TEXT_MUTED . ';">Our team reviews your request</td>
                      </tr>
                      <tr>
                        <td valign="top" width="18" style="padding:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:' . SPC_EMAIL_BRAND_GREEN . ';">&#10003;</td>
                        <td style="padding:0 0 10px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.55;color:' . SPC_EMAIL_TEXT_MUTED . ';">We will contact you within 24&ndash;48 hours</td>
                      </tr>
                      <tr>
                        <td valign="top" width="18" style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.5;color:' . SPC_EMAIL_BRAND_GREEN . ';">&#10003;</td>
                        <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.55;color:' . SPC_EMAIL_TEXT_MUTED . ';">We may request additional information if needed</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border-left:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-right:1px solid ' . SPC_EMAIL_CARD_BORDER . ';padding:0 28px 32px;text-align:center;">
              <!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="' . $websiteUrl . '" style="height:46px;v-text-anchor:middle;width:220px;" arcsize="12%" stroke="f" fillcolor="' . SPC_EMAIL_BRAND_GREEN . '">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;">Visit Our Website</center>
              </v:roundrect>
              <![endif]-->
              <!--[if !mso]><!-->
              <a href="' . $websiteUrl . '" style="display:inline-block;background-color:' . SPC_EMAIL_BRAND_GREEN . ';color:#FFFFFF;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:700;text-decoration:none;text-transform:uppercase;letter-spacing:0.06em;padding:14px 28px;border-radius:6px;box-shadow:0 4px 14px rgba(11,107,74,0.28);">Visit Our Website</a>
              <!--<![endif]-->
            </td>
          </tr>
          <tr>
            <td style="background-color:#FFFFFF;border:1px solid ' . SPC_EMAIL_CARD_BORDER . ';border-top:0;border-radius:0 0 12px 12px;padding:22px 28px 28px;text-align:center;">
              <p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:700;color:' . SPC_EMAIL_TEXT_DARK . ';">' . $siteName . '</p>
              <p style="margin:0 0 6px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6;color:' . SPC_EMAIL_TEXT_MUTED . ';"><a href="mailto:' . $contactEmail . '" style="color:' . SPC_EMAIL_BRAND_GREEN . ';text-decoration:none;">' . $contactEmail . '</a></p>
              <p style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6;color:' . SPC_EMAIL_TEXT_MUTED . ';"><a href="' . $websiteUrl . '" style="color:' . SPC_EMAIL_BRAND_GREEN . ';text-decoration:none;">' . $websiteUrl . '</a></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}

/**
 * Plain-text admin notification.
 */
function spc_render_admin_plain(array $data): string
{
    $formName = $data['form_name'] ?? 'Website Form';
    $submittedAt = $data['submitted_at'] ?? gmdate('F j, Y \a\t g:i A') . ' UTC';
    $fields = spc_email_fields_plain($data['fields'] ?? []);

    return "NEW FORM SUBMISSION RECEIVED\n\n"
        . "Form Type: {$formName}\n\n"
        . "{$fields}\n\n"
        . "Submission Date & Time: {$submittedAt}\n\n"
        . "This message was generated automatically from your website.\n";
}

/**
 * Plain-text client confirmation.
 */
function spc_render_client_plain(array $data): string
{
    $firstName = $data['first_name'] ?? 'there';
    $formName = $data['form_name'] ?? 'your inquiry';
    $websiteUrl = $data['website_url'] ?? spc_email_site_url();
    $contactEmail = $data['contact_email'] ?? 'business@shieldpointcapital.co.zw';
    $summary = spc_email_fields_plain($data['summary_fields'] ?? []);

    return "Hi {$firstName},\n\n"
        . "Thank you for reaching out. We have successfully received your submission regarding {$formName} and our team will review your request shortly.\n\n"
        . "SUBMISSION SUMMARY\n{$summary}\n\n"
        . "WHAT HAPPENS NEXT?\n"
        . "- Our team reviews your request\n"
        . "- We will contact you within 24-48 hours\n"
        . "- We may request additional information if needed\n\n"
        . "Visit our website: {$websiteUrl}\n"
        . "Contact: {$contactEmail}\n";
}
