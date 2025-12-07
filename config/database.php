<?php
if (class_exists('Database')) {
    return;
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password; 
    private $port;
    public $conn;

    public function __construct() {
        // RAILWAY
        // this->$host = "tramway.proxy.rlwy.net";
        // this->$db_name = "railway";
        // this->$username = "root";
        // this->$password = "zqRZpHByLbLhYOzKgWRfCDYhqbNYpTeB";
        // this->$port = "42537";

        // Use environment variables if available (Docker), otherwise use defaults (XAMPP)
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'ltw_game_shop';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->port = getenv('DB_PORT') ?: '3306';
    }

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
