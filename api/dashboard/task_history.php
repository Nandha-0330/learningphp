<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Function to get token from request body
function getTokenFromBody() {
    // Check if the request body contains JSON data
    $requestData = json_decode(file_get_contents('php://input'), true);
    
    // Check if the token is present in the request body
    if (isset($requestData['token'])) {
        return $requestData['token'];
    } else {
        return null;
    }
}

// Function to insert daily task URL into task_history table
function insertTaskHistory($userId, $dailyTaskId, $conn) {
    // Prepare SQL statement to insert into task_history table
    $sql = "INSERT INTO task_history (user_id, taskvideo_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return "Error preparing statement: " . $conn->error;
    }
    
    // Bind parameters
    $stmt->bind_param("ii", $userId, $dailyTaskId);
    
    // Execute statement
    if (!$stmt->execute()) {
        return "Error executing statement: " . $stmt->error;
    }

    // Close statement
    $stmt->close();

    return "Daily task URL inserted successfully.";
}

// Main function
function main() {
    // Fetch token from request body
    $token = getTokenFromBody();

    if ($token === null) {
        return "Token is missing.";
    }

    // Connect to your database
    try {
        $conn = new mysqli("localhost", "johste948_reearning", "Biovus21$$", "johste948_reearning");
        if ($conn->connect_error) {
            return "Connection failed: " . $conn->connect_error;
        }
    } catch(Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
    
    // Get user ID based on token
    $userId = getUserIdByToken($token, $conn);

    if ($userId === null) {
        return "User not found for the given token.";
    }

    // Fetch daily_task_id from the request body
    $requestData = json_decode(file_get_contents('php://input'), true);
    if (!isset($requestData['daily_task_id'])) {
        return "Daily task ID is missing.";
    }
    $dailyTaskId = $requestData['daily_task_id'];

    // Insert daily task URL into task_history table
    $result = insertTaskHistory($userId, $dailyTaskId, $conn);

    // Return the result
    return $result;
}

// Call the main function
$result = main();

// Output the result
echo $result;
?>