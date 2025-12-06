<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
$baseDir = dirname(dirname(dirname(__DIR__)));
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/model/Category.php';
require_once $baseDir . '/service/CategoryService.php';

class CategoryServiceTest {
    private $db;
    private $categoryService;
    private $testCategoryId; // To store the ID of the test category we create

    public function __construct() {
        // Database configuration
        $host = "localhost";
        $dbname = "ltw_game_shop";
        $username = "root";
        $password = "";
        
        // Initialize database connection
        $this->db = new PDO(
            "mysql:host=$host;dbname=$dbname",
            $username,
            $password
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Set up the test environment
        $this->testCategoryId = null;
        $this->categoryService = new CategoryService($this->db);
    }

    public function runTests() {
        $this->testGetAllCategories();
        $this->testGetCategoryById();
        $this->testCreateCategory();
        $this->testUpdateCategory();
    }

    private function testGetAllCategories() {
        echo "Testing getAllCategories()...\n";
        try {
            $categories = $this->categoryService->getAllCategories();
            if (is_array($categories) && count($categories) > 0) {
                echo "✓ getAllCategories() test passed - Found " . count($categories) . " categories\n";
                // Print first few categories for debugging
                $sample = array_slice($categories, 0, 3);
                foreach ($sample as $cat) {
                    echo "   - " . ($cat['name'] ?? 'N/A') . " (ID: " . ($cat['catId'] ?? 'N/A') . ")\n";
                }
                if (count($categories) > 3) {
                    echo "   ... and " . (count($categories) - 3) . " more\n";
                }
            } else {
                echo "✗ getAllCategories() test failed - No categories found or invalid format\n";
            }
        } catch (Exception $e) {
            echo "✗ readAll() test failed: " . $e->getMessage() . "\n";
        }
    }

    private function testGetCategoryById() {
        echo "\nTesting getCategory()...\n";
        try {
            // First, try to get a category ID from the database
            $stmt = $this->db->query("SELECT catId FROM category LIMIT 1");
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                $result = $this->categoryService->getCategory($category['catId']);
                if ($result && isset($result['catId'])) {
                    echo "✓ getCategory() test passed - Found category: " . ($result['name'] ?? 'N/A') . " (ID: " . $result['catId'] . ")\n";
                    echo "   - Description: " . ($result['description'] ?? 'N/A') . "\n";
                } else {
                    echo "✗ getCategory() test failed - Category not found or invalid format\n";
                    if ($result === null) {
                        echo "   - Method returned null\n";
                    } else {
                        echo "   - Response format: " . print_r($result, true) . "\n";
                    }
                }
            } else {
                echo "✗ readOne() test skipped - No categories in database\n";
            }
        } catch (Exception $e) {
            echo "✗ readOne() test failed: " . $e->getMessage() . "\n";
        }
    }

    private function testCreateCategory() {
        echo "\nTesting createCategory()...\n";
        $testCategoryName = 'Test Category ' . uniqid(); // Use uniqid() for more unique names
        $testDescription = 'This is a test category created by unit test at ' . date('Y-m-d H:i:s');
        
        try {
            // First, check if the category already exists
            $checkStmt = $this->db->prepare("SELECT catId FROM category WHERE name = ?");
            $checkStmt->execute([$testCategoryName]);
            
            if ($checkStmt->rowCount() > 0) {
                // If it exists, use a different name
                $testCategoryName .= ' ' . uniqid();
            }
            
            $result = $this->categoryService->createCategory($testCategoryName, $testDescription);
            
            if ($result) {
                $this->testCategoryId = $result;
                echo "✓ createCategory() test passed - Created category ID: " . $this->testCategoryId . "\n";
                
                // Verify the category was created
                $stmt = $this->db->prepare("SELECT * FROM category WHERE catId = ?");
                $stmt->execute([$this->testCategoryId]);
                $createdCategory = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($createdCategory) {
                    echo "   - Name: " . $createdCategory['name'] . "\n";
                    echo "   - Description: " . $createdCategory['description'] . "\n";
                } else {
                    echo "⚠️ Created category not found in database\n";
                }
            } else {
                echo "✗ createCategory() test failed - Returned false\n";
            }
        } catch (Exception $e) {
            echo "✗ createCategory() test failed: " . $e->getMessage() . "\n";
            // If it's a duplicate entry error, suggest cleaning up old test data
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "   - Note: Try cleaning up old test categories from the database\n";
            }
        }
    }
    
    private function testUpdateCategory() {
        if (!$this->testCategoryId) {
            echo "\n✗ updateCategory() test skipped - No test category available\n";
            return;
        }
        
        echo "\nTesting updateCategory()...\n";
        $newName = 'Updated Test Category ' . time();
        $newDescription = 'This is an updated test category';
        
        try {
            $result = $this->categoryService->updateCategory($this->testCategoryId, $newName, $newDescription);
            if ($result) {
                echo "✓ updateCategory() test passed - Category updated successfully\n";
                
                // Verify the update
                $stmt = $this->db->prepare("SELECT * FROM category WHERE catId = ?");
                $stmt->execute([$this->testCategoryId]);
                $updatedCategory = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($updatedCategory) {
                    echo "   - New name: " . $updatedCategory['name'] . "\n";
                    echo "   - New description: " . $updatedCategory['description'] . "\n";
                }
            } else {
                echo "✗ updateCategory() test failed - Returned false\n";
            }
        } catch (Exception $e) {
            echo "✗ updateCategory() test failed: " . $e->getMessage() . "\n";
        }
    }
    
   
}

// Run the tests
$test = new CategoryServiceTest();
$test->runTests();
