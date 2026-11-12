<?php
// Handles the Contact page form and the Partners page enquiry form.
// Each message is saved to the admin inbox (when the shared database is available)
// and emailed to the company email address set in the admin's Site settings.
declare(strict_types=1);
require __DIR__ . '/includes/site.php';

// Must be an address on this hosting account's domain, or many inboxes will
// treat the message as spam. Visitors are reached through Reply-To instead.
const SENDER = 'no-reply@truehome.com.ug';

$isPartner = ($_POST['form_type'] ?? '') === 'partner';
$returnTo = $isPartner ? 'partners.php' : 'contact.php';
$anchor = $isPartner ? '#partner-enquiry' : '#contact-form';

function back(string $page, string $anchor, string $result): never
{
    header('Location: ' . $page . '?' . $result . $anchor, true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: contact.php', true, 303);
    exit;
}

// Hidden field that only bots fill in: pretend success and discard.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    back($returnTo, $anchor, 'sent=1');
}

$field = static fn (string $key): string => trim((string) ($_POST[$key] ?? ''));

if ($isPartner) {
    $organisation = $field('organisation');
    $area = $field('area');
    $name = $field('name');
    $email = $field('email');
    $phone = '';
    $subject = 'Partnership enquiry: ' . $area . ($organisation !== '' ? ' (' . $organisation . ')' : '');
    $message = "Organisation: {$organisation}\nPartnership area: {$area}\n\n" . $field('message');
    $required = [$organisation, $area, $name, $field('message')];
} else {
    $name = $field('full_name');
    $email = $field('email');
    $phone = $field('phone');
    $subject = $field('subject');
    $message = $field('message');
    $required = [$name, $subject, $message];
}

$singleLine = static fn (string $value): bool => !preg_match('/[\r\n]/', $value);

if (
    in_array('', $required, true)
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !$singleLine($name) || !$singleLine($email) || !$singleLine($subject) || !$singleLine($phone)
    || mb_strlen($name) > 160 || mb_strlen($email) > 190 || mb_strlen($phone) > 40
    || mb_strlen($subject) > 255 || mb_strlen($message) > 10000
) {
    back($returnTo, $anchor, 'error=1');
}

// 1. Save to the admin inbox.
$saved = false;
if ($db = site_db()) {
    try {
        $db->prepare('INSERT INTO messages (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)')
            ->execute([$name, $email, $phone !== '' ? $phone : null, $subject, $message]);
        $saved = true;
    } catch (Throwable $exception) {
        error_log('Trume site: could not save message: ' . $exception->getMessage());
    }
}

// 2. Email it.
$body = 'New ' . ($isPartner ? 'partnership enquiry' : 'enquiry') . " from the Trume website\n\n"
    . "Name:    {$name}\n"
    . "Email:   {$email}\n"
    . 'Phone:   ' . ($phone !== '' ? $phone : 'Not provided') . "\n"
    . "Subject: {$subject}\n\n"
    . "Message:\n{$message}\n";

$headers = [
    'From' => 'Trume Website <' . SENDER . '>',
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8',
    'X-Mailer' => 'Trume website',
];
$recipient = setting('company_email');
$encodedSubject = '=?UTF-8?B?' . base64_encode('Website: ' . $subject) . '?=';
$emailed = $recipient !== '' && @mail($recipient, $encodedSubject, $body, $headers, '-f' . SENDER);

// Success if the team will see it in either place.
back($returnTo, $anchor, ($saved || $emailed) ? 'sent=1' : 'error=send');
