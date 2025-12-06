<?php
class Customer {
    private $conn;
    private $table_name = "`customer`";

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
                  JOIN `user` U ON U.uid = C.uid
                  WHERE C.`uid` = :uid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);

        print("lỗi đây");

        print($stmt->execute());

        print("excute xong");

       

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE customer (called when creating user)
    public function create($uid = null, $balance = 100.00) {
        try {
            if ($uid !== null) {
                $this->uid = $uid;
            }
            if ($balance !== null) {
                $this->balance = $balance;
            }
            
            error_log("Creating customer with uid: " . $this->uid . ", balance: " . $this->balance);
            
            $query = "INSERT INTO {$this->table_name} (`uid`, `balance`) 
                      VALUES (:uid, :balance)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":uid", $this->uid, PDO::PARAM_INT);
            $stmt->bindParam(":balance", $this->balance);

            $result = $stmt->execute();
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Customer creation failed. Error: " . print_r($errorInfo, true));
            } else {
                error_log("Customer created successfully for user ID: " . $this->uid);
            }
            
            return $result;
            
        } catch (PDOException $e) {
            error_log('PDO Exception in Customer::create(): ' . $e->getMessage());
            error_log('SQL Query: ' . ($query ?? 'Not defined'));
            return false;
        } catch (Exception $e) {
            error_log('General Exception in Customer::create(): ' . $e->getMessage());
            return false;
        }
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
