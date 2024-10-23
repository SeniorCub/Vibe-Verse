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
     // Decode the incoming JSON data
     $data = json_decode(file_get_contents('php://input'), true);

     $pin = trim($data['pin']);
     $pwd = trim($data['password']);
     $hashedPassword = $user['password'];

     if (empty($pin)) {
          echo json_encode(['success' => false,'status' => "error",'message' =>"Pin cannot be empty"]);
     } else if (empty($pwd)) {
          echo json_encode(['success' => false,'status' => "error",'message' =>"Password cannot be empty"]);
     } else{
     
     // Verify the password
     if (password_verify($pwd, $hashedPassword)) {
          // Update the details in the database
          $update = "UPDATE `organizers` SET `pin`='$pin' WHERE `email` = '$sessionemail'";
          if ($conn->query($update) === TRUE) {
               echo json_encode(['success' => true,'message' =>"Details updated successfully"]);
               } else {
                    echo json_encode(['success' => false,'message' => "Error updating details: " . $conn->error]);
               }
     } else {
          echo json_encode(['success' => false,'message' => "Invalid password."]);
     }
}
$conn->close();
?>