<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../service/GameService.php';

class GameController {
    private $gameService;

    public function __construct($db = null) {
        if (!$db) {
            $database = new Database();
            $db = $database->getConnection();
        }
        $this->gameService = new GameService($db);
    }

    public function getAll() {
        header('Content-Type: application/json');
        try {
            $games = $this->gameService->getAllGames();
            
            if (!empty($games)) {
                echo json_encode([
                    "status" => "success",
                    "data" => $games
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "No games found."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function getOne($id) {
        header('Content-Type: application/json');
        try {
            $game = $this->gameService->getGameById($id);

            if ($game) {
                echo json_encode([
                    "status" => "success",
                    "data" => $game
                ]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Game not found."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
    public function create() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!empty($data['name']) && !empty($data['price'])) {
                if ($this->gameService->createGame($data)) {
                    http_response_code(201);
                    echo json_encode(["status" => "success", "message" => "Game created successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to create game."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Incomplete data."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function update($id) {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!empty($data['name'])) {
                if ($this->gameService->updateGame($id, $data)) {
                    echo json_encode(["status" => "success", "message" => "Game updated successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to update game."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Incomplete data."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function updateStatus($id) {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!empty($data['status'])) {
                if ($this->gameService->updateGameStatus($id, $data['status'])) {
                    echo json_encode(["status" => "success", "message" => "Game status updated successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["status" => "error", "message" => "Unable to update game status."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Incomplete data."]);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine()]);
        }
    }

    public function delete($id) {
        header('Content-Type: application/json');
        try {
            if ($this->gameService->deleteGame($id)) {
                echo json_encode(["status" => "success", "message" => "Game deleted successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["status" => "error", "message" => "Unable to delete game."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
?>
