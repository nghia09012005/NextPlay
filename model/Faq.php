<?php
class Faq {
    private $conn;
    private $table_name = "`faqs`";

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

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    topic_key = :topic_key,
                    topic_name = :topic_name,
                    topic_icon = :topic_icon,
                    question = :question,
                    answer = :answer";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->topic_key = htmlspecialchars(strip_tags($this->topic_key));
        $this->topic_name = htmlspecialchars(strip_tags($this->topic_name));
        $this->topic_icon = htmlspecialchars(strip_tags($this->topic_icon));
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->answer = htmlspecialchars(strip_tags($this->answer));

        // Bind
        $stmt->bindParam(":topic_key", $this->topic_key);
        $stmt->bindParam(":topic_name", $this->topic_name);
        $stmt->bindParam(":topic_icon", $this->topic_icon);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":answer", $this->answer);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    topic_key = :topic_key,
                    topic_name = :topic_name,
                    topic_icon = :topic_icon,
                    question = :question,
                    answer = :answer
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->topic_key = htmlspecialchars(strip_tags($this->topic_key));
        $this->topic_name = htmlspecialchars(strip_tags($this->topic_name));
        $this->topic_icon = htmlspecialchars(strip_tags($this->topic_icon));
        $this->question = htmlspecialchars(strip_tags($this->question));
        $this->answer = htmlspecialchars(strip_tags($this->answer));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind
        $stmt->bindParam(":topic_key", $this->topic_key);
        $stmt->bindParam(":topic_name", $this->topic_name);
        $stmt->bindParam(":topic_icon", $this->topic_icon);
        $stmt->bindParam(":question", $this->question);
        $stmt->bindParam(":answer", $this->answer);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
