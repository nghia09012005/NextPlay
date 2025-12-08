<?php
require_once __DIR__ . '/../service/NewsService.php';

class NewsController {
    private $newsService;

    public function __construct($db) {
        $this->newsService = new NewsService($db);
    }

    public function getAll() {
        $result = $this->newsService->getAll();
        echo json_encode(['status' => 'success', 'data' => $result]);
    }

    public function getOne($id) {
        $result = $this->newsService->getOne($id);
        if ($result) {
            echo json_encode(['status' => 'success', 'data' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'News not found']);
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->newsService->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'News created']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create news']);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));
        if ($this->newsService->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'News updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update news']);
        }
    }

    public function delete($id) {
        if ($this->newsService->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'News deleted']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete news']);
        }
    }

    // Stub for addComment
    public function addComment($id) {
        // Implement when Comments table is verified
        echo json_encode(['status' => 'success', 'message' => 'Comment added (Stub)']);
    }

    // Stub for deleteComment
    public function deleteComment($id) {
        // Implement when Comments table is verified
        echo json_encode(['status' => 'success', 'message' => 'Comment deleted (Stub)']);
    }
}
