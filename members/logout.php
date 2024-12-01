<?php
// public/logout.php
session_start();
session_destroy(); // Destroy the session to log the user out
header("Location: login.php"); // Redirect back to login page
exit();
?>
