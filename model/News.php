<?php
class News {
    private $conn;
    private $table_name = "news";

    public $id;
    public $title;
    public $content;
    public $thumbnail;
    public $author_id;
    public $created_at;
    public $views;
    public $category;
    public $source;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    title = :title,
                    content = :content,
                    thumbnail = :thumbnail,
                    author_id = :author_id,
                    category = :category,
                    source = :source";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->thumbnail = htmlspecialchars(strip_tags($this->thumbnail));
        $this->author_id = htmlspecialchars(strip_tags($this->author_id));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->source = htmlspecialchars(strip_tags($this->source));

        // Bind
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":author_id", $this->author_id);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":source", $this->source);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    title = :title,
                    content = :content,
                    thumbnail = :thumbnail,
                    category = :category,
                    source = :source
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = htmlspecialchars(strip_tags($this->content));
        $this->thumbnail = htmlspecialchars(strip_tags($this->thumbnail));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->source = htmlspecialchars(strip_tags($this->source));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":thumbnail", $this->thumbnail);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":source", $this->source);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
