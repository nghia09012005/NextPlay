<?php
class Game {
    private $conn;
    private $table_name = "`Game`";

    public $gid;
    public $title;
    public $description;
    public $price;
    public $thumbnail;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all
    public function readAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one
    public function readOne($gid) {
        $query = "SELECT * FROM {$this->table_name} WHERE `gid` = :gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":gid", $gid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE
    public function create() {
        $query = "INSERT INTO {$this->table_name} 
            (`title`, `description`, `price`, `thumbnail`) 
            VALUES (:title, :description, :price, :thumbnail)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":thumbnail", $this->thumbnail);

        return $stmt->execute();
    }

    // UPDATE
    public function update() {
        $query = "UPDATE {$this->table_name} 
                SET `title`=:title, `description`=:description, 
                    `price`=:price, `thumbnail`=:thumbnail
                WHERE `gid`=:gid";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":gid", $this->gid);

        return $stmt->execute();
    }

    // DELETE
    public function delete($gid) {
        $query = "DELETE FROM {$this->table_name} WHERE `gid`=:gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":gid", $gid);
        return $stmt->execute();
    }
}
?>
