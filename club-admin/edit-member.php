<?php
session_start();

if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}

require '../includes/db.php';

// Fetch member details using registration_number
if (isset($_GET['registration_number'])) {
    $registration_number = $_GET['registration_number'];
    
    $query = "SELECT * FROM club_members WHERE registration_number = :registration_number AND club_id = :club_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':registration_number', $registration_number, PDO::PARAM_STR); // Use string for registration_number
    $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$member) {
        // Handle case where member is not found
        header("Location: manage-members.php");
        exit();
    }
} else {
    header("Location: manage-members.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    // Update member status in club_members table
    $update_query = "UPDATE club_members SET status = :status WHERE registration_number = :registration_number";
    $update_stmt = $conn->prepare($update_query);
    
    // Binding parameters to the prepared statement
    $update_stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $update_stmt->bindParam(':registration_number', $registration_number, PDO::PARAM_STR); // Use registration_number for the update
    
    // Execute the club_members status update
    if ($update_stmt->execute()) {
        // Now, update the status in the users table
        $update_user_query = "UPDATE users SET status = :status WHERE registration_number = :registration_number";
        $update_user_stmt = $conn->prepare($update_user_query);
        
        // Binding parameters to the users table update
        $update_user_stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $update_user_stmt->bindParam(':registration_number', $registration_number, PDO::PARAM_STR); // Use registration_number for the update
        
        if ($update_user_stmt->execute()) {
            $message = "Member status updated successfully !";
        } else {
            $message = "Failed to update member status in the users table.";
        }
    } else {
        $message = "Failed to update member status in the club_members table.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member Status</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Member Status</h1>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="approved" <?= $member['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="suspended" <?= $member['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>

        <br>
        <a href="manage-members.php" class="btn btn-secondary">Back to Members</a>
    </div>
</body>
</html>
