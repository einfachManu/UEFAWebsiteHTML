// 1. Dein geheimer Schlüssel, nur auf dem Server gespeichert
$secret = 'DEIN_SECRET_KEY_HIER';

// 2. Das Token, das der Browser geschickt hat
$token  = $_POST['h-captcha-response'] ?? '';

// 3. Baue die POST-Anfrage an den hCaptcha-Server
$verifyUrl = 'https://hcaptcha.com/siteverify';
$data = [
  'secret'   => $secret,           // damit hCaptcha weiß, wer du bist
  'response' => $token,            // das vom Client erhaltene Token
  'remoteip' => $_SERVER['REMOTE_ADDR'],  // optional: Absender-IP
];

// 4. Sende die Anfrage und hole die Antwort
$options = [
  'http' => [
    'method'  => 'POST',
    'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
    'content' => http_build_query($data),
  ]
];
$context  = stream_context_create($options);
$response = file_get_contents($verifyUrl, false, $context);

// 5. JSON-Antwort parsen
$result = json_decode($response, true);
