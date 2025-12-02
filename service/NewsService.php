<?php
require_once __DIR__ . '/../model/News.php';
require_once __DIR__ . '/../model/NewsComment.php';

class NewsService {
    private $newsModel;
    private $newsCommentModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
        $this->newsCommentModel = new NewsComment($db);
    }

    public function getAllNews() {
        return $this->newsModel->getAllNews();
    }

    public function getNewsById($id) {
        $news = $this->newsModel->getNewsById($id);
        if ($news) {
            $news['comments'] = $this->newsCommentModel->getCommentsByNewsId($id);
        }
        return $news;
    }

    public function createNews($data) {
        // Validate required fields
        $requiredFields = [
            'title' => 'string',
            'content' => 'string',
            'author_id' => 'integer'
        ];
        
        $errors = [];
        
        foreach ($requiredFields as $field => $type) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[] = "The $field field is required";
            } elseif ($type === 'integer' && !is_numeric($data[$field])) {
                $errors[] = "The $field must be a number";
            } elseif ($type === 'string' && !is_string($data[$field])) {
                $errors[] = "The $field must be a string";
            }
        }
        
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
        
        // Sanitize and prepare data
        $newsData = [
            'title' => trim($data['title']),
            'content' => trim($data['content']),
            'author_id' => (int)$data['author_id'],
            'thumbnail' => isset($data['thumbnail']) ? trim($data['thumbnail']) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Additional validation
        if (strlen($newsData['title']) < 5) {
            throw new Exception('Title must be at least 5 characters long');
        }
        
        if (strlen($newsData['content']) < 10) {
            throw new Exception('Content must be at least 10 characters long');
        }
        
        return $this->newsModel->createNews($newsData);
    }

    public function updateNews($id, $data) {
        if (empty($data['author_id'])) {
            throw new Exception("Author ID is required");
        }
        return $this->newsModel->updateNews($id, $data);
    }

    public function deleteNews($id, $author_id) {
        return $this->newsModel->deleteNews($id, $author_id);
    }

    public function addComment($data) {
        $requiredFields = ['news_id', 'user_id', 'content'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        return $this->newsCommentModel->createComment($data);
    }

    public function deleteComment($id, $user_id) {
        return $this->newsCommentModel->deleteComment($id, $user_id);
    }
}
?>
