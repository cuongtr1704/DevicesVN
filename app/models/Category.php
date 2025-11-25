<?php
/**
 * Category Model
 */

require_once ROOT_PATH . '/app/models/Model.php';

class Category extends Model {
    protected $table = 'categories';

    public function getActive() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get parent categories (categories with no parent)
     */
    public function getParentCategories() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND parent_id IS NULL 
                ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get child categories for a specific parent
     */
    public function getChildCategories($parentId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND parent_id = :parent_id 
                ORDER BY sort_order ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':parent_id', $parentId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get hierarchical category tree (parents with their children)
     */
    public function getCategoryTree() {
        $parents = $this->getParentCategories();
        
        foreach ($parents as &$parent) {
            $parent['children'] = $this->getChildCategories($parent['id']);
        }
        
        return $parents;
    }

    /**
     * Get all category IDs including children for a parent category
     * Used to show products from parent and all its children
     */
    public function getCategoryWithChildren($categoryId) {
        $categoryIds = [$categoryId];
        
        // Get all children of this category
        $children = $this->getChildCategories($categoryId);
        foreach ($children as $child) {
            $categoryIds[] = $child['id'];
        }
        
        return $categoryIds;
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
