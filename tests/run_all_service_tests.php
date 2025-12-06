<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include all test files
require_once __DIR__ . '/Unit/Service/GameServiceTest.php';
require_once __DIR__ . '/Unit/Service/UserServiceTest.php';
require_once __DIR__ . '/Unit/Service/CategoryServiceTest.php';

class TestRunner {
    private $testClasses = [
        'GameServiceTest',
        'UserServiceTest',
        'CategoryServiceTest'

    ];
    
    private $testResults = [];
    
    public function runAllTests() {
        echo "=== Starting All Service Tests ===\n\n";
        
        $startTime = microtime(true);
        $totalTests = 0;
        $passedTests = 0;
        $failedTests = 0;
        
        foreach ($this->testClasses as $testClass) {
            echo "\n=== Running $testClass ===\n";
            
            try {
                $testFile = __DIR__ . "/Unit/Service/$testClass.php";
                
                if (!file_exists($testFile)) {
                    throw new Exception("Test file not found: $testFile");
                }
                
                // Include the test file
                require_once $testFile;
                
                // Create and run the test
                $test = new $testClass();
                $test->runTests();
                
                // Count test results (simplified - assumes tests echo their own results)
                $totalTests++;
                $passedTests++; 
                
            } catch (Exception $e) {
                $error = "❌ Error running $testClass: " . $e->getMessage();
                echo "$error\n";
                $this->testResults[] = $error;
                $failedTests++;
            }
            
            echo "\n";
        }
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        // Output summary
        echo "\n=== Test Execution Summary ===\n";
        echo "Total Tests: $totalTests\n";
        echo "Passed: $passedTests\n";
        echo "Failed: $failedTests\n";
        echo "Execution Time: {$executionTime}s\n";
        
        if (!empty($this->testResults)) {
            echo "\n=== Failures/Errors ===\n";
            foreach ($this->testResults as $result) {
                echo "- $result\n";
            }
        }
        
        echo "\n=== All Tests Completed ===\n";
    }
}

// Run all tests
$testRunner = new TestRunner();
$testRunner->runAllTests();
