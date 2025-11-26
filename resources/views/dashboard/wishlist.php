<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
<link rel="stylesheet" href="<?= asset('css/dashboard-wishlist.css') ?>">

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../components/dashboard-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="fas fa-heart me-2"></i>My Wishlist
                        <?php if (isset($totalItems) && $totalItems > 0): ?>
                            <span class="badge bg-primary fs-6 ms-2"><?= $totalItems ?></span>
                        <?php endif; ?>
                    </h2>
                    <a href="<?= url('products') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Browse Products
                    </a>
                </div>
                
                <!-- Search -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <form method="GET" action="<?= url('dashboard/wishlist') ?>" class="row g-2 align-items-end">
                            <div class="col-md-10">
                                <label class="form-label small mb-1">
                                    <i class="fas fa-search me-1"></i>Search Products
                                </label>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search for products in your wishlist..." 
                                       value="<?= escape($search ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if (!empty($search) && empty($items)): ?>
                    <!-- No Results State -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No products found</h5>
                            <p class="text-muted">No items match your search criteria.</p>
                            <a href="<?= url('dashboard/wishlist') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-1"></i>View All
                            </a>
                        </div>
                    </div>
                <?php elseif (empty($items)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-heart-broken"></i>
                        </div>
                        <h3>Your wishlist is empty</h3>
                        <p>Start adding products you love to your wishlist!</p>
                        <a href="<?= url('products') ?>" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Browse Products
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($items as $item): ?>
                            <div class="col-md-6 col-lg-4" id="wishlist-item-<?= $item['product_id'] ?>">
                                <div class="wishlist-card">
                                    <button class="remove-wishlist-btn" onclick="removeFromWishlist(<?= $item['product_id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    
                                    <a href="<?= url('products/' . $item['slug']) ?>">
                                        <div class="wishlist-image">
                                            <img src="<?= asset($item['main_image']) ?>" 
                                                 alt="<?= escape($item['name']) ?>">
                                            <?php if ($item['sale_price']): ?>
                                                <span class="sale-badge">Sale</span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                    
                                    <div class="wishlist-details">
                                        <h5>
                                            <a href="<?= url('products/' . $item['slug']) ?>">
                                                <?= escape($item['name']) ?>
                                            </a>
                                        </h5>
                                        
                                        <div class="price-section">
                                            <?php if ($item['sale_price']): ?>
                                                <span class="sale-price"><?= number_format($item['sale_price']) ?> ₫</span>
                                                <span class="original-price"><?= number_format($item['price']) ?> ₫</span>
                                            <?php else: ?>
                                                <span class="current-price"><?= number_format($item['price']) ?> ₫</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="stock-status">
                                            <?php if ($item['stock_quantity'] > 0): ?>
                                                <span class="in-stock">
                                                    <i class="fas fa-check-circle me-1"></i>In Stock
                                                </span>
                                            <?php else: ?>
                                                <span class="out-of-stock">
                                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="wishlist-actions">
                                            <?php if ($item['stock_quantity'] > 0): ?>
                                                <button class="btn btn-primary btn-sm w-100" onclick="addToCart(<?= $item['product_id'] ?>)">
                                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="fas fa-ban me-2"></i>Out of Stock
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <?php
                        function buildWishlistPaginationUrl($page, $search) {
                            $params = ['page' => $page];
                            if (!empty($search)) $params['search'] = $search;
                            return url('dashboard/wishlist') . '?' . http_build_query($params);
                        }
                        ?>
                        <nav aria-label="Wishlist pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildWishlistPaginationUrl($currentPage - 1, $search) ?>">
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
                                        <a class="page-link" href="<?= buildWishlistPaginationUrl(1, $search) ?>">1</a>
                                    </li>
                                    <?php if ($start > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= buildWishlistPaginationUrl($i, $search) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($end < $totalPages): ?>
                                    <?php if ($end < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildWishlistPaginationUrl($totalPages, $search) ?>"><?= $totalPages ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildWishlistPaginationUrl($currentPage + 1, $search) ?>">
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

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                    Confirm Removal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to remove this item from your wishlist?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                    <i class="fas fa-trash me-2"></i>Remove
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/dashboard.js') ?>"></script>
<script>
// Set the URLs as global variables for dashboard-wishlist.js
window.removeWishlistUrl = '<?= url('wishlist/remove') ?>';
window.addToCartUrl = '<?= url('cart/add') ?>';
</script>
<script src="<?= asset('js/dashboard-wishlist.js') ?>"></script>
