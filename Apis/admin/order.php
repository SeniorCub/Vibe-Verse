<?php
include "../connect.php"; // Include database connection
include "../header.php";

// Get all headers
$header = getallheaders(); 
error_log(print_r($header, true)); // Log the headers for debugging

// Check for the Authorization header
$token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;

if (!$token) {
    echo json_encode(["status" => 'error', "message" => "No token provided", "data" => null, "url" => 'login.html']);
    exit();
}

// Get the 'id' parameter from the URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Validate the 'id' parameter
if (empty($id)) {
    echo json_encode(['success' => false, 'error' => "ID is required."]);
    exit();
}

// Query to check the user's token
$que = "SELECT * FROM `organizers` WHERE `token` = ?";
if ($stmt = $conn->prepare($que)) {
    $stmt->bind_param("s", $token);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    }
}

// Prepare the SQL query for fetching payment records
$query = "SELECT * FROM `payment` WHERE `isPending` = 0 AND `id` = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id); // Bind the 'id' parameter as an integer

    if ($stmt->execute()) {
        // Fetch the details from the database
        $result = $stmt->get_result();

        // Convert the result set to an array
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row; // Add each row to the events array
        }

        if (count($events) == 0) {
            // Return the response if no events are found
            echo json_encode(['success' => false, 'message' => 'Order fetch failed', 'url' => 'recent.html']);
        } else {
            // Return the success response with event details
            echo json_encode(['success' => true, 'message' => 'Order fetched successfully', 'data' => $events]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
    }

    $stmt->close(); // Close the statement
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}

$conn->close(); // Close the database connection
?>
