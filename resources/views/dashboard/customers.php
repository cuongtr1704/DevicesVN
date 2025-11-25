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
                        <i class="fas fa-users me-2"></i>Manage Customers
                    </h3>
                    <div>
                        <span class="badge bg-primary fs-6 px-3 py-2">
                            Total: <?= count($customers) ?> Customers
                        </span>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th style="width: 120px;">Role</th>
                                        <th style="width: 150px;">Registered</th>
                                        <th style="width: 120px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><?= $customer['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                        <?= strtoupper(substr($customer['full_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= escape($customer['full_name']) ?></strong>
                                                        <br><small class="text-muted"><?= escape($customer['email']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($customer['phone'])): ?>
                                                    <i class="fas fa-phone text-muted me-1"></i><?= escape($customer['phone']) ?>
                                                    <br>
                                                <?php endif; ?>
                                                <?php if (!empty($customer['address'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i><?= escape(substr($customer['address'], 0, 30)) ?>...
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($customer['role'] === 'admin'): ?>
                                                    <span class="badge bg-danger">Admin</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Customer</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= date('M d, Y', strtotime($customer['created_at'])) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-square btn-info me-1" 
                                                        title="View Details"
                                                        onclick="viewCustomer(<?= $customer['id'] ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($customer['role'] !== 'admin'): ?>
                                                    <button class="btn btn-sm btn-square btn-warning" 
                                                            title="Edit"
                                                            onclick="editCustomer(<?= $customer['id'] ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                <?php endif; ?>
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

<!-- Customer Details Modal -->
<div class="modal fade" id="customerDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="customerDetailsContent">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-square {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}
</style>

<script src="<?= asset('js/dashboard.js') ?>"></script>

<script>
function viewCustomer(id) {
    // Placeholder for view functionality
    alert('View customer #' + id + ' details');
}

function editCustomer(id) {
    // Placeholder for edit functionality
    alert('Edit customer #' + id);
}
</script>
