<?php
class News {
    private $db;
    private $table = '`news`';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllNews() {
        $query = 'SELECT n.*, u.uname as author_name, u.avatar as author_avatar 
                 FROM ' . $this->table . ' n 
                 JOIN `user` u ON n.author_id = u.uid 
                 ORDER BY n.created_at DESC';
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewsById($id) {
        $query = 'SELECT n.*, u.uname as author_name, u.avatar as author_avatar 
                 FROM ' . $this->table . ' n 
                 JOIN `user` u ON n.author_id = u.uid 
                 WHERE n.id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);

        // Increment view count
        if ($stmt->rowCount() > 0) {
            $this->incrementViews($id);
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createNews($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                 (title, content, thumbnail, author_id, category, source) 
                 VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['thumbnail'] ?? null,
            $data['author_id'],
            $data['category'] ?? null,
            $data['source'] ?? null
        ]);
    }

    public function updateNews($id, $data) {
        $query = 'UPDATE ' . $this->table . ' 
                 SET title = ?, content = ?, thumbnail = ?, category = ?, source = ? 
                 WHERE id = ? AND author_id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['thumbnail'] ?? null,
            $data['category'] ?? null,
            $data['source'] ?? null,
            $id,
            $data['author_id']
        ]);
    }

    public function updateNewsAdmin($id, $data) {
        // Admin can update any news without author check
        $query = 'UPDATE ' . $this->table . ' 
                 SET title = ?, content = ?, thumbnail = ?, category = ?, source = ? 
                 WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['thumbnail'] ?? null,
            $data['category'] ?? null,
            $data['source'] ?? null,
            $id
        ]);
    }

    public function deleteNews($id, $author_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = ? AND author_id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id, $author_id]);
    }

    public function deleteNewsAdmin($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    private function incrementViews($id) {
        $query = 'UPDATE ' . $this->table . ' SET views = views + 1 WHERE id = ?';
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
    }
}
?>
