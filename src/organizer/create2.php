<?php
include "connect.php";
// Fetch all organizers who are not admin
$organizers = mysqli_query($conn, "SELECT * FROM `party` ");
$organizer = mysqli_fetch_assoc($organizers);
var_dump($organizer['category']);
?>