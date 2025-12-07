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
                $neededAmount = number_format($totalCost - $currentBalance, 2);
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

    /**
     * Get all purchased games (all users' libraries) - for admin
     * @return array Array of all library entries with user and game info
     */
    public function getAllPurchases() {
        try {
            $query = "SELECT lg.uid, lg.Gid, lg.libname, 
                            g.name as game_name, g.cost as game_price, g.thumbnail,
                            u.uname, u.fname, u.lname
                     FROM `lib_game` lg
                     JOIN `game` g ON lg.Gid = g.Gid
                     JOIN `user` u ON lg.uid = u.uid
                     ORDER BY lg.uid, lg.libname";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in getAllPurchases: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get purchase statistics - for admin
     * @return array Statistics about purchases
     */
    public function getPurchaseStats() {
        try {
            // Total games purchased
            $query1 = "SELECT COUNT(*) as total_purchases FROM `lib_game`";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->execute();
            $totalPurchases = $stmt1->fetch(PDO::FETCH_ASSOC)['total_purchases'];

            // Total users with purchases
            $query2 = "SELECT COUNT(DISTINCT uid) as total_customers FROM `lib_game`";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->execute();
            $totalCustomers = $stmt2->fetch(PDO::FETCH_ASSOC)['total_customers'];

            // Total revenue (sum of game prices in libraries)
            $query3 = "SELECT COALESCE(SUM(g.cost), 0) as total_revenue 
                       FROM `lib_game` lg 
                       JOIN `game` g ON lg.Gid = g.Gid";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->execute();
            $totalRevenue = $stmt3->fetch(PDO::FETCH_ASSOC)['total_revenue'];

            // Average games per customer
            $avgGames = $totalCustomers > 0 ? round($totalPurchases / $totalCustomers, 1) : 0;

            return [
                'total_purchases' => (int)$totalPurchases,
                'total_customers' => (int)$totalCustomers,
                'total_revenue' => (float)$totalRevenue,
                'avg_games_per_customer' => $avgGames
            ];
        } catch (PDOException $e) {
            error_log('Error in getPurchaseStats: ' . $e->getMessage());
            return [
                'total_purchases' => 0,
                'total_customers' => 0,
                'total_revenue' => 0,
                'avg_games_per_customer' => 0
            ];
        }
    }
}
?>
