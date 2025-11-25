<section class="hero-section text-white">
    <div class="container text-center">
        <h1 class="display-3 fw-bold">Welcome to DevicesVN</h1>
        <p class="lead">Discover Premium Laptops, Gaming Devices, Phones & Accessories</p>
        <a href="<?php echo url('products'); ?>" class="btn btn-light btn-lg">Shop Now</a>
    </div>
</section>

<div class="container">
    <section class="categories-section">
        <h2 class="text-center">Shop by Category</h2>
        <div class="row g-4">
            <?php if (!empty($categories)): ?>
                <?php 
                $displayLimit = 11; // Show 11 categories + 1 "More" card if needed
                $totalCategories = count($categories);
                $hasMore = $totalCategories > 12;
                $displayCount = $hasMore ? $displayLimit : min($totalCategories, 12);
                
                for ($i = 0; $i < $displayCount; $i++): 
                    $category = $categories[$i];
                ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?php echo url('products/category/' . $category['slug']); ?>" class="text-decoration-none">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <?php
                                // Use icon from database or fallback to default
                                $icon = !empty($category['icon']) ? $category['icon'] : 'fa-laptop';
                                ?>
                                <i class="fas <?php echo $icon; ?> fa-3x mb-3"></i>
                                <h5 class="card-title"><?php echo escape($category['name']); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endfor; ?>
                
                <?php if ($hasMore): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?php echo url('categories'); ?>" class="text-decoration-none">
                        <div class="card text-center h-100 more-categories-card">
                            <div class="card-body">
                                <i class="fas fa-ellipsis-h fa-3x mb-3"></i>
                                <h5 class="card-title">More Categories</h5>
                                <p class="text-muted small mb-0"><?php echo $totalCategories - $displayLimit; ?> more</p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="featured-products">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Featured Products</h2>
            <a href="<?php echo url('products/featured'); ?>" class="btn btn-outline-primary">
                View All <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <?php if (empty($featuredProducts)): ?>
            <div class="alert alert-info">No featured products available.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <div class="position-relative">
                            <?php if (isOnSale($product)): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2" style="z-index: 10;">
                                -<?php echo discountPercent($product['price'], $product['sale_price']); ?>%
                            </span>
                            <?php endif; ?>
                            
                            <img src="<?php echo productImage($product['main_image']); ?>" 
                                 class="card-img-top" alt="<?php echo escape($product['name']); ?>"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%27400%27 height=%27400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f0f0f0%27/%3E%3Ctext x=%2750%25%27 y=%2750%25%27 font-family=%27Arial%27 font-size=%2720%27 fill=%27%23999%27 text-anchor=%27middle%27 dy=%27.3em%27%3ENo Image%3C/text%3E%3C/svg%3E';">
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="<?php echo url('products/' . $product['slug']); ?>">
                                    <?php echo escape($product['name']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                <?php echo truncate($product['description'], 80); ?>
                            </p>
                            <div class="price mb-2 mt-auto">
                                <?php if (isOnSale($product)): ?>
                                    <div class="text-decoration-line-through text-muted small">
                                        <?php echo formatPrice($product['price']); ?>
                                    </div>
                                    <div class="text-danger fw-bold fs-5">
                                        <?php echo formatPrice($product['sale_price']); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="fw-bold fs-5"><?php echo formatPrice($product['price']); ?></div>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo url('products/' . $product['slug']); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

