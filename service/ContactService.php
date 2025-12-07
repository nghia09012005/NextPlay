<?php
require_once __DIR__ . '/../model/ContactMessage.php';

class ContactService {
    private $db;
    private $contactModel;

    public function __construct($db) {
        $this->db = $db;
        $this->contactModel = new ContactMessage($db);
    }

    public function createMessage($data) {
        $this->contactModel->name = $data['name'];
        $this->contactModel->email = $data['email'];
        $this->contactModel->subject = $data['subject'];
        $this->contactModel->message = $data['message'];

        if($this->contactModel->create()) {
            return true;
        }
        return false;
    }
    public function getAllMessages() {
        $stmt = $this->contactModel->getAll();
        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $row;
        }
        return $messages;
    }

    public function updateStatus($id, $status) {
        return $this->contactModel->updateStatus($id, $status);
    }

    public function deleteMessage($id) {
        return $this->contactModel->delete($id);
    }
}
?>
