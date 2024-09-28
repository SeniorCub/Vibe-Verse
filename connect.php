<?php
     $servername = "localhost";
     $username = "root";
     $password = ""; // Leave this empty if you have not set a password
     $dbname = "tunetribe";
     $port = 3307; // Add the new port number
     
     // Create connection
     $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
     if ($conn) {
          echo"Connected";
     }
?>