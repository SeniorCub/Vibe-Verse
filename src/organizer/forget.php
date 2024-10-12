<?php
    include "connect.php";
    include "logout.php";

    session_start();

    $condition = "";

    if (isset($_SESSION['email'])) {
        $sessionemail = $_SESSION['email'];
    } else {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? null;
        $code = $_POST['code'] ?? null; // Submitted code
        $newPassword = $_POST['password'] ?? null; // New password
        $confirmPassword = $_POST['confirm-password'] ?? null; // Confirm password

        // Step 1: Verify email and send code
        if ($email) {
            $select = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `email` = '$email'");
            if (mysqli_num_rows($select) > 0) {
                $generatedCode = rand(100000, 999999); // Generate a 6-digit code
                $_SESSION['code'] = $generatedCode;
                $_SESSION['email'] = $email;
                // Simulate sending email with the code
                $condition = "Code sent to email: $email";
                echo "<script>alert('Code sent successfully!');</script>";
            } else {
                $condition = "Email not found!";
                echo "<script>alert('Email not found!');</script>";
            }
        }

        // Step 2: Verify the submitted code
        if ($code && isset($_SESSION['code'])) {
            if ($code == $_SESSION['code']) {
                $condition = "Code verified successfully!";
                echo "<script>alert('Code verified successfully!');</script>";
            } else {
                $condition = "Invalid code. Please try again.";
                echo "<script>alert('Invalid code.');</script>";
            }
        }

        // Step 3: Change and confirm password
        if ($newPassword && $confirmPassword) {
            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $update = mysqli_query($conn, "UPDATE `organizers` SET `password`='$hashedPassword' WHERE `email` = '$sessionemail'");
                if ($update) {
                    $condition = "Password changed successfully!";
                    echo "<script>alert('Password changed successfully! Redirecting to login page...');</script>";
                    // Redirect after success
                    header("refresh:5;url=login.php");
                    exit();
                } else {
                    $condition = "Password update failed!";
                    echo "<script>alert('Password update failed!');</script>";
                }
            } else {
                $condition = "Passwords do not match!";
                echo "<script>alert('Passwords do not match!');</script>";
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
    <title>Forget Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        #condition {
            position: fixed;
            right: 0;
            top: 10vh;
            background-color: var(--white);
            padding: 10px 15px;
            border-radius: 15px 0 0 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <button type="button" class="back-btn" onclick="window.location.href='/src/organizer/login.html'">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </button>
    
    <div class="auth-container">
        <p id="condition"><?php echo $condition; ?></p>
        <h2>Forget Password</h2>
        <form id="forgetForm" action="" method="POST">
            <!-- Step 1: Request 6-digit code -->
            <div class="form-group step active">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit" name="submit" class="submit-btn mt-2">Request Code</button>
            </div>
            
            <!-- Step 2: Enter 6-digit code -->
            <div class="form-group step">
                <label for="code">6-digit Code:</label>
                <input type="number" id="code" name="code" required>
                <button type="submit" name="submit" class="submit-btn mt-2">Verify Code</button>
            </div>
            
            <!-- Step 3: Change password -->
            <div class="form-group step">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="submit" class="submit-btn mt-2">Confirm Password</button>
            </div>
            
            <!-- Step 4: Confirm password -->
            <div class="form-group step">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
                <button type="submit" name="submit" class="submit-btn mt-2">Change Password</button>
            </div>
        </form>
    </div>

    <script src="../OfflineResources/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/2a49fbdbb8.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = document.querySelectorAll('.step');
            const submitButtons = document.querySelectorAll('.submit-btn');
        
            submitButtons.forEach((button, index) => {
                button.addEventListener('click', function(e) {
                    // Prevent default form submission
                    e.preventDefault();

                    // Move between steps
                    if (index < steps.length - 1) {
                        steps[index].classList.remove('active');
                        steps[index + 1].classList.add('active');
                        submitButtons[index].style.display = 'none';
                        submitButtons[index + 1].style.display = 'block';
                    }
                });
            });
        });

        let condition = document.getElementById("condition").innerText;
        if (condition) {
            setTimeout(() => {
                document.getElementById("condition").style.display = "none";
            }, 5000);
        }
    </script>
</body>
</html>
