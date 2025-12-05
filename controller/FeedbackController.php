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
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            $data['customerid'] = $_SESSION['user_id'];

            $result = $this->feedbackService->addReview($data);
            
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Review added successfully'
                ]);
            } else {
                throw new Exception('Failed to add review');
            }
        } catch (Exception $e) {
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
