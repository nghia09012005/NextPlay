<?php
class Library {
    private $conn;
    private $table_name = "`library`";
    private $lib_game_table = "`lib_game`";

    public $uid;
    public $libname;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all libraries for a user
    public function getUserLibraries($uid) {
        $query = "SELECT * FROM {$this->table_name} WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt;
    }

    // Create a new library
    public function create() {
        $query = "INSERT INTO {$this->table_name} (uid, libname) VALUES (:uid, :libname)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        return $stmt->execute();
    }

    // Delete a library
    public function delete() {
        // First delete all games from the library
        $query = "DELETE FROM {$this->lib_game_table} WHERE uid = :uid AND libname = :libname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->execute();

        // Then delete the library itself
        $query = "DELETE FROM {$this->table_name} WHERE uid = :uid AND libname = :libname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        return $stmt->execute();
    }

    // Add game to library
    public function addGame($Gid) {
        $query = "INSERT INTO {$this->lib_game_table} (uid, libname, Gid) 
                 VALUES (:uid, :libname, :Gid)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->bindParam(":Gid", $Gid);
        return $stmt->execute();
    }

    // Remove game from library
    public function removeGame($Gid) {
        $query = "DELETE FROM {$this->lib_game_table} 
                 WHERE uid = :uid AND libname = :libname AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->bindParam(":Gid", $Gid);
        return $stmt->execute();
    }

    // Get all games in a library
    public function getGames() {
        $query = "SELECT G.* 
                 FROM `Game` G
                 JOIN {$this->lib_game_table} LG ON G.Gid = LG.Gid
                 WHERE LG.uid = :uid AND LG.libname = :libname";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->execute();
        return $stmt;
    }

    // Get ALL games for a user (across all libraries)
    public function getAllUserGames($uid) {
        $query = "SELECT DISTINCT G.* 
                 FROM `Game` G
                 JOIN {$this->lib_game_table} LG ON G.Gid = LG.Gid
                 WHERE LG.uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt;
    }
}
?>
