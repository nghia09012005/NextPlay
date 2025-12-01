<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../service/CategoryService.php';

class CategoryController {
    private $categoryService;

    public function __construct($db) {
        $this->categoryService = new CategoryService($db);
    }

    public function getAll() {
        header('Content-Type: application/json');
        try {
            $categories = $this->categoryService->getAllCategories();
            echo json_encode([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOne($catId) {
        header('Content-Type: application/json');
        try {
            $category = $this->categoryService->getCategory($catId);
            if ($category) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $category
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Category not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function create() {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (empty($data['name'])) {
                throw new Exception('Category name is required');
            }

            $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
            $description = isset($data['description']) ? filter_var($data['description'], FILTER_SANITIZE_STRING) : null;

            $result = $this->categoryService->createCategory($name, $description);
            
            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Category created successfully'
                ]);
            } else {
                throw new Exception('Failed to create category');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($catId) {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (empty($data['name'])) {
                throw new Exception('Category name is required');
            }

            $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
            $description = isset($data['description']) ? filter_var($data['description'], FILTER_SANITIZE_STRING) : null;

            $result = $this->categoryService->updateCategory($catId, $name, $description);
            
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Category updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update category');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($catId) {
        header('Content-Type: application/json');
        try {
            $result = $this->categoryService->deleteCategory($catId);
            
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Category deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete category');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
