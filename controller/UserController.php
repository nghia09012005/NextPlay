<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../service/UserService.php';

class UserController {
    private $service;

    public function __construct($db) {
        $this->service = new UserService($db);
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
            $requiredFields = ['uname', 'email', 'password', 'DOB', 'lname', 'fname'];
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

            // Sanitize inputs
            $uname = filter_var($data['uname'], FILTER_SANITIZE_STRING);
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $lname = filter_var($data['lname'], FILTER_SANITIZE_STRING);
            $fname = filter_var($data['fname'], FILTER_SANITIZE_STRING);

            // Attempt registration
            $uid = $this->service->register(
                $uname,
                $email,
                $data['password'], // Don't sanitize password
                $data['DOB'],
                $lname,
                $fname
            );

            if ($uid) {
                http_response_code(201); // 201 Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'uid' => $uid
                ]);
            } else {
                throw new Exception('Failed to register user. The username or email may already be in use.', 400);
            }

        } catch (PDOException $e) {
            // Database errors
            error_log('Database error in UserController: ' . $e->getMessage());
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

    public function signin() {
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

            // Validate required fields
            if (empty($data['uname']) || empty($data['password'])) {
                throw new Exception('Username and password are required', 400);
            }

            // Sanitize inputs
            $uname = filter_var($data['uname'], FILTER_SANITIZE_STRING);
            $password = $data['password']; // Don't sanitize password

            // Authenticate user
            $user = $this->service->authenticate($uname, $password);

            if ($user) {
                // Start session (if not already started)
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Set session variables
                $_SESSION['user_id'] = $user['uid'];
                $_SESSION['username'] = $user['uname'];
                $_SESSION['email'] = $user['email'];
             

                // Return success response with user data (without password)
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user' => $user
                ]);
            } else {
                throw new Exception('Invalid username or password', 401);
            }

        } catch (Exception $e) {
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
        $users = $this->service->getAll();
        echo json_encode(["status" => "success", "data" => $users]);
    }

    public function getOne($uid) {
        header('Content-Type: application/json');
        try {
            $user = $this->service->getOne($uid);
            if ($user) {
                // Remove sensitive data before sending response
                unset($user['password']);
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update current user's information
     * Gets user ID from session
     */
    public function update() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login first.']);
            return;
        }
        
        $uid = $_SESSION['user_id'];
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

            // Check if user exists
            $existingUser = $this->service->getOne($uid);
            if (!$existingUser) {
                http_response_code(404);
                echo json_encode(['message' => 'User not found']);
                return;
            }

            // Validate email if provided
            if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format', 400);
            }

            // Update user
            $result = $this->service->updateUser($uid, $data);
            
            if ($result) {
                // Get updated user data
                $updatedUser = $this->service->getOne($uid);
                unset($updatedUser['password']); // Remove sensitive data
                
                http_response_code(200);
                echo json_encode([
                    'message' => 'User updated successfully',
                    'user' => $updatedUser
                ]);
            } else {
                throw new Exception('Failed to update user', 500);
            }
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            http_response_code($statusCode);
            echo json_encode([
                'message' => $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }
}
?>
