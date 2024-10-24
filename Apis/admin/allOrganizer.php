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

$query = "SELECT * FROM `organizers` WHERE `isAdmin` = 0";
// Execute the que
if ($stmt = $conn->prepare($query)) {
     if ($stmt->execute()) {
          $result = $stmt->get_result();
          $users = $result->fetch_assoc();
          // Return the success response with event details
          echo json_encode(['success' => true, 'message' => 'Organizers fetched successfully', 'data' => $users]);
     } else {
          echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
     }
     $stmt->close(); // Close the statement
} else {
     echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}

$conn->close(); // Close the database connection
