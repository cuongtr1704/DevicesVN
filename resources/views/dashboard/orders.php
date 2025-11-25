<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>My Orders
                    </h3>
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

<script>
let pendingCancelId = null;

function cancelOrder(orderId) {
    pendingCancelId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

document.getElementById('confirmCancelBtn').addEventListener('click', function() {
    if (!pendingCancelId) return;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    modal.hide();
    
    fetch('<?= url('orders/cancel') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'order_id=' + pendingCancelId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
    
    pendingCancelId = null;
});

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type === 'success' ? 'success' : 'error'}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<script src="<?= asset('js/dashboard.js') ?>"></script>

<style>
.order-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}

.order-icon-col {
    padding-right: 0;
}

.order-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    background: linear-gradient(135deg, #2F1067, #151C32);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
    margin-right:6px;
}

.order-number {
    font-size: 1rem;
    color: #2F1067;
}

.order-info-item strong {
    font-size: 0.95rem;
    display: block;
}

.badge-status {
    font-weight: 600;
    font-size: 0.8rem;
    padding: 6px 14px;
    border-radius: 20px;
    display: inline-block;
}

.order-actions {
    display: flex;
    gap: 6px;
    justify-content: flex-end;
}

@media (max-width: 991px) {
    .order-actions {
        justify-content: flex-start;
    }
}

@media (min-width: 992px) {
    .order-actions {
        justify-content: flex-end !important;
    }
}

.btn-square {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    border: none;
}

.btn-view {
    background: linear-gradient(135deg, #2F1067, #151C32);
    color: white;
}

.btn-view:hover {
    background: linear-gradient(135deg, #1a0a3d, #0a0e19);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(47, 16, 103, 0.4);
}

.btn-cancel {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
}

.empty-state {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.user-info .avatar-circle {
    transition: all 0.3s ease;
}

.user-info .avatar-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(47, 16, 103, 0.3);
}

.custom-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 16px 24px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    font-weight: 500;
    display: flex;
    align-items: center;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
    min-width: 300px;
}

.custom-notification.show {
    opacity: 1;
    transform: translateX(0);
}

.custom-notification.success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-left: 4px solid #28a745;
}

.custom-notification.error {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    padding: 20px 24px;
}

.modal-body {
    padding: 20px 24px;
    font-size: 1rem;
}

.modal-footer {
    padding: 16px 24px;
}

@media (max-width: 768px) {
    .order-card .card-body {
        padding: 1rem !important;
    }
    
    .order-icon-col {
        display: none;
    }
    
    .order-number {
        font-size: 0.9rem;
    }
    
    .order-info-item strong {
        font-size: 0.85rem;
    }
    
    .order-info-item small {
        font-size: 0.75rem;
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 5px 12px;
    }
    
    .order-actions {
        gap: 8px;
        justify-content: flex-start;
    }
    
    .btn-square {
        width: 34px;
        height: 34px;
        font-size: 13px;
    }
    
    .dashboard-content h3 {
        font-size: 1.25rem;
    }
    
    .btn-outline-primary {
        font-size: 0.85rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
