<?php
include('../includes/db.php');
include('../includes/auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (login($email, $password, $conn)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2 class="text-center">Login</h2>
            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required class="form-control">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>

    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
