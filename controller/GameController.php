<?php
require_once __DIR__ . '/../service/GameService.php';

class GameController {
    private $gameService;

    public function __construct($db) {
        $this->gameService = new GameService($db);
    }

    // Get all games (public)
    public function getAll() {
        header('Content-Type: application/json');
        try {
            $games = $this->gameService->getAllGames();
            echo json_encode([
                'status' => 'success',
                'data' => $games
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get game by ID (public)
    public function getOne($gameId) {
        header('Content-Type: application/json');
        try {
            $game = $this->gameService->getGameById($gameId);
            if ($game) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $game
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Game not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get games by publisher (protected)
    public function getPublisherGames($publisherId) {
        header('Content-Type: application/json');
        try {
            $games = $this->gameService->getPublisherGames($publisherId);
            echo json_encode([
                'status' => 'success',
                'data' => $games
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get games for the currently logged-in publisher
    public function getMyGames() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Authentication required');
            }
            
            $games = $this->gameService->getPublisherGames($_SESSION['user_id']);
            echo json_encode([
                'status' => 'success',
                'data' => $games
            ]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Create new game (protected)
    public function create() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('You must be logged in to create games');
            }
            
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validate required fields
            $required = ['name', 'version', 'description', 'cost'];
            $missing = array_diff($required, array_keys($data));
            
            if (!empty($missing)) {
                throw new Exception('Missing required fields: ' . implode(', ', $missing));
            }

            $result = $this->gameService->createGame(
                $data['name'],
                $data['version'],
                $data['description'],
                $data['cost'],
                $_SESSION['user_id']
            );

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Game created successfully',
                    'gameId' => $result  // Return the game ID directly from the service
                ]);
            } else {
                throw new Exception('Failed to create game');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Update game (protected)
    public function update($gameId) {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $result = $this->gameService->updateGame(
                $gameId,
                $data['name'] ?? null,
                $data['version'] ?? null,
                $data['description'] ?? null,
                $data['cost'] ?? null
            );

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Game updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update game');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Delete game (protected)
    public function delete($gameId) {
        header('Content-Type: application/json');
        try {
            $result = $this->gameService->deleteGame($gameId);
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Game deleted successfully'
                ]);
            } else {
                throw new Exception('Failed to delete game');
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
