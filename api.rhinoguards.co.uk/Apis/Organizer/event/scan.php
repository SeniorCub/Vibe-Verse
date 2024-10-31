<?php
include "../../connect.php"; // Include main database connection
include "../../header.php";

// Check connection to main database
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $organizerEmail = $data["organizerEmail"] ?? null;
    $eventEmail = $data["email"] ?? null;
    $eventId = $data["eventId"] ?? null;

    // Validate required fields
    if (empty($organizerEmail) || empty($eventEmail) || empty($eventId)) {
        echo json_encode(['success' => false, 'message' => "Organizer email, event email, and event ID are required"]);
        exit();
    }

    // Define organizer-specific database name and connect
    $userTableName = "events_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $organizerEmail);

// Connect to the organizer's specific database
    $userDbName = "rhinogua_user_events";
    $userConn = new mysqli($servername, $username, $password, $userDbName);
    
    if (!$userConn) {
        echo json_encode(['success' => false, 'message' => "Failed to connect to the organizer's database"]);
        exit();
    }

    // Prepare and execute the query to find the registered user's details in the `events` table
    $query = "SELECT * FROM $userTableName WHERE email = ? AND eventId = ?";
    if ($stmt = $userConn->prepare($query)) {
        $stmt->bind_param("si", $eventEmail, $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch user details
        if ($result->num_rows > 0) {
            $events = "";
            while ($row = $result->fetch_assoc()) {
                $events = $row; // Collect each row's data
            }
            echo json_encode(['success' => true, 'message' => "Email found in the specified event", 'data' => $events]);
        } else {
            echo json_encode(['success' => false, 'message' => "No records found for the specified email and event ID"]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Failed to prepare the select statement: " . $userConn->error]);
    }

    mysqli_close($userConn); // Close the personalized database connection
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request method"]);
}

$conn->close(); // Close the main database connection
?>