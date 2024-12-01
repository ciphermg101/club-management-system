<?php

session_start();
if (!isset($_SESSION['root-admin']) || $_SESSION['root-admin']['role'] !== 'root-admin') {
    header("Location: ../admin/root-login.php");
    exit();
}

require '../includes/db.php';

$club_id = $_GET['id'] ?? null;
if (!$club_id) {
    header("Location: manage-clubs.php");
    exit();
}

try {
    // Fetch existing club details using PDO
    $query = "SELECT * FROM clubs WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);
    $stmt->execute();
    $club = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$club) {
        header("Location: manage-clubs.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $club_name = $_POST['club_name'];
        $admin_name = $_POST['admin_name'];
        $admin_reg_num = $_POST['admin_reg_num'];
        $admin_contact = $_POST['admin_contact'];

        $updateQuery = "UPDATE clubs SET name = :name, admin_name = :admin_name, admin_reg_num = :admin_reg_num, admin_contact = :admin_contact WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':name', $club_name);
        $updateStmt->bindParam(':admin_name', $admin_name);
        $updateStmt->bindParam(':admin_reg_num', $admin_reg_num);
        $updateStmt->bindParam(':admin_contact', $admin_contact);
        $updateStmt->bindParam(':id', $club_id, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            $message = "Club updated successfully!";
        } else {
            $message = "Failed to update the club!";
        }
    }
} catch (PDOException $e) {
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Club</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Club</h1>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="club_name">Club Name</label>
                <input type="text" class="form-control" id="club_name" name="club_name" value="<?= htmlspecialchars($club['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_name">Admin Name</label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?= htmlspecialchars($club['admin_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_reg_num">Admin Registration Number</label>
                <input type="text" class="form-control" id="admin_reg_num" name="admin_reg_num" value="<?= htmlspecialchars($club['admin_reg_num']) ?>" required>
            </div>
            <div class="form-group">
                <label for="admin_contact">Admin Contact (Phone)</label>
                <input type="text" class="form-control" id="admin_contact" name="admin_contact" value="<?= htmlspecialchars($club['admin_contact']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Club</button>
        </form>

        <br>
        <a href="manage-clubs.php" class="btn btn-secondary">Back to Manage Clubs</a>
    </div>
</body>
</html>
