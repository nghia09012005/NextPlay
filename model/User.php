<?php
class User {
    private $conn;
    private $table_name = "`User`";

    public $uid;
    public $uname;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all users
    public function readAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one user
    public function readOne($uid) {
        $query = "SELECT * FROM {$this->table_name} WHERE `uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE user
    public function create() {
        $query = "INSERT INTO {$this->table_name} (`uname`, `email`, `password`) VALUES (:uname, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uname", $this->uname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        return $stmt->execute();
    }

    // UPDATE user
    public function update() {
        $query = "UPDATE {$this->table_name} SET `uname`=:uname, `email`=:email, `password`=:password WHERE `uid`=:uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uname", $this->uname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":uid", $this->uid);
        return $stmt->execute();
    }

    // DELETE user
    public function delete($uid) {
        $query = "DELETE FROM {$this->table_name} WHERE `uid`=:uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }
}
?>
