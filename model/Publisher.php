<?php
class Publisher {
    private $conn;
    private $table_name = "`publisher`";

    public $uid;
    public $description;
    public $taxcode;
    public $location;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all publishers
    public function readAll() {
        $query = "SELECT P.*, U.uname, U.email 
                 FROM {$this->table_name} P
                 JOIN `User` U ON U.uid = P.uid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one publisher
    public function readOne($uid) {
        $query = "SELECT P.*, U.uname, U.email 
                 FROM {$this->table_name} P
                 JOIN `User` U ON U.uid = P.uid
                 WHERE P.`uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE publisher
    public function create() {
        $query = "INSERT INTO {$this->table_name} (`uid`, `description`, `taxcode`, `location`) 
                 VALUES (:uid, :description, :taxcode, :location)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":taxcode", $this->taxcode);
        $stmt->bindParam(":location", $this->location);
        return $stmt->execute();
    }

    // UPDATE publisher
    public function update() {
        $query = "UPDATE {$this->table_name} 
                 SET description = :description, 
                     taxcode = :taxcode, 
                     location = :location 
                 WHERE uid = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":taxcode", $this->taxcode);
        $stmt->bindParam(":location", $this->location);
        return $stmt->execute();
    }

    // DELETE publisher
    public function delete($uid) {
        $query = "DELETE FROM {$this->table_name} WHERE `uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }
}
?>
