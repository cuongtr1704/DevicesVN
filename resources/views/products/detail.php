<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- Product Images Gallery -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <!-- Main Image -->
                <div class="main-image mb-3">
                    <?php if (!empty($images)): ?>
                        <img id="mainProductImage" 
                             src="<?= asset($images[0]['image_url']) ?>" 
                             alt="<?= escape($images[0]['alt_text']) ?>"
                             class="img-fluid rounded shadow">
                    <?php else: ?>
                        <img src="<?= asset('images/placeholder.jpg') ?>" 
                             alt="<?= escape($product['name']) ?>"
                             class="img-fluid rounded shadow">
                    <?php endif; ?>
                </div>

                <!-- Thumbnail Images -->
                <?php if (count($images) > 1): ?>
                <div class="thumbnail-images">
                    <div class="row g-2">
                        <?php foreach ($images as $index => $image): ?>
                        <div class="col-3">
                            <img src="<?= asset($image['image_url']) ?>" 
                                 alt="<?= escape($image['alt_text']) ?>"
                                 class="img-fluid rounded thumbnail-img <?= $index === 0 ? 'active' : '' ?>"
                                 onclick="changeMainImage(this.src, this)"
                                 style="cursor: pointer; border: 2px solid <?= $index === 0 ? 'var(--primary-color)' : 'transparent' ?>;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold mb-3"><?= escape($product['name']) ?></h1>
            
            <!-- Brand & SKU -->
            <div class="mb-3">
                <?php if (!empty($product['brand'])): ?>
                    <span class="badge bg-secondary me-2">
                        <i class="fas fa-tag"></i> <?= escape($product['brand']) ?>
                    </span>
                <?php endif; ?>
                <?php if (!empty($product['sku'])): ?>
                    <span class="text-muted">SKU: <?= escape($product['sku']) ?></span>
                <?php endif; ?>
            </div>

            <!-- Price -->
            <div class="price-section mb-4">
                <?php if (isOnSale($product)): ?>
                    <div class="d-flex align-items-center gap-3">
                        <h2 class="text-danger fw-bold mb-0"><?= formatPrice($product['sale_price']) ?></h2>
                        <span class="text-decoration-line-through text-muted fs-5"><?= formatPrice($product['price']) ?></span>
                        <span class="badge bg-danger">-<?= discountPercent($product['price'], $product['sale_price']) ?>%</span>
                    </div>
                <?php else: ?>
                    <h2 class="fw-bold mb-0"><?= formatPrice($product['price']) ?></h2>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <div class="mb-4">
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="badge bg-success p-2">
                        <i class="fas fa-check-circle"></i> In Stock (<?= $product['stock_quantity'] ?> available)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger p-2">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </span>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h5>Description</h5>
                <p><?= nl2br(escape($product['description'])) ?></p>
            </div>

            <!-- Add to Cart Form -->
            <?php if ($product['stock_quantity'] > 0): ?>
            <div class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" 
                               class="form-control form-control-lg" 
                               id="quantity" 
                               name="quantity" 
                               value="1" 
                               min="1" 
                               max="<?= $product['stock_quantity'] ?>">
                    </div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-primary btn-lg w-100" onclick="addToCart(<?= $product['id'] ?>)">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="mb-4">
                <button class="btn <?= $inWishlist ? 'btn-danger' : 'btn-outline-danger' ?> w-100" id="wishlistBtn" onclick="toggleWishlist(<?= $product['id'] ?>)">
                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart me-2"></i>
                    <span id="wishlistBtnText"><?= $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' ?></span>
                </button>
            </div>

            <!-- Category Link -->
            <div class="mb-3">
                <strong>Category:</strong>
                <a href="<?= url('products/category/' . $product['category_slug']) ?>" class="text-decoration-none">
                    <?= escape($product['category_name']) ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#specifications" type="button">
                        <i class="fas fa-list"></i> Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                        <i class="fas fa-star"></i> Reviews 
                        <?php if ($ratingStats['total_reviews'] > 0): ?>
                            <span class="badge bg-primary"><?= $ratingStats['total_reviews'] ?></span>
                        <?php endif; ?>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#delivery" type="button">
                        <i class="fas fa-truck"></i> Delivery & Returns
                    </button>
                </li>
            </ul>

            <div class="tab-content border border-top-0 rounded-bottom p-4">
                <!-- Specifications Tab -->
                <div class="tab-pane fade show active" id="specifications">
                    <?php if (!empty($specifications)): ?>
                        <table class="table table-striped">
                            <tbody>
                                <?php foreach ($specifications as $key => $value): ?>
                                <tr>
                                    <th style="width: 30%;"><?= escape($key) ?></th>
                                    <td><?= escape($value) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No specifications available.</p>
                    <?php endif; ?>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <?php if ($ratingStats['total_reviews'] > 0): ?>
                        <!-- Rating Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4 text-center border-end">
                                <h1 class="display-3 fw-bold text-primary mb-2"><?= number_format($ratingStats['average_rating'], 1) ?></h1>
                                <div class="mb-2">
                                    <?php 
                                    $avgRating = round($ratingStats['average_rating'] * 2) / 2;
                                    for ($i = 1; $i <= 5; $i++): 
                                        if ($i <= $avgRating): ?>
                                            <i class="fas fa-star text-warning fs-5"></i>
                                        <?php elseif ($i - 0.5 <= $avgRating): ?>
                                            <i class="fas fa-star-half-alt text-warning fs-5"></i>
                                        <?php else: ?>
                                            <i class="far fa-star text-warning fs-5"></i>
                                        <?php endif;
                                    endfor; ?>
                                </div>
                                <p class="text-muted mb-0"><?= $ratingStats['total_reviews'] ?> review<?= $ratingStats['total_reviews'] > 1 ? 's' : '' ?></p>
                            </div>
                            <div class="col-md-8">
                                <h6 class="mb-3">Rating Breakdown</h6>
                                <?php foreach ($ratingBreakdown as $stars => $count): 
                                    $percentage = $ratingStats['total_reviews'] > 0 ? ($count / $ratingStats['total_reviews']) * 100 : 0;
                                ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="me-2" style="width: 60px;"><?= $stars ?> star<?= $stars > 1 ? 's' : '' ?></span>
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <span class="text-muted" style="width: 50px;"><?= $count ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                    <?php endif; ?>
                    
                    <!-- Write Review Form -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (!$hasReviewed): ?>
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Write a Review</h5>
                                    <form id="reviewForm">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Your Rating *</label>
                                            <div class="star-rating">
                                                <input type="radio" name="rating" value="5" id="star5" required>
                                                <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                                
                                                <input type="radio" name="rating" value="4" id="star4">
                                                <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                                
                                                <input type="radio" name="rating" value="3" id="star3">
                                                <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                                
                                                <input type="radio" name="rating" value="2" id="star2">
                                                <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                                
                                                <input type="radio" name="rating" value="1" id="star1">
                                                <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Your Review *</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="4" required 
                                                      placeholder="Share your experience with this product..."></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Submit Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>You have already reviewed this product.
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Please <a href="#" onclick="showLoginModal(); return false;" class="alert-link">login</a> to write a review.
                        </div>
                    <?php endif; ?>
                    
                    <!-- Reviews List -->
                    <?php if (!empty($reviews)): ?>
                        <h5 class="mb-3">Customer Reviews</h5>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar-circle me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2F1067, #151C32); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                        <?= strtoupper(substr($review['user_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= escape($review['user_name']) ?></h6>
                                                        <small class="text-muted"><?= date('M d, Y', strtotime($review['created_at'])) ?></small>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= $review['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteReview(<?= $review['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-0"><?= nl2br(escape($review['comment'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($ratingStats['total_reviews'] == 0): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5>No reviews yet</h5>
                            <p class="text-muted">Be the first to review this product!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Delivery Tab -->
                <div class="tab-pane fade" id="delivery">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-truck me-2"></i>Delivery Information</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free shipping on orders over 5,000,000 VND</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>1-2 days delivery in Hanoi & HCMC</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>2-3 days delivery in other cities</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Express delivery available</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-undo me-2"></i>Return Policy</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>7-day return policy</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free return shipping</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Full refund or exchange</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Original packaging required</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="related-products mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row g-4">
            <?php foreach ($relatedProducts as $relatedProduct): ?>
            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <?php if (isOnSale($relatedProduct)): ?>
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                        -<?= discountPercent($relatedProduct['price'], $relatedProduct['sale_price']) ?>%
                    </span>
                    <?php endif; ?>
                    
                    <img src="<?= productImage($relatedProduct['main_image']) ?>" 
                         class="card-img-top" 
                         alt="<?= escape($relatedProduct['name']) ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">
                            <a href="<?= url('products/' . $relatedProduct['slug']) ?>" class="text-decoration-none">
                                <?= escape($relatedProduct['name']) ?>
                            </a>
                        </h6>
                        <div class="price mt-auto">
                            <?php if (isOnSale($relatedProduct)): ?>
                                <div class="text-danger fw-bold"><?= formatPrice($relatedProduct['sale_price']) ?></div>
                                <div class="text-decoration-line-through text-muted small"><?= formatPrice($relatedProduct['price']) ?></div>
                            <?php else: ?>
                                <div class="fw-bold"><?= formatPrice($relatedProduct['price']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
// Change main product image when clicking thumbnail
function changeMainImage(src, element) {
    document.getElementById('mainProductImage').src = src;
    
    // Update active state on thumbnails
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.style.border = '2px solid transparent';
    });
    element.style.border = '2px solid var(--primary-color)';
}

