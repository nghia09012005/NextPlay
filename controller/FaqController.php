<?php
require_once __DIR__ . '/../service/FaqService.php';

class FaqController {
    private $faqService;

    public function __construct($db) {
        $this->faqService = new FaqService($db);
    }

    private function jsonResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function getAll() {
        try {
            $data = $this->faqService->getAllFaqsGrouped();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllFlat() {
        try {
            $data = $this->faqService->getAllFaqs();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $data
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getById($id) {
        try {
            $faq = $this->faqService->getFaqById($id);
            if ($faq) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'data' => $faq
                ]);
            } else {
                $this->jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'FAQ not found'
                ]);
            }
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            // Validate required fields
            $required = ['topic_key', 'topic_name', 'topic_icon', 'question', 'answer'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            $result = $this->faqService->createFaq($data);
            
            if ($result) {
                $this->jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'FAQ created successfully'
                ]);
            } else {
                throw new Exception('Failed to create FAQ');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data)) {
                throw new Exception('No data provided');
            }

            $result = $this->faqService->updateFaq($id, $data);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'FAQ updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update FAQ');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($id) {
        try {
            $result = $this->faqService->deleteFaq($id);
            
            if ($result) {
                $this->jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'FAQ deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete FAQ');
            }
        } catch (Exception $e) {
            $this->jsonResponse(400, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getTopics() {
        try {
            $topics = $this->faqService->getTopics();
            $this->jsonResponse(200, [
                'status' => 'success',
                'data' => $topics
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
