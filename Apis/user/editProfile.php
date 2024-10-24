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

     $name =trim($data["organizationName"]);
     $phone =trim($data["contactNumber"]);
     $email = trim($data["email"]);
     $address =trim($data["address"]);
     $website =trim($data["website"]) || $user['website'];
     $about =trim($data["description"]);
     $pwd =trim($data["password"]);

     // Check if any fields are empty
     if (empty($name) && empty($phone) && empty($email) && empty($address) && empty($about) && empty($pwd)) {
          echo json_encode(['success' => false,'message' =>"All fields are required"]);
     } else if (empty($name)){
          echo json_encode(['success' => false,'message' =>"Organization name is required"]);
     } else if (empty($phone)) {
          echo json_encode(['success' => false,'message' =>"Contact number is required"]);
     } else if (empty($email)) {
          echo json_encode(['success' => false,'message' =>"Email is required"]);
     } else if (empty($address)) {
          echo json_encode(['success' => false,'message' =>"Address is required"]);
     } else if (empty($about)) {
          echo json_encode(['success' => false,'message' =>"Description is required"]);
     } else if (empty($pwd)) {
          echo json_encode(['success' => false,'message' =>"Password is required"]);
     }
     else {
          $hashedPassword = $user['password'];
          // Verify the password
          if (password_verify($pwd, $hashedPassword)) {
               if ($email != $sessionemail) {
                    echo json_encode(['success' => false,'message' =>"Email does not match with the email in the database", "url" => "login.html"]);
               } else{
               // Update the details in the database
               $update = "UPDATE `organizers` SET `name`='$name',`phone`='$phone',`email`='$email',`address`='$address',`website`='$website',`about`='$about' WHERE `email` = '$email'";
               if ($conn->query($update) === TRUE) {
                    echo json_encode(['success' => true,'message' =>"Details updated successfully"]);
                    } else {
                         echo json_encode(['success' => false,'message' => "Error updating details: " . $conn->error]);
                    }
               }
          } else {
               echo json_encode(['success' => false,'message' => "Invalid password."]);
          }
     }
     
     $conn->close();
     
?>   