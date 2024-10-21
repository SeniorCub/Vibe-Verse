<?php
session_start(); // Start the session
include "connect.php";
include "header.php";

// // Check if the session email is set
// if (isset($_SESSION['email'])) {
//     $sessionemail = $_SESSION['email'];
// } else {
//     header("location:login.php");
//     exit();
// }

$sessionemail = 'farinderif@gmail.com';
// Prepare the SQL query
$query = "SELECT * FROM `party` WHERE `organizerEmail` = ?"; // Use prepared statement

// Execute the query
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $sessionemail); // Bind the email parameter

    if ($stmt->execute()) {
        // Fetch the details from the database
        $result = $stmt->get_result();
        
        // Convert the result set to an array
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row; // Add each row to the events array
        }

        // Return the success response with event details
        echo json_encode(['success' => true, 'message' => 'Events fetched successfully', 'details' => $events]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
    }
    
    $stmt->close(); // Close the statement
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}

$conn->close(); // Close the database connection
?>
