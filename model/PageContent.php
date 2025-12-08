<?php
class PageContent {
    private $conn;
    private $table_name = "PageContent";

    public $id;
    public $page_key;
    public $section_key;
    public $content_key;
    public $content_value;
    public $content_type;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        // Update based on (page, section, key) combo
        $query = "UPDATE " . $this->table_name . "
                SET content_value = :content_value
                WHERE page_key = :page_key 
                  AND section_key = :section_key 
                  AND content_key = :content_key";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        // $this->content_value = htmlspecialchars(strip_tags($this->content_value)); // Allow HTML? Yes, for some fields.
        $this->page_key = htmlspecialchars(strip_tags($this->page_key));
        $this->section_key = htmlspecialchars(strip_tags($this->section_key));
        $this->content_key = htmlspecialchars(strip_tags($this->content_key));

        $stmt->bindParam(':content_value', $this->content_value);
        $stmt->bindParam(':page_key', $this->page_key);
        $stmt->bindParam(':section_key', $this->section_key);
        $stmt->bindParam(':content_key', $this->content_key);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
