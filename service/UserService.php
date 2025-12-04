<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Customer.php';

class UserService {
    private $db;
    private $userModel;
    private $customerModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->customerModel = new Customer($db);
    }

    public function register($uname, $email, $password, $DOB, $lname, $fname) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
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

    /**
     * Update user information
     * @param int $uid User ID
     * @param array $data User data to update (uname, email, DOB, lname, fname)
     * @return bool True on success, false on failure
     */
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

        // Update the user
        return $this->userModel->update();
    }

    /**
     * Authenticate user with username and password
     * @param string $uname Username
     * @param string $password Plain text password
     * @return array|false User data if authentication successful, false otherwise
     */
    /**
     * Update user password
     * @param int $uid User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return array|string Returns true on success, error message on failure
     */
    public function updatePassword($uid, $currentPassword, $newPassword) {
        // try {
            // Get user data
            $user = $this->userModel->readOne($uid);
            if (!$user) {
                return 'User not found';
            }
            
            // Verify password
            if (!password_verify($currentPassword, $user['password'])) {
                return 'Current password is incorrect';
            }
            
            // Update password
            $this->userModel->uid = $uid;
            // $this->userModel->password = password_hash($newPassword, PASSWORD_BCRYPT);
            $this->userModel->updatePassword($uid, password_hash($newPassword, PASSWORD_BCRYPT));
            return 'Password updated successfully';
        // }
    }

    public function authenticate($uname, $password) {
        try {
            // Get user by username
            $user = $this->userModel->getByUsername($uname);
            
            if (!$user) {
                return false;
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Remove password from returned user data
                unset($user['password']);
                
                // Get balance if customer
                $customer = $this->customerModel->readOne($user['uid']);
                if ($customer) {
                    $user['balance'] = $customer['balance'];
                }
                
                return $user;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return false;
        }
    }

    public function uploadAvatar($uid, $filename) {
        $this->userModel->uid = $uid;
        // We need to fetch existing user data to update only avatar, 
        // OR update User model to allow updating single field.
        // For now, let's fetch, update avatar, and save back.
        // BUT User::update updates ALL fields.
        // So we must populate all fields.
        
        $user = $this->userModel->readOne($uid);
        if (!$user) return false;

        $this->userModel->uname = $user['uname'];
        $this->userModel->email = $user['email'];
        $this->userModel->password = $user['password'];
        $this->userModel->DOB = $user['DOB'];
        $this->userModel->lname = $user['lname'];
        $this->userModel->fname = $user['fname'];
        $this->userModel->avatar = $filename;
        
        return $this->userModel->update();
    }

    public function getUserGames($uid) {
        require_once __DIR__ . '/../model/Library.php';
        $libraryModel = new Library($this->db);
        return $libraryModel->getAllUserGames($uid)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>