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
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Set user data
            $this->userModel->uname = $uname;
            $this->userModel->email = $email;
            $this->userModel->password = password_hash($password, PASSWORD_BCRYPT);
            $this->userModel->DOB = $DOB;
            $this->userModel->lname = $lname;
            $this->userModel->fname = $fname;

            // Create user
            $userCreated = $this->userModel->create();
            if (!$userCreated) {
                throw new Exception('Failed to create user account');
            }

            // Get the new user's ID
            $uid = $this->db->lastInsertId();
            
            // Set publisher data
            $this->publisherModel->uid = $uid;
            $this->publisherModel->description = $description;
            $this->publisherModel->taxcode = $taxcode;
            $this->publisherModel->location = $location;

            // Create publisher
            $publisherCreated = $this->publisherModel->create();
            if (!$publisherCreated) {
                throw new Exception('Failed to create publisher profile');
            }

            // Commit transaction
            $this->db->commit();
            return $uid;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Publisher registration error: ' . $e->getMessage());
            return false;
        }
    }

    public function getAll() {
        return $this->publisherModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($uid) {
        return $this->publisherModel->readOne($uid);
    }
}
?>
