<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <h2 class="mb-4">
                    <i class="fas fa-chart-line me-2"></i>
                    Welcome back, <?= escape($_SESSION['user_name']) ?>!
                </h2>
                
                <?php if ($userRole === 'admin'): ?>
                    <!-- ADMIN DASHBOARD -->
                    
                    <!-- Statistics Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                    <h4 class="mb-1"><?= number_format($totalRevenue) ?> ₫</h4>
                                    <p class="text-muted mb-0 small">Total Revenue</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1"><?= $totalOrders ?></h4>
                                    <p class="text-muted mb-0 small">Total Orders</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x text-info mb-2"></i>
                                    <h4 class="mb-1"><?= $totalProducts ?></h4>
                                    <p class="text-muted mb-0 small">Total Products</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                                    <h4 class="mb-1"><?= $totalUsers ?></h4>
                                    <p class="text-muted mb-0 small">Total Customers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Revenue Overview (Last 6 Months)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueChart" height="80"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Order Status</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                            <a href="<?= url('dashboard/all-orders') ?>" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recentOrders)): ?>
                                <p class="text-muted text-center py-4">No orders yet</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order</th>
                                                <th>Customer</th>
                                                <th>Items</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOrders as $order): ?>
                                                <tr>
                                                    <td><strong>#<?= escape($order['order_number']) ?></strong></td>
                                                    <td><?= escape($order['customer_name']) ?></td>
                                                    <td><span class="badge bg-secondary"><?= $order['item_count'] ?></span></td>
                                                    <td><strong><?= number_format($order['total_amount']) ?> ₫</strong></td>
                                                    <td>
                                                        <?php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'confirmed' => 'info',
                                                            'shipped' => 'primary',
                                                            'delivered' => 'success',
                                                            'cancelled' => 'danger'
                                                        ];
                                                        $color = $statusColors[$order['status']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($order['status']) ?></span>
                                                    </td>
                                                    <td><small><?= date('M d, Y', strtotime($order['created_at'])) ?></small></td>
                                                    <td>
                                                        <a href="<?= url('orders/detail/' . $order['id']) ?>?from=all-orders" 
                                                           class="btn btn-sm btn-info"
                                                           style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- CUSTOMER DASHBOARD -->
                    
                    <!-- Statistics Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1"><?= $totalOrders ?></h4>
                                    <p class="text-muted mb-0 small">Total Orders</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h4 class="mb-1"><?= $pendingOrders ?></h4>
                                    <p class="text-muted mb-0 small">Pending Orders</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="mb-1"><?= $completedOrders ?></h4>
                                    <p class="text-muted mb-0 small">Completed Orders</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                    <h4 class="mb-1"><?= $wishlistCount ?></h4>
                                    <p class="text-muted mb-0 small">Wishlist Items</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Spent Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <p class="text-muted mb-1"><i class="fas fa-wallet me-2"></i>Total Spent</p>
                                    <h3 class="mb-0 text-primary"><?= number_format($totalSpent) ?> ₫</h3>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="<?= url('products') ?>" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i>Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Status Chart -->
                    <?php if (!empty($statusDistribution)): ?>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>My Order Status</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="customerStatusChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($statusDistribution as $status): ?>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'confirmed' => 'info',
                                            'shipped' => 'primary',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $color = $statusColors[$status['status']] ?? 'secondary';
                                        $percentage = $totalOrders > 0 ? ($status['count'] / $totalOrders) * 100 : 0;
                                        ?>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-capitalize"><?= escape($status['status']) ?></span>
                                                <span><strong><?= $status['count'] ?></strong> orders</span>
                                            </div>
                                            <div class="progress" style="height: 10px;">
                                                <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentage ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Recent Orders -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                            <a href="<?= url('dashboard/orders') ?>" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recentOrders)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                    <h5>No orders yet</h5>
                                    <p class="text-muted">Start shopping to see your orders here</p>
                                    <a href="<?= url('products') ?>" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i>Browse Products
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order Number</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOrders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?= escape($order['order_number']) ?></strong>
                                                    </td>
                                                    <td><span class="badge bg-secondary"><?= $order['item_count'] ?> items</span></td>
                                                    <td><strong class="text-primary"><?= number_format($order['total_amount']) ?> ₫</strong></td>
                                                    <td>
                                                        <?php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'confirmed' => 'info',
                                                            'shipped' => 'primary',
                                                            'delivered' => 'success',
                                                            'cancelled' => 'danger'
                                                        ];
                                                        $color = $statusColors[$order['status']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?= $color ?>"><?= ucfirst($order['status']) ?></span>
                                                    </td>
                                                    <td><small><?= date('M d, Y', strtotime($order['created_at'])) ?></small></td>
                                                    <td>
                                                        <a href="<?= url('orders/detail/' . $order['id']) ?>" 
                                                           class="btn btn-sm btn-info"
                                                           style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<?php if ($userRole === 'admin'): ?>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = <?= json_encode($monthlyRevenue) ?>;

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueData.map(item => {
            const [year, month] = item.month.split('-');
            return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        }),
        datasets: [{
            label: 'Revenue (₫)',
            data: revenueData.map(item => item.revenue),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' ₫';
                    }
                }
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = <?= json_encode($statusDistribution) ?>;

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(13, 110, 253, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
<?php else: ?>
<script>
// Customer Status Chart
<?php if (!empty($statusDistribution)): ?>
const customerStatusCtx = document.getElementById('customerStatusChart').getContext('2d');
const customerStatusData = <?= json_encode($statusDistribution) ?>;

new Chart(customerStatusCtx, {
    type: 'doughnut',
    data: {
        labels: customerStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
        datasets: [{
            data: customerStatusData.map(item => item.count),
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(23, 162, 184, 0.8)',
                'rgba(13, 110, 253, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(220, 53, 69, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
<?php endif; ?>
</script>
<?php endif; ?>

<style>
.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
