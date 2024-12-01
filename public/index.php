<?php
// public/index.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Club</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <div class="hero-section">
        <h1>Welcome to Our Club</h1>
        <p>Join us for exciting events, activities, and a great community!</p>
        <a href="register.php" class="btn btn-custom btn-custom-primary">Register for the Club</a>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Club Information</h2>
                        <p>Learn more about the club's purpose, mission, and vision.</p>
                        <a href="club-info.php" class="btn btn-custom btn-custom-secondary">View Club Info</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Upcoming Events</h2>
                        <p>Check out our upcoming events and don't miss out on the fun!</p>
                        <a href="events.php" class="btn btn-custom btn-custom-success">View Events</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">Join Us Today</h2>
                        <p>Become part of a community that values growth, friendship, and fun!</p>
                        <a href="register.php" class="btn btn-custom btn-custom-primary">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© 2024 Our Club. All Rights Reserved.</p>
        <p>Connect with us on <a href="https://facebook.com" target="_blank">Facebook</a> | <a href="https://twitter.com" target="_blank">Twitter</a></p>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
