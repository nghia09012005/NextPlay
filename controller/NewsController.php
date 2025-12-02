<?php
require_once __DIR__ . '/../service/NewsService.php';

class NewsController {
    private $newsService;

    public function __construct($db) {
        $this->newsService = new NewsService($db);
    }

    public function getAllNews() {
        try {
            $news = $this->newsService->getAllNews();
            $this->jsonResponse(200, $news);
        } catch (Exception $e) {
            $this->jsonResponse(500, ['error' => $e->getMessage()]);
        }
    }

    public function getNews($id) {
        try {
            $news = $this->newsService->getNewsById($id);
            if ($news) {
                $this->jsonResponse(200, $news);
            } else {
                $this->jsonResponse(404, ['error' => 'News not found']);
            }
        } catch (Exception $e) {
            $this->jsonResponse(500, ['error' => $e->getMessage()]);
        }
    }

    public function createNews() {
        try {
            // Get and validate input data
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if required fields are present
            $requiredFields = ['title', 'content', 'author_id'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                throw new Exception('Missing required fields: ' . implode(', ', $missingFields));
            }
            
            // Set default values for optional fields
            $data['thumbnail'] = $data['thumbnail'] ?? null;
            
            $result = $this->newsService->createNews($data);
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'News created successfully',
                    'data' => $data
                ]);
            } else {
                $this->jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Failed to create news'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateNews($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->newsService->updateNews($id, $data);
            if ($result) {
                $this->jsonResponse(200, ['message' => 'News updated successfully']);
            } else {
                $this->jsonResponse(400, ['error' => 'Failed to update news']);
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, ['error' => $e->getMessage()]);
        }
    }

    public function deleteNews($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['author_id'])) {
                throw new Exception('Author ID is required');
            }
            $result = $this->newsService->deleteNews($id, $data['author_id']);
            if ($result) {
                $this->jsonResponse(200, ['message' => 'News deleted successfully']);
            } else {
                $this->jsonResponse(400, ['error' => 'Failed to delete news']);
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, ['error' => $e->getMessage()]);
        }
    }

    public function addComment() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->newsService->addComment($data);
            if ($result) {
                $this->jsonResponse(201, ['message' => 'Comment added successfully']);
            } else {
                $this->jsonResponse(400, ['error' => 'Failed to add comment']);
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, ['error' => $e->getMessage()]);
        }
    }

    public function deleteComment($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['user_id'])) {
                throw new Exception('User ID is required');
            }
            $result = $this->newsService->deleteComment($id, $data['user_id']);
            if ($result) {
                $this->jsonResponse(200, ['message' => 'Comment deleted successfully']);
            } else {
                $this->jsonResponse(400, ['error' => 'Failed to delete comment']);
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, ['error' => $e->getMessage()]);
        }
    }

    private function jsonResponse($statusCode, $data) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }
}
?>
