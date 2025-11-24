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
    
    // ==================== PRODUCT ROUTES ====================
    // All products page
    'products' => ['controller' => 'ProductsController', 'action' => 'index'],
    
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
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'forgot-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],
    
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
    'checkout' => ['controller' => 'CheckoutController', 'action' => 'index'],
    'checkout/process' => ['controller' => 'CheckoutController', 'action' => 'process'],
    'checkout/success' => ['controller' => 'CheckoutController', 'action' => 'success'],
    
    // ==================== STORE LOCATIONS ROUTES ====================
    'stores' => ['controller' => 'StoresController', 'action' => 'index'],
    
    // Single store detail (e.g., /stores/hanoi-store)
    // :slug = store name in URL format
    'stores/:slug' => ['controller' => 'StoresController', 'action' => 'detail'],
    
    // ==================== STATIC PAGE ROUTES ====================
    'privacy-policy' => ['controller' => 'PageController', 'action' => 'privacyPolicy'],
    'terms-of-service' => ['controller' => 'PageController', 'action' => 'termsOfService'],
    'shipping-policy' => ['controller' => 'PageController', 'action' => 'shippingPolicy'],
    'return-policy' => ['controller' => 'PageController', 'action' => 'returnPolicy'],
];
