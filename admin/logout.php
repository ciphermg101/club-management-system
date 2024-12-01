<?php

session_start();
session_destroy(); // Destroy the session to log the user out
header("Location: root-login.php"); // Redirect back to login page
exit();
?>
