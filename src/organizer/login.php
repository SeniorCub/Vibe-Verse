<?php 
    include "connect.php";

    session_start();

    $condition ="";

    if (isset($_POST["submit"])) {
          $email = $_POST["email"];
          $pwd = $_POST["password"];

          if (empty( $name) && empty($pwd)) {
               $condition = "All fields are required";
          } else {
               // if ($pwd != $Conpwd) {
               //      $condition = "Password and Confirm Password do not match";
               // } else {
               //      $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
               //      $insert = mysqli_query($conn, "INSERT INTO `organizers`(`name`, `phone`, `email`, `address`, `website`, `about`, `password`) VALUES ('$name','$phone','$email','$address','$website','$about','$hashedPassword')");
               //      $condition = "Registration Successful";
               // }
               $select = "SELECT `password` FROM `organizers` WHERE `email` = '$email'";
               $result = mysqli_query($conn, $select);

               if (mysqli_num_rows($result) == 1) {
                   $row = mysqli_fetch_assoc($result);
                   $hashedPassword = $row['password'];
               
                    // Verify the password
                    if (password_verify($pwd, $hashedPassword)) {
                        $condition = "Login successful!";
                         $select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$email'");
                         $user = mysqli_fetch_assoc($select);
                         $_SESSION['email'] = $user['email'];
                         header("location: index.html");
                    } else {
                        $condition = "Invalid password.";
                    }
               } else {
                  $condition = "Invalid username.";
               }
          }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon">
     <link rel="stylesheet" href="../OfflineResources/fontawesome-free-6.4.2-web/css/all.css">
     <link rel="stylesheet" href="../OfflineResources/bootstrap-5.3.3-dist/css/bootstrap.min.css">
     <link rel="stylesheet" href="../style.css">
     <title>Login</title>
     <link rel="stylesheet" href="styles.css">
</head>
<body>
     <div class="auth-container">
          <h2>Login</h2>
          <form id="loginForm" method="POST">
               <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
               </div>
               <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
               </div>
                    <p class="forget"><a href="forget.html">Forget Password?</a></p>
               <button type="submit" class="submit-btn" name="submit">Login</button>
          </form>
          <p>Don't have an account? <a href="register.php">Register here</a></p>
     </div>

    <script src="../OfflineResources/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/2a49fbdbb8.js" crossorigin="anonymous"></script>
</body>
</html>