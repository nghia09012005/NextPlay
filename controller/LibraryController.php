<?php
require_once __DIR__ . '/../service/LibraryService.php';

class LibraryController {
    private $libraryService;

    public function __construct($db) {
        $this->libraryService = new LibraryService($db);
    }

    /**
     * Get all games in the current user's library
     * @return void
     */
    public function getUserLibrary() {
        header('Content-Type: application/json');
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'User not authenticated. Please login first.'
            ]);
            return;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {

            $result = $this->libraryService->getUserLibraryGames($userId);
            
            if ($result['status'] === 'error') {
                http_response_code(500);
            } else {
                http_response_code(200);
            }
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
?>
