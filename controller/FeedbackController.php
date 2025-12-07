<?php
require_once __DIR__ . '/../service/FeedbackService.php';

class FeedbackController {
    private $feedbackService;

    public function __construct($db) {
        $this->feedbackService = new FeedbackService($db);
    }

    private function jsonResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Get all feedback (for admin)
    public function getAllFeedback() {
        try {
            $feedback = $this->feedbackService->getAllFeedback();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $feedback
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get single feedback
    public function getFeedback($customerid, $gid) {
        try {
            $feedback = $this->feedbackService->getFeedbackByCustomerAndGame($customerid, $gid);
            if ($feedback) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'data' => $feedback
                ]);
            } else {
                $this->jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'Feedback not found'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Admin delete feedback
    public function adminDeleteFeedback($customerid, $gid) {
        try {
            $result = $this->feedbackService->adminDeleteFeedback($customerid, $gid);
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Feedback deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete feedback');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getGameReviews($gameId) {
        try {
            $reviews = $this->feedbackService->getGameReviews($gameId);
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

    public function addReview() {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            $data['customerid'] = $_SESSION['user_id'];

            $result = $this->feedbackService->addReview($data);
            
            if ($result === true) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Review added successfully'
                ]);
            } else {
                // $result contains the error message
                throw new Exception('Failed to add review: ' . $result);
            }
        } catch (Exception $e) {
            error_log("Error in addReview: " . $e->getMessage());
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateReview($gameId) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            $result = $this->feedbackService->updateReview($_SESSION['user_id'], $gameId, $data['content'], $data['rating']);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Review updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update review');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteReview($gameId) {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            $result = $this->feedbackService->deleteReview($_SESSION['user_id'], $gameId);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Review deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete review');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
