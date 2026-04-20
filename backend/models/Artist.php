<?php
require_once __DIR__ . '/../config/database.php';

class Artist {
    private $conn;
    private $table = 'artists';
    
    public $id;
    public $name;
    public $image;
    public $monthly_listeners;
    public $total_streams;
    public $albums_count;
    public $genre;
    public $revenue;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table} (name, image, monthly_listeners, total_streams, albums_count, genre, revenue) 
                  VALUES (:name, :image, :monthly_listeners, :total_streams, :albums_count, :genre, :revenue)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':monthly_listeners', $this->monthly_listeners);
        $stmt->bindParam(':total_streams', $this->total_streams);
        $stmt->bindParam(':albums_count', $this->albums_count);
        $stmt->bindParam(':genre', $this->genre);
        $stmt->bindParam(':revenue', $this->revenue);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
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
        
        $allowedFields = ['name', 'image', 'monthly_listeners', 'total_streams', 'albums_count', 'genre', 'revenue'];
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
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getAll($limit = 100, $offset = 0, $filters = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (isset($filters['genre'])) {
            $query .= " AND genre = :genre";
            $params[':genre'] = $filters['genre'];
        }
        
        if (isset($filters['search'])) {
            $query .= " AND name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $query .= " ORDER BY total_streams DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getStats() {
        $query = "SELECT 
                    COALESCE(SUM(total_streams), 0) as total_streams,
                    COALESCE(SUM(revenue), 0) as total_revenue,
                    COUNT(*) as total_artists,
                    COALESCE(SUM(monthly_listeners), 0) as active_listeners
                  FROM {$this->table}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}