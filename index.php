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
require_once __DIR__ . '/controller/NewsController.php';
require_once __DIR__ . '/controller/ReviewController.php';
require_once __DIR__ . '/controller/FeedbackController.php';
require_once __DIR__ . '/controller/PageController.php';
require_once __DIR__ . '/controller/PageContentController.php';
require_once __DIR__ . '/controller/ContactController.php';

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
$newsController = new NewsController($db);
$newsController = new NewsController($db);
$reviewController = new ReviewController($db);
$feedbackController = new FeedbackController($db);
$pageController = new PageController();
$contactController = new ContactController($db);
$pageContentController = new PageContentController($db);

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

// // Debug
// echo "Request URI: " . $request_uri . "<br>";
// echo "URI array: ";
// print_r($uri);
// echo "<br>";



error_log("Parsed URI Array: " . print_r($uri, true));

// Handle review routes
if (isset($uri[0]) && $uri[0] === 'reviews') {
    // GET /reviews/news/{news_id}
    if (isset($uri[1]) && $uri[1] === 'news' && isset($uri[2]) && is_numeric($uri[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $reviewController->getNewsReviews($uri[2]);
            exit();
        }
    }
    // GET /reviews/customer/{customer_id}
    if (isset($uri[1]) && $uri[1] === 'customer' && isset($uri[2]) && is_numeric($uri[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $reviewController->getCustomerReviews($uri[2]);
            exit();
        }
    }
    // POST /reviews
    if (count($uri) === 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
        checkAuth();
        $reviewController->saveReview();
        exit();
    }
    // GET /reviews/average/{news_id}
    if (isset($uri[1]) && $uri[1] === 'average' && isset($uri[2]) && is_numeric($uri[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $reviewController->getNewsAverageRating($uri[2]);
            exit();
        }
    }
    // GET /reviews/check/{customer_id}/{news_id}
    if (isset($uri[1]) && $uri[1] === 'check' && isset($uri[2]) && is_numeric($uri[2]) && isset($uri[3]) && is_numeric($uri[3])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $reviewController->checkCustomerReview($uri[2], $uri[3]);
            exit();
        }
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
    exit();
}

// Handle feedback routes (Game Reviews)
if (isset($uri[0]) && $uri[0] === 'feedback') {
    // GET /feedback/game/{id}
    if (isset($uri[1]) && $uri[1] === 'game' && isset($uri[2]) && is_numeric($uri[2])) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $feedbackController->getGameReviews($uri[2]);
            exit();
        }
        // PUT /feedback/game/{id}
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            checkAuth();
            $feedbackController->updateReview($uri[2]);
            exit();
        }
        // DELETE /feedback/game/{id}
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            checkAuth();
            $feedbackController->deleteReview($uri[2]);
            exit();
        }
    }
    // POST /feedback
    if (count($uri) === 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
        checkAuth();
        $feedbackController->addReview();
        exit();
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
    exit();
}

// Handle DELETE review
if (isset($uri[0]) && $uri[0] === 'review' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && is_numeric($uri[2])) {
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        checkAuth();
        $reviewController->deleteReview($uri[1], $uri[2]);
        exit();
    }
}

// Handle library route
if (isset($uri[0]) && $uri[0] === 'library') {
    checkAuth();
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

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[0]) && $uri[0] === 'users') {
    if (isset($uri[1]) && $uri[1] === 'password') {
        $userController->updatePassword();
    } else if (!isset($uri[1]) || $uri[1] === 'me') {
        $userController->update();
    }
    exit();
}

// Check authentication for protected routes
if (isset($uri[0]) && in_array($uri[0], ['users', 'publishers', 'categories', 'games'])) {
    if ($uri[0] === 'games' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        // Public access allowed
    } elseif ($uri[0] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1])) {
        // Public access allowed
    } else {
        checkAuth();
    }
}

// Handle news endpoints
if (isset($uri[0]) && $uri[0] === 'news') {
    // GET /news/{id}/comments
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'comments') {
        $newsController->getNewsComments($uri[1]);
        exit();
    }
    // POST /news/{id}/comments
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'comments') {
        $newsController->addComment($uri[1]);
        exit();
    }
    // DELETE /news/{id}/comments
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'comments') {
        $newsController->deleteComment($uri[1]);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $newsController->getNewsById($uri[1]);
        } else {
            $newsController->getAllNews();
        }
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newsController->createNews();
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[1]) && is_numeric($uri[1])) {
        $newsController->updateNews($uri[1]);
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && is_numeric($uri[1])) {
        $newsController->deleteNews($uri[1]);
        exit();
    }
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
    exit();
}

// Handle game endpoints
if (isset($uri[0]) && $uri[0] === 'games') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && $uri[1] === 'me') {
        $gameController->getMyGames();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && is_numeric($uri[1])) {
        $gameController->getOne($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
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
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $wishlistController->getUserWishlistNames();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($uri[1])) {
        $wishlistController->createWishlist();
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games') {
        $wishlistName = urldecode($uri[1]);
        $wishlistController->getWishlistGames($wishlistName);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games') {
        $wishlistName = urldecode($uri[1]);
        $wishlistController->addGameToWishlist($wishlistName);
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1]) && isset($uri[2]) && $uri[2] === 'games' && isset($uri[3])) {
        $wishlistName = urldecode($uri[1]);
        $gameId = $uri[3];
        $wishlistController->removeGameFromWishlist($wishlistName, $gameId);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Not Found']);
    }
    exit();
}

// Handle page endpoints
if (isset($uri[0]) && $uri[0] === 'pages') {
    if (isset($uri[1])) {
        $pageController->handleRequest($_SERVER['REQUEST_METHOD'], 'pages/' . $uri[1]);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Page slug required']);
    }
    exit();
}

// Handle user endpoints
if (isset($uri[0]) && $uri[0] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1]) && $uri[1] === 'check_admin') {
        $userController->checkAdmin();
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($uri[1]) && is_numeric($uri[1])) {
            $userController->getOne($uri[1]);
        } else {
            $userController->getAll();
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'avatar') {
        $userController->uploadAvatar($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($uri[1]) && is_numeric($uri[1]) && isset($uri[2]) && $uri[2] === 'deposit') {
        $userController->deposit($uri[1]);
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    }
    exit();
}

// Handle contact endpoints
if (isset($uri[0]) && $uri[0] === 'contact') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contactController->createMessage();
        exit();
    }
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit();
}

// Handle content endpoints
if (isset($uri[0]) && $uri[0] === 'content') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $pageContentController->getAllContent();
        exit();
    }
}

if (isset($uri[0]) && $uri[0] === 'admin' && isset($uri[1]) && $uri[1] === 'content') {
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // checkAuth(); // Uncomment to enforce auth
        $pageContentController->updateContent();
        exit();
    }
}

// If no route matches
http_response_code(404);
echo json_encode(['status' => 'error', 'message' => 'Not Found']);
?>
