<?php

session_start();
if (!isset($_SESSION['root-admin']) || $_SESSION['root-admin']['role'] !== 'root-admin') {
    header("Location: ../admin/root-login.php");
    exit();
}

require '../includes/db.php';

// Fetch all clubs
$query = "SELECT * FROM clubs";
$clubs = $conn->query($query);

// Handle add/edit/delete club logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new club
    if (isset($_POST['add_club'])) {
        $club_name = $_POST['club_name'];
        $admin_name = $_POST['admin_name'];
        $admin_reg_num = $_POST['admin_reg_num'];
        $admin_contact = $_POST['admin_contact'];
        
        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO clubs (name, admin_name, admin_reg_num, admin_contact) VALUES (:name, :admin_name, :admin_reg_num, :admin_contact)");
        $stmt->bindParam(':name', $club_name);
        $stmt->bindParam(':admin_name', $admin_name);
        $stmt->bindParam(':admin_reg_num', $admin_reg_num);
        $stmt->bindParam(':admin_contact', $admin_contact);

        if ($stmt->execute()) {
            header("Location: manage-clubs.php");
            exit();
        } else {
            $error = "Failed to add club.";
        }
    }
}

// Delete club logic
if (isset($_GET['delete'])) {
    $club_id = $_GET['delete'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM clubs WHERE id = :id");
    $stmt->bindParam(':id', $club_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: manage-clubs.php");
        exit();
    } else {
        $error = "Failed to delete club.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clubs</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin_styles.css">
</head>
<body>
    <div class="container">
        <h1>Manage Clubs</h1>

        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Club Name</th>
                    <th>Club Lead</th>
                    <th>Lead Registration Number</th>
                    <th>Lead Contact</th>                
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $clubs->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['admin_name']) ?></td>
                        <td><?= htmlspecialchars($row['admin_reg_num']) ?></td>
                        <td><?= htmlspecialchars($row['admin_contact']) ?></td>
                        <td>
                            <a href="edit-club.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this club?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add Club Form -->
        <h2>Add New Club</h2>
        <form method="POST" action="manage-clubs.php">
            <div class="form-group">
                <label for="club_name">Club Name</label>
                <input type="text" id="club_name" name="club_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_name">Admin Name</label>
                <input type="text" id="admin_name" name="admin_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_reg_num">Admin Registration Number</label>
                <input type="text" id="admin_reg_num" name="admin_reg_num" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_contact">Admin Contact (Phone)</label>
                <input type="text" id="admin_contact" name="admin_contact" class="form-control" required>
            </div>   
            <button type="submit" name="add_club" class="btn btn-success">Add Club</button>
        </form>
        <div class="btn">
            <a href="index.php" class="btn btn-secondary btn-title">Back</a>
        </div>
    </div>
</body>
</html>
