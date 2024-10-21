// Get User Details
async function getUserDetails() {
     try {
       const response = await fetch("http://localhost/Vibe-Verse/Apis/user.php", {
         method: "GET",
         headers: {
           "Content-Type": "application/json",
         },
         credentials: "include", // This ensures cookies (session ID) are sent with the request
       });
   
       const data = await response.json();
   
       if (data.success) {
         const user = data.user;
         console.log("User details:", user);
       } else {
         console.log("Error:", data.message);
         // Redirect to login page if not logged in
         if (data.message === "Please log in.") {
           window.location.href = "login.html";
         }
       }
     } catch (error) {
       console.error("Error fetching user details:", error);
     }
   }
   
   // Call the function when the page loads
   window.onload = getUserDetails;
   

// Logout User
async function checkUserStatus() {
  try {
    const response = await fetch(
      "http://localhost/Vibe-Verse/Apis/logout.php",
      {
        method: "POST", // Use POST if you need to handle logout
        headers: {
          "Content-Type": "application/json",
        },
        // If you want to logout, include body like this:
        body: JSON.stringify({ logout: true }),
      }
    );

    const data = await response.json();
    if (data.success) {
      console.log("User Status:", data.data.condition);
    } else {
      console.error("Error:", data.message);
    }
  } catch (error) {
    console.error("Fetch Error:", error);
  }
}
