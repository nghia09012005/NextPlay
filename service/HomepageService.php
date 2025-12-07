<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Homepage.php';

class HomepageService {
    private $homepage;

    public function __construct($db) {
        $this->homepage = new Homepage($db);
    }

    // Lấy tất cả các bản ghi homepage
    public function getAllHomepages() {
        $stmt = $this->homepage->read();
        $homepages_arr = array();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $homepage_item = array(
                "id" => $id,
                "title" => $title,
                "description" => $description,
                "variety" => $variety,
                "activeplayer" => $activeplayer,
                "supporttime" => $supporttime,
                "free" => $free
            );
            array_push($homepages_arr, $homepage_item);
        }
        
        return $homepages_arr;
    }

    // Lấy một bản ghi homepage theo ID
    public function getHomepageById($id) {
        $this->homepage->id = $id;
        
        if ($this->homepage->readOne()) {
            $homepage_item = array(
                "id" => $this->homepage->id,
                "title" => $this->homepage->title,
                "description" => $this->homepage->description,
                "variety" => $this->homepage->variety,
                "activeplayer" => $this->homepage->activeplayer,
                "supporttime" => $this->homepage->supporttime,
                "free" => $this->homepage->free
            );
            return $homepage_item;
        }
        return null;
    }

    // Tạo mới một bản ghi homepage
    public function createHomepage($data) {
        $this->homepage->title = $data['title'];
        $this->homepage->description = $data['description'];
        $this->homepage->variety = $data['variety'];
        $this->homepage->activeplayer = $data['activeplayer'];
        $this->homepage->supporttime = $data['supporttime'];
        $this->homepage->free = $data['free'];

        if ($this->homepage->create()) {
            return array("message" => "Homepage created successfully.");
        } else {
            return array("message" => "Unable to create homepage.");
        }
    }

    // Cập nhật một bản ghi homepage
    public function updateHomepage($id, $data) {
        $this->homepage->id = $id;
        
        // Kiểm tra xem bản ghi có tồn tại không
        if (!$this->homepage->readOne()) {
            return array("message" => "Homepage not found.");
        }

        // Cập nhật các trường
        $this->homepage->title = isset($data['title']) ? $data['title'] : $this->homepage->title;
        $this->homepage->description = isset($data['description']) ? $data['description'] : $this->homepage->description;
        $this->homepage->variety = isset($data['variety']) ? $data['variety'] : $this->homepage->variety;
        $this->homepage->activeplayer = isset($data['activeplayer']) ? $data['activeplayer'] : $this->homepage->activeplayer;
        $this->homepage->supporttime = isset($data['supporttime']) ? $data['supporttime'] : $this->homepage->supporttime;
        $this->homepage->free = isset($data['free']) ? $data['free'] : $this->homepage->free;

        if ($this->homepage->update()) {
            return array("message" => "Homepage updated successfully.");
        } else {
            return array("message" => "Unable to update homepage.");
        }
    }

    // Xóa một bản ghi homepage
    public function deleteHomepage($id) {
        $this->homepage->id = $id;
        
        // Kiểm tra xem bản ghi có tồn tại không
        if (!$this->homepage->readOne()) {
            return array("message" => "Homepage not found.");
        }

        if ($this->homepage->delete()) {
            return array("message" => "Homepage deleted successfully.");
        } else {
            return array("message" => "Unable to delete homepage.");
        }
    }
}
?>
