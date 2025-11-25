<?php
require_once __DIR__ . '/Model.php';

class Order extends Model {
    
    /**
     * Create a new order
     */
    public function create($userId, $orderData) {
        // Generate order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        $stmt = $this->db->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, status, 
                               shipping_name, shipping_address, shipping_phone, notes)
            VALUES (?, ?, ?, 'pending', ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $orderNumber,
            $orderData['total_amount'],
            $orderData['shipping_name'],
            $orderData['shipping_address'],
            $orderData['shipping_phone'],
            $orderData['notes'] ?? null
        ]);
        
        return [
            'order_id' => $this->db->lastInsertId(),
            'order_number' => $orderNumber
        ];
    }
    
    /**
     * Add items to order
     */
    public function addItems($orderId, $items) {
        $stmt = $this->db->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $subtotal
            ]);
        }
        
        return true;
    }
    
    /**
     * Get user's orders
     */
    public function getUserOrders($userId) {
        $stmt = $this->db->prepare("
            SELECT o.*, COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get order by ID
     */
    public function getById($orderId, $userId = null) {
        $query = "SELECT * FROM orders WHERE id = ?";
        $params = [$orderId];
        
        if ($userId) {
            $query .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.slug,
            (SELECT image_url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) as image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }
    
    /**
     * Cancel order
     */
    public function cancel($orderId, $userId) {
        $stmt = $this->db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
        return $stmt->execute([$orderId, $userId]);
    }
    
    /**
     * Get all orders (admin)
     */
    public function getAll($orderBy = 'o.created_at DESC', $limit = null) {
        $query = "
            SELECT o.*, u.full_name as customer_name, u.email as customer_email,
            COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY {$orderBy}
        ";
        
        if ($limit) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
