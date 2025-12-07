<?php
require_once __DIR__ . '/../service/AdminService.php';

class AdminController {
    private $service;

    public function __construct($db) {
        $this->service = new AdminService($db);
    }

    /**
     * GET /admins - Get all admins
     */
    public function getAll() {
        header('Content-Type: application/json');
        try {
            $admins = $this->service->getAll();
            echo json_encode([
                'status' => 'success',
                'data' => $admins
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET /admins/{uid} - Get one admin
     */
    public function getOne($uid) {
        header('Content-Type: application/json');
        try {
            $admin = $this->service->getOne($uid);
            if ($admin) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $admin
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Admin not found'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET /admins/stats - Get dashboard statistics
     */
    public function getStats() {
        header('Content-Type: application/json');
        try {
            $stats = $this->service->getStats();
            echo json_encode([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /admins - Create new admin (register new user as admin)
     */
    public function create() {
        header('Content-Type: application/json');
        try {
            $json = file_get_contents("php://input");
            if (empty($json)) {
                throw new Exception('No input data received', 400);
            }

            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format', 400);
            }

            $admin = $this->service->createAdmin($data);
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Admin created successfully',
                'data' => $admin
            ]);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            http_response_code($code);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /admins/promote/{uid} - Promote existing user to admin
     */
    public function promote($uid) {
        header('Content-Type: application/json');
        try {
            $admin = $this->service->promoteToAdmin($uid);
            echo json_encode([
                'status' => 'success',
                'message' => 'User promoted to admin successfully',
                'data' => $admin
            ]);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            http_response_code($code);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /admins/demote/{uid} - Demote admin to regular user
     */
    public function demote($uid) {
        header('Content-Type: application/json');
        try {
            $this->service->demoteAdmin($uid);
            echo json_encode([
                'status' => 'success',
                'message' => 'Admin demoted to regular user successfully'
            ]);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            http_response_code($code);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * DELETE /admins/{uid} - Delete admin (remove privileges only)
     */
    public function delete($uid) {
        header('Content-Type: application/json');
        try {
            // Check if should also delete user
            $json = file_get_contents("php://input");
            $data = $json ? json_decode($json, true) : [];
            $deleteUser = isset($data['deleteUser']) && $data['deleteUser'] === true;

            $this->service->deleteAdmin($uid, $deleteUser);
            echo json_encode([
                'status' => 'success',
                'message' => $deleteUser 
                    ? 'Admin and user account deleted successfully' 
                    : 'Admin privileges removed successfully'
            ]);
        } catch (Exception $e) {
            $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 404;
            http_response_code($code);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET /admins/check/{uid} - Check if user is admin
     */
    public function checkAdmin($uid) {
        header('Content-Type: application/json');
        try {
            $isAdmin = $this->service->isAdmin($uid);
            echo json_encode([
                'status' => 'success',
                'isAdmin' => $isAdmin
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * GET /admins/non-admin-users - Get users who are not admins
     */
    public function getNonAdminUsers() {
        header('Content-Type: application/json');
        try {
            $users = $this->service->getNonAdminUsers();
            echo json_encode([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
