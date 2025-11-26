<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-categories.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-tags me-2"></i>Manage Categories
                    </h2>
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
                                        <th style="width: 80px;" class="text-center">Order</th>
                                        <th style="width: 200px;">Icon Class</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th style="width: 120px;" class="text-center">Products</th>
                                        <th style="width: 150px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= $category['sort_order'] ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($category['icon'])): ?>
                                                    <code class="icon-code"><?= escape($category['icon']) ?></code>
                                                <?php else: ?>
                                                    <span class="text-muted"><em>No icon</em></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= escape($category['name']) ?></strong>
                                                <?php if (!empty($category['description'])): ?>
                                                    <br><small class="text-muted"><?= escape(substr($category['description'], 0, 60)) ?>...</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?= escape($category['slug']) ?></code></td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $category['product_count'] > 0 ? 'primary' : 'secondary' ?>">
                                                    <?= $category['product_count'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-square btn-warning me-1" 
                                                        onclick="editCategory(<?= $category['id'] ?>)" 
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-square btn-danger" 
                                                        onclick="deleteCategory(<?= $category['id'] ?>, '<?= escape($category['name']) ?>', <?= $category['product_count'] ?>)" 
                                                        title="Delete">
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



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h5 class="mb-3">Delete Category?</h5>
                <p class="text-muted mb-0" id="deleteConfirmMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
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
                        <label class="form-label">Icon (Font Awesome)</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i id="addIconPreview" class="fas fa-folder"></i>
                            </span>
                            <input type="text" class="form-control" name="icon" id="addCategoryIcon" 
                                   placeholder="e.g. fas fa-laptop, fas fa-mobile-alt" 
                                   oninput="previewIcon(this.value, 'addIconPreview')">
                        </div>
                        <small class="text-muted">Browse icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0">
                        <small class="text-muted">Leave as 0 to add at the end</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">Save Category</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" name="id" id="editCategoryId">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon (Font Awesome)</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i id="editIconPreview" class="fa-solid fa-icons"></i>
                            </span>
                            <input type="text" class="form-control" name="icon" id="editCategoryIcon" 
                                   placeholder="e.g. fas fa-laptop, fas fa-mobile-alt" 
                                   oninput="previewIcon(this.value, 'editIconPreview')">
                        </div>
                        <small class="text-muted">Browse icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editCategoryDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="editCategorySortOrder" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateCategory()">Update Category</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script>
// Set the categories URL as a global variable for dashboard-categories.js
window.categoriesUrl = '<?= url('dashboard/categories') ?>';
</script>
<script src="<?= asset('js/dashboard-categories.js') ?>"></script>

