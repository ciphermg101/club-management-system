<?php

include('../includes/db.php');
include('../includes/admin-auth.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the login credentials match the root admin
    if (rootLogin($username, $password, $conn)) {
        // Ensure the user is a root admin
        if (isset($_SESSION['root-admin']) && $_SESSION['root-admin']['role'] === 'root-admin') {
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
    <title>Root Admin Login</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Root Admin Login</h2>
            
            <!-- Display error message if credentials are incorrect -->
            <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            
            <form method="POST" action="root-login.php">
                <input type="text" name="username" placeholder="Username" required class="form-control">
                <input type="password" name="password" placeholder="Password" required class="form-control">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
