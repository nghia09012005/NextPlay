<?php
require_once __DIR__ . '/../model/Faq.php';

class FaqService {
    private $faqModel;

    public function __construct($db) {
        $this->faqModel = new Faq($db);
    }

    public function getAllFaqsGrouped() {
        $stmt = $this->faqModel->readAll();
        $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($faqs as $faq) {
            $key = $faq['topic_key'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => $key,
                    'name' => $faq['topic_name'],
                    'icon' => $faq['topic_icon'],
                    'questions' => []
                ];
            }
            $grouped[$key]['questions'][] = [
                'id' => $faq['id'],
                'title' => $faq['question'],
                'answer' => $faq['answer']
            ];
        }

        return array_values($grouped);
    }
}
?>
