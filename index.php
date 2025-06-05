<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $recaptcha_url      = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret   = '6LdZLiQrAAAAAOW87kWekFUxEb-cvbQpCVhocsJg';
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $recaptcha = file_get_contents(
        $recaptcha_url
        . '?secret=' . $recaptcha_secret
        . '&response=' . $recaptcha_response
    );
    $recaptcha = json_decode($recaptcha, true);

    if (
        $recaptcha['success'] == 1
        AND $recaptcha['score'] >= 0.5
        AND $recaptcha['action'] == "login"
    ) {
        alert("reCAPTCHA verified successfully!" + "your score: " + $recaptcha['score']);
    } else {
        alert("reCAPTCHA verification failed! " + "your score: " + $recaptcha['score']);
    }

}
?>