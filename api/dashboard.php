<?php
include_once 'helo.php';
session_start();

// Function to get token from query parameter
function getTokenFromQuery() {
    // Check if the token query parameter is set
    if (isset($_GET['token'])) {
        return $_GET['token'];
    } else {
        return null;
    }
}

// Function to get user ID based on token
function getUserIdByToken($token) {
    // Include your database connection
    include_once 'helo.php';

    // Prepare SQL statement
    $sql = "SELECT id FROM users WHERE token = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("s", $token);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if result contains rows
    if ($result->num_rows > 0) {
        // Fetch user ID
        $row = $result->fetch_assoc();
        $userId = $row["id"];
    } else {
        $userId = null;
    }

    // Close statement
    $stmt->close();

    return $userId;
}

// Function to fetch user history based on user ID
function getUserHistory($userId) {
    // Include your database connection
    include_once 'helo.php';

    // Prepare SQL statement
    $sql = "SELECT rni, timestamp FROM user_history WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("i", $userId);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch user history
    $userHistory = [];
    while ($row = $result->fetch_assoc()) {
        $userHistory[] = $row;
    }

    // Close statement
    $stmt->close();

    return $userHistory;
}

// Main function
function main() {
    // Fetch token from query parameter
    $token = getTokenFromQuery();

    if ($token === null) {
        return "Token is missing.";
    }

    // Get user ID based on token
    $userId = getUserIdByToken($token);

    if ($userId === null) {
        return "User not found for the given token.";
    }

    // Get user history based on user ID
    $userHistory = getUserHistory($userId);

    if (empty($userHistory)) {
        return "User history not found for user ID: $userId";
    }

    // Return the user history
    return $userHistory;
}

// Call the main function
$result = main();

// Output the result
echo json_encode($result);

?>