<?php


$conn = new mysqli("sql305.infinityfree.com", "if0_41614068", "Swagata1077", "if0_41614068_test");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $stmt = $conn->prepare("INSERT INTO responses (name, email, message) VALUES (?, ?, ?)");

    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<h2 style='color:green; text-align:center;'>Message Sent Successfully ✅</h2>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>