<?php
require_once __DIR__ . '/../service/PageContentService.php';
require_once __DIR__ . '/../service/CloudinaryService.php';

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

        if (!empty($data['page_key']) && !empty($data['section_key']) && !empty($data['content_key']) && isset($data['content_value'])) {
            if ($this->contentService->updateContent($data['page_key'], $data['section_key'], $data['content_key'], $data['content_value'])) {
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

    public function uploadContentImage() {
        if (!isset($_FILES['file']) || !isset($_POST['page_key']) || !isset($_POST['section_key']) || !isset($_POST['content_key'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Missing file or parameters']);
            return;
        }

        $cloudinary = new CloudinaryService();
        $fileTmpPath = $_FILES['file']['tmp_name'];
        
        $imageUrl = $cloudinary->uploadImage($fileTmpPath);

        if ($imageUrl) {
            // Update database with new URL
            if ($this->contentService->updateContent($_POST['page_key'], $_POST['section_key'], $_POST['content_key'], $imageUrl)) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Image uploaded and content updated',
                    'url' => $imageUrl
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Cloudinary upload failed']);
        }
    }
}
?>
