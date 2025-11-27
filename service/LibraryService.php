<?php
require_once __DIR__ . '/../model/Library.php';
require_once __DIR__ . '/../model/Lib_game.php';

class LibraryService {
    private $db;
    private $libraryModel;
    private $libGameModel;

    public function __construct($db) {
        $this->db = $db;
        $this->libraryModel = new Library($db);
        $this->libGameModel = new Lib_game($db);
    }

    /**
     * Get or create the default "Payed" library for a user
     * @param int $userId
     * @return array Result with library info or error
     */
    public function getOrCreatePayedLibrary($userId) {
        $defaultLibName = "Payed";
        
        // Check if Payed library exists
        $stmt = $this->libraryModel->getUserLibraries($userId);
        $libraries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($libraries as $lib) {
            if (strcasecmp($lib['libname'], $defaultLibName) === 0) {
                return [
                    'status' => 'success',
                    'data' => [
                        'libname' => $lib['libname'],
                        'exists' => true
                    ],
                    'code' => 200
                ];
            }
        }
        
        // Create Payed library if it doesn't exist
        $this->libraryModel->uid = $userId;
        $this->libraryModel->libname = $defaultLibName;
        
        if ($this->libraryModel->create()) {
            return [
                'status' => 'success',
                'data' => [
                    'libname' => $defaultLibName,
                    'exists' => false
                ],
                'code' => 201
            ];
        }
        
        return [
            'status' => 'error',
            'message' => 'Failed to create Payed library',
            'code' => 500
        ];
    }

    /**
     * Move games from wishlist to library
     * @param int $userId
     * @param string $wishlistName
     * @param array $gameIds Array of game IDs to move
     * @return array Result with status and moved games
     */
    public function moveGamesFromWishlist($userId, $wishlistName, $gameIds) {
        try {
            // Get or create Payed library
            $libraryResult = $this->getOrCreatePayedLibrary($userId);
            if ($libraryResult['status'] !== 'success') {
                return $libraryResult;
            }
            $libname = $libraryResult['data']['libname'];
            
            $movedGames = [];
            $errors = [];
            
            foreach ($gameIds as $gameId) {
                // Add to library
                $this->libGameModel->Gid = $gameId;
                $this->libGameModel->libname = $libname;
                $this->libGameModel->uid = $userId;
                
                if ($this->libGameModel->add()) {
                    $movedGames[] = $gameId;
                } else {
                    $errors[] = "Failed to add game $gameId to library";
                }
            }
            
            if (!empty($movedGames)) {
                return [
                    'status' => 'success',
                    'data' => [
                        'moved_games' => $movedGames,
                        'library' => $libname,
                        'errors' => $errors
                    ],
                    'code' => 200
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to move any games to library',
                    'errors' => $errors,
                    'code' => 400
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error moving games to library: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }
}
?>
