<?php

session_start();

if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}

require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_title = $_POST['event_title'];
    $event_date = $_POST['event_date'];
    $event_description = $_POST['event_description'];

    // Prepare the INSERT query using PDO
    $query = "INSERT INTO events (club_id, title, event_date, description) VALUES (:club_id, :title, :event_date, :description)";
    $stmt = $conn->prepare($query);
    
    // Bind parameters to the query
    $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
    $stmt->bindParam(':title', $event_title, PDO::PARAM_STR);
    $stmt->bindParam(':event_date', $event_date, PDO::PARAM_STR);
    $stmt->bindParam(':description', $event_description, PDO::PARAM_STR);

    // Execute the query and check success
    if ($stmt->execute()) {
        $message = "Event added successfully!";
    } else {
        $message = "Failed to add event!";
    }
}

// Fetch events using PDO
$query = "SELECT * FROM events WHERE club_id = :club_id";
$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Manage Club Events</h1>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="event_title">Event Title</label>
                <input type="text" class="form-control" id="event_title" name="event_title" required>
            </div>
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" required>
            </div>
            <div class="form-group">
                <label for="event_description">Event Description</label>
                <textarea class="form-control" id="event_description" name="event_description" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Event</button>
        </form>

        <br>

        <h2>Existing Events</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event) : ?>
                    <tr>
                        <td><?= htmlspecialchars($event['title']) ?></td>
                        <td><?= htmlspecialchars($event['event_date']) ?></td>
                        <td><?= htmlspecialchars($event['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
