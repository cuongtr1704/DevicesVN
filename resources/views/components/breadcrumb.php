<!-- Breadcrumb Component -->
<nav aria-label="breadcrumb" class="breadcrumb-nav">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= url('') ?>">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            
            <?php if (!empty($breadcrumbs)): ?>
                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                    <?php if ($index === array_key_last($breadcrumbs)): ?>
                        <!-- Last item (current page) -->
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= escape($crumb['label']) ?>
                        </li>
                    <?php else: ?>
                        <!-- Intermediate items with links -->
                        <li class="breadcrumb-item">
                            <a href="<?= $crumb['url'] ?>">
                                <?= escape($crumb['label']) ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </ol>
    </div>
</nav>
