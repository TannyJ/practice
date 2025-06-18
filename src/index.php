
<?php
require_once 'functions.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            if (sendVerificationEmail($email, $code)) {
                $_SESSION['current_email'] = $email;
                $message = 'Verification code sent to your email.';
            } else {
                $message = 'Failed to send verification code.';
            }
        } else {
            $message = 'Invalid email format.';
        }
    } elseif (isset($_POST['verification_code'])) {
        $email = $_SESSION['current_email'] ?? '';
        $code = trim($_POST['verification_code']);
        if ($email && verifyCode($email, $code)) {
            registerEmail($email);
            $message = 'Email verified and registered successfully.';
        } else {
            $message = 'Invalid verification code.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
    <h1>Subscribe to XKCD Comics</h1>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <button id="submit-email">Submit</button>
    </form>

    <form method="POST">
        <label for="verification_code">Verification Code:</label>
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification">Verify</button>
    </form>

    <p><?= htmlspecialchars($message) ?></p>
</body>
</html>
