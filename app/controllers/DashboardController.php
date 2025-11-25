<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '']
        ];
        
        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'activeSection' => 'overview',
            'userRole' => $userRole,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    public function profile() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if it's a password change request
            if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                $errors = [];
                
                if (empty($currentPassword)) {
                    $errors[] = 'Current password is required';
                } elseif (!password_verify($currentPassword, $user['password'])) {
                    $errors[] = 'Current password is incorrect';
                }
                
                if (strlen($newPassword) < 8) {
                    $errors[] = 'New password must be at least 8 characters';
                }
                
                if ($newPassword !== $confirmPassword) {
                    $errors[] = 'New password and confirm password do not match';
                }
                
                if (empty($errors)) {
                    $updated = $userModel->update($_SESSION['user_id'], [
                        'password' => password_hash($newPassword, PASSWORD_DEFAULT)
                    ]);
                    
                    if ($updated) {
                        $this->setFlash('password', 'Password changed successfully!', 'success');
                        $this->redirect('dashboard/profile');
                    } else {
                        $errors[] = 'Failed to update password';
                    }
                } else {
                    // Show password errors
                    $this->setFlash('password', implode('<br>', $errors), 'danger');
                    $this->redirect('dashboard/profile');
                }
                
                $data['errors'] = $errors;
            } else {
                // Profile update
                $fullName = trim($_POST['full_name'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');
                
                $errors = [];
                
                if (empty($fullName)) {
                    $errors[] = 'Full name is required';
                }
                
                if (empty($errors)) {
                    $updated = $userModel->update($_SESSION['user_id'], [
                        'full_name' => $fullName,
                        'phone' => $phone,
                        'address' => $address
                    ]);
                    
                    if ($updated) {
                        $_SESSION['user_name'] = $fullName;
                        $this->setFlash('profile', 'Profile updated successfully!', 'success');
                        $this->redirect('dashboard/profile');
                    } else {
                        $errors[] = 'Failed to update profile';
                    }
                }
                
                $data['errors'] = $errors;
            }
        }
        
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'My Profile', 'url' => '']
        ];
        
        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $user,
            'activeSection' => 'profile',
            'userRole' => $userRole,
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/profile', $data);
    }
    
    /**
     * Categories management (Admin only)
     */
    public function categories() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        // Handle AJAX requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
            // Clear any output buffers and ensure clean JSON
            while (ob_get_level()) {
                ob_end_clean();
            }
            ob_start();
            header('Content-Type: application/json');
            
            $categoryModel = $this->model('Category');
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'add':
                    try {
                        $name = trim($_POST['name'] ?? '');
                        $description = trim($_POST['description'] ?? '');
                        $icon = trim($_POST['icon'] ?? '');
                        $sortOrder = (int)($_POST['sort_order'] ?? 0);
                        
                        if (empty($name)) {
                            echo json_encode(['success' => false, 'message' => 'Category name is required']);
                            exit;
                        }
                        
                        // Auto-arrange sort order: get max sort_order and add 1
                        if ($sortOrder <= 0) {
                            $sortOrder = $categoryModel->getMaxSortOrder() + 1;
                        } else {
                            // If specific sort order provided, shift others
                            $categoryModel->adjustSortOrders($sortOrder, 'insert');
                        }
                        
                        $slug = $this->generateSlug($name);
                        
                        $id = $categoryModel->insert([
                            'name' => $name,
                            'slug' => $slug,
                            'description' => $description,
                            'icon' => $icon,
                            'sort_order' => $sortOrder,
                            'is_active' => 1
                        ]);
                        
                        if ($id) {
                            // Cleanup and reorder sequentially after insert
                            $categoryModel->reorderSequentially();
                            echo json_encode(['success' => true, 'message' => 'Category added successfully']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Failed to add category']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                    }
                    exit;
                    
                case 'edit':
                    $id = (int)($_POST['id'] ?? 0);
                    $name = trim($_POST['name'] ?? '');
                    $description = trim($_POST['description'] ?? '');
                    $icon = trim($_POST['icon'] ?? '');
                    $sortOrder = (int)($_POST['sort_order'] ?? 0);
                    
                    if (empty($name) || $id <= 0) {
                        echo json_encode(['success' => false, 'message' => 'Invalid data']);
                        exit;
                    }
                    
                    $currentCategory = $categoryModel->findById($id);
                    if (!$currentCategory) {
                        echo json_encode(['success' => false, 'message' => 'Category not found']);
                        exit;
                    }
                    
                    // If sort order changed, adjust others
                    if ($sortOrder != $currentCategory['sort_order']) {
                        $categoryModel->adjustSortOrders($sortOrder, 'update', $id, $currentCategory['sort_order']);
                    }
                    
                    $slug = $this->generateSlug($name);
                    
                    $updated = $categoryModel->update($id, [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'icon' => $icon,
                        'sort_order' => $sortOrder
                    ]);
                    
                    if ($updated) {
                        // Cleanup and reorder sequentially after update
                        $categoryModel->reorderSequentially();
                        echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update category']);
                    }
                    exit;
                    
                case 'delete':
                    $id = (int)($_POST['id'] ?? 0);
                    
                    if ($id <= 0) {
                        echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
                        exit;
                    }
                    
                    // Check if category has products
                    $productCount = $this->getCategoryProductCount($id);
                    
                    if ($productCount > 0) {
                        echo json_encode(['success' => false, 'message' => "Cannot delete category with {$productCount} product(s). Please remove or reassign products first."]);
                        exit;
                    }
                    
                    // Get current sort order before deletion
                    $currentCategory = $categoryModel->findById($id);
                    $oldSortOrder = $currentCategory ? $currentCategory['sort_order'] : null;
                    
                    $deleted = $categoryModel->delete($id);
                    
                    if ($deleted) {
                        // Adjust sort orders after deletion
                        if ($oldSortOrder !== null) {
                            $categoryModel->adjustSortOrders(0, 'delete', null, $oldSortOrder);
                        }
                        // Cleanup and reorder sequentially
                        $categoryModel->reorderSequentially();
                        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
                    }
                    exit;
                    
                case 'get':
                    $id = (int)($_POST['id'] ?? 0);
                    $category = $categoryModel->findById($id);
                    
                    if ($category) {
                        echo json_encode(['success' => true, 'category' => $category]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Category not found']);
                    }
                    exit;
            }
            
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
        }
        
        $categoryModel = $this->model('Category');
        $categoriesRaw = $categoryModel->getAll();
        
        // Sort by sort_order ASC
        usort($categoriesRaw, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });
        
        // Add product count to each category
        $categories = [];
        foreach ($categoriesRaw as $category) {
            $category['product_count'] = $this->getCategoryProductCount($category['id']);
            $categories[] = $category;
        }
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Categories', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage Categories - ' . APP_NAME,
            'categories' => $categories,
            'activeSection' => 'categories',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/categories', $data);
    }
    
    /**
     * Get product count for a category (including children)
     */
    private function getCategoryProductCount($categoryId) {
        $categoryModel = $this->model('Category');
        $productModel = $this->model('Product');
        
        // Get category with all children
        $categoryIds = $categoryModel->getCategoryWithChildren($categoryId);
        
        // Count products in these categories using Product model method
        return $productModel->countByCategoryIds($categoryIds);
    }
    
    /**
     * Generate URL-friendly slug
     */
    private function generateSlug($text) {
        // Convert Vietnamese characters
        $text = $this->removeVietnameseTones($text);
        
        // Convert to lowercase
        $text = strtolower($text);
        
        // Replace spaces and special chars with hyphens
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        
        // Remove duplicate hyphens
        $text = preg_replace('/-+/', '-', $text);
        
        // Trim hyphens from ends
        $text = trim($text, '-');
        
        return $text;
    }
    
    /**
     * Remove Vietnamese tones
     */
    private function removeVietnameseTones($str) {
        $vietnamese = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];
        
        $replace = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'I', 'I', 'I', 'I', 'I',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'D'
        ];
        
        return str_replace($vietnamese, $replace, $str);
    }
    
    /**
     * Products management (Admin only)
     */
    public function products() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        $productModel = $this->model('Product');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        
        $products = $productModel->getPaginated($page, $perPage);
        $totalProducts = $productModel->count();
        $totalPages = ceil($totalProducts / $perPage);
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Products', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage Products - ' . APP_NAME,
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'activeSection' => 'products',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/products', $data);
    }
    
    /**
     * Customers management (Admin only)
     */
    public function customers() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        $userModel = $this->model('User');
        $customers = $userModel->getAll();
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Customers', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage Customers - ' . APP_NAME,
            'customers' => $customers,
            'activeSection' => 'customers',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/customers', $data);
    }
    
    /**
     * All orders management (Admin only)
     */
    public function allOrders() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        $orderModel = $this->model('Order');
        $orders = $orderModel->getAll('created_at DESC', 100);
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'All Orders', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage All Orders - ' . APP_NAME,
            'orders' => $orders,
            'activeSection' => 'all-orders',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/all-orders', $data);
    }
    
    /**
     * View product details (API endpoint)
     */
    public function viewProduct($id) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $productModel = $this->model('Product');
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        // Get product with category info
        $sql = "SELECT p.*, c.name as category_name, pi.image_url as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE p.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    }
    
    /**
     * Add new product (API endpoint)
     */
    public function addProduct() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = $this->model('Product');
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'sku' => $_POST['sku'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'description' => $_POST['description'] ?? '',
                'specifications' => $_POST['specifications'] ?? ''
            ];
            
            $productId = $productModel->insert($data);
            
            if ($productId) {
                echo json_encode(['success' => true, 'message' => 'Product added successfully', 'product_id' => $productId]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Update product (API endpoint)
     */
    public function updateProduct($id) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = $this->model('Product');
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'sku' => $_POST['sku'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'description' => $_POST['description'] ?? '',
                'specifications' => $_POST['specifications'] ?? ''
            ];
            
            $updated = $productModel->update($id, $data);
            
            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Delete product (API endpoint)
     */
    public function deleteProduct($id) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $productModel = $this->model('Product');
            $deleted = $productModel->delete($id);
            
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Get all categories (API endpoint)
     */
    public function getCategories() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $categoryModel = $this->model('Category');
        $categories = $categoryModel->getAll('name ASC');
        
        echo json_encode(['success' => true, 'categories' => $categories]);
    }
}
