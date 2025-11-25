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
                        <i class="fas fa-tags me-2"></i>Manage Categories
                    </h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </div>

                <!-- Categories Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th style="width: 150px;">Sort Order</th>
                                        <th style="width: 150px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td>
                                                <strong><?= escape($category['name']) ?></strong>
                                                <?php if (!empty($category['description'])): ?>
                                                    <br><small class="text-muted"><?= escape(substr($category['description'], 0, 60)) ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?= escape($category['slug']) ?></code></td>
                                            <td><?= $category['sort_order'] ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-square btn-warning me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-square btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Save Category</button>
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
