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

<script>
function removeFromWishlist(productId) {
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const confirmBtn = document.getElementById('confirmRemoveBtn');
    
    // Remove any existing event listeners
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Show modal
    modal.show();
    
    // Handle confirmation
    newConfirmBtn.addEventListener('click', function() {
        modal.hide();
        
        fetch('<?= url('wishlist/remove') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.getElementById('wishlist-item-' + productId);
                item.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    item.remove();
                    
                    // Check if wishlist is empty
                    const remainingItems = document.querySelectorAll('[id^="wishlist-item-"]').length;
                    if (remainingItems === 0) {
                        location.reload();
                    }
                    
                    // Show success notification
                    showNotification(data.message, 'success');
                }, 300);
            } else {
                showNotification(data.message || 'Failed to remove item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
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
            showNotification('Product added to cart successfully!', 'success');
            // Trigger cart count update
            window.dispatchEvent(new Event('cartUpdated'));
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}
</script>

<style>
.dashboard-sidebar {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: sticky;
    top: 20px;
}

.dashboard-nav .nav-link {
    color: #333;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-bottom: 5px;
    font-weight: 500;
}

.dashboard-nav .nav-link:hover {
    background: #f8f9fa;
    color: #2F1067;
    transform: translateX(5px);
}

.dashboard-nav .nav-link.active {
    background: linear-gradient(135deg, #2F1067, #151C32);
    color: white;
    box-shadow: 0 2px 8px rgba(47, 16, 103, 0.3);
}

.dashboard-content {
    background: white;
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-icon {
    font-size: 120px;
    color: #e9ecef;
    margin-bottom: 30px;
}

.empty-state h3 {
    color: #333;
    font-size: 2rem;
    margin-bottom: 15px;
}

.empty-state p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.wishlist-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
}

.wishlist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.remove-wishlist-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    color: #dc3545;
    font-size: 16px;
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.remove-wishlist-btn:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.wishlist-image {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    background: #f8f9fa;
}

.wishlist-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.wishlist-card:hover .wishlist-image img {
    transform: scale(1.1);
}

.sale-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.wishlist-details {
    padding: 20px;
}

.wishlist-details h5 {
    font-size: 1.1rem;
    margin-bottom: 12px;
    height: 50px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.wishlist-details h5 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.wishlist-details h5 a:hover {
    color: #2F1067;
}

.price-section {
    margin-bottom: 12px;
}

.current-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2F1067;
}

.sale-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #dc3545;
    margin-right: 8px;
}

.original-price {
    font-size: 1rem;
    color: #6c757d;
    text-decoration: line-through;
}

.stock-status {
    margin-bottom: 15px;
    font-size: 0.9rem;
    font-weight: 600;
}

.in-stock {
    color: #28a745;
}

.out-of-stock {
    color: #dc3545;
}

.wishlist-actions .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #2F1067, #151C32);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1a0a3d, #0a0f1f);
    transform: translateY(-2px);
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0;
        transform: scale(0.8);
    }
}

.user-info .avatar-circle {
    transition: all 0.3s ease;
}

.user-info .avatar-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(47, 16, 103, 0.3);
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

.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    padding: 20px 24px;
}

.modal-body {
    padding: 20px 24px;
    font-size: 1rem;
}

.modal-footer {
    padding: 16px 24px;
}
</style>

<script src="<?= asset('js/dashboard.js') ?>"></script>
