<?php
require_once __DIR__ . '/../model/Feedback.php';

class FeedbackService {
    private $feedbackModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->feedbackModel = new Feedback($db);
    }

    // Get all feedback (for admin)
    public function getAllFeedback() {
        $query = "SELECT rf.*, g.name as game_name, u.uname 
                  FROM receives_feedback rf 
                  LEFT JOIN game g ON rf.Gid = g.Gid 
                  LEFT JOIN user u ON rf.customerid = u.uid 
                  ORDER BY rf.feedback_time DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single feedback by customerid and gid
    public function getFeedbackByCustomerAndGame($customerid, $gid) {
        $query = "SELECT rf.*, g.name as game_name, u.uname 
                  FROM receives_feedback rf 
                  LEFT JOIN game g ON rf.Gid = g.Gid 
                  LEFT JOIN user u ON rf.customerid = u.uid 
                  WHERE rf.customerid = :customerid AND rf.Gid = :gid 
                  ORDER BY rf.feedback_time DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":customerid", $customerid);
        $stmt->bindParam(":gid", $gid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Admin delete feedback by customerid and gid
    public function adminDeleteFeedback($customerid, $gid) {
        $query = "DELETE FROM receives_feedback WHERE customerid = :customerid AND Gid = :gid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":customerid", $customerid);
        $stmt->bindParam(":gid", $gid);
        return $stmt->execute();
    }

    public function getGameReviews($gameId) {
        $stmt = $this->feedbackModel->getReviewsByGame($gameId);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addReview($data) {
        $this->feedbackModel->customerid = $data['customerid'];
        $this->feedbackModel->Gid = $data['Gid'];
        $this->feedbackModel->content = $data['content'];
        $this->feedbackModel->rating = $data['rating'];
        // We need publisherid. Fetch it from Game table or pass it?
        // Ideally fetch from Game table to be safe.
        // For now, let's assume the controller or caller handles it, OR we fetch it here.
        // Let's fetch it here to be safe.
        $query = "SELECT publisherid FROM Game WHERE Gid = :Gid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":Gid", $data['Gid']);
        $stmt->execute();
        $game = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->feedbackModel->publisherid = $game['publisherid'] ?? null;
        
        $this->feedbackModel->feedback_time = date('Y-m-d'); // Schema says DATE
        
        return $this->feedbackModel->create();
    }

    public function updateReview($customerid, $gid, $content, $rating) {
        // Since PK is (feedback_time, customerid), updating a specific review for a game is hard without time.
        // However, we can try to find the latest review for this user and game and update it.
        // Or we assume the user wants to update their review for this game.
        
        // Find the review first
        $query = "SELECT feedback_time FROM Receives_feedback WHERE customerid = :uid AND Gid = :gid ORDER BY feedback_time DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uid", $customerid);
        $stmt->bindParam(":gid", $gid);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return false;
        
        $feedback_time = $row['feedback_time'];
        
        // Update
        $query = "UPDATE Receives_feedback SET content = :content, rating = :rating WHERE customerid = :uid AND feedback_time = :time";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":rating", $rating);
        $stmt->bindParam(":uid", $customerid);
        $stmt->bindParam(":time", $feedback_time);
        
        return $stmt->execute();
    }

    public function deleteReview($customerid, $gid) {
        // Similar to update, find the review(s) for this game and user and delete.
        // This might delete multiple if user reviewed multiple times (which shouldn't happen ideally).
        $query = "DELETE FROM Receives_feedback WHERE customerid = :uid AND Gid = :gid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":uid", $customerid);
        $stmt->bindParam(":gid", $gid);
        return $stmt->execute();
    }
}
?>
