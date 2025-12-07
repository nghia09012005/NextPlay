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

    /**
     * Create a new game
     */
    public function create() {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format', 400);
            }

            // Required fields
            if (empty($data['name']) || !isset($data['cost'])) {
                throw new Exception('Name and cost are required', 400);
            }

            $gameId = $this->gameService->createGame(
                $data['name'],
                $data['version'] ?? '1.0',
                $data['description'] ?? '',
                $data['cost'],
                $data['publisherid'] ?? null
            );
            
            if ($gameId) {
                http_response_code(201);
                echo json_encode([
                    "status" => "success",
                    "message" => "Game created successfully",
                    "data" => ["Gid" => $gameId]
                ]);
            } else {
                throw new Exception('Failed to create game', 500);
            }
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Update an existing game
     */
    public function update($id) {
        header('Content-Type: application/json');
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('Invalid game ID', 400);
            }

            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format', 400);
            }

            $result = $this->gameService->updateGame(
                $id,
                $data['name'] ?? null,
                $data['version'] ?? null,
                $data['description'] ?? null,
                $data['cost'] ?? null
            );
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Game updated successfully"
                ]);
            } else {
                throw new Exception('Failed to update game', 500);
            }
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Delete a game
     */
    public function delete($id) {
        header('Content-Type: application/json');
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('Invalid game ID', 400);
            }

            $result = $this->gameService->deleteGame($id);
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Game deleted successfully"
                ]);
            } else {
                throw new Exception('Failed to delete game', 500);
            }
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Get games for the current logged-in publisher
     */
    public function getMyGames() {
        header('Content-Type: application/json');
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(["status" => "error", "message" => "User not authenticated"]);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $games = $this->gameService->getUserGames($userId);
            
            echo json_encode([
                "status" => "success",
                "data" => $games
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    /**
     * Get games by publisher ID
     */
    public function getPublisherGames($publisherId) {
        header('Content-Type: application/json');
        try {
            if (!is_numeric($publisherId) || $publisherId <= 0) {
                throw new Exception('Invalid publisher ID', 400);
            }
            
            $games = $this->gameService->getPublisherGames($publisherId);
            
            echo json_encode([
                "status" => "success",
                "data" => $games
            ]);
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}
?>
