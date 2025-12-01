<?php
class Game {
    private $conn;
    private $table_name = "`Game`";

    public $gid;
    public $name;
    public $description;
    public $price;
    public $thumbnail;
    public $category;
    public $tags;
    public $developer;
    public $publisher;
    public $release_date;
    public $rating;
    public $reviews;

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
            (`name`, `description`, `price`, `thumbnail`, `category`, `tags`, `developer`, `publisher`, `release_date`, `rating`, `reviews`) 
            VALUES (:name, :description, :price, :thumbnail, :category, :tags, :developer, :publisher, :release_date, :rating, :reviews)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":tags", $this->tags);
        $stmt->bindParam(":developer", $this->developer);
        $stmt->bindParam(":publisher", $this->publisher);
        $stmt->bindParam(":release_date", $this->release_date);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":reviews", $this->reviews);

        if($stmt->execute()){
            return true;
        }
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // UPDATE
    public function update() {
        $query = "UPDATE {$this->table_name} 
                SET `name`=:name, `description`=:description, 
                    `price`=:price, `thumbnail`=:thumbnail,
                    `category`=:category, `tags`=:tags,
                    `developer`=:developer, `publisher`=:publisher,
                    `release_date`=:release_date, `rating`=:rating,
                    `reviews`=:reviews
                WHERE `gid`=:gid";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":tags", $this->tags);
        $stmt->bindParam(":developer", $this->developer);
        $stmt->bindParam(":publisher", $this->publisher);
        $stmt->bindParam(":release_date", $this->release_date);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":reviews", $this->reviews);
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
