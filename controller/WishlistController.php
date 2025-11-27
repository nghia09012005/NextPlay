<?php
require_once __DIR__ . '/../service/WishlistService.php';

class WishlistController {
    private $wishlistService;

    public function __construct($db) {
        $this->wishlistService = new WishlistService($db);
    }
    
    /**
     * Get all wishlist names for the current user
     */
    public function getUserWishlistNames() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to view wishlists'
            ]);
            return;
        }

        // Get wishlist names
        $result = $this->wishlistService->getUserWishlistNames($_SESSION['user_id']);
        
        // Send response
        http_response_code($result['code']);
        echo json_encode([
            'status' => $result['status'],
            'message' => $result['message'] ?? '',
            'data' => $result['data'] ?? null
        ]);
    }

    /**
     * Create a new wishlist for the current user
     */
    public function createWishlist() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to create a wishlist'
            ]);
            return;
        }

        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        if (empty($data['wishlist_name'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Wishlist name is required'
            ]);
            return;
        }

        // Create wishlist
        $result = $this->wishlistService->createWishlist(
            $_SESSION['user_id'],
            $data['wishlist_name']
        );

        // Send response
        http_response_code($result['code']);
        unset($result['code']);
        echo json_encode($result);
    }

    /**
     * Add a game to a wishlist
     */
    /**
     * Get all games from a specific wishlist
     * @param string $wishlistName
     */
    public function getWishlistGames($wishlistName) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to view wishlist games'
            ]);
            return;
        }

        // Get games from wishlist
        $result = $this->wishlistService->getGamesInWishlist(
            $_SESSION['user_id'],
            $wishlistName
        );

        // Send response
        http_response_code($result['code']);
        unset($result['code']);
        echo json_encode($result);
    }

    /**
     * Add a game to a wishlist
     * @param string $wishlistName
     */
    public function addGameToWishlist($wishlistName) {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to add games to a wishlist'
            ]);
            return;
        }

        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        if (empty($data['game_id'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Game ID is required'
            ]);
            return;
        }

        // Add game to wishlist
        $result = $this->wishlistService->addGameToWishlist(
            $_SESSION['user_id'],
            $wishlistName,
            $data['game_id']
        );

        // Send response
        http_response_code($result['code']);
        unset($result['code']);
        echo json_encode($result);
    }
}
?>
