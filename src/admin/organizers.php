<?php
include "connect.php";
include "logout.php";
session_start();

// Check if the session has a valid email
if (isset($_SESSION['email'])) {
    $sessionemail = $_SESSION['email'];
} else {
    header("location: login.php");
    exit();
}

$cookie_name = "user";

// Fetch the current organizer's details based on the session email
$selectOrganizer = mysqli_prepare($conn, "SELECT * FROM `organizers` WHERE `email` = ?");
mysqli_stmt_bind_param($selectOrganizer, "s", $sessionemail);
mysqli_stmt_execute($selectOrganizer);
$details = mysqli_fetch_assoc(mysqli_stmt_get_result($selectOrganizer));

// Fetch all organizers who are not admin
$organizers = mysqli_query($conn, "SELECT * FROM `organizers` WHERE `isAdmin` = 0");

// Handle form submission
if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    // Use prepared statement to avoid SQL injection
    $selectUser = mysqli_prepare($conn, "SELECT * FROM `organizers` WHERE `email` = ?");
    mysqli_stmt_bind_param($selectUser, "s", $email);
    mysqli_stmt_execute($selectUser);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($selectUser));

    if ($user) {
        // Set cookie with the email value
        $cookie_value = $email;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
        // Redirect or perform other actions if needed
         header("location: organize.php");
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
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link href="../output.css" rel="stylesheet">
    <title>Admin Dashboard</title>
</head>
<body>
     
     <nav class="fixed top-0 z-50 w-full border-b bg-primary border-white">
          <div class="px-3 py-3 lg:px-5 lg:pl-3">
               <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm  rounded-lg sm:hidden focus:outline-none focus:ring-2 text-gray-400 hover:bg-gray-700 focus:ring-gray-600">
                         <span class="sr-only">Open sidebar</span>
                         <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                         <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                         </svg>
                    </button>
                    <a href="/" class="flex ms-2 md:me-24">
                         <img src="../images/logo.png" class="h-8 me-3" alt="FlowBite Logo" />
                         <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-white">Tune Tribe</span>
                    </a>
               </div>
          </div>
     </nav>
   
     <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full border-r sm:translate-x-0 bg-primary border-gray-700" aria-label="Sidebar">
          <div class="h-full px-3 pb-4 overflow-y-auto bg-primary flex flex-col justify-between">
               <ul class="space-y-2 font-medium">
                    <!-- Dashboard -->
                    <li>
                         <a href="./" class="flex items-center p-2 rounded-lg text-white hover:bg-gray-700 group">
                              <svg class="w-5 h-5  transition duration-75 text-gray-400  group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                                   <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                                   <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                              </svg>
                              <span class="ms-3">Dashboard</span>
                         </a>
                    </li>
                    <!-- Organizers -->
                    <li>
                         <a href="./organizers.html" class="flex items-center p-2 rounded-lg text-white hover:bg-gray-700 group onnn">
                              <svg class="flex-shrink-0 w-5 h-5  transition duration-75 text-gray-400  group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 18">
                                   <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                              </svg>
                              <span class="flex-1 ms-3 whitespace-nowrap">Organizers</span>
                         </a>
                    </li>
                    <!-- recent Events -->
                    <li>
                         <a href="./recent.html" class="flex items-center p-2 rounded-lg text-white bg-gray-700 group">
                         <svg class="flex-shrink-0 w-5 h-5  transition duration-75 text-gray-400  group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                              <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                              <path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .49-.263l6.118-6.117a2.884 2.884 0 0 0-4.079-4.078l-6.117 6.117a.96.96 0 0 0-.263.491l-.679 3.4A.961.961 0 0 0 8.961 16Zm7.477-9.8a.958.958 0 0 1 .68-.281.961.961 0 0 1 .682 1.644l-.315.315-1.36-1.36.313-.318Zm-5.911 5.911 4.236-4.236 1.359 1.359-4.236 4.237-1.7.339.341-1.699Z"/>
                         </svg>
                         <span class="flex-1 ms-3 whitespace-nowrap">Recent Withdrawals</span>
                         <span class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium rounded-full bg-blue-900 text-blue-300">3</span>
                         </a>
                    </li>
                    <!-- Transaction History -->
                    <li>
                         <a href="./transactions.html" class="flex items-center p-2 rounded-lg text-white hover:bg-gray-700 group">
                              <svg class="flex-shrink-0 w-5 h-5  transition duration-75 text-gray-400  group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                 </svg>
                                 
                              <span class="flex-1 ms-3 whitespace-nowrap">Transactions</span>
                         </a>
                    </li>
               </ul>
               <div>
                    <a href="#" class="flex items-center mt-auto p-2 rounded-lg text-white hover:bg-gray-700 group">
                    <svg class="flex-shrink-0 w-5 h-5  transit20ion duration-75 text-gray-400  group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                         <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Logout</span>
                    </a>
               </div>
          </div>
     </aside>
   
     <div class="p-4 sm:ml-64 mt-14 space-y-2 bg-white">
        <h1 class="text-5xl font-bold text-gold">Organizers</h1>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                <thead class="text-xs uppercase bg-primary text-gold">
                    <tr>
                        <th scope="col" class="px-6 py-3">Organizer's name</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Phone Number</th>
                        <th scope="col" class="px-6 py-3">Total Revenue</th>
                        <th scope="col" class="px-6 py-3"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($organizer = mysqli_fetch_assoc($organizers)): ?>
                    <tr class="border-b bg-white border-gray-700">
                        <form action="" method="POST">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                <?php echo $organizer['name']; ?>
                            </th>
                            <td class="px-6 py-4">
                                <input type="text" name="email" class="border-0 p-0 bg-transparent" value="<?php echo $organizer['email']; ?>" readonly>
                            </td>
                            <td class="px-6 py-4"><?php echo $organizer['phone']; ?></td>
                            <td class="px-6 py-4"><?php echo $organizer['availabeBalance']; ?></td>
                            <td class="px-6 py-4 text-right">
                                <button type="submit" class="font-medium text-blue-600 hover:underline" name="submit">View</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
</body>
</html>