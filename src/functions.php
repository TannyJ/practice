
<?php

// Store temporary verification codes in session (for demo; in production use persistent storage)
session_start();

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';

    if (file_exists($file)) {
        $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filtered = array_filter($emails, fn($e) => trim($e) !== trim($email));
        file_put_contents($file, implode(PHP_EOL, $filtered) . PHP_EOL);
    }
}

function sendVerificationEmail($email, $code) {
    $_SESSION['verification_codes'][$email] = $code;

    $subject = 'Your Verification Code';
    $message = "<p>Your verification code is: <strong>$code</strong></p>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    return mail($email, $subject, $message, $headers);
}

function verifyCode($email, $code) {
    return isset($_SESSION['verification_codes'][$email]) && $_SESSION['verification_codes'][$email] === $code;
}

function fetchAndFormatXKCDData() {
    $randomComicId = rand(1, 2800); // Adjust range as needed
    $url = "https://xkcd.com/$randomComicId/info.0.json";

    $json = @file_get_contents($url);
    if ($json === false) return false;

    $data = json_decode($json, true);

    if (!$data || !isset($data['img'])) return false;

    $imgUrl = $data['img'];
    $html = "<h2>XKCD Comic</h2>";
    $html .= "<img src=\"$imgUrl\" alt=\"XKCD Comic\">";
    $html .= "<p><a href=\"http://yourdomain.com/src/unsubscribe.php\" id=\"unsubscribe-button\">Unsubscribe</a></p>";

    return $html;
}

function sendXKCDUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $comicHTML = fetchAndFormatXKCDData();

    if (!$comicHTML) return;

    $subject = 'Your XKCD Comic';
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        mail($email, $subject, $comicHTML, $headers);
    }
}

