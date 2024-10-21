<?php
// CORS Headers
// Allow from any origin
// header("Access-Control-Allow-Origin: *");
// Allow from specific origin
header("Access-Control-Allow-Origin: http://127.0.0.1:5501");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// If you're receiving an OPTIONS request, respond with a 200 status code and exit
// Preflight check for OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Send a 200 OK response and exit for preflight checks
    http_response_code(200);
    exit();
}

// Enable error reporting (only for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>