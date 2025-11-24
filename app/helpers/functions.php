<?php
/**
 * Helper Functions
 */

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' VND';
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function asset($path) {
    return ASSETS_URL . ltrim($path, '/');
}

function isActive($path) {
    $currentPath = $_SERVER['REQUEST_URI'];
    return strpos($currentPath, $path) !== false;
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

function productImage($image) {
    if (empty($image)) {
        return asset('images/no-image.png');
    }
    return asset('images/products/' . $image);
}

function discountPercent($regularPrice, $salePrice) {
    if ($salePrice >= $regularPrice) {
        return 0;
    }
    return round((($regularPrice - $salePrice) / $regularPrice) * 100);
}

function isOnSale($product) {
    return !empty($product['sale_price']) && $product['sale_price'] < $product['price'];
}

function finalPrice($product) {
    return $product['sale_price'] ?? $product['price'];
}
