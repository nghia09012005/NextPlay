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
}
?>
