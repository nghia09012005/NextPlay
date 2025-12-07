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

    /**
     * Update a publisher
     */
    public function update($uid, $data) {
        try {
            // Check if publisher exists
            $publisher = $this->publisherModel->readOne($uid);
            if (!$publisher) {
                throw new Exception('Publisher not found', 404);
            }

            // Update publisher data
            $this->publisherModel->uid = $uid;
            $this->publisherModel->description = $data['description'] ?? $publisher['description'];
            $this->publisherModel->taxcode = $data['taxcode'] ?? $publisher['taxcode'];
            $this->publisherModel->location = $data['location'] ?? $publisher['location'];

            // Also update user data if provided
            if (isset($data['lname']) || isset($data['fname']) || isset($data['email'])) {
                $user = $this->userModel->readOne($uid);
                if ($user) {
                    $this->userModel->uid = $uid;
                    $this->userModel->lname = $data['lname'] ?? $user['lname'];
                    $this->userModel->fname = $data['fname'] ?? $user['fname'];
                    $this->userModel->email = $data['email'] ?? $user['email'];
                    $this->userModel->update();
                }
            }

            return $this->publisherModel->update();
        } catch (Exception $e) {
            error_log('Error in PublisherService::update: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a publisher
     */
    public function delete($uid) {
        try {
            // Check if publisher exists
            $publisher = $this->publisherModel->readOne($uid);
            if (!$publisher) {
                throw new Exception('Publisher not found', 404);
            }

            // Delete publisher first (foreign key constraint)
            $this->publisherModel->delete($uid);
            
            // Then delete user
            return $this->userModel->delete($uid);
        } catch (Exception $e) {
            error_log('Error in PublisherService::delete: ' . $e->getMessage());
            throw $e;
        }
    }
}
?>
