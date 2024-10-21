<?php
     session_start(); // Start the session
     include "connect.php";
     include "header.php";
     
     // Check if the session email is set
     if (isset($_SESSION['email'])) {
         $sessionemail = $_SESSION['email'];
     } else {
         header("location:login.php");
         exit();
     }     

     // Decode the incoming JSON data
     $data = json_decode(file_get_contents('php://input'), true);

     // Check if 'password' field is set in the request
     if (!isset($data['password'])) {
          echo json_encode(['error' => 'Password is required']);
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
          echo json_encode(['message' => 'Password updated successfully', 'url' => 'login.php']);
     } else {
          echo json_encode(['error' => 'Failed to update password']);
     }

     $stmt->close();
     } else {
     echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
     }

     $conn->close();
?>