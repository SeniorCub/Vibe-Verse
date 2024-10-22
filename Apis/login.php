<?php
include "connect.php"; // Include database connection
include "header.php";

// Function to respond with JSON
function jsonResponse($success, $data = [], $message = "") {
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit(); // Ensure no further output is sent
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data["email"] ?? '';
    $pwd = $data["password"] ?? '';

    // Validate inputs
    if (empty($email) || empty($pwd)) {
        jsonResponse(false, [], "All fields are required.");
    }

    // Query to select password for the provided email
    $select = "SELECT `password` FROM `organizers` WHERE `email` = ?";
    $stmt = $conn->prepare($select);
    $stmt->bind_param("s", $email); // Bind email parameter

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            
            // Verify the password
            if (password_verify($pwd, $hashedPassword)) {
                // Fetch user details
                $selectUser = "SELECT * FROM `organizers` WHERE `email` = ?";
                $stmtUser = $conn->prepare($selectUser);
                $stmtUser->bind_param("s", $email);
                $stmtUser->execute();
                $userResult = $stmtUser->get_result();
                $user = $userResult->fetch_assoc();
                
                $selectAll = mysqli_query($conn, "SELECT * FROM `organizers`");
               $totalUsers = mysqli_num_rows($selectAll);
               $nextUserNumber = $totalUsers + 1;
               $formattedUserNumber = str_pad($nextUserNumber, 5, "0", STR_PAD_LEFT);
               $currentYear = date("Y");
               $matricNoo = $currentYear . $formattedUserNumber;

               $update = "UPDATE `organizers` SET `token` = ? WHERE `email` = ?";
               $stmtUpdate = $conn->prepare($update);
               $stmtUpdate->bind_param("ss", $matricNoo, $email);
               $stmtUpdate->execute();  
               
                // Set session email
                $_SESSION['email'] = $user['email'];

                // Debugging: Check if session email is set
                error_log("Session email set: " . $_SESSION['email']); // Log session email

                // Determine user role
                if ($user['email'] == 'admin@mail.com') {
                    jsonResponse(true, ['role' => "ADMIN"], "Login successful!");
                } else {
                    jsonResponse(true, ['role' => "USER"], "Login successful!");
                }
            } else {
                jsonResponse(false, [], "Invalid password.");
            }
        } else {
            jsonResponse(false, [], "Invalid email.");
        }
    } else {
        jsonResponse(false, [], "Database query failed.");
    }

    $stmt->close();
}

jsonResponse(false, [], "No action taken.");
?>