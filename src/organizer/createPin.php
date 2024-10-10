<?php
     include "connect.php";
     include "logout.php";
     
     $condition ="";

     session_start();
     if  (isset( $_SESSION['email'])){
          $sessionemail = $_SESSION['email'];
     } else {
          header("location: login.php");
          exit();
     }

     $select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$sessionemail'");
     $details = mysqli_fetch_assoc($select);

     if (isset($_POST["submit"])) {
          $pin1 = $_POST['pin1'];
          $pin2 = $_POST['pin2'];
          $pin3 = $_POST['pin3'];
          $pin4 = $_POST['pin4'];
          $pin21 = $_POST['pin21'];
          $pin22 = $_POST['pin22'];
          $pin23 = $_POST['pin23'];
          $pin24 = $_POST['pin24'];
          $pwd = $_POST['password'];

          $NewPin = $pin1 . $pin2 . $pin3 . $pin4;
          
          $ConPin = $pin21 . $pin22 . $pin23 . $pin24;

          if ($NewPin == $ConPin){
               $hashedPassword = $details['password'];
               
               // Verify the password
               if (password_verify($pwd, $hashedPassword)) {
                    $update = mysqli_query($conn, "UPDATE `organizers` SET `pin`='$NewPin' WHERE `email` = '$sessionemail' ");
                    if ($update) {
                         set_time_limit(5);
                         $condition = "Pin Update Successful";
                         header("refresh:5;url=profile.php");
                    } else {
                         $condition = "Pin Update Failed";
                    }
               } else {
                    $condition = "Password Incorrect";
               }
          } else {
               $condition = "Pin does not match";
          }
      
     }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../OfflineResources/fontawesome-free-6.4.2-web/css/all.css">
    <link rel="stylesheet" href="../OfflineResources/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/2a49fbdbb8.js" crossorigin="anonymous"></script>
     <title>Create Pin</title>
     <style>
          body {
               background-color: #f5f5f5;
          }
          .container {
               max-width: 600px;
               margin: 50px auto;
               padding: 20px;
               background-color: #fff;
               border: 1px solid #ddd;
               border-radius: 10px;
          }
          label span{
               font-size: 0.7rem;
               color: #ff7f5f;
          }
          .pin{
               width: 50px;
               height: 50px;
               border-radius: var(--radius);
               border: 1px solid var(--black);
               padding: auto;
               font-size: 1.5rem;
               font-weight: 200;
               -webkit-text-security: disc; /* Hide input text */
          }
          @media (min-width: 300px) and (max-width: 750px) {
               .pin{
                    width: 40px;
               }
          }
          #condition{
               position: fixed;
               right: 0;
               top: 10vh;
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
     <div class="container">
          <p id="condition"><?php echo($condition); ?></p>
          <button type="button" class="back-btn position-absolute" style="background-color: var(--gold); color: #ffffff; border: none; padding: 10px 20px; font-size: 16px; cursor: pointer; position: absolute; top: 50px; left: 50px; border-radius: var(--radius);" onclick="window.location.href='/src/organizer/'"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
          <h2 class="text-center">Create Pin</h2>
          <form method="post">
          <div class="form-group">
                    <label for="pin1">Create Pin:</label>
                    <div class="d-flex gap-3 justify-content-center align-items-center pins">
                         <input type="text" name="pin1" id="pin1" class="form-control text-center pin pin1" maxlength="1"><span>-</span>
                         <input type="text" name="pin2" id="pin2" class="form-control text-center pin pin1" maxlength="1"><span>-</span>
                         <input type="text" name="pin3" id="pin3" class="form-control text-center pin pin1" maxlength="1"><span>-</span>
                         <input type="text" name="pin4" id="pin4" class="form-control text-center pin pin1" maxlength="1">
                    </div>
               </div>
               <div class="form-group">
                    <label for="pin2">Confirm Pin:</label>
                    <div class="d-flex gap-3 justify-content-center align-items-center pins">
                         <input type="text" name="pin21" id="pin11" class="form-control text-center pin pin2" maxlength="1"><span>-</span>
                         <input type="text" name="pin22" id="pin12" class="form-control text-center pin pin2" maxlength="1"><span>-</span>
                         <input type="text" name="pin23" id="pin13" class="form-control text-center pin pin2" maxlength="1"><span>-</span>
                         <input type="text" name="pin24" id="pin14" class="form-control text-center pin pin2" maxlength="1">
                    </div>
               </div>
               <div class="form-group">
                    <label for="password">Enter Password:</label>
                    <input type="password" class="form-control" name="password" >
               </div>
               <button type="submit" name="submit" class="btn my-2 submit" style="background-color: var(--gold); color: #ffffff; border: none;">Proceed</button>
          </form>
     </div>
     <script src="../OfflineResources/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
     <script src="../OfflineResources/fontawesome-free-6.4.2-web/css/all.css" crossorigin="anonymous"></script>
     <script>
          const pinInputs1 = document.querySelectorAll('.pin1');
          const pinInputs2 = document.querySelectorAll('.pin2');

          // AUTOMATICALLY MOVE TO THE NEXT INPUT WHEN THE CURRENT INPUT IS FILLED
          pinInputs1.forEach((input, index) => {
                input.addEventListener('input', function () {
                    if (input.value.length === 1 && index < pinInputs1.length - 1) {
                        pinInputs1[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Backspace' && input.value.length === 0 && index > 0) {
                        pinInputs1[index - 1].focus();
                    }
                });
            });
          pinInputs2.forEach((input, index) => {
               input.addEventListener('input', function () {
               if (input.value.length === 1 && index < pinInputs2.length - 1) {
                    pinInputs2[index + 1].focus();
               }
               });

               input.addEventListener('keydown', function (event) {
               if (event.key === 'Backspace' && input.value.length === 0 && index > 0) {
                    pinInputs2[index - 1].focus();
               }
               });
          });

          let condition = document.getElementById("condition");
          if (condition != ""){
               setTimeout(() => {
                    condition.style.display = "none";
               }, 5000);
          } else if (condition == "Pin Update Successful"){
               setTimeout(() => {
                    condition.style.display = "none"
                    location.href = "./profile.php"
               }, 5000);
          }
     </script>
</body>
</html>