<?php

session_start();
require '../includes/db.php'; 

// Fetch all events along with club names using a JOIN query
$query = "
    SELECT events.*, clubs.name 
    FROM events
    INNER JOIN clubs ON events.club_id = clubs.id
";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Club Events</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/public_styles.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4">All Upcoming Club Events</h1>
        
        <table class="table mt-4 table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Club</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)) : ?>
                    <?php foreach ($events as $row) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['event_date']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center">No events available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

