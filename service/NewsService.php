<?php
require_once __DIR__ . '/../model/News.php';
require_once __DIR__ . '/../model/Review.php';

class NewsService {
    private $newsModel;
    private $reviewModel;

    public function __construct($db) {
        $this->newsModel = new News($db);
        $this->reviewModel = new Review($db);
    }

    public function getAllNews() {
        return $this->newsModel->getAllNews();
    }

    public function getNewsById($id) {
        $news = $this->newsModel->getNewsById($id);
        if ($news) {
            // Get reviews as comments
            $stmt = $this->reviewModel->readByNews($id);
            $news['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            'category' => isset($data['category']) ? trim($data['category']) : null,
            'source' => isset($data['source']) ? trim($data['source']) : null,
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

    public function updateNewsAdmin($id, $data) {
        return $this->newsModel->updateNewsAdmin($id, $data);
    }

    public function deleteNews($id, $author_id) {
        return $this->newsModel->deleteNews($id, $author_id);
    }

    public function deleteNewsAdmin($id) {
        return $this->newsModel->deleteNewsAdmin($id);
    }

    public function addComment($data) {
        // Add a default rating since it's required by the Review model
        $data['rating'] = $data['rating'] ?? 5; // Default to 5 if not provided

        // Set the review data
        $this->reviewModel->customerid = $data['customerid'];
        $this->reviewModel->news_id = $data['news_id'];
        $this->reviewModel->content = $data['content'];
        $this->reviewModel->rating = $data['rating'];

        // Create the review (comment)
        return $this->reviewModel->create();
    }

    public function getNewsComments($news_id) {
        $stmt = $this->reviewModel->readByNews($news_id);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteComment($news_id, $user_id) {
        // Delete the review directly using composite key
        $this->reviewModel->customerid = $user_id;
        $this->reviewModel->news_id = $news_id;
        return $this->reviewModel->delete();
    }

    private function isAdmin($user_id) {
        // Implement admin check logic here
        // This is a placeholder - you'll need to implement actual admin check
        // For example, check if user has admin role in the database
        return false;
    }
}
?>
