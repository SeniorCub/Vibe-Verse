<?php
include "../connect.php";
include "../header.php";

header("Content-Type: application/json");

$header = getallheaders();
$token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;

if (!$token) {
    echo json_encode(["success" => false, "message" => "No token provided", "url" => "login.html"]);
    exit();
}

// Validate token and fetch user details
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

// Use existing values if new ones aren't provided
$name = !empty(trim($data["organizationName"])) ? trim($data["organizationName"]) : $user['name'];
$phone = !empty(trim($data["contactNumber"])) ? trim($data["contactNumber"]) : $user['phone'];
$email = trim($data["email"]); // Required field
$address = !empty(trim($data["address"])) ? trim($data["address"]) : $user['address'];
$website = !empty(trim($data["website"])) ? trim($data["website"]) : $user['website'];
$about = !empty(trim($data["description"])) ? trim($data["description"]) : $user['about'];
$pwd = trim($data["password"]);

// Validate password
if (empty($pwd)) {
    echo json_encode(['success' => false, 'message' => "Password is required"]);
    exit();
} else {
    $hashedPassword = $user['password'];
    if (password_verify($pwd, $hashedPassword)) {
        if ($email != $sessionemail) {
            echo json_encode(['success' => false, 'message' => "Email does not match the database", "url" => "login.html"]);
            exit();
        } else {
            // Update query to use prepared statements
            $update = "UPDATE `organizers` SET `name` = ?, `phone` = ?, `email` = ?, `address` = ?, `website` = ?, `about` = ? WHERE `email` = ? AND `token` = ?";
            if ($stmt = $conn->prepare($update)) {
                $stmt->bind_param("ssssssss", $name, $phone, $email, $address, $website, $about, $sessionemail, $token);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => "Details updated successfully"]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Error updating details: " . $conn->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Database query failed"]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Invalid password"]);
    }
}

$conn->close();
?>