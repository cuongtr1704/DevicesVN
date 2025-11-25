<?php
/**
 * Product Model
 */

require_once ROOT_PATH . '/app/models/Model.php';

class Product extends Model {
    protected $table = 'products';

    public function getPaginated($page = 1, $perPage = 12, $orderBy = 'id DESC', $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name, 
                       pi.image_url as main_image
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE 1=1";
        
        $bindParams = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $bindParams[] = $filters['category_id'];
        }
        
        if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
            $placeholders = implode(',', array_fill(0, count($filters['category_ids']), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            foreach ($filters['category_ids'] as $catId) {
                $bindParams[] = $catId;
            }
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $bindParams[] = $searchTerm;
            $bindParams[] = $searchTerm;
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $bindParams[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $bindParams[] = $filters['max_price'];
        }
        
        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $bindParams[] = $perPage;
        $bindParams[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        
        // Bind all parameters positionally
        $paramIndex = 1;
        foreach ($bindParams as $param) {
            if (is_int($param)) {
                $stmt->bindValue($paramIndex++, $param, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $bindParams = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = ?";
            $bindParams[] = $filters['category_id'];
        }
        
        if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
            $placeholders = implode(',', array_fill(0, count($filters['category_ids']), '?'));
            $sql .= " AND category_id IN ($placeholders)";
            foreach ($filters['category_ids'] as $catId) {
                $bindParams[] = $catId;
            }
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $bindParams[] = $searchTerm;
            $bindParams[] = $searchTerm;
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(sale_price, price) >= ?";
            $bindParams[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(sale_price, price) <= ?";
            $bindParams[] = $filters['max_price'];
        }
        
        $stmt = $this->db->prepare($sql);
        
        // Bind all parameters positionally
        $paramIndex = 1;
        foreach ($bindParams as $param) {
            if (is_int($param)) {
                $stmt->bindValue($paramIndex++, $param, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }

    public function findBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       pi.image_url as main_image
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE p.slug = :slug 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Search products with pagination
     */
    public function search($query, $page = 1, $perPage = 12) {
        $offset = ($page - 1) * $perPage;
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug,
                   pi.image_url as main_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
            WHERE (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR p.sku LIKE ?)
            ORDER BY 
                CASE 
                    WHEN p.name LIKE ? THEN 1
                    WHEN p.brand LIKE ? THEN 2
                    ELSE 3
                END,
                p.name ASC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([
            $searchTerm, $searchTerm, $searchTerm, $searchTerm,
            $searchTerm, $searchTerm,
            $perPage, $offset
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get search suggestions (for autocomplete dropdown)
     */
    public function searchSuggestions($query, $limit = 5) {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.brand,
                   pi.image_url as main_image,
                   c.name as category_name
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE (p.name LIKE ? OR p.brand LIKE ?)
            ORDER BY 
                CASE 
                    WHEN p.name LIKE ? THEN 1
                    WHEN p.brand LIKE ? THEN 2
                    ELSE 3
                END,
                p.name ASC
            LIMIT ?
        ");
        
        $stmt->execute([
            $searchTerm, $searchTerm,
            $searchTerm, $searchTerm,
            $limit
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Count search results
     */
    public function countSearchResults($query) {
        $searchTerm = '%' . $query . '%';
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM products
            WHERE (name LIKE ? OR description LIKE ? OR brand LIKE ? OR sku LIKE ?)
        ");
        
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }

    public function getFeatured($limit = 8) {
        $sql = "SELECT p.*, pi.image_url as main_image
                FROM {$this->table} p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE p.is_featured = 1 
                ORDER BY p.views DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getFeaturedPaginated($page = 1, $perPage = 12, $orderBy = 'p.views DESC', $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name, 
                       pi.image_url as main_image
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE p.is_featured = 1";
        
        $bindParams = [];
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $bindParams[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $bindParams[] = $filters['max_price'];
        }
        
        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        
        $bindParams[] = $perPage;
        $bindParams[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        
        $paramIndex = 1;
        foreach ($bindParams as $param) {
            if (is_int($param)) {
                $stmt->bindValue($paramIndex++, $param, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function countFeatured($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_featured = 1";
        
        $bindParams = [];
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND COALESCE(sale_price, price) >= ?";
            $bindParams[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND COALESCE(sale_price, price) <= ?";
            $bindParams[] = $filters['max_price'];
        }
        
        $stmt = $this->db->prepare($sql);
        
        $paramIndex = 1;
        foreach ($bindParams as $param) {
            if (is_int($param)) {
                $stmt->bindValue($paramIndex++, $param, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($paramIndex++, $param);
            }
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }
    
    /**
     * Update product stock quantity
     */
    public function updateStock($productId, $newStock) {
        $stmt = $this->db->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
        return $stmt->execute([$newStock, $productId]);
    }

}
