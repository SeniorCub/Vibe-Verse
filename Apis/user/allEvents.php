<?php
include "../connect.php"; // Include database connection

// Prepare the SQL query
$query = "SELECT * FROM `party`"; // Use prepared statement

// Execute the query
if ($stmt = $conn->prepare($query)) {

     if ($stmt->execute()) {
          // Fetch the details from the database
          $result = $stmt->get_result();

          // Convert the result set to an array
          $events = [];
          while ($row = $result->fetch_assoc()) {
               $events[] = $row; // Add each row to the events array
          }

          // Return the success response with event details
          echo json_encode(['success' => true, 'message' => 'Events fetched successfully', 'data' => $events]);
     } else {
          echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
     }

     $stmt->close(); // Close the statement
} else {
     echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}

$conn->close(); // Close the database connection
?>