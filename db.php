<?php
$conn = new mysqli("sql305.infinityfree.com", "if0_41614068", "Swagata1077", "if0_41614068_test");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>