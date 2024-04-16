<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");


// Function to fetch daily tasks based on page number
function getDailyTasks($page, $conn) {
    // Calculate offset based on page number
    $offset = ($page - 1) * 10;

    // Prepare SQL statement to fetch daily tasks
    $sql = "SELECT id, videourl FROM taskvideo LIMIT 10 OFFSET ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return null; // Return null if unable to prepare the statement
    }
    
    // Bind parameter
    $stmt->bind_param("i", $offset);
    
    // Execute statement
    $stmt->execute();
    
    // Get result
    $result = $stmt->get_result();

    // Fetch daily tasks
    $dailyTasks = [];
    while ($row = $result->fetch_assoc()) {
        $dailyTasks[] = $row;
    }

    // Close statement
    $stmt->close();

    return $dailyTasks;
}

// Main function
function main() {
    // Fetch token from query parameter

    // Connect to your database
    try {
        $conn = new mysqli("localhost", "johste948_reearning", "Biovus21$$", "johste948_reearning");
        if ($conn->connect_error) {
            return "Connection failed: " . $conn->connect_error;
        }
    } catch(Exception $e) {
        return "Connection failed: " . $e->getMessage();
    }
    


    // Check if page parameter exists
    if (!isset($_GET['page'])) {
        return "Page parameter is missing.";
    }

    // Get page number
    $page = intval($_GET['page']);

    // Get daily tasks based on page number
    $dailyTasks = getDailyTasks($page, $conn);

    // Return the daily tasks
    return $dailyTasks;
}

// Call the main function
$result = main();

// Output the result
echo json_encode($result);
?>