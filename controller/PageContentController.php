<?php
require_once __DIR__ . '/../service/PageContentService.php';

class PageContentController {
    private $contentService;

    public function __construct($db) {
        $this->contentService = new PageContentService($db);
    }

    public function getAllContent() {
        $content = $this->contentService->getAllContent();
        echo json_encode(['status' => 'success', 'data' => $content]);
    }

    public function updateContent() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!empty($data['page_key']) && !empty($data['section_key']) && isset($data['content_value'])) {
            if ($this->contentService->updateContent($data['page_key'], $data['section_key'], $data['content_value'])) {
                echo json_encode(['status' => 'success', 'message' => 'Content updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update content']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Incomplete data']);
        }
    }
}
?>
