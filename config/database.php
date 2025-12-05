<?php
class Database {
    private $host = "localhost";
    private $db_name = "ltw_game_shop";
    private $username = "root";
    private $password = ""; 
    private $port = "3307"; // Default MySQL port
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Connection error: " . $exception->getMessage()]);
            exit();
        }
        return $this->conn;
    }
}
?>
