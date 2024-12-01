<?php
// admin/index.php
session_start();
if (!isset($_SESSION['root-admin']) || $_SESSION['root-admin']['role'] !== 'root-admin') {
    header("Location: ../admin/root-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section text-white text-center py-5">
        <h1>Welcome, Root Admin</h1>
        <p class="lead">Manage your clubs and Club Admins seamlessly</p>
    </div>

    <!-- Main Dashboard -->
    <div class="container my-5">
        <div class="row justify-content-center"> 
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Clubs</h5>
                        <a href="manage-clubs.php" class="btn btn-primary btn-lg w-100">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manage Club Admins</h5>
                        <a href="manage-club-admins.php" class="btn btn-secondary btn-lg w-100">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Logout</h5>
                        <a href="../admin/logout.php" class="btn btn-danger btn-lg w-100">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
