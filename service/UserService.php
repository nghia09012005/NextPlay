<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Customer.php';
require_once __DIR__ . '/../model/Admin.php';
require_once __DIR__ . '/../service/WishlistService.php';

class UserService {
    private $db;
    private $userModel;
    private $customerModel;
    private $adminModel;
    private $wishlistService;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->customerModel = new Customer($db);
        $this->adminModel = new Admin($db);
        $this->wishlistService = new WishlistService($db);
    }

    /**
     * Get database connection (for testing purposes)
     */
    public function getDb() {
        return $this->db;
    }

    private function validatePasswordStrength($password) {
        // Min 8 chars, at least 1 uppercase, 1 lowercase, 1 number, 1 special char
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($regex, $password)) {
            throw new Exception('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
        }
    }

    public function register($uname, $email, $password, $DOB, $lname, $fname) {
        // Validate password strength
        $this->validatePasswordStrength($password);

        // Start transaction
        $this->db->beginTransaction();
        
        try {

            print("register user service");
            // Set user data
            $this->userModel->uname = $uname;
            $this->userModel->email = $email;
            $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);
            $this->userModel->DOB = $DOB;
            $this->userModel->lname = $lname;
            $this->userModel->fname = $fname;

            // Create user - this will throw an exception if username/email exists
            $uid = $this->userModel->create();

            
            
            if (!$uid) {
                throw new Exception('Failed to create user account');
            }

            
            // Create customer record with initial balance
            $initialBalance = 0.00; // Set initial balance to 0
            $customerCreated = $this->customerModel->create($uid, $initialBalance);
            
            if (!$customerCreated) {
                throw new Exception('Failed to create customer account');
            }

            $this->wishlistService->createWishlist($uid, 'Cart');

            // Commit transaction
            $this->db->commit();

            

            error_log("User and customer created successfully. User ID: " . $uid);
            return $uid;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Database error in UserService::register(): " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw new Exception('A database error occurred. Please try again.');
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in UserService::register(): " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e; // Re-throw with original message
        }
    }

    public function getAll() {
        return $this->userModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($uid) {
        $user = $this->userModel->readOne($uid);
        if ($user) {
            // Get balance if customer
            $customer = $this->customerModel->readOne($uid);
            if ($customer) {
                $user['balance'] = $customer['balance'];
            }
            unset($user['password']);
        }
        return $user;
    }

    public function updateUser($uid, $data) {
        // Get existing user data
        $user = $this->userModel->readOne($uid);
        if (!$user) {
            return false;
        }

        // Update model properties
        $this->userModel->uid = $uid;
        $this->userModel->uname = $data['uname'] ?? $user['uname'];
        $this->userModel->email = $data['email'] ?? $user['email'];
        $this->userModel->DOB = $data['DOB'] ?? $user['DOB'];
        $this->userModel->lname = $data['lname'] ?? $user['lname'];
        $this->userModel->fname = $data['fname'] ?? $user['fname'];
        $this->userModel->avatar = $data['avatar'] ?? $user['avatar'];
        $this->userModel->password = $user['password'];

        // Update the user
        return $this->userModel->update();
    }

    public function updatePassword($uid, $currentPassword, $newPassword) {
        // Get user data
        $user = $this->userModel->readOne($uid);
        if (!$user) {
            return 'User not found';
        }
        
        // Verify password
        if (!password_verify($currentPassword, $user['password'])) {
            return 'Current password is incorrect';
        }

        try {
            $this->validatePasswordStrength($newPassword);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        
        // Update password
        $this->userModel->uid = $uid;
        // Update password and timestamp
        $query = "UPDATE `user` SET password = :password, password_changed_at = NOW() WHERE uid = :uid";
        $stmt = $this->db->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':uid', $uid);
        
        if($stmt->execute()) {
             return 'Password updated successfully';
        }
        return 'Failed to update password';
    }

    public function authenticate($uname, $password) {
        try {
          
            // Get user by username
            $user = $this->userModel->getByUsername($uname);
            
            if (!$user) {
                // To prevent user enumeration, we could delay here, but for now just return false
                // Or throw specific error if we want to be helpful but less secure against enumeration
                throw new Exception('Invalid username or password');
            }

            // Check for lockout
            if (isset($user['lockout_time']) && $user['lockout_time']) {
                $lockoutTime = new DateTime($user['lockout_time']);
                $now = new DateTime();
                if ($now < $lockoutTime) {
                    $diff = $now->diff($lockoutTime);
                    $minutes = $diff->i + ($diff->h * 60) + 1; // Round up
                    throw new Exception("Account is locked. Please try again in $minutes minutes.");
                } else {
                    // Lockout expired, reset
                    $this->resetLockout($user['uid']);
                }
            }
            
    

            // Verify password (plain text comparison for testing)
            if ($password === $user['password']) {
                // Reset failed attempts on success

          
                // $this->resetLockout($user['uid']);
                

             

                // Remove password from returned user data
                unset($user['password']);
                
                

                // Get balance if customer
                $customer = $this->customerModel->readOne($user['uid']);

               

                if ($customer) {
                    $user['balance'] = $customer['balance'];
                }
                
                
                
                return $user;
            } else {
                // Increment failed attempts
                $currentAttempts = $user['failed_attempts'] ?? 0;
                $this->handleFailedLogin($user['uid'], $currentAttempts);
                
                $newAttempts = $currentAttempts + 1;
                $remaining = 5 - $newAttempts;
                
                if ($remaining > 0) {
                     throw new Exception("Invalid username or password. Remaining attempts: $remaining");
                } else {
                     throw new Exception("Invalid username or password. Account is now locked.");
                }
            }
            // print("fail");
            
        } catch (PDOException $e) {
            error_log('Authentication error: ' . $e->getMessage());
            throw new Exception('System error during authentication');
        }
    }

    private function resetLockout($uid) {
        $query = "UPDATE `user` SET failed_attempts = 0, lockout_time = NULL WHERE uid = :uid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
    }

    private function handleFailedLogin($uid, $currentAttempts) {
        $newAttempts = $currentAttempts + 1;
        $lockoutTime = null;
        
        // Lockout policy: 5 failed attempts = 15 minutes lockout
        if ($newAttempts >= 5) {
            $lockoutTime = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $query = "UPDATE `user` SET failed_attempts = :attempts, lockout_time = :lockout WHERE uid = :uid";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':attempts', $newAttempts);
        $stmt->bindParam(':lockout', $lockoutTime);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
    }

    public function uploadAvatar($uid, $fileTmpPath) {
        require_once __DIR__ . '/CloudinaryService.php';
        $cloudinary = new CloudinaryService();
        
        $secureUrl = $cloudinary->uploadImage($fileTmpPath);
        
        if (!$secureUrl) {
            return false;
        }

        $this->userModel->uid = $uid;
        $user = $this->userModel->readOne($uid);
        if (!$user) return false;

        $this->userModel->uname = $user['uname'];
        $this->userModel->email = $user['email'];
        $this->userModel->password = $user['password'];
        $this->userModel->DOB = $user['DOB'];
        $this->userModel->lname = $user['lname'];
        $this->userModel->fname = $user['fname'];
        $this->userModel->avatar = $secureUrl;
        
        return $this->userModel->update();
    }

    public function getUserGames($uid) {
        require_once __DIR__ . '/../model/Library.php';
        $libraryModel = new Library($this->db);
        return $libraryModel->getAllUserGames($uid)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isAdmin($uid) {
        return $this->adminModel->readOne($uid) ? true : false;
    }

    public function deposit($uid, $amount) {
        if ($amount <= 0) {
            return false;
        }

        try {
            // Get current customer data
            $customer = $this->customerModel->readOne($uid);
            if (!$customer) {
                return false;
            }

            // Calculate new balance
            $currentBalance = (float)$customer['balance'];
            $newBalance = $currentBalance + $amount;

            // Update balance
            $this->customerModel->uid = $uid;
            $this->customerModel->balance = $newBalance;
            
            if ($this->customerModel->update()) {
                return $newBalance;
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Error in UserService::deposit: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a user by ID
     */
    public function delete($uid) {
        try {
            // First check if user exists
            $user = $this->userModel->readOne($uid);
            if (!$user) {
                throw new Exception('User not found', 404);
            }

            // Delete from admin table if exists
            $this->adminModel->delete($uid);

            // Delete from customer table if exists
            $this->customerModel->delete($uid);

            // Delete from user table
            return $this->userModel->delete($uid);
        } catch (Exception $e) {
            error_log('Error in UserService::delete: ' . $e->getMessage());
            throw $e;
        }
    }
}
?>