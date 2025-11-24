<?php
/**
 * Category Model
 */

require_once ROOT_PATH . '/app/models/Model.php';

class Category extends Model {
    protected $table = 'categories';

    public function getActive() {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1 ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findBySlug($slug) {
        return $this->findBy('slug', $slug);
    }

    public function getBreadcrumb($categoryId) {
        $breadcrumb = [];
        $category = $this->findById($categoryId);
        
        while ($category) {
            array_unshift($breadcrumb, $category);
            $category = $category['parent_id'] ? $this->findById($category['parent_id']) : null;
        }
        
        return $breadcrumb;
    }
}
