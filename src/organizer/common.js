// Define constants for API endpoints
const API_BASE_URL = "https://api.rhinoguards.co.uk";

// Function to get user information
async function getInfo() {
     try {
         const token = await getTokenFromLocalStorage();
         if (!token) throw new Error("Token not found in local storage");
 
         const result = await fetch(`${API_BASE_URL}/session.php`, {
             method: "POST",
             headers: {
                 "Authorization": `Bearer ${token}`,
                 "Content-Type": "application/json",
             },
         });
 
         const data = await result.json();
         if (data.status == "error") {
             console.log(data.url);
             window.location.href = data.url;
         } else {
             console.log(data);
 
             // Check if user is an admin
             if (data.data.isAdmin === 1) {
                 console.error("Access denied: User is not an admin.");
                 document.getElementsByTagName('body').innerHTML= "<h1>Access denied: You are not authorized to access the admin dashboard.</h1>";
                 setTimeout(() => {
                    logoutUser()
               }, 5000)
             } else {
                 // Populate user details
                 const displayName = data.data.name.length > 20 ? data.data.name.slice(0, 15) + "..." : data.data.name;
                 document.getElementById('user-name').innerHTML = displayName;
                 document.getElementById('user-email').innerHTML = data.data.email;
                 document.getElementById('loading').style.display = "none"; // Hide loading screen
                 document.getElementById('content').style.display = "block"; // Show content
             }
         }
     } catch (error) {
         console.error("Fetch Error in getInfo:", error);
     }
 } 


// Function to check user status (logout)
async function logoutUser() {
     try {
          const token = await getTokenFromLocalStorage();

          const response = await fetch(`${API_BASE_URL}/logout.php`, {
               method: 'POST',
               headers: {
                    "Authorization": `Bearer ${token}`,
                    'Content-Type': 'application/x-www-form-urlencoded', // Ensure correct form encoding for POST
               },
               body: new URLSearchParams({ logout: true }), // Send logout flag
               credentials: 'include', // Include cookies in the request
          });

          const data = await response.json();

          if (data.success) {
               window.location.href = data.url;
               localStorage.removeItem('token');
               console.log('Logout successful:', data.message);
          } else {
               console.error('Error:', data.message);
               // Redirect based on role after showing the message for 5 seconds
               setTimeout(() => {
                    window.location.href = data.url;
                    localStorage.removeItem('token');
               }, 5000)
          }
     } catch (error) {
          console.error('Fetch Error:', error);
     }
}


// Helper function to fetch data from API
async function fetchData(url, options) {
     const response = await fetch(url, options);

     // Check if the response is ok
     if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
     }

     return response.json();
}

// Helper function to get token from local storage
async function getTokenFromLocalStorage() {
     return localStorage.getItem('token');
}

// Wait for getInfo to complete before loading content
document.addEventListener("DOMContentLoaded", getInfo);

let condition = document.getElementById("condition");
if (condition.innerText != "") {
    setTimeout(() => {
        condition.style.display = "none";
    }, 5000);
}