<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
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

<script src="<?= asset('js/dashboard.js') ?>"></script>
