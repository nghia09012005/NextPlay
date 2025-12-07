<?php
class HomepageController {
    private $homepageService;
    private $requestMethod;
    private $id = null;

    public function __construct($db) {
        $this->homepageService = new HomepageService($db);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Get ID from URL if exists
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriSegments = explode('/', $uri);
        
        // The ID is the last segment of the URL
        if (isset($uriSegments[count($uriSegments) - 1]) && is_numeric($uriSegments[count($uriSegments) - 1])) {
            $this->id = $uriSegments[count($uriSegments) - 1];
        }
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->id) {
                    $this->getHomepage($this->id);
                } else {
                    $this->getAllHomepages();
                }
                break;
            case 'POST':
                $this->createHomepage();
                break;
            case 'PUT':
                if ($this->id) {
                    $this->updateHomepage($this->id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            case 'DELETE':
                if ($this->id) {
                    $this->deleteHomepage($this->id);
                } else {
                    $this->notFoundResponse();
                }
                break;
            default:
                $this->methodNotAllowedResponse();
                break;
        }
    }

    private function getAllHomepages() {
        $homepages = $this->homepageService->getAllHomepages();
        
        if (!empty($homepages)) {
            $this->sendResponse(200, $homepages);
        } else {
            $this->notFoundResponse();
        }
    }

    private function getHomepage($id) {
        $homepage = $this->homepageService->getHomepageById($id);
        
        if ($homepage) {
            $this->sendResponse(200, $homepage);
        } else {
            $this->notFoundResponse();
        }
    }

    private function createHomepage() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if ($this->validateHomepage($data)) {
            $result = $this->homepageService->createHomepage($data);
            
            if (strpos($result['message'], 'successfully') !== false) {
                $this->sendResponse(201, $result);
            } else {
                $this->sendResponse(503, $result);
            }
        } else {
            $this->sendResponse(400, array("message" => "Unable to create homepage. Data is incomplete."));
        }
    }

    private function updateHomepage($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!empty($data)) {
            $result = $this->homepageService->updateHomepage($id, $data);
            
            if (strpos($result['message'], 'successfully') !== false) {
                $this->sendResponse(200, $result);
            } else if (strpos($result['message'], 'not found') !== false) {
                $this->notFoundResponse();
            } else {
                $this->sendResponse(503, $result);
            }
        } else {
            $this->sendResponse(400, array("message" => "No data provided for update."));
        }
    }

    private function deleteHomepage($id) {
        $result = $this->homepageService->deleteHomepage($id);
        
        if (strpos($result['message'], 'successfully') !== false) {
            $this->sendResponse(200, $result);
        } else if (strpos($result['message'], 'not found') !== false) {
            $this->notFoundResponse();
        } else {
            $this->sendResponse(503, $result);
        }
    }

    private function validateHomepage($data) {
        return (
            !empty($data['title']) &&
            !empty($data['description']) &&
            isset($data['variety']) &&
            !empty($data['activeplayer']) &&
            !empty($data['supporttime']) &&
            !empty($data['free'])
        );
    }

    private function sendResponse($statusCode, $data) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    private function notFoundResponse() {
        $this->sendResponse(404, array("message" => "Not Found"));
    }

    private function methodNotAllowedResponse() {
        $this->sendResponse(405, array("message" => "Method not allowed"));
    }
}
?>
