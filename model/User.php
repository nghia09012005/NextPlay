<?php
class User {
    private $conn;
    private $table_name = "`User`";

    public $uid;
    public $uname;
    public $email;
    public $password;
    public $DOB;
    public $lname;
    public $fname;

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

    /**
     * Get user by username
     * @param string $uname Username to search for
     * @return array|false User data if found, false otherwise
     */
    public function getByUsername($uname) {
        $query = "SELECT * FROM {$this->table_name} WHERE `uname` = :uname LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uname", $uname);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // CREATE user
    public function create() {
        try {
            $query = "INSERT INTO {$this->table_name} (`uname`, `email`, `password`, `DOB`, `lname`, `fname`, `avatar`) 
                     VALUES (:uname, :email, :password, :DOB, :lname, :fname, :avatar)";
            
            $stmt = $this->conn->prepare($query);
            
            // Debug: Log the values being inserted
            error_log("Creating user with data: " . print_r([
                'uname' => $this->uname,
                'email' => $this->email,
                'DOB' => $this->DOB,
                'lname' => $this->lname,
                'fname' => $this->fname
            ], true));
            
            $stmt->bindParam(":uname", $this->uname);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":DOB", $this->DOB);
            $stmt->bindParam(":lname", $this->lname);
            $stmt->bindParam(":fname", $this->fname);
            $stmt->bindValue(":avatar", $this->avatar ?? null, PDO::PARAM_STR | PDO::PARAM_NULL);
            
            $result = $stmt->execute();
            
            if ($result) {
                $lastId = $this->conn->lastInsertId();
                error_log("User created successfully with ID: " . $lastId);
                return $lastId;
            }
            
            $errorInfo = $stmt->errorInfo();
            error_log("User creation failed. Error: " . print_r($errorInfo, true));
            return false;
            
        } catch (PDOException $e) {
            error_log("PDO Exception in User::create(): " . $e->getMessage());
            error_log("SQL Query: " . $query);
            return false;
        } catch (Exception $e) {
            error_log("General Exception in User::create(): " . $e->getMessage());
            return false;
        }
    }

    // UPDATE user
    public function update() {
        $query = "UPDATE {$this->table_name} SET 
            `uname` = :uname,
            `email` = :email,
            `DOB` = :DOB,
            `lname` = :lname,
            `fname` = :fname,
            `avatar` = :avatar
            WHERE `uid` = :uid";

        $stmt = $this->conn->prepare($query);
        
        // Sanitize and bind
        $this->uname = htmlspecialchars(strip_tags($this->uname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->DOB = htmlspecialchars(strip_tags($this->DOB));
        $this->lname = htmlspecialchars(strip_tags($this->lname));
        $this->fname = htmlspecialchars(strip_tags($this->fname));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        $this->avatar = $this->avatar ? htmlspecialchars(strip_tags($this->avatar)) : null;
        
        // Bind values
        $stmt->bindParam(":uname", $this->uname);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":DOB", $this->DOB);
        $stmt->bindParam(":lname", $this->lname);
        $stmt->bindParam(":fname", $this->fname);
        $stmt->bindParam(":avatar", $this->avatar, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindParam(":uid", $this->uid);

        // Execute query
        return $stmt->execute();
    }

    // DELETE user
    public function delete($uid) {
        $query = "DELETE FROM {$this->table_name} WHERE `uid`=:uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }

    /**
     * Update user password
     * @param int $uid User ID
     * @param string $hashedPassword Hashed password
     * @return bool True on success, false on failure
     */
    public function updatePassword($uid, $hashedPassword) {
        $query = "UPDATE {$this->table_name} SET `password` = :password WHERE `uid` = :uid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":uid", $uid);
        return $stmt->execute();
    }
}
?>
