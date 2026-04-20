<?php
require_once __DIR__ . '/../config/database.php';

class Stream {
    private $conn;
    private $table = 'streams';
    
    public $id;
    public $track_name;
    public $artist;
    public $stream_date;
    public $stream_count;
    public $revenue;
    public $created_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table} (track_name, artist, stream_date, stream_count, revenue) 
                  VALUES (:track_name, :artist, :stream_date, :stream_count, :revenue)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':track_name', $this->track_name);
        $stmt->bindParam(':artist', $this->artist);
        $stmt->bindParam(':stream_date', $this->stream_date);
        $stmt->bindParam(':stream_count', $this->stream_count);
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
    
    public function getAll($limit = 100, $offset = 0, $filters = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (isset($filters['artist'])) {
            $query .= " AND artist = :artist";
            $params[':artist'] = $filters['artist'];
        }
        
        if (isset($filters['track_name'])) {
            $query .= " AND track_name LIKE :track_name";
            $params[':track_name'] = '%' . $filters['track_name'] . '%';
        }
        
        if (isset($filters['start_date'])) {
            $query .= " AND stream_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (isset($filters['end_date'])) {
            $query .= " AND stream_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        $query .= " ORDER BY stream_date DESC, stream_count DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getRecent($limit = 10) {
        $query = "SELECT * FROM {$this->table} ORDER BY stream_date DESC, stream_count DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}