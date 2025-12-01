<?php
require_once __DIR__ . '/../model/Game.php';

class GameService {
    private $gameModel;

    public function __construct($db) {
        $this->gameModel = new Game($db);
    }

    public function getAllGames() {
        $stmt = $this->gameModel->readAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameById($gameId) {
        return $this->gameModel->readOne($gameId);
    }

    public function getPublisherGames($publisherId) {
        $stmt = $this->gameModel->readByPublisher($publisherId);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createGame($name, $version, $description, $cost, $publisherId) {
        $this->gameModel->name = $name;
        $this->gameModel->version = $version;
        $this->gameModel->description = $description;
        $this->gameModel->cost = $cost;
        $this->gameModel->publisherid = $publisherId;
        $this->gameModel->adminid = 3; // Default admin ID

        return $this->gameModel->create();
    }

    public function updateGame($gameId, $name, $version, $description, $cost) {
        $game = $this->getGameById($gameId);
        if (!$game) {
            throw new Exception("Game not found");
        }

        $this->gameModel->Gid = $gameId;
        $this->gameModel->name = $name ?? $game['name'];
        $this->gameModel->version = $version ?? $game['version'];
        $this->gameModel->description = $description ?? $game['description'];
        $this->gameModel->cost = $cost ?? $game['cost'];
        $this->gameModel->adminid = 3; // Default admin ID
        $this->gameModel->publisherid = $game['publisherid'];

        return $this->gameModel->update();
    }

    public function deleteGame($gameId) {
        $game = $this->getGameById($gameId);
        if (!$game) {
            throw new Exception("Game not found");
        }

        $this->gameModel->Gid = $gameId;
        return $this->gameModel->delete();
    }
}
?>
