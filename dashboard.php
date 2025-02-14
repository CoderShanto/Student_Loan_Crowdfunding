<?php
// dashboard.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
echo "<p>Welcome to your dashboard.</p>";
?>

<a href="logout.php">Logout</a>
