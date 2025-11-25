<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="dashboard-sidebar">
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
                    
                    <?php if (($userRole ?? 'customer') === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/categories') ?>">
                                <i class="fas fa-tags me-2"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/products') ?>">
                                <i class="fas fa-box me-2"></i> Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/customers') ?>">
                                <i class="fas fa-users me-2"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/orders') ?>">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/orders') ?>">
                                <i class="fas fa-shopping-bag me-2"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('dashboard/wishlist') ?>">
                                <i class="fas fa-heart me-2"></i> Wishlist
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
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <h2 class="mb-4">Welcome back, <?= escape($_SESSION['user_name']) ?>!</h2>
                
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #e8f5e9; border-radius: 50%;">
                                    <i class="fas fa-shopping-bag fa-2x text-success"></i>
                                </div>
                                <h3 class="mb-2">0</h3>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #fff3e0; border-radius: 50%;">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                                <h3 class="mb-2">0</h3>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <div class="icon-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #fce4ec; border-radius: 50%;">
                                    <i class="fas fa-heart fa-2x text-danger"></i>
                                </div>
                                <h3 class="mb-2">0</h3>
                                <p class="text-muted mb-0">Wishlist Items</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center py-4">No orders yet. <a href="<?= url('products') ?>">Start shopping</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('logoutLink').addEventListener('click', function(e) {
    e.preventDefault();
    
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = this.href;
    }
});
</script>

<style>
.dashboard-sidebar {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-nav .nav-link {
    color: #333;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.3s;
    margin-bottom: 5px;
}

.dashboard-nav .nav-link:hover {
    background: #f8f9fa;
    color: #2F1067;
}

.dashboard-nav .nav-link.active {
    background: linear-gradient(135deg, #2F1067, #151C32);
    color: white;
}

.dashboard-card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.dashboard-card:hover {
    transform: translateY(-5px);
}

.dashboard-content .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background: white;
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
}
</style>
