<?php
require_once __DIR__ . '/../service/FaqService.php';

class FaqController {
    private $faqService;

    public function __construct($db) {
        $this->faqService = new FaqService($db);
    }

    public function getAll() {
        try {
            $data = $this->faqService->getAllFaqsGrouped();
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
}
?>
