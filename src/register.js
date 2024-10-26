const API_BASE_URL = "https://api.rhinoguards.co.uk";

let locationss, selectEventId, selectEventTitle, selectUsername, selectAmount, selectTicketType, selectImage, selectEmail, selectRef, flyerIma, organizerEmail;

document.addEventListener('DOMContentLoaded', async () => {
     const urlParams = new URLSearchParams(window.location.search);
     const eventId = urlParams.get('id');
     const headersList = {};
     let selectElement;

     try {
          const response = await fetch(`${API_BASE_URL}/User/event.php?id=${eventId}`, {
               method: "GET",
               headers: headersList
          });

          const data = await response.json();
          console.log(data);

          if (data.success) {
               const eventDetails = data.data;
               selectEventId = eventDetails.id;
               selectEventTitle = eventDetails.eventTitle;
               organizerEmail = eventDetails.organizerEmail;
               flyerIma = eventDetails.flyer;
               // Display event details
               document.getElementById('ticket').innerHTML += `
                <h3>${selectEventTitle}</h3>
                <p><strong>Location:</strong> ${eventDetails.eventLocation}</p>
                <p><strong>Description:</strong> ${eventDetails.eventDescription}</p>
                <p><strong>Start:</strong> ${eventDetails.eventStart}</p>
                <p><strong>End:</strong> ${eventDetails.eventEnd}</p>
            `;

               let categories = eventDetails.category;
               categories = JSON.parse(categories);

               selectElement = document.getElementById("level");

               if (categories.length % 2 === 0) {
                    for (let i = 0; i < categories.length; i += 2) {
                         const displayText = categories[i];
                         const value = categories[i + 1];

                         const option = document.createElement("option");
                         option.value = value; // Set the option value
                         option.textContent = displayText; // Set the display text
                         selectElement.appendChild(option); // Append the option
                    }
               } else {
                    updateCondition("Categories array length is not even, unable to pair display text and values.");
               }

               // Add event listener to handle selection changes
               if (selectElement) {
                    selectElement.addEventListener('change', (event) => {
                         const selectedIndex = event.target.selectedIndex; // Get the index of the selected option
                         selectAmount = event.target.value; // Get the selected value (amount)
                         selectTicketType = event.target.options[selectedIndex].text; // Get the selected text (ticket type)
                         document.getElementById('amount').value = selectAmount; // Set the amount in the input

                         console.log(`Selected Amount: ${selectAmount}, Selected Ticket Type: ${selectTicketType}`);
                    });
               }
          }

     } catch (error) {
          updateCondition("Error fetching event data: " + error.message);
     }

     // Function to get user location
     function getUserLocation() {
          if ("geolocation" in navigator) {
               navigator.geolocation.getCurrentPosition(
                    (position) => {
                         const latitude = position.coords.latitude;
                         const longitude = position.coords.longitude;
                         console.log(`Latitude: ${latitude}, Longitude: ${longitude}`);
                         locationss = latitude + ',' + longitude;
                    },
                    (error) => {
                         switch (error.code) {
                              case error.PERMISSION_DENIED:
                                   updateCondition("User denied the request for Geolocation.");
                                   break;
                              case error.POSITION_UNAVAILABLE:
                                   updateCondition("Location information is unavailable.");
                                   break;
                              case error.TIMEOUT:
                                   updateCondition("The request to get user location timed out.");
                                   break;
                              case error.UNKNOWN_ERROR:
                                   updateCondition("An unknown error occurred.");
                                   break;
                         }
                    }
               );
          } else {
               updateCondition("Geolocation is not supported by this browser.");
          }
     }

     // Call the function to get user location
     getUserLocation();
     console.log(locationss);
});

// Function to update the condition element
function updateCondition(message) {
     let condition = document.getElementById("condition");
     condition.textContent = message;
     condition.style.display = "block";
     setTimeout(() => {
          condition.style.display = "none";
     }, 5000);
}

// Updated payWithPaystack function
function payWithPaystack() {
     const email = document.getElementById('email');
     const amount = document.querySelector('#amount');
     const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

     if (email.value === "") {
          updateCondition('Error: Email cannot be empty.');
          return; // Exit the function early
     } else if (!emailPattern.test(email.value)) {
          updateCondition('Invalid email');
          return; // Exit the function early
     } else if (amount.value <= 0 || isNaN(amount.value)) {
          updateCondition('Invalid amount. Please enter a valid number.');
          return; // Exit the function early
     } else {
          let handler = PaystackPop.setup({
               key: 'pk_test_7c8afac2a29e69900f51137136528cd58d92ff5e',
               email: email.value,
               amount: amount.value * 100, // Convert amount to kobo
               currency: 'NGN',
               ref: 'PSK-' + Math.floor((Math.random() * 1000000000) + 1),
               metadata:{
                    custom_fields: [{
                        display_name: "Event Title",
                        variable_name: "event_title",
                        value: selectEventTitle // Using the event title from the fetched data
                    }, {
                        display_name: "Event Location",
                        variable_name: "event_location",
                        value: locationss // Using event location
                    }, {
                        display_name: "Ticket Type",
                        variable_name: "ticket_type",
                        value: selectTicketType // Using the selected ticket type
                    }]
                },
               callback: function (response) {
                    selectRef = response.reference; // Store the reference
               },
               onClose: function () {
                    updateCondition('Payment window closed.');
               }
          });

          handler.openIframe();
          document.querySelector('.next3').style.display = 'block';
          document.querySelector('.payNow').style.display = 'none';
     }
}

