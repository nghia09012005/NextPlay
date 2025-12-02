<?php
class Wishlist {
    private $conn;
    private $table_name = "`Wishlist`";
    private $wish_game_table = "`Wish_game`";

    public $uid;
    public $wishname;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all wishlists for a user
    public function getUserWishlists($uid) {
        $query = "SELECT * FROM {$this->table_name} WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt;
    }

    // Create a new wishlist
    public function create() {
        $query = "INSERT INTO {$this->table_name} (uid, wishname) VALUES (:uid, :wishname)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        return $stmt->execute();
    }

    // Delete a wishlist
    public function delete() {
        // First delete all games from the wishlist
        $query = "DELETE FROM {$this->wish_game_table} WHERE uid = :uid AND wishname = :wishname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->execute();

        // Then delete the wishlist itself
        $query = "DELETE FROM {$this->table_name} WHERE uid = :uid AND wishname = :wishname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        return $stmt->execute();
    }

    // Add game to wishlist
    public function addGame($Gid) {
        $query = "INSERT INTO {$this->wish_game_table} (uid, wishname, Gid) 
                 VALUES (:uid, :wishname, :Gid)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->bindParam(":Gid", $Gid);
        return $stmt->execute();
    }

    // Remove game from wishlist
    public function removeGame($Gid) {
        $query = "DELETE FROM {$this->wish_game_table} 
                 WHERE uid = :uid AND wishname = :wishname AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->bindParam(":Gid", $Gid);
        return $stmt->execute();
    }

    // Clear all games from wishlist
    public function clearGames() {
        $query = "DELETE FROM {$this->wish_game_table} 
                 WHERE uid = :uid AND wishname = :wishname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        return $stmt->execute();
    }

    // Get all games in a wishlist
    public function getGames() {
        $query = "SELECT G.* 
                 FROM `Game` G
                 JOIN {$this->wish_game_table} WG ON G.Gid = WG.Gid
                 WHERE WG.uid = :uid AND WG.wishname = :wishname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":wishname", $this->wishname);
        $stmt->execute();
        return $stmt;
    }
}
?>
