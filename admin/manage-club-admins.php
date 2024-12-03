<?php
session_start();
if (!isset($_SESSION['root-admin']) || $_SESSION['root-admin']['role'] !== 'root-admin') {
    header("Location: ../admin/root-login.php");
    exit();
}

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['new_role'])) {
    try {
        // Update the role of the user in the users table
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$_POST['new_role'], $_POST['user_id']]);
        $message = "Role updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating role: " . $e->getMessage();
    }
}

try {
    // Fetch users whose registration numbers exist in both clubs and users tables
    $stmt = $conn->prepare("
        SELECT u.*, c.name AS club_name 
        FROM users u
        INNER JOIN clubs c ON u.registration_number = c.admin_reg_num
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Club Admins</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/admin_styles.css">
</head>
<body>
    <div class="container">
        <h1>Club Admins</h1>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Registration Number</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Club</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['registration_number']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <select name="new_role" class="form-select">
                                    <option value="club-admin" <?= $row['role'] === 'club-admin' ? 'selected' : '' ?>>Club Admin</option>
                                    <option value="member" <?= $row['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mt-1">Update</button>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($row['club_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
