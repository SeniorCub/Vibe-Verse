<?php
include "../../connect.php"; // Include database connection

// Connection success check
if (!$conn) {
     echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
     exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $data = json_decode(file_get_contents("php://input"), true);

     $organizerEmail = $data["organizerEmail"] ?? null;
     $eventEmail = $data["email"] ?? null;
     $eventId = $data["eventId"] ?? null;

     // Validation
     if (empty($organizerEmail) || empty($eventEmail) || empty($eventId)) {
          echo json_encode(['success' => false, 'message' => "Organizer email, event email, and event ID are required"]);
          exit();
     }

     // Retrieve the personalized database name
     $userDbName = "user_events_" . preg_replace('/[^a-zA-Z0-9]/', '_', $organizerEmail);

     $servername = "localhost";
     $usernamea = "root";
     $password = ""; // Adjust this if you have set a password
     $port = 3307;

     // Connect to the personalized organizer's database
     $userConn = mysqli_connect($servername, $usernamea, $password, $userDbName, $port);

     if (!$userConn) {
          echo json_encode(['success' => false, 'message' => "Failed to connect to the organizer's database"]);
          exit();
     }

     // Check if the email exists in the `events` table and matches the event ID
     $query = "SELECT * FROM events WHERE email = ? AND eventId = ?";
     if ($stmt = $userConn->prepare($query)) {
          $stmt->bind_param("si", $eventEmail, $eventId);
          $stmt->execute();
          $result = $stmt->get_result();
          // Convert the result set to an array
          $events = [];
          while ($row = $result->fetch_assoc()) {
               $events[] = $row; // Add each row to the events array
          }

          // Return the success response with event details
          echo json_encode(['success' => true, 'message' => '"Email found in the specified event', 'data' => $events]);
     } else {
          echo json_encode(['success' => false, 'message' => "Email not found in the specified event"]);
     }

     $stmt->close();
} else {
     echo json_encode(['success' => false, 'message' => "Failed to prepare select statement: " . $userConn->error]);
}

mysqli_close($userConn); // Close the personalized database connection
$conn->close(); // Close the main database connection
