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
          alert("Please enter some text to generate the QR code.");
     }
}

function upload() {
     let input = document.querySelector("#file");
     input.click();
}

function previewImage() {
     const input = document.querySelector('#file');
     const file = input.files[0];
     const reader = new FileReader();

     reader.addEventListener('load', function() {
          const uploadPic = document.querySelector('#profile1');
          uploadPic.innerHTML = `<img id="upload-pic" src="${reader.result}" alt="">`
     });

     if (file) {
          reader.readAsDataURL(file);
     }
}

// Next on Form one
const next1 = document.querySelector('.next1').addEventListener("click", btn_next1);
// Next and Back on Form 2
const next2 = document.querySelector('.next2').addEventListener("click", btn_next2);
const back2 = document.querySelector('.back2').addEventListener("click", btn_back2);
// Next and Back on Form 3
const next3 = document.querySelector('.next3');
next3.addEventListener("click", btn_next3);
const back3 = document.querySelector('.back3').addEventListener("click", btn_back3);
// Back and Next on Form 4
const back4 = document.querySelector('.back4').addEventListener("click", btn_back4);
const next4 = document.querySelector('.next4');
next4.addEventListener("click", btn_next4);
const download = document.querySelector('.download');
const payNow = document.querySelector('.payNow');

let amountt = document.querySelector('#amount');
// Inputs on Form 1
let level = document.querySelector('#level');
let lastName = document.querySelector('#last_name');
let userName = document.querySelector('#user_name');
// Inputs on Form 2
let email = document.querySelector('#email');
let emaill = document.querySelector('#email');
let uname = document.querySelector('#uname');
let uploadPic = document.querySelector('#upload-pic')
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

function payWithPaystack() {
     if (email.value == "") {
          alert('Error')
     } else if (!emailPattern.test(email.value) ) {
          alert('Invalid email uname');
     }else{
          let handler = PaystackPop.setup({
          key: 'pk_test_7c8afac2a29e69900f51137136528cd58d92ff5e',
          email: emaill.value,
          amount: amountt.value * 100,
          currency: 'NGN',
          ref: 'PSK-' + Math.floor((Math.random() * 1000000000) + 1),
          metadata: {
               custom_fields: [
                    {
                         display_name: "Manual",
                         variable_name: "manual",
                         value: 'Tuition Fees'
                    }
                    // },
                    // {
                    //      display_name: "Amount",
                    //      variable_name: "amount",
                    //      value: amountt.value
                    // },
                    // {
                    //      display_name: "Payment Type",
                    //      variable_name: "payment_type",
                    //      value: full.checked ? 'Full Payment' : 'Part Payment'
                    // }
               ]
          },
          callback: function(response) {
               handlePaymentResponse(response);
          },
          onClose: function() {
               alert('Payment window closed.');
          }
          });
          handler.openIframe();
          next3.style.display = 'block';
          payNow.style.display = 'none';
     }
}

// Step 1
function btn_next1() {
     const level = document.getElementById('level'); // Get the level select element
     const form1 = document.querySelector('.form1')
     const form2 = document.querySelector('.form2')
     const line2 =document.querySelector('.line2')

     if (level.value === "") { // Check if the user selected an option
     alert('Please select an option for "Are you for regular?"');
     } else {
     line2.style.display = 'block'
          form1.style.left = '-120vw'
          form2.style.left = '20vw'
     }
}

// Step 2
function btn_next2() {
     const form2 = document.querySelector('.form2')
     const form3 = document.querySelector('.form3')
     const line3 =document.querySelector('.line3')

     if (uname.value == "") {
          alert('Error')
     } else {
          line3.style.display = 'block';
          form2.style.left = '-120vw';
          form3.style.left = '20vw';
          next3.style.display = 'none';
     }
};

function btn_back2() {
     const form1 = document.querySelector('.form1')
     const form2 = document.querySelector('.form2')
     const line2 =document.querySelector('.line2')

    
     line2.style.display = 'none'
     form1.style.left = '20vw'
     form2.style.left = '120vw'
};

// Step 3
function btn_back3() {
     const form2 = document.querySelector('.form2');
     const form3 = document.querySelector('.form3');
     const line3 =document.querySelector('.line3');

     line3.style.display = 'none';
     form2.style.left = '20vw' ;
     form3.style.left = '120vw';
};

function btn_next3() {
     const form3 = document.querySelector('.form3')
     const form4 = document.querySelector('.form4')
     const line4 =document.querySelector('.line4')
    
     line4.style.display = 'block' 
     form3.style.left = '-120vw'
     form4.style.left = '20vw'
     next4.style.display = 'none';
};

// Step 4
function btn_back4() {
     const form3 = document.querySelector('.form3')
     const form4 = document.querySelector('.form4')
     const line4 =document.querySelector('.line4')

     line4.style.display = 'none';
     form3.style.left = '20vw'
     form4.style.left = '120vw'
};

download.addEventListener('click', () => {
     try {
          // Change 'path/to/your/ticket/file.jpg' to the actual path of your ticket image file
          var ticketUrl = 'images/ticket.jpg';
          
          // Create links for ticket and QR code
          var ticketLink = document.createElement('a');
          var qrCodeLink = document.createElement('a');
          
          // Set the href attribute to the ticket image file URL
          ticketLink.href = ticketUrl;
          
          // Set the download attribute to force download the ticket image file
          ticketLink.download = 'ticket.jpg';
          
          // Generate the QR code data URL
          var qrCodeCanvas = document.querySelector('#qrcode canvas');
          if (qrCodeCanvas) {
               qrCodeLink.href = qrCodeCanvas.toDataURL("image/png");
               qrCodeLink.download = 'qrcode.png';
          } else {
               throw new Error("QR code generation failed.");
          }
          
          // Append the links to the document body
          document.body.appendChild(ticketLink);
          document.body.appendChild(qrCodeLink);
          
          // Trigger a click event on the ticket and QR code links
          ticketLink.click();
          qrCodeLink.click();
          
          // Remove the links from the document body
          document.body.removeChild(ticketLink);
          document.body.removeChild(qrCodeLink);
     
          // Show the Finish button after successful download
          next4.style.display = 'block';
          download.style.display = 'none';
     } catch (error) {
     // Handle any errors that occur during download
     alert('Error: ' + error.message);
     console.error('Download failed:', error);
     }
});

function btn_next4() {
     if (ckbox.checked) {
          // For external link
          window.location.href = "https://www.github.com/seniorcub"
     } else {
          alert('Error')
     }
};