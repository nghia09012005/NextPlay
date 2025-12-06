<?php
require_once __DIR__ . '/../model/Category.php';

class CategoryService {
    private $categoryModel;

    public function __construct($db) {
        $this->categoryModel = new Category($db);
    }

    public function getAllCategories() {
        $stmt = $this->categoryModel->readAll();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategory($catId) {
        return $this->categoryModel->readOne($catId);
    }

    public function createCategory($name, $description) {
        $this->categoryModel->name = $name;
        $this->categoryModel->description = $description;
        return $this->categoryModel->create();
    }

    public function updateCategory($catId, $name, $description) {
        $this->categoryModel->catId = $catId;
        $this->categoryModel->name = $name;
        $this->categoryModel->description = $description;
        return $this->categoryModel->update();
    }

    public function deleteCategory($catId) {
        $this->categoryModel->catId = $catId;
        return $this->categoryModel->delete($catId);
    }
}
?>
