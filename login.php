<?php
require 'db.php';

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (!$inputData) {
        echo json_encode(["status" => "false", "message" => "Invalid input."]);
        exit;
    }

    $email = sanitizeInput($inputData['email'] ?? '');
    $password = sanitizeInput($inputData['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["status" => "false", "message" => "Email and password are required."]);
        exit;
    }

    $query = "SELECT id, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            echo json_encode(["status" => "true", "message" => "Login successful!"]);
        } else {
            echo json_encode(["status" => "false", "message" => "Incorrect password."]);
        }
    } else {
        echo json_encode(["status" => "false", "message" => "No user found with this email."]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
