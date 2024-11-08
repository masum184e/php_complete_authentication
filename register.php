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

    $fullname = sanitizeInput($inputData['fullname'] ?? '');
    $email = sanitizeInput($inputData['email'] ?? '');
    $password = sanitizeInput($inputData['password'] ?? '');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (empty($fullname) || empty($email) || empty($password)) {
        echo json_encode(["status" => "false", "message" => "All fields are required."]);
        exit;
    }

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo json_encode(["status" => "false", "message" => "Email already exists."]);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit;
    }
    mysqli_stmt_close($stmt);

    $insertQuery = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $hashedPassword);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "true", "message" => "User registered successfully!"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Error: " . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
