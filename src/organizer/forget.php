<?php
     include "connect.php";
     include "logout.php";
     session_start();
     $sessionemail = $_SESSION['email'];
     $select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$sessionemail'");
     $details = mysqli_fetch_assoc($select);

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
    <title>Forget Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
     <button type="button" class="back-btn" onclick="window.location.href='/src/organizer/login.html'"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
    
     <div class="auth-container">
        <h2>Forget Password</h2>
        <form id="forgetForm" action="/forget-password" method="POST">
            <!-- Step 1: Request 6-digit code -->
          <div class="form-group step active">
               <label for="email">Email:</label>
               <input type="email" id="email" name="email" required>
               <button type="button" class="submit-btn mt-2">Request Code</button>
          </div>
          
          <!-- Step 2: Enter 6-digit code -->
          <div class="form-group step">
               <label for="code">6-digit Code:</label>
               <input type="number" id="code" name="code" required>
               <button type="button" class="submit-btn mt-2">Verify Code</button>
          </div>
          
          <!-- Step 3: Change password -->
          <div class="form-group step">
               <label for="password">New Password:</label>
               <input type="password" id="password" name="password" required>
               <button type="submit" class="submit-btn mt-2">confirm Password</button>
          </div>
          
          <!-- Step 4: Confirm password -->
          <div class="form-group step">
               <label for="confirm-password">Confirm Password:</label>
               <input type="password" id="confirm-password" name="confirm-password" required>
               <button type="submit" class="submit-btn mt-2">Change Password</button>
          </div>
        </form>
    </div>

    <script src="../OfflineResources/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/2a49fbdbb8.js" crossorigin="anonymous"></script>
    <script>
          // Define the code variable globally
          let code;

          // Function to generate a 6-digit code
          function generateCode() {
               return Math.floor(100000 + Math.random() * 900000);
          }

          // Function to send the code (simulated)
          function sendCode(code) {
               console.log(`Code sent: ${code}`);
          }

          // Function to generate and send a 6-digit code
          function requestCode() {
               code = generateCode();
               sendCode(code);
               return code; // Return the generated code
          }

          // Function to verify the code
          function verifyCodeForm() {
               const userCode = document.getElementById('code').value;
               console.log(userCode);
               if (userCode == code) {
                    console.log("Code verified successfully!");
               } else {
                    console.log("Invalid code. Please try again.");
                    
               }
          }

          // Function to verify passwords
          function verifyPasswords() {
               const newPassword = document.getElementById('password').value;
               const confirmPassword = document.getElementById('confirm-password').value;
               if (newPassword === confirmPassword) {
                    console.log("Passwords match. Proceed with password reset.");
                    
                    window.location.href="login.html"
               } else {
                    console.log("Passwords do not match. Please try again.");
               }
          }

          document.addEventListener('DOMContentLoaded', function() {
               const steps = document.querySelectorAll('.step');
               const submitButtons = document.querySelectorAll('.submit-btn');
          
               submitButtons.forEach((button, index) => {
               button.addEventListener('click', function() {
                    if (index < steps.length - 1) {
                         if (index === 0) {
                              requestCode();
                         } else if (index === 1) {
                              verifyCodeForm();
                         } else if (index === 3) {
                              verifyPasswords();
                         }
                         steps[index].classList.remove('active');
                         steps[index + 1].classList.add('active');
                         submitButtons[index].style.display = 'none';
                         submitButtons[index + 1].style.display = 'block';
                    }
               });
               });
          });

    </script>
</body>
</html>