<?php
     include "connect.php";

     // Logout
     if (isset($_POST['logout'])) {
          session_unset();
          session_destroy();
          header('location: login.php');
          $_SESSION['email'] = "";
     }
?>