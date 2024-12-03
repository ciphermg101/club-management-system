<?php
session_start();

include('../includes/db.php');

// Authentication check
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details from the session
$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated information from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate the data
    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone_number)) {
        $error = "All fields except password and confirm password are required.";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Update personal information (first name, last name, email, and phone number)
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute([$first_name, $last_name, $email, $phone_number, $userId])) {
            $success = "Your profile has been updated successfully.";
        } else {
            $error = "An error occurred while updating your profile.";
        }

        // If password is provided, hash it using Argon2 and update the password
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_ARGON2I); // Hash password with Argon2
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$hashedPassword, $userId])) {
                $success = "Your password has been updated successfully.";
            } else {
                $error = "An error occurred while updating your password.";
            }
        }
    }
}

// Fetch user details for the profile page
$sql = "SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Edit Personal Information</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/members_styles.css">
    <style>
        /* Sidebar and general styles */
        .sidebar {
            margin-top: 340px;
            background-color: #3C4F63FF;
            color: white;
            padding: 30px 20px;
            min-height: 100vh;
            width: 220px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .main-content {
            margin-top: 340px;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .navbar {
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-link {
            font-size: 16px;
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
                        <a class="nav-link text-white" href="profile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="settings.php">Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="notifications.php">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="tasks.php">My Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="?logout=true">Logout</a>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <main class="col-md-9 p-4 main-content">
                <header class="mb-4">
                    <h2>Edit Profile</h2>
                </header>

                <!-- Display success or error messages -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Form -->
                <form action="profile.php" method="POST">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp">
                        <small id="passwordHelp" class="form-text text-muted">Leave blank if you don't want to change your password.</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
