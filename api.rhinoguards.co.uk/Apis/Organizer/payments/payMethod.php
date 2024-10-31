<?php
include "../../connect.php";
include "../../header.php";

header("Content-Type: application/json"); // Ensure JSON response

// Get all headers
$header = getallheaders();
error_log(print_r($header, true));

$token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;

if (!$token) {
    echo json_encode(["status" => 'error', "message" => "No token provided", "data" => null, "url" => 'login.html']);
    exit();
}

$que = "SELECT * FROM `organizers` WHERE `token` = ?";

if ($stmt = $conn->prepare($que)) {
    $stmt->bind_param("s", $token);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $sessionemail = $user['email'];
    }
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
    exit();
}

$accountNumber = $data['accountNumber'];
$bankName = $data['bankName'];
$accountName = $data['accountName'];

$query = "UPDATE `organizers` SET `accountNumber` = ?, `bankName` = ?, `accountName` = ? WHERE `email` = ? AND `token` = ?";

if (empty($accountNumber) && empty($bankName) && empty($accountName)) {
    echo json_encode(['success' => false, 'message' => "All fields are required"]);
}else if (empty($accountNumber)) {
    echo json_encode(['success' => false, 'message' => "accountNumber required"]);
} else if (empty($bankName)) {
    echo json_encode(['success' => false, 'message' => "bankName required"]);
} else if (empty($accountName)) {
    echo json_encode(['success' => false, 'message' => "accountName required"]);
} else {
    if ($stmt = $conn->prepare($query)) {
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