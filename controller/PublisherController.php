<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../service/PublisherService.php';

class PublisherController {
    private $service;

    public function __construct($db) {
        $this->db = $db;
        $this->service = new PublisherService($db);
    }

    public function register() {
        header('Content-Type: application/json');
        
        try {
            // Get and validate JSON input
            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format: ' . json_last_error_msg(), 400);
            }

            // Required fields validation
            $requiredFields = [
                'uname', 'email', 'password', 'DOB', 
                'lname', 'fname', 'description', 'taxcode', 'location'
            ];
            
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                throw new Exception('Missing required fields: ' . implode(', ', $missingFields), 400);
            }

            // Email format validation
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format', 400);
            }

            // Password strength validation
            if (strlen($data['password']) < 8) {
                throw new Exception('Password must be at least 8 characters long', 400);
            }

            // Date of birth validation
            $dob = DateTime::createFromFormat('Y-m-d', $data['DOB']);
            if (!$dob || $dob->format('Y-m-d') !== $data['DOB']) {
                throw new Exception('Invalid date of birth format. Please use YYYY-MM-DD', 400);
            }

            // Tax code validation (basic check)
            if (!preg_match('/^[0-9A-Za-z-]+$/', $data['taxcode'])) {
                throw new Exception('Tax code can only contain letters, numbers and hyphens', 400);
            }

            // Sanitize inputs
            $uname = filter_var($data['uname'], FILTER_SANITIZE_STRING);
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $lname = filter_var($data['lname'], FILTER_SANITIZE_STRING);
            $fname = filter_var($data['fname'], FILTER_SANITIZE_STRING);
            $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
            $taxcode = filter_var($data['taxcode'], FILTER_SANITIZE_STRING);
            $location = filter_var($data['location'], FILTER_SANITIZE_STRING);

            // Attempt registration
            $uid = $this->service->register(
                $uname,
                $email,
                $data['password'], // Don't sanitize password
                $data['DOB'],
                $lname,
                $fname,
                $description,
                $taxcode,
                $location
            );

            if ($uid) {
                http_response_code(201); // 201 Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Publisher registered successfully',
                    'uid' => $uid
                ]);
            } else {
                throw new Exception('Failed to register publisher. The username or email may already be in use.', 400);
            }

        } catch (PDOException $e) {
            // Database errors
            error_log('Database error in PublisherController: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'A database error occurred. Please try again later.'
            ]);
        } catch (Exception $e) {
            // Other errors
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            http_response_code($statusCode);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }



    public function getAll() {
        header('Content-Type: application/json');
        
        try {
            $publishers = $this->service->getAll();
            
            if (empty($publishers)) {
                throw new Exception('No publishers found', 404);
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $publishers,
                'count' => count($publishers)
            ]);
            
        } catch (PDOException $e) {
            error_log('Database error in PublisherController::getAll: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to retrieve publishers. Please try again later.'
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOne($uid) {
        header('Content-Type: application/json');
        
        try {
            if (!is_numeric($uid) || $uid <= 0) {
                throw new Exception('Invalid publisher ID', 400);
            }
            
            $publisher = $this->service->getOne($uid);
            
            if (!$publisher) {
                throw new Exception('Publisher not found', 404);
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $publisher
            ]);
            
        } catch (PDOException $e) {
            error_log('Database error in PublisherController::getOne: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to retrieve publisher information. Please try again later.'
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
