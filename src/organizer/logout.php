<?php
     include "connect.php";
     
     session_start();

     $sessionemail = $_SESSION['email'];
     $select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$sessionemail'");
     $details = mysqli_fetch_assoc($select);

     // Disabled 
     if ($details['active'] != 0){
          header("location: suspended.html");
     }

     // Admin
     if ($details['email'] == 'admin@mail.com'){
          $condition = "ADMIN";
         header("location: ../admin/");
     } else {
          $condition = "USER";
     }
                         
     // Logout
     if (isset($_POST['logout'])) {
          session_unset();
          session_destroy();
          header('location: login.php');
          $_SESSION['email'] = "";
     }
?>