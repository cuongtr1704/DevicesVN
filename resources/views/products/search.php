<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">
                <i class="fas fa-search me-2"></i>Search Results for "<?= escape($query) ?>"
            </h2>
            <p class="text-muted">
                Found <?= $totalResults ?> product<?= $totalResults != 1 ? 's' : '' ?>
            </p>
        </div>
    </div>

    <?php if (empty($products)): ?>
        <!-- No Results -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-5x text-muted mb-4"></i>
                    <h3>No products found</h3>
                    <p class="text-muted mb-4">We couldn't find any products matching "<?= escape($query) ?>"</p>
                    <p class="text-muted mb-4">Try:</p>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Checking your spelling</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Using more general keywords</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Browsing our categories</li>
                    </ul>
                    <a href="<?= url('products') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-th me-2"></i>Browse All Products
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Products Grid -->
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <a href="<?= url('products/' . $product['slug']) ?>">
                            <?php if (isOnSale($product)): ?>
                                <div class="badge-sale">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</div>
                            <?php endif; ?>
                            
                            <img src="<?= $product['main_image'] ? asset($product['main_image']) : asset('images/no-image.png') ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?= escape($product['name']) ?>">
                        </a>
                        <div class="card-body">
                            <?php if (!empty($product['brand'])): ?>
                                <p class="text-muted small mb-1"><?= escape($product['brand']) ?></p>
                            <?php endif; ?>
                            
                            <h5 class="card-title">
                                <a href="<?= url('products/' . $product['slug']) ?>" class="text-decoration-none text-dark">
                                    <?= escape($product['name']) ?>
                                </a>
                            </h5>
                            
                            <div class="price-section mt-3">
                                <?php if (isOnSale($product)): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-danger fw-bold fs-5"><?= formatPrice($product['sale_price']) ?></span>
                                        <span class="text-decoration-line-through text-muted small"><?= formatPrice($product['price']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="fw-bold fs-5"><?= formatPrice($product['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-3">
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-0 pt-0">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button class="btn btn-primary w-100" onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times me-2"></i>Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Search results pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('search?q=' . urlencode($query) . '&page=' . ($currentPage - 1)) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('search?q=' . urlencode($query) . '&page=' . $i) ?>"><?= $i ?></a>
                            </li>
                        <?php elseif (abs($i - $currentPage) == 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('search?q=' . urlencode($query) . '&page=' . ($currentPage + 1)) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function addToCart(productId) {
    fetch('<?= url('cart/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            window.dispatchEvent(new Event('cartUpdated'));
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

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
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15) !important;
}

.product-image {
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.badge-sale {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    z-index: 1;
}

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
