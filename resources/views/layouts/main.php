<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? escape($title) : APP_NAME; ?></title>
    <meta name="description" content="<?php echo isset($metaDescription) ? escape($metaDescription) : META_DESCRIPTION; ?>">
    <meta name="keywords" content="<?php echo META_KEYWORDS; ?>">
    <link rel="icon" type="image/x-icon" href="/devicesvn/assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo asset('css/main-layout.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/auth-modal.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/home.css'); ?>">
</head>
<body>
    <header class="header">
        <div class="top-bar bg-dark text-white py-2">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <small><i class="fas fa-phone"></i> 1900-xxxx</small>
                        <small class="ms-3"><i class="fas fa-envelope"></i> info@devicesvn.com</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <small></i>Welcome, <?php echo escape($_SESSION['user_name']); ?>!</small>
                        <?php else: ?>
                            <a href="javascript:void(0)" onclick="openLoginModal()" class="text-white"><small>Login</small></a>
                            <a href="javascript:void(0)" onclick="openRegisterModal()" class="text-white ms-3"><small>Register</small></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="main-header py-3 border-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <a href="<?php echo url(''); ?>" class="logo d-flex align-items-center text-decoration-none">
                            <img src="<?php echo asset('assets/logo.png'); ?>" alt="DevicesVN" class="logo-icon me-2" style="height: 40px;" onerror="this.style.display='none'">
                            <span class="logo-text">DevicesVN</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form action="<?php echo url('search'); ?>" method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" name="q" class="form-control" placeholder="Search products..." 
                                       id="searchInput" autocomplete="off" value="<?php echo isset($_GET['q']) ? escape($_GET['q']) : ''; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div id="searchSuggestions" class="search-suggestions"></div>
                        </form>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="<?= url('cart') ?>" class="btn btn-outline-primary me-2 position-relative">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount" style="display: none;">
                                0
                            </span>
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown d-inline-block">
                                <a href="#" class="text-decoration-none" id="userAvatarDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2F1067, #151C32); color: white; border-radius: 50%; font-weight: 600; font-size: 16px;" title="<?php echo escape($_SESSION['user_name']); ?>">
                                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo url('dashboard'); ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo url('logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url(''); ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('products'); ?>">Products</a>
                        </li>
                        
                        <!-- Dynamic Categories Dropdown -->
                        <?php if (!empty($navCategories)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="categoriesDropdown">
                                Categories
                            </a>
                            <ul class="dropdown-menu" id="categoriesMenu">
                                <?php foreach ($navCategories as $category): ?>
                                    <?php if (!empty($category['children'])): ?>
                                        <!-- Parent with children - nested dropdown -->
                                        <li class="dropdown dropend">
                                            <a class="dropdown-item parent-category-link" href="<?php echo url('products/category/' . $category['slug']); ?>">
                                                <?php echo escape($category['name']); ?>
                                                <i class="fas fa-chevron-right float-end ms-2 nested-toggle-icon"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <?php foreach ($category['children'] as $child): ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo url('products/category/' . $child['slug']); ?>">
                                                            <?php echo escape($child['name']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                    <?php else: ?>
                                        <!-- Parent without children - simple link -->
                                        <li>
                                            <a class="dropdown-item" href="<?php echo url('products/category/' . $category['slug']); ?>">
                                                <?php echo escape($category['name']); ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('about'); ?>">About</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content py-4">
        <div class="container">
            <?php
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'flash_') === 0) {
                    $flash = $value;
                    unset($_SESSION[$key]);
                    ?>
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
                        <?php echo escape($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php
                    break;
                }
            }
            ?>
            
            <?php echo $content; ?>
        </div>
    </main>

    <footer class="footer bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>DevicesVN</h5>
                    <p>Your trusted store for devices in Vietnam</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo url(''); ?>" class="text-white-50">Home</a></li>
                        <li><a href="<?php echo url('products'); ?>" class="text-white-50">Products</a></li>
                        <li><a href="<?php echo url('about'); ?>" class="text-white-50">About</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p><i class="fas fa-phone"></i> 1900-xxxx<br>
                    <i class="fas fa-envelope"></i> info@devicesvn.com</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> DevicesVN. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/auth-modal.js'); ?>"></script>
    
    <script>
    // Update cart count on page load
    function updateCartCount() {
        fetch('<?= url('cart/count') ?>')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cartCount');
                if (cartBadge && data.count > 0) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.display = 'inline-block';
                } else if (cartBadge) {
                    cartBadge.style.display = 'none';
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
    }
    
    // Update on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateCartCount);
    } else {
        updateCartCount();
    }
    
    // Listen for cart update events
    window.addEventListener('cartUpdated', updateCartCount);
    </script>
    
    <?php require_once __DIR__ . '/modals.php'; ?>
</body>
</html>
