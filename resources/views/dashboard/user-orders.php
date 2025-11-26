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
                    <div>
                        <h2 class="mb-2">
                            <i class="fas fa-shopping-bag me-2"></i><?= escape($user['full_name']) ?>'s Orders
                        </h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-envelope me-2"></i><?= escape($user['email']) ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?= url('dashboard/users') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Search Order</label>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by order number..." 
                                       value="<?= escape($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="shipped" <?= ($_GET['status'] ?? '') === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= ($_GET['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">Sort By</label>
                                <select class="form-select" name="sort">
                                    <option value="newest" <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                    <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                    <option value="amount_high" <?= ($_GET['sort'] ?? '') === 'amount_high' ? 'selected' : '' ?>>Highest Amount</option>
                                    <option value="amount_low" <?= ($_GET['sort'] ?? '') === 'amount_low' ? 'selected' : '' ?>>Lowest Amount</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (empty($orders) && (isset($_GET['search']) || isset($_GET['status']))): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            <a href="<?= url('dashboard/users/user-orders/' . $user['id']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-1"></i>Reset Filters
                            </a>
                        </div>
                    </div>
                <?php elseif (empty($orders)): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders found</h5>
                            <p class="text-muted"><?= escape($user['full_name']) ?> hasn't placed any orders yet.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Orders Table -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50px; font-weight: 600;">#</th>
                                            <th style="min-width: 150px; font-weight: 600;">Order Number</th>
                                            <th style="min-width: 150px; font-weight: 600;">Date</th>
                                            <th style="width: 100px; font-weight: 600;">Items</th>
                                            <th style="min-width: 150px; font-weight: 600;">Total</th>
                                            <th style="width: 130px; font-weight: 600;">Status</th>
                                            <th style="width: 80px; font-weight: 600;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $index => $order): ?>
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
                                            <tr>
                                                <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                                                <td>
                                                    <strong class="text-primary">#<?= escape($order['order_number']) ?></strong>
                                                </td>
                                                <td>
                                                    <div><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                                                    <small class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $order['item_count'] ?? 0 ?> item(s)</span>
                                                </td>
                                                <td>
                                                    <strong><?= number_format($order['total_amount'], 0, ',', '.') ?> ₫</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $statusColor ?> rounded-pill">
                                                        <i class="fas fa-<?= $statusIcon ?> me-1"></i><?= $statusLabel ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= url('orders/detail/' . $order['id']) ?>" 
                                                       class="btn btn-sm btn-primary"
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
                        </div>
                        
                        <!-- Pagination -->
                        <?php 
                        // Build query string for pagination
                        $queryParams = [];
                        if (!empty($_GET['search'])) $queryParams['search'] = $_GET['search'];
                        if (!empty($_GET['status'])) $queryParams['status'] = $_GET['status'];
                        if (!empty($_GET['sort'])) $queryParams['sort'] = $_GET['sort'];
                        
                        function buildPaginationUrl($page, $params) {
                            $params['page'] = $page;
                            return '?' . http_build_query($params);
                        }
                        ?>
                        <?php if ($pagination['total_pages'] > 1): ?>
                            <div class="card-footer bg-white">
                                <nav>
                                    <ul class="pagination justify-content-center mb-0">
                                        <?php if ($pagination['current_page'] > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= buildPaginationUrl($pagination['current_page'] - 1, $queryParams) ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php
                                        $start = max(1, $pagination['current_page'] - 2);
                                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                        
                                        if ($start > 1): ?>
                                            <li class="page-item"><a class="page-link" href="<?= buildPaginationUrl(1, $queryParams) ?>">1</a></li>
                                            <?php if ($start > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = $start; $i <= $end; $i++): ?>
                                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= buildPaginationUrl($i, $queryParams) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($end < $pagination['total_pages']): ?>
                                            <?php if ($end < $pagination['total_pages'] - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item"><a class="page-link" href="<?= buildPaginationUrl($pagination['total_pages'], $queryParams) ?>"><?= $pagination['total_pages'] ?></a></li>
                                        <?php endif; ?>
                                        
                                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= buildPaginationUrl($pagination['current_page'] + 1, $queryParams) ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <div class="text-center text-muted small mt-2">
                                    Showing <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> 
                                    to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']) ?> 
                                    of <?= $pagination['total_items'] ?> orders
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mt-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-0"><?= $pagination['total_items'] ?></h3>
                                    <small class="text-muted">Total Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0"><?= $orderStats['delivered'] ?? 0 ?></h3>
                                    <small class="text-muted">Delivered</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h3 class="mb-0"><?= $orderStats['pending'] ?? 0 ?></h3>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0"><?= number_format($orderStats['total_spent'] ?? 0, 0, ',', '.') ?> ₫</h3>
                                    <small class="text-muted">Total Spent</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
