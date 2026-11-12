<?php
declare(strict_types=1);
require __DIR__ . '/../admin/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
$name = trim((string) ($_POST['full_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    header('Location: ../contact.html?sent=1#contact-form');
    exit;
}
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
    header('Location: ../contact.html?error=1#contact-form'); exit;
}
if (mb_strlen($name) > 160 || mb_strlen($email) > 190 || mb_strlen($subject) > 255 || mb_strlen($message) > 10000) {
    header('Location: ../contact.html?error=1#contact-form'); exit;
}
db()->prepare('INSERT INTO messages (full_name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)')->execute([$name, $email, $phone ?: null, $subject, $message]);
header('Location: ../contact.html?sent=1#contact-form');
exit;
