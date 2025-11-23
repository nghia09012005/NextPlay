<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controller/UserController.php';
require_once __DIR__ . '/controller/CategoryController.php';
require_once __DIR__ . '/controller/PublisherController.php';
require_once __DIR__ . '/controller/GameController.php';

$database = new Database();
$db = $database->getConnection();


$userController = new UserController($db);
$categoryController = new CategoryController($db);
$publisherController = new PublisherController($db);
$gameController = new GameController($db);


$base_path = '/Assignment/NextPlay';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = str_replace($base_path, '', $request_uri);

// Remove 'index.php' from the request URI if present
$request_uri = preg_replace('|^/index\.php|', '', $request_uri);

$uri = array_values(array_filter(explode('/', trim($request_uri, '/'))));

// // Debug
echo "Request URI: " . $request_uri . "<br>";
echo "URI array: ";
print_r($uri);
echo "<br>";




// auth middleware
function checkAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login first.']);
        exit();
    }
    return true;
}


// Public endpoints (no auth required)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[0])) {
    if ($uri[0] === 'users' && isset($uri[1]) && $uri[1] === 'signin') {
        $userController->signin();
        exit();
    } elseif ($uri[0] === 'users' && (!isset($uri[1]) || $uri[1] === 'register')) {
        $userController->register();
        exit();
    }
}

// Handle publisher registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[0]) && $uri[0] === 'publishers' && (!isset($uri[1]) || $uri[1] === 'register')) {
    $publisherController->register();
    exit();
}

// Check authentication for protected routes
if (isset($uri[0]) && in_array($uri[0], ['users', 'publishers', 'categories', 'games'])) {
    checkAuth();
}

// Handle game endpoints
if (isset($uri[0]) && $uri[0] === 'games') {
    // Handle /games/me to get current publisher's games
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && $uri[1] === 'me') {
        $gameController->getMyGames();
    }
    // Handle /games/{id} to get a specific game
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1])) {
        $gameController->getOne($uri[1]);
    }
    // Handle /games to get all games
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $gameController->getAll();
    }
    // Handle other methods
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $gameController->create();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[1]) && is_numeric($uri[1])) {
        $gameController->update($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && is_numeric($uri[1])) {
        $gameController->delete($uri[1]);
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// Handle publisher games endpoint
if (isset($uri[0]) && $uri[0] === 'publishers' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'games') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $gameController->getPublisherGames($uri[1]);
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// Handle other publisher endpoints
if (isset($uri[0]) && $uri[0] === 'publishers') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $publisherController->getOne($uri[1]);
        } else {
            $publisherController->getAllPublishers();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $publisherController->create();
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// Handle category endpoints
if (isset($uri[0]) && $uri[0] === 'categories') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $categoryController->getOne($uri[1]);
        } else {
            $categoryController->getAll();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryController->create();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[1]) && is_numeric($uri[1])) {
        $categoryController->update($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && is_numeric($uri[1])) {
        $categoryController->delete($uri[1]);
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// Handle user endpoints
if (isset($uri[0]) && $uri[0] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $userController->getUserById($uri[1]);
        } else {
            $userController->getAllUsers();
        }
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// If no route matches
http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Not Found']);



?>
