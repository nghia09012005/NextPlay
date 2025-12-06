<?php
class Wish_game {
    private $conn;
    private $table_name = "`wish_game`";

    public $Gid;
    public $wishname;
    public $uid;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all games in a wishlist
    public function getGamesInWishlist($uid, $wishname) {
        $query = "SELECT g.* 
                 FROM `Game` g
                 INNER JOIN {$this->table_name} wg ON g.Gid = wg.Gid
                 WHERE wg.uid = :uid AND wg.wishname = :wishname";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->bindParam(":wishname", $wishname);
        $stmt->execute();
        
        return $stmt;
    }

    // Add game to wishlist
    public function add() {
        $query = "INSERT INTO {$this->table_name} (Gid, wishname, uid) 
                 VALUES (:Gid, :wishname, :uid)";
        
        $stmt = $this->conn->prepare($query);
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));
        $this->wishname = htmlspecialchars(strip_tags($this->wishname));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->bindParam(":uid", $this->uid);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Remove game from wishlist
    public function remove() {
        $query = "DELETE FROM {$this->table_name} 
                 WHERE Gid = :Gid AND wishname = :wishname AND uid = :uid";
        
        $stmt = $this->conn->prepare($query);
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));
        $this->wishname = htmlspecialchars(strip_tags($this->wishname));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->bindParam(":uid", $this->uid);
        
        return $stmt->execute();
    }

    // Check if game exists in wishlist
    public function exists() {
        $query = "SELECT 1 FROM {$this->table_name} 
                 WHERE Gid = :Gid AND wishname = :wishname AND uid = :uid";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->bindParam(":uid", $this->uid);
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
