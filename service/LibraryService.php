<?php
require_once __DIR__ . '/../model/Library.php';
require_once __DIR__ . '/../model/Lib_game.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Game.php';

class LibraryService {
    private $db;
    private $libraryModel;
    private $libGameModel;
    private $customerModel;
    private $gameModel;

    public function __construct($db) {
        $this->db = $db;
        $this->libraryModel = new Library($db);
        $this->libGameModel = new Lib_game($db);
        $this->customerModel = new Customer($db);
        $this->gameModel = new Game($db);
    }
    
    /**
     * Get all games in a user's library
     * @param int $userId
     * @return array Array of games in the user's library
     */
    public function getUserLibraryGames($userId) {
        try {
            $query = "SELECT g.* 
                     FROM `Game` g
                     JOIN `Lib_game` lg ON g.Gid = lg.Gid
                     WHERE lg.uid = :userId";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            $games = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Format game data to match GameService output
                $games[] = [
                    "id" => $row['Gid'],
                    "name" => $row['name'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "image" => $row['thumbnail'],
                    "category" => $row['category'],
                    "tags" => json_decode($row['tags']),
                    "developer" => $row['developer'],
                    "publisher" => $row['publisher'],
                    "releaseDate" => $row['release_date'],
                    "rating" => $row['rating'],
                    "reviews" => $row['reviews']
                ];
            }
            
            return [
                'status' => 'success',
                'data' => $games,
                'count' => count($games)
            ];
            
        } catch (PDOException $e) {
            error_log('Error in getUserLibraryGames: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get total cost of games
     * @param array $gameIds Array of game IDs
     * @return float Total cost of all games
     */
    private function getTotalCost($gameIds) {
        if (empty($gameIds)) {
            return 0;
        }
        
        $placeholders = str_repeat('?,', count($gameIds) - 1) . '?';
        $query = "SELECT SUM(price) as total FROM `Game` WHERE Gid IN ($placeholders)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($gameIds);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float) ($result['total'] ?? 0);
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
            // Check if game IDs are provided
            if (empty($gameIds)) {
                return [
                    'status' => 'error',
                    'message' => 'No game IDs provided',
                    'code' => 400
                ];
            }

            // Get or create Payed library
            $libraryResult = $this->getOrCreatePayedLibrary($userId);
            if ($libraryResult['status'] !== 'success') {
                return $libraryResult;
            }
            $libname = $libraryResult['data']['libname'];

            // Filter out games already in library
            $gamesToBuy = [];
            $alreadyOwned = [];
            
            foreach ($gameIds as $gameId) {
                $this->libGameModel->Gid = $gameId;
                $this->libGameModel->libname = $libname;
                $this->libGameModel->uid = $userId;
                
                if ($this->libGameModel->exists()) {
                    $alreadyOwned[] = $gameId;
                } else {
                    $gamesToBuy[] = $gameId;
                }
            }

            // If all games are already owned
            if (empty($gamesToBuy)) {
                return [
                    'status' => 'success',
                    'message' => 'All games are already in your library',
                    'data' => [
                        'moved_games' => $alreadyOwned, // Treat as moved so they get removed from cart
                        'library' => $libname,
                        'errors' => []
                    ],
                    'code' => 200
                ];
            }

            // Get customer balance
            $customer = $this->customerModel->readOne($userId);
            if (!$customer) {
                return [
                    'status' => 'error',
                    'message' => 'Customer not found',
                    'code' => 404
                ];
            }
            
            // Calculate total cost of NEW games only
            $totalCost = $this->getTotalCost($gamesToBuy);
            $currentBalance = (float)$customer['balance'];
            
            // Check if balance is sufficient
            if ($currentBalance < $totalCost) {
                $neededAmount = $totalCost - $currentBalance;
                return [
                    'status' => 'error',
                    'message' => 'Insufficient balance',
                    'code' => 402, // Payment Required
                    'data' => [
                        'current_balance' => $currentBalance,
                        'total_cost' => $totalCost,
                        'needed_amount' => $neededAmount,
                        'required_balance' => $totalCost
                    ]
                ];
            }

            $movedGames = [];
            $errors = [];
            
            // Add new games to library
            foreach ($gamesToBuy as $gameId) {
                $this->libGameModel->Gid = $gameId;
                $this->libGameModel->libname = $libname;
                $this->libGameModel->uid = $userId;
                
                if ($this->libGameModel->add()) {
                    $movedGames[] = $gameId;
                } else {
                    $errors[] = "Failed to add game $gameId to library";
                }
            }
            
            // Combine with already owned for the response (so they are removed from cart)
            $allProcessedGames = array_merge($movedGames, $alreadyOwned);

            // Deduct balance ONLY if games were successfully added
            if (!empty($movedGames)) {
                // Recalculate cost for only the games that were successfully added
                $actualCost = $this->getTotalCost($movedGames);
                $newBalance = $currentBalance - $actualCost;
                
                $this->customerModel->balance = $newBalance;
                $this->customerModel->uid = $userId;
                
                if (!$this->customerModel->update()) {
                    // Critical error: Games added but balance not deducted
                    error_log("CRITICAL: Failed to deduct balance for user $userId after adding games " . implode(',', $movedGames));
                }
            }
            
            if (!empty($allProcessedGames)) {
                return [
                    'status' => 'success',
                    'data' => [
                        'moved_games' => $allProcessedGames,
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
