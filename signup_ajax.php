<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? 'user');

if ($name === '' || $email === '' || $password === '' || $role === '') {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists."]);
    exit();
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Signup successful."]);
} else {
    echo json_encode(["status" => "error", "message" => "Signup failed."]);
}

$stmt->close();
$check->close();
$conn->close();
?>
