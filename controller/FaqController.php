<?php
require_once __DIR__ . '/../service/FaqService.php';

class FaqController {
    private $faqService;

    public function __construct($db) {
        $this->faqService = new FaqService($db);
    }

    public function getAll() {
        try {
            // Check if raw format is requested (for admin table)
            $isRaw = isset($_GET['raw']) && $_GET['raw'] === 'true';
            
            if ($isRaw) {
                $data = $this->faqService->getAll();
            } else {
                $data = $this->faqService->getAllFaqsGrouped();
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (
                !isset($data->topic_key) || 
                !isset($data->topic_name) || 
                !isset($data->question) || 
                !isset($data->answer)
            ) {
                throw new Exception("Missing required fields");
            }

            // Set default icon if missing
            if (!isset($data->topic_icon)) {
                $data->topic_icon = 'bi-question-circle';
            }

            if ($this->faqService->create($data)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'FAQ created successfully'
                ]);
            } else {
                throw new Exception("Unable to create FAQ");
            }
        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if ($this->faqService->update($id, $data)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'FAQ updated successfully'
                ]);
            } else {
                throw new Exception("Unable to update FAQ");
            }
        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($id) {
        try {
            if ($this->faqService->delete($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'FAQ deleted successfully'
                ]);
            } else {
                throw new Exception("Unable to delete FAQ");
            }
        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
