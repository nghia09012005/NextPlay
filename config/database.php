<?php
class Database {
    // XAMPP LOCAL
    private $host = "localhost";
    private $db_name = "ltw_game_shop";
    private $username = "root";
    private $password = ""; 
    private $port = "3306"; // Default MySQL port
    public $conn;


    // RAILWAY
    // private $host = "tramway.proxy.rlwy.net";
    // private $db_name = "railway";
    // private $username = "root";
    // private $password = "zqRZpHByLbLhYOzKgWRfCDYhqbNYpTeB";
    // private $port = "42537";
    //public $conn;

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

        print("ko lỗi here");
        return $this->conn;
    }
}
?>
