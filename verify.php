<?php
    define('RECAPTCHA_KEY_PUBLIC', '6LdZLiQrAAAAAGozk6jDs0KOKhgH2kAQ4ZxE-mOZ')
    define('RECAPTCHA_KEY_SECRET', '6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg')
    define('RECAPTCHA_SCORE', 0.6)



    $request = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret= 6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg&response=' . $_POST['recaptcha_token']);

    $request = json_decode($request);


    if (
        isset($request->success, $request->score)
        && $request->success === true
        && $request->score >= RECAPTCHA_SCORE
    ) {
        // reCAPTCHA erfolgreich
        echo 'reCAPTCHA validiert – weiter geht‘s!';
        // Hier dein weiterer Code, z.B. Form-Verarbeitung...
    } else {
        // reCAPTCHA fehlgeschlagen
        echo 'Fehler: reCAPTCHA-Validierung fehlgeschlagen.';
        exit; // Script beenden
    }
?>