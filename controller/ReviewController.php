<?php
require_once __DIR__ . '/../service/ReviewService.php';

class ReviewController {
    private $reviewService;

    public function __construct($db) {
        $this->reviewService = new ReviewService($db);
    }

    // Send JSON response
    private function jsonResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Get all reviews (Admin)
    public function getAll() {
        try {
            $reviews = $this->reviewService->getAll();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $reviews
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get all reviews for a news article
    public function getNewsReviews($news_id) {
        try {
            $reviews = $this->reviewService->getReviewsByNews($news_id);
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $reviews
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get all reviews by a customer
    public function getCustomerReviews($customer_id) {
        try {
            $reviews = $this->reviewService->getReviewsByCustomer($customer_id);
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $reviews
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Add or update a review
    public function saveReview() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            $result = $this->reviewService->saveReview($data);
            
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Review saved successfully',
                    'data' => $data
                ]);
            } else {
                throw new Exception('Failed to save review');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Delete a review
    public function deleteReview($customer_id, $news_id) {
        try {
            $result = $this->reviewService->deleteReview($customer_id, $news_id);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Review deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete review or review not found');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get average rating for a news article
    public function getNewsAverageRating($news_id) {
        try {
            $average = $this->reviewService->getAverageRating($news_id);
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => [
                    'news_id' => $news_id,
                    'average_rating' => (float)$average
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Check if a customer has reviewed a news article
    public function checkCustomerReview($customer_id, $news_id) {
        try {
            $hasReviewed = $this->reviewService->hasCustomerReviewed($customer_id, $news_id);
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => [
                    'has_reviewed' => $hasReviewed
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
