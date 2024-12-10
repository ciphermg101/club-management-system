<?php
session_start();

// Verify session and role
if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}

// Include required files
require '../includes/db.php';
require '../lib/phpmailer/src/PHPMailer.php';
require '../lib/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../includes/envLoader.php';
loadEnv('../includes/.env');

// Fetch pending registration requests
try {
    $query = "SELECT * FROM registration_requests WHERE club_id = :club_id AND status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = null;
    $request_id = null;

    if (isset($_POST['approve_id'])) {
        $request_id = $_POST['approve_id'];
        $status = 'approved';
    } elseif (isset($_POST['reject_id'])) {
        $request_id = $_POST['reject_id'];
        $status = 'rejected';
    }

    if ($request_id && $status) {
        try {
            $conn->beginTransaction();

            // Update registration request status
            $query_update_request = "UPDATE registration_requests SET status = :status WHERE id = :id";
            $stmt_update_request = $conn->prepare($query_update_request);
            $stmt_update_request->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt_update_request->bindParam(':id', $request_id, PDO::PARAM_INT);
            $stmt_update_request->execute();

            if ($status === 'approved') {
                // Fetch request details
                $query_get_request = "SELECT registration_number, email, first_name, club_id FROM registration_requests WHERE id = :id";
                $stmt_get_request = $conn->prepare($query_get_request);
                $stmt_get_request->bindParam(':id', $request_id, PDO::PARAM_INT);
                $stmt_get_request->execute();
                $request = $stmt_get_request->fetch(PDO::FETCH_ASSOC);

                if (!$request) {
                    throw new Exception("Request not found.");
                }

                // Approve user
                $query_update_user = "UPDATE users SET status = 'approved' WHERE registration_number = :registration_number";
                $stmt_update_user = $conn->prepare($query_update_user);
                $stmt_update_user->bindParam(':registration_number', $request['registration_number'], PDO::PARAM_STR);
                $stmt_update_user->execute();

                // Add to club members
                $query_insert_member = "INSERT INTO club_members (registration_number, club_id, membership_date, status) 
                                        VALUES (:registration_number, :club_id, NOW(), 'active')";
                $stmt_insert_member = $conn->prepare($query_insert_member);
                $stmt_insert_member->bindParam(':registration_number', $request['registration_number'], PDO::PARAM_STR);
                $stmt_insert_member->bindParam(':club_id', $request['club_id'], PDO::PARAM_INT);
                $stmt_insert_member->execute();

                // Send approval email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['SMTP_USERNAME'];
                    $mail->Password = $_ENV['SMTP_PASSWORD'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($_ENV['SMTP_USERNAME'], 'Club Management System');
                    $mail->addAddress($request['email']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Club Membership Approved';
                    $mail->Body = "Dear {$request['first_name']},<br>Your membership to the club has been approved. Welcome!<br><br>Best Regards,<br>Club Management Team";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Error sending email: " . $mail->ErrorInfo);
                }
            }

            $conn->commit();
            header("Location: view-registration-requests.php?message=Request $status successfully.");
        } catch (PDOException | Exception $e) {
            $conn->rollBack();
            error_log("Error: " . $e->getMessage());
            header("Location: view-registration-requests.php?error=An error occurred.");
        }
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
