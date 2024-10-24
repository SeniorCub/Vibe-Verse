<?php
include "../connect.php";
include "../header.php";

// Connection success check
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST["organizationName"]);
    $phone = mysqli_real_escape_string($conn, $_POST["contactNumber"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $website = mysqli_real_escape_string($conn, $_POST["website"]);
    $about = mysqli_real_escape_string($conn, $_POST["description"]);
    $pwd = mysqli_real_escape_string($conn, $_POST["password"]);
    $Conpwd = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);

    // Validation
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($about) || empty($pwd) || empty($Conpwd)) {
        echo json_encode(['success' => false, 'message' => "All fields are required"]);
        exit();
    }

    // Check password match
    if ($pwd != $Conpwd) {
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
        // Create user-specific database and table
        $userDbName = "user_events_" . preg_replace('/[^a-zA-Z0-9]/', '_', $email);
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$userDbName`";
        
        if (mysqli_query($conn, $createDbQuery)) {
            // Connect to the new database and create the table
            $userConn = mysqli_connect($servername, $username, $password, $userDbName, $port);
            
            if ($userConn) {
                $createTableQuery = "CREATE TABLE IF NOT EXISTS events (
                    id INT NOT NULL AUTO_INCREMENT,
                    eventTitle VARCHAR(255) NOT NULL,
                    ticketType VARCHAR(255) NOT NULL,
                    amount INT(11) NOT NULL,
                    eventId INT(11) NOT NULL,
                    username VARCHAR(255) NOT NULL,
                    image VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    ref VARCHAR(20) NOT NULL,
                    PRIMARY KEY (id)
                )";

                if (mysqli_query($userConn, $createTableQuery)) {
                    echo json_encode(['success' => true, 'message' => "Registration Successful"]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Error creating event table: " . mysqli_error($userConn)]);
                }

                mysqli_close($userConn);
            } else {
                echo json_encode(['success' => false, 'message' => "Error connecting to new user database"]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Error creating database: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Error registering organizer: " . mysqli_error($conn)]);
    }
}
?>