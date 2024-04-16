<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Function to get token from query parameter
function getTokenFromQuery() {
    // Check if the token query parameter is set
    if (isset($_GET['token'])) {
        return $_GET['token'];
    } else {
        return null;
    }
}

// Function to fetch user ID based on token from the database
function getUserIdByToken($token, $conn) {
    // Prepare SQL statement
    $sql = "SELECT id FROM users WHERE token = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return null; // Return null if unable to prepare the statement
    }
    
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

// Function to fetch user history and sum the rni date-wise for the current month
function getUserHistoryAndSumRni($userId, $conn) {
    // Get the first and last day of the current month
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');

    // Prepare SQL statement to fetch user history for the current month
    $sql = "SELECT DATE_FORMAT(timeanddate, '%Y-%m-%d') AS date, SUM(rni) AS total_rni FROM user_history WHERE user_id = ? AND timeanddate BETWEEN ? AND ? GROUP BY DATE_FORMAT(timeanddate, '%Y-%m-%d')";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null; // Return null if unable to prepare the statement
    }

    // Bind parameters
    $stmt->bind_param("iss", $userId, $firstDayOfMonth, $lastDayOfMonth);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Fetch user history and sum the rni date-wise
    $summedRni = [];
    while ($row = $result->fetch_assoc()) {
        $summedRni[] = $row;
    }

    // Close statement
    $stmt->close();

    return $summedRni;
}

// Main function
function main() {
    // Fetch token from query parameter
    $token = getTokenFromQuery();

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

    // Get summed RNI for the current month
    $summedRni = getUserHistoryAndSumRni($userId, $conn);

    if (empty($summedRni)) {
        return "RNI data not found for the current month.";
    }

    // Return the summed RNI for the current month
    return $summedRni;
}

// Call the main function
$result = main();

// Output the result
echo json_encode($result);
?>