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
        return $this->userModel->readOne($uid);
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
