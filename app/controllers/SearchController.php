<?php
require_once __DIR__ . '/../models/Product.php';

class SearchController extends Controller {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    /**
     * Main search page
     */
    public function index() {
        $query = trim($_GET['q'] ?? '');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 12;
        
        if (empty($query)) {
            $this->redirect('products');
            return;
        }
        
        // Search products
        $results = $this->productModel->search($query, $page, $perPage);
        $totalResults = $this->productModel->countSearchResults($query);
        $totalPages = ceil($totalResults / $perPage);
        
        $breadcrumbs = [
            ['label' => 'Products', 'url' => url('products')],
            ['label' => 'Search Results', 'url' => '']
        ];
        
        $this->view('products/search', [
            'title' => 'Search Results for "' . escape($query) . '"',
            'query' => $query,
            'products' => $results,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalResults' => $totalResults,
            'breadcrumbs' => $breadcrumbs
        ]);
    }
    
    /**
     * AJAX search suggestions
     */
    public function suggestions() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        $query = trim($_GET['q'] ?? '');
        
        if (strlen($query) < 2) {
            echo json_encode(['suggestions' => []]);
            exit;
        }
        
        // Get top 5 matching products
        $suggestions = $this->productModel->searchSuggestions($query, 5);
        
        // Convert image paths to full URLs
        foreach ($suggestions as &$product) {
            if (!empty($product['main_image'])) {
                $product['main_image'] = asset($product['main_image']);
            } else {
                $product['main_image'] = asset('images/no-image.png');
            }
        }
        
        echo json_encode(['suggestions' => $suggestions]);
        exit;
    }
}
