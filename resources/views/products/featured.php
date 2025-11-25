<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<link rel="stylesheet" href="<?= asset('css/products.css') ?>">

<div class="container my-5">
    <!-- Page Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold">
                <i class="fas fa-star text-warning me-2"></i>Featured Products
            </h1>
            <p class="lead text-muted">Our handpicked selection of the best products</p>
        </div>
        <div class="col-md-4 text-md-end">
            <p class="text-muted mb-2">Showing <?= count($products) ?> of <?= $totalProducts ?> products</p>
            <button class="btn btn-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="fas fa-filter me-2"></i>Filters
            </button>
        </div>
    </div>

    <!-- Desktop Filters -->
    <div class="row mb-3 d-none d-lg-flex">
        <div class="col-lg-4">
            <label class="form-label fw-bold"><i class="fas fa-th-list me-2"></i>Category</label>
            <select class="form-select" onchange="if(this.value) window.location.href=this.value">
                <option value="<?= url('products') ?>">All Products</option>
                <option value="<?= url('products/featured') ?>" selected>⭐ Featured Products</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= url('products/category/' . $cat['slug']) ?>">
                        <?= escape($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-lg-4">
            <label class="form-label fw-bold"><i class="fas fa-sort me-2"></i>Sort By</label>
            <form method="GET" action="">
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="p.views DESC" <?= ($currentSort ?? '') == 'p.views DESC' ? 'selected' : '' ?>>
                        Most Popular
                    </option>
                    <option value="p.created_at DESC" <?= ($currentSort ?? '') == 'p.created_at DESC' ? 'selected' : '' ?>>
                        Newest First
                    </option>
                    <option value="p.name ASC" <?= ($currentSort ?? '') == 'p.name ASC' ? 'selected' : '' ?>>
                        Name (A-Z)
                    </option>
                    <option value="p.price ASC" <?= ($currentSort ?? '') == 'p.price ASC' ? 'selected' : '' ?>>
                        Price: Low to High
                    </option>
                    <option value="p.price DESC" <?= ($currentSort ?? '') == 'p.price DESC' ? 'selected' : '' ?>>
                        Price: High to Low
                    </option>
                </select>
            </form>
        </div>
        <div class="col-lg-4">
            <label class="form-label fw-bold"><i class="fas fa-filter me-2"></i>Price Range</label>
            <form method="GET" action="" class="d-flex gap-2" onsubmit="return validatePriceRange(this, 'desktop')">
                <?php if (isset($_GET['sort'])): ?>
                    <input type="hidden" name="sort" value="<?= $_GET['sort'] ?>">
                <?php endif; ?>
                <input type="number" name="min_price" id="desktop_min_price" class="form-control" placeholder="Min" 
                       value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>" min="0">
                <input type="number" name="max_price" id="desktop_max_price" class="form-control" placeholder="Max" 
                       value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>" min="0">
                <button type="submit" class="btn btn-primary">Go</button>
            </form>
            <small id="desktop_price_error" class="text-danger" style="display: none;">Minimum price cannot be greater than maximum price</small>
        </div>
    </div>

    <!-- Mobile Filter Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="fas fa-filter me-2"></i>Filters</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Categories -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-th-list me-2"></i>Categories</h6>
                <select class="form-select" onchange="if(this.value) window.location.href=this.value">
                    <option value="<?= url('products') ?>">All Products</option>
                    <option value="<?= url('products/featured') ?>" selected>⭐ Featured Products</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= url('products/category/' . $cat['slug']) ?>"><?= escape($cat['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Sort -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-sort me-2"></i>Sort By</h6>
                <form method="GET" action="">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="p.views DESC" <?= ($currentSort ?? '') == 'p.views DESC' ? 'selected' : '' ?>>
                            Most Popular
                        </option>
                        <option value="p.created_at DESC" <?= ($currentSort ?? '') == 'p.created_at DESC' ? 'selected' : '' ?>>
                            Newest First
                        </option>
                        <option value="p.name ASC" <?= ($currentSort ?? '') == 'p.name ASC' ? 'selected' : '' ?>>
                            Name (A-Z)
                        </option>
                        <option value="p.price ASC" <?= ($currentSort ?? '') == 'p.price ASC' ? 'selected' : '' ?>>
                            Price: Low to High
                        </option>
                        <option value="p.price DESC" <?= ($currentSort ?? '') == 'p.price DESC' ? 'selected' : '' ?>>
                            Price: High to Low
                        </option>
                    </select>
                </form>
            </div>

            <!-- Price Range -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Price Range</h6>
                <form method="GET" action="" onsubmit="return validatePriceRange(this, 'mobile')">
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= $_GET['sort'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label small">Min Price</label>
                        <input type="number" name="min_price" id="mobile_min_price" class="form-control" placeholder="0" 
                               value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : '' ?>" min="">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Max Price</label>
                        <input type="number" name="max_price" id="mobile_max_price" class="form-control" placeholder="" 
                               value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : '' ?>" min="0">
                    </div>
                    <small id="mobile_price_error" class="text-danger" style="display: none;">Minimum price cannot be greater than maximum price</small>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Apply Filter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Products Grid -->
        <div class="col-12 product-grid">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No featured products available at the moment.
                </div>
            <?php else: ?>
                <div class="row g-4 mb-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <div class="position-relative">
                                <!-- Featured Badge -->
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2" style="z-index: 11;">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                                
                                <?php if (isOnSale($product)): ?>
                                <span class="badge bg-danger position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                    -<?= discountPercent($product['price'], $product['sale_price']) ?>%
                                </span>
                                <?php endif; ?>
                                
                                <img src="<?= productImage($product['main_image']) ?>" 
                                     class="card-img-top" alt="<?= escape($product['name']) ?>"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f0f0f0%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 font-family=%27Arial%27 font-size=%2720%27 fill=%27%23999%27 text-anchor=%27middle%27 dy=%27.3em%27%3ENo Image%3C/text%3E%3C/svg%3E';">
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="<?= url('products/' . $product['slug']) ?>">
                                        <?= escape($product['name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small">
                                    <?= truncate($product['description'], 80) ?>
                                </p>
                                <div class="price mb-2 mt-auto">
                                    <?php if (isOnSale($product)): ?>
                                        <div class="text-decoration-line-through text-muted small">
                                            <?= formatPrice($product['price']) ?>
                                        </div>
                                        <div class="text-danger fw-bold" style="font-size: 1.1rem;">
                                            <?= formatPrice($product['sale_price']) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="fw-bold" style="font-size: 1.1rem;"><?= formatPrice($product['price']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= url('products/' . $product['slug']) ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Featured products pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php 
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);
                        
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : '' ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.pagination .page-link {
    color: #2F1067;
}

.pagination .page-item.active .page-link {
    background-color: #2F1067;
    border-color: #2F1067;
    color: white;
}

.form-select {
    border: 1px solid #dee2e6;
    padding: 0.5rem 2.5rem 0.5rem 0.75rem;
}

.form-select:focus {
    border-color: #2F1067;
    box-shadow: 0 0 0 0.2rem rgba(47, 16, 103, 0.25);
}

.offcanvas-body h6 {
    color: #151C32;
}

.badge.bg-warning {
    font-weight: 700;
}

@media (max-width: 991px) {
    .product-grid .col-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 992px) {
    .product-grid .col-6 {
        flex: 0 0 25%;
        max-width: 25%;
    }
}
</style>

<script>
function validatePriceRange(form, type) {
    const minInput = form.querySelector('[name="min_price"]');
    const maxInput = form.querySelector('[name="max_price"]');
    const errorLabel = document.getElementById(type + '_price_error');
    
    const minPrice = minInput.value ? parseFloat(minInput.value) : 0;
    const maxPrice = maxInput.value ? parseFloat(maxInput.value) : 0;
    
    // If min is set but max is 0 or empty, show error
    if (minPrice > 0 && maxPrice === 0) {
        errorLabel.textContent = 'Please enter a maximum price';
        errorLabel.style.display = 'block';
        return false;
    }
    
    // If both are set and min > max, show error
    if (minPrice > 0 && maxPrice > 0 && minPrice > maxPrice) {
        errorLabel.textContent = 'Minimum price cannot be greater than maximum price';
        errorLabel.style.display = 'block';
        return false;
    }
    
    errorLabel.style.display = 'none';
    return true;
}
</script>
