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

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["status" => "false", "message" => "Email already exists."]);
        exit;
    }

    $insertQuery = "INSERT INTO users (fullname, email, password) VALUES ('$fullname', '$email', '$hashedPassword')";

    if (mysqli_query($conn, $insertQuery)) {
        echo json_encode(["status" => "true", "message" => "User registered successfully!"]);
    } else {
        echo json_encode(["status" => "false", "message" => "Error: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
}
?>