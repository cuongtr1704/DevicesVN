<?php
require_once __DIR__ . '/Model.php';

class Review extends Model {
    
    /**
     * Add a new review
     */
    public function add($productId, $userId, $rating, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (product_id, user_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([$productId, $userId, $rating, $comment]);
    }
    
    /**
     * Get reviews for a product
     */
    public function getProductReviews($productId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.full_name as user_name, u.email as user_email
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
            ORDER BY r.created_at DESC
        ");
        
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get average rating for a product
     */
    public function getAverageRating($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews
            FROM reviews
            WHERE product_id = ?
        ");
        
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }
    
    /**
     * Get rating breakdown (count per star)
     */
    public function getRatingBreakdown($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                rating,
                COUNT(*) as count
            FROM reviews
            WHERE product_id = ?
            GROUP BY rating
            ORDER BY rating DESC
        ");
        
        $stmt->execute([$productId]);
        $results = $stmt->fetchAll();
        
        // Create array with all ratings (1-5) initialized to 0
        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($results as $row) {
            $breakdown[$row['rating']] = $row['count'];
        }
        
        return $breakdown;
    }
    
    /**
     * Check if user has reviewed a product
     */
    public function hasUserReviewed($productId, $userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM reviews
            WHERE product_id = ? AND user_id = ?
        ");
        
        $stmt->execute([$productId, $userId]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    /**
     * Delete a review
     */
    public function deleteReview($reviewId, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM reviews
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$reviewId, $userId]);
    }
}
