<?php
require_once __DIR__ . '/../model/Review.php';

class ReviewService {
    private $reviewModel;

    public function __construct($db) {
        $this->reviewModel = new Review($db);
    }

    // Get all reviews for a news article
    public function getReviewsByNews($news_id) {
        $stmt = $this->reviewModel->readByNews($news_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all reviews by a customer
    public function getReviewsByCustomer($customer_id) {
        $stmt = $this->reviewModel->readByCustomer($customer_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add or update a review
    public function saveReview($data) {
        // Validate required fields
        $required = ['customerid', 'news_id', 'content', 'rating'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new Exception("Missing required field: $field");
            }
        }

        // Validate rating (1-5)
        $rating = (int)$data['rating'];
        if ($rating < 1 || $rating > 5) {
            throw new Exception("Rating must be between 1 and 5");
        }

        // Set review data
        $this->reviewModel->customerid = $data['customerid'];
        $this->reviewModel->news_id = $data['news_id'];
        $this->reviewModel->content = trim($data['content']);
        $this->reviewModel->rating = $rating;

        // Save the review
        return $this->reviewModel->create();
    }

    // Delete a review
    public function deleteReview($customer_id, $news_id) {
        $this->reviewModel->customerid = $customer_id;
        $this->reviewModel->news_id = $news_id;
        return $this->reviewModel->delete();
    }

    // Get average rating for a news article
    public function getAverageRating($news_id) {
        return $this->reviewModel->getAverageRating($news_id);
    }

    // Check if a customer has reviewed a news article
    public function hasCustomerReviewed($customer_id, $news_id) {
        return $this->reviewModel->hasReviewed($customer_id, $news_id);
    }
}
?>
