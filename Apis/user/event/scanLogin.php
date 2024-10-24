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

// Execute the query
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

// Retrieve the event title and email from the incoming data
// Specify the event ID (you may adjust this as needed)
$eventId = $data['eventId'];
$eventTitle = trim($data['eventTitle']);
$email = trim($data['email']);

// Validate if the event title and email are provided
if (empty($eventTitle)) {
     echo json_encode(['message' => "Event Title is required!."]);
     exit;
} else if  (empty($eventId)) {
     echo json_encode(['message' => "Event Id is required!."]);
     exit;
} else if (empty($email)) {
     echo json_encode(['message' => "Email is required!."]);
     exit;
}

// Prepare the SQL query to fetch the event details
$query = "SELECT * FROM `party` WHERE `organizerEmail` = ? AND `id` = ?";

// Execute the query
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ss", $sessionemail, $eventId); // Bind the email and event ID parameters

    if ($stmt->execute()) {
        // Fetch the details from the database
        $result = $stmt->get_result();
        $events = $result->fetch_assoc();

        // Check if 'emails' column is available
        if (isset($events['emails'])) {
            // Decode the JSON string to an array
            $emailsArray = json_decode($events['emails'], true);

            if (is_array($emailsArray)) {
                // Check if the provided email is in the emails array
                if (in_array($email, $emailsArray)) {
                    // Email found, return success response
                    echo json_encode(['success' => true, 'message' => 'Email matched successfully!', 'data' => $email]);
                } else {
                    // Email not found in the array
                    echo json_encode(['success' => false, 'message' => 'Email not found in the event']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid emails format in the database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No emails found in the database']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
    }
}
?>