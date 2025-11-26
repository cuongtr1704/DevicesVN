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
    // Handle storage/uploads paths - serve through routing system
    if (strpos($path, 'storage/uploads/') === 0) {
        return BASE_URL . $path;
    }
    return ASSETS_URL . ltrim($path, '/');
}

function slugify($text) {
    // Convert to lowercase
    $text = strtolower($text);
    // Replace Vietnamese characters
    $vietnamese = ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ','ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự','ý','ỳ','ỷ','ỹ','ỵ'];
    $latin = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'];
    $text = str_replace($vietnamese, $latin, $text);
    // Replace special characters with hyphens
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    // Remove leading/trailing hyphens
    $text = trim($text, '-');
    return $text;
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
        return asset('storage/uploads/no-image.svg');
    }
    // If image path already starts with storage/uploads, use it directly
    if (strpos($image, 'storage/uploads/') === 0) {
        return asset($image);
    }
    // Otherwise assume it's in images/products/
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
