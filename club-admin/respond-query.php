<?php
// club-admin/respond-query.php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'club_admin') {
    header("Location: ../public/login.php");
    exit();
}

require '../config/db.php';

// Check if query ID is provided
if (!isset($_GET['id'])) {
    header("Location: view-queries.php");
    exit();
}

$query_id = $_GET['id'];

// Fetch query details
$query = "SELECT * FROM queries WHERE id = ? AND club_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('ii', $query_id, $_SESSION['club_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Query not found or doesn't belong to this club
    header("Location: view-queries.php");
    exit();
}

$query_data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response_text = $_POST['response_text'];

    // Update query with the response
    $update_query = "UPDATE queries SET response = ?, status = 'responded' WHERE id = ?";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bind_param('si', $response_text, $query_id);

    if ($update_stmt->execute()) {
        $message = "Response submitted successfully!";
    } else {
        $message = "Failed to submit response.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Query</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Respond to Query</h1>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="query-details">
            <h3>Query Details</h3>
            <p><strong>Member Name:</strong> <?= htmlspecialchars($query_data['member_name']) ?></p>
            <p><strong>Query:</strong> <?= nl2br(htmlspecialchars($query_data['query_text'])) ?></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="response_text">Your Response</label>
                <textarea class="form-control" id="response_text" name="response_text" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Response</button>
        </form>

        <br>
        <a href="view-queries.php" class="btn btn-secondary">Back to Queries</a>
    </div>
</body>
</html>
