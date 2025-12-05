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
}
?>
