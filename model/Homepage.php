<?php
class Homepage {
    private $conn;
    private $table_name = "homepage";

    // Object properties
    public $id;
    public $title;
    public $description;
    public $variety;
    public $activeplayer;
    public $supporttime;
    public $free;

    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all records
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single record
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->variety = $row['variety'];
            $this->activeplayer = $row['activeplayer'];
            $this->supporttime = $row['supporttime'];
            $this->free = $row['free'];
            return true;
        }
        return false;
    }

    // Create new record
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET 
                    title = :title, 
                    description = :description, 
                    variety = :variety,
                    activeplayer = :activeplayer,
                    supporttime = :supporttime,
                    free = :free";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->variety = htmlspecialchars(strip_tags($this->variety));
        $this->activeplayer = htmlspecialchars(strip_tags($this->activeplayer));
        $this->supporttime = htmlspecialchars(strip_tags($this->supporttime));
        $this->free = htmlspecialchars(strip_tags($this->free));

        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':variety', $this->variety);
        $stmt->bindParam(':activeplayer', $this->activeplayer);
        $stmt->bindParam(':supporttime', $this->supporttime);
        $stmt->bindParam(':free', $this->free);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update record
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET 
                    title = :title, 
                    description = :description, 
                    variety = :variety,
                    activeplayer = :activeplayer,
                    supporttime = :supporttime,
                    free = :free
                WHERE 
                    id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->variety = htmlspecialchars(strip_tags($this->variety));
        $this->activeplayer = htmlspecialchars(strip_tags($this->activeplayer));
        $this->supporttime = htmlspecialchars(strip_tags($this->supporttime));
        $this->free = htmlspecialchars(strip_tags($this->free));

        // Bind parameters
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':variety', $this->variety);
        $stmt->bindParam(':activeplayer', $this->activeplayer);
        $stmt->bindParam(':supporttime', $this->supporttime);
        $stmt->bindParam(':free', $this->free);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete record
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
