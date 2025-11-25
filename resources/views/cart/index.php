<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                </h2>
                <a href="<?= url('products') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart State -->
        <div class="row">
            <div class="col-12">
                <div class="empty-cart-container text-center py-5">
                    <div class="empty-cart-icon mb-4">
                        <i class="fas fa-shopping-cart" style="font-size: 120px; color: #dee2e6;"></i>
                    </div>
                    <h3 class="mb-3">Your cart is empty</h3>
                    <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="<?= url('products') ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Browse Products
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-0">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item p-4 border-bottom" id="cart-item-<?= $item['id'] ?>" data-price="<?= $item['price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                                        <a href="<?= url('products/' . $item['slug']) ?>">
                                            <img src="<?= $item['image'] ? asset($item['image']) : asset('images/no-image.png') ?>" 
                                                 alt="<?= escape($item['name']) ?>" 
                                                 class="img-fluid rounded product-thumbnail">
                                        </a>
                                    </div>
                                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                                        <h5 class="mb-1">
                                            <a href="<?= url('products/' . $item['slug']) ?>" class="text-decoration-none text-dark">
                                                <?= escape($item['name']) ?>
                                            </a>
                                        </h5>
                                        <?php if ($item['stock_quantity'] > 0): ?>
                                            <small class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>In Stock
                                            </small>
                                        <?php else: ?>
                                            <small class="text-danger">
                                                <i class="fas fa-times-circle me-1"></i>Out of Stock
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 col-4 mb-3 mb-md-0">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?= $item['id'] ?>, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center quantity-input" 
                                                   id="quantity-<?= $item['id'] ?>" 
                                                   value="<?= $item['quantity'] ?>" 
                                                   min="1" 
                                                   max="<?= $item['stock_quantity'] ?>"
                                                   onchange="changeQuantity(<?= $item['id'] ?>, this.value)">
                                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?= $item['id'] ?>, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 text-md-center mb-3 mb-md-0">
                                        <div class="item-price fw-bold" id="item-total-<?= $item['id'] ?>">
                                            <?= number_format($item['price'] * $item['quantity']) ?> ₫
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-2 text-end">
                                        <button class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" 
                                                style="width: 36px; height: 36px; padding: 0;" 
                                                onclick="removeFromCart(<?= $item['id'] ?>)" 
                                                title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="subtotal"><?= number_format($cartTotal) ?> ₫</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success">Free</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Total</span>
                            <span class="h5 mb-0 text-primary" id="total"><?= number_format($cartTotal) ?> ₫</span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= url('orders/checkout') ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-lock me-2"></i>Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary btn-lg w-100 mb-3" href="javascript:void(0)" onclick="openLoginModal()">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                            </button>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>Secure Checkout
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
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
                <p class="mb-0">Are you sure you want to remove this item from your cart?</p>
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
let pendingRemoveId = null;

function showLoginModal() {
    // Check if auth modal exists (from main layout)
    const authModal = document.getElementById('authModal');
    if (authModal) {
        const modal = new bootstrap.Modal(authModal);
        // Switch to login tab
        const loginTab = document.querySelector('#authModal .nav-link[data-bs-target="#login"]');
        if (loginTab) {
            const tab = new bootstrap.Tab(loginTab);
            tab.show();
        }
        modal.show();
        
        // After successful login, reload the page to update cart
        authModal.addEventListener('hidden.bs.modal', function(e) {
            // Check if user is now logged in by checking if checkout button exists
            setTimeout(() => {
                fetch('<?= url('cart/count') ?>')
                    .then(response => response.json())
                    .then(data => {
                        // If we have a different count, reload to show updated cart
                        location.reload();
                    });
            }, 500);
        }, { once: true });
    } else {
        // Fallback: show notification
        showNotification('Please login to proceed with checkout', 'error');
    }
}

function updateQuantity(cartId, change) {
    const input = document.getElementById('quantity-' + cartId);
    let newQuantity = parseInt(input.value) + change;
    
    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > parseInt(input.max)) {
        showNotification('Maximum stock reached', 'error');
        return;
    }
    
    input.value = newQuantity;
    changeQuantity(cartId, newQuantity);
}

function changeQuantity(cartId, quantity) {
    if (quantity < 1) {
        showNotification('Quantity must be at least 1', 'error');
        return;
    }
    
    fetch('<?= url('cart/update') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cart_id=' + cartId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update item total
            const item = document.getElementById('cart-item-' + cartId);
            const price = parseFloat(item.dataset.price);
            const itemTotal = price * quantity;
            document.getElementById('item-total-' + cartId).textContent = 
                itemTotal.toLocaleString('vi-VN') + ' ₫';
            
            // Update cart total
            document.getElementById('subtotal').textContent = 
                parseFloat(data.cartTotal).toLocaleString('vi-VN') + ' ₫';
            document.getElementById('total').textContent = 
                parseFloat(data.cartTotal).toLocaleString('vi-VN') + ' ₫';
            
            item.dataset.quantity = quantity;
            
            // Trigger cart count update
            window.dispatchEvent(new Event('cartUpdated'));
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function removeFromCart(cartId) {
    pendingRemoveId = cartId;
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
    if (!pendingRemoveId) return;
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    if (modal) modal.hide();
    
    fetch('<?= url('cart/remove') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cart_id=' + pendingRemoveId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.getElementById('cart-item-' + pendingRemoveId);
            if (item) {
                item.style.animation = 'fadeOut 0.3s ease';
                
                setTimeout(() => {
                    item.remove();
                    
                    // Update totals
                    const subtotalEl = document.getElementById('subtotal');
                    const totalEl = document.getElementById('total');
                    if (subtotalEl && totalEl) {
                        subtotalEl.textContent = parseFloat(data.cartTotal).toLocaleString('vi-VN') + ' ₫';
                        totalEl.textContent = parseFloat(data.cartTotal).toLocaleString('vi-VN') + ' ₫';
                    }
                    
                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('[id^="cart-item-"]').length;
                    if (remainingItems === 0) {
                        location.reload();
                    }
                    
                    // Trigger cart count update
                    window.dispatchEvent(new Event('cartUpdated'));
                    
                    showNotification(data.message, 'success');
                }, 300);
            } else {
                location.reload();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
    
    pendingRemoveId = null;
});

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
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.product-thumbnail {
    transition: transform 0.3s;
    width: 100%;
    height: 100px;
    object-fit: contain;
    background: #f8f9fa;
    padding: 10px;
    border-radius: 8px;
}

.product-thumbnail:hover {
    transform: scale(1.05);
}

.quantity-input {
    max-width: 70px;
}

.item-price {
    font-size: 1.1rem;
    color: var(--primary-color);
}

.empty-cart-container {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
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

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
        transform: translateX(-20px);
    }
}
</style>
