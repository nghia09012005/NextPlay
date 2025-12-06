<?php
class Review {
    private $conn;
    private $table_name = "`review`";

    public $customerid;
    public $news_id;
    public $review_time;
    public $content;
    public $rating;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all reviews for a news article
    public function readByNews($news_id) {
        $query = "SELECT R.*, U.uname, U.avatar 
                 FROM {$this->table_name} R
                 JOIN `User` U ON U.uid = R.customerid
                 WHERE R.news_id = :news_id
                 ORDER BY R.review_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":news_id", $news_id);
        $stmt->execute();
        return $stmt;
    }

    // GET all reviews by a customer
    public function readByCustomer($customerid) {
        $query = "SELECT R.*, N.title as news_title 
                 FROM {$this->table_name} R
                 JOIN `News` N ON N.id = R.news_id
                 WHERE R.customerid = :customerid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $customerid);
        $stmt->execute();
        return $stmt;
    }

    // CREATE review
    public function create() {
        $query = "INSERT INTO {$this->table_name} 
                 (customerid, news_id, content, rating) 
                 VALUES (:customerid, :news_id, :content, :rating)
                 ON DUPLICATE KEY UPDATE 
                 content = :content, 
                 rating = :rating,
                 review_time = CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $this->customerid);
        $stmt->bindParam(":news_id", $this->news_id);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":rating", $this->rating);
        
        return $stmt->execute();
    }

    // DELETE review
    public function delete() {
        $query = "DELETE FROM {$this->table_name} 
                 WHERE customerid = :customerid AND news_id = :news_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $this->customerid);
        $stmt->bindParam(":news_id", $this->news_id);
        return $stmt->execute();
    }

    // Get average rating for a news article
    public function getAverageRating($news_id) {
        $query = "SELECT AVG(rating) as avg_rating 
                 FROM {$this->table_name} 
                 WHERE news_id = :news_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":news_id", $news_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['avg_rating'];
    }
    
    // Check if a customer has reviewed a news article
    public function hasReviewed($customerid, $news_id) {
        $query = "SELECT COUNT(*) as count 
                 FROM {$this->table_name} 
                 WHERE customerid = :customerid AND news_id = :news_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customerid", $customerid);
        $stmt->bindParam(":news_id", $news_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>
