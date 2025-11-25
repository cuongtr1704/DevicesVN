<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class CartController extends Controller {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        
        // Clean up old guest carts periodically (10% chance on each cart access)
        // This prevents the need for a cron job while not slowing down every request
        if (rand(1, 100) <= 10) {
            $this->cartModel->cleanupOldGuestCarts(30); // Remove carts older than 30 days
        }
    }
    
    /**
     * Get cart session ID
     */
    private function getCartSessionId() {
        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = session_id() ?: uniqid('cart_', true);
        }
        return $_SESSION['cart_session_id'];
    }
    
    /**
     * Display cart page
     */
    public function index() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $this->getCartSessionId();
        
        $cartItems = $this->cartModel->getUserCart($userId, $sessionId);
        $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
        
        $this->view('cart/index', [
            'title' => 'Shopping Cart',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'breadcrumbs' => [
                ['label' => 'Shopping Cart', 'url' => '']
            ]
        ]);
    }
    
    /**
     * Add item to cart (AJAX)
     */
    public function add() {
        // Clean any output before JSON
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$productId || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
            exit;
        }
        
        // Check if product exists and has stock
        $product = $this->productModel->getById($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        if ($product['stock_quantity'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            exit;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $this->getCartSessionId();
        
        if ($this->cartModel->add($userId, $sessionId, $productId, $quantity)) {
            $count = $this->cartModel->getCount($userId, $sessionId);
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product']);
        }
        exit;
    }
    
    /**
     * Update cart item quantity (AJAX)
     */
    public function update() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $cartId = $_POST['cart_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if (!$cartId || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $this->getCartSessionId();
        
        if ($this->cartModel->updateQuantity($cartId, $userId, $sessionId, $quantity)) {
            $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated',
                'cartTotal' => $cartTotal
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
        exit;
    }
    
    /**
     * Remove item from cart (AJAX)
     */
    public function remove() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $cartId = $_POST['cart_id'] ?? null;
        
        if (!$cartId) {
            echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
            exit;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $this->getCartSessionId();
        
        if ($this->cartModel->remove($cartId, $userId, $sessionId)) {
            $count = $this->cartModel->getCount($userId, $sessionId);
            $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart',
                'count' => $count,
                'cartTotal' => $cartTotal
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
        }
        exit;
    }
    
    /**
     * Get cart count (AJAX)
     */
    public function getCount() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = $this->getCartSessionId();
        
        $count = $this->cartModel->getCount($userId, $sessionId);
        
        echo json_encode(['count' => $count]);
        exit;
    }
}
