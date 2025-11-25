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
                        <i class="fas fa-box me-2"></i>Manage Products
                    </h3>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>

                <!-- Products Table -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 80px;">Image</th>
                                        <th>Product</th>
                                        <th style="width: 120px;">Price</th>
                                        <th style="width: 100px;">Stock</th>
                                        <th style="width: 100px;">Category</th>
                                        <th style="width: 150px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="<?= asset($product['main_image']) ?>" 
                                                     alt="<?= escape($product['name']) ?>"
                                                     class="img-thumbnail"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <strong><?= escape($product['name']) ?></strong>
                                                <br><small class="text-muted">SKU: <?= escape($product['sku']) ?></small>
                                            </td>
                                            <td>
                                                <strong>$<?= number_format($product['price'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($product['stock'] > 10): ?>
                                                    <span class="badge bg-success"><?= $product['stock'] ?></span>
                                                <?php elseif ($product['stock'] > 0): ?>
                                                    <span class="badge bg-warning text-dark"><?= $product['stock'] ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?= escape($product['category_name']) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= url('products/' . $product['id']) ?>" 
                                                   class="btn btn-sm btn-square btn-info me-1" 
                                                   title="View"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="card-footer bg-white border-0">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <!-- Previous -->
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= url('dashboard/products?page=' . ($page - 1)) ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                    
                                    <!-- Pages -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= url('dashboard/products?page=' . $i) ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php elseif (abs($i - $page) == 3): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    
                                    <!-- Next -->
                                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="<?= url('dashboard/products?page=' . ($page + 1)) ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
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
