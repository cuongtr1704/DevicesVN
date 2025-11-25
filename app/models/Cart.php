<?php
require_once __DIR__ . '/Model.php';

class Cart extends Model {
    
    /**
     * Add item to cart
     */
    public function add($userId, $sessionId, $productId, $quantity = 1) {
        // Check if item already exists in cart
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart WHERE (user_id = ? OR session_id = ?) AND product_id = ?");
        $stmt->execute([$userId, $sessionId, $productId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $this->db->prepare("UPDATE cart SET quantity = ?, user_id = ? WHERE id = ?");
            return $stmt->execute([$newQuantity, $userId, $existing['id']]);
        } else {
            // Insert new item
            $stmt = $this->db->prepare("INSERT INTO cart (user_id, session_id, product_id, quantity) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $sessionId, $productId, $quantity]);
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function updateQuantity($cartId, $userId, $sessionId, $quantity) {
        if ($userId) {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            return $stmt->execute([$quantity, $cartId, $userId]);
        } else {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND session_id = ?");
            return $stmt->execute([$quantity, $cartId, $sessionId]);
        }
    }
    
    /**
     * Remove item from cart
     */
    public function remove($cartId, $userId, $sessionId) {
        if ($userId) {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            return $stmt->execute([$cartId, $userId]);
        } else {
            $stmt = $this->db->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
            return $stmt->execute([$cartId, $sessionId]);
        }
    }
    
    /**
     * Get user's cart items with product details
     * Prioritizes user_id over session_id if both exist
     */
    public function getUserCart($userId, $sessionId = null) {
        if ($userId) {
            // If user is logged in, only get their user-specific cart
            $query = "SELECT c.*, p.name, p.price, p.stock_quantity, p.slug,
                      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) as image
                      FROM cart c
                      INNER JOIN products p ON c.product_id = p.id
                      WHERE c.user_id = ?
                      ORDER BY c.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
        } else {
            // If guest, get session-based cart
            $query = "SELECT c.*, p.name, p.price, p.stock_quantity, p.slug,
                      (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) as image
                      FROM cart c
                      INNER JOIN products p ON c.product_id = p.id
                      WHERE c.session_id = ? AND c.user_id IS NULL
                      ORDER BY c.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$sessionId]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal($userId, $sessionId = null) {
        $items = $this->getUserCart($userId, $sessionId);
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Get cart item count
     * Prioritizes user_id over session_id
     */
    public function getCount($userId, $sessionId = null) {
        if ($userId) {
            $stmt = $this->db->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
        } else {
            $stmt = $this->db->prepare("SELECT SUM(quantity) as count FROM cart WHERE session_id = ? AND user_id IS NULL");
            $stmt->execute([$sessionId]);
        }
        
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    /**
     * Clear user's cart
     */
    public function clearUserCart($userId) {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Transfer guest cart to user account
     */
    public function transferGuestCart($sessionId, $userId) {
        $stmt = $this->db->prepare("UPDATE cart SET user_id = ? WHERE session_id = ? AND user_id IS NULL");
        return $stmt->execute([$userId, $sessionId]);
    }
    
    /**
     * Clean up old guest cart sessions (older than 30 days)
     * Should be called periodically via cron job or on certain actions
     */
    public function cleanupOldGuestCarts($daysOld = 30) {
        $stmt = $this->db->prepare("
            DELETE FROM cart 
            WHERE user_id IS NULL 
            AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        return $stmt->execute([$daysOld]);
    }
    
    /**
     * Clean up abandoned carts (no activity for X days, not checked out)
     */
    public function cleanupAbandonedCarts($daysOld = 7) {
        // Get carts that haven't been updated recently and weren't converted to orders
        $stmt = $this->db->prepare("
            DELETE c FROM cart c
            LEFT JOIN orders o ON c.user_id = o.user_id
            WHERE c.created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            AND (o.id IS NULL OR o.created_at < c.created_at)
        ");
        return $stmt->execute([$daysOld]);
    }
}
