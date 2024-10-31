<?php
include "../connect.php"; // Include database connection
include "../header.php";

// Connection success check
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

// Proceed if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $organizerEmail = $data["organizerEmail"] ?? null;
    $eventId = $data["eventId"] ?? null;
    $eventTitle = $data["eventTitle"] ?? null;
    $usernamee = $data["username"] ?? null;
    $amount = $data["amount"] ?? null;
    $ticketType = $data["ticketType"] ?? null;
    $image = $data["image"] ?? null;
    $ref = $data["ref"] ?? null;
    $email = $data["email"] ?? null;

    // Validation
    if (empty($organizerEmail) || empty($eventId) || empty($eventTitle) || empty($usernamee) || empty($amount) || empty($ticketType) || empty($image) || empty($ref) || empty($email)) {
        echo json_encode(['success' => false, 'message' => "All fields are required", 'url' => 'events.html']);
        exit();
    }

    // Connect to the organizer's specific database
    $userDbName = "rhinogua_user_events";
    $userConn = new mysqli($servername, $username, $password, $userDbName);

    if ($userConn->connect_error) {
        echo json_encode(['success' => false, 'message' => "Failed to connect to the organizer's database", 'url' => 'events.html']);
        exit();
    }

    // Define the table name based on the organizer's email
    $userTableName = "events_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $organizerEmail);

    // Check if the email already registered for the same event ID
    $checkQuery = "SELECT * FROM $userTableName WHERE email = ? AND eventId = ?";
    if ($checkStmt = $userConn->prepare($checkQuery)) {
        $checkStmt->bind_param("si", $email, $eventId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Email already registered for this event
            echo json_encode(['success' => false, 'message' => "Email already registered for this event", 'url' => 'events.html']);
            $checkStmt->close();
            $userConn->close();
            $conn->close();
            exit();
        }
        $checkStmt->close();
    }

    // Prepare the upload directory for event flyers
    $uploadDir = "Uploads/$userTableName/$eventTitle";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Decode the base64 image and store it on the server
    $flyerPath = $uploadDir . '/' . $usernamee . "_" . uniqid() . ".png";
    file_put_contents($flyerPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image)));

    // Insert event details into the organizer's events table
    $query = "INSERT INTO $userTableName (eventId, eventTitle, username, amount, ticketType, image, email, ref)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $userConn->prepare($query)) {
        $stmt->bind_param("isssssss", $eventId, $eventTitle, $usernamee, $amount, $ticketType, $flyerPath, $email, $ref);

        if ($stmt->execute()) {
            // Fetch event details to update balance and participants
            $fetchQuery = "SELECT * FROM `party` WHERE `organizerEmail` = ? AND `id` = ?";
            if ($fetchStmt = $conn->prepare($fetchQuery)) {
                $fetchStmt->bind_param("ss", $organizerEmail, $eventId);

                if ($fetchStmt->execute()) {
                    $result = $fetchStmt->get_result();
                    $events = $result->fetch_assoc();
                    $newBal = $events['totalBalance'] + $amount;
                    $newPart = $events['toatalParticipant'] + 1;

                    // Update the main event table with the new balance and participant count
                    $updateQuery = "UPDATE `party` SET `totalBalance`=?,`availableBalance`=?, `toatalParticipant`=? WHERE `organizerEmail`=? AND `id`=?";
                    if ($updateStmt = $conn->prepare($updateQuery)) {
                        $updateStmt->bind_param("diss", $newBal, $newBal, $newPart, $organizerEmail, $eventId);

                        if ($updateStmt->execute()) {
                            echo json_encode(['success' => true, 'message' => "Event registered and updated successfully", 'url' => 'events.html']);
                        } else {
                            echo json_encode(['success' => false, 'message' => "Failed to update event details: " . $updateStmt->error, 'url' => 'events.html']);
                        }
                        $updateStmt->close();
                    } else {
                        echo json_encode(['success' => false, 'message' => "Failed to prepare update statement: " . $conn->error, 'url' => 'events.html']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => "Failed to execute fetch query: " . $fetchStmt->error, 'url' => 'events.html']);
                }
                $fetchStmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => "Failed to prepare fetch statement: " . $conn->error, 'url' => 'events.html']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Error inserting event details: " . $stmt->error, 'url' => 'events.html']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => "Failed to prepare insert statement: " . $userConn->error, 'url' => 'events.html']);
    }

    $userConn->close(); // Close the organizer's database connection
}

$conn->close(); // Close the main database connection
?>