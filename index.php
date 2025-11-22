<?php
require_once __DIR__ . '/controller/UserController.php';

$controller = new UserController();

// Lấy URL
$base_path = '/Assignment/NextPlay';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = str_replace($base_path, '', $request_uri);
$uri = explode('/', trim($request_uri, '/'));

// // Debug
// echo "Request URI: " . $request_uri . "<br>";
// echo "URI array: ";
// print_r($uri);
// echo "<br>";

//---------------------API-----------------------------

//---------------------User-----------------------------
// EndPoint: /users
// Method: GET
// Description: Get all users

// EndPoint: /users/{uid}
// Method: GET
// Description: Get user by id

// EndPoint: /users/register
// Method: POST
// Description: Register a new user

// EndPoint: /users/signin
// Method: POST
// Description: Authenticate user and create session


//---------------------Publisher-----------------------------
// EndPoint: /publishers
// Method: GET
// Description: Get all publishers

// EndPoint: /publishers/{uid}
// Method: GET
// Description: Get publisher by id

// EndPoint: /publishers
// Method: POST
// Description: Register a new publisher    


// Xử lý route
if (count($uri) > 0 && $uri[1] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[2])) {
        $controller->getAll();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[2])) {
        $controller->getOne($uri[2]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[2]) && $uri[2] === 'signin') {
        $controller->signin();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($uri[2]) || $uri[2] === 'register')) {
        $controller->register();
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} elseif (count($uri) > 0 && $uri[1] === 'publishers') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[2])) {
        $controller->getAll();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[2])) {
        $controller->getOne($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->register();
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}







?>
