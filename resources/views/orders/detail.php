<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>Order Details
                </h2>
                <?php
                // Determine the back URL from breadcrumbs
                $backUrl = url('dashboard/orders');
                $backLabel = 'Back to Orders';
                if (isset($breadcrumbs) && count($breadcrumbs) >= 2) {
                    $backUrl = $breadcrumbs[count($breadcrumbs) - 2]['url'];
                    $backLabel = 'Back to ' . $breadcrumbs[count($breadcrumbs) - 2]['label'];
                }
                ?>
                <a href="<?= $backUrl ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i><?= $backLabel ?>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information Card -->
        <div class="col-lg-8 mb-4">
            <!-- Order Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h4 class="mb-3 text-primary">Order #<?= escape($order['order_number']) ?></h4>
                            <p class="mb-2">
                                <i class="fas fa-calendar text-muted me-2"></i>
                                <strong>Order Date:</strong> 
                                <?= date('F d, Y', strtotime($order['created_at'])) ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <strong>Time:</strong> 
                                <?= date('h:i A', strtotime($order['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
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
                            $statusIcons = [
                                'pending' => 'clock',
                                'confirmed' => 'check-circle',
                                'shipped' => 'truck',
                                'delivered' => 'check-double',
                                'cancelled' => 'times-circle'
                            ];
                            $statusColor = $statusColors[$order['status']] ?? 'secondary';
                            $statusLabel = $statusLabels[$order['status']] ?? ucfirst($order['status']);
                            $statusIcon = $statusIcons[$order['status']] ?? 'info-circle';
                            ?>
                            <div class="mb-3">
                                <span class="badge bg-<?= $statusColor ?> px-4 py-3" style="font-size: 1.1rem;">
                                    <i class="fas fa-<?= $statusIcon ?> me-2"></i><?= $statusLabel ?>
                                </span>
                            </div>
                            <?php if (($userRole ?? 'customer') === 'admin'): ?>
                                <!-- Admin Status Update -->
                                <div class="status-update-section">
                                    <label class="status-update-label mb-2">
                                        <i class="fas fa-edit me-1"></i>Update Status
                                    </label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <select id="orderStatus" class="form-select status-select">
                                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <button class="btn btn-update-status" onclick="updateOrderStatus(<?= $order['id'] ?>)">
                                            <i class="fas fa-check me-1"></i>Update
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($order['status'] === 'pending'): ?>
                                <button class="btn btn-danger" onclick="cancelOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-ban me-2"></i>Cancel Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th style="width: 120px;" class="text-center">Quantity</th>
                                    <th style="width: 150px;" class="text-end">Price</th>
                                    <th style="width: 150px;" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <h6 class="mb-0"><?= escape($item['product_name']) ?></h6>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-secondary px-3 py-2"><?= $item['quantity'] ?></span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <strong><?= number_format($item['product_price']) ?> ₫</strong>
                                        </td>
                                        <td class="align-middle text-end">
                                            <strong class="text-primary"><?= number_format($item['subtotal']) ?> ₫</strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast me-2"></i>Shipping Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Full Name</label>
                            <p class="mb-0 fw-bold"><?= escape($order['shipping_name']) ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small mb-1">Phone Number</label>
                            <p class="mb-0 fw-bold">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                <?= escape($order['shipping_phone']) ?>
                            </p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small mb-1">Delivery Address</label>
                            <p class="mb-0 fw-bold">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                <?= escape($order['shipping_address']) ?>
                            </p>
                        </div>
                        <?php if (!empty($order['notes'])): ?>
                            <div class="col-12">
                                <label class="text-muted small mb-1">Notes</label>
                                <p class="mb-0"><?= nl2br(escape($order['notes'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Subtotal</span>
                        <strong><?= number_format($order['total_amount']) ?> ₫</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Shipping Fee</span>
                        <strong class="text-success">Free</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Tax</span>
                        <strong>Included</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <h5 class="mb-0">Total</h5>
                        <h4 class="mb-0 text-primary"><?= number_format($order['total_amount']) ?> ₫</h4>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 p-4">
                    <div class="payment-method">
                        <small class="text-muted d-block mb-2">Payment Method</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-money-bill-wave text-success me-2" style="font-size: 1.5rem;"></i>
                            <strong>Cash on Delivery</strong>
                        </div>
                    </div>
                </div>
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
let currentOrderId = null;

function cancelOrder(orderId) {
    currentOrderId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

document.getElementById('confirmCancelBtn').addEventListener('click', function() {
    if (!currentOrderId) return;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    modal.hide();
    
    fetch('<?= url('orders/cancel') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'order_id=' + currentOrderId
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
    
    currentOrderId = null;
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

<style>
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: 2px solid #f0f0f0;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.badge {
    font-weight: 600;
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

.payment-method {
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 2px dashed #dee2e6;
}

.alert {
    border-radius: 10px;
}

.sticky-top {
    z-index: 1;
}

/* Status Update Section Styling */
.status-update-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 16px;
    border-radius: 10px;
    border: 2px solid #dee2e6;
    margin-top: 16px;
}

.status-update-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    display: block;
    margin-bottom: 0;
}

.status-select {
    border: 2px solid #ced4da;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
    min-width: 140px;
}

.status-select:focus {
    border-color: #6c63ff;
    box-shadow: 0 0 0 0.2rem rgba(108, 99, 255, 0.15);
}

.btn-update-status {
    background: linear-gradient(135deg, #6c63ff 0%, #5a52d5 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-update-status:hover {
    background: linear-gradient(135deg, #5a52d5 0%, #4840b0 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
    color: white;
}

.btn-update-status:active {
    transform: translateY(0);
}

@media (max-width: 768px) {
    .table {
        font-size: 0.9rem;
    }
    
    .sticky-top {
        position: relative !important;
    }
}
</style>

<script>
function updateOrderStatus(orderId) {
    const status = document.getElementById('orderStatus').value;
    
    fetch(`<?= url('orders/update-status/') ?>${orderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Order status updated successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'An error occurred. Please try again.');
    });
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type === 'success' ? 'success' : 'error'}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'times-circle'} me-2"></i>
        <span>${message}</span>
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
