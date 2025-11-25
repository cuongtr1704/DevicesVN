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
    
    /**
     * Get max sort order
     */
    public function getMaxSortOrder() {
        $sql = "SELECT MAX(sort_order) as max_order FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)($result['max_order'] ?? 0);
    }
    
    /**
     * Adjust sort orders for other categories
     */
    public function adjustSortOrders($newSortOrder, $action, $currentId = null, $oldSortOrder = null) {
        if ($action === 'insert') {
            // Shift all categories with sort_order >= newSortOrder up by 1
            $sql = "UPDATE {$this->table} SET sort_order = sort_order + 1 WHERE sort_order >= ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$newSortOrder]);
        } elseif ($action === 'delete' && $oldSortOrder !== null) {
            // Shift all categories with sort_order > deleted item down by 1
            $sql = "UPDATE {$this->table} SET sort_order = sort_order - 1 WHERE sort_order > ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$oldSortOrder]);
        } elseif ($action === 'update' && $currentId) {
            if ($newSortOrder < $oldSortOrder) {
                // Moving up: shift categories between new and old position down
                $sql = "UPDATE {$this->table} SET sort_order = sort_order + 1 WHERE sort_order >= ? AND sort_order < ? AND id != ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$newSortOrder, $oldSortOrder, $currentId]);
            } elseif ($newSortOrder > $oldSortOrder) {
                // Moving down: shift categories between old and new position up
                $sql = "UPDATE {$this->table} SET sort_order = sort_order - 1 WHERE sort_order > ? AND sort_order <= ? AND id != ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$oldSortOrder, $newSortOrder, $currentId]);
            }
        }
    }
    
    /**
     * Reorder all categories sequentially (cleanup gaps)
     */
    public function reorderSequentially() {
        $sql = "SELECT id FROM {$this->table} ORDER BY sort_order ASC, id ASC";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $updateSql = "UPDATE {$this->table} SET sort_order = ? WHERE id = ?";
        $updateStmt = $this->db->prepare($updateSql);
        
        foreach ($categories as $index => $categoryId) {
            $updateStmt->execute([$index + 1, $categoryId]);
        }
    }
}
