<?php
class Game {
    private $conn;
    private $table_name = "`Game`";

    public $Gid;
    public $name;
    public $version;
    public $description;
    public $cost;
    public $adminid;
    public $publisherid;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all games
    public function readAll() {
        $query = "SELECT g.*, u.uname as publisher_name, a.uname as admin_name 
                 FROM {$this->table_name} g
                 LEFT JOIN `User` u ON g.publisherid = u.uid
                 LEFT JOIN `User` a ON g.adminid = a.uid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one game by ID
    public function readOne($Gid) {
        $query = "SELECT g.*, u.uname as publisher_name, a.uname as admin_name 
                 FROM {$this->table_name} g
                 LEFT JOIN `User` u ON g.publisherid = u.uid
                 LEFT JOIN `User` a ON g.adminid = a.uid
                 WHERE g.Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // GET games by publisher
    public function readByPublisher($publisherId) {
        $query = "SELECT * FROM {$this->table_name} WHERE publisherid = :publisherid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":publisherid", $publisherId);
        $stmt->execute();
        return $stmt;
    }

    // CREATE new game
    public function create() {
        $query = "INSERT INTO {$this->table_name} 
                 (name, version, description, cost, adminid, publisherid) 
                 VALUES (:name, :version, :description, :cost, :adminid, :publisherid)";
        
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->version = htmlspecialchars(strip_tags($this->version));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->cost = htmlspecialchars(strip_tags($this->cost));
        $this->adminid = htmlspecialchars(strip_tags($this->adminid));
        $this->publisherid = htmlspecialchars(strip_tags($this->publisherid));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":version", $this->version);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":cost", $this->cost);
        $stmt->bindParam(":adminid", $this->adminid);
        $stmt->bindParam(":publisherid", $this->publisherid);

        if($stmt->execute()) {
            $this->Gid = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // UPDATE game
    public function update() {
        $query = "UPDATE {$this->table_name} 
                 SET name = :name,
                     version = :version,
                     description = :description,
                     cost = :cost,
                     adminid = :adminid,
                     publisherid = :publisherid
                 WHERE Gid = :Gid";
        
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->version = htmlspecialchars(strip_tags($this->version));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->cost = htmlspecialchars(strip_tags($this->cost));
        $this->adminid = htmlspecialchars(strip_tags($this->adminid));
        $this->publisherid = htmlspecialchars(strip_tags($this->publisherid));
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":version", $this->version);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":cost", $this->cost);
        $stmt->bindParam(":adminid", $this->adminid);
        $stmt->bindParam(":publisherid", $this->publisherid);
        $stmt->bindParam(":Gid", $this->Gid);

        return $stmt->execute();
    }

    // DELETE game
    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $this->Gid = htmlspecialchars(strip_tags($this->Gid));
        $stmt->bindParam(":Gid", $this->Gid);
        return $stmt->execute();
    }
}
?>
