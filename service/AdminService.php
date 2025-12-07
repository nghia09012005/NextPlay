<?php
require_once __DIR__ . '/../model/Admin.php';
require_once __DIR__ . '/../model/User.php';

class AdminService {
    private $db;
    private $admin;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->admin = new Admin($db);
        $this->user = new User($db);
    }

    /**
     * Get all admins with user details
     */
    public function getAll() {
        $stmt = $this->admin->readAll();
        $admins = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            unset($row['password']); // Remove sensitive data
            $admins[] = $row;
        }
        return $admins;
    }

    /**
     * Get one admin by uid
     */
    public function getOne($uid) {
        $admin = $this->admin->readOne($uid);
        if ($admin) {
            unset($admin['password']);
        }
        return $admin;
    }

    /**
     * Check if a user is an admin
     */
    public function isAdmin($uid) {
        $admin = $this->admin->readOne($uid);
        return $admin !== false && $admin !== null;
    }

    /**
     * Create a new admin from existing user
     */
    public function promoteToAdmin($uid) {
        // Check if user exists
        $user = $this->user->readOne($uid);
        if (!$user) {
            throw new Exception('User not found', 404);
        }

        // Check if already admin
        if ($this->isAdmin($uid)) {
            throw new Exception('User is already an admin', 400);
        }

        // Create admin record
        $this->admin->uid = $uid;
        $this->admin->startdate = date('Y-m-d H:i:s');
        
        if ($this->admin->create()) {
            return $this->getOne($uid);
        }
        
        throw new Exception('Failed to promote user to admin', 500);
    }

    /**
     * Create a new admin user (register + promote)
     */
    public function createAdmin($data) {
        // Validate required fields
        $required = ['uname', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field", 400);
            }
        }

        // Check if username or email exists
        $this->user->uname = $data['uname'];
        $this->user->email = $data['email'];
        $existing = $this->user->checkExistingUser();
        
        if ($existing) {
            if ($existing['uname']) {
                throw new Exception('Username already exists', 400);
            }
            if ($existing['email']) {
                throw new Exception('Email already exists', 400);
            }
        }

        // Create user first
        $this->user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->user->DOB = $data['DOB'] ?? '1990-01-01';
        $this->user->lname = $data['lname'] ?? '';
        $this->user->fname = $data['fname'] ?? '';
        $this->user->avatar = $data['avatar'] ?? null;

        if (!$this->user->create()) {
            throw new Exception('Failed to create user', 500);
        }

        // Get the new user ID
        $uid = $this->db->lastInsertId();

        // Promote to admin
        return $this->promoteToAdmin($uid);
    }

    /**
     * Remove admin privileges (demote to regular user)
     */
    public function demoteAdmin($uid) {
        if (!$this->isAdmin($uid)) {
            throw new Exception('User is not an admin', 400);
        }

        if ($this->admin->delete($uid)) {
            return true;
        }
        
        throw new Exception('Failed to demote admin', 500);
    }

    /**
     * Delete admin and optionally the user account
     */
    public function deleteAdmin($uid, $deleteUser = false) {
        if (!$this->isAdmin($uid)) {
            throw new Exception('Admin not found', 404);
        }

        // Remove admin privileges first
        $this->admin->delete($uid);

        // Optionally delete the user account too
        if ($deleteUser) {
            $this->user->delete($uid);
        }

        return true;
    }

    /**
     * Get admin statistics for dashboard
     */
    public function getStats() {
        $stats = [];

        // Total admins
        $query = "SELECT COUNT(*) as total FROM `admin`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalAdmins'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total users
        $query = "SELECT COUNT(*) as total FROM `user`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalUsers'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total games
        $query = "SELECT COUNT(*) as total FROM `game`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalGames'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total categories
        $query = "SELECT COUNT(*) as total FROM `category`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalCategories'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total publishers
        $query = "SELECT COUNT(*) as total FROM `publisher`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalPublishers'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total reviews
        $query = "SELECT COUNT(*) as total FROM `review`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalReviews'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total cart items (orders)
        $query = "SELECT COUNT(*) as total FROM `cart`";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['totalOrders'] = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Recent admins (last 5)
        $query = "SELECT A.*, U.uname, U.email 
                  FROM `admin` A 
                  JOIN `user` U ON U.uid = A.uid 
                  ORDER BY A.startdate DESC 
                  LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['recentAdmins'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    }

    /**
     * Get all users who are not admins (for promoting)
     */
    public function getNonAdminUsers() {
        $query = "SELECT U.uid, U.uname, U.email, U.fname, U.lname 
                  FROM `user` U 
                  LEFT JOIN `admin` A ON U.uid = A.uid 
                  WHERE A.uid IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
