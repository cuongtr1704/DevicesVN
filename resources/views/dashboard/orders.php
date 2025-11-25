<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-orders.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>My Orders
                    </h2>
                    <a href="<?= url('products') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                    </a>
                </div>

                <?php if (empty($orders)): ?>
                    <!-- Empty State -->
                    <div class="empty-state text-center py-5">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-shopping-bag" style="font-size: 120px; color: #dee2e6;"></i>
                        </div>
                        <h4 class="mb-3">No orders yet</h4>
                        <p class="text-muted mb-4">You haven't placed any orders. Start shopping now!</p>
                        <a href="<?= url('products') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Browse Products
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Orders List -->
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="card border-0 shadow-sm mb-3 order-card">
                                <div class="card-body p-3">
                                    <div class="row align-items-center g-2">
                                        <div class="col-auto order-icon-col">
                                            <div class="order-icon">
                                                <i class="fas fa-shopping-bag"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="col">
                                            <div class="row align-items-center g-3">
                                                <div class="col-lg-3 col-md-4 col-12">
                                                    <div class="order-number mb-1">
                                                        <strong>Order #<?= escape($order['order_number']) ?></strong>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                                    </small>
                                                </div>
                                                
                                                <div class="col-lg-2 col-md-3 col-6">
                                                    <div class="order-info-item">
                                                        <small class="text-muted d-block">Items</small>
                                                        <strong><?= $order['item_count'] ?> item<?= $order['item_count'] > 1 ? 's' : '' ?></strong>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-2 col-md-3 col-6">
                                                    <div class="order-info-item">
                                                        <small class="text-muted d-block">Total</small>
                                                        <strong class="text-primary"><?= number_format($order['total_amount']) ?> ₫</strong>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-2 col-md-4 col-12">
                                                    <?php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'shipped' => 'primary',
                                                        'delivered' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'Pending',
                                                        'confirmed' => 'Confirmed',
                                                        'shipped' => 'Shipped',
                                                        'delivered' => 'Delivered',
                                                        'cancelled' => 'Cancelled'
                                                    ];
                                                    $statusColor = $statusColors[$order['status']] ?? 'secondary';
                                                    $statusLabel = $statusLabels[$order['status']] ?? ucfirst($order['status']);
                                                    ?>
                                                    <span class="badge bg-<?= $statusColor ?> badge-status">
                                                        <?= $statusLabel ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="col-lg-3 col-md-8 col-12">
                                                    <div class="order-actions">
                                                        <a href="<?= url('orders/detail/' . $order['id']) ?>" 
                                                           class="btn btn-square btn-view" 
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($order['status'] === 'pending'): ?>
                                                            <button class="btn btn-square btn-cancel" 
                                                                    onclick="cancelOrder(<?= $order['id'] ?>)" 
                                                                    title="Cancel Order">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                    Cancel Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to cancel this order? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>No, Keep Order
                </button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">
                    <i class="fas fa-ban me-2"></i>Yes, Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script>
// Set the cancel order URL as a global variable for dashboard-orders.js
window.cancelOrderUrl = '<?= url('orders/cancel') ?>';
</script>
<script src="<?= asset('js/dashboard-orders.js') ?>"></script>
