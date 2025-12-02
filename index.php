<?php
require_once __DIR__ . '/config/Database.php';
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
require_once __DIR__ . '/controller/UserController.php';
require_once __DIR__ . '/controller/CategoryController.php';
require_once __DIR__ . '/controller/PublisherController.php';
require_once __DIR__ . '/controller/GameController.php';
require_once __DIR__ . '/controller/WishlistController.php';
require_once __DIR__ . '/controller/PaymentController.php';
require_once __DIR__ . '/controller/LibraryController.php';

$database = new Database();
$db = $database->getConnection();

// Configure session
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => '', // Default to current domain
        'secure' => false, // Set to true if using HTTPS
    ]);
    session_start();
}

// Allow CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$userController = new UserController($db);
$categoryController = new CategoryController($db);
$publisherController = new PublisherController($db);
$gameController = new GameController($db);
$libraryController = new LibraryController($db);


$base_path = '/BTL_LTW/BTL_LTW_BE';
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
error_log("Original URI: " . $request_uri);

// Remove base path
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}
error_log("After Base Path: " . $request_uri);

// Remove 'index.php' from the request URI if present
$request_uri = preg_replace('|^/index\.php|', '', $request_uri);
error_log("After index.php strip: " . $request_uri);

$uri = array_values(array_filter(explode('/', trim($request_uri, '/'))));
error_log("Parsed URI Array: " . print_r($uri, true));




// Handle library route
if (isset($uri[0]) && $uri[0] === 'library') {
    checkAuth(); // Ensure user is authenticated
    $libraryController->getUserLibrary();
    exit();
}

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

// Handle user update (no ID in URL, gets from session)
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[0]) && $uri[0] === 'users') {
    if (isset($uri[1]) && $uri[1] === 'password') {
        // Handle password update
        $userController->updatePassword();
    } else if (!isset($uri[1]) || $uri[1] === 'me') {
        // Handle regular user info update
        $userController->update();
    }
    exit();
}



// Check authentication for protected routes
if (isset($uri[0]) && in_array($uri[0], ['users', 'publishers', 'categories', 'games'])) {
    // Allow public access to GET /games and GET /games/{id}
    if ($uri[0] === 'games' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Public access allowed
    } 
    // Allow public access to GET /users/{id} and GET /users/{id}/games
    elseif ($uri[0] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1])) {
        // Public access allowed
    }
    else {
        checkAuth();
    }
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
    elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $gameController->getAll();
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

// Handle payment endpoint
if (isset($uri[0]) && $uri[0] === 'payments' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentController = new PaymentController($db);
    $paymentController->processPayment();
    exit();
}

// Handle wishlist endpoints
if (isset($uri[0]) && $uri[0] === 'wishlists') {
    $wishlistController = new WishlistController($db);
    
    // Get all wishlist names for current user: GET /wishlists
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $wishlistController->getUserWishlistNames();
    }
    // Create a new wishlist: POST /wishlists
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($uri[1])) {
        $wishlistController->createWishlist();
    } 
    // Get games from wishlist: GET /wishlists/{wishlist_name}/games
    else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games') {
        $wishlistName = urldecode($uri[1]);
        $wishlistController->getWishlistGames($wishlistName);
    }
    // Add game to wishlist: POST /wishlists/{wishlist_name}/games
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games') {
        $wishlistName = urldecode($uri[1]);
        $wishlistController->addGameToWishlist($wishlistName);
    }
    // Remove game from wishlist: DELETE /wishlists/{wishlist_name}/games/{game_id}
    else if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games' && isset($uri[3])) {
        $wishlistName = urldecode($uri[1]);
        $gameId = $uri[3];
        $wishlistController->removeGameFromWishlist($wishlistName, $gameId);
    }
    else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Not Found']);
    }
    exit();
}

// Handle user endpoints
if (isset($uri[0]) && $uri[0] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $userController->getOne($uri[1]);
        } else {
            $userController->getAll();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'avatar') {
        $userController->uploadAvatar($uri[1]);
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
