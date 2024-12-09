<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Club</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/public_styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Our Club</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Join</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/root-login.php">Admin Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../club-admin/club-admin-login.php">Club Lead Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main content area -->
    <div class="content">
        <div class="hero-section">
            <h1>Welcome to the Innovation Club</h1>
            <p>Join us for exciting events, activities, and a vibrant community!</p>
            <a href="register.php" class="btn btn-lg btn-custom btn-custom-primary mt-3">Get Started</a>
        </div>

        <div class="container my-5">
            <h2 class="text-center section-title">What We Offer</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Club Information</h5>
                            <p class="card-text">Discover our mission, vision, and the amazing benefits of joining.</p>
                            <a href="club-info.php" class="btn btn-custom btn-custom-secondary">Learn More</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Upcoming Events</h5>
                            <p class="card-text">Stay updated with our latest events and gatherings.</p>
                            <a href="events.php" class="btn btn-custom btn-custom-success">View Events</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Join Us</h5>
                            <p class="card-text">Become a member today and be part of something special.</p>
                            <a href="register.php" class="btn btn-custom btn-custom-primary">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 Our Club. All Rights Reserved.</p>
        <p>
            Connect with us:
            <a href="https://facebook.com" target="_blank">Facebook</a> | 
            <a href="https://twitter.com" target="_blank">Twitter</a> | 
            <a href="https://instagram.com" target="_blank">Instagram</a>
        </p>
    </footer>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
