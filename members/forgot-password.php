<?php

require_once '../includes/db.php';
require_once '../lib/phpmailer/src/PHPMailer.php';
require_once '../lib/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../includes/envLoader.php';

loadEnv('../includes/.env');

// Start session for CSRF protection and feedback messages
session_start();

// CSRF protection token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = $_ENV['SMTP_USERNAME'];
$password = $_ENV['SMTP_PASSWORD'];
$error = $success = ''; // Initialize feedback messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please try again.";
    } else {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            try {
                // Check if email exists in the database
                $query = "SELECT * FROM users WHERE email = :email";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Generate reset token and expiry
                    $resetToken = bin2hex(random_bytes(16));
                    $hashedToken = password_hash($resetToken, PASSWORD_DEFAULT);
                    $dateTime = new DateTime();
                    $dateTime->modify('+1 hour');
                    $resetTokenExpiry = $dateTime->format('Y-m-d H:i:s');

                    // Update the database with the reset token and expiry
                    $updateQuery = "UPDATE users SET reset_token = :reset_token, token_expires_at = :reset_token_expiry WHERE email = :email";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindValue(':reset_token', $hashedToken);
                    $updateStmt->bindValue(':reset_token_expiry', $resetTokenExpiry);
                    $updateStmt->bindValue(':email', $email, PDO::PARAM_STR);
                    $updateStmt->execute();

                    // Send the reset email
                    $resetLink = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/club-management-system/members/reset-password.php?token=" . $resetToken;
                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $username; // SMTP username
                        $mail->Password = $password; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom($username, 'Club Management System');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Password Reset Request';
                        $mail->Body = "Click <a href='" . htmlspecialchars($resetLink) . "'>here</a> to reset your password. This link will expire in 1 hour.";

                        $mail->send();
                        $success = "Password reset link has been sent to your email.";
                    } catch (Exception $e) {
                        $error = "Failed to send reset email. Please try again later.";
                        error_log("Mailer error: " . $mail->ErrorInfo); // Log error
                    }
                } else {
                    $success = "User not found";
                }
            } catch (PDOException $e) {
                $error = "An unexpected error occurred. Please try again later.";
                error_log("Database error: " . $e->getMessage()); // Log error
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../members/assets/members_styles.css">
</head>
<body>
    <div class="email-container">
        <div class="email-form">
            <h2>Forgot Password</h2>

            <!-- Display success or error messages -->
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php } elseif (!empty($success)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php } ?>

            <form method="POST" action="forgot-password.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Enter your email" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
            </form>
        </div>
    </div>
</body>
</html>
