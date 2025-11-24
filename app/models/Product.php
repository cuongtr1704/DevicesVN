<?php
/**
 * Product Model
 */

require_once ROOT_PATH . '/app/models/Model.php';

class Product extends Model {
    protected $table = 'products';

    public function getPaginated($page = 1, $perPage = 12, $orderBy = 'id DESC', $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }

    public function findBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = :slug 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function search($query, $limit = 10) {
        $sql = "SELECT id, name, slug, price, sale_price, image_url 
                FROM {$this->table} 
                WHERE name LIKE :query 
                ORDER BY name 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getFeatured($limit = 8) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_featured = 1 
                ORDER BY views DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

}
