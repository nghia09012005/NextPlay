<?php
class Admin {
    private $conn;
    private $table_name = "`Admin`";

    public $uid;
    public $startdate;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET all admins
    public function readAll() {
        $query = "SELECT A.*, U.uname, U.email 
                 FROM {$this->table_name} A
                 JOIN `User` U ON U.uid = A.uid";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // GET one admin
    public function readOne($uid) {
        $query = "SELECT A.*, U.uname, U.email 
                 FROM {$this->table_name} A
                 JOIN `User` U ON U.uid = A.uid
                 WHERE A.`uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE admin
    public function create() {
        $query = "INSERT INTO {$this->table_name} (`uid`, `startdate`) 
                 VALUES (:uid, :startdate)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $this->uid);
        $stmt->bindParam(":startdate", $this->startdate);
        return $stmt->execute();
    }

    // DELETE admin
    public function delete($uid) {
        $query = "DELETE FROM {$this->table_name} WHERE `uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }
}
?>
