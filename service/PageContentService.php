<?php
require_once __DIR__ . '/../model/PageContent.php';

class PageContentService {
    private $db;
    private $contentModel;

    public function __construct($db) {
        $this->db = $db;
        $this->contentModel = new PageContent($db);
    }

    public function getAllContent() {
        $stmt = $this->contentModel->getAll();
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Structure: page -> section -> key -> value
            if (!isset($content[$row['page_key']])) {
                $content[$row['page_key']] = [];
            }
            if (!isset($content[$row['page_key']][$row['section_key']])) {
                $content[$row['page_key']][$row['section_key']] = [];
            }
            $content[$row['page_key']][$row['section_key']][$row['content_key']] = $row['content_value'];
        }
        return $content;
    }

    public function updateContent($pageKey, $sectionKey, $contentKey, $value) {
        $this->contentModel->page_key = $pageKey;
        $this->contentModel->section_key = $sectionKey;
        $this->contentModel->content_key = $contentKey;
        $this->contentModel->content_value = $value;

        return $this->contentModel->update();
    }
}
?>
