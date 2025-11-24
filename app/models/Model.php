<?php
/**
 * Base Model Class
 */

class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    public function getAll($orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function findBy($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $sets);
        
        $sql = "UPDATE {$this->table} SET {$setString} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        return $stmt->execute();
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function count($where = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }
}
