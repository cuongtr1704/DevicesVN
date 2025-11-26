<?php

// ProductImage Model - Handles product image gallery operations
class ProductImage
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all images for a product
    public function getProductImages($productId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                pi.id, 
                pi.product_id, 
                pi.image_url, 
                pi.sort_order, 
                pi.is_main,
                p.name as product_name
            FROM product_images pi
            JOIN products p ON pi.product_id = p.id
            WHERE pi.product_id = ?
            ORDER BY pi.sort_order ASC
        ");
        $stmt->execute([$productId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Auto-generate alt text from product name
        foreach ($images as &$image) {
            $image['alt_text'] = $this->generateAltText($image['product_name'], $image['image_url']);
            unset($image['product_name']); // Remove temporary field
        }
        
        return $images;
    }

    // Get the main image for a product
    public function getMainImage($productId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                pi.id, 
                pi.product_id, 
                pi.image_url,
                p.name as product_name
            FROM product_images pi
            JOIN products p ON pi.product_id = p.id
            WHERE pi.product_id = ? AND pi.is_main = 1
            LIMIT 1
        ");
        $stmt->execute([$productId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            $image['alt_text'] = $this->generateAltText($image['product_name'], $image['image_url']);
            unset($image['product_name']);
        }
        
        return $image;
    }
    
    // Generate alt text from product name and image filename
    private function generateAltText($productName, $imageUrl)
    {
        // Extract description from filename (e.g., "dell-xps-13-keyboard.jpg" -> "keyboard")
        $filename = basename($imageUrl, '.jpg');
        $parts = explode('-', $filename);
        $lastPart = end($parts);
        
        // Common image type keywords
        $keywords = ['front', 'side', 'back', 'keyboard', 'display', 'ports', 'colors', 
                     'rgb', 'cooling', 'camera', 'spen', 'pen'];
        
        if (in_array(strtolower($lastPart), $keywords)) {
            return $productName . ' - ' . ucfirst($lastPart);
        }
        
        return $productName;
    }

    // Add a new product image
    public function addImage($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_images (product_id, image_url, sort_order, is_main)
            VALUES (?, ?, ?, ?)
        ");
        
        $success = $stmt->execute([
            $data['product_id'],
            $data['image_url'],
            $data['sort_order'] ?? 0,
            $data['is_main'] ?? 0
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    // Set an image as the main image for a product
    public function setMainImage($productId, $imageId)
    {
        try {
            $this->db->beginTransaction();

            // Unset all main images for this product
            $stmt = $this->db->prepare("
                UPDATE product_images 
                SET is_main = 0 
                WHERE product_id = ?
            ");
            $stmt->execute([$productId]);

            // Set the new main image
            $stmt = $this->db->prepare("
                UPDATE product_images 
                SET is_main = 1 
                WHERE id = ? AND product_id = ?
            ");
            $stmt->execute([$imageId, $productId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Delete a product image
    public function deleteImage($imageId)
    {
        $stmt = $this->db->prepare("DELETE FROM product_images WHERE id = ?");
        return $stmt->execute([$imageId]);
    }

    // Update image details
    public function updateImage($imageId, $data)
    {
        $fields = [];
        $values = [];

        if (isset($data['image_url'])) {
            $fields[] = 'image_url = ?';
            $values[] = $data['image_url'];
        }
        if (isset($data['sort_order'])) {
            $fields[] = 'sort_order = ?';
            $values[] = $data['sort_order'];
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $imageId;
        $sql = "UPDATE product_images SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    // Reorder product images
    public function reorderImages($imageOrder)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE product_images SET sort_order = ? WHERE id = ?");
            
            foreach ($imageOrder as $imageId => $order) {
                $stmt->execute([$order, $imageId]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Get total image count for a product
    public function getImageCount($productId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }
}
