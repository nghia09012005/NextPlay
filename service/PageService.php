<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Page.php';

class PageService {
    private $db;
    private $page;

    public function __construct($db = null) {
        if (!$db) {
            $database = new Database();
            $db = $database->getConnection();
        }
        $this->db = $db;
        $this->page = new Page($this->db);
    }

    public function getPageContent($slug) {
        if ($this->page->getBySlug($slug)) {
            return [
                'status' => 'success',
                'data' => [
                    'slug' => $this->page->slug,
                    'title' => $this->page->title,
                    'content' => json_decode($this->page->content, true),
                    'updated_at' => $this->page->updated_at
                ]
            ];
        } else {
            return ['status' => 'error', 'message' => 'Page not found'];
        }
    }

    public function updatePageContent($slug, $data) {
        // Basic validation: Check if user is admin (This should be handled in Controller or Middleware, 
        // but for now we assume the Controller checks the session/token)
        
        $this->page->slug = $slug;
        $this->page->title = $data['title'] ?? 'Untitled';
        $this->page->content = json_encode($data['content'], JSON_UNESCAPED_UNICODE);

        if ($this->page->update()) {
            return ['status' => 'success', 'message' => 'Page updated successfully'];
        } else {
            return ['status' => 'error', 'message' => 'Unable to update page'];
        }
    }
}
?>