const certificate = document.getElementById('certificate');

function generateCertificate() {
     return new Promise((resolve, reject) => {
          const flyerImage = new Image();
          flyerImage.onload = function () {
               const canvas = document.createElement('canvas');
               canvas.width = 800;
               canvas.height = 600;
               const ctx = canvas.getContext('2d');
               selectUsername = document.getElementById('uname').value;
               selectEmail = document.getElementById('email').value;
               const userImage = document.getElementById('upload-pic');

               // Set the background color with a gradient
               const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
               gradient.addColorStop(0, '#f0f4f8');
               gradient.addColorStop(1, '#c3e5f5');
               ctx.fillStyle = gradient;
               ctx.fillRect(0, 0, canvas.width, canvas.height);

               // Add a fancy border
               ctx.strokeStyle = '#0a74da';
               ctx.lineWidth = 10;
               ctx.strokeRect(20, 20, canvas.width - 40, canvas.height - 40);

               // Add a shadow to the user image
               ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
               ctx.shadowBlur = 10;
               ctx.shadowOffsetX = 5;
               ctx.shadowOffsetY = 5;

               // Draw the user image
               ctx.drawImage(userImage, 50, 50, 150, 150);

               // Reset shadow for other elements
               ctx.shadowColor = 'transparent';
               ctx.shadowBlur = 0;
               ctx.shadowOffsetX = 0;
               ctx.shadowOffsetY = 0;

               // Draw the flyer image with rounded corners
               ctx.save();
               ctx.beginPath();
               ctx.moveTo(50, 250);
               ctx.arcTo(250, 250, 250, 450, 20);
               ctx.arcTo(250, 450, 50, 450, 20);
               ctx.arcTo(50, 450, 50, 250, 20);
               ctx.arcTo(50, 250, 250, 250, 20);
               ctx.closePath();
               ctx.clip();
               ctx.drawImage(flyerImage, 50, 250, 200, 200);
               ctx.restore();

               // Add the party name in a stylish font
               ctx.font = 'bold 40px Recursive';
               ctx.fillStyle = '#0a74da';
               ctx.textAlign = 'center';
               ctx.textBaseline = 'middle';
               ctx.fillText(selectEventTitle, canvas.width / 2, 100);

               // Add a stylish header
               ctx.font = 'bold 50px Recursive';
               ctx.fillStyle = '#333';
               ctx.fillText('PROOF OF REGISTRATION', canvas.width / 2, 180);

               // Add the username and email
               ctx.font = '30px Recursive';
               ctx.fillStyle = '#333';
               ctx.fillText(`Name: ${selectUsername}`, canvas.width / 2, 300);
               ctx.fillText(`Email: ${selectEmail}`, canvas.width / 2, 340);

               // Add the ticket type
               ctx.fillText(`Ticket Type: ${selectTicketType}`, canvas.width / 2, 380);

               // Create a data URL for the generated certificate
               const certificateDataUrl = canvas.toDataURL('image/png');
               resolve(certificateDataUrl);
          };
          flyerImage.src = `../${flyerIma}`;
     });
}

document.querySelector('.download').addEventListener('click', async () => {
     try {
          const certificateUrl = await generateCertificate();

          const canvas = document.createElement('canvas');
          canvas.width = 800;
          canvas.height = 600;
          const ctx = canvas.getContext('2d');

          const certificateImage = new Image();
          certificateImage.src = certificateUrl;

          // Wait until the certificate image is loaded
          certificateImage.onload = () => {
               ctx.drawImage(certificateImage, 0, 0, canvas.width, canvas.height);

               // Draw the QR code on the canvas
               const qrCodeCanvas = document.querySelector('#qrcode canvas');
               ctx.drawImage(qrCodeCanvas, canvas.width - 200, canvas.height - 200, 200, 200);

               // Download the combined image
               const combinedImageURL = canvas.toDataURL();
               const combinedImageLink = document.createElement('a');
               combinedImageLink.href = combinedImageURL;
               combinedImageLink.download = 'certificate-and-qrcode.jpg';
               combinedImageLink.click();

               document.querySelector('.next4').style.display = 'block';
               document.querySelector('.download').style.display = 'none';
          };
     } catch (error) {
          updateCondition('Error: ' + error.message);
          console.error('Download failed:', error);
     }
});

// Next and Back on Forms
function btn_next1() {
     const level = document.getElementById('level');
     const form1 = document.querySelector('.form1');
     const form2 = document.querySelector('.form2');
     const line2 = document.querySelector('.line2');

     if (level.value === "") {
          updateCondition('Please select an option for "Are you for regular?"');
     } else {
          line2.style.display = 'block';
          form1.style.left = '-120vw';
          form2.style.left = '20vw';
     }
}