// Add to cart
function addToCart(productId) {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
    
    if (quantity < 1) {
        showNotification('Please enter a valid quantity', 'error');
        return;
    }
    
    fetch('<?= url('cart/add') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            // Reset quantity to 1
            if (quantityInput) quantityInput.value = 1;
            // Trigger cart count update
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

// Toggle wishlist
function toggleWishlist(productId) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        showNotification('Please login to add items to wishlist', 'error');
        return;
    <?php endif; ?>
    
    const btn = document.getElementById('wishlistBtn');
    const icon = btn.querySelector('i');
    const text = document.getElementById('wishlistBtnText');
    const originalText = text.textContent;
    
    // Disable button during request
    btn.disabled = true;
    text.textContent = 'Processing...';
    
    fetch('<?= url('wishlist/toggle') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.inWishlist) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.textContent = 'Remove from Wishlist';
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.textContent = 'Add to Wishlist';
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
            }
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            text.textContent = originalText;
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        text.textContent = originalText;
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// Submit review form
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    
    fetch('<?= url('reviews/add') ?>', {
        method: 'POST',
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
});

// Delete review
function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete this review?')) {
        return;
    }
    
    fetch('<?= url('reviews/delete') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'review_id=' + reviewId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// Show login modal
function showLoginModal() {
    const authModal = document.getElementById('authModal');
    if (authModal) {
        const modal = new bootstrap.Modal(authModal);
        modal.show();
        
        // Switch to login tab
        const loginTab = document.getElementById('login-tab');
        if (loginTab) {
            const tab = new bootstrap.Tab(loginTab);
            tab.show();
        }
    }
}

// Show notification
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
.main-image img {
    max-height: 500px;
    object-fit: contain;
    width: 100%;
}

.thumbnail-img {
    transition: all 0.3s;
}

.thumbnail-img:hover {
    opacity: 0.7;
    transform: scale(1.05);
}

.price-section {
    border-top: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem 0;
}

.nav-tabs .nav-link {
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
}

#wishlistBtn {
    transition: all 0.3s ease;
    font-weight: 600;
}

#wishlistBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

#wishlistBtn.btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-color: #dc3545;
    color: white;
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

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Star Rating Styles */
.star-rating {
    direction: rtl;
    display: inline-flex;
    font-size: 2rem;
}

.star-rating input[type="radio"] {
    display: none;
}

.star-rating label {
    color: #ddd;
    cursor: pointer;
    margin: 0 4px;
    transition: color 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffc107;
}

.star-rating input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.review-item {
    transition: all 0.3s ease;
}

.review-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.progress {
    background-color: #e9ecef;
}
</style>
