<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
$baseDir = dirname(dirname(dirname(__DIR__)));
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/model/Game.php';
require_once $baseDir . '/service/GameService.php';

class GameServiceTest {
    private $db;
    private $gameService;

    public function __construct() {
        // Database configuration
        $host = "localhost";
        $dbname = "ltw_game_shop";
        $username = "root";
        $password = "";
        
        // Initialize database connection
        $this->db = new PDO(
            "mysql:host=$host;dbname=$dbname",
            $username,
            $password
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->gameService = new GameService($this->db);
    }

    public function runTests() {
        $this->testGetAllGames();
        $this->testGetGameById();
    }

    private function testGetAllGames() {
        echo "Testing getAllGames()...\n";
        try {
            $games = $this->gameService->getAllGames();
            if (is_array($games)) {
                echo "✓ getAllGames() test passed - Found " . count($games) . " games\n";
            } else {
                echo "✗ getAllGames() test failed - Unexpected result format\n";
            }
        } catch (Exception $e) {
            echo "✗ getAllGames() test failed: " . $e->getMessage() . "\n";
        }
    }

    private function testGetGameById() {
        echo "\nTesting getGameById()...\n";
        try {
            // Get a game ID from the database
            $stmt = $this->db->query("SELECT Gid FROM game LIMIT 1");
            $game = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($game) {
                $result = $this->gameService->getGameById($game['Gid']);
                if ($result && (isset($result['id']) || isset($result['Gid']))) {
                    $gameId = $result['id'] ?? $result['Gid'];
                    echo "✓ getGameById() test passed - Found game ID: " . $gameId . "\n";
                    echo "   - Game details: " . json_encode([
                        'name' => $result['name'] ?? 'N/A',
                        'price' => $result['price'] ?? 'N/A',
                        'category' => $result['category'] ?? 'N/A'
                    ]) . "\n";
                } else {
                    echo "✗ getGameById() test failed - Game not found or invalid format\n";
                    if ($result === null) {
                        echo "   - Method returned null\n";
                    } else {
                        echo "   - Response format: " . print_r($result, true) . "\n";
                    }
                }
            } else {
                echo "✗ getGameById() test skipped - No games in database\n";
            }
        } catch (Exception $e) {
            echo "✗ getGameById() test failed: " . $e->getMessage() . "\n";
        }
    }
}

// Run the tests
$test = new GameServiceTest();
$test->runTests();
