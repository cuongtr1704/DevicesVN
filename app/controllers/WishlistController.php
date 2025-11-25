<?php

class WishlistController extends Controller {
    
    /**
     * Display wishlist page
     */
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $wishlistModel = $this->model('Wishlist');
        $items = $wishlistModel->getUserWishlist($_SESSION['user_id']);
        
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Wishlist', 'url' => '']
        ];
        
        $data = [
            'title' => 'My Wishlist - ' . APP_NAME,
            'items' => $items,
            'activeSection' => 'wishlist',
            'userRole' => $userRole,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/wishlist', $data);
    }
    
    /**
     * Add product to wishlist (AJAX)
     */
    public function add() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to add items to wishlist']);
            exit;
        }
        
        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $wishlistModel = $this->model('Wishlist');
        
        // Check if already in wishlist
        if ($wishlistModel->isInWishlist($_SESSION['user_id'], $productId)) {
            echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
            exit;
        }
        
        $added = $wishlistModel->add($_SESSION['user_id'], $productId);
        
        if ($added) {
            $count = $wishlistModel->getCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true, 
                'message' => 'Product added to wishlist',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist']);
        }
        exit;
    }
    
    /**
     * Remove product from wishlist (AJAX)
     */
    public function remove() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }
        
        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $wishlistModel = $this->model('Wishlist');
        $removed = $wishlistModel->remove($_SESSION['user_id'], $productId);
        
        if ($removed) {
            $count = $wishlistModel->getCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true, 
                'message' => 'Product removed from wishlist',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product']);
        }
        exit;
    }
    
    /**
     * Toggle wishlist status (AJAX)
     */
    public function toggle() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to add items to wishlist']);
            exit;
        }
        
        $productId = $_POST['product_id'] ?? null;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID is required']);
            exit;
        }
        
        $wishlistModel = $this->model('Wishlist');
        
        // Check current status
        $isInWishlist = $wishlistModel->isInWishlist($_SESSION['user_id'], $productId);
        
        if ($isInWishlist) {
            $result = $wishlistModel->remove($_SESSION['user_id'], $productId);
            $message = 'Product removed from wishlist';
            $inWishlist = false;
        } else {
            $result = $wishlistModel->add($_SESSION['user_id'], $productId);
            $message = 'Product added to wishlist';
            $inWishlist = true;
        }
        
        if ($result) {
            $count = $wishlistModel->getCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'inWishlist' => $inWishlist,
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Operation failed']);
        }
        exit;
    }
}
