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

// Get the 'id' and 'email' parameters from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$email = isset($_GET['email']) ? $_GET['email'] : null;

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);
$description = $data["description"] ?? ''; // Fixed typo in "description"

// Check if token is valid by querying the organizers table
$que = "SELECT * FROM `organizers` WHERE `token` = ?";
if ($stmt = $conn->prepare($que)) {
    $stmt->bind_param("s", $token);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    }
}

// Validate the 'id' and 'email' parameters
if (empty($id)) {
    echo json_encode(['success' => false, 'error' => "ID is required.", 'url' => 'recent.html']);
    exit();
} else if (empty($email)) {
    echo json_encode(['success' => false, 'error' => "Email is required.", 'url' => 'recent.html']);
    exit();
} else {
    // Update the payment record with the provided id
    $query = "UPDATE `payment` SET `isPending` = 1, `describe` = ? WHERE `id` = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $description, $id); // Bind description as string and id as integer

        if ($stmt->execute()) {
            // Success response
            echo json_encode(['success' => true, 'message' => 'Order Completed successfully', 'url' => 'recent.html']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to execute query']);
        }

        $stmt->close(); // Close the statement
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
    }
}

$conn->close(); // Close the database connection
?>
