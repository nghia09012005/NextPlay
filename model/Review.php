<?php
class Review {
    private $conn;
    private $table_name = "`Review`";

    public $customerid;
    public $Gid;
    public $review_time;
    public $content;
    public $rating;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all reviews for a game
    public function readByGame($Gid) {
        $query = "SELECT R.*, U.uname, U.avatar 
                 FROM {$this->table_name} R
                 JOIN `User` U ON U.uid = R.customerid
                 WHERE R.Gid = :Gid
                 ORDER BY R.review_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->execute();
        return $stmt;
    }

    // GET all reviews by a customer
    public function readByCustomer($customerid) {
        $query = "SELECT R.*, G.name as game_name 
                 FROM {$this->table_name} R
                 JOIN `Game` G ON G.Gid = R.Gid
                 WHERE R.customerid = :customerid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $customerid);
        $stmt->execute();
        return $stmt;
    }

    // CREATE review
    public function create() {
        $query = "INSERT INTO {$this->table_name} 
                 (customerid, Gid, content, rating) 
                 VALUES (:customerid, :Gid, :content, :rating)
                 ON DUPLICATE KEY UPDATE 
                 content = :content, 
                 rating = :rating,
                 review_time = CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $this->customerid);
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":rating", $this->rating);
        
        return $stmt->execute();
    }

    // DELETE review
    public function delete() {
        $query = "DELETE FROM {$this->table_name} 
                 WHERE customerid = :customerid AND Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $this->customerid);
        $stmt->bindParam(":Gid", $this->Gid);
        return $stmt->execute();
    }

    // Get average rating for a game
    public function getAverageRating($Gid) {
        $query = "SELECT AVG(rating) as avg_rating 
                 FROM {$this->table_name} 
                 WHERE Gid = :Gid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $Gid);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['avg_rating'];
    }
}
?>
