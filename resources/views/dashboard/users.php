<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-users.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-users me-2"></i>Manage Users
                        <?php if (isset($totalUsers) && $totalUsers > 0): ?>
                            <span class="badge bg-primary fs-6 ms-2"><?= $totalUsers ?></span>
                        <?php endif; ?>
                    </h2>
                </div>

                <!-- Search and Filter -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <form method="GET" action="<?= url('dashboard/users') ?>" class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small mb-1">
                                    <i class="fas fa-search me-1"></i>Search Users
                                </label>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name or email..." 
                                       value="<?= escape($search ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">
                                    <i class="fas fa-filter me-1"></i>Role
                                </label>
                                <select class="form-select" name="role">
                                    <option value="">All Roles</option>
                                    <option value="admin" <?= ($selectedRole ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="customer" <?= ($selectedRole ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1">
                                    <i class="fas fa-sort me-1"></i>Sort By
                                </label>
                                <select class="form-select" name="sort">
                                    <option value="newest" <?= ($sort ?? 'newest') === 'newest' ? 'selected' : '' ?>>Newest First</option>
                                    <option value="oldest" <?= ($sort ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                    <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                                    <option value="name_desc" <?= ($sort ?? '') === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ((!empty($search) || !empty($selectedRole)) && empty($users)): ?>
                    <!-- No Results State -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No users found</h5>
                            <p class="text-muted">Try adjusting your search or filter criteria.</p>
                            <a href="<?= url('dashboard/users') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-1"></i>Reset Filters
                            </a>
                        </div>
                    </div>
                <?php elseif (!empty($users)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px; font-weight: 600;">#</th>
                                        <th style="min-width: 250px; font-weight: 600;">User</th>
                                        <th style="min-width: 200px; font-weight: 600;">Contact</th>
                                        <th style="width: 100px; font-weight: 600;">Role</th>
                                        <th style="width: 130px; font-weight: 600;">Registered</th>
                                        <th style="width: 100px; font-weight: 600;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $index => $user): ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; flex-shrink: 0;">
                                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= escape($user['full_name']) ?></div>
                                                        <small class="text-muted"><?= escape($user['email']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if (!empty($user['phone'])): ?>
                                                    <div class="mb-1">
                                                        <i class="fas fa-phone text-primary me-2"></i>
                                                        <span class="text-dark"><?= escape($user['phone']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($user['address'])): ?>
                                                    <div>
                                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                        <small class="text-muted"><?= escape(substr($user['address'], 0, 35)) ?><?= strlen($user['address']) > 35 ? '...' : '' ?></small>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-2">Admin</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary rounded-pill px-3 py-2">User</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="d-grid gap-1" style="grid-template-columns: repeat(2, 1fr);">
                                                    <button class="btn btn-sm btn-info" 
                                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                            title="View Details"
                                                            onclick="viewUserDetails(<?= $user['id'] ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary" 
                                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                            title="View Orders"
                                                            onclick="viewUserOrders(<?= $user['id'] ?>)">
                                                        <i class="fas fa-shopping-bag"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning" 
                                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                            title="Edit Role"
                                                            onclick="editUserRole(<?= $user['id'] ?>, '<?= escape($user['full_name']) ?>', '<?= $user['role'] ?>')">
                                                        <i class="fas fa-user-tag"></i>
                                                    </button>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button class="btn btn-sm btn-danger" 
                                                                style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                                                title="Delete User"
                                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= escape($user['full_name']) ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <div style="width: 32px; height: 32px;"></div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                    <?php
                    function buildUsersPaginationUrl($page, $search, $role, $sort) {
                        $params = ['page' => $page];
                        if (!empty($search)) $params['search'] = $search;
                        if (!empty($role)) $params['role'] = $role;
                        if (!empty($sort) && $sort !== 'newest') $params['sort'] = $sort;
                        return url('dashboard/users') . '?' . http_build_query($params);
                    }
                    ?>
                    <nav aria-label="Users pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= buildUsersPaginationUrl($currentPage - 1, $search, $selectedRole, $sort) ?>">
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
                                    <a class="page-link" href="<?= buildUsersPaginationUrl(1, $search, $selectedRole, $sort) ?>">1</a>
                                </li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= buildUsersPaginationUrl($i, $search, $selectedRole, $sort) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end < $totalPages): ?>
                                <?php if ($end < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= buildUsersPaginationUrl($totalPages, $search, $selectedRole, $sort) ?>"><?= $totalPages ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= buildUsersPaginationUrl($currentPage + 1, $search, $selectedRole, $sort) ?>">
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

<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-circle me-2"></i>User Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-user-tag me-2"></i>Edit User Role
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editRoleForm">
                    <input type="hidden" id="editUserId" name="user_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <select class="form-select" id="editUserRole" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="saveUserRole()">
                    <i class="fas fa-save me-1"></i>Update Role
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Delete User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteUserId">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p class="mb-0">
                    Are you sure you want to delete user <strong id="deleteUserName"></strong>?
                </p>
                <p class="text-muted small mt-2">
                    This will also delete all their orders and associated data.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">
                    <i class="fas fa-trash me-1"></i>Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script src="<?= asset('js/dashboard-users.js') ?>"></script>
