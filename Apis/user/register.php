<?php
include "../connect.php"; // Include database connection

// Connection success check
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $data = json_decode(file_get_contents("php://input"), true);

     $organizerEmail = $data["organizerEmail"] ?? null;
     $eventId = $data["eventId"] ?? null;
     $eventTitle = $data["eventTitle"] ?? null;
     $username = $data["username"] ?? null;
     $amount = $data["amount"] ?? null;
     $ticketType = $data["ticketType"] ?? null;
     $image = $data["image"] ?? null;
     $ref = $data["ref"] ?? null;
     $email = $data["email"] ?? null;
     

    // Validation
    if (empty($organizerEmail) || empty($eventId) || empty($eventTitle) || empty($username) || empty($amount) || empty($ticketType) || empty($image) || empty($ref) || empty($email)) {
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit();
    }

    // Retrieve the personalized database name
    $userDbName = "user_events_" . preg_replace('/[^a-zA-Z0-9]/', '_', $organizerEmail);

    $servername = "localhost";
     $usernamea = "root";
     $password = ""; // Leave this empty if you have not set a password
     $port = 3307; 
    // Connect to the personalized organizer's database
    $userConn = mysqli_connect($servername, $usernamea, $password, $userDbName, $port);

    if (!$userConn) {
        echo json_encode(['success' => false, 'message' => "Failed to connect to the organizer's database"]);
        exit();
    }

    // Insert event details into the organizer's `events` table
    $query = "INSERT INTO events (eventId, eventTitle, username, amount, ticketType, image, email, ref)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $userConn->prepare($query)) {
        $stmt->bind_param("ississss", $eventId, $eventTitle, $username, $amount, $ticketType, $image, $email, $ref);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Event registered successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Error inserting event details: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Failed to prepare statement: " . $userConn->error]);
    }

    mysqli_close($userConn); // Close the personalized database connection
}

$conn->close(); // Close the main database connection
?>
