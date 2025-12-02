<?php
class NewsComment {
    private $db;
    private $table = 'NewsComments';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCommentsByNewsId($news_id) {
        $query = 'SELECT c.*, u.uname, u.avatar 
                 FROM ' . $this->table . ' c 
                 JOIN User u ON c.user_id = u.uid 
                 WHERE c.news_id = ? 
                 ORDER BY c.created_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute([$news_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createComment($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                 (news_id, user_id, content) 
                 VALUES (?, ?, ?)';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['news_id'],
            $data['user_id'],
            $data['content']
        ]);
    }

    public function deleteComment($id, $user_id) {
        $query = 'DELETE FROM ' . $this->table . ' 
                 WHERE id = ? AND user_id = ?';
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id, $user_id]);
    }
}
?>
