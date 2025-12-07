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
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY topic_key ASC, id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (topic_key, topic_name, topic_icon, question, answer) 
                  VALUES (:topic_key, :topic_name, :topic_icon, :question, :answer)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':topic_key', $data['topic_key']);
        $stmt->bindParam(':topic_name', $data['topic_name']);
        $stmt->bindParam(':topic_icon', $data['topic_icon']);
        $stmt->bindParam(':question', $data['question']);
        $stmt->bindParam(':answer', $data['answer']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET topic_key = :topic_key, topic_name = :topic_name, topic_icon = :topic_icon, 
                      question = :question, answer = :answer 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':topic_key', $data['topic_key']);
        $stmt->bindParam(':topic_name', $data['topic_name']);
        $stmt->bindParam(':topic_icon', $data['topic_icon']);
        $stmt->bindParam(':question', $data['question']);
        $stmt->bindParam(':answer', $data['answer']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getTopics() {
        $query = "SELECT DISTINCT topic_key, topic_name, topic_icon FROM " . $this->table_name . " ORDER BY topic_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
