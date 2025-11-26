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
                        <i class="fas fa-shopping-cart me-2"></i>All Orders
                        <span class="badge bg-primary ms-2"><?= $totalOrders ?></span>
                    </h2>
                </div>

                <!-- Search and Filter Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="<?= url('dashboard/all-orders') ?>" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-search me-1"></i>Search
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="search" 
                                           placeholder="Order #, customer name or email..." 
                                           value="<?= escape($search) ?>">
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-filter me-1"></i>Status
                                    </label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" <?= $selectedStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $selectedStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="shipped" <?= $selectedStatus === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="delivered" <?= $selectedStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $selectedStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="fas fa-sort me-1"></i>Sort By
                                    </label>
                                    <select class="form-select" name="sort">
                                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                        <option value="amount_high" <?= $sort === 'amount_high' ? 'selected' : '' ?>>Amount (High to Low)</option>
                                        <option value="amount_low" <?= $sort === 'amount_low' ? 'selected' : '' ?>>Amount (Low to High)</option>
                                        <option value="customer" <?= $sort === 'customer' ? 'selected' : '' ?>>Customer Name</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (empty($orders)): ?>
                    <?php if (!empty($search) || !empty($selectedStatus)): ?>
                        <!-- No Results State -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders found</h5>
                                <p class="text-muted">Try adjusting your search or filter criteria.</p>
                                <a href="<?= url('dashboard/all-orders') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-redo me-1"></i>Reset Filters
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No orders yet</h5>
                                <p class="text-muted">Orders will appear here once customers start placing them.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Orders Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 10%;">#</th>
                                            <th style="width: 15%;">Order Number</th>
                                            <th style="width: 25%;">Customer</th>
                                            <th style="width: 10%;">Items</th>
                                            <th style="width: 12%;">Total</th>
                                            <th style="width: 13%;">Status</th>
                                            <th style="width: 10%;">Date</th>
                                            <th style="width: 5%;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = ($currentPage - 1) * 10 + 1;
                                        foreach ($orders as $order): 
                                        ?>
                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td>
                                                    <strong><?= escape($order['order_number']) ?></strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-circle me-2" style="width: 35px; height: 35px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                                            <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                                                        </div>
                                                        <div>
                                                            <div><strong><?= escape($order['customer_name']) ?></strong></div>
                                                            <small class="text-muted"><?= escape($order['customer_email']) ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $order['item_count'] ?> item<?= $order['item_count'] > 1 ? 's' : '' ?></span>
                                                </td>
                                                <td>
                                                    <strong class="text-primary"><?= number_format($order['total_amount']) ?> ₫</strong>
                                                </td>
                                                <td>
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
                                                    <span class="badge bg-<?= $statusColor ?>"><?= $statusLabel ?></span>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y', strtotime($order['created_at'])) ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-column gap-1">
                                                        <a href="<?= url('orders/detail/' . $order['id']) ?>?from=all-orders&search=<?= urlencode($search) ?>&status=<?= urlencode($selectedStatus) ?>&sort=<?= urlencode($sort) ?>&page=<?= $currentPage ?>" 
                                                           class="btn btn-sm btn-info" 
                                                           style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-warning" 
                                                                style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#statusModal<?= $order['id'] ?>" 
                                                                title="Edit Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <!-- Status Change Modal -->
                                            <div class="modal fade" id="statusModal<?= $order['id'] ?>" tabindex="-1" aria-labelledby="statusModalLabel<?= $order['id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="statusModalLabel<?= $order['id'] ?>">
                                                                <i class="fas fa-edit me-2"></i>Change Order Status
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="mb-3"><strong>Order #<?= escape($order['order_number']) ?></strong></p>
                                                            <p class="text-muted mb-3">Current Status: <span class="badge bg-<?= $statusColor ?>"><?= $statusLabel ?></span></p>
                                                            <div class="mb-3">
                                                                <label for="newStatus<?= $order['id'] ?>" class="form-label">New Status</label>
                                                                <select class="form-select" id="newStatus<?= $order['id'] ?>">
                                                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                    <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="button" class="btn btn-primary" onclick="updateOrderStatus(<?= $order['id'] ?>, document.getElementById('newStatus<?= $order['id'] ?>').value, 'statusModal<?= $order['id'] ?>')">
                                                                <i class="fas fa-save me-1"></i>Update Status
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <?php
                        function buildAllOrdersPaginationUrl($page, $search, $status, $sort) {
                            $params = ['page' => $page];
                            if (!empty($search)) $params['search'] = $search;
                            if (!empty($status)) $params['status'] = $status;
                            if (!empty($sort) && $sort !== 'newest') $params['sort'] = $sort;
                            return url('dashboard/all-orders') . '?' . http_build_query($params);
                        }
                        ?>
                        <nav aria-label="Orders pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildAllOrdersPaginationUrl($currentPage - 1, $search, $selectedStatus, $sort) ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                                
                                if ($start > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildAllOrdersPaginationUrl(1, $search, $selectedStatus, $sort) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= buildAllOrdersPaginationUrl($i, $search, $selectedStatus, $sort) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($end < $totalPages): ?>
                                    <?php if ($end < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildAllOrdersPaginationUrl($totalPages, $search, $selectedStatus, $sort) ?>"><?= $totalPages ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildAllOrdersPaginationUrl($currentPage + 1, $search, $selectedStatus, $sort) ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script>
function updateOrderStatus(orderId, newStatus, modalId) {
    // Build the correct URL with orderId
    const updateUrl = '<?= url("orders/update-status/") ?>' + orderId;
    
    fetch(updateUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close the modal
            if (modalId) {
                const modalElement = document.getElementById(modalId);
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
            // Show success notification
            showNotification('Order status updated successfully!', 'success');
            // Reload after short delay to show notification
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to update order status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating the order status', 'error');
    });
}

// Notification system
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
</style>
