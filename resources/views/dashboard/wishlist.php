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
                    <h2 class="mb-0"><i class="fas fa-heart me-2"></i>My Wishlist</h2>
                    <span class="badge bg-primary" style="font-size: 1rem; padding: 8px 16px;">
                        <?= count($items) ?> <?= count($items) === 1 ? 'Item' : 'Items' ?>
                    </span>
                </div>
                
                <?php if (empty($items)): ?>
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
