<?php
class Customer {
    private $conn;
    private $table_name = "`Customer`";

    public $uid;
    public $balance;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all customers
    public function readAll() {
        $query = "SELECT C.*, U.uname, U.email 
                  FROM {$this->table_name} C
                  JOIN `User` U ON U.uid = C.uid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one customer
    public function readOne($uid) {
        $query = "SELECT C.*, U.uname, U.email 
                  FROM {$this->table_name} C
                  JOIN `User` U ON U.uid = C.uid
                  WHERE C.`uid` = :uid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE customer (called when creating user)
    public function create() {
        $query = "INSERT INTO {$this->table_name} (`uid`, `balance`) 
                  VALUES (:uid, :balance)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":balance", $this->balance);

        return $stmt->execute();
    }

    // UPDATE customer balance
    public function update() {
        $query = "UPDATE {$this->table_name} 
                  SET `balance` = :balance
                  WHERE `uid` = :uid";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":balance", $this->balance);
        $stmt->bindParam(":uid", $this->uid);

        return $stmt->execute();
    }

    // DELETE customer (usually when deleting user)
    public function delete($uid) {
        $query = "DELETE FROM {$this->table_name} WHERE `uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }
}
?>
