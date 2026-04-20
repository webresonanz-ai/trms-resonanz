<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = 'products';
    
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $user_id;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table} (name, description, price, stock, user_id) 
                  VALUES (:name, :description, :price, :stock, :user_id)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock', $this->stock);
        $stmt->bindParam(':user_id', $this->user_id);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function findById($id) {
        $query = "SELECT p.*, u.name as owner_name 
                  FROM {$this->table} p 
                  JOIN users u ON p.user_id = u.id 
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
        
        return null;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['name', 'description', 'price', 'stock'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute($params);
    }
    
    public function delete($id, $user_id) {
        // Only allow deletion if product belongs to user
        $query = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    public function getAll($limit = 100, $offset = 0, $filters = []) {
        $query = "SELECT p.*, u.name as owner_name 
                  FROM {$this->table} p 
                  JOIN users u ON p.user_id = u.id 
                  WHERE 1=1";
        $params = [];
        
        if (isset($filters['user_id'])) {
            $query .= " AND p.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        
        if (isset($filters['min_price'])) {
            $query .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $query .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>