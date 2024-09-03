// Get the certificate element
const certificate = document.getElementById('certificate');

function generateCertificate() {
     const username = document.getElementById('uname').value;
     const email = document.getElementById('email').value;
     const amount = document.getElementById('amount').value;
     const level = document.getElementById('level').value;
     const partyName = `Now Sounds`;
     const userImage = document.getElementById('upload-pic');
     const flyerImage = new Image();
     flyerImage.src = './images/party/party1.jpg'; // Replace with the actual flyer image URL
   
     const canvas = document.createElement('canvas');
     canvas.width = 800;
     canvas.height = 600;
     const ctx = canvas.getContext('2d');
   
     // Set the background color
     ctx.fillStyle = '#fff';
     ctx.fillRect(0, 0, canvas.width, canvas.height);
   
     // Add a border
     ctx.strokeStyle = '#000';
     ctx.lineWidth = 5;
     ctx.strokeRect(10, 10, canvas.width - 20, canvas.height - 20);
   
     // Add the user image
     ctx.drawImage(userImage, 50, 50, 100, 100);
   
     // Add the flyer image
     ctx.drawImage(flyerImage, 50, 200, 200, 200);
   
     // Add the party name
     ctx.font = '30px Arial';
     ctx.fillStyle = '#000';
     ctx.textAlign = 'center';
     ctx.textBaseline = 'middle';
     ctx.fillText(partyName, canvas.width / 2, 150);
   
     // Add the level
     ctx.font = '30px Arial';
     ctx.fillStyle = '#000';
     ctx.textAlign = 'center';
     ctx.textBaseline = 'middle';
     ctx.fillText(level, canvas.width / 2, 250);
   
     // Add a header
     ctx.font = '50px Arial';
     ctx.fillStyle = '#000';
     ctx.textAlign = 'center';
     ctx.textBaseline = 'middle';
     ctx.fillText('CERTIFICATE OF COMPLETION', canvas.width / 2, 100);
   
     // Add user data
     ctx.font = '30px Arial';
     ctx.fillStyle = '#000';
     ctx.textAlign = 'center';
     ctx.textBaseline = 'middle';
     ctx.fillText(`Name: ${username}`, canvas.width / 2, 350);
     ctx.fillText(`Email: ${email}`, canvas.width / 2, 400);
     ctx.fillText(`Amount: ${amount}`, canvas.width / 2, 450);
   
     // Save the certificate as an image
     const certificateUrl = canvas.toDataURL();
     return certificateUrl;
   }

// Next and Back on Forms
function btn_next1() {
    const level = document.getElementById('level');
    const form1 = document.querySelector('.form1');
    const form2 = document.querySelector('.form2');
    const line2 = document.querySelector('.line2');

    if (level.value === "") {
        alert('Please select an option for "Are you for regular?"');
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
        alert('Error');
    } else {
        line3.style.display = 'block';
        form2.style.left = '-120vw';
        form3.style.left = '20vw';
        document.querySelector('.next3').style.display = 'none';
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
    let input = document.querySelector("#file");
    input.click();
}

function previewImage() {
    const input = document.querySelector('#file');
    const file = input.files[0];
    const reader = new FileReader();

    reader.addEventListener('load', function() {
        const uploadPic = document.querySelector('#profile1');
        uploadPic.innerHTML = `<img id="upload-pic" src="${reader.result}" alt="">`;
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
        alert("Please enter some text to generate the QR code.");
    }
}

function payWithPaystack() {
    const email = document.getElementById('email');
    const amount = document.querySelector('#amount');
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    if (email.value == "") {
        alert('Error');
    } else if (!emailPattern.test(email.value)) {
        alert('Invalid email');
    } else {
        let handler = PaystackPop.setup({
            key: 'pk_test_7c8afac2a29e69900f51137136528cd58d92ff5e',
            email: email.value,
            amount: amount.value * 100,
            currency: 'NGN',
            ref: 'PSK-' + Math.floor((Math.random() * 1000000000) + 1),
            metadata: {
                custom_fields: [{
                    display_name: "Manual",
                    variable_name: "manual",
                    value: 'Tuition Fees'
                }]
            },
            callback: function(response) {
                handlePaymentResponse(response);
            },
            onClose: function() {
                alert('Payment window closed.');
            }
        });
        handler.openIframe();
        document.querySelector('.next3').style.display = 'block';
        document.querySelector('.payNow').style.display = 'none';
    }
}

function btn_next4() {
    const ckbox = document.querySelector('#ckbox');
    if (ckbox.checked) {
        // For external link
        window.location.href = "https://www.github.com/seniorcub";
    } else {
        alert('Error');
    }
}

document.querySelector('.download').addEventListener('click', () => {
     try {
       const certificateUrl = generateCertificate(); // Call the generateCertificate function and store the URL
   
       const canvas = document.createElement('canvas');
       canvas.width = 800;
       canvas.height = 600;
       const ctx = canvas.getContext('2d');
   
       // Draw the certificate on the canvas
       const certificateImage = new Image();
       certificateImage.onload = function() {
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
       certificateImage.src = certificateUrl;
     } catch (error) {
       alert('Error: ' + error.message);
       console.error('Download failed:', error);
     }
   });

// Event Listeners for form navigation
document.querySelector('.next1').addEventListener("click", btn_next1);
document.querySelector('.next2').addEventListener("click", btn_next2);
document.querySelector('.back2').addEventListener("click", btn_back2);
document.querySelector('.next3').addEventListener("click", btn_next3);
document.querySelector('.back3').addEventListener("click", btn_back3);
document.querySelector('.back4').addEventListener("click", btn_back4);
document.querySelector('.next4').addEventListener("click", btn_next4);