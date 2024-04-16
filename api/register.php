<?php
include_once 'helo.php';
session_start();

$data = json_decode(file_get_contents("php://input"));

// Check if all required fields are present and not empty
if (empty($data->username) || empty($data->email) || empty($data->password) || empty($data->mobileno)) {
    echo json_encode(array("message" => "Please provide all required fields"));
    exit; // Exit script if any field is missing or empty
}

// Escape and hash password
$username = $conn->real_escape_string($data->username);
$email = $conn->real_escape_string($data->email);
$password =password_hash($conn->real_escape_string($data->password), PASSWORD_DEFAULT); // Hash password
$mobileno = $conn->real_escape_string($data->mobileno);

// Check if the user already exists
$check_query = "SELECT * FROM users WHERE mobileno= '$mobileno'";
$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    echo json_encode(array("message" => "User already exists"));
} else {
    // Insert the user if not already exists
    $sql = "INSERT INTO users (username, email, password, mobileno) VALUES ('$username', '$email', '$password', '$mobileno')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "User registered successfully"));
    } else {
        echo json_encode(array("message" => "Error: " . $conn->error));
    }
}

$conn->close();
?>