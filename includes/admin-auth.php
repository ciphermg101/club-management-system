<?php
session_start();  // Ensure the session is started

function rootLogin($username, $password, $conn) {
    // Fetch the user record from the database using the username
    $stmt = $conn->prepare("SELECT * FROM root_admin WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If a user is found and the password matches the hashed password in the database
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['root-admin'] = $user;  // Store the user session
        return true;  // Successful login
    }

    return false;  // Invalid login
}

function leadLogin($registration_number, $password, $conn) {
    // Check if the registration number exists in the clubs table
    $stmt = $conn->prepare("SELECT * FROM clubs WHERE admin_reg_num = ?");
    $stmt->execute([$registration_number]);
    $club = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no matching club is found, return false
    if (!$club) {
        return false;
    }

    // Fetch the user record from the users table using the registration number
    $stmt = $conn->prepare("SELECT * FROM users WHERE registration_number = ?");
    $stmt->execute([$registration_number]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password from the users table
    if ($user && password_verify($password, $user['password'])) {
        // Store only necessary data in the session
        $_SESSION['club-admin'] = [
            'id' => $user['id'],
            'registration_number' => $user['registration_number'],
            'role' => 'club-admin',
            'club_id' => $club['id'] 
        ];
        return true;  // Successful login
    }

    return false;  // Invalid login
}

function logout() {
    session_start();  // Ensure session is started before destroying it
    session_destroy();  // Destroy the session
    header("Location: ../public/login.php");
    exit; 
}
?>
