<?php
require_once __DIR__ . '/../models/Review.php';

class ReviewController extends Controller {
    private $reviewModel;
    
    public function __construct() {
        $this->reviewModel = new Review();
    }
    
    /**
     * Add a review (AJAX)
     */
    public function add() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $productId = intval($_POST['product_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        $userId = $_SESSION['user_id'];
        
        // Validation
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product']);
            exit;
        }
        
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5 stars']);
            exit;
        }
        
        if (empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'Please write a comment']);
            exit;
        }
        
        // Check if user already reviewed this product
        if ($this->reviewModel->hasUserReviewed($productId, $userId)) {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product']);
            exit;
        }
        
        // Add review
        if ($this->reviewModel->add($productId, $userId, $rating, $comment)) {
            echo json_encode([
                'success' => true,
                'message' => 'Thank you for your review!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to submit review. Please try again.'
            ]);
        }
        exit;
    }
    
    /**
     * Delete a review (AJAX)
     */
    public function delete() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $reviewId = intval($_POST['review_id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if ($reviewId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid review']);
            exit;
        }
        
        if ($this->reviewModel->deleteReview($reviewId, $userId)) {
            echo json_encode([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete review'
            ]);
        }
        exit;
    }
}
