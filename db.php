<?php
$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
