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

// Check for the token in the database
$que = "SELECT * FROM `organizers` WHERE `token` = ?";
if ($stmt = $conn->prepare($que)) {
    $stmt->bind_param("s", $token);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $sessionemail = $user['email'];
    }
}

// Get the 'email' parameter from the URL
$email = isset($_GET['email']) ? $_GET['email'] : null;

// Validate the 'email' parameter
if (empty($email)) {
    echo json_encode(['success' => false, "error" => "Please provide an email"]);
    exit();
}

// Prepare the SQL query to check if the email is in the database
$query = "SELECT * FROM `organizers` WHERE `email` = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user) {
            echo json_encode(['success' => true, "status" => 'success', "message" => "Email Found", "data" => $user]);
        } else {
            echo json_encode(['success' => false, "status" => 'error', "message" => "Email not found", "url" => 'organizers.html']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to execute statement']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}

$conn->close(); // Close the database connection
?>
