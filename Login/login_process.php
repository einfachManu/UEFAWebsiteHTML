<?php
// Aktiviere Fehlermeldungen während der Entwicklung
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Helper-Funktion für Console-Logs (Debugging)
function console_log($data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    echo "<script>console.log({$json});</script>";
}

// Google reCAPTCHA v3 API Einstellungen
$secretKey = '6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg'; // Dein Secret Key

// Standardwertzuweisungen
$response = [
    'success' => false,
    'error' => '',
    'debug' => []
];

// Header für JSON-Antwort setzen
header('Content-Type: application/json');

// Wenn das Formular abgeschickt wurde
if(isset($_POST['email'])) {
    // Formulardaten abrufen
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? 1 : 0;
    $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';
    
    // Eingabefelder validieren
    if(empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $response['error'] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
        echo json_encode($response);
        exit();
    }
    
    if(empty($password)) {
        $response['error'] = 'Bitte gib dein Passwort ein.';
        echo json_encode($response);
        exit();
    }
    
    // Honeypot-Check (zusätzliche Sicherheit)
    if(!empty($_POST['hp_email'])) {
        // Wahrscheinlich ein Bot - abbrechen ohne spezifische Fehlermeldung
        $response['error'] = 'Ungültige Anfrage.';
        echo json_encode($response);
        exit();
    }
    
    // reCAPTCHA-Antwort validieren
    if(!empty($recaptchaToken)) {
        // Google reCAPTCHA-Überprüfungs-API-Anfrage
        $api_url = 'https://www.google.com/recaptcha/api/siteverify';
        $request_data = [
            'secret' => $secretKey,
            'response' => $recaptchaToken,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $curlConfig = [
            CURLOPT_URL => $api_url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $request_data,
            CURLOPT_SSL_VERIFYPEER => true  // In Produktion sollte dies true sein
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, $curlConfig);
        $curl_response = curl_exec($ch);
        
        if(curl_errno($ch)) {
            $response['error'] = 'Fehler bei der Sicherheitsüberprüfung: ' . curl_error($ch);
            $response['debug']['curl_error'] = curl_error($ch);
            echo json_encode($response);
            curl_close($ch);
            exit();
        }
        
        curl_close($ch);
        
        // JSON-Daten der API-Antwort decodieren
        $responseData = json_decode($curl_response);
        $response['debug']['recaptcha_response'] = $responseData;
        
        // Wenn die reCAPTCHA-API-Antwort gültig ist
        if(!empty($responseData) && $responseData->success) {
            // Überprüfe den Score (0.0 ist wahrscheinlich ein Bot, 1.0 ist wahrscheinlich ein Mensch)
            $score = $responseData->score;
            $response['debug']['score'] = $score;
            
            // Score-Schwellenwert (anpassen nach Bedarf, 0.5 ist ein guter Startpunkt)
            if($score >= 0.5) {
                // Hier deine eigene Login-Logik implementieren
                // z.B. Datenbankabfrage für Benutzervalidierung
                
                // Simulierte Anmeldung für dieses Beispiel
                $response['success'] = true;
                echo json_encode($response);
                exit();
                
                // In einer echten Anwendung:
                // 1. Überprüfe Benutzeranmeldedaten in der Datenbank
                // 2. Setze Session-Variablen
                // 3. Sende Erfolgsantwort zurück
            } else {
                // Score ist zu niedrig - wahrscheinlich ein Bot
                $response['error'] = 'Die Sicherheitsüberprüfung wurde nicht bestanden. Bitte versuche es später erneut.';
                echo json_encode($response);
                exit();
            }
        } else {
            $response['error'] = 'Die reCAPTCHA-Überprüfung ist fehlgeschlagen, bitte versuche es erneut.';
            echo json_encode($response);
            exit();
        }
    } else {
        $response['error'] = 'reCAPTCHA-Token fehlt. Bitte aktualisiere die Seite und versuche es erneut.';
        echo json_encode($response);
        exit();
    }
} else {
    $response['error'] = 'Ungültige Anfrage. Bitte verwende das Login-Formular.';
    echo json_encode($response);
    exit();
}
?>