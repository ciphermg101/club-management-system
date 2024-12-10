<?php

require_once '../includes/db.php';
require_once '../includes/envLoader.php';

loadEnv('../includes/.env');

// Start session for CSRF protection and feedback messages
session_start();

// CSRF protection token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $success = ''; // Initialize feedback messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = "Invalid request. Please try again.";
    } else {
        $resetToken = $_POST['token'] ?? '';
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate password
        if (strlen($newPassword) < 8) {
            $error = "Password must be at least 8 characters long.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match. Please try again.";
        } else {
            try {
                // Fetch token details from database
                $query = "SELECT * FROM users WHERE reset_token IS NOT NULL AND token_expires_at > NOW()";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify token
                if ($user && password_verify($resetToken, $user['reset_token'])) {
                    // Hash the new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update user's password
                    $updateQuery = "UPDATE users SET password = :password, reset_token = NULL, token_expires_at = NULL WHERE id = :id";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bindValue(':password', $hashedPassword);
                    $updateStmt->bindValue(':id', $user['id']);
                    $updateStmt->execute();

                    $success = "Your password has been updated successfully.";
                } else {
                    $error = "Invalid or expired token. Please request a new password reset.";
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
    <title>Reset Password</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../members/assets/members_styles.css">
</head>
<body>
    <div class="email-container">
        <div class="email-form">
            <h2>Reset Password</h2>

            <!-- Display success or error messages -->
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php } elseif (!empty($success)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php } ?>

            <?php if (empty($success)) { ?>
                <form method="POST" action="reset-password.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                    <div class="form-group">
                        <input type="password" name="password" placeholder="Enter your new password" required class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" placeholder="Confirm your new password" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>
