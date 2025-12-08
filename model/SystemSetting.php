<?php
class SystemSetting {
    private $conn;
    private $table_name = "system_settings";

    public $setting_key;
    public $setting_value;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all settings as an associative array
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Decode JSON value
            $settings[$row['setting_key']] = json_decode($row['setting_value'], true);
        }
        return $settings;
    }

    // Get a specific setting group
    public function getByKey($key) {
        $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = :key LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return json_decode($row['setting_value'], true);
        }
        return null;
    }

    // Update or Insert a setting group
    public function save($key, $data) {
        $query = "INSERT INTO " . $this->table_name . " (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value";

        $stmt = $this->conn->prepare($query);

        // Sanitize key
        $this->setting_key = htmlspecialchars(strip_tags($key));
        
        // Data is passed as array, encode to JSON
        $jsonValue = json_encode($data, JSON_UNESCAPED_UNICODE);

        $stmt->bindParam(':key', $this->setting_key);
        $stmt->bindParam(':value', $jsonValue);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
