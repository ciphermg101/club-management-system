<?php
// public/register-confirm.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Request Submitted</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/public_styles.css">
    <!-- Add FontAwesome for the icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <!-- Confirmation Icon -->
            <i class="fas fa-check-circle"></i>
            <h1>Registration Request Submitted</h1>
            <p>Thank you for your registration request. Our club admin will review and approve your request. You will be notified once your registration is confirmed.</p>
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>

    <script>
        document.querySelector('.btn-primary').addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            setTimeout(function () {
                window.location.href = 'index.php'; // Redirect after smooth scroll
            }, 500);
        });
    </script>
</body>
</html>
