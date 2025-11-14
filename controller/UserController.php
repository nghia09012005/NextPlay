<?php
require_once '../config/database.php';
require_once '../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function index() {
        $stmt = $this->user->readAll();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function show($uid) {
        $data = $this->user->readOne($uid);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        $this->user->uname = $input['uname'];
        $this->user->email = $input['email'];
        $this->user->password = password_hash($input['password'], PASSWORD_DEFAULT);

        if ($this->user->create()) {
            http_response_code(201);
            echo json_encode(['message' => 'User created']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error creating user']);
        }
    }

    public function update($uid) {
        $input = json_decode(file_get_contents('php://input'), true);
        $this->user->uid = $uid;
        $this->user->uname = $input['uname'];
        $this->user->email = $input['email'];
        $this->user->password = password_hash($input['password'], PASSWORD_DEFAULT);

        if ($this->user->update()) {
            echo json_encode(['message' => 'User updated']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error updating user']);
        }
    }

    public function delete($uid) {
        if ($this->user->delete($uid)) {
            echo json_encode(['message' => 'User deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error deleting user']);
        }
    }
}
?>
