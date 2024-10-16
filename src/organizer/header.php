// Allow from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// If you're receiving an OPTIONS request, respond with a 200 status code and exit
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
     http_response_code(200);
     exit();
}