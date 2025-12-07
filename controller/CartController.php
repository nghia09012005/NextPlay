<?php
require_once __DIR__ . '/../service/CartService.php';

class CartController {
    private $cartService;

    public function __construct($db) {
        $this->cartService = new CartService($db);
    }

    public function getAllCarts() {
        $result = $this->cartService->getAllCarts();
        
        if ($result['status'] === 'success') {
            echo json_encode($result);
        } else {
            http_response_code(500);
            echo json_encode($result);
        }
    }

    public function getCartDetails($uid) {
        $result = $this->cartService->getCartDetails($uid);
        
        if ($result['status'] === 'success') {
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode($result);
        }
    }
    
    // Stats endpoint
    public function getStats() {
       $result = $this->cartService->getAllCarts();
       if ($result['status'] === 'success') {
           $carts = $result['data'];
           $activeCarts = count($carts);
           $pendingValue = array_reduce($carts, function($carry, $item) {
               return $carry + $item['total_amount'];
           }, 0);
           
           echo json_encode([
               'status' => 'success',
               'data' => [
                   'stats' => [
                       'active_carts' => $activeCarts,
                       'pending_value' => $pendingValue,
                       'completed_orders' => 0, // Not implemented
                       'total_revenue' => 0 // Not implemented
                   ]
               ]
           ]);
       } else {
           http_response_code(500);
           echo json_encode(['status' => 'error', 'message' => 'Failed to fetch stats']);
       }
    }
}
?>
