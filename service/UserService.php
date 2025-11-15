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

    public function register($uname, $email, $password) {
        $this->userModel->uname = $uname;
        $this->userModel->email = $email;
        $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);

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
}
?>
