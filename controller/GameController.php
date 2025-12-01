<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/Game.php';

class GameController {
    private $db;
    private $game;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->game = new Game($this->db);
    }

    public function getAll() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->game->readAll();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $games_arr = array();
                $games_arr["status"] = "success";
                $games_arr["data"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $game_item = array(
                        "id" => $Gid, // DB column is Gid (case sensitive in extract?) usually it's case insensitive but let's be safe
                        "name" => $name,
                        "description" => $description,
                        "price" => $price,
                        "image" => $thumbnail,
                        "category" => $category,
                        "tags" => json_decode($tags),
                        "developer" => $developer,
                        "publisher" => $publisher,
                        "releaseDate" => $release_date,
                        "rating" => $rating,
                        "reviews" => $reviews
                    );
                    array_push($games_arr["data"], $game_item);
                }
                http_response_code(200);
                echo json_encode($games_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("status" => "error", "message" => "No games found."));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("status" => "error", "message" => $e->getMessage()));
        }
    }

    public function getOne($id) {
        header('Content-Type: application/json');
        try {
            $row = $this->game->readOne($id);

            if ($row) {
                extract($row);
                $game_item = array(
                    "id" => $Gid,
                    "name" => $name,
                    "description" => $description,
                    "price" => $price,
                    "image" => $thumbnail,
                    "category" => $category,
                    "tags" => json_decode($tags),
                    "developer" => $developer,
                    "publisher" => $publisher,
                    "releaseDate" => $release_date,
                    "rating" => $rating,
                    "reviews" => $reviews
                );
                
                $result = array(
                    "status" => "success",
                    "data" => $game_item
                );
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(array("status" => "error", "message" => "Game not found."));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("status" => "error", "message" => $e->getMessage()));
        }
    }
}
?>
