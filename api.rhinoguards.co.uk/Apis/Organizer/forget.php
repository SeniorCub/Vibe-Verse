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

     // Decode the incoming JSON data
     $data = json_decode(file_get_contents('php://input'), true);

     // Check if 'password' field is set in the request
     if (!isset($data['password'])) {
          echo json_encode(['success' => false, 'error' => 'Password is required']);
     exit;
     }

     // Define the email and the new password
     $email = $sessionemail; 
     $pwd = trim($data['password']); // Trim the password input

     // Hash the password for security purposes
     $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

     // Prepare the SQL query with placeholders
     $query = "UPDATE `organizers` SET `password` = ? WHERE `email` = ?";

     // Create a prepared statement
     if ($stmt = $conn->prepare($query)) {
     $stmt->bind_param("ss", $hashedPassword, $email);

     if ($stmt->execute()) {
          echo json_encode(['success' => true,'message' => 'Password updated successfully', 'url' => 'login.php']);
     } else {
          echo json_encode(['success' => false,'error' => 'Failed to update password']);
     }

     $stmt->close();
     } else {
     echo json_encode(['success' => false,'error' => 'Failed to prepare statement: ' . $conn->error]);
     }

     $conn->close();
?>