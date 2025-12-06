<?php
class Cart {
    private $conn;
    private $table_name = "`cart`";
    private $cart_game_table = "`cart_game`";

    public $uid;
    public $status;
    public $created_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get user's active cart
    public function getActiveCart($uid) {
        $query = "SELECT * FROM {$this->table_name} 
                 WHERE uid = :uid AND status = 'active' 
                 ORDER BY created_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new cart
    public function create() {
        // Check if there's already an active cart
        $existingCart = $this->getActiveCart($this->uid);
        if ($existingCart) {
            $this->status = $existingCart['status'];
            $this->created_date = $existingCart['created_date'];
            return true;
        }

        $query = "INSERT INTO {$this->table_name} (uid, status) 
                 VALUES (:uid, 'active')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        
        if ($stmt->execute()) {
            $this->status = 'active';
            $this->created_date = date('Y-m-d');
            return true;
        }
        return false;
    }

    // Update cart status (e.g., 'active' to 'completed' after checkout)
    public function updateStatus($newStatus) {
        $query = "UPDATE {$this->table_name} 
                 SET status = :status 
                 WHERE uid = :uid AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $newStatus);
        $stmt->bindParam(":uid", $this->uid);
        
        if ($stmt->execute()) {
            $this->status = $newStatus;
            return true;
        }
        return false;
    }

    // Add game to cart
    public function addGame($Gid, $quantity = 1) {
        // Make sure cart exists
        if (!$this->getActiveCart($this->uid)) {
            $this->create();
        }

        // Check if game already in cart
        $query = "SELECT * FROM {$this->cart_game_table} 
                 WHERE uid = :uid AND cart_status = 'active' AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Update quantity if already in cart
            $query = "UPDATE {$this->cart_game_table} 
                     SET quantity = quantity + :quantity 
                     WHERE uid = :uid AND cart_status = 'active' AND Gid = :Gid";
        } else {
            // Add new item to cart
            $query = "INSERT INTO {$this->cart_game_table} 
                     (uid, cart_status, Gid, quantity) 
                     VALUES (:uid, 'active', :Gid, :quantity)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->bindParam(":quantity", $quantity);
        
        return $stmt->execute();
    }

    // Remove game from cart
    public function removeGame($Gid) {
        $query = "DELETE FROM {$this->cart_game_table} 
                 WHERE uid = :uid AND cart_status = 'active' AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":Gid", $Gid);
        return $stmt->execute();
    }

    // Update game quantity in cart
    public function updateQuantity($Gid, $quantity) {
        if ($quantity <= 0) {
            return $this->removeGame($Gid);
        }
        
        $query = "UPDATE {$this->cart_game_table} 
                 SET quantity = :quantity 
                 WHERE uid = :uid AND cart_status = 'active' AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->bindParam(":quantity", $quantity);
        return $stmt->execute();
    }

    // Get all items in cart
    public function getItems() {
        $query = "SELECT G.*, CG.quantity, (G.cost * CG.quantity) as subtotal 
                 FROM `Game` G
                 JOIN {$this->cart_game_table} CG ON G.Gid = CG.Gid
                 WHERE CG.uid = :uid AND CG.cart_status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get cart total
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    // Clear cart (after checkout)
    public function clear() {
        return $this->updateStatus('completed');
    }
}
?>
