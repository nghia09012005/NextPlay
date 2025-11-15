<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../service/UserService.php';

class UserController {
    private $service;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->service = new UserService($db);
    }

    public function register() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        echo "[UserController]: data";
        print_r($data);

        if (!isset($data['uname']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $uid = $this->service->register(
            $data["uname"], 
            $data["email"], 
            $data["password"]
        );

        if ($uid) {
            echo json_encode(["status" => "success", "uid" => $uid]);
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Registration failed"]);
        }
    }

    public function getAll() {
        header('Content-Type: application/json');
        $users = $this->service->getAll();
        echo json_encode(["status" => "success", "data" => $users]);
    }

    public function getOne($uid) {
        header('Content-Type: application/json');
        $user = $this->service->getOne($uid);
        if ($user) {
            echo json_encode(["status" => "success", "data" => $user]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }
    }
}
?>
