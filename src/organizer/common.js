// Define constants for API endpoints
const API_BASE_URL = "http://localhost/Vibe-Verse/Apis";

// Function to get user information
async function getInfo() {
    try {
        const token = await getTokenFromLocalStorage();

        // Ensure token is available
        if (!token) {
            throw new Error("Token not found in local storage");
        }

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
        }
    } catch (error) {
        console.error("Fetch Error in getInfo:", error);
    }
}

// Function to check user status (logout)
async function logoutUser() {
     try {
        const token = await getTokenFromLocalStorage();

         const response = await fetch('http://localhost/Vibe-Verse/Apis/logout.php', {
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
             console.log('Logout successful:', data.message);
              // Redirect based on role after showing the message for 5 seconds
            setTimeout(() => {
               window.location.href = data.url;
           }, 5000)
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

// Example usage
// Uncomment the following lines to test the functions
getInfo();