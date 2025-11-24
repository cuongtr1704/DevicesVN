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
                <?php foreach ($categories as $category): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?php echo url('products/category/' . $category['slug']); ?>" class="text-decoration-none">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <?php
                                // Icon mapping for categories
                                $icons = [
                                    'laptops' => 'fa-laptop',
                                    'gaming-laptops' => 'fa-gamepad',
                                    'phones' => 'fa-mobile-alt',
                                    'tablets' => 'fa-tablet-alt',
                                    'accessories' => 'fa-keyboard',
                                    'mice' => 'fa-computer-mouse',
                                    'keyboards' => 'fa-keyboard',
                                    'headphones' => 'fa-headphones'
                                ];
                                $icon = isset($icons[$category['slug']]) ? $icons[$category['slug']] : 'fa-laptop';
                                ?>
                                <i class="fas <?php echo $icon; ?> fa-3x mb-3"></i>
                                <h5 class="card-title"><?php echo escape($category['name']); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>
        
        <?php if (empty($featuredProducts)): ?>
            <div class="alert alert-info">No featured products available.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100">
                        <?php if (isOnSale($product)): ?>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                            -<?php echo discountPercent($product['price'], $product['sale_price']); ?>%
                        </span>
                        <?php endif; ?>
                        
                        <img src="<?php echo productImage($product['image_url']); ?>" 
                             class="card-img-top" alt="<?php echo escape($product['name']); ?>">
                        
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

<section class="newsletter-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0">
                <h3>Subscribe to Our Newsletter</h3>
                <p>Get the latest updates on new products and exclusive offers!</p>
            </div>
            <div class="col-lg-7">
                <form action="<?php echo url('newsletter/subscribe'); ?>" method="POST">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                        <button class="btn btn-light" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
