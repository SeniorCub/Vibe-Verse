<?php
$servername = "localhost";
$username = "rhinogua_RhinoAdmin";
$password = "H2SO4.H2O"; // Leave this empty if you have not set a password
$dbname = "rhinogua_vibeVerse";
// $port = 3307; // Your specified port

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
// } else{
// Uncomment for debugging
// echo "Connected";
}
?>
