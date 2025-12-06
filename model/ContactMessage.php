<?php
class ContactMessage {
    private $conn;
    private $table_name = "`contactmessages`";

    public $id;
    public $name;
    public $email;
    public $subject;
    public $message;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    email = :email,
                    subject = :subject,
                    message = :message,
                    status = 'new'";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->subject = htmlspecialchars(strip_tags($this->subject));
        $this->message = htmlspecialchars(strip_tags($this->message));

        // Bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":subject", $this->subject);
        $stmt->bindParam(":message", $this->message);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
