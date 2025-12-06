<?php
class Feedback {
    private $conn;
    private $table_name = "Receives_feedback";

    public $feedback_time;
    public $customerid;
    public $Gid;
    public $publisherid;
    public $content;
    public $rating;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get reviews for a specific game
    public function getReviewsByGame($gameId) {
        $query = "SELECT f.*, u.uname as reviewer_name, u.avatar as reviewer_avatar
                  FROM " . $this->table_name . " f
                  JOIN User u ON f.customerid = u.uid
                  WHERE f.Gid = :Gid
                  ORDER BY f.feedback_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Gid", $gameId);
        $stmt->execute();

        return $stmt;
    }

    // Create a new review
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET feedback_time = :feedback_time,
                      customerid = :customerid,
                      Gid = :Gid,
                      publisherid = :publisherid,
                      content = :content,
                      rating = :rating";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->rating = htmlspecialchars(strip_tags($this->rating));

        // Bind values
        $stmt->bindParam(":feedback_time", $this->feedback_time);
        $stmt->bindParam(":customerid", $this->customerid);
        $stmt->bindParam(":Gid", $this->Gid);
        $stmt->bindParam(":publisherid", $this->publisherid);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":rating", $this->rating);

        if ($stmt->execute()) {
            return true;
        }
        $error = implode(" ", $stmt->errorInfo());
        error_log("Feedback create error: " . $error);
        return $error;
    }
}
?>
