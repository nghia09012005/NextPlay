<?php
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Publisher.php';

class PublisherService {
    private $db;
    private $userModel;
    private $publisherModel;

    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new User($db);
        $this->publisherModel = new Publisher($db);
    }

    public function register($uname, $email, $password, $DOB, $lname, $fname, $description, $taxcode, $location) {
        $this->userModel->uname = $uname;
        $this->userModel->email = $email;
        $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->DOB = $DOB;
        $this->userModel->lname = $lname;
        $this->userModel->fname = $fname;
        $this->publisherModel->description = $description;
        $this->publisherModel->taxcode = $taxcode;
        $this->publisherModel->location = $location;
        $uid = $this->userModel->create();

        if ($uid) {
            $this->publisherModel->create($uid);
            return $uid;
        }
        return false;
    }

    public function getAll() {
        return $this->publisherModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($uid) {
        return $this->publisherModel->readOne($uid);
    }
}
?>
