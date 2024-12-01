<?php
session_start();
if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Admin Dashboard</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/dashboard.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Club Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../club-admin/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container dashboard-container">
        <h1 class="text-center mb-4">Welcome, Club Admin</h1>
        <p class="text-center text-muted">Manage your club's members, events, and queries efficiently.</p>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Manage Members</h5>
                        <p class="card-text">Add, update, or remove club members.</p>
                        <a href="manage-members.php" class="btn btn-primary">Go to Members</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Club Registration Requests</h5>
                        <p class="card-text">Respond To Registration Requests</p>
                        <a href="view-registration-requests.php" class="btn btn-primary">Go to Registration requests</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Manage Events</h5>
                        <p class="card-text">Create and oversee club events.</p>
                        <a href="manage-events.php" class="btn btn-secondary">Go to Events</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">View Queries</h5>
                        <p class="card-text">Review and respond to member queries.</p>
                        <a href="view-queries.php" class="btn btn-success">Go to Queries</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
