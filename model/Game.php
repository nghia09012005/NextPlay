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

    // UPDATE game
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
