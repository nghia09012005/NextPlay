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
        try {
            // Set user data
            $this->userModel->uname = $uname;
            $this->userModel->email = $email;
            $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);
            $this->userModel->DOB = $DOB;
            $this->userModel->lname = $lname;
            $this->userModel->fname = $fname;

            // Start transaction
            $this->db->beginTransaction();

            // Create user
            $uid = $this->userModel->create();
            
            if (!$uid) {
                throw new Exception('Failed to create user account');
            }

            // Create customer record
            $customerCreated = $this->customerModel->create($uid, 100.00);
            
            if (!$customerCreated) {
                $this->db->rollBack();
                error_log("Failed to create customer record for user ID: " . $uid);
                return false;
            }

            // Commit transaction
            $this->db->commit();
            
            error_log("User and customer created successfully. User ID: " . $uid);
            return $uid;
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error in UserService::register(): " . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        return $this->userModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($uid) {
        return $this->userModel->readOne($uid);
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
        try {
            // Get user data
            $user = $this->userModel->readOne($uid);
            if (!$user) {
                return 'User not found';
            }

            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return 'Current password is incorrect';
            }

            // Validate new password
            if (strlen($newPassword) < 8) {
                return 'New password must be at least 8 characters long';
            }

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Update password in database
            $result = $this->userModel->updatePassword($uid, $hashedPassword);
            
            return $result ? true : 'Failed to update password';
            
        } catch (PDOException $e) {
            error_log('Password update error: ' . $e->getMessage());
            return 'A database error occurred while updating password';
        } catch (Exception $e) {
            error_log('Error in updatePassword: ' . $e->getMessage());
            return $e->getMessage();
        }
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
                return $user;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
