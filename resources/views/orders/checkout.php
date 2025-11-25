<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-lock me-2"></i>Checkout
            </h2>
        </div>
    </div>

    <div class="row">
        <!-- Shipping Information Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-4">
                        <i class="fas fa-shipping-fast me-2"></i>Shipping Information
                    </h5>
                    
                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label for="shipping_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                   value="<?= escape($_SESSION['user_name']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" 
                                   placeholder="0123456789" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                      rows="3" placeholder="Enter your full address" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="2" placeholder="Any special instructions for delivery?"></textarea>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">
                        <i class="fas fa-credit-card me-2"></i>Payment Method
                    </h5>
                    
                    <div class="payment-method-option p-3 border rounded mb-3" style="cursor: pointer; background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="payment_method" id="cod" value="cod" checked class="form-check-input me-3">
                            <div class="flex-grow-1">
                                <label for="cod" class="form-check-label mb-0" style="cursor: pointer;">
                                    <strong>Cash on Delivery (COD)</strong>
                                    <p class="mb-0 small text-muted">Pay when you receive your order</p>
                                </label>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-body p-4">
                    <h5 class="mb-4">Order Summary</h5>
                    
                    <!-- Order Items -->
                    <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="<?= $item['image'] ? asset($item['image']) : asset('images/no-image.png') ?>" 
                                     alt="<?= escape($item['name']) ?>" 
                                     class="rounded me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= escape($item['name']) ?></h6>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                </div>
                                <div class="text-end">
                                    <strong><?= number_format($item['price'] * $item['quantity']) ?> ₫</strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr>
                    
                    <!-- Pricing Breakdown -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold"><?= number_format($cartTotal) ?> ₫</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping Fee</span>
                        <span class="text-success fw-bold">Free</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 mb-0">Total</span>
                        <span class="h5 mb-0 text-primary"><?= number_format($cartTotal) ?> ₫</span>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="button" class="btn btn-primary btn-lg w-100 mb-3" id="placeOrderBtn">
                        <i class="fas fa-check-circle me-2"></i>Place Order
                    </button>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>Your information is secure
                        </small>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="<?= url('cart') ?>" class="btn btn-link">
                            <i class="fas fa-arrow-left me-1"></i>Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('placeOrderBtn').addEventListener('click', function() {
    const form = document.getElementById('checkoutForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    const formData = new FormData(form);
    
    fetch('<?= url('orders/process') ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            showNotification(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
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
.payment-method-option {
    transition: all 0.3s ease;
    border: 2px solid transparent !important;
}

.payment-method-option:hover {
    border-color: var(--primary-color) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.order-items::-webkit-scrollbar {
    width: 6px;
}

.order-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.order-items::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.order-items::-webkit-scrollbar-thumb:hover {
    background: #555;
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
