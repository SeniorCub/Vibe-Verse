<?php
include "connect.php"; // Include database connection
include "header.php";

// Function to respond with JSON
function jsonResponse($success, $data = [], $message = "") {
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit(); // Ensure no further output is sent
}

// Function to generate a random token with both letters and numbers
function generateToken($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $index = random_int(0, $charactersLength - 1); // Ensure it's getting a valid index within the character set range
        $randomString .= $characters[$index];
    }
    
    return $randomString;
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
    if (!$stmt) {
        jsonResponse(false, [], "Database preparation failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email); // Bind email parameter

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            
            // Verify the password
            if (password_verify($pwd, $hashedPassword)) {
                // Fetch user details, including `isAdmin` column
                $selectUser = "SELECT * FROM `organizers` WHERE `email` = ?";
                $stmtUser = $conn->prepare($selectUser);
                if (!$stmtUser) {
                    jsonResponse(false, [], "Database preparation failed for user query: " . $conn->error);
                }

                $stmtUser->bind_param("s", $email);
                $stmtUser->execute();
                $userResult = $stmtUser->get_result();
                $user = $userResult->fetch_assoc();

                // Generate a random token for this user
                $token = generateToken(20); // 20-character token

                // Update user with the token
                $update = "UPDATE `organizers` SET `token` = ? WHERE `email` = ?";
                $stmtUpdate = $conn->prepare($update);
                if (!$stmtUpdate) {
                    jsonResponse(false, [], "Database preparation failed for token update: " . $conn->error);
                }

                $stmtUpdate->bind_param("ss", $token, $email);
                if (!$stmtUpdate->execute()) {
                    jsonResponse(false, [], "Token update failed: " . $stmtUpdate->error);
                } else {
                    // Use the `isAdmin` column to determine the user's role
                    $role = $user['isAdmin'] ? "ADMIN" : "USER";
                    $redirectUrl = $user['isAdmin'] ? '../admin/' : '/src/organizer/';
                    
                    // Add this log to ensure the token update was successful
                    jsonResponse(true, ['role' => $role, 'url' => $redirectUrl, 'token' => $token], "Login successful, token updated.");
                }

            } else {
                jsonResponse(false, [], "Invalid password.");
            }
        } else {
            jsonResponse(false, [], "Invalid email.");
        }
    } else {
        jsonResponse(false, [], "Database query failed: " . $stmt->error);
    }

    $stmt->close();
}

jsonResponse(false, [], "No action taken.");
?>