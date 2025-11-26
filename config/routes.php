<?php

/**
 * Route Configuration - DevicesVN E-commerce
 * 
 * Define custom routes here for better URL control
 * Format: 'url-pattern' => ['controller' => 'ControllerName', 'action' => 'methodName']
 * 
 * Note: :id, :slug, :token are parameters that get passed to the controller method
 * - :slug = SEO-friendly URL text (e.g., "dell-xps-13" instead of ID "123")
 * - :id = Numeric identifier (e.g., "5", "100")
 * - :token = Unique token for password reset, verification, etc.
 */

return [
    // ==================== HOME ROUTES ====================
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'home' => ['controller' => 'HomeController', 'action' => 'index'],
    'about' => ['controller' => 'HomeController', 'action' => 'about'],
    'contact' => ['controller' => 'HomeController', 'action' => 'contact'],
    'categories' => ['controller' => 'HomeController', 'action' => 'categories'],
    
    // ==================== PRODUCT ROUTES ====================
    // All products page
    'products' => ['controller' => 'ProductsController', 'action' => 'index'],
    
    // Featured products page
    'products/featured' => ['controller' => 'ProductsController', 'action' => 'featured'],
    
    // Product by category (e.g., /products/category/laptops)
    // :slug = category name in URL format (laptops, phones, gaming-laptops)
    'products/category/:slug' => ['controller' => 'ProductsController', 'action' => 'category'],
    
    // Single product detail (e.g., /products/dell-xps-13)
    // :slug = product name in URL format (dell-xps-13, iphone-15-pro)
    'products/:slug' => ['controller' => 'ProductsController', 'action' => 'detail'],
    
    // ==================== SEARCH ROUTES ====================
    'search' => ['controller' => 'SearchController', 'action' => 'index'],
    'search/suggestions' => ['controller' => 'SearchController', 'action' => 'suggestions'],
    
    // ==================== AUTHENTICATION ROUTES ====================
    // Note: Login/Register now handled via modals, but keep POST endpoints for form submission
    'auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    'auth/register' => ['controller' => 'AuthController', 'action' => 'register'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'forgot-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],
    
    // ==================== DASHBOARD ROUTES ====================
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard/profile' => ['controller' => 'DashboardController', 'action' => 'profile'],
    'dashboard/wishlist' => ['controller' => 'WishlistController', 'action' => 'index'],
    'dashboard/categories' => ['controller' => 'DashboardController', 'action' => 'categories'],
    'dashboard/products' => ['controller' => 'DashboardController', 'action' => 'products'],
    'dashboard/users' => ['controller' => 'DashboardController', 'action' => 'users'],
    'dashboard/users/view/:id' => ['controller' => 'DashboardController', 'action' => 'viewUser'],
    'dashboard/users/orders/:id' => ['controller' => 'DashboardController', 'action' => 'userOrders'],
    'dashboard/users/user-orders/:id' => ['controller' => 'DashboardController', 'action' => 'viewUserOrders'],
    'dashboard/users/update-role/:id' => ['controller' => 'DashboardController', 'action' => 'updateUserRole'],
    'dashboard/users/delete/:id' => ['controller' => 'DashboardController', 'action' => 'deleteUser'],
    'dashboard/all-orders' => ['controller' => 'DashboardController', 'action' => 'allOrders'],
    
    // ==================== WISHLIST ROUTES ====================
    'wishlist/add' => ['controller' => 'WishlistController', 'action' => 'add'],
    'wishlist/remove' => ['controller' => 'WishlistController', 'action' => 'remove'],
    'wishlist/toggle' => ['controller' => 'WishlistController', 'action' => 'toggle'],
    
    // Password reset with token (e.g., /reset-password/abc123xyz)
    // :token = unique token sent to user's email
    'reset-password/:token' => ['controller' => 'AuthController', 'action' => 'resetPassword'],
    
    // ==================== USER ACCOUNT ROUTES ====================
    'account' => ['controller' => 'AccountController', 'action' => 'index'],
    'account/profile' => ['controller' => 'AccountController', 'action' => 'profile'],
    'account/orders' => ['controller' => 'AccountController', 'action' => 'orders'],
    'account/wishlist' => ['controller' => 'AccountController', 'action' => 'wishlist'],
    
    // ==================== CART & CHECKOUT ROUTES ====================
    'cart' => ['controller' => 'CartController', 'action' => 'index'],
    'cart/add' => ['controller' => 'CartController', 'action' => 'add'],
    'cart/update' => ['controller' => 'CartController', 'action' => 'update'],
    'cart/remove' => ['controller' => 'CartController', 'action' => 'remove'],
    'cart/count' => ['controller' => 'CartController', 'action' => 'getCount'],
    
    // ==================== ORDER ROUTES ====================
    'orders' => ['controller' => 'OrderController', 'action' => 'index'],
    'orders/checkout' => ['controller' => 'OrderController', 'action' => 'checkout'],
    'orders/process' => ['controller' => 'OrderController', 'action' => 'process'],
    'orders/detail/:id' => ['controller' => 'OrderController', 'action' => 'detail'],
    'orders/update-status/:id' => ['controller' => 'OrderController', 'action' => 'updateStatus'],
    'orders/cancel' => ['controller' => 'OrderController', 'action' => 'cancel'],
    'dashboard/orders' => ['controller' => 'OrderController', 'action' => 'index'],
    
    'checkout' => ['controller' => 'OrderController', 'action' => 'checkout'],
    'checkout/process' => ['controller' => 'OrderController', 'action' => 'process'],
    
    // ==================== REVIEW ROUTES ====================
    'reviews/add' => ['controller' => 'ReviewController', 'action' => 'add'],
    'reviews/delete' => ['controller' => 'ReviewController', 'action' => 'delete'],
    
    // ==================== STORE LOCATIONS ROUTES ====================
    'stores' => ['controller' => 'StoresController', 'action' => 'index'],
    
    // Single store detail (e.g., /stores/hanoi-store)
    // :slug = store name in URL format
    'stores/:slug' => ['controller' => 'StoresController', 'action' => 'detail'],
    
    // ==================== STORAGE FILE SERVING ====================
    'storage/uploads/:filename' => ['controller' => 'StorageController', 'action' => 'serve'],
    
    // ==================== PRODUCT API ROUTES (ADMIN) ====================
    'dashboard/products/view/:id' => ['controller' => 'DashboardController', 'action' => 'viewProduct'],
    'dashboard/products/add' => ['controller' => 'DashboardController', 'action' => 'addProduct'],
    'dashboard/products/update/:id' => ['controller' => 'DashboardController', 'action' => 'updateProduct'],
    'dashboard/products/delete/:id' => ['controller' => 'DashboardController', 'action' => 'deleteProduct'],
    'dashboard/products/delete-image/:id' => ['controller' => 'DashboardController', 'action' => 'deleteProductImage'],
    'dashboard/categories/list' => ['controller' => 'DashboardController', 'action' => 'getCategories'],
    
    // ==================== STATIC PAGE ROUTES ====================
    'privacy-policy' => ['controller' => 'PageController', 'action' => 'privacyPolicy'],
    'terms-of-service' => ['controller' => 'PageController', 'action' => 'termsOfService'],
    'shipping-policy' => ['controller' => 'PageController', 'action' => 'shippingPolicy'],
    'return-policy' => ['controller' => 'PageController', 'action' => 'returnPolicy'],
];
