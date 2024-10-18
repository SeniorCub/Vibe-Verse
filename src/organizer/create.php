<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

     include "connect.php"; // Include the database connection
     include "header.php";
     header("Content-Type: application/json");

     // Decode the incoming JSON data
     $data = json_decode(file_get_contents('php://input'), true);

    $organizerEmail = trim($data['email1']);
     $eventTitle = trim($data['eventTitle']);
     $eventLocation = trim($data['eventLocation']);
     $eventDescription = trim($data['eventDescription']);
     $eventStart = trim($data['eventStart']);
     $eventEnd = trim($data['eventEnd']);
     $flyer = $data['flyer'];
     $category = $data['category'];
     $emails = $data['emails'];

     // Check if Fields are empty
     if (!isset($organizerEmail) && 
          !isset($eventTitle) &&
          !isset($eventLocation) &&
          !isset($eventDescription) &&
          !isset($eventStart) &&
          !isset($eventEnd) &&
          !isset($flyer) &&
          !isset($category) &&
          !isset($emails)
     ) {
          echo json_encode(['message' => "All fields are required!."]);
      } elseif (!isset($organizerEmail)) {
          echo json_encode(['message' => "Organizer Email is required!."]);
      } elseif (!isset($eventTitle)) {
          echo json_encode(['message' => "Event Title is required!."]);
      } elseif (!isset($eventLocation)) {
          echo json_encode(['message' => "Event Location is required!."]);
     } elseif (!isset($eventDescription)) {
          echo json_encode(['message' => "Event Description is required!."]);
     } elseif (!isset($eventStart)) {
          echo json_encode(['message' => "Event Start Date is required!."]);
     } elseif (!isset($eventEnd)) {
          echo json_encode(['message' => "Event End Date is required!."]);
     } elseif (!isset($flyer)) {
          echo json_encode(['message' => "Event Flyer is required!."]);
     } elseif (!isset($category)) {
               echo json_encode(['message' => "Event Category is required!."]);
     } elseif (!isset($emails)) {
          echo json_encode(['message' => "Emails are required!."]);
     } 

          $perm_file = $_FILES["flyer"]["name"];
          $tmp_file = $_FILES["flyer"]["tmp_name"];
        
        
        // Prepare the SQL query with placeholders
        $query = "INSERT INTO `party`(`organizerEmail`, `eventTitle`, `eventLocation`, `eventDescription`, `eventStart`, `eventEnd`, `flyer`, `category`, `emails`) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        move_uploaded_file($tmp_file, "Uploads/$perm_file");

      $json_category = json_encode($category);
      $json_emails = json_encode($emails);

      // Execute the query
     // Create a prepared statement
     if ($stmt = $conn->prepare($query)) {
          $stmt->bind_param("sssssssss", $organizerEmail, $eventTitle, $eventLocation, $eventDescription, $eventStart, $eventEnd, $flyer, $ticketTypes, $emails);
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