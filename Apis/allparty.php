<?php
include "connect.php";
include "header.php";
// Get all headers
$header = getallheaders();
error_log(print_r($header, true)); // Log the headers for debugging

// Check for the Authorization header
$token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;

if (!$token) {
     echo json_encode(['success' => false,"status" => 'error', "message" => "No token provided"]);
     exit();
}
$que = "SELECT * FROM `organizers` WHERE `token` = ?";

// Execute the que
if ($stmt = $conn->prepare($que)) {
     $stmt->bind_param("s", $token);
     if ($stmt->execute()) {
          $result = $stmt->get_result();
          $user = $result->fetch_assoc();
          $sessionemail = $user['email'];
     }
}
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