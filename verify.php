<?php
require 'vendor/autoload.php';
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties;

// Das hier sind die Werte, die vom Frontend kommen:
$recaptchaSiteKey    = '6LdZLiQrAAAAAGozk6jDs0KOKhgH2kAQ4ZxE-mOZ';      // derselbe Key wie im <script>-Tag
$recaptchaAction     = 'login';              // gleiche Action wie beim JS-Aufruf
$recaptchaToken      = $_POST['recaptcha_token'] ?? '';
$projectId           = 'my-project-3722-1745570823170'; // dein Google Cloud Projekt

if (empty($recaptchaToken)) {
    die('Kein reCAPTCHA-Token erhalten.');
}

// reCAPTCHA-Client initialisieren
$client = new RecaptchaEnterpriseServiceClient();
$projectName = $client->projectName($projectId);

// Event für die Aktion „login“ erstellen
$event = (new Event())
    ->setSiteKey($recaptchaSiteKey)
    ->setToken($recaptchaToken)
    ->setExpectedAction($recaptchaAction);

// Assessment anlegen
$assessment = (new Assessment())
    ->setEvent($event);

$request = (new \Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest())
    ->setParent($projectName)
    ->setAssessment($assessment);

try {
    $response = $client->createAssessment($request);

    // Token validieren
    $tokenProps = $response->getTokenProperties();
    if (!$tokenProps->getValid()) {
        printf("Ungültiges Token: %s\n", $tokenProps->getInvalidReason());
        exit;
    }

    // Action prüfen
    if ($tokenProps->getAction() !== $recaptchaAction) {
        printf("Ungleiche Action: erwartet '%s', erhalten '%s'\n", $recaptchaAction, $tokenProps->getAction());
        exit;
    }

    // Risiko-Score auslesen (0.0 = vermutlich Bot, 1.0 = vermutlich Mensch)
    $riskScore = $response->getRiskAnalysis()->getScore();
    printf("reCAPTCHA Score: %f\n", $riskScore);

    if ($riskScore < 0.5) {
        // Hier kannst du entscheiden, ob du bei niedrigem Score Login ablehnst
        die("Zu niedriger reCAPTCHA Score, bitte erneut versuchen.");
    }

    // Wenn alles passt: Nutzer einloggen / Session erzeugen / etc.
    // Zum Beispiel:
    // 1. E-Mail und Passwort aus $_POST prüfen
    // 2. Wenn korrekt: session_start(); $_SESSION['user_id'] = ...;

    echo "Login erfolgreich!";
} catch (Exception $e) {
    printf("Fehler bei createAssessment(): %s\n", $e->getMessage());
    exit;
}
