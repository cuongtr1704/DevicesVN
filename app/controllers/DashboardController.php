<?php

class DashboardController extends Controller {
    
    public function index() {
        if (!$this->isLoggedIn()) {
            $this->redirect('');
        }
        
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'] ?? 'customer';
        
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => '']
        ];
        
        if ($userRole === 'admin') {
            // Admin Dashboard Statistics
            
            // Total Revenue
            $revenueStmt = $db->prepare("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
            $revenueStmt->execute();
            $totalRevenue = $revenueStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Total Orders
            $ordersStmt = $db->prepare("SELECT COUNT(*) as total FROM orders");
            $ordersStmt->execute();
            $totalOrders = $ordersStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total Products
            $productsStmt = $db->prepare("SELECT COUNT(*) as total FROM products");
            $productsStmt->execute();
            $totalProducts = $productsStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total Users
            $usersStmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
            $usersStmt->execute();
            $totalUsers = $usersStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pending Orders
            $pendingStmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
            $pendingStmt->execute();
            $pendingOrders = $pendingStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Low Stock Products
            $lowStockStmt = $db->prepare("SELECT COUNT(*) as total FROM products WHERE stock_quantity > 0 AND stock_quantity <= 10");
            $lowStockStmt->execute();
            $lowStockProducts = $lowStockStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Recent Orders (last 5)
            $recentOrdersStmt = $db->prepare("
                SELECT o.*, u.full_name as customer_name, COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $recentOrdersStmt->execute();
            $recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Order Status Distribution
            $statusStmt = $db->prepare("
                SELECT status, COUNT(*) as count
                FROM orders
                GROUP BY status
            ");
            $statusStmt->execute();
            $statusDistribution = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Monthly Revenue (last 6 months)
            $monthlyRevenueStmt = $db->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(total_amount) as revenue,
                    COUNT(*) as order_count
                FROM orders
                WHERE status != 'cancelled'
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            $monthlyRevenueStmt->execute();
            $monthlyRevenue = $monthlyRevenueStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Admin Dashboard - ' . APP_NAME,
                'activeSection' => 'overview',
                'userRole' => $userRole,
                'breadcrumbs' => $breadcrumbs,
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'totalProducts' => $totalProducts,
                'totalUsers' => $totalUsers,
                'pendingOrders' => $pendingOrders,
                'lowStockProducts' => $lowStockProducts,
                'recentOrders' => $recentOrders,
                'statusDistribution' => $statusDistribution,
                'monthlyRevenue' => $monthlyRevenue
            ];
        } else {
            // Customer Dashboard Statistics
            
            // Total Orders
            $ordersStmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
            $ordersStmt->execute([$userId]);
            $totalOrders = $ordersStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pending Orders
            $pendingStmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'pending'");
            $pendingStmt->execute([$userId]);
            $pendingOrders = $pendingStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Completed Orders
            $completedStmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'delivered'");
            $completedStmt->execute([$userId]);
            $completedOrders = $completedStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Wishlist Count
            $wishlistStmt = $db->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?");
            $wishlistStmt->execute([$userId]);
            $wishlistCount = $wishlistStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Total Spent
            $spentStmt = $db->prepare("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status != 'cancelled'");
            $spentStmt->execute([$userId]);
            $totalSpent = $spentStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            // Recent Orders (last 5)
            $recentOrdersStmt = $db->prepare("
                SELECT o.*, COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $recentOrdersStmt->execute([$userId]);
            $recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Order Status Distribution
            $statusStmt = $db->prepare("
                SELECT status, COUNT(*) as count
                FROM orders
                WHERE user_id = ?
                GROUP BY status
            ");
            $statusStmt->execute([$userId]);
            $statusDistribution = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [
                'title' => 'Dashboard - ' . APP_NAME,
                'activeSection' => 'overview',
                'userRole' => $userRole,
                'breadcrumbs' => $breadcrumbs,
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'completedOrders' => $completedOrders,
                'wishlistCount' => $wishlistCount,
                'totalSpent' => $totalSpent,
                'recentOrders' => $recentOrders,
                'statusDistribution' => $statusDistribution
            ];
        }
        
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
        
        // Get search and pagination parameters
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Get all categories for filtering
        $categoriesRaw = $categoryModel->getAll();
        
        // Filter by search
        if (!empty($search)) {
            $categoriesRaw = array_filter($categoriesRaw, function($category) use ($search) {
                return stripos($category['name'], $search) !== false || 
                       stripos($category['description'], $search) !== false;
            });
        }
        
        // Sort by sort_order ASC
        usort($categoriesRaw, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });
        
        // Calculate pagination
        $totalCategories = count($categoriesRaw);
        $totalPages = ceil($totalCategories / $perPage);
        
        // Apply pagination
        $categoriesRaw = array_slice($categoriesRaw, $offset, $perPage);
        
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
            'breadcrumbs' => $breadcrumbs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCategories' => $totalCategories,
            'search' => $search
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
        $categoryModel = $this->model('Category');
        
        // Get search, filter, and pagination parameters
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $stock = $_GET['stock'] ?? '';
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        $whereConditions = ["1=1"];
        $params = [];
        
        // Search by product name
        if (!empty($search)) {
            $whereConditions[] = "p.name LIKE :search";
            $params[':search'] = "%$search%";
        }
        
        // Filter by category
        if (!empty($category)) {
            $whereConditions[] = "p.category_id = :category";
            $params[':category'] = $category;
        }
        
        // Filter by stock status
        if ($stock === 'in') {
            $whereConditions[] = "p.stock_quantity > 0";
        } elseif ($stock === 'out') {
            $whereConditions[] = "p.stock_quantity = 0";
        } elseif ($stock === 'low') {
            $whereConditions[] = "p.stock_quantity > 0 AND p.stock_quantity <= 10";
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Determine sort order
        $orderBy = match($sort) {
            'oldest' => 'p.created_at ASC',
            'name_asc' => 'p.name ASC',
            'name_desc' => 'p.name DESC',
            'price_low' => 'p.price ASC',
            'price_high' => 'p.price DESC',
            default => 'p.created_at DESC'
        };
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM products p WHERE $whereClause";
        $stmt = $db->prepare($countQuery);
        $stmt->execute($params);
        $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalProducts / $perPage);
        
        // Get products with pagination
        $query = "SELECT p.*, c.name as category_name,
                         (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                  FROM products p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE $whereClause
                  ORDER BY $orderBy
                  LIMIT $perPage OFFSET $offset";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all categories for filter dropdown
        $categories = $categoryModel->getAll();
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Products', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage Products - ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'activeSection' => 'products',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs,
            'search' => $search,
            'selectedCategory' => $category,
            'selectedStock' => $stock,
            'sort' => $sort
        ];
        
        $this->view('dashboard/products', $data);
    }
    
    /**
     * Customers management (Admin only)
     */
    public function users() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        $userModel = $this->model('User');
        
        // Get search, filter, and pagination parameters
        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        $whereConditions = [];
        $params = [];
        
        // Search by name or email
        if (!empty($search)) {
            $whereConditions[] = "(full_name LIKE :search1 OR email LIKE :search2)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
        }
        
        // Filter by role
        if (!empty($role)) {
            $whereConditions[] = "role = :role";
            $params[':role'] = $role;
        }
        
        $whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : '1=1';
        
        // Determine sort order
        $orderBy = match($sort) {
            'oldest' => 'created_at ASC',
            'name_asc' => 'full_name ASC',
            'name_desc' => 'full_name DESC',
            default => 'created_at DESC'
        };
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM users WHERE $whereClause";
        $countStmt = $db->prepare($countQuery);
        
        // Create fresh params array for count query
        $countParams = [];
        if (!empty($search)) {
            $countParams[':search1'] = "%$search%";
            $countParams[':search2'] = "%$search%";
        }
        if (!empty($role)) {
            $countParams[':role'] = $role;
        }
        
        $countStmt->execute($countParams);
        $totalUsers = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalUsers / $perPage);
        
        // Get users with pagination
        $query = "SELECT * FROM users 
                  WHERE $whereClause 
                  ORDER BY $orderBy 
                  LIMIT $perPage OFFSET $offset";
        $usersStmt = $db->prepare($query);
        
        // Create fresh params array for users query
        $usersParams = [];
        if (!empty($search)) {
            $usersParams[':search1'] = "%$search%";
            $usersParams[':search2'] = "%$search%";
        }
        if (!empty($role)) {
            $usersParams[':role'] = $role;
        }
        
        $usersStmt->execute($usersParams);
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Users', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage Users - ' . APP_NAME,
            'users' => $users,
            'activeSection' => 'users',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalUsers' => $totalUsers,
            'search' => $search,
            'selectedRole' => $role,
            'sort' => $sort
        ];
        
        $this->view('dashboard/users', $data);
    }
    
    /**
     * View single user details (API endpoint)
     */
    public function viewUser($userId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($userId);
        
        if ($user) {
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
    }
    
    /**
     * View user's orders (API endpoint)
     */
    public function userOrders($userId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($userId);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            return;
        }
        
        $orderModel = $this->model('Order');
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        // Get user's orders with item count
        $stmt = $db->prepare("
            SELECT o.*, 
                   COUNT(oi.id) as item_count,
                   SUM(oi.quantity) as total_items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true, 
            'orders' => $orders,
            'user' => [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email']
            ]
        ]);
    }
    
    /**
     * View user's orders page (full page view)
     */
    public function viewUserOrders($userId) {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        $userModel = $this->model('User');
        $user = $userModel->findById($userId);
        
        if (!$user) {
            $this->redirect('dashboard/customers');
            return;
        }
        
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $sortBy = $_GET['sort'] ?? 'newest';
        
        // Pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Build WHERE clause
        $whereConditions = ["o.user_id = ?"];
        $params = [$userId];
        
        if (!empty($search)) {
            $whereConditions[] = "o.order_number LIKE ?";
            $params[] = "%{$search}%";
        }
        
        if (!empty($statusFilter)) {
            $whereConditions[] = "o.status = ?";
            $params[] = $statusFilter;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Determine ORDER BY
        $orderBy = match($sortBy) {
            'oldest' => 'o.created_at ASC',
            'amount_high' => 'o.total_amount DESC',
            'amount_low' => 'o.total_amount ASC',
            default => 'o.created_at DESC',
        };
        
        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM orders o WHERE {$whereClause}");
        $countStmt->execute($params);
        $totalOrders = $countStmt->fetchColumn();
        $totalPages = ceil($totalOrders / $perPage);
        
        // Get user's orders with item count (with pagination and filters)
        $stmt = $db->prepare("
            SELECT o.*, 
                   COUNT(oi.id) as item_count,
                   SUM(oi.quantity) as total_items
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE {$whereClause}
            GROUP BY o.id
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ");
        $params[] = $perPage;
        $params[] = $offset;
        $stmt->execute($params);
        $orders = $stmt->fetchAll();
        
        // Get all orders for statistics (not paginated, but with filters)
        $statsStmt = $db->prepare("SELECT status, total_amount FROM orders o WHERE {$whereClause}");
        array_pop($params); // Remove offset
        array_pop($params); // Remove limit
        $statsStmt->execute($params);
        $allOrders = $statsStmt->fetchAll();
        
        // Calculate order statistics
        $orderStats = [
            'pending' => 0,
            'confirmed' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0,
            'total_spent' => 0
        ];
        
        foreach ($allOrders as $order) {
            if (isset($orderStats[$order['status']])) {
                $orderStats[$order['status']]++;
            }
            if ($order['status'] !== 'cancelled') {
                $orderStats['total_spent'] += $order['total_amount'];
            }
        }
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'Users', 'url' => url('dashboard/customers')],
            ['label' => $user['full_name'] . "'s Orders", 'url' => '']
        ];
        
        $data = [
            'title' => $user['full_name'] . "'s Orders - " . APP_NAME,
            'user' => $user,
            'orders' => $orders,
            'orderStats' => $orderStats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalOrders
            ],
            'activeSection' => 'customers',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs
        ];
        
        $this->view('dashboard/user-orders', $data);
    }
    
    /**
     * Update user role (API endpoint)
     */
    public function updateUserRole($userId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $role = $input['role'] ?? '';
        
        if (!in_array($role, ['customer', 'admin'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role']);
            return;
        }
        
        // Prevent changing own role
        if ($userId == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Cannot change your own role']);
            return;
        }
        
        $userModel = $this->model('User');
        $updated = $userModel->update($userId, ['role' => $role]);
        
        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'User role updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user role']);
        }
    }
    
    /**
     * Delete user (API endpoint)
     */
    public function deleteUser($userId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        // Prevent deleting own account
        if ($userId == $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
            return;
        }
        
        $userModel = $this->model('User');
        $deleted = $userModel->delete($userId);
        
        if ($deleted) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
    }
    
    /**
     * All orders management (Admin only)
     */
    public function allOrders() {
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('dashboard');
            return;
        }
        
        // Get search, filter, and pagination parameters
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Build query
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        $whereConditions = [];
        $params = [];
        
        // Search by order number, customer name or email
        if (!empty($search)) {
            $whereConditions[] = "(o.order_number LIKE :search1 OR u.full_name LIKE :search2 OR u.email LIKE :search3)";
            $params[':search1'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
        }
        
        // Filter by status
        if (!empty($status)) {
            $whereConditions[] = "o.status = :status";
            $params[':status'] = $status;
        }
        
        $whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : '1=1';
        
        // Determine sort order
        $orderBy = match($sort) {
            'oldest' => 'o.created_at ASC',
            'amount_high' => 'o.total_amount DESC',
            'amount_low' => 'o.total_amount ASC',
            'customer' => 'u.full_name ASC',
            default => 'o.created_at DESC'
        };
        
        // Get total count for pagination
        $countQuery = "SELECT COUNT(DISTINCT o.id) as total 
                       FROM orders o 
                       LEFT JOIN users u ON o.user_id = u.id 
                       WHERE $whereClause";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute($params);
        $totalOrders = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalOrders / $perPage);
        
        // Get orders with pagination
        $query = "SELECT o.*, 
                         u.full_name as customer_name, 
                         u.email as customer_email,
                         COUNT(oi.id) as item_count
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  LEFT JOIN order_items oi ON o.id = oi.order_id
                  WHERE $whereClause
                  GROUP BY o.id
                  ORDER BY $orderBy
                  LIMIT $perPage OFFSET $offset";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => url('dashboard')],
            ['label' => 'All Orders', 'url' => '']
        ];
        
        $data = [
            'title' => 'Manage All Orders - ' . APP_NAME,
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalOrders' => $totalOrders,
            'activeSection' => 'all-orders',
            'userRole' => 'admin',
            'breadcrumbs' => $breadcrumbs,
            'search' => $search,
            'selectedStatus' => $status,
            'sort' => $sort
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
            // Get all product images
            $imgStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_main DESC, id ASC");
            $imgStmt->execute([$id]);
            $product['images'] = $imgStmt->fetchAll();
            
            // Parse specifications JSON to array
            if (!empty($product['specifications'])) {
                $specs = json_decode($product['specifications'], true);
                $product['specifications_array'] = $specs ?: [];
            } else {
                $product['specifications_array'] = [];
            }
            
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
            $database = Database::getInstance();
            $db = $database->getConnection();
            
            // Convert specifications array to JSON
            $specifications = [];
            if (!empty($_POST['spec_keys']) && is_array($_POST['spec_keys'])) {
                foreach ($_POST['spec_keys'] as $index => $key) {
                    if (!empty($key) && !empty($_POST['spec_values'][$index])) {
                        $specifications[$key] = $_POST['spec_values'][$index];
                    }
                }
            }
            
            // Generate slug from product name
            $slug = slugify($_POST['name'] ?? '');
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'slug' => $slug,
                'sku' => $_POST['sku'] ?? '',
                'brand' => $_POST['brand'] ?? null,
                'price' => $_POST['price'] ?? 0,
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'description' => $_POST['description'] ?? '',
                'specifications' => !empty($specifications) ? json_encode($specifications) : null
            ];
            
            try {
                $productId = $productModel->insert($data);
                
                if ($productId) {
                    // Handle image uploads
                    $this->handleProductImages($productId, $_FILES);
                    
                    echo json_encode(['success' => true, 'message' => 'Product added successfully', 'product_id' => $productId]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add product']);
                }
            } catch (PDOException $e) {
                // Check for duplicate entry error
                if ($e->getCode() == 23000) {
                    if (strpos($e->getMessage(), 'slug') !== false) {
                        echo json_encode(['success' => false, 'message' => 'Product with this name already exists. Please use a different name.']);
                    } else if (strpos($e->getMessage(), 'sku') !== false) {
                        echo json_encode(['success' => false, 'message' => 'SKU already exists. Please use a different SKU.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Duplicate entry detected.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                }
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Handle product image uploads
     */
    private function handleProductImages($productId, $files) {
        $database = Database::getInstance();
        $db = $database->getConnection();
        $uploadDir = ROOT_PATH . '/storage/uploads/';
        
        // Ensure upload directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Handle main image
        if (isset($files['main_image']) && $files['main_image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadImage($files['main_image'], $uploadDir);
            if ($imageUrl) {
                // Check if main image already exists for this product
                $stmt = $db->prepare("SELECT id, image_url FROM product_images WHERE product_id = ? AND is_main = 1");
                $stmt->execute([$productId]);
                $existingMain = $stmt->fetch();
                
                if ($existingMain) {
                    // Delete old main image file
                    $oldImagePath = ROOT_PATH . '/' . $existingMain['image_url'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    // Update existing main image
                    $stmt = $db->prepare("UPDATE product_images SET image_url = ? WHERE id = ?");
                    $stmt->execute([$imageUrl, $existingMain['id']]);
                } else {
                    // Insert new main image
                    $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 1)");
                    $stmt->execute([$productId, $imageUrl]);
                }
            }
        }
        
        // Handle alternative images
        for ($i = 1; $i <= 3; $i++) {
            $fieldName = 'alt_image_' . $i;
            if (isset($files[$fieldName]) && $files[$fieldName]['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->uploadImage($files[$fieldName], $uploadDir);
                if ($imageUrl) {
                    // Just insert alternative images (user can delete unwanted ones)
                    $stmt = $db->prepare("INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 0)");
                    $stmt->execute([$productId, $imageUrl]);
                }
            }
        }
    }
    
    /**
     * Upload single image
     */
    private function uploadImage($file, $uploadDir) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'storage/uploads/' . $filename;
        }
        
        return false;
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
            
            // Convert specifications array to JSON
            $specifications = [];
            if (!empty($_POST['spec_keys']) && is_array($_POST['spec_keys'])) {
                foreach ($_POST['spec_keys'] as $index => $key) {
                    if (!empty($key) && !empty($_POST['spec_values'][$index])) {
                        $specifications[$key] = $_POST['spec_values'][$index];
                    }
                }
            }
            
            // Generate slug from product name
            $slug = slugify($_POST['name'] ?? '');
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'slug' => $slug,
                'sku' => $_POST['sku'] ?? '',
                'brand' => $_POST['brand'] ?? null,
                'price' => $_POST['price'] ?? 0,
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'description' => $_POST['description'] ?? '',
                'specifications' => !empty($specifications) ? json_encode($specifications) : null
            ];
            
            $updated = $productModel->update($id, $data);
            
            if ($updated) {
                // Handle image deletions if requested
                if (!empty($_POST['delete_images'])) {
                    $imageIdsToDelete = json_decode($_POST['delete_images'], true);
                    if (is_array($imageIdsToDelete)) {
                        foreach ($imageIdsToDelete as $imageId) {
                            $this->deleteProductImageById($imageId);
                        }
                    }
                }
                
                // Handle new image uploads if provided
                $filesUploaded = false;
                if (!empty($_FILES)) {
                    // Check if any files were actually uploaded (not just empty fields)
                    foreach ($_FILES as $key => $file) {
                        if (is_array($file['error'])) {
                            foreach ($file['error'] as $error) {
                                if ($error === UPLOAD_ERR_OK) {
                                    $filesUploaded = true;
                                    break 2;
                                }
                            }
                        } else if ($file['error'] === UPLOAD_ERR_OK) {
                            $filesUploaded = true;
                            break;
                        }
                    }
                    
                    if ($filesUploaded) {
                        $this->handleProductImages($id, $_FILES);
                    }
                }
                
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update product']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
    
    /**
     * Delete individual product image (API endpoint)
     */
    public function deleteProductImage($imageId) {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        $result = $this->deleteProductImageById($imageId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
    }
    
    /**
     * Helper method to delete product image by ID (used by both API and update operations)
     */
    private function deleteProductImageById($imageId) {
        $database = Database::getInstance();
        $db = $database->getConnection();
        
        // Get image details
        $stmt = $db->prepare("SELECT * FROM product_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if (!$image) {
            return false;
        }
        
        // Delete physical file
        $filePath = ROOT_PATH . '/' . $image['image_url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $deleteStmt = $db->prepare("DELETE FROM product_images WHERE id = ?");
        return $deleteStmt->execute([$imageId]);
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
