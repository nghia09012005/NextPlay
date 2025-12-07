<?php
require_once __DIR__ . '/../model/Game.php';

class GameService {
    private $gameModel;

    public function __construct($db) {
        $this->gameModel = new Game($db);
    }

    public function getAllGames() {
        $stmt = $this->gameModel->readAll();
        $games = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $this->formatGameData($row);
        }
        return $games;
    }

    public function getGameById($gameId) {
        $row = $this->gameModel->readOne($gameId);
        if ($row) {
            return $this->formatGameData($row);
        }
        return null;
    }

    public function getPublisherGames($publisherId) {
        $stmt = $this->gameModel->readByPublisher($publisherId);
        $games = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $this->formatGameData($row);
        }
        return $games;
    }

    public function getUserGames($uid) {
        $stmt = $this->gameModel->readByUser($uid);
        $games = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = $this->formatGameData($row);
        }
        return $games;
    }

    public function createGame($data) {
        $this->gameModel->name = $data['name'];
        $this->gameModel->description = $data['description'];
        $this->gameModel->price = $data['price'];
        $this->gameModel->thumbnail = $data['thumbnail'];
        $this->gameModel->category = $data['category'];
        $this->gameModel->tags = json_encode($data['tags']); // Ensure tags are JSON encoded for storage
        $this->gameModel->developer = $data['developer'];
        $this->gameModel->publisherid = $data['publisherid'];
        $this->gameModel->release_date = date('Y-m-d'); // Default to today if not provided, or handle in input
        $this->gameModel->rating = 0;
        $this->gameModel->reviews = 0;
        $this->gameModel->trailer = $data['trailer'] ?? '';
        $this->gameModel->status = $data['status'] ?? 'pending';

        return $this->gameModel->create();
    }

    public function updateGame($gameId, $data) {
        $this->gameModel->gid = $gameId;
        $this->gameModel->name = $data['name'];
        $this->gameModel->description = $data['description'];
        $this->gameModel->price = $data['price'];
        $this->gameModel->thumbnail = $data['thumbnail'];
        $this->gameModel->category = $data['category'];
        $this->gameModel->tags = json_encode($data['tags']);
        $this->gameModel->developer = $data['developer'];
        $this->gameModel->publisherid = $data['publisherid'];
        // Keep existing release date or update? Assuming update for now
        $this->gameModel->release_date = $data['release_date'] ?? date('Y-m-d'); 
        $this->gameModel->rating = $data['rating'] ?? 0;
        $this->gameModel->reviews = $data['reviews'] ?? 0;
        $this->gameModel->trailer = $data['trailer'] ?? '';
        $this->gameModel->status = $data['status'];

        return $this->gameModel->update();
    }

    public function updateGameStatus($gameId, $status) {
        // First get the game to preserve other fields
        $game = $this->gameModel->readOne($gameId);
        if (!$game) return false;

        $this->gameModel->gid = $gameId;
        $this->gameModel->name = $game['name'];
        $this->gameModel->description = $game['description'];
        $this->gameModel->price = $game['price'];
        $this->gameModel->thumbnail = $game['thumbnail'];
        $this->gameModel->category = $game['category'];
        $this->gameModel->tags = $game['tags']; // Already JSON string from DB
        $this->gameModel->developer = $game['developer'];
        $this->gameModel->publisherid = $game['publisherid'];
        $this->gameModel->release_date = $game['release_date'];
        $this->gameModel->rating = $game['rating'];
        $this->gameModel->reviews = $game['reviews'];
        $this->gameModel->trailer = $game['trailer'];
        $this->gameModel->status = $status;

        return $this->gameModel->update();
    }

    public function deleteGame($gameId) {
        $this->gameModel->gid = $gameId;
        return $this->gameModel->delete();
    }

    private function formatGameData($row) {
        extract($row);
        return array(
            "id" => $Gid,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "image" => $thumbnail,
            "category" => $category,
            "tags" => json_decode($tags),
            "developer" => $developer,
            "publisher" => $publisher,
            "publisherid" => $publisherid,
            "status" => $status ?? 'pending',
            "releaseDate" => $release_date,
            "rating" => $rating,
            "reviews" => $reviews
        );
    }
}
?>
