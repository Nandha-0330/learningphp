<?php
include_once 'helo.php';
session_start();

$data = json_decode(file_get_contents("php://input"));

// Check if all required fields are present and not empty
if (empty($data->mobileno) || empty($data->password)) {
    echo json_encode(array("message" => "Please provide mobile number and password"));
    exit; // Exit script if any field is missing or empty
}

// Escape input
$mobileno = $conn->real_escape_string($data->mobileno);
$password = $conn->real_escape_string($data->password);

// Retrieve user record from database
$sql = "SELECT * FROM users WHERE mobileno='$mobileno'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, verify password
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // Verify password
    if (password_verify($password, $hashedPassword)) {
        // Password is correct, generate and store token
        $token = generateToken($conn, $mobileno);
        
        // Get user's name
        $username = $row['username'];

        // Return success response with token and username
        $response = array("status" => "success", "message" => "Welcome Back $username.", "token" => $token);
        echo json_encode($response);
    } else {
        // Password is incorrect
        echo json_encode(array("status" => "error", "message" => "Invalid mobile number or password."));
    }
} else {
    // User not found
    echo json_encode(array("status" => "error", "message" => "User not found."));
}

// Close connection
$conn->close();

// Function to generate and store token
function generateToken($conn, $mobileno) {
    $token = uniqid();
    $sql = "UPDATE users SET token='$token' WHERE mobileno='$mobileno'";
    $conn->query($sql);
    return $token;
}
?>