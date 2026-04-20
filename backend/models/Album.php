<?php
require_once __DIR__ . '/../config/database.php';

class Album {
    private $conn;
    private $table = 'albums';
    
    public $id;
    public $title;
    public $artist;
    public $artist_id;
    public $cover;
    public $release_date;
    public $streams;
    public $revenue;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table} (title, artist, artist_id, cover, release_date, streams, revenue) 
                  VALUES (:title, :artist, :artist_id, :cover, :release_date, :streams, :revenue)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':artist', $this->artist);
        $stmt->bindParam(':artist_id', $this->artist_id);
        $stmt->bindParam(':cover', $this->cover);
        $stmt->bindParam(':release_date', $this->release_date);
        $stmt->bindParam(':streams', $this->streams);
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
    
    public function findByArtist($artistId) {
        $query = "SELECT * FROM {$this->table} WHERE artist_id = :artist_id ORDER BY release_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':artist_id', $artistId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['title', 'artist', 'artist_id', 'cover', 'release_date', 'streams', 'revenue'];
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
        
        if (isset($filters['artist_id'])) {
            $query .= " AND artist_id = :artist_id";
            $params[':artist_id'] = $filters['artist_id'];
        }
        
        if (isset($filters['artist'])) {
            $query .= " AND artist = :artist";
            $params[':artist'] = $filters['artist'];
        }
        
        $query .= " ORDER BY release_date DESC LIMIT :limit OFFSET :offset";
        
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