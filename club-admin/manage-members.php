<?php
session_start();
if (!isset($_SESSION['club-admin']) || $_SESSION['club-admin']['role'] !== 'club-admin') {
    header("Location: ../club-admin/club-admin-login.php");
    exit();
}

require '../includes/db.php';

$searchTerm = ''; // Initialize the search term
$members = []; // Initialize members array

try {
    // Check if there is a search term
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = $_GET['search'];

        // Modify the query to include search functionality
        $query = "
            SELECT 
                cm.registration_number, 
                u.first_name, 
                u.last_name, 
                u.gender, 
                u.status, 
                u.phone_number, 
                u.current_year, 
                u.school,
                cm.membership_date
            FROM club_members cm
            INNER JOIN users u ON cm.registration_number = u.registration_number
            WHERE cm.club_id = :club_id
            AND (
                u.first_name LIKE :searchTerm OR 
                u.last_name LIKE :searchTerm OR 
                cm.registration_number LIKE :searchTerm
            )
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR); // Search term with wildcards
    } else {
        // Default query if no search term is provided
        $query = "
            SELECT 
                cm.registration_number, 
                u.first_name, 
                u.last_name, 
                u.gender, 
                u.status, 
                u.phone_number, 
                u.current_year, 
                u.school,
                cm.membership_date
            FROM club_members cm
            INNER JOIN users u ON cm.registration_number = u.registration_number
            WHERE cm.club_id = :club_id
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':club_id', $_SESSION['club-admin']['club_id'], PDO::PARAM_INT);
    }

    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>There was an error fetching the data. Please try again later.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members</title>
    <link rel="stylesheet" href="../public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">Manage Members</h1>
        </div>

        <!-- Search Bar -->
        <form class="mb-4" method="GET" action="manage-members.php">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by First Name, Last Name, or Registration Number" value="<?= htmlspecialchars($searchTerm) ?>">
                <button class="btn btn-search" type="submit">Search</button>
            </div>
        </form>

        <div class="table-responsive table-container bg-white p-3">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Registration Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Phone Number</th>
                        <th>Current Year</th>
                        <th>School</th>
                        <th>Membership Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= htmlspecialchars($member['registration_number']) ?></td>
                                <td><?= htmlspecialchars($member['first_name']) ?></td>
                                <td><?= htmlspecialchars($member['last_name']) ?></td>
                                <td><?= htmlspecialchars($member['gender']) ?></td>
                                <td>
                                    <span class="badge <?= ($member['status'] === 'active' || $member['status'] === 'approved') ? 'bg-success' : 'bg-danger' ?>">
                                        <?= htmlspecialchars($member['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($member['phone_number']) ?></td>
                                <td><?= htmlspecialchars($member['current_year']) ?></td>
                                <td><?= htmlspecialchars($member['school']) ?></td>
                                <td><?= date('d M Y', strtotime($member['membership_date'])) ?></td>
                                <td>
                                    <a href="edit-member.php?registration_number=<?= $member['registration_number'] ?>" class="btn btn-primary btn-sm-custom">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../public/assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
