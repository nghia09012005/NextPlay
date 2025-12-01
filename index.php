<?php
require_once __DIR__ . '/controller/UserController.php';

$controller = new UserController();

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Handle static files for PHP built-in server
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}

// Lấy URL
$base_path = '';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = str_replace($base_path, '', $request_uri);
$uri = explode('/', trim($request_uri, '/'));

//---------------------API-----------------------------

// Xử lý route
if (count($uri) > 0 && $uri[0] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $controller->getAll();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1])) {
        $controller->getOne($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && $uri[1] === 'signin') {
        $controller->signin();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($uri[1]) || $uri[1] === 'register')) {
        $controller->register();
    } elseif (isset($uri[1]) && is_numeric($uri[1])) {
        // Handle /users/{uid}/...
        $uid = $uri[1];
        if (isset($uri[2])) {
            if ($uri[2] === 'avatar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->uploadAvatar($uid);
            } elseif ($uri[2] === 'games' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->getGames($uid);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Not Found']);
            }
        } else {
             // /users/{uid} handled above
             http_response_code(404);
             echo json_encode(['message' => 'Not Found']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} elseif (count($uri) > 0 && $uri[0] === 'publishers') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $controller->getAll();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1])) {
        $controller->getOne($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->register();
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} elseif (count($uri) > 0 && $uri[0] === 'games') {
    require_once __DIR__ . '/controller/GameController.php';
    $gameController = new GameController();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $gameController->getOne($uri[1]);
        } else {
            $gameController->getAll();
        }
    } else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}







?>
