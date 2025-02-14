<?php
// db_connection.php

$host = 'localhost'; // Database host
$dbname = 'student_loan_platform'; // Database name
$username = 'root'; // MySQL username
$password = ''; // MySQL password (default is empty for local setups)

try {
    // Create a new PDO instance to connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display the error message
    die("Connection failed: " . $e->getMessage());
}
?>
