<?php
// Deploy this file on your own PHP hosting (same domain as the site, or any host reachable via HTTPS).
// It receives the quiz JSON payload and emails it to you via Zoho Mail SMTP.
// Requires PHPMailer: composer require phpmailer/phpmailer  (or download it manually into ./PHPMailer)

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // tighten to your domain in production
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'invalid_json']);
  exit;
}

// Honeypot check
if (!empty($data['website'])) {
  echo json_encode(['success' => true]); // silently accept, don't send
  exit;
}

$name    = trim($data['name'] ?? '');
$email   = trim($data['email'] ?? '');
$phone   = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');
$type    = $data['type'] ?? '';
$answers = $data['answers'] ?? [];

if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$phone) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'missing_fields']);
  exit;
}

require __DIR__ . '/vendor/autoload.php'; // run `composer install` on the server first

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
  // --- Zoho Mail SMTP settings ---
  $mail->isSMTP();
  $mail->Host       = 'smtp.zoho.eu';       // use smtp.zoho.com if your Zoho account is on the .com data center
  $mail->SMTPAuth   = true;
  $mail->Username   = 'office@pixelfenster.at';   // your full Zoho mailbox address
  $mail->Password   = getenv('ZOHO_SMTP_PASSWORD'); // set this as an env var / app-specific password, never hardcode
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port       = 465;

  $mail->setFrom('office@pixelfenster.at', 'PixelFenster Website');
  $mail->addAddress('office@pixelfenster.at');
  $mail->addReplyTo($email, $name);

  $label = $type === 'partner' ? 'Standortpartner-Anfrage' : 'Werbekunden-Anfrage';
  $mail->Subject = $label . ' — ' . $name;

  $body = "Neue Anfrage: $label\n\n";
  $body .= "Name: $name\nE-Mail: $email\nTelefon: $phone\n\n";
  foreach ($answers as $k => $v) { $body .= ucfirst($k) . ": $v\n"; }
  if ($message) { $body .= "\nNachricht:\n$message\n"; }

  $mail->Body = $body;
  $mail->send();

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => 'send_failed']);
}
