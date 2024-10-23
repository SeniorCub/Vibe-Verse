<?php
// Connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Leave empty if there's no password
$dbname = "tunetribe";
$port = 3307; // Your specified port

// Connect to the database
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$condition = "";

// Check if form is submitted
if (isset($_POST["submit"])) {
    // Retrieve form data
    $name = mysqli_real_escape_string($conn, $_POST["organizationName"]);
    $phone = mysqli_real_escape_string($conn, $_POST["contactNumber"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $address = mysqli_real_escape_string($conn, $_POST["address"]);
    $website = mysqli_real_escape_string($conn, $_POST["website"]);
    $about = mysqli_real_escape_string($conn, $_POST["description"]);
    $pwd = mysqli_real_escape_string($conn, $_POST["password"]);
    $Conpwd = mysqli_real_escape_string($conn, $_POST["confirmPassword"]);

    // Validate fields
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($about) || empty($pwd) || empty($Conpwd)) {
        $condition = "All fields are required";
    } else {
        // Check if passwords match
        if ($pwd != $Conpwd) {
            $condition = "Password and Confirm Password do not match";
        } else {
            // Check if email or organization name already exists
            $checkQuery = "SELECT * FROM organizers WHERE email='$email' OR name='$name'";
            $result = mysqli_query($conn, $checkQuery);

            if (mysqli_num_rows($result) > 0) {
                $condition = "Email or Organization Name already exists";
            } else {
                // Hash the password
                $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

                // Insert organizer details into `organizers` table
                $insertQuery = "INSERT INTO organizers (name, phone, email, address, website, about, password) 
                                VALUES ('$name', '$phone', '$email', '$address', '$website', '$about', '$hashedPassword')";

                if (mysqli_query($conn, $insertQuery)) {
                    // Switch to another database to create the table
                    $userDbName = "user_events_" . preg_replace('/[^a-zA-Z0-9]/', '_', $email);
                    $createDbQuery = "CREATE DATABASE IF NOT EXISTS `$userDbName`";
                    
                    if (mysqli_query($conn, $createDbQuery)) {
                        // Connect to the newly created database
                        $userConn = mysqli_connect($servername, $username, $password, $userDbName, $port);

                        if ($userConn) {
                            // Create table for this organizer in the new database
                            $createTableQuery = "CREATE TABLE IF NOT EXISTS events (
                                id INT NOT NULL AUTO_INCREMENT,
                                eventTitle VARCHAR(255) NOT NULL,
                                ticketType VARCHAR(255) NOT NULL,
                                amount INT(11) NOT NULL,
                                eventId INT(11) NOT NULL,
                                username VARCHAR(255) NOT NULL,
                                image VARCHAR(255) NOT NULL,
                                email VARCHAR(255) NOT NULL,
                                ref VARCHAR(20) NOT NULL,
                                PRIMARY KEY (id)
                            )";

                            if (mysqli_query($userConn, $createTableQuery)) {
                                $condition = "Registration Successful";
                                header("Location: login.php");
                                exit();
                            } else {
                                $condition = "Error creating event table: " . mysqli_error($userConn);
                            }

                            mysqli_close($userConn); // Close the connection to the user-specific DB
                        } else {
                            $condition = "Error connecting to new user database";
                        }
                    } else {
                        $condition = "Error creating database: " . mysqli_error($conn);
                    }
                } else {
                    $condition = "Error registering organizer: " . mysqli_error($conn);
                }
            }
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
    <link rel="stylesheet" href="styless.css">
    <title>Organizer Registration</title>
    <style>
        #condition {
            position: fixed;
            right: 0;
            top: 5vh;
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

    <div class="auth-container registerFormContainer">
        <h2>Sign Up as an Organizer</h2>
        <p id="condition"><?php echo ($condition); ?></p>
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
        if (condition != "") {
            setTimeout(() => {
                condition.style.display = "none";
            }, 5000);
        } else if (condition == "Registration Successful") {
            setTimeout(() => {
                condition.style.display = "none";
            }, 5000);
            location.href = "./login.php"
        }
    </script>
</body>
</html>