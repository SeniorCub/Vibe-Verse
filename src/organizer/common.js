const requestOptions = {
  method: "POST",
  headers: {
    Authorization: "Bearer 202400003", // Directly set headers as an object
  },
  body: null, // Set to null if you're not sending any body content
  redirect: "follow",
};

fetch("http://localhost/Vibe-Verse/Apis/session.php", requestOptions)
  .then((response) => response.json())
  .then((result) => {console.log(result)
     localStorage.setItem('token', result.data.token)
  })
  .catch((error) => console.error("Error:", error));

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
