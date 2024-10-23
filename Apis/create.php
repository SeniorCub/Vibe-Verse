<?php
      include "connect.php";
      include "header.php";
      // Get all headers
      $header = getallheaders();
      error_log(print_r($header, true)); // Log the headers for debugging
      
      // Check for the Authorization header
      $token = isset($header['Authorization']) ? str_replace('Bearer ', '', $header['Authorization']) : null;
      
      if (!$token) {
           echo json_encode(["status" => 'error', "message" => "No token provided"]);
           exit();
      }
      $que = "SELECT * FROM `organizers` WHERE `token` = ?";
      
      // Execute the que
      if ($stmt = $conn->prepare($que)) {
           $stmt->bind_param("s", $token);
           if ($stmt->execute()) {
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $sessionemail = $user['email'];
           }
      }
     
     // Decode the incoming JSON data
     $data = json_decode(file_get_contents('php://input'), true);

     $organizerEmail = $sessionemail;
     $eventTitle = trim($data['eventTitle']);
     $eventLocation = trim($data['eventLocation']);
     $eventDescription = trim($data['eventDescription']);
     $eventStart = trim($data['eventStart']);
     $eventEnd = trim($data['eventEnd']);
     $flyer = $data['flyer'];
     $category = json_encode($data['category']);  // Encode arrays as JSON
     $emails = json_encode($data['emails']);      // Encode arrays as JSON

     // Check if fields are empty
     if (empty($organizerEmail)) {
          echo json_encode(['message' => "Organizer Email is required!.",  'url' => 'login.php']);
         header("location:login.php");
          exit;
     }
     if (empty($eventTitle)) {
          echo json_encode(['message' => "Event Title is required!."]);
          exit;
     }
     if (empty($eventLocation)) {
          echo json_encode(['message' => "Event Location is required!."]);
          exit;
     }
     if (empty($eventDescription)) {
          echo json_encode(['message' => "Event Description is required!."]);
          exit;
     }
     if (empty($eventStart)) {
          echo json_encode(['message' => "Event Start Date is required!."]);
          exit;
     }
     if (empty($eventEnd)) {
          echo json_encode(['message' => "Event End Date is required!."]);
          exit;
     }
     if (empty($flyer)) {
          echo json_encode(['message' => "Event Flyer is required!."]);
          exit;
     }
     if (empty($category)) {
          echo json_encode(['message' => "Event Category is required!."]);
          exit;
     }
     if (empty($emails)) {
          echo json_encode(['message' => "Emails are required!."]);
          exit;
     }

     // Store flyer image as a file on the server
     $flyerPath = "../Uploads/flyer_" . uniqid() . ".png";
     file_put_contents($flyerPath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $flyer)));

     // Prepare the SQL query
     $query = "INSERT INTO `party`(`organizerEmail`, `eventTitle`, `eventLocation`, `eventDescription`, `eventStart`, `eventEnd`, `flyer`, `category`, `emails`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

     // Execute the query
     if ($stmt = $conn->prepare($query)) {
          $stmt->bind_param("sssssssss", $organizerEmail, $eventTitle, $eventLocation, $eventDescription, $eventStart, $eventEnd, $flyerPath, $category, $emails);
          if ($stmt->execute()) {
              echo json_encode(['success' => true, 'message' => 'Event created successfully', 'url' => 'party.html']);
          } else {
              echo json_encode(['success' => false, 'error' => 'Failed to create event']);
          }
          $stmt->close();
      } else {
          echo json_encode(['success' => false, 'error' => 'Failed to prepare statement: ' . $conn->error]);
      }

     $conn->close();
?>