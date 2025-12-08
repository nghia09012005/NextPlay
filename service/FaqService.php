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

    public function getAll() {
        $stmt = $this->faqModel->readAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $this->faqModel->topic_key = $data->topic_key;
        $this->faqModel->topic_name = $data->topic_name;
        $this->faqModel->topic_icon = $data->topic_icon;
        $this->faqModel->question = $data->question;
        $this->faqModel->answer = $data->answer;

        return $this->faqModel->create();
    }

    public function update($id, $data) {
        $this->faqModel->id = $id;
        $this->faqModel->topic_key = $data->topic_key;
        $this->faqModel->topic_name = $data->topic_name;
        $this->faqModel->topic_icon = $data->topic_icon;
        $this->faqModel->question = $data->question;
        $this->faqModel->answer = $data->answer;

        return $this->faqModel->update();
    }

    public function delete($id) {
        $this->faqModel->id = $id;
        return $this->faqModel->delete();
    }
}
?>
