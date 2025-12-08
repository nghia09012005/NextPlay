<?php
require_once __DIR__ . '/../model/News.php';

class NewsService {
    private $db;
    private $news;

    public function __construct($db) {
        $this->db = $db;
        $this->news = new News($db);
    }

    public function getAll() {
        $stmt = $this->news->readAll();
        $num = $stmt->rowCount();
        $news_arr = array();
        
        if($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $news_item = array(
                    "id" => $id,
                    "title" => $title,
                    "content" => html_entity_decode($content),
                    "thumbnail" => $thumbnail,
                    "author_id" => $author_id,
                    "created_at" => $created_at,
                    "views" => $views,
                    "category" => $category,
                    "source" => $source
                );
                array_push($news_arr, $news_item);
            }
        }
        return $news_arr;
    }

    public function getOne($id) {
        $this->news->id = $id;
        $stmt = $this->news->readOne();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            return $row;
        }
        return null;
    }

    public function create($data) {
        $this->news->title = $data->title;
        $this->news->content = $data->content;
        $this->news->thumbnail = $data->thumbnail;
        $this->news->author_id = $data->author_id ?? 1; // Default author
        $this->news->category = $data->category;
        $this->news->source = $data->source;

        if($this->news->create()) {
            return true;
        }
        return false;
    }

    public function update($id, $data) {
        $this->news->id = $id;
        $this->news->title = $data->title;
        $this->news->content = $data->content;
        $this->news->thumbnail = $data->thumbnail;
        $this->news->category = $data->category;
        $this->news->source = $data->source;

        if($this->news->update()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->news->id = $id;
        if($this->news->delete()) {
            return true;
        }
        return false;
    }
}
