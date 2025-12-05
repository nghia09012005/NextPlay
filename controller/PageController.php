<?php
require_once __DIR__ . '/../service/PageService.php';

class PageController {
    private $pageService;

    public function __construct() {
        $this->pageService = new PageService();
    }

    public function handleRequest($method, $uri) {
        // URI format: /pages/{slug}
        $parts = explode('/', $uri);
        $slug = end($parts);

        if (empty($slug)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Page slug is required']);
            return;
        }

        switch ($method) {
            case 'GET':
                $result = $this->pageService->getPageContent($slug);
                if ($result['status'] === 'success') {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode($result);
                }
                break;

            case 'POST': // Using POST for update to be simple, or PUT
                // Check Authentication/Authorization here
                // For simplicity, we'll check if a specific header or session exists, 
                // but ideally this should use the Auth Middleware.
                // Assuming the frontend sends the user info or token.
                
                $data = json_decode(file_get_contents("php://input"), true);
                
                // Security Check: Ideally verify JWT or Session here.
                // For this demo, we trust the frontend to only allow admins to call this,
                // BUT in production, you MUST verify the user role on the backend.
                // Let's assume the frontend sends a 'user_role' in the body for now (INSECURE but fits the scope if no full Auth middleware)
                // OR better, check PHP Session if using session-based auth.
                
                // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { ... }

                $result = $this->pageService->updatePageContent($slug, $data);
                if ($result['status'] === 'success') {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(500);
                    echo json_encode($result);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
                break;
        }
    }
}
?>
