<?php
require_once __DIR__ . '/../service/ContactService.php';

class ContactController {
    private $contactService;

    public function __construct($db) {
        $this->contactService = new ContactService($db);
    }

    public function createMessage() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !empty($data['name']) &&
            !empty($data['email']) &&
            !empty($data['subject']) &&
            !empty($data['message'])
        ) {
            if ($this->contactService->createMessage($data)) {
                http_response_code(201);
                echo json_encode(array("status" => "success", "message" => "Message sent successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("status" => "error", "message" => "Unable to send message."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "Incomplete data."));
        }
    }
}
?>
