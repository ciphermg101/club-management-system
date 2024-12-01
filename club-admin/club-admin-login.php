<?php

include('../includes/db.php');
include('../includes/admin-auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registration_number = $_POST['registration_number'];
    $password = $_POST['password'];

    // Check if the login credentials match the club admin
    if (leadLogin($registration_number, $password, $conn)) {
        // Ensure the user is a club admin
        if (isset($_SESSION['club-admin']) && $_SESSION['club-admin']['role'] === 'club-admin') {
            header("Location: index.php"); 
            exit;
        } else {
            $error = "You are not authorized to access this page.";
        }
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
    <title>Club Admin Login</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Club Admin Login</h2>
            
            <!-- Display error message if credentials are incorrect -->
            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            
            <form method="POST" action="club-admin-login.php">
                <input type="text" name="registration_number" placeholder="Registration Number" required class="custom-form-control">
                <input type="password" name="password" placeholder="Password" required class="custom-form-control">
                <button type="submit" class="custom-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
