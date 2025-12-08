<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/SystemSetting.php';
require_once __DIR__ . '/../service/CloudinaryService.php';

class SystemSettingController {
    private $db;
    private $setting;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->setting = new SystemSetting($this->db);
    }

    public function handleRequest($method, $uri) {
        // URI is now an array: ['settings', 'key']
        $key = isset($uri[1]) ? $uri[1] : null;

        switch ($method) {
            case 'GET':
                if ($key) {
                    $this->getSetting($key);
                } else {
                    $this->getAllSettings();
                }
                break;

            case 'POST':
                if ($key === 'upload') {
                    $this->uploadImage();
                } elseif ($key) {
                    $this->updateSetting($key);
                } else {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Setting key is required for update']);
                }
                break;
            case 'PUT':
                if ($key) {
                    $this->updateSetting($key);
                } else {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Setting key is required for update']);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }

    private function getAllSettings() {
        $data = $this->setting->getAll();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    private function getSetting($key) {
        $data = $this->setting->getByKey($key);
        if ($data) {
            echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Setting group not found']);
        }
    }

    private function updateSetting($key) {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
            return;
        }

        if ($this->setting->save($key, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Settings updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update settings']);
        }
    }
    private function uploadImage() {
        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
            return;
        }

        $cloudinary = new CloudinaryService();
        $fileTmpPath = $_FILES['file']['tmp_name'];
        
        $imageUrl = $cloudinary->uploadImage($fileTmpPath);

        if ($imageUrl) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Image uploaded successfully',
                'url' => $imageUrl
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Cloudinary upload failed']);
        }
    }
}
?>
