<?php
include "connect.php"; // Include database connection
include "header.php";

session_start();

// Function to respond with JSON
function jsonResponse($success, $data = [], $message = "") {
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit(); // Ensure no further output is sent
}

// Check if the session email is set
if (!isset($_SESSION['email'])) {
    jsonResponse(false, [], "Session not active. Please log in.");
}

// Get the session email
$sessionemail = $_SESSION['email'];

// Fetch user details from the database
$select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$sessionemail'");
$details = mysqli_fetch_assoc($select);

// Check if the user exists
if (!$details) {
    jsonResponse(false, [], "User not found.");
}

// Check if the user is suspended
if ($details['active'] != 0) {
    jsonResponse(false, [], "Your account is suspended.");
}

// Check if the user is an admin
if ($details['email'] == 'admin@mail.com') {
    jsonResponse(true, ['condition' => "ADMIN"], "User is an admin.");
} else {
    jsonResponse(true, ['condition' => "USER"], "User is a standard user.");
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    jsonResponse(true, [], "Successfully logged out.");
}

// If the script reaches here, return a default message
jsonResponse(false, [], "No action taken.");
?>