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

            try {
                // Attempt registration
                $uid = $this->service->register(
                    $uname,
                    $email,
                    $data['password'], // Don't sanitize password
                    $data['DOB'],
                    $lname,
                    $fname
                );

                http_response_code(201); // 201 Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'User registered successfully',
                    'uid' => $uid
                ]);
            } catch (Exception $e) {
                // Rethrow with the original error message
                throw new Exception($e->getMessage(), $e->getCode() ?: 400);
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
                echo json_encode([
                    'status' => 'success',
                    'data' => $user
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'User not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update user's password
     * Requires current password for verification
     */
    public function updatePassword() {
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

            // Validate required fields
            if (empty($data['currentPassword']) || empty($data['newPassword'])) {
                throw new Exception('Both currentPassword and newPassword are required', 400);
            }

            // Update password
            $result = $this->service->updatePassword(
                $uid,
                $data['currentPassword'],
                $data['newPassword']
            );
            
            if ($result === true) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Password updated successfully'
                ]);
            } else {
                throw new Exception($result ?: 'Failed to update password', 400);
            }
        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 500;
            http_response_code($statusCode);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update current user's information
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

    public function uploadAvatar($uid) {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_FILES['avatar'])) {
                throw new Exception('No file uploaded', 400);
            }

            $file = $_FILES['avatar'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));
            $allowed = array('jpg', 'jpeg', 'png', 'gif');

            if (in_array($fileActualExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 5000000) { // 5MB
                        // Upload to Cloudinary via Service
                        if ($this->service->uploadAvatar($uid, $fileTmpName)) {
                            // Fetch updated user to get the new avatar URL
                            $updatedUser = $this->service->getOne($uid);
                            echo json_encode([
                                'status' => 'success', 
                                'message' => 'Avatar uploaded successfully',
                                'avatar' => $updatedUser['avatar']
                            ]);
                        } else {
                            throw new Exception('Failed to upload avatar to Cloudinary', 500);
                        }
                    } else {
                        throw new Exception('File is too big', 400);
                    }
                } else {
                    throw new Exception('There was an error uploading your file', 400);
                }
            } else {
                throw new Exception('You cannot upload files of this type', 400);
            }

        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getGames($uid) {
        header('Content-Type: application/json');
        try {
            $games = $this->service->getUserGames($uid);
            echo json_encode(['status' => 'success', 'data' => $games]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function checkAdmin() {
        header('Content-Type: application/json');
        try {
            if (!isset($_GET['uid'])) {
                throw new Exception('User ID is required', 400);
            }
            $uid = $_GET['uid'];
            $isAdmin = $this->service->isAdmin($uid);
            echo json_encode(['status' => 'success', 'isAdmin' => $isAdmin]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function toggleUserLock() {
        header('Content-Type: application/json');
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format', 400);
            }

            if (!isset($data['uid']) || !isset($data['lock'])) {
                throw new Exception('User ID and lock status are required', 400);
            }
            
            // Authorization check (Ensure requester is Admin)
            if (!isset($_SESSION['user_id']) || !$this->service->isAdmin($_SESSION['user_id'])) {
                throw new Exception('Unauthorized access', 403);
            }

            $duration = isset($data['duration']) ? $data['duration'] : 'permanent';

            $result = $this->service->toggleUserLock($data['uid'], $data['lock'], $duration);

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'User lock status updated successfully',
	                'locked' => $data['lock'],
                    'duration' => $duration
                ]);
            } else {
                throw new Exception('Failed to update user lock status', 500);
            }

        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deposit($uid) {
        header('Content-Type: application/json');
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            // Check authorization: User can only deposit to their own account
            if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $uid) {
                 // Unless admin? For now strictly own account or just rely on router auth check which passed $uid
                 // The router calls this method with $uri[1] which is $uid from URL.
                 // We should ensure the logged in user matches the target $uid.
                 if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $uid) {
                     throw new Exception('Unauthorized action', 403);
                 }
            }

            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);

            if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
                throw new Exception('Invalid amount', 400);
            }

            $amount = (float)$data['amount'];
            $newBalance = $this->service->deposit($uid, $amount);

            if ($newBalance !== false) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Deposit successful',
                    'new_balance' => $newBalance
                ]);
            } else {
                throw new Exception('Deposit failed', 500);
            }

        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function adminResetPasswordEndpoint() {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format', 400);
            }

            if (!isset($data['newPassword'])) {
                throw new Exception('New password is required', 400);
            }

            // Authorization check
             if (!isset($_SESSION['user_id']) || !$this->service->isAdmin($_SESSION['user_id'])) {
                 throw new Exception('Unauthorized access', 403);
             }

            $uid = isset($data['uid']) ? $data['uid'] : null;
             if (!$uid) {
                 throw new Exception('Target user ID is required', 400);
            }

            $result = $this->service->adminResetPassword($uid, $data['newPassword']);

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Password reset successfully'
                ]);
            } else {
                throw new Exception('Failed to reset password', 500);
            }

        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function adminGetUserDetail() {
        header('Content-Type: application/json');
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            if (!isset($_GET['uid'])) {
                throw new Exception('User ID is required', 400);
            }
            $uid = $_GET['uid'];
            
            // Authorization check
            if (!isset($_SESSION['user_id']) || !$this->service->isAdmin($_SESSION['user_id'])) {
                throw new Exception('Unauthorized access', 403);
            }

            // 1. Get User Info
            $userInfo = $this->service->getOne($uid);
            if (!$userInfo) {
                throw new Exception('User not found', 404);
            }
            unset($userInfo['password']);

            // 2. Get User Games
            $games = $this->service->getUserGames($uid);

            // 3. Get User Feedbacks (Game Reviews)
            require_once __DIR__ . '/../service/FeedbackService.php';
            $feedbackService = new FeedbackService($this->service->getDb()); // Access DB from UserService
            $feedbacks = $feedbackService->getFeedbacksByCustomer($uid);

            // 4. Get User Comments (News Reviews)
            require_once __DIR__ . '/../service/ReviewService.php';
            $reviewService = new ReviewService($this->service->getDb());
            $comments = $reviewService->getReviewsByCustomer($uid);

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'user' => $userInfo,
                    'games' => $games,
                    'feedbacks' => $feedbacks,
                    'comments' => $comments
                ]
            ]);

        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
?>