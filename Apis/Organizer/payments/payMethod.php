<?php
include "../../connect.php"; // Include database connection
include "../../header.php";

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

// Execute the query to fetch the user based on token
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

// Cast account number to string and sanitize inputs
$accountNumber = trim($data['accountNumber']); // Treat account number as a string
$bankName = trim($data['bankName']);
$accountName = trim($data['accountName']);

$query = "UPDATE `organizers` SET `accountNumber` = ?, `bankName` = ?, `accountName` = ? WHERE `email` = ? AND `token` = ?";

// Validate input fields
if (empty($accountNumber) || empty($bankName) || empty($accountName)) {
    echo json_encode(['success' => false, 'message' => "All fields are required"]);
} else {
    if ($stmt = $conn->prepare($query)) {
        // Bind all parameters as strings
        $stmt->bind_param("sssss",  $accountNumber, $bankName, $accountName, $sessionemail, $token);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Payment Method Updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Payment Method Update Failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Database query failed: " . $stmt->error]);
    }

    $stmt->close();
}
?>
