<?php
require_once __DIR__ . '/../service/NewsService.php';

class NewsController {
    private $newsService;

    public function __construct($db) {
        $this->newsService = new NewsService($db);
    }

    // Send JSON response
    private function jsonResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Get all news articles
    public function getAllNews() {
        try {
            $news = $this->newsService->getAllNews();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $news
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get news article by ID
    public function getNewsById($id) {
        try {
            $news = $this->newsService->getNewsById($id);
            if ($news) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'data' => $news
                ]);
            } else {
                $this->jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'News article not found'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Create new news article
    public function createNews() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            // Set default values
            $data['publish_date'] = date('Y-m-d H:i:s');
            $data['adminid'] = $_SESSION['user_id'] ?? null; // Get admin ID from session

            $result = $this->newsService->createNews($data);
            
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'News article created successfully',
                    'data' => $data
                ]);
            } else {
                throw new Exception('Failed to create news article');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Update news article
    public function updateNews($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            $result = $this->newsService->updateNews($id, $data);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'News article updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update news article or article not found');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Delete news article
    public function deleteNews($id) {
        try {
            $result = $this->newsService->deleteNews($id);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'News article deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete news article or article not found');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get news comments
    public function getNewsComments($news_id) {
        try {
            $comments = $this->newsService->getNewsComments($news_id);
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $comments
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Add comment to news
    public function addComment($news_id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data) || !isset($data['content'])) {
                throw new Exception('Comment content is required');
            }

            // Get user ID from session
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            $commentData = [
                'news_id' => $news_id,
                'customerid' => $_SESSION['user_id'],
                'content' => $data['content'],
                'rating' => isset($data['rating']) ? (int)$data['rating'] : 5 // Default to 5 if not provided
            ];

            $result = $this->newsService->addComment($commentData);
            
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Comment added successfully',
                    'data' => $commentData
                ]);
            } else {
                throw new Exception('Failed to add comment');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Delete comment
    public function deleteComment($news_id) {
        try {
            // Get user ID from session
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not authenticated');
            }

            // Delete by news_id and user_id (composite key)
            $result = $this->newsService->deleteComment($news_id, $_SESSION['user_id']);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Comment deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete comment or comment not found');
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
