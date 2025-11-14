<?php
require_once 'controllers/UserController.php';

$controller = new UserController();

// Lấy URL
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

// Ví dụ: /users hoặc /users/1
if ($uri[0] === 'users') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($uri[1])) {
        $controller->index();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($uri[1])) {
        $controller->show($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($uri[1])) {
        $controller->update($uri[1]);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($uri[1])) {
        $controller->delete($uri[1]);
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
