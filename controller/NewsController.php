<?php
require_once __DIR__ . '/../service/NewsService.php';

require_once __DIR__ . '/../service/ReviewService.php';

class NewsController {
    private $newsService;
    private $reviewService;

    public function __construct($db) {
        $this->newsService = new NewsService($db);
        $this->reviewService = new ReviewService($db);
    }

    public function getAll() {
        $result = $this->newsService->getAll();
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    public function getOne($id) {
        $result = $this->newsService->getOne($id);
        if ($result) {
            echo json_encode(['status' => 'success', 'data' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'News not found']);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->newsService->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'News created']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create news']);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->newsService->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'News updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update news']);
        }
    }

    public function delete($id) {
        if ($this->newsService->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'News deleted']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete news']);
        }
    }

    // Get comments for a news article
    public function getNewsComments($id) {
        try {
            $comments = $this->reviewService->getReviewsByNews($id);
            echo json_encode(['status' => 'success', 'data' => $comments]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Add a comment
    public function addComment($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (empty($data['content'])) {
                throw new Exception('Content is required');
            }

            // Ensure user IS logged in (Auth middleware should have run, but checking session ID)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Unauthorized', 401);
            }

            // Prepare review data structure
            $reviewData = [
                'news_id' => $id,
                'customerid' => $_SESSION['user_id'],
                'content' => $data['content'],
                'rating' => isset($data['rating']) ? $data['rating'] : 5 // Default rating if not provided
            ];

            if ($this->reviewService->saveReview($reviewData)) {
                echo json_encode(['status' => 'success', 'message' => 'Comment added successfully']);
            } else {
                throw new Exception('Failed to add comment');
            }
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($code);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Delete a comment
    public function deleteComment($id) { // This $id might be comment ID or news ID?
        // Route: DELETE /news/{id}/comments
        // This is ambiguous. Is {id} the news ID? Yes.
        // But to delete a specific comment, we usually need the user ID (customerid) or a unique comment ID.
        // The Review table key is composite (customerid, news_id) according to Review.php delete method.
        // So a user can only have ONE review per news? 
        // `deleteReview($customer_id, $news_id)` suggests yes.
        // If so, deleting "my" comment on news {id} works.
        // Admin might need to pass customer ID.
        
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Unauthorized', 401);
            }
            
            $customerId = $_SESSION['user_id'];
            // If admin, maybe allow deleting by passing query param? 
            // For now assume user deleting their own comment.
            
            if ($this->reviewService->deleteReview($customerId, $id)) {
                echo json_encode(['status' => 'success', 'message' => 'Comment deleted']);
            } else {
                throw new Exception('Failed to delete comment or not found');
            }
        } catch (Exception $e) {
             $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
             http_response_code($code);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
