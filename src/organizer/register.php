<?php 
    include "connect.php";
    $condition ="";

    if (isset($_POST["submit"])) {
          $name = $_POST["organizationName"];
          $phone = $_POST["contactNumber"];
          $email = $_POST["email"];
          $address = $_POST["address"];
          $website = $_POST["website"];
          $about = $_POST["description"];
          $pwd = $_POST["password"];
          $Conpwd = $_POST["confirmPassword"];

          if (empty( $name) && empty($phone) && empty($email) && empty($address) && empty($website) && empty($about) && empty($pwd) && empty($Conpwd)) {
               $condition = "All fields are required";
          } else {
               if ($pwd != $Conpwd) {
                    $condition = "Password and Confirm Password do not match";
               } else {
                    $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
                    $insert = mysqli_query($conn, "INSERT INTO `organizers`(`name`, `phone`, `email`, `address`, `website`, `about`, `password`) VALUES ('$name','$phone','$email','$address','$website','$about','$hashedPassword')");
                    $condition = "Registration Successful";
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
    <title>Organizer Registration</title>
    <link rel="stylesheet" href="styless.css">
    <style>
          #condition{
               position: fixed;
               right: 0;
               top: 5vh;
               background-color: var(--white);
               padding: 10px 15px;
               border-radius: 15px 0  0 15px;
               box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
               color: red;
               text-align: center;
          }
    </style>
</head>
<body>

     <div class="auth-container registerFormContainer">
          <h2>Sign Up as an Organizer</h2>
          <p id="condition"><?php echo($condition); ?></p>
          <form id="registerForm" method="POST">
               <div class="row justify-content-center">
                      <!-- Organization/Group Details -->
                    <div class="col-12 form-group">
                         <label for="organizationName">Organization/Group Name:</label>
                         <input type="text" id="organizationName" name="organizationName" placeholder="e.g., Party Planners Co." required>
                    </div>

                    <!-- Contact Details -->
                    <div class="col-md-6 col-12">
                              <div class="form-group">
                                   <label for="contactNumber">Contact Number:</label>
                                   <input type="tel" id="contactNumber" name="contactNumber" placeholder="e.g., +1234567890" required>
                              </div>
                              <div class="form-group">
                                   <label for="email">Email:</label>
                                   <input type="email" id="email" name="email" placeholder="e.g., organizer@parties.com" required>
                              </div>
                              <div class="form-group">
                                   <label for="address">Address:</label>
                                   <input type="text" id="address" name="address" placeholder="e.g., 123 Party Street, Event City" required>
                              </div>
                    </div>
                    <div class="col-md-6 col-12">
                         <!-- Additional Information -->
                         <div class="form-group">
                              <label for="website">Website (Optional):</label>
                              <input type="text" id="website" name="website" placeholder="e.g., www.yourwebsite.com">
                         </div>
                         <div class="form-group">
                              <label for="description">Tell Us About Your Organization:</label>
                              <textarea id="description" name="description" rows="4" placeholder="Describe your services, history, or mission..." required></textarea>
                         </div>
                    </div>

                    <!-- Account Details -->
                    <div class="col-md-6 col-12 form-group">
                         <label for="password">Password:</label>
                         <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>
                    <div class="col-md-6 col-12 form-group">
                         <label for="confirmPassword">Confirm Password:</label>
                         <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="col-6 submit-btn" name="submit">Register</button>
               </div>
          </form>
          <p>Already have an account? <a href="login.php">Login here</a></p>
     </div>

     <script src="../OfflineResources/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://kit.fontawesome.com/2a49fbdbb8.js" crossorigin="anonymous"></script>
     <script>
          let condition = document.getElementById("condition");
          if (condition != ""){
               setTimeout(() => {
                    condition.style.display = "none";
               }, 5000);
          } else if (condition == "Registration Successful"){
               setTimeout(() => {
                    condition.style.display = "none";
               }, 5000);
               location.href = "./login.php"
          }
     </script>
 </body>
 </html> 