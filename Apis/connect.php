<?php
$servername = "localhost";
$username = "root";
$password = ""; // Leave this empty if you have not set a password
$dbname = "tunetribe";
$port = 3307; // Your specified port

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Uncomment for debugging
// echo "Connected";
?>
