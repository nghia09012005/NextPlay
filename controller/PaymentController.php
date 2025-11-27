<?php
require_once __DIR__ . '/../service/LibraryService.php';
require_once __DIR__ . '/../model/Wish_game.php';

class PaymentController {
    private $db;
    private $libraryService;
    private $wishGameModel;

    public function __construct($db) {
        $this->db = $db;
        $this->libraryService = new LibraryService($db);
        $this->wishGameModel = new Wish_game($db);
    }

    /**
     * Process payment and move games from wishlist to library
     */
    public function processPayment() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'You must be logged in to make a payment'
            ]);
            return;
        }

        $userId = $_SESSION['user_id'];
        
        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        // Validate input
        if (empty($data['wishlist_name']) || empty($data['game_ids']) || !is_array($data['game_ids'])) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Wishlist name and game IDs array are required'
            ]);
            return;
        }

        $wishlistName = $data['wishlist_name'];
        $gameIds = $data['game_ids'];
        $movedGames = [];
        $failedGames = [];

        try {
            // Move games to library
            $result = $this->libraryService->moveGamesFromWishlist($userId, $wishlistName, $gameIds);
            
            if ($result['status'] === 'success') {
                // Remove games from wishlist if they were successfully added to library
                foreach ($result['data']['moved_games'] as $gameId) {
                    $this->wishGameModel->Gid = $gameId;
                    $this->wishGameModel->wishname = $wishlistName;
                    $this->wishGameModel->uid = $userId;
                    
                    if ($this->wishGameModel->remove()) {
                        $movedGames[] = $gameId;
                    } else {
                        $failedGames[] = $gameId;
                    }
                }

                $response = [
                    'status' => 'success',
                    'message' => count($movedGames) . ' games moved to library',
                    'data' => [
                        'moved_to_library' => $movedGames,
                        'failed_to_remove_from_wishlist' => $failedGames,
                        'library' => $result['data']['library']
                    ]
                ];

                if (!empty($result['data']['errors'])) {
                    $response['warnings'] = $result['data']['errors'];
                }

                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code($result['code']);
                unset($result['code']);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ]);
        }
    }
}
?>
