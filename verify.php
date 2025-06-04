<?php
require 'vendor/autoload.php';
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;

// 1) Empfange das reCAPTCHA-Token und die restlichen Login-Daten aus POST
$token             = $_POST['recaptcha_token'] ?? '';
$email             = $_POST['email'] ?? '';
$password          = $_POST['password'] ?? '';
$projectId         = 'umemfma.com';
$recaptchaSiteKey  = '6LdZLiQrAAAAAGozk6jDs0KOKhgH2kAQ4ZxE-mOZ';
$expectedAction    = 'login';

// 2) Token da? Wenn nicht → sofort abbrechen
if (!$token) {
    die('reCAPTCHA-Token fehlt.');
}

// 3) Client initialisieren und Assessment anlegen
$client      = new RecaptchaEnterpriseServiceClient();
$projectName = $client->projectName($projectId);

$event = (new Event())
    ->setSiteKey($recaptchaSiteKey)
    ->setToken($token)
    ->setExpectedAction($expectedAction);

$assessment = (new Assessment())->setEvent($event);
$request    = (new CreateAssessmentRequest())
    ->setParent($projectName)
    ->setAssessment($assessment);

try {
    $response = $client->createAssessment($request);
} catch (Exception $e) {
    die('reCAPTCHA-API-Fehler: ' . $e->getMessage());
}

// 4) Antworten auslesen
$tokenProps   = $response->getTokenProperties();
$riskAnalysis = $response->getRiskAnalysis();

$valid           = $tokenProps->getValid();
$returnedAction  = $tokenProps->getAction();
$invalidReason   = $tokenProps->getInvalidReason();
$score           = $riskAnalysis->getScore();
$reasons         = $riskAnalysis->getReasons(); // Array mit Grund-Codes, falls vorhanden
$userIpAddress   = $_SERVER['REMOTE_ADDR'];    // Optionale Speicherung für Logs

// 5) Loggen – immer in DB oder Datei protokollieren
// In der Praxis: INSERT INTO recaptcha_logs (email, ip, score, valid, action, invalidReason, reasons, time)
// VALUES (...);
// Hier nur ein kurzes Beispiel im Error-Log:
error_log(sprintf(
    "[reCAPTCHA-Log] E-Mail=%s, IP=%s, valid=%s, action=%s, score=%.2f, reasons=%s",
    $email,
    $userIpAddress,
    $valid ? 'true' : 'false',
    $returnedAction,
    $score,
    implode(',', $reasons)
));

// 6) Validität prüfen
if (!$valid) {
    die('reCAPTCHA ungültig: ' . $invalidReason);
}

// 7) Action checken
if ($returnedAction !== $expectedAction) {
    die('reCAPTCHA-Action stimmt nicht überein.');
}

// 8) Score auswerten und Gegenmaßnahmen
if ($score >= 1.0) {
    session_start();
    $_SESSION['user_email'] = $email;
    header('Location: dashboard.php');
    exit;
}

?>
<form id="redirectForm" action="recaptcha_v2.php" method="POST" style="display:none;">
  <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>" />
  <input type="hidden" name="password" value="<?= htmlspecialchars($password); ?>" />
  <!-- Du könntest hier auch per Session speichern statt im versteckten Feld -->
</form>
<script>
  // Automatisch Formular absenden, um E-Mail/Passwort an recaptcha_v2.php zu übergeben
  document.getElementById('redirectForm').submit();
</script>