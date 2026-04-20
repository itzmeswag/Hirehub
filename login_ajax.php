<?php
session_start();
header('Content-Type: application/json');



if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed."
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required."
    ]);
    exit();
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        echo json_encode([
            "status" => "success",
            "message" => "Login successful. Welcome, " . $user['name'] . "!",
            "role" => $user['role']
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Incorrect password."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "User not found."
    ]);
}

$stmt->close();
$conn->close();
?>
