<?php
class Page {
    private $conn;
    private $table_name = "pages";

    public $id;
    public $slug;
    public $title;
    public $content;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getBySlug($slug) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE slug = :slug LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->slug = $row['slug'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET title = :title, content = :content
                  WHERE slug = :slug";

        $stmt = $this->conn->prepare($query);

        // Sanitize (content is JSON, so we don't strip tags from it generally, but be careful)
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->slug = htmlspecialchars(strip_tags($this->slug));
        
        // Bind values
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':slug', $this->slug);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
