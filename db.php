<?php
$host = 'localhost';
$username = 'root';
$password = 'Santosh208@'; // default for XAMPP
$database = 'final_project'; // ✅ wrap database name in quotes

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}
?>
