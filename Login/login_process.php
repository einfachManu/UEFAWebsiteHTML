

<?php
function console_log($data) {
    // Für komplexe Daten zuerst in JSON umwandeln
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    // Dann ein <script> ausgeben, das im Browser console.log() aufruft
    echo "<script>console.log({$json});</script>";
}

// Google reCAPTCHA v3 API Einstellungen
$secretKey = '6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg'; // Ersetzen mit deinem Secret Key

// Standardwertzuweisungen
$valErr = $statusMsg = $api_error = '';
$status = 'error';

// Wenn das Formular abgeschickt wurde
if(isset($_POST['loginForm'])) {
    // Formulardaten abrufen
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? 1 : 0;
    
    // Eingabefelder validieren
    if(empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $valErr .= 'Bitte gib eine gültige E-Mail-Adresse ein.<br/>';
    }
    if(empty($password)) {
        $valErr .= 'Bitte gib dein Passwort ein.<br/>';
    }
    
    // Honeypot-Check (zusätzliche Sicherheit)
    if(!empty($_POST['hp_email'])) {
        // Wahrscheinlich ein Bot - abbrechen ohne Fehlermeldung
        exit();
    }
    
    // Überprüfen, ob Eingabedaten gültig sind
    if(empty($valErr)) {
        // reCAPTCHA-Antwort validieren
        if(!empty($_POST['g-recaptcha-response'])) {
            
            // Google reCAPTCHA-Überprüfungs-API-Anfrage
            $api_url = 'https://www.google.com/recaptcha/api/siteverify';
            $resq_data = array(
                'secret' => $secretKey,
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            );
            
            $curlConfig = array(
                CURLOPT_URL => $api_url,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => $resq_data,
                CURLOPT_SSL_VERIFYPEER => false
            );
            
            $ch = curl_init();
            curl_setopt_array($ch, $curlConfig);
            $response = curl_exec($ch);
            if(curl_errno($ch)) {
                $api_error = curl_error($ch);
            }
            curl_close($ch);
            
            // JSON-Daten der API-Antwort im Array decodieren
            $responseData = json_decode($response);
            
            // Wenn die reCAPTCHA-API-Antwort gültig ist
            if(!empty($responseData) && $responseData->success) {
                // Überprüfe auch den Score (optional)
                $score = $responseData->score;
                
                // Ein niedriger Score deutet auf einen Bot hin
                if($score >= 0.5) {
                    // Hier kannst du deine eigene Login-Logik implementieren
                    // z.B. Datenbankabfrage für Benutzervalidierung
                    
                    // Beispiel: Simulierte erfolgreiche Anmeldung
                    $status = 'success';
                    console_log($score);

                    // Hier könntest du Session starten, Benutzer in Datenbank suchen usw.
                    // session_start();
                    // $_SESSION['user_id'] = $user_id;
                    
                    // Weiterleitung zur Hauptseite nach erfolgreichem Login
                    header("Location: ./Homescreen/Main%20Screen/Main.html");
                    exit();
                } else {
                    // Score ist zu niedrig - wahrscheinlich ein Bot
                    $statusMsg = 'Die Sicherheitsüberprüfung wurde nicht bestanden. Bitte versuche es später erneut.';
                    console_log($score);
                }

            } else {
                $statusMsg = !empty($api_error) ? $api_error : 'Die reCAPTCHA-Überprüfung ist fehlgeschlagen, bitte versuche es erneut.';
            }
        } else {
            $statusMsg = 'Etwas ist schief gelaufen, bitte versuche es erneut.';
        }
    } else {
        $valErr = !empty($valErr) ? '<br/>'.trim($valErr, '<br/>') : '';
        $statusMsg = 'Bitte fülle alle Pflichtfelder aus:' . $valErr;
    }
    
    // Wenn wir hierher gelangen, gab es einen Fehler - zur Login-Seite zurückleiten mit Fehlermeldung
    header("Location: index.html?error=" . urlencode($statusMsg));
    exit();
}

// Falls jemand direkt auf diese Seite zugreift, leite zur Login-Seite um
header("Location: index.html");
exit();
?>