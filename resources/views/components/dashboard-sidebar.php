<!-- Mobile Menu Toggle Button - Outside column wrapper -->
<button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay - Outside column wrapper -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar Column -->
<div class="col-md-3 sidebar-column">
    <div class="dashboard-sidebar" id="dashboardSidebar">
        <div class="user-info mb-4">
            <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2F1067, #151C32); color: white; border-radius: 50%; font-weight: 600; font-size: 32px;">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <h5 class="text-center mb-1"><?= escape($_SESSION['user_name']) ?></h5>
            <p class="text-center text-muted small"><?= escape($_SESSION['user_email']) ?></p>
        </div>
        
        <ul class="nav flex-column dashboard-nav">
            <li class="nav-item">
                <a class="nav-link <?= ($activeSection ?? '') === 'overview' ? 'active' : '' ?>" href="<?= url('dashboard') ?>">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeSection ?? '') === 'profile' ? 'active' : '' ?>" href="<?= url('dashboard/profile') ?>">
                    <i class="fas fa-user me-2"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeSection ?? '') === 'orders' ? 'active' : '' ?>" href="<?= url('dashboard/orders') ?>">
                    <i class="fas fa-shopping-bag me-2"></i> My Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeSection ?? '') === 'wishlist' ? 'active' : '' ?>" href="<?= url('dashboard/wishlist') ?>">
                    <i class="fas fa-heart me-2"></i> Wishlist
                </a>
            </li>
            <?php if (($userRole ?? 'customer') === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeSection ?? '') === 'categories' ? 'active' : '' ?>" href="<?= url('dashboard/categories') ?>">
                        <i class="fas fa-tags me-2"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeSection ?? '') === 'products' ? 'active' : '' ?>" href="<?= url('dashboard/products') ?>">
                        <i class="fas fa-box me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeSection ?? '') === 'users' ? 'active' : '' ?>" href="<?= url('dashboard/users') ?>">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activeSection ?? '') === 'all-orders' ? 'active' : '' ?>" href="<?= url('dashboard/all-orders') ?>">
                        <i class="fas fa-shopping-cart me-2"></i> All Orders
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="<?= url('logout') ?>" id="logoutLink">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
