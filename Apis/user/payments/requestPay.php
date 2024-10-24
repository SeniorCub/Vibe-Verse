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

// Trim the input values
$eventTitle = trim($data['eventTitle']);
$eventId = $data['eventId'];
$amount = trim($data['amount']);
$description = trim($data['description']);
$pin = trim($data['pin']);
$accountNumber = $user['accountNumber'];
$bankName = $user['bankName'];
$accountName = $user['accountName'];

// Validate if any required fields are empty
if (empty($eventTitle)) {
   echo json_encode(['success' => false, 'error' => "Event title is required."]);
} elseif (empty($eventId)) {
   echo json_encode(['success' => false, 'error' => "Event ID is required."]);
} elseif (empty($amount)) {
   echo json_encode(['success' => false, 'error' => "Amount is required."]);
} elseif (empty($pin)) {
   echo json_encode(['success' => false, 'error' => "Pin is required."]);
} elseif (empty($accountNumber)) {
   echo json_encode(['success' => false, 'error' => "Account number is required."]);
} elseif (empty($bankName)) {
   echo json_encode(['success' => false, 'error' => "Bank name is required."]);
} elseif (empty($accountName)) {
   echo json_encode(['success' => false, 'error' => "Account name is required."]);
} else {

$query = "INSERT INTO `payment` (`eventId`, `eventTitle`, `organizerEmail`, `amount`, `description`, `accountNumber`, `bankName`, `accountName`) VALUES (?,?,?,?,?,?,?,?)";

// Create a prepared statement
if ($stmt = $conn->prepare($query)) {
     $stmt->bind_param("ssssssss", $eventId, $eventTitle, $sessionemail, $amount, $description, $accountNumber, $bankName, $accountName);

     if ($stmt->execute()) {
          echo json_encode(['success' => true, 'message' => 'Payment requested successfully', 'url' => 'payment.html']);
     } else {
          echo json_encode(['success' => false, 'error' => 'Failed to request payment']);
     }

     $stmt->close();
} else {
     echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
}
}
$conn->close();

?>
