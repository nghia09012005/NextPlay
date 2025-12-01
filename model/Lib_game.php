<?php
class Lib_game {
    private $conn;
    private $table_name = "`Lib_game`";

    public $Gid;
    public $libname;
    public $uid;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all games in a library
     * @param int $uid User ID
     * @param string $libname Library name
     * @return PDOStatement
     */
    public function getGamesInLibrary($uid, $libname) {
        $query = "SELECT g.* 
                 FROM `Game` g
                 INNER JOIN {$this->table_name} lg ON g.Gid = lg.Gid
                 WHERE lg.uid = :uid AND lg.libname = :libname";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->bindParam(":libname", $libname);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Add a game to a library
     * @return bool True on success, false on failure
     */
    public function add() {
        $query = "INSERT INTO {$this->table_name} (Gid, libname, uid) 
                 VALUES (:Gid, :libname, :uid)";
        
        $stmt = $this->conn->prepare($query);
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));
        $this->libname = htmlspecialchars(strip_tags($this->libname));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->bindParam(":uid", $this->uid);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Remove a game from a library
     * @return bool True on success, false on failure
     */
    public function remove() {
        $query = "DELETE FROM {$this->table_name} 
                 WHERE Gid = :Gid AND libname = :libname AND uid = :uid";
        
        $stmt = $this->conn->prepare($query);
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));
        $this->libname = htmlspecialchars(strip_tags($this->libname));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->bindParam(":uid", $this->uid);
        
        return $stmt->execute();
    }

    /**
     * Check if a game exists in a library
     * @return bool True if exists, false otherwise
     */
    public function exists() {
        $query = "SELECT 1 FROM {$this->table_name} 
                 WHERE Gid = :Gid AND libname = :libname AND uid = :uid";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":libname", $this->libname);
        $stmt->bindParam(":uid", $this->uid);
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
