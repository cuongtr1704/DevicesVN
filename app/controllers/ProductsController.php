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
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;
        
        $filters = [];
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        if ($minPrice) {
            $filters['min_price'] = $minPrice;
        }
        if ($maxPrice) {
            $filters['max_price'] = $maxPrice;
        }
        
        $products = $productModel->getPaginated($page, PRODUCTS_PER_PAGE, $sortBy, $filters);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = ceil($totalProducts / PRODUCTS_PER_PAGE);
        
        $categories = $categoryModel->getActive();
        
        $breadcrumbs = [
            ['label' => 'Products', 'url' => '']
        ];
        
        $data = [
            'title' => 'Products - ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'currentCategory' => $categoryId,
            'currentSort' => $sortBy,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('products/index', $data);
    }

    public function detail($slug) {
        $productModel = $this->model('Product');
        $productImageModel = $this->model('ProductImage');
        
        $product = $productModel->findBySlug($slug);
        
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', ['title' => '404 - Product Not Found']);
            return;
        }
        
        // Get all images for gallery
        $images = $productImageModel->getProductImages($product['id']);
        
        // Get related products (same category)
        $relatedProducts = $productModel->getPaginated(1, 4, 'RAND()', ['category_id' => $product['category_id']]);
        
        $specifications = json_decode($product['specifications'], true);
        
        $breadcrumbs = [
            ['label' => 'Products', 'url' => url('products')],
            ['label' => $product['category_name'], 'url' => url('products/category/' . $product['category_slug'])],
            ['label' => $product['name'], 'url' => '']
        ];
        
        $data = [
            'title' => $product['name'] . ' - ' . APP_NAME,
            'product' => $product,
            'images' => $images,
            'specifications' => $specifications,
            'relatedProducts' => $relatedProducts,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('products/detail', $data);
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
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;
        
        // Get category IDs - include children if this is a parent category
        $categoryIds = $categoryModel->getCategoryWithChildren($category['id']);
        
        $filters = ['category_ids' => $categoryIds];
        if ($minPrice) {
            $filters['min_price'] = $minPrice;
        }
        if ($maxPrice) {
            $filters['max_price'] = $maxPrice;
        }
        
        $products = $productModel->getPaginated($page, PRODUCTS_PER_PAGE, $sortBy, $filters);
        $totalProducts = $productModel->countFiltered($filters);
        $totalPages = ceil($totalProducts / PRODUCTS_PER_PAGE);
        
        $allCategories = $categoryModel->getActive();
        
        $breadcrumbs = [
            ['label' => 'Products', 'url' => url('products')],
            ['label' => $category['name'], 'url' => '']
        ];
        
        $data = [
            'title' => $category['name'] . ' - ' . APP_NAME,
            'category' => $category,
            'products' => $products,
            'categories' => $allCategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'currentSort' => $sortBy,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('products/category', $data);
    }

    public function featured() {
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'p.views DESC';
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;
        
        $filters = [];
        if ($minPrice) {
            $filters['min_price'] = $minPrice;
        }
        if ($maxPrice) {
            $filters['max_price'] = $maxPrice;
        }
        
        $products = $productModel->getFeaturedPaginated($page, PRODUCTS_PER_PAGE, $sortBy, $filters);
        $totalProducts = $productModel->countFeatured($filters);
        $totalPages = ceil($totalProducts / PRODUCTS_PER_PAGE);
        
        $allCategories = $categoryModel->getActive();
        
        $breadcrumbs = [
            ['label' => 'Products', 'url' => url('products')],
            ['label' => 'Featured Products', 'url' => '']
        ];
        
        $data = [
            'title' => 'Featured Products - ' . APP_NAME,
            'products' => $products,
            'categories' => $allCategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'currentSort' => $sortBy,
            'breadcrumbs' => $breadcrumbs,
            'isFeaturedPage' => true
        ];
        
        $this->view('products/featured', $data);
    }
}
