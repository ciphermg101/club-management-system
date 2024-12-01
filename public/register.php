<?php

session_start();

require '../includes/db.php';  // Including the PDO database connection

// Fetch all clubs from the database
$query = "SELECT id, name FROM clubs";
$stmt = $conn->prepare($query);
$stmt->execute();
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecting form data
    $registration_number = $_POST['registration_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_ARGON2ID);  // Using Argon2ID for password hashing
    $club_name = $_POST['club_name'];
    $phone_number = $_POST['phone_number'];
    $profile_picture = $_FILES['profile_picture']['name'];
    $current_year = $_POST['current_year'];
    $school = $_POST['school'];

    // File upload handling and validation
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_file_size = 5000000;  // 5MB

    $profile_picture_path = '';

    // Check if a file is uploaded
    if (!empty($_FILES['profile_picture']['name'])) {
        // Check for valid file type
        if (!in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            $message = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($_FILES['profile_picture']['size'] > $max_file_size) {
            $message = "File is too large. Maximum size is 5MB.";
        } else {
            // Rename file to ensure uniqueness
            $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_file_name = uniqid('profile_', true) . '.' . $file_ext;

            // Define the full target directory path
            $target_dir = "C:/xampp/htdocs/club-management-system/public/assets/uploads/";

            // Ensure the directory exists
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);  // Create the directory if it doesn't exist
            }

            // Set the target file path with the new unique file name
            $profile_picture_path = $target_dir . $new_file_name;

            // Move the uploaded file from temporary location to the target directory
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path)) {
                $message = "Error uploading file. Please try again.";
            }
        }
    }

    // Fetch the club_id from the clubs table using the club_name
    $query_club = "SELECT id FROM clubs WHERE name = :club_name LIMIT 1";
    $stmt_club = $conn->prepare($query_club);
    $stmt_club->bindParam(':club_name', $club_name, PDO::PARAM_STR);
    $stmt_club->execute();
    $club = $stmt_club->fetch(PDO::FETCH_ASSOC);
    
    if ($club) {
        $club_id = $club['id'];  // Get the club_id

        // Prepare SQL query using PDO to insert into users table
        $query_users = "INSERT INTO users (registration_number, first_name, last_name, gender, email, password, role, club_name, phone_number, profile_picture, current_year, school, created_at) 
                        VALUES (:registration_number, :first_name, :last_name, :gender, :email, :password, 'member', :club_name, :phone_number, :profile_picture, :current_year, :school, NOW())";
        
        try {
            // Prepare the statement for users table
            $stmt_users = $conn->prepare($query_users);
            
            // Bind parameters
            $stmt_users->bindParam(':registration_number', $registration_number, PDO::PARAM_STR);
            $stmt_users->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt_users->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt_users->bindParam(':gender', $gender, PDO::PARAM_STR);
            $stmt_users->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt_users->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt_users->bindParam(':club_name', $club_name, PDO::PARAM_STR);
            $stmt_users->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
            $stmt_users->bindParam(':profile_picture', $profile_picture_path, PDO::PARAM_STR);
            $stmt_users->bindParam(':current_year', $current_year, PDO::PARAM_STR);
            $stmt_users->bindParam(':school', $school, PDO::PARAM_STR);
            
            // Execute the query
            if ($stmt_users->execute()) {
                
                // Now insert the required fields into the registration_requests table
                $query_requests = "INSERT INTO registration_requests (registration_number, first_name, last_name, email, club_id, club_name, status, created_at) 
                                   VALUES (:registration_number, :first_name, :last_name, :email, :club_id, :club_name, 'pending', NOW())";

                // Prepare the statement for registration_requests table
                $stmt_requests = $conn->prepare($query_requests);

                // Bind parameters for registration_requests table
                $stmt_requests->bindParam(':registration_number', $registration_number, PDO::PARAM_STR);
                $stmt_requests->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt_requests->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt_requests->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt_requests->bindParam(':club_id', $club_id, PDO::PARAM_INT);
                $stmt_requests->bindParam(':club_name', $club_name, PDO::PARAM_STR);

                // Execute the query for registration_requests table
                if ($stmt_requests->execute()) {
                    header("Location: register-confirm.php");
                    exit();
                } else {
                    $message = "Failed to submit registration request. Please try again later.";
                }
            } else {
                $message = "Registration failed. Please try again later.";
            }
        } catch (PDOException $e) {
            // Handle errors if the query fails
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Invalid club selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for the Club</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section text-white text-center py-5 mb-4" style="background-color: #007bff;">
        <h1>Register for Our Club</h1>
        <p>Become a member and be part of an exciting community!</p>
    </div>

    <!-- Main Registration Form -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg p-4">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Sign Up</h2>

                        <?php if (isset($message)) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="registration_number">Registration Number</label>
                                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="" disabled selected>Select your gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="club_name">Club</label>
                                <select class="form-control" id="club_name" name="club_name" required>
                                    <option value="" disabled selected>Select your club</option>
                                    <?php foreach ($clubs as $club): ?>
                                        <option value="<?= htmlspecialchars($club['name']) ?>"><?= htmlspecialchars($club['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                            <div class="form-group mb-3">
                                <label for="current_year">Current Year</label>
                                <select class="form-control" id="current_year" name="current_year" required>
                                    <option value="" disabled selected>Select your current year</option>
                                    <option value="First Year">First Year</option>
                                    <option value="Second Year">Second Year</option>
                                    <option value="Third Year">Third Year</option>
                                    <option value="Fourth Year">Fourth Year</option>
                                    <option value="Post Graduate">Post Graduate</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="school">School</label>
                                <select class="form-control" id="school" name="school" required>
                                    <option value="" disabled selected>Select your school</option>
                                    <option value="School of Computing and Informatics (SCI)">School of Computing and Informatics (SCI)</option>
                                    <option value="School of Business and Economics (SBE)">School of Business and Economics (SBE)</option>
                                    <option value="School of Agriculture and Food Science (SAFS)">School of Agriculture and Food Science (SAFS)</option>
                                    <option value="School of Education (SED)">School of Education (SED)</option>
                                    <option value="School of Engineering and Architecture (SEA)">School of Engineering and Architecture (SEA)</option>
                                    <option value="School of Health Sciences (SHS)">School of Health Sciences (SHS)</option>
                                    <option value="School of Nursing (SON)">School of Nursing (SON)</option>
                                    <option value="School of Pure and Applied Sciences (SPAS)">School of Pure and Applied Sciences (SPAS)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
