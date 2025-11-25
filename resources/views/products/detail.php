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
            <form action="<?= BASE_URL ?>cart/add" method="POST" class="mb-4">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
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
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-4">
                <button class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-heart"></i> Add to Wishlist
                </button>
                <button class="btn btn-outline-secondary flex-fill">
                    <i class="fas fa-share-alt"></i> Share
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
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5>No reviews yet</h5>
                        <p class="text-muted">Be the first to review this product!</p>
                        <button class="btn btn-primary">Write a Review</button>
                    </div>
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
</style>
