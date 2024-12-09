<?php
session_start();
if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}

require '../includes/db.php';

// Fetch all pending registration requests for the logged-in club admin's club
try {
    $query = "SELECT * FROM registration_requests WHERE club_id = :club_id AND status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle the approval or rejection process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $request_id = $_POST['approve_id'];
        $status = 'approved';
    } elseif (isset($_POST['reject_id'])) {
        $request_id = $_POST['reject_id'];
        $status = 'rejected';
    }

    try {
        // Start a transaction to ensure both tables are updated together
        $conn->beginTransaction();

        // Update the registration request status
        $query_update_request = "UPDATE registration_requests SET status = :status WHERE id = :id";
        $stmt_update_request = $conn->prepare($query_update_request);
        $stmt_update_request->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt_update_request->bindParam(':id', $request_id, PDO::PARAM_INT);
        $stmt_update_request->execute();

        // Get the registration_number to update the user status if approved
        if ($status === 'approved') {
            $query_get_request = "SELECT registration_number FROM registration_requests WHERE id = :id";
            $stmt_get_request = $conn->prepare($query_get_request);
            $stmt_get_request->bindParam(':id', $request_id, PDO::PARAM_INT);
            $stmt_get_request->execute();
            $request = $stmt_get_request->fetch(PDO::FETCH_ASSOC);

            // Debugging: Check if the registration_number is retrieved correctly
            if (!$request) {
                die("No registration number found for the request ID: $request_id");
            }

            // Update the user's status to 'approved'
            $query_update_user = "UPDATE users SET status = 'approved' WHERE registration_number = :registration_number";
            $stmt_update_user = $conn->prepare($query_update_user);
            $stmt_update_user->bindParam(':registration_number', $request['registration_number'], PDO::PARAM_STR);
            $stmt_update_user->execute();

            // Debugging: Check if the user status update was successful
            if ($stmt_update_user->rowCount() === 0) {
                die("User status not updated. Please check the registration_number.");
            }

            // Retrieve registration_number and club_id from the registration_requests table
            $query_get_user_and_club = "SELECT registration_number, club_id FROM registration_requests WHERE id = :id";
            $stmt_get_user_and_club = $conn->prepare($query_get_user_and_club);
            $stmt_get_user_and_club->bindParam(':id', $request_id, PDO::PARAM_INT);
            $stmt_get_user_and_club->execute();
            $request_details = $stmt_get_user_and_club->fetch(PDO::FETCH_ASSOC);

            if (!$request_details) {
                die("Error: Registration request details not found.");
            }

            // Insert the new club member record into the club_members table
            $query_insert_member = "INSERT INTO club_members (registration_number, club_id, membership_date, status) 
                                    VALUES (:registration_number, :club_id, NOW(), 'active')";
            $stmt_insert_member = $conn->prepare($query_insert_member);
            $stmt_insert_member->bindParam(':registration_number', $request_details['registration_number'], PDO::PARAM_STR);
            $stmt_insert_member->bindParam(':club_id', $request_details['club_id'], PDO::PARAM_INT);

            if ($stmt_insert_member->execute()) {
                echo "New club member added successfully.";
            } else {
                echo "Error: Could not add new club member.";
            }

            // Delete the approved registration request from the table
            $query_delete_request = "DELETE FROM registration_requests WHERE id = :id";
            $stmt_delete_request = $conn->prepare($query_delete_request);
            $stmt_delete_request->bindParam(':id', $request_id, PDO::PARAM_INT);
            $stmt_delete_request->execute();
        
        
        } else {
                // If rejected, update the registration request status to 'rejected' (do not delete)
                $query_update_request = "UPDATE registration_requests SET status = 'rejected' WHERE id = :id";
                $stmt_update_request = $conn->prepare($query_update_request);
                $stmt_update_request->bindParam(':id', $request_id, PDO::PARAM_INT);
                $stmt_update_request->execute();
        }

        // Commit the transaction
        $conn->commit();

        // Redirect back to the page with a success message
        header("Location: view-registration-requests.php?message=Registration request $status successfully.");
        exit();

    } catch (PDOException $e) {
        // If an error occurs, rollback the transaction
        $conn->rollBack();
        error_log("Error: " . $e->getMessage());
        header("Location: view-registration-requests.php?error=An unexpected error occurred.");
        exit();        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Requests</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Pending Registration Requests</h1>

        <?php if (empty($requests)) : ?>
            <div class="alert alert-info mt-3">No pending registration requests at the moment.</div>
        <?php else : ?>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name']) ?></td>
                            <td><?= htmlspecialchars($row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <form action="view-registration-requests.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                                    <button class="btn-success">Approve</button>
                                </form>
                                <form action="view-registration-requests.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="reject_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div class="btn">
            <a href="index.php" class="btn btn-secondary btn-title">Back</a>
        </div>
    </div>
</body>
</html>
