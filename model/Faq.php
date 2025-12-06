<?php
class Faq {
    private $conn;
    private $table_name = "faqs";

    public $id;
    public $topic_key;
    public $topic_name;
    public $topic_icon;
    public $question;
    public $answer;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