function btn_next2() {
     const form2 = document.querySelector('.form2');
     const form3 = document.querySelector('.form3');
     const line3 = document.querySelector('.line3');
     const uname = document.getElementById('uname');

     if (uname.value == "") {
          updateCondition('Error');
     } else {
          line3.style.display = 'block';
          form2.style.left = '-120vw';
          form3.style.left = '20vw';
          document.querySelector('.next3').style.display = 'none';
          selectUsername = document.getElementById('uname').value;
     }
}

function btn_back2() {
     const form1 = document.querySelector('.form1');
     const form2 = document.querySelector('.form2');
     const line2 = document.querySelector('.line2');

     line2.style.display = 'none';
     form1.style.left = '20vw';
     form2.style.left = '120vw';
}

function btn_next3() {
     const form3 = document.querySelector('.form3');
     const form4 = document.querySelector('.form4');
     const line4 = document.querySelector('.line4');

     line4.style.display = 'block';
     form3.style.left = '-120vw';
     form4.style.left = '20vw';
     document.querySelector('.next4').style.display = 'none';
}

function btn_back3() {
     const form2 = document.querySelector('.form2');
     const form3 = document.querySelector('.form3');
     const line3 = document.querySelector('.line3');

     line3.style.display = 'none';
     form2.style.left = '20vw';
     form3.style.left = '120vw';
}

function btn_back4() {
     const form3 = document.querySelector('.form3');
     const form4 = document.querySelector('.form4');
     const line4 = document.querySelector('.line4');

     line4.style.display = 'none';
     form3.style.left = '20vw';
     form4.style.left = '120vw';
}

function upload() {
     document.querySelector("#file").click();
}
function previewImage() {
     const input = document.querySelector("#file");
     const file = input.files[0];
     const reader = new FileReader();

     reader.addEventListener("load", function () {
          const uploadPic = document.querySelector("#profile1");
          uploadPic.innerHTML = `<img id="upload-pic" src="${reader.result}" alt="">`;
          selectImage = reader.result; // Store the base64 image data
     });

     if (file) {
          reader.readAsDataURL(file);
     }
}

function generateQRCode() {
     const text = document.getElementById("email").value;
     const qrcodeContainer = document.getElementById("qrcode");
     qrcodeContainer.innerHTML = ""; // Clear previous QR code if any

     if (text.trim()) {
          new QRCode(qrcodeContainer, {
               text: text,
               width: 128,
               height: 128,
               colorDark: "#000000",
               colorLight: "#ffffff",
          });
     } else {
          updateCondition("Please enter some text to generate the QR code.");
     }
}

function registerNow() {

     console.log(organizerEmail, selectEventId, selectEventTitle, selectUsername,selectAmount, selectTicketType, selectImage, selectRef, selectEmail);

     const data = {
          organizerEmail: organizerEmail,
          eventId: selectEventId,
          eventTitle: selectEventTitle,
          username: selectUsername,
          amount: selectAmount,
          ticketType: selectTicketType,
          image: selectImage,
          ref: selectRef,
          email: selectEmail
     };

     fetch(`${API_BASE_URL}/User/register.php`, {
          method: "POST",
          headers: {
               "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
          credentials: "include",
     })
          .then((response) => {
               if (response.ok) {
                    return response.json();
               } else {
                    return response.text().then((text) => {
                         throw new Error(text);
                    });
               }
          })
          .then((result) => {
               if (result.success) {
                    // Show the server message in the condition element
                    console.log(result)
                    condition.textContent = result.message;
                    condition.style.display = "block";
                    // Hide the message and redirect after 5 seconds
                    setTimeout(() => {
                         window.location.href = result.url;
                    }, 5000);
               } else {
                    // Handle failure case by showing error message
                    condition.textContent = result.error || result.message || 'An error occurred!';
                    console.log(result)
                    condition.style.display = "block";
                    // Hide the message after 5 seconds
                    setTimeout(() => {
                         condition.style.display = "none";
                    }, 5000);
               }
          })
          .catch((error) => {
               console.error("Error:", error);
               // Display error in condition element
               condition.textContent = 'Failed to create event: ' + error.message;
               condition.style.display = "block";
               // Hide the message after 5 seconds
               setTimeout(() => {
                    condition.style.display = "none";
               }, 5000);
          });
}

// Event Listeners for form navigation
document.querySelector('.next1').addEventListener("click", btn_next1);
document.querySelector('.next2').addEventListener("click", btn_next2);
document.querySelector('.back2').addEventListener("click", btn_back2);
document.querySelector('.next3').addEventListener("click", btn_next3);
document.querySelector('.back3').addEventListener("click", btn_back3);
document.querySelector('.back4').addEventListener("click", btn_back4);
let condition = document.getElementById("condition");

if (condition != "") {
     setTimeout(() => {
          condition.style.display = "none";
     }, 5000);
}