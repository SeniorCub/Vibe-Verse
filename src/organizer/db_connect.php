<?php
     class DbConnect {
          private $servername = "localhost";
          private $username = "root";
          private $password = ""; // Leave this empty if you have not set a password
          private $dbname = "tunetribe";
          private $port = 3307; // Add the new port number
          private $conn;

          public function connect() {
               // Initialize the connection to null
               $this->conn = null;

               try {
                    // Create a new PDO instance
                    $this->conn = new PDO("mysql:host={$this->servername};port={$this->port};dbname={$this->dbname}", $this->username, $this->password);
                    // Set the PDO error mode to exception
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               } catch (PDOException $e) {
                    // Handle connection error
                    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
                    http_response_code(500); // Internal Server Error
                    exit; // Stop further execution
               }
               
               return $this->conn; // Return the connection
          }
     }
?>
