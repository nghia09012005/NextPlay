<?php
require_once __DIR__ . '/../model/Wishlist.php';
require_once __DIR__ . '/../model/Wish_game.php';
require_once __DIR__ . '/../model/Game.php';
require_once __DIR__ . '/../model/User.php';

class CartService {
    private $db;
    private $wishlistModel;
    private $wishGameModel;
    private $userModel;

    public function __construct($db) {
        $this->db = $db;
        $this->wishlistModel = new Wishlist($db);
        $this->wishGameModel = new Wish_game($db);
        $this->userModel = new User($db);
    }

    /**
     * Get all active carts (Wishlists named 'Cart') with details
     */
    public function getAllCarts() {
        // Query to get all users who have a 'Cart' wishlist
        // Join with Wish_game to get item count and total value
        // Join with User to get customer info
        $query = "
            SELECT 
                w.uid,
                u.uname,
                u.fname,
                u.lname,
                u.email,
                u.avatar,
                COUNT(wg.Gid) as item_count,
                SUM(g.price) as total_amount,
                MAX(w.wishname) as wishname -- Should be 'Cart'
            FROM 
                Wishlist w
            JOIN 
                User u ON w.uid = u.uid
            LEFT JOIN 
                Wish_game wg ON w.uid = wg.uid AND w.wishname = wg.wishname
            LEFT JOIN 
                Game g ON wg.Gid = g.Gid
            WHERE 
                w.wishname = 'Cart'
            GROUP BY 
                w.uid
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Normalize data
            foreach ($carts as &$cart) {
                // Ensure numbers are numbers
                $cart['item_count'] = (int)$cart['item_count'];
                $cart['total_amount'] = (float)$cart['total_amount'];
                // Add a mock status since Wishlist doesn't have one, assume 'active' for existing carts
                $cart['status'] = 'active';
                $cart['created_date'] = date('Y-m-d'); // Wishlist doesn't have created_date, use today or mock
            }

            return [
                'status' => 'success',
                'data' => $carts
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get details of a specific user's cart
     */
    public function getCartDetails($uid) {
        try {
            // Get user info
            $this->userModel->uid = $uid;
            $user = $this->userModel->readOne($uid);
            if (!$user) {
                return ['status' => 'error', 'message' => 'User not found'];
            }

            // Get games in 'Cart' wishlist
            $this->wishGameModel->uid = $uid;
            $this->wishGameModel->wishname = 'Cart'; // Hardcoded as we are managing Carts
            $headers = $this->wishGameModel->getGamesInWishlist($uid, 'Cart'); // This logic is in Wish_game model? 
            // Wait, Wish_game model method name is likely getGamesInWishlist or similar.
            // Let's check Wish_game.php... it has `getGamesInWishlist($uid, $wishname)`.
            
            $stmt = $this->wishGameModel->getGamesInWishlist($uid, 'Cart');
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = 0;
            $items = array_map(function($game) use (&$total) {
                $price = (float)$game['price'];
                $total += $price;
                return [
                    'id' => $game['Gid'],
                    'name' => $game['name'],
                    'price' => $price,
                    'quantity' => 1, // Wishlist is 1 per item usually
                    'subtotal' => $price,
                    'thumbnail' => $game['thumbnail']
                ];
            }, $games);

            return [
                'status' => 'success',
                'data' => [
                    'uid' => $user['uid'],
                    'uname' => $user['uname'],
                    'fname' => $user['fname'],
                    'lname' => $user['lname'],
                    'email' => $user['email'],
                    'avatar' => $user['avatar'],
                    'status' => 'active', // Mock
                    'created_date' => date('Y-m-d'), // Mock
                    'total' => $total,
                    'items' => $items
                ]
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
?>
