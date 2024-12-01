<?php
session_start();

// Authentication check
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Retrieve the user details from the session
$user = $_SESSION['user'];

// Logout functionality
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    session_destroy(); // End the session
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .sidebar {
            background-color: #3C4F63FF;
            color: white;
            padding: 30px 20px;
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            width: 220px;
        }

        .main-content {
            padding: 30px;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: bold;
        }

        .navbar {
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
        }

        .card:hover {
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            font-size: 16px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-3 sidebar">
                <h3 class="text-center">Welcome, <?php echo htmlspecialchars($user['first_name']); ?></h3>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="settings.php"><i class="fas fa-cogs"></i> Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="tasks.php"><i class="fas fa-tasks"></i> My Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="col-md-9 p-4 main-content">
                <header class="mb-4">
                    <h2 class="mb-0">Dashboard</h2>
                    <p class="text-muted">Stay updated with your activities and progress.</p>
                </header>

                <section class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Upcoming Events</h5>
                                <p class="card-text">Check out the latest events happening in your community.</p>
                                <a href="events.php" class="btn btn-primary">View Events</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Profile Overview</h5>
                                <p class="card-text">Update your personal information and preferences.</p>
                                <a href="profile.php" class="btn btn-primary">Manage Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tasks</h5>
                                <p class="card-text">Track and manage your pending tasks.</p>
                                <a href="tasks.php" class="btn btn-primary">View Tasks</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Notifications</h5>
                                <p class="card-text">Check your latest notifications.</p>
                                <a href="notifications.php" class="btn btn-primary">View Notifications</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Settings</h5>
                                <p class="card-text">Customize your account settings and preferences.</p>
                                <a href="settings.php" class="btn btn-primary">Go to Settings</a>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
