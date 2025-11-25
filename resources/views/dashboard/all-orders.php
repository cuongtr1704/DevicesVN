<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-all-orders.css') ?>">

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
                    </h2>
                    <div>
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            Total: <?= count($orders) ?> Orders
                        </span>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Confirmed</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Shipped</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Delivered</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Cancelled</a>
                    </li>
                </ul>

                <!-- Orders Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 120px;">Order ID</th>
                                        <th>Customer</th>
                                        <th style="width: 120px;">Total</th>
                                        <th style="width: 140px;">Status</th>
                                        <th style="width: 150px;">Date</th>
                                        <th style="width: 120px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="width: 35px; height: 35px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                                        <?= strtoupper(substr($order['customer_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= escape($order['customer_name']) ?></strong>
                                                        <br><small class="text-muted"><?= escape($order['customer_email']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
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
                                                $color = $statusColors[$order['status']] ?? 'secondary';
                                                ?>
                                                <select class="form-select form-select-sm badge-select" 
                                                        onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)"
                                                        style="background-color: var(--bs-<?= $color ?>); color: white; border: none; font-weight: 500;">
                                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <small><?= date('M d, Y', strtotime($order['created_at'])) ?></small>
                                                <br><small class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= url('orders/detail/' . $order['id']) ?>" 
                                                   class="btn btn-sm btn-square btn-info" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script>
// Set the API URL as a global variable for dashboard-all-orders.js
window.updateOrderStatusUrl = '<?= url("api/orders/update-status") ?>';
</script>
<script src="<?= asset('js/dashboard-all-orders.js') ?>"></script>
