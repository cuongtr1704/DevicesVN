<?php require_once __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="container my-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold">All Categories</h1>
            <p class="lead text-muted">Browse all our product categories</p>
        </div>
    </div>

    <!-- All Categories Grid -->
    <div class="row g-4 mb-5">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?php echo url('products/category/' . $category['slug']); ?>" class="text-decoration-none">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <?php
                            // Use icon from database or fallback to default
                            $icon = !empty($category['icon']) ? $category['icon'] : 'fa-laptop';
                            ?>
                            <i class="fas <?php echo $icon; ?> fa-4x mb-3 category-icon"></i>
                            <h5 class="card-title mb-2"><?php echo escape($category['name']); ?></h5>
                            <?php if (!empty($category['description'])): ?>
                            <p class="card-text text-muted small mb-0">
                                <?php echo escape($category['description']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>No categories available at the moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.category-card {
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(47, 16, 103, 0.15);
    border-color: #2F1067;
}

.category-icon {
    color: #2F1067;
    transition: all 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.15);
    color: #151C32;
}

.category-card .card-title {
    color: #151C32;
    font-weight: 600;
    font-size: 1.1rem;
}

.category-card:hover .card-title {
    color: #2F1067;
}

.category-card .card-body {
    padding: 2rem 1.5rem;
}

@media (max-width: 768px) {
    .category-icon {
        font-size: 3rem !important;
    }
    
    .category-card .card-body {
        padding: 1.5rem 1rem;
    }
    
    .category-card .card-title {
        font-size: 1rem;
    }
}
</style>
