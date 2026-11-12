<?php
declare(strict_types=1);

// Where website enquiries are delivered. Change this to info@truehome.com.ug
// once that mailbox has been created in cPanel.
const RECIPIENT = 'ramzyhadah@gmail.com';
// Must be an address on this hosting account's domain, or many inboxes will
// treat the message as spam. Visitors are reached through Reply-To instead.
const SENDER = 'no-reply@truehome.com.ug';

function back(string $result): never
{
    header('Location: contact.html?' . $result . '#contact-form', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: contact.html', true, 303);
    exit;
}

// Hidden field that only bots fill in: pretend success and discard.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    back('sent=1');
}

$field = static fn (string $key): string => trim((string) ($_POST[$key] ?? ''));
$name = $field('full_name');
$email = $field('email');
$phone = $field('phone');
$subject = $field('subject');
$message = $field('message');

$singleLine = static fn (string $value): bool => !preg_match('/[\r\n]/', $value);

if (
    $name === '' || $subject === '' || $message === ''
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !$singleLine($name) || !$singleLine($email) || !$singleLine($subject) || !$singleLine($phone)
    || mb_strlen($name) > 160 || mb_strlen($email) > 190 || mb_strlen($phone) > 40
    || mb_strlen($subject) > 200 || mb_strlen($message) > 10000
) {
    back('error=1');
}

$body = "New enquiry from the Trume website\n\n"
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

$encodedSubject = '=?UTF-8?B?' . base64_encode('Website enquiry: ' . $subject) . '?=';
$sent = mail(RECIPIENT, $encodedSubject, $body, $headers, '-f' . SENDER);

back($sent ? 'sent=1' : 'error=send');
