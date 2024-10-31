<?php
// Include database connection parameters
include "../connect.php"; // Make sure this file sets $servername, $username, $password, and $dbname
include "../header.php";

// Connection success check
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true); // Decode JSON to associative array

    // Check if data is valid
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => "Invalid JSON data"]);
        exit();
    }

    // Safely access the decoded data
    $name = mysqli_real_escape_string($conn, $data["organizationName"] ?? '');
    $phone = mysqli_real_escape_string($conn, $data["contactNumber"] ?? '');
    $email = mysqli_real_escape_string($conn, $data["email"] ?? '');
    $address = mysqli_real_escape_string($conn, $data["address"] ?? '');
    $website = mysqli_real_escape_string($conn, $data["website"] ?? '');
    $about = mysqli_real_escape_string($conn, $data["description"] ?? '');
    $pwd = mysqli_real_escape_string($conn, $data["password"] ?? '');
    $Conpwd = mysqli_real_escape_string($conn, $data["confirmPassword"] ?? '');

    // Validation
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($about) || empty($pwd) || empty($Conpwd)) {
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit();
    }

    // Check password match
    if ($pwd !== $Conpwd) {
        echo json_encode(['success' => false, 'message' => "Password and Confirm Password do not match"]);
        exit();
    }

    // Check for existing email or organization
    $checkQuery = "SELECT * FROM organizers WHERE email='$email' OR name='$name'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'message' => "Email or Organization Name already exists"]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

    // Insert organizer details
    $insertQuery = "INSERT INTO organizers (name, phone, email, address, website, about, password) 
                    VALUES ('$name', '$phone', '$email', '$address', '$website', '$about', '$hashedPassword')";

    if (mysqli_query($conn, $insertQuery)) {
        // Use the existing rhinogua_user_events database
        $userDbName = "rhinogua_user_events";
        
        // Connect to the existing database
        $userConn = mysqli_connect($servername, $username, $password, $userDbName);
        
        if ($userConn) {
            // Create user-specific table within the rhinogua_user_events database
            $userTableName = "events_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $email); // Ensuring valid table name
            $createTableQuery = "CREATE TABLE IF NOT EXISTS `$userTableName` (
                id INT NOT NULL AUTO_INCREMENT,
                eventTitle VARCHAR(255) NOT NULL,
                ticketType VARCHAR(255) NOT NULL,
                amount INT(11) NOT NULL,
                eventId INT(11) NOT NULL,
                username VARCHAR(255) NOT NULL,
                image VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                ref VARCHAR(20) NOT NULL,
                attended BOOLEAN NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            )";

            if (mysqli_query($userConn, $createTableQuery)) {
                echo json_encode(['success' => true, 'message' => "Registration Successful"]);
            } else {
                echo json_encode(['success' => false, 'message' => "Error creating event table: " . mysqli_error($userConn)]);
            }

            mysqli_close($userConn);
        } else {
            echo json_encode(['success' => false, 'message' => "Error connecting to the rhinogua_user_events database: " . mysqli_connect_error()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Error registering organizer: " . mysqli_error($conn)]);
    }
}
?>