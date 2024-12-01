<?php
// club-admin/view-queries.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'club_admin') {
    header("Location: ../public/login.php");
    exit();
}

require '../config/db.php';

// Fetch queries
$query = "SELECT * FROM queries WHERE club_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $_SESSION['club_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Respond to Queries</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>View and Respond to Queries</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Query</th>
                    <th>Response</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($query = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($query['member_name']) ?></td>
                        <td><?= htmlspecialchars($query['query_text']) ?></td>
                        <td><?= htmlspecialchars($query['response'] ?: 'Not yet responded') ?></td>
                        <td>
                            <?php if (!$query['response']) : ?>
                                <a href="respond-query.php?id=<?= $query['id'] ?>" class="btn btn-primary">Respond</a>
                            <?php else : ?>
                                <span class="badge badge-success">Responded</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
