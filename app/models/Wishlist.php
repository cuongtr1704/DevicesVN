<?php

require_once ROOT_PATH . '/app/models/Model.php';

class Wishlist extends Model {
    
    /**
     * Add product to wishlist
     */
    public function add($userId, $productId) {
        $sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }
    
    /**
     * Remove product from wishlist
     */
    public function remove($userId, $productId) {
        $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $productId]);
    }
    
    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($userId, $productId) {
        $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Get all wishlist items for user with product details
     */
    public function getUserWishlist($userId) {
        $sql = "SELECT w.*, p.*, 
                       COALESCE(pi.image_url, '/public/images/no-image.jpg') as main_image
                FROM wishlist w
                INNER JOIN products p ON w.product_id = p.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE w.user_id = ?
                ORDER BY w.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get wishlist count for user
     */
    public function getCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Clear all wishlist items for user
     */
    public function clearUserWishlist($userId) {
        $sql = "DELETE FROM wishlist WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
