<?php
include "connect.php"; // Include database connection
include "header.php";

// Get all headers
$header = getallheaders();
error_log(print_r($header, true)); // Log the headers for debugging

// Check for the Authorization header
$token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;

if (!$token) {
     echo json_encode(["status" => 'error', "message" => "No token provided"]);
     exit();
}
$noe = 0;
$query = "SELECT * FROM `organizers` WHERE `token` = ?";

// Execute the query
if ($stmt = $conn->prepare($query)) {
     $stmt->bind_param("s", $noe);
     if ($stmt->execute()) {
          $result = $stmt->get_result();
          $user = $result->fetch_assoc();
          if ($user) {
               echo json_encode(["status" => 'success', "message" => "Logout Successful", "url" => '/src/organizer/login.html']);
          } else {
               echo json_encode(["status" => 'error', "message" => "Session not active. Please log in."]);
          }
     } else {
          echo json_encode(['success' => false, 'error' => 'Failed to execute statement']);
     }
     $stmt->close();
} else {
     echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}
?>