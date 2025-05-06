<?php
header('Content-Type: application/json');

// 1) reCAPTCHA-Secret
$secretKey = '6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg';

// 2) Empfange POST-Daten
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']) ? 1 : 0;
$hp        = $_POST['hp_email'] ?? '';
$token     = $_POST['g-recaptcha-response'] ?? '';
$remoteIp  = $_SERVER['REMOTE_ADDR'];

// 3) Input-Validierung
$errors = '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors .= "Ungültige E-Mail-Adresse. ";
}
if ($password === '') {
  $errors .= "Passwort darf nicht leer sein. ";
}
if ($hp !== '') {
  // Honeypot gefüllt → Bot
  echo json_encode(['success' => false, 'error' => 'Bot erkannt.']);
  exit;
}
if ($errors !== '') {
  echo json_encode(['success' => false, 'error' => trim($errors)]);
  exit;
}

// 4) reCAPTCHA-API-Request
$verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
$postData  = http_build_query([
  'secret'   => $secretKey,
  'response' => $token,
  'remoteip' => $remoteIp
]);
$opts = [
  CURLOPT_URL            => $verifyUrl,
  CURLOPT_POST           => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POSTFIELDS     => $postData,
  CURLOPT_SSL_VERIFYPEER => true
];
$ch       = curl_init();
curl_setopt_array($ch, $opts);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// 5) Auswertung und Antwort
if (!empty($result['success']) && $result['score'] >= 0.5) {
  // TODO: Dein Login-Check (z.B. DB-Query) hier
  // Angenommen, Login erfolgreich:
  echo json_encode(['success' => true]);
} else {
  $msg = 'reCAPTCHA fehlgeschlagen. Bitte erneut versuchen.';
  echo json_encode(['success' => false, 'error' => $msg]);
}
exit;
