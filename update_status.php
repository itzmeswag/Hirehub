<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("sql305.infinityfree.com", "if0_41614068", "Swagata1077", "if0_41614068_test");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = $_GET['status'] ?? 'Pending';

$allowed = ['Pending', 'Accepted', 'Rejected'];
if (!in_array($status, $allowed)) {
    die("Invalid status.");
}

$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>