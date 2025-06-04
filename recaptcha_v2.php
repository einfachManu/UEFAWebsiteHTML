<?php
// Wir lassen hier das Formular erst einmal anzeigen.
// E-Mail/Passwort sind als POST-Felder gesetzt.
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
if (!$email || !$password) {
    // Falls man ohne „richtige“ Weiterleitung kommt, zurück zum normalen Login
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device‐width, initial‐scale=1.0" />
  <title>reCAPTCHA v2-Challenge</title>

  <!-- 7) reCAPTCHA v2 (Checkbox) einbinden -->
  <!-- Ersetze YOUR_V2_SITE_KEY durch deinen reCAPTCHA-v2-Site-Key -->
  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
</head>
<body>
  <div class="challenge-container">
    <h1>Bitte bestätige, dass du kein Roboter bist</h1>
    <p>Aus Sicherheitsgründen müssen wir dich jetzt um eine reCAPTCHA v2-Challenge bitten.</p>

    <form action="verify_v2.php" method="POST">
      <!-- E-Mail und Passwort verdeckt weiterleiten -->
      <input type="hidden" name="email"    value="<?= htmlspecialchars($email); ?>" />
      <input type="hidden" name="password" value="<?= htmlspecialchars($password); ?>" />

      <!-- 8) reCAPTCHA-v2-Widget (Checkbox) -->
      <div class="g-recaptcha" data-sitekey="6LehRVUrAAAAAF5Cx7779HLKrSsL1UoP2-i51-nI"></div>

      <br />
      <button type="submit">Jetzt reCAPTCHA v2 lösen und einloggen</button>
    </form>
  </div>
</body>
<script type="text/javascript">
  var onloadCallback = function() {
    alert("grecaptcha is ready!");
  };
</script>
</html>
