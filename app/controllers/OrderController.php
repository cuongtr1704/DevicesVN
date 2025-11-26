<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController extends Controller {
    private $orderModel;
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    /**
     * Display user's orders
     */
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $orders = $this->orderModel->getUserOrders($userId);
        
        $this->view('dashboard/orders', [
            'title' => 'My Orders',
            'orders' => $orders,
            'activeSection' => 'orders',
            'userRole' => $_SESSION['user_role'] ?? 'customer',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => url('dashboard')],
                ['label' => 'My Orders', 'url' => '']
            ]
        ]);
    }
    
    /**
     * Display order details
     */
    public function detail($orderId) {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        // Admin can view any order, customer can only view their own
        if ($userRole === 'admin') {
            $order = $this->orderModel->findById($orderId);
        } else {
            $order = $this->orderModel->getById($orderId, $userId);
        }
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            $this->redirect('dashboard/orders');
            return;
        }
        
        $orderItems = $this->orderModel->getOrderItems($orderId);
        
        // Determine breadcrumbs based on referrer
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $breadcrumbs = [];
        
        if ($userRole === 'admin') {
            // Always show user-specific orders link for admin
            $userModel = $this->model('User');
            $orderUser = $userModel->findById($order['user_id']);
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => url('dashboard')],
                ['label' => 'Users', 'url' => url('dashboard/users')],
                ['label' => $orderUser['full_name'] . "'s Orders", 'url' => url('dashboard/users/user-orders/' . $order['user_id'])],
                ['label' => 'Order #' . $order['order_number'], 'url' => '']
            ];
        } else {
            $breadcrumbs = [
                ['label' => 'Dashboard', 'url' => url('dashboard')],
                ['label' => 'My Orders', 'url' => url('dashboard/orders')],
                ['label' => 'Order #' . $order['order_number'], 'url' => '']
            ];
        }
        
        $this->view('orders/detail', [
            'title' => 'Order Details - ' . $order['order_number'],
            'order' => $order,
            'orderItems' => $orderItems,
            'breadcrumbs' => $breadcrumbs,
            'userRole' => $userRole
        ]);
    }
    
    /**
     * Display checkout page
     */
    public function checkout() {
        if (!$this->isLoggedIn()) {
            $_SESSION['error'] = 'Please login to checkout';
            $this->redirect('login');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $sessionId = $_SESSION['cart_session_id'] ?? null;
        
        $cartItems = $this->cartModel->getUserCart($userId, $sessionId);
        
        if (empty($cartItems)) {
            $_SESSION['error'] = 'Your cart is empty';
            $this->redirect('cart');
            return;
        }
        
        $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
        
        $this->view('orders/checkout', [
            'title' => 'Checkout',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'breadcrumbs' => [
                ['label' => 'Shopping Cart', 'url' => url('cart')],
                ['label' => 'Checkout', 'url' => '']
            ]
        ]);
    }
    
    /**
     * Process checkout
     */
    public function process() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $sessionId = $_SESSION['cart_session_id'] ?? null;
        
        // Validate input
        $shippingName = trim($_POST['shipping_name'] ?? '');
        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $shippingPhone = trim($_POST['shipping_phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        
        if (empty($shippingName) || empty($shippingAddress) || empty($shippingPhone)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
            exit;
        }
        
        // Get cart items
        $cartItems = $this->cartModel->getUserCart($userId, $sessionId);
        
        if (empty($cartItems)) {
            echo json_encode(['success' => false, 'message' => 'Your cart is empty']);
            exit;
        }
        
        // Check stock availability
        foreach ($cartItems as $item) {
            $product = $this->productModel->getById($item['product_id']);
            if ($product['stock_quantity'] < $item['quantity']) {
                echo json_encode([
                    'success' => false,
                    'message' => "Insufficient stock for {$item['name']}"
                ]);
                exit;
            }
        }
        
        // Calculate total
        $totalAmount = $this->cartModel->getCartTotal($userId, $sessionId);
        
        // Create order
        $orderData = [
            'total_amount' => $totalAmount,
            'shipping_name' => $shippingName,
            'shipping_address' => $shippingAddress,
            'shipping_phone' => $shippingPhone,
            'notes' => $notes
        ];
        
        $result = $this->orderModel->create($userId, $orderData);
        
        if ($result) {
            // Add order items
            $this->orderModel->addItems($result['order_id'], $cartItems);
            
            // Update product stock
            foreach ($cartItems as $item) {
                $product = $this->productModel->getById($item['product_id']);
                $newStock = $product['stock_quantity'] - $item['quantity'];
                $this->productModel->updateStock($item['product_id'], $newStock);
            }
            
            // Clear cart
            $this->cartModel->clearUserCart($userId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_number' => $result['order_number'],
                'redirect' => url('dashboard/orders')
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to place order']);
        }
    }
    
    /**
     * Update order status (Admin only)
     */
    public function updateStatus($orderId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';
        
        $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        
        $updated = $this->orderModel->update($orderId, ['status' => $status]);
        
        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
        }
    }
    
    /**
     * Cancel order
     */
    public function cancel() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $orderId = $_POST['order_id'] ?? null;
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Invalid order']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        if ($this->orderModel->cancel($orderId, $userId)) {
            echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order. Order may not be in pending status.']);
        }
        exit;
    }
}
