
<?php
require_once 'functions.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = $code;

            $subject = 'Confirm Un-subscription';
            $messageHtml = '<p>To confirm un-subscription, use this code: <strong>' . htmlspecialchars($code) . '</strong></p>';
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= 'From: no-reply@example.com' . "\r\n";

            if (mail($email, $subject, $messageHtml, $headers)) {
                $message = 'Confirmation code sent to your email.';
            } else {
                $message = 'Failed to send confirmation email.';
            }
        } else {
            $message = 'Invalid email format.';
        }
    } elseif (isset($_POST['verification_code'])) {
        $code = trim($_POST['verification_code']);
        $email = $_SESSION['unsubscribe_email'] ?? '';
        if ($email && $_SESSION['unsubscribe_code'] === $code) {
            unsubscribeEmail($email);
            $message = 'You have been unsubscribed successfully.';
        } else {
            $message = 'Invalid confirmation code.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe</title>
</head>
<body>
    <h1>Unsubscribe from XKCD Comics</h1>

    <form method="POST">
        <label for="unsubscribe_email">Email:</label>
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <form method="POST">
        <label for="verification_code">Confirmation Code:</label>
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification">Verify</button>
    </form>

    <p><?= htmlspecialchars($message) ?></p>
</body>
</html>
