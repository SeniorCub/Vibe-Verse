<?php
include "connect.php"; // Include database connection
include "header.php";

// Ensure that sessions are started properly
session_start();

// Function to respond with JSON
function jsonResponse($success, $data = [], $message = "") {
    echo json_encode(['success' => $success, 'data' => $data, 'message' => $message]);
    exit(); // Ensure no further output is sent
}

// Debugging: Check if session exists
if (!isset($_SESSION['email'])) {
    error_log("Session email is not set. Redirecting to login.");
    jsonResponse(false, [], "Session not active. Please log in.");
}

// Get the session email
$sessionemail = $_SESSION['email'];
error_log("Session email found: " . $sessionemail);

// Fetch user details from the database
$select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$sessionemail'");
$details = mysqli_fetch_assoc($select);

// Check if the user exists
if (!$details) {
    error_log("User not found for email: " . $sessionemail);
    jsonResponse(false, [], "User not found.");
}

// Check if the user is suspended
if ($details['active'] != 0) {
    error_log("User suspended: " . $sessionemail);
    jsonResponse(false, [], "Your account is suspended.");
}

// Check if the user is an admin
if ($details['email'] == 'admin@mail.com') {
    jsonResponse(true, ['condition' => "ADMIN"], "User is an admin.");
} else {
    jsonResponse(true, ['condition' => "USER", 'details' => $details], "User is a standard user.");
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    error_log("User logged out: " . $sessionemail);
    jsonResponse(true, [], "Successfully logged out.");
}

// If no action, default response
jsonResponse(false, [], "No action taken.");
?>