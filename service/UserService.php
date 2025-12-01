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
        $this->userModel->uname = $uname;
        $this->userModel->email = $email;
        $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->DOB = $DOB;
        $this->userModel->lname = $lname;
        $this->userModel->fname = $fname;

        $uid = $this->userModel->create();

        if ($uid) {
            $this->customerModel->create($uid, 0.00);
            return $uid;
        }
        return false;
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
     * Authenticate user with username and password
     * @param string $uname Username
     * @param string $password Plain text password
     * @return array|false User data if authentication successful, false otherwise
     */
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
