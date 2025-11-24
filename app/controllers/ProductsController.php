<?php
/**
 * Products Controller
 */

class ProductsController extends Controller {
    
    public function index() {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'id DESC';
        
        $filters = [];
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        
        $products = $productModel->getPaginated($page, PRODUCTS_PER_PAGE, $sortBy, $filters);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = ceil($totalProducts / PRODUCTS_PER_PAGE);
        
        $categories = $categoryModel->getActive();
        
        $data = [
            'title' => 'Products - ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'currentCategory' => $categoryId,
            'currentSort' => $sortBy
        ];
        
        $this->view('products/index', $data);
    }

    public function detail($slug) {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $product = $productModel->findBySlug($slug);
        
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', ['title' => '404 - Product Not Found']);
            return;
        }
        
        $storeAvailability = $productModel->getStoreAvailability($product['id']);
        $breadcrumb = $categoryModel->getBreadcrumb($product['category_id']);
        $specifications = json_decode($product['specifications'], true);
        
        $data = [
            'title' => $product['name'] . ' - ' . APP_NAME,
            'metaDescription' => $product['meta_description'] ?? substr($product['description'], 0, 160),
            'product' => $product,
            'specifications' => $specifications,
            'storeAvailability' => $storeAvailability,
            'breadcrumb' => $breadcrumb
        ];
        
        $this->view('products/view', $data);
    }

    public function category($slug) {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $category = $categoryModel->findBySlug($slug);
        
        if (!$category) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', ['title' => '404 - Category Not Found']);
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'id DESC';
        
        $filters = ['category_id' => $category['id']];
        
        $products = $productModel->getPaginated($page, PRODUCTS_PER_PAGE, $sortBy, $filters);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = ceil($totalProducts / PRODUCTS_PER_PAGE);
        
        $breadcrumb = $categoryModel->getBreadcrumb($category['id']);
        
        $data = [
            'title' => $category['name'] . ' - ' . APP_NAME,
            'category' => $category,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'currentSort' => $sortBy,
            'breadcrumb' => $breadcrumb
        ];
        
        $this->view('products/category', $data);
    }
}
