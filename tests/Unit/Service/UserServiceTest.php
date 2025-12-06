<?php
// Simple test file for UserService - No Composer required

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
$baseDir = dirname(dirname(dirname(__DIR__)));
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/model/User.php';
require_once $baseDir . '/model/Customer.php';
require_once $baseDir . '/model/Admin.php';
require_once $baseDir . '/service/UserService.php';

// Test class for UserService
class UserServiceTest
{
    private $userService;

    public function __construct()
    {
        // Local XAMPP database configuration
        $host = "localhost";
        $dbname = "ltw_game_shop";
        $username = "root";
        $password = "";
        $port = "3306";
        
        // Initialize database connection
        $db = new PDO(
            "mysql:host=$host;dbname=$dbname",
            $username,
            $password
        );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->userService = new UserService($db);
    }

    public function runTests()
    {
        $this->testPasswordStrength();
        // Add more test methods here
    }

    private function testPasswordStrength()
    {
        echo "Testing password strength through register method...\n";
        
        // Test valid password
        try {
            $result = $this->userService->register(
                'testuser_' . time(), // Unique username
                'test' . time() . '@example.com', // Unique email
                'ValidPass123!', // Valid password
                '2000-01-01',
                'Test',
                'User'
            );
            echo "✓ Valid password test passed\n";
            // Clean up - delete test user
            if (isset($result['uid'])) {
                $stmt = $this->userService->getDb()->prepare("DELETE FROM user WHERE uid = ?");
                $stmt->execute([$result['uid']]);
            }
        } catch (Exception $e) {
            echo "✗ Valid password test failed: " . $e->getMessage() . "\n";
        }

        // Test invalid password
        try {
            $this->userService->register(
                'testuser_invalid',
                'invalid@example.com',
                'weak', // Invalid password
                '2000-01-01',
                'Test',
                'Invalid'
            );
            echo "✗ Invalid password test failed (should have thrown exception)\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Password must be at least 8 characters') !== false) {
                echo "✓ Invalid password test passed\n";
            } else {
                echo "✗ Invalid password test failed with unexpected error: " . $e->getMessage() . "\n";
            }
        }
    }
}

// The test runner will instantiate and run the tests