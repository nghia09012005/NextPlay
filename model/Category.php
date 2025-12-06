<?php
class Category {
    private $conn;
    private $table_name = "`category`";

    public $catId;
    public $name;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all categories
    public function readAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one category
    public function readOne($catId) {
        $query = "SELECT * FROM {$this->table_name} WHERE catId = :catId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":catId", $catId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE category
    public function create() {
        $query = "INSERT INTO {$this->table_name} (name, description) 
                 VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        return $stmt->execute();
    }

    // UPDATE category
    public function update() {
        $query = "UPDATE {$this->table_name} 
                 SET name = :name, 
                     description = :description 
                 WHERE catId = :catId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":catId", $this->catId);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        return $stmt->execute();
    }

    // DELETE category
    public function delete($catId) {
        $query = "DELETE FROM {$this->table_name} WHERE catId = :catId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":catId", $catId);
        return $stmt->execute();
    }
}
?>
