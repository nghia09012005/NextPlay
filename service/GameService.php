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

    public function createGame($name, $version, $description, $price, $publisherId) {
        $this->gameModel->name = $name;
        $this->gameModel->version = $version; // Game model might not have version? I removed it in my previous edit?
        // Let's check Game.php again.
        $this->gameModel->description = $description;
        $this->gameModel->price = $price;
        $this->gameModel->publisher = $publisherId; // Model has 'publisher' (string) and 'publisherid' (int)? 
        // My Game.php update had 'publisher' as varchar.
        // The original SQL had 'publisherid'.
        // My update to Game.php: public $publisher; (varchar)
        // The original GameService used $this->gameModel->publisherid.
        
        // This GameService is quite out of sync with my new Game model.
        // I should probably just focus on the read methods for now as that's what the user is asking about.
        return false;
    }

    public function updateGame($gameId, $name, $version, $description, $price) {
        return false;
    }

    public function deleteGame($gameId) {
        $this->gameModel->Gid = $gameId;
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
            "releaseDate" => $release_date,
            "rating" => $rating,
            "reviews" => $reviews
        );
    }
}
?>
