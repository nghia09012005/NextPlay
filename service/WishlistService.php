<?php
require_once __DIR__ . '/../model/Wishlist.php';
require_once __DIR__ . '/../model/Wish_game.php';

class WishlistService {
    private $db;
    private $wishlistModel;
    private $wishGameModel;

    public function __construct($db) {
        $this->db = $db;
        $this->wishlistModel = new Wishlist($db);
        $this->wishGameModel = new Wish_game($db);
    }
    
    /**
     * Get all wishlist names for a user
     * @param int $userId
     * @return array Result with status and wishlist names
     */
    public function getUserWishlistNames($userId) {
        try {
            $stmt = $this->wishlistModel->getUserWishlists($userId);
            $wishlists = $stmt->fetchAll(PDO::FETCH_COLUMN, 1); // Get only the wishname column
            
            return [
                'status' => 'success',
                'data' => [
                    'wishlists' => $wishlists
                ],
                'code' => 200
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch wishlists: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Create a new wishlist for a user
     * @param int $userId
     * @param string $wishlistName
     * @return array Result with status and message
     */
    public function createWishlist($userId, $wishlistName) {
        try {
            // Check if wishlist name is provided
            if (empty($wishlistName)) {
                return [
                    'status' => 'error',
                    'message' => 'Wishlist name is required',
                    'code' => 400
                ];
            }

            // Check if wishlist name already exists for this user
            $this->wishlistModel->uid = $userId;
            $this->wishlistModel->wishname = $wishlistName;
            
            $stmt = $this->wishlistModel->getUserWishlists($userId);
            $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($wishlists as $wishlist) {
                if (strcasecmp($wishlist['wishname'], $wishlistName) === 0) {
                    return [
                        'status' => 'error',
                        'message' => 'A wishlist with this name already exists',
                        'code' => 409
                    ];
                }
            }

            // Create the wishlist
            $result = $this->wishlistModel->create();
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Wishlist created successfully',
                    'data' => [
                        'wishname' => $wishlistName
                    ],
                    'code' => 201
                ];
            } else {
                throw new Exception('Failed to create wishlist');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to create wishlist: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Add a game to a user's wishlist
     * @param int $userId
     * @param string $wishlistName
     * @param int $gameId
     * @return array Result with status and message
     */
    /**
     * Get all games from a specific wishlist
     * @param int $userId
     * @param string $wishlistName
     * @return array Result with status and games data
     */
    public function getGamesInWishlist($userId, $wishlistName) {
        try {
            // Check if wishlist exists
            $this->wishlistModel->uid = $userId;
            $this->wishlistModel->wishname = $wishlistName;
            
            $stmt = $this->wishlistModel->getUserWishlists($userId);
            $wishlistExists = false;
            $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($wishlists as $wishlist) {
                if (strcasecmp($wishlist['wishname'], $wishlistName) === 0) {
                    $wishlistExists = true;
                    break;
                }
            }

            if (!$wishlistExists) {
                return [
                    'status' => 'error',
                    'message' => 'Wishlist not found',
                    'code' => 404
                ];
            }

            // Get games from wishlist
            $stmt = $this->wishGameModel->getGamesInWishlist($userId, $wishlistName);
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => [
                    'wishlist_name' => $wishlistName,
                    'games' => $games
                ],
                'code' => 200
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to get games from wishlist: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Add a game to a user's wishlist
     * @param int $userId
     * @param string $wishlistName
     * @param int $gameId
     * @return array Result with status and message
     */
    public function addGameToWishlist($userId, $wishlistName, $gameId) {
        try {
            // Validate inputs
            if (empty($wishlistName) || empty($gameId)) {
                return [
                    'status' => 'error',
                    'message' => 'Wishlist name and game ID are required',
                    'code' => 400
                ];
            }

            // Check if wishlist exists
            $this->wishlistModel->uid = $userId;
            $this->wishlistModel->wishname = $wishlistName;
            
            $stmt = $this->wishlistModel->getUserWishlists($userId);
            $wishlistExists = false;
            $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($wishlists as $wishlist) {
                if (strcasecmp($wishlist['wishname'], $wishlistName) === 0) {
                    $wishlistExists = true;
                    break;
                }
            }

            if (!$wishlistExists) {
                return [
                    'status' => 'error',
                    'message' => 'Wishlist not found',
                    'code' => 404
                ];
            }

            // Check if game is already in the wishlist
            $this->wishGameModel->Gid = $gameId;
            $this->wishGameModel->wishname = $wishlistName;
            $this->wishGameModel->uid = $userId;
            
            if ($this->wishGameModel->exists()) {
                return [
                    'status' => 'error',
                    'message' => 'Game is already in the wishlist',
                    'code' => 409
                ];
            }

            // Add game to wishlist
            $result = $this->wishGameModel->add();
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Game added to wishlist successfully',
                    'code' => 200
                ];
            } else {
                throw new Exception('Failed to add game to wishlist');
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to add game to wishlist: ' . $e->getMessage(),
                'code' => 500
            ];
        }
    }
}
?>
